FROM mediawiki

USER root

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install pcntl

WORKDIR /var/www/html

USER www-data

RUN git clone --depth 1 --branch weirdgloop/master https://github.com/weirdgloop/mediawiki-services-jobrunner.git mediawiki-services-jobrunner

RUN --mount=type=cache,target=/var/www/.composer/cache,uid=33,gid=33 \
   set -eux; \
   cd mediawiki-services-jobrunner; \
   /usr/bin/composer config --no-plugins allow-plugins.composer/installers true; \
   /usr/bin/composer install --no-dev \
     --ignore-platform-reqs \
     --no-ansi \
     --no-interaction \
     --no-scripts

COPY --chown=www-data:www-data ./jobrunner-conf.json /var/www/html/mediawiki-services-jobrunner
COPY --chown=www-data:www-data --chmod=770 ./entrypoint.sh /var/www/html/mediawiki-services-jobrunner

ENTRYPOINT ["/var/www/html/mediawiki-services-jobrunner/entrypoint.sh"]
