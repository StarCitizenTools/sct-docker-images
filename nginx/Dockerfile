# Only what nginx itself serves from disk: static assets, and the PHP entry
# points it checks exist before handing a request to php-fpm. With the whole
# tree, nginx also served files like composer.lock and passed any .php under
# the web root (vendor/, tests/) to php-fpm, and this image was nearly as big
# as the PHP one.
FROM mediawiki AS static
RUN set -eu; \
	cd /var/www/mediawiki; \
	for f in index.php api.php load.php rest.php thumb.php thumb_handler.php img_auth.php opensearch_desc.php \
		COPYING CREDITS; do \
		test -f "$f"; \
		echo "$f"; \
	done > /tmp/files; \
	find extensions resources skins -type f \( \
		-iname '*.css' -o -iname '*.js' -o -iname '*.mjs' -o -iname '*.wasm' \
		-o -iname '*.png' -o -iname '*.svg' -o -iname '*.gif' -o -iname '*.jpg' -o -iname '*.jpeg' \
		-o -iname '*.webp' -o -iname '*.ico' \
		-o -iname '*.woff' -o -iname '*.woff2' -o -iname '*.ttf' -o -iname '*.otf' -o -iname '*.eot' \
	\) >> /tmp/files; \
	tar -cf /tmp/static.tar -T /tmp/files; \
	mkdir /tmp/static; \
	tar -xf /tmp/static.tar -C /tmp/static

FROM nginx:1.31.8@sha256:c4d1df1ca40ef8cf932f19853f00eb0f81ffe5becda1256365bbf0a057060ffb

# System dependencies
RUN set -eux; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		netcat-traditional \
	; \
	rm -rf /var/lib/apt/lists/*

# Ownership is set on copy rather than by a separate chown -R pass.
COPY --from=static --chown=www-data:www-data /tmp/static /var/www/mediawiki

COPY --chown=www-data:www-data ./config/robots.txt /var/www/mediawiki/robots.txt

RUN chown www-data:www-data /var/www /var/www/mediawiki
