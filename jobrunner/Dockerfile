FROM mediawiki

USER root

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install pcntl

WORKDIR /var/www/html

USER www-data

# Exact commit of the runner to ship. Both values are pinned in
# docker-bake.hcl (the single version manifest); there is deliberately no
# default here so the pin cannot drift into two places.
ARG JOBRUNNER_REPO
ARG JOBRUNNER_COMMIT_HASH

RUN set -eux; \
   : "${JOBRUNNER_REPO:?set in docker-bake.hcl}"; \
   : "${JOBRUNNER_COMMIT_HASH:?set in docker-bake.hcl}"; \
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
