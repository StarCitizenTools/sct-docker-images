# Named stage rather than an inline COPY --from image: Dependabot only reads FROM.
FROM composer:2.11.1@sha256:8de2184c6de224f4689e8f80ed5f5edaf0030b6b8b7839a22a2bcb6722ac87d0 AS composer

FROM mediawiki

USER root

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install pcntl

WORKDIR /var/www/html

USER www-data

COPY --chown=www-data:www-data ./mediawiki-services-jobrunner /var/www/html/mediawiki-services-jobrunner

RUN --mount=type=cache,target=/var/www/.composer/cache,uid=33,gid=33 \
   set -eux; \
   cd mediawiki-services-jobrunner; \
   # Upstream ships no composer.lock, so install resolves require-dev even
   # with --no-dev, and Composer refuses to resolve packages with security
   # advisories — dev-only linters can then break the image build. Drop
   # require-dev so only the runtime dependencies are resolved; advisory
   # blocking stays active for those.
   /usr/bin/composer config --no-plugins --unset require-dev; \
   /usr/bin/composer config --no-plugins allow-plugins.composer/installers true; \
   /usr/bin/composer install --no-dev \
     --ignore-platform-reqs \
     --no-ansi \
     --no-interaction \
     --no-scripts

COPY --chown=www-data:www-data ./jobrunner-conf.json /var/www/html/mediawiki-services-jobrunner
COPY --chown=www-data:www-data --chmod=770 ./entrypoint.sh /var/www/html/mediawiki-services-jobrunner

ENTRYPOINT ["/var/www/html/mediawiki-services-jobrunner/entrypoint.sh"]
