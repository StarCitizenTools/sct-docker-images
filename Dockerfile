# Builder stage
FROM php:8.3-fpm as builder

# Version
ARG MEDIAWIKI_MAJOR_VERSION='1.43'
ARG MEDIAWIKI_VERSION='1.43.1'

# System dependencies (includes build tools)
RUN set -eux; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		git \
		imagemagick \
		libvips-tools \
		ffmpeg \
		webp \
		unzip \
		openssh-client \
		rsync \
		nano \
		s3cmd \
		python3 \
		python3-pip \
	; \
	rm -rf /var/lib/apt/lists/*

# Install the Python packages we need
RUN set -eux; \
	pip3 install Pygments --break-system-packages \
	;

# Install the PHP extensions we need
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
		calendar \
		exif \
		intl \
		mbstring \
		mysqli \
		opcache \
		zip \
		apcu \
		luasandbox \
		redis \
		wikidiff2 \
	;

# MediaWiki setup
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
	gpg --fetch-keys "https://www.mediawiki.org/keys/keys.txt"; \
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

COPY ./composer.json /var/www/mediawiki/composer.local.json

RUN set -eux; \
	mkdir /usr/local/smw; \
	mkdir -p /var/www/.composer; \
	chown -R www-data:www-data /var/www/mediawiki /usr/local/smw /var/www/.composer

USER www-data

RUN set -eux; \
	/usr/bin/composer config --no-plugins allow-plugins.composer/installers true; \
	/usr/bin/composer install --no-dev \
		--prefer-source \
		--ignore-platform-reqs \
		--no-ansi \
		--no-interaction \
		--no-scripts; \
	rm -f composer.lock; \
	/usr/bin/composer update --no-dev \
		--prefer-source \
		--no-ansi \
		--no-interaction \
		--no-scripts;

# Final image
FROM php:8.3-fpm

# Install only runtime dependencies
RUN set -eux; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		imagemagick \
		libvips-tools \
		ffmpeg \
		webp \
		s3cmd \
		python3 \
	; \
	rm -rf /var/lib/apt/lists/*

# Install PHP extensions
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
		calendar \
		exif \
		intl \
		mbstring \
		mysqli \
		opcache \
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
COPY --from=builder /usr/local/lib/python3.11/site-packages /usr/local/lib/python3.11/site-packages

# Copy final configs
COPY ./config/LocalSettings.php /var/www/mediawiki/LocalSettings.php
COPY ./resources /var/www/mediawiki/resources
COPY ./config/robots.txt /var/www/mediawiki/robots.txt

# Set final ownership
RUN chown -R www-data:www-data /var/www/mediawiki

USER www-data

# Rename extensions as before
RUN set -eux; \
	mv /var/www/mediawiki/extensions/Checkuser /var/www/mediawiki/extensions/CheckUser; \
	mv /var/www/mediawiki/extensions/Dismissablesitenotice /var/www/mediawiki/extensions/DismissableSiteNotice; \
	mv /var/www/mediawiki/extensions/Mediasearch /var/www/mediawiki/extensions/MediaSearch; \
	mv /var/www/mediawiki/extensions/Parsermigration /var/www/mediawiki/extensions/ParserMigration; \
	mv /var/www/mediawiki/extensions/Revisionslider /var/www/mediawiki/extensions/RevisionSlider; \
	mv /var/www/mediawiki/extensions/Webauthn /var/www/mediawiki/extensions/WebAuthn; \
	mv /var/www/mediawiki/extensions/Twocolconflict /var/www/mediawiki/extensions/TwoColConflict;

CMD ["php-fpm"]
