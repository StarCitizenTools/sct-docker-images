FROM mediawiki

USER root

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install pcntl

WORKDIR /var/www/html

USER www-data

# Pin the runner to an exact commit so a push to weirdgloop/master cannot
# change what the next image build ships. Bump deliberately: check
# https://github.com/weirdgloop/mediawiki-services-jobrunner/commits/weirdgloop/master
# and update the hash here.
ARG JOBRUNNER_REPO='https://github.com/weirdgloop/mediawiki-services-jobrunner.git'
ARG JOBRUNNER_COMMIT_HASH='6c10e770bd47f452aec22edba6fc8e7bd9d7a8ea'

RUN set -eux; \
   git init -q mediawiki-services-jobrunner; \
   cd mediawiki-services-jobrunner; \
   git fetch --depth 1 "${JOBRUNNER_REPO}" "${JOBRUNNER_COMMIT_HASH}"; \
   git checkout -q FETCH_HEAD

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
