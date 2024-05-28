FROM nginx:1.26.0

# Version
ENV MEDIAWIKI_MAJOR_VERSION 1.39
ENV MEDIAWIKI_VERSION 1.39.7

# System dependencies
RUN set -eux; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
        netcat-traditional \
	; \
	rm -rf /var/lib/apt/lists/*

# Compile ngx_brotli
# Set working directory inside the container
WORKDIR /usr/src

# Install dependencies required for building ngx_brotli
RUN apt-get update && \
    apt-get install -y git build-essential libpcre3 libpcre3-dev zlib1g zlib1g-dev libssl-dev cmake

# Clone the ngx_brotli module from the official GitHub repository
RUN git clone --recurse-submodules https://github.com/google/ngx_brotli.git

# Build the Brotli dependencies
RUN cd ngx_brotli/deps/brotli && \
    mkdir out && cd out && \
    cmake -DCMAKE_BUILD_TYPE=Release -DBUILD_SHARED_LIBS=OFF .. && \
    cmake --build . --config Release --target install

# Configure NGINX with ngx_brotli module
RUN cd /usr/src/nginx-1.26.0 && \
    ./configure --add-module=/usr/src/ngx_brotli && \
    make && make install

# Clean up unnecessary files and packages to reduce image size
RUN apt-get remove --purge -y git build-essential libpcre3-dev zlib1g-dev libssl-dev cmake && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /usr/src/ngx_brotli

# MediaWiki setup
RUN set -eux; \
    fetchDeps=" \
        gnupg \
        dirmngr \
        unzip \
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
    curl -fSL "https://github.com/StarCitizenTools/mediawiki-skins-Citizen/archive/main.zip" -o citizenskin.zip; \
    unzip citizenskin.zip -d /var/www/mediawiki/skins; \
    mv /var/www/mediawiki/skins/mediawiki-skins-Citizen-main /var/www/mediawiki/skins/Citizen; \
    rm -r "$GNUPGHOME" mediawiki.tar.gz.sig mediawiki.tar.gz citizenskin.zip; \
    \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false $fetchDeps; \
    rm -rf /var/lib/apt/lists/*
    
COPY ./resources /var/www/mediawiki/resources

COPY ./config/robots.txt /var/www/mediawiki/robots.txt

RUN set -eux; \
   chown -R www-data:www-data /var/www
