# Builder stage
FROM php:8.3-fpm AS builder

# Version
ARG MEDIAWIKI_MAJOR_VERSION='1.43'
ARG MEDIAWIKI_VERSION='1.43.3'

# Build arguments
ARG UPDATE_SYSTEM_DEPENDENCIES=false
ARG UPDATE_PHP_EXTENSIONS=false
ARG UPDATE_COMPOSER_DEPENDENCIES=false

# System dependencies
RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
	--mount=type=cache,target=/var/lib/apt,sharing=locked \
	set -eux; \
	echo "Updating system dependencies: ${UPDATE_SYSTEM_DEPENDENCIES}"; \
	apt-get update; \
	apt-get install -y --no-install-recommends \
		git \
		unzip \
		openssh-client \
		# Required to build Pygments
		python3 \
		python3-pip \
	;

# Pygments
# Required for Extension:SyntaxHighlight
# This is compiled from source because both the bundled and Debian packages are too old
RUN --mount=type=cache,target=/root/.cache/pip \
	set -eux; \
	pip3 install Pygments --break-system-packages \
	;

# Create a tarball of the Python packages so that we can copy them to the final image
RUN export PY_PACKAGES_PATH=$(python3 -c 'import sysconfig; print(sysconfig.get_path("platlib"))') && \
	tar -czf /python-packages.tar.gz -C ${PY_PACKAGES_PATH} .

# PHP extensions
# install-php-extensions is used for simplicity since it also supports pecl and it can install wikidiff2 correctly
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN --mount=type=cache,target=/tmp/phpexts-cache \
	set -eux; \
	echo "Updating PHP extensions: ${UPDATE_PHP_EXTENSIONS}"; \
	install-php-extensions \
		calendar \
		exif \
		intl \
		mysqli \
		zip \
		apcu \
		luasandbox \
		redis \
		wikidiff2 \
	;

# MediaWiki
RUN set -eux; \
	fetchDeps=" \
		gnupg \
		dirmngr \
	"; \
	apt-get update; \
	apt-get install -y --no-install-recommends $fetchDeps; \
	\
	curl -fSL "https://releases.wikimedia.org/mediawiki/${MEDIAWIKI_MAJOR_VERSION}/mediawiki-${MEDIAWIKI_VERSION}.tar.gz" -o mediawiki.tar.gz; \
	curl -fSL "https://releases.wikimedia.org/mediawiki/${MEDIAWIKI_MAJOR_VERSION}/mediawiki-${MEDIAWIKI_VERSION}.tar.gz.sig" -o mediawiki.tar.gz.sig; \
	export GNUPGHOME="$(mktemp -d)"; \
	# gpg key from https://www.mediawiki.org/keys/keys.txt
	gpg --batch --keyserver keyserver.ubuntu.com --recv-keys \
		D7D6767D135A514BEB86E9BA75682B08E8A3FEC4 \
		441276E9CCD15F44F6D97D18C119E1A64D70938E \
		F7F780D82EBFB8A56556E7EE82403E59F9F8CD79 \
		1D98867E82982C8FE0ABC25F9B69B3109D3BB7B0 \
		E059C034E7A430583C252F4AA8F734246D73B586 \
	; \
	gpg --batch --verify mediawiki.tar.gz.sig mediawiki.tar.gz; \
	mkdir /var/www/mediawiki; \
	tar -x --strip-components=1 -f mediawiki.tar.gz -C /var/www/mediawiki; \
	gpgconf --kill all; \
	rm -r "$GNUPGHOME" mediawiki.tar.gz.sig mediawiki.tar.gz; \
	\
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false $fetchDeps; \
	rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/mediawiki

# Skins and extensions
# Defined in composer.local.json
COPY ./composer.json /var/www/mediawiki/composer.local.json

RUN set -eux; \
	mkdir /usr/local/smw; \
	mkdir -p /var/www/.composer; \
	chown -R www-data:www-data /var/www/mediawiki /usr/local/smw /var/www/.composer

USER www-data

