FROM nginx:1.31.8@sha256:6e67f8eaac94fc456588d4ff6c470d984841f5b5dc4279863d5cbe6ec5a60def

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
# Ownership is set on copy rather than by a separate chown -R pass, which
# has to walk all ~77k files and adds up to a minute per stage.
COPY --from=mediawiki --chown=www-data:www-data /var/www/mediawiki /var/www/mediawiki

COPY --chown=www-data:www-data ./config/robots.txt /var/www/mediawiki/robots.txt

RUN chown www-data:www-data /var/www /var/www/mediawiki
