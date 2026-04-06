FROM nginx:1.29.7

# System dependencies
RUN set -eux; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		netcat-traditional \
	; \
	rm -rf /var/lib/apt/lists/*

# Copy skins, extensions, and resources from the mediawiki build
COPY --from=mediawiki /var/www/mediawiki/skins /var/www/mediawiki/skins
COPY --from=mediawiki /var/www/mediawiki/extensions /var/www/mediawiki/extensions
COPY --from=mediawiki /var/www/mediawiki/resources /var/www/mediawiki/resources

COPY ./config/robots.txt /var/www/mediawiki/robots.txt

RUN set -eux; \
	chown -R www-data:www-data /var/www