RUN --mount=type=cache,target=/var/www/.composer/cache,uid=33,gid=33 \
	set -eux; \
	echo "Forcing composer update: ${UPDATE_COMPOSER_DEPENDENCIES}"; \
	/usr/bin/composer config --no-plugins allow-plugins.composer/installers true; \
	\
	# Install the skins and extensions first
	/usr/bin/composer install --no-dev \
		--prefer-source \
		--ignore-platform-reqs \
		--no-ansi \
		--no-interaction \
		--no-scripts; \
	\
	# Remove composer.lock so the next command won't use it
	rm -f composer.lock; \
	\
	# Needed so that composer would install the depedencies of the skins and extensions that we just installed
	/usr/bin/composer update --no-dev \
		--prefer-source \
		--no-ansi \
		--no-interaction \
		--no-scripts;

# Final image
FROM php:8.3-fpm

ARG UPDATE_SYSTEM_DEPENDENCIES=false
ARG UPDATE_PHP_EXTENSIONS=false

# Runtime dependencies
RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
	--mount=type=cache,target=/var/lib/apt,sharing=locked \
	set -eux; \
	echo "Updating system dependencies: ${UPDATE_SYSTEM_DEPENDENCIES}"; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		# Sysops tools
		openssh-client \
		nano \
		rsync \
		s3cmd \
		unzip \
		# MediaWiki requirements
		imagemagick \
		# Required to show commit info in Special:Version
		git \
		# Extension:EmbedVideo
		ffmpeg \
		# Extension:SyntaxHighlight
		python3 \
		# Extension:Thumbro
		libvips-tools \
	;

# PHP extensions
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN --mount=type=cache,target=/tmp/phpexts-cache \
	set -eux; \
	echo "Updating PHP extensions: ${UPDATE_PHP_EXTENSIONS}"; \
	install-php-extensions \
		calendar \
		exif \
		intl \
		mysqli \
		zip \
		apcu \
		luasandbox \
		redis \
		wikidiff2 \
	;

# Copy PHP configs
COPY ./config/php-config.ini /usr/local/etc/php/conf.d/php-config.ini
COPY ./config/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini; \
	echo 'max_execution_time = 60' >> /usr/local/etc/php/conf.d/docker-php-executiontime.ini; \
	echo 'pm.max_children = 30' >> /usr/local/etc/php-fpm.d/zz-docker.conf; \
	echo 'pm.max_requests = 200' >> /usr/local/etc/php-fpm.d/zz-docker.conf; \
	echo 'pm.start_servers = 10' >> /usr/local/etc/php-fpm.d/zz-docker.conf; \
	echo 'pm.min_spare_servers = 10' >> /usr/local/etc/php-fpm.d/zz-docker.conf; \
	echo 'pm.max_spare_servers = 30' >> /usr/local/etc/php-fpm.d/zz-docker.conf;

# Create required directories
RUN mkdir -p /var/www/mediawiki /usr/local/smw; \
	chown www-data:www-data /usr/local/smw

WORKDIR /var/www/mediawiki

# Copy built application files and python packages from the builder stage
COPY --from=builder /var/www/mediawiki /var/www/mediawiki
COPY --from=builder /python-packages.tar.gz /python-packages.tar.gz
RUN export PY_PACKAGES_PATH=$(python3 -c 'import sysconfig; print(sysconfig.get_path("platlib"))') && \
	mkdir -p ${PY_PACKAGES_PATH} && \
	tar -xzf /python-packages.tar.gz -C ${PY_PACKAGES_PATH} && \
	rm /python-packages.tar.gz
COPY --from=builder /usr/local/bin/pygmentize /usr/local/bin/pygmentize

# Copy final configs
COPY ./config/LocalSettings.php /var/www/mediawiki/LocalSettings.php
COPY ./resources /var/www/mediawiki/resources
COPY ./config/robots.txt /var/www/mediawiki/robots.txt

# Set final ownership
RUN chown -R www-data:www-data /var/www/mediawiki

USER www-data

CMD ["php-fpm"]
