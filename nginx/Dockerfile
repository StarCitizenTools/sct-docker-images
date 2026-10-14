FROM nginx:1.31.6@sha256:60f0d4e986561e08804f75c211dcaf7fc5455699eada29f42cbe8a0a7253dd86

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
