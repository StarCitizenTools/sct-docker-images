FROM nginx:1.23.3

# Version
ENV MEDIAWIKI_MAJOR_VERSION 1.39
ENV MEDIAWIKI_VERSION 1.39.3

# MediaWiki setup
RUN set -eux; \
    fetchDeps=" \
        gnupg \
        dirmngr \
    "; \
    apt-get update; \
    apt-get install -y --no-install-recommends $fetchDeps; \
    \
    curl -fSL "https://releases.wikimedia.org/mediawiki/${MEDIAWIKI_MAJOR_VERSION}/mediawiki-${MEDIAWIKI_VERSION}.tar.gz" -o mediawiki.tar.gz; \
    curl -fSL "https://releases.wikimedia.org/mediawiki/${MEDIAWIKI_MAJOR_VERSION}/mediawiki-${MEDIAWIKI_VERSION}.tar.gz.sig" -o mediawiki.tar.gz.sig; \
    export GNUPGHOME="$(mktemp -d)"; \
    # gpg key from https://www.mediawiki.org/keys/keys.txt
    gpg --batch --keyserver keyserver.ubuntu.com --recv-keys \
        D7D6767D135A514BEB86E9BA75682B08E8A3FEC4 \
        441276E9CCD15F44F6D97D18C119E1A64D70938E \
        F7F780D82EBFB8A56556E7EE82403E59F9F8CD79 \
        1D98867E82982C8FE0ABC25F9B69B3109D3BB7B0 \
    ; \
    gpg --batch --verify mediawiki.tar.gz.sig mediawiki.tar.gz; \
	mkdir -p /var/www/mediawiki; \
    tar -x --strip-components=1 -f mediawiki.tar.gz -C /var/www/mediawiki; \
    gpgconf --kill all; \
    rm -r "$GNUPGHOME" mediawiki.tar.gz.sig mediawiki.tar.gz; \
    \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false $fetchDeps; \
    rm -rf /var/lib/apt/lists/*
    
COPY ./resources /var/www/mediawiki/resources

COPY ./config/robots.txt /var/www/mediawiki/robots.txt
COPY ./resources/assets/favicon.ico /var/www/mediawiki/favicon.ico

RUN set -eux; \
   chown -R www-data:www-data /var/www

CMD ["nginx" "-g" "daemon off;"]
