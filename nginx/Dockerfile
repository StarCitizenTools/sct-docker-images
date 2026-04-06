FROM nginx:1.29.7

# System dependencies
RUN set -eux; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		netcat-traditional \
	; \
	rm -rf /var/lib/apt/lists/*

# Copy the full MediaWiki installation from the mediawiki build
# Nginx needs core files (index.php, load.php, thumb.php, etc.) for routing,
# plus skins/extensions/resources for serving static assets
COPY --from=mediawiki /var/www/mediawiki /var/www/mediawiki

COPY ./config/robots.txt /var/www/mediawiki/robots.txt

RUN set -eux; \
	chown -R www-data:www-data /var/www
