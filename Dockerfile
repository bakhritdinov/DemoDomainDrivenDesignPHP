# ------------------------------
# ARGs
# ------------------------------
ARG PHP_VERSION=8.2
ARG COMPOSER_VERSION=latest
ARG ENVIRONMENT=prod
ARG INSTALL_XDEBUG=false
ARG INSTALL_PHPREDIS=false

ARG CONTAINER_MODE=app
ARG WWWUSER=1000
ARG WWWGROUP=1000
ARG TIME_ZONE=Europe/Moscow
ARG ROOT=/var/www
ARG PORT=9501

# ------------------------------
# BASE IMAGE (PHP + extensions)
# ------------------------------
FROM php:${PHP_VERSION}-cli-bullseye AS base

ARG ROOT
ARG TIME_ZONE
ARG WWWUSER
ARG WWWGROUP
ARG INSTALL_XDEBUG
ARG INSTALL_PHPREDIS

ENV ROOT=${ROOT} \
    DEBIAN_FRONTEND=noninteractive \
    TERM=xterm-color

WORKDIR ${ROOT}

SHELL ["/bin/bash", "-eou", "pipefail", "-c"]

RUN ln -snf /usr/share/zoneinfo/$TIME_ZONE /etc/localtime \
    && echo $TIME_ZONE > /etc/timezone \
    && apt-get update \
    && apt-get upgrade -yqq \
    && pecl -q channel-update pecl.php.net \
    && apt-get install -yqq --no-install-recommends \
        apt-utils gnupg gosu curl wget git supervisor \
        libcurl4-openssl-dev ca-certificates libz-dev libbrotli-dev \
        libpq-dev libjpeg-dev libpng-dev libfreetype6-dev libssl-dev \
        libwebp-dev libmcrypt-dev libonig-dev libzip-dev zip unzip \
        libargon2-1 libidn2-0 libpcre2-8-0 libpcre3 libxml2 libzstd1 \
        postgresql procps exiftool \
    && docker-php-ext-install sockets zip mbstring gd pcntl bcmath exif pdo_pgsql pgsql pdo_mysql \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && apt-get clean \
    && docker-php-source delete \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN if [ "$INSTALL_XDEBUG" = "true" ]; then pecl install xdebug && docker-php-ext-enable xdebug; fi
RUN if [ "$INSTALL_PHPREDIS" = "true" ]; then pecl install redis && docker-php-ext-enable redis; fi

RUN groupadd --force -g $WWWGROUP app \
    && useradd -ms /bin/bash --no-log-init --no-user-group -g $WWWGROUP -u $WWWUSER app

# ------------------------------
# DEPENDENCIES STAGE (prod)
# ------------------------------
FROM composer:${COMPOSER_VERSION} AS deps

ARG ROOT

WORKDIR ${ROOT}
COPY composer.json composer.lock ./
RUN if [ "$ENVIRONMENT" = "prod" ]; then \
        composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader --ignore-platform-reqs --no-interaction; \
    else \
        composer install --no-scripts --ignore-platform-reqs --no-interaction; \
    fi

# ------------------------------
# FINAL APP STAGE
# ------------------------------
FROM base AS final

ARG ROOT
ARG PORT
ARG ENVIRONMENT
ARG CONTAINER_MODE

ENV ENVIRONMENT=${ENVIRONMENT}

WORKDIR ${ROOT}

COPY . ${ROOT}

COPY --from=deps ${ROOT}/vendor ${ROOT}/vendor

COPY entrypoint.sh /entrypoint.sh
COPY utilities.sh /utilities.sh
RUN chmod +x /entrypoint.sh \
    && cat /utilities.sh >> ~/.bashrc

COPY supervisord.app.conf /etc/supervisor/conf.d/supervisord.app.conf
COPY supervisord.scheduler.conf /etc/supervisor/conf.d/supervisord.scheduler.conf
COPY supercronic /etc/supercronic/supercronic

RUN if [ "$CONTAINER_MODE" = "testing" ]; then \
        sed -i 's/RR_CONFIG/.rr.testing.yaml/g' /etc/supervisor/conf.d/supervisord.app.conf; \
    elif [ "$ENVIRONMENT" = "prod" ]; then \
        sed -i 's/RR_CONFIG/.rr.yaml/g' /etc/supervisor/conf.d/supervisord.app.conf; \
    else \
        sed -i 's/RR_CONFIG/.rr.dev.yaml/g' /etc/supervisor/conf.d/supervisord.app.conf; \
    fi

RUN if [ "$CONTAINER_MODE" = "scheduler" ]; then \
        wget -q "https://github.com/aptible/supercronic/releases/download/v0.1.12/supercronic-linux-amd64" \
            -O /usr/bin/supercronic && chmod +x /usr/bin/supercronic \
        && mkdir -p /etc/supercronic /var/log/supercronic; \
        PORT=0; \
    fi

RUN if [ "$CONTAINER_MODE" = "testing" ]; then \
        PORT=9502; \
        mkdir -p config/jwt && \
        openssl genrsa -aes256 -passout pass:test_key -out config/jwt/private-test.pem 4096 && \
        openssl rsa -in config/jwt/private-test.pem -passin pass:test_key -pubout -out config/jwt/public-test.pem; \
    fi

EXPOSE ${PORT}
ENTRYPOINT ["/entrypoint.sh"]

# HEALTHCHECK
HEALTHCHECK --start-period=5s --interval=10s --timeout=5s --retries=8 \
  CMD curl --fail http://localhost:2114/health?plugin=http || exit 1
