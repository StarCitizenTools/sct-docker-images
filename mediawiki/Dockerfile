# Base stage
# Named stages rather than inline COPY --from images: Dependabot only reads FROM.
FROM composer:2.10.3@sha256:da624c2c705d54e2f5a9bb77a5dc6c7d091711433ebefd7035672e84c2010fee AS composer
FROM mlocati/php-extension-installer:2.12.1@sha256:486d090fba21c04170262152c0ae63e7434d75e84fdd13450201c7fd54721b22 AS php-extension-installer

FROM php:8.4.27-fpm@sha256:e2d8e787dc775bdbd9d7e0c5e8f2f9ddb41ce5977c8bbaf99a2ba2e7d90fd8ae AS base

ARG UPDATE_PHP_EXTENSIONS=false

# PHP extensions
# install-php-extensions is used for simplicity since it also supports pecl and it can install wikidiff2 correctly
COPY --from=php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
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

# MediaWiki source
# Cloned in its own stage so it runs in parallel with the PHP extension build
# in the base stage; it only needs git, which the composer image already ships.
FROM composer AS source

# Version
ARG MEDIAWIKI_BRANCH='REL1_46'
ARG MEDIAWIKI_COMMIT_HASH=''

# Core's submodules (bundled extensions, skins, vendor) point at Gerrit, which
# rate-limits CI runners with HTTP 429 partway through the ~40 fetches. Fetch
# them from Wikimedia's GitHub mirrors instead, which carry the same commits.
# VisualEditor's nested lib/ve submodule lives outside /r/mediawiki/, so it
# needs its own rewrite.
RUN set -eux; \
	git config --global url.https://github.com/wikimedia/VisualEditor.git.insteadOf https://gerrit.wikimedia.org/r/VisualEditor/VisualEditor.git; \
	git config --global url.https://github.com/wikimedia/mediawiki-extensions-.insteadOf https://gerrit.wikimedia.org/r/mediawiki/extensions/; \
	git config --global url.https://github.com/wikimedia/mediawiki-skins-.insteadOf https://gerrit.wikimedia.org/r/mediawiki/skins/; \
	git config --global url.https://github.com/wikimedia/mediawiki-.insteadOf https://gerrit.wikimedia.org/r/mediawiki/; \
	git clone --single-branch --branch ${MEDIAWIKI_BRANCH} --depth 1 https://github.com/wikimedia/mediawiki.git /var/www/mediawiki; \
	cd /var/www/mediawiki; \
	if [ -n "${MEDIAWIKI_COMMIT_HASH}" ]; then \
		echo "Checking out commit: ${MEDIAWIKI_COMMIT_HASH}"; \
		git fetch --depth 1 origin ${MEDIAWIKI_COMMIT_HASH}; \
		git checkout ${MEDIAWIKI_COMMIT_HASH}; \
	fi; \
	git submodule update --init --recursive --depth 1 --jobs 8

# Builder stage
FROM base AS builder

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
# Ownership is set on copy rather than by a separate chown -R pass, which
# has to walk all ~77k files and adds up to a minute per stage.
RUN install -d -o www-data -g www-data /var/www/mediawiki /var/www/.composer
COPY --from=source --chown=www-data:www-data /var/www/mediawiki /var/www/mediawiki

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/mediawiki

# Skins and extensions
# Defined in composer.local.json
COPY --chown=www-data:www-data ./composer.json /var/www/mediawiki/composer.local.json

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
	# Install from source (git clones) so Special:Version can show commit info,
	# except aws-sdk-php: nothing reads its git metadata and its full history is
	# over 600 MB, more than all the extensions and skins combined.
	/usr/bin/composer config --no-plugins preferred-install.aws/aws-sdk-php dist; \
	/usr/bin/composer config --no-plugins "preferred-install.*" source; \
	\
	# Install the skins and extensions
	/usr/bin/composer install --no-dev \
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
		--no-ansi \
		--no-interaction \
		--no-scripts;

# Patch CirrusSearch REL1_46 to accept OpenSearch 2.x. Its version gate allows
# only 1.3.x, which has been EOL since May 2025; support for 2.x is already
# upstream in 03f64ca0, just not backported to the release branch. WMF is on
# 2.19.5 as a step toward 3.x. Drop this at MW 1.47, where the gate accepts 2.x.
#   https://phabricator.wikimedia.org/T421757  (1.x -> 2.x, 1.x unmaintained)
#   https://phabricator.wikimedia.org/T420407  (2.19.5 on the path to 3.x)
# The grep fails the build if the sed ever stops matching.
RUN set -eux; \
	f=/var/www/mediawiki/extensions/CirrusSearch/includes/Maintenance/ConfigUtils.php; \
	test -f "$f"; \
	sed -i "s/\$required = \$distribution === 'opensearch' ? '1.3' : '7.10';/\$required = \$distribution === 'opensearch' ? '2.' : '7.10';/" "$f"; \
	grep -q "'opensearch' ? '2\.' :" "$f"; \
	php -l "$f"

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
		webp \
	;

# Copy PHP configs
COPY ./config/php-config.ini /usr/local/etc/php/conf.d/php-config.ini
COPY ./config/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./config/zzz-fpm-pool.conf /usr/local/etc/php-fpm.d/zzz-fpm-pool.conf
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini; \
	echo 'max_execution_time = 60' >> /usr/local/etc/php/conf.d/docker-php-executiontime.ini;

# Create required directories
RUN install -d -o www-data -g www-data /var/www/mediawiki

WORKDIR /var/www/mediawiki

# Copy built application files and python packages from the builder stage
# Ownership is set on copy rather than by a separate chown -R pass, which
# has to walk all ~77k files and adds up to a minute per stage.
COPY --from=builder --chown=www-data:www-data /var/www/mediawiki /var/www/mediawiki
COPY --from=builder /opt/venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"
# Create a symlink to ensure MediaWiki finds pygmentize
RUN ln -s /opt/venv/bin/pygmentize /usr/local/bin/pygmentize

# Copy final configs
COPY --chown=www-data:www-data ./config/LocalSettings.php /var/www/mediawiki/LocalSettings.php
COPY --chown=www-data:www-data ./config/settings /var/www/mediawiki/settings
COPY --chown=www-data:www-data ./resources /var/www/mediawiki/resources

USER www-data

CMD ["php-fpm"]
