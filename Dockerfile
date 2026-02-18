# Base stage
FROM php:8.3-fpm AS base

ARG UPDATE_PHP_EXTENSIONS=false

# PHP extensions
# install-php-extensions is used for simplicity since it also supports pecl and it can install wikidiff2 correctly
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN --mount=type=cache,target=/tmp/phpexts-cache \
	set -eux; \
	echo "Updating PHP extensions: ${UPDATE_PHP_EXTENSIONS}"; \
	\
	if [ "${UPDATE_PHP_EXTENSIONS}" != "false" ]; then \
		echo "Clearing PHP extensions cache..."; \
		rm -rf /tmp/phpexts-cache/*; \
	fi; \
	\
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

# Builder stage
FROM base AS builder

# Version
ARG MEDIAWIKI_BRANCH='REL1_43'
ARG MEDIAWIKI_COMMIT_HASH=''

# Build arguments
ARG UPDATE_SYSTEM_DEPENDENCIES=false
ARG UPDATE_COMPOSER_DEPENDENCIES=false

# System dependencies
RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
	--mount=type=cache,target=/var/lib/apt,sharing=locked \
	set -eux; \
	echo "Updating system dependencies: ${UPDATE_SYSTEM_DEPENDENCIES}"; \
	\
	if [ "${UPDATE_SYSTEM_DEPENDENCIES}" != "false" ]; then \
		echo "Clearing apt cache..."; \
		rm -rf /var/cache/apt/*; \
		rm -rf /var/lib/apt/lists/*; \
	fi; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		git \
		unzip \
		openssh-client \
		# Required to build Pygments
		python3 \
		python3-venv \
	;

# Pygments
# Required for Extension:SyntaxHighlight
# This is compiled from source because both the bundled and Debian packages are too old
RUN --mount=type=cache,target=/root/.cache/pip \
	set -eux; \
	python3 -m venv /opt/venv; \
	/opt/venv/bin/pip install Pygments

# MediaWiki
RUN set -eux; \
	git clone --single-branch --branch ${MEDIAWIKI_BRANCH} --depth 1 https://github.com/wikimedia/mediawiki.git /var/www/mediawiki; \
	cd /var/www/mediawiki; \
	if [ -n "${MEDIAWIKI_COMMIT_HASH}" ]; then \
		echo "Checking out commit: ${MEDIAWIKI_COMMIT_HASH}"; \
		git fetch --depth 1 origin ${MEDIAWIKI_COMMIT_HASH}; \
		git checkout ${MEDIAWIKI_COMMIT_HASH}; \
	fi; \
	git submodule update --init --recursive

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
	\
	if [ "${UPDATE_COMPOSER_DEPENDENCIES}" != "false" ]; then \
		echo "Clearing composer cache..."; \
		rm -rf /var/www/.composer/cache/*; \
	fi; \
	\
	/usr/bin/composer config --no-plugins allow-plugins.composer/installers true; \
	\
	# Ignore security advisories
	/usr/bin/composer config --json audit.ignore '{"PKSA-y2cr-5h3j-g3ys": "ignored"}'; \
	\
	# Install the skins and extensions
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
FROM base AS final

ARG UPDATE_SYSTEM_DEPENDENCIES=false

# Runtime dependencies
RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
	--mount=type=cache,target=/var/lib/apt,sharing=locked \
	set -eux; \
	echo "Updating system dependencies: ${UPDATE_SYSTEM_DEPENDENCIES}"; \
	\
	if [ "${UPDATE_SYSTEM_DEPENDENCIES}" != "false" ]; then \
		echo "Clearing apt cache..."; \
		rm -rf /var/cache/apt/*; \
		rm -rf /var/lib/apt/lists/*; \
	fi; \
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
COPY --from=builder /opt/venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"
# Create a symlink to ensure MediaWiki finds pygmentize
RUN ln -s /opt/venv/bin/pygmentize /usr/local/bin/pygmentize

# Copy final configs
COPY ./config/LocalSettings.php /var/www/mediawiki/LocalSettings.php
COPY ./resources /var/www/mediawiki/resources
COPY ./config/robots.txt /var/www/mediawiki/robots.txt

# Set final ownership
RUN chown -R www-data:www-data /var/www/mediawiki

USER www-data

CMD ["php-fpm"]
