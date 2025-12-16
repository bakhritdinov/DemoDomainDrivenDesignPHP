# Accepted values: 8.2 - 8.1 - 8.0
ARG PHP_VERSION=8.2
ARG COMPOSER_VERSION=latest

###########################################
# Install PHP extenstions
###########################################
FROM php:${PHP_VERSION}-cli-buster AS base

ARG TIME_ZONE=Europe/Moscow
ARG WWWUID=1000
ARG WWWGID=1000
ARG WWWUSER=app

ENV DEBIAN_FRONTEND=noninteractive \
    TERM=xterm-color

SHELL ["/bin/bash", "-eou", "pipefail", "-c"]

RUN ln -snf /usr/share/zoneinfo/$TIME_ZONE /etc/localtime \
    && echo $TIME_ZONE > /etc/timezone

RUN apt-get update; \
    apt-get upgrade -yqq; \
    pecl -q channel-update pecl.php.net; \
    apt-get install -yqq --no-install-recommends --show-progress \
          apt-utils \
          gnupg \
          gosu \
          curl \
          wget \
          libcurl4-openssl-dev \
          ca-certificates \
          supervisor \
          libz-dev \
          libbrotli-dev \
          libpq-dev \
          libjpeg-dev \
          libpng-dev \
          libfreetype6-dev \
          libssl-dev \
          libwebp-dev \
          libmcrypt-dev \
          libonig-dev \
          libzip-dev zip unzip \
          libargon2-1 \
          libidn2-0 \
          libpcre2-8-0 \
          libpcre3 \
          libxml2 \
          libzstd1 \
          postgresql \
          procps

###########################################
# sockets
###########################################
RUN docker-php-ext-install sockets;

###########################################
# zip
###########################################
RUN docker-php-ext-configure zip && docker-php-ext-install zip;

###########################################
# mbstring
###########################################
RUN docker-php-ext-install mbstring;

###########################################
# GD
###########################################
RUN docker-php-ext-configure gd \
            --prefix=/usr \
            --with-jpeg \
            --with-webp \
            --with-freetype \
    && docker-php-ext-install gd;

###########################################
# OPcache
###########################################
ARG INSTALL_OPCACHE=true

RUN if [ ${INSTALL_OPCACHE} = true ]; then \
      docker-php-ext-install opcache; \
  fi

###########################################
# PCNTL
###########################################
ARG INSTALL_PCNTL=true

RUN if [ ${INSTALL_PCNTL} = true ]; then \
      docker-php-ext-install pcntl; \
  fi

###########################################
# BCMath
###########################################
ARG INSTALL_BCMATH=true

RUN if [ ${INSTALL_BCMATH} = true ]; then \
      docker-php-ext-install bcmath; \
  fi

###########################################
# Exif
###########################################
ARG INSTALL_EXIF=true

RUN if [ ${INSTALL_EXIF} = true ]; then \
      docker-php-ext-configure exif \
      && docker-php-ext-install exif \
      && docker-php-ext-enable exif; \
  fi

###########################################
# RabbitMQ
###########################################
ARG INSTALL_RABBITMQ=true

RUN if [ ${INSTALL_RABBITMQ} = true ]; then \
      apt-get install -yqq --no-install-recommends --show-progress librabbitmq-dev \
      && pecl -q install -o -f amqp \
      && docker-php-ext-enable amqp; \
  fi

###########################################################################
# Human Language and Character Encoding Support
###########################################################################
ARG INSTALL_INTL=true

RUN if [ ${INSTALL_INTL} = true ]; then \
      apt-get install -yqq --no-install-recommends --show-progress zlib1g-dev libicu-dev g++ \
      && docker-php-ext-configure intl \
      && docker-php-ext-install intl; \
  fi

###########################################
# pdo_pgsql
###########################################
ARG INSTALL_PDO_PGSQL=true

RUN if [ ${INSTALL_PDO_PGSQL} = true ]; then \
      docker-php-ext-install pdo_pgsql; \
  fi

###########################################
# pgsql
###########################################
ARG INSTALL_PGSQL=true

RUN if [ ${INSTALL_PGSQL} = true ]; then \
      docker-php-ext-install pgsql; \
  fi

###########################################
# pgsql client and postgis
###########################################

ARG INSTALL_PG_CLIENT=true
ARG INSTALL_POSTGIS=true

RUN if [ ${INSTALL_PG_CLIENT} = true ]; then \
      . /etc/os-release \
      && echo "deb http://apt.postgresql.org/pub/repos/apt $VERSION_CODENAME-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
      && curl -sL https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add - \
      && apt-get install -yqq --no-install-recommends --show-progress postgresql-client-12 postgis \
      && apt-get update; \
      if [ ${INSTALL_POSTGIS} = true ]; then \
        apt-get install -yqq --no-install-recommends --show-progress postgis; \
      fi; \
  fi

###########################################
# pdo_mysql
###########################################

ARG INSTALL_PDO_MYSQL=true

RUN if [ ${INSTALL_PDO_MYSQL} = true ]; then \
      docker-php-ext-install pdo_mysql; \
  fi

###########################################
# Cleanup and setup
###########################################
RUN apt-get clean \
    && docker-php-source delete \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && rm /var/log/lastlog /var/log/faillog

RUN groupadd --force -g $WWWGID $WWWUSER \
    && useradd -ms /bin/bash --no-log-init --no-user-group -g $WWWGID -u $WWWUID $WWWUSER

RUN mkdir -p \
  var/{cache,log} \
  export/mindbox \
  import \
  && chown -R $WWWUSER:$WWWUSER \
  var/ \
  export/ \
  import/ \
  && chmod -R ug+rwx var/ export/ import/

###########################################
# Install PHP dependencies for deployment
###########################################
FROM composer:${COMPOSER_VERSION} AS deployment_vendor
ARG ROOT=/var/www

WORKDIR $ROOT
COPY composer* ./

RUN composer install \
            --no-dev \
            --no-interaction \
            --prefer-dist \
            --ignore-platform-reqs \
            --optimize-autoloader \
            --apcu-autoloader \
            --ansi \
            --no-scripts

###########################################
# Install PHP dependencies for testing
###########################################
FROM composer:${COMPOSER_VERSION} AS testing_vendor
ARG ROOT=/var/www

WORKDIR $ROOT

COPY composer* ./

RUN composer install \
             --ignore-platform-reqs \
             --no-scripts

###########################################
# Build app container
###########################################
FROM base AS app

ARG ROOT=/var/www
ARG PORT=9501
ARG RPC_PORT=6001/tcp
ARG ENVIRONMENT=prod
# Accepted values: app, scheduler, testing
ARG CONTAINER_MODE=app

ENV CONTAINER_MODE=${CONTAINER_MODE} \
    ROOT=${ROOT} \
    ENVIRONMENT=${ENVIRONMENT}

WORKDIR $ROOT

COPY . ${ROOT}
COPY --from=deployment_vendor ${ROOT}/vendor vendor

COPY .deployment/${ENVIRONMENT}/app/supervisord* /etc/supervisor/conf.d/
COPY .deployment/${ENVIRONMENT}/app/conf.d/php.ini /usr/local/etc/php/conf.d/app.ini
COPY .deployment/${ENVIRONMENT}/app/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

RUN chmod +x .deployment/shared/entrypoint.sh
RUN cat .deployment/shared/utilities.sh >> ~/.bashrc

EXPOSE ${PORT}
EXPOSE ${RPC_PORT}

ENTRYPOINT [".deployment/shared/entrypoint.sh"]

HEALTHCHECK --start-period=5s --interval=10s --timeout=5s --retries=8 CMD curl --fail http://localhost:2114/health?plugin=http || exit 1

###########################################
# Build testing container
###########################################
FROM base AS testing

ARG ROOT=/var/www
ARG PORT=9502
ARG ENVIRONMENT=prod
# Accepted values: app, scheduler, testing
ARG CONTAINER_MODE=testing

ENV CONTAINER_MODE=${CONTAINER_MODE} \
    ROOT=${ROOT} \
    ENVIRONMENT=${ENVIRONMENT}

WORKDIR $ROOT

COPY . ${ROOT}
COPY --from=testing_vendor ${ROOT}/vendor vendor

COPY .deployment/${ENVIRONMENT}/app/supervisord* /etc/supervisor/conf.d/
COPY .deployment/${ENVIRONMENT}/app/conf.d/php.ini /usr/local/etc/php/conf.d/app.ini

RUN chmod +x .deployment/shared/entrypoint.sh
RUN cat .deployment/shared/utilities.sh >> ~/.bashrc

RUN mkdir -p config/jwt
RUN openssl genrsa -aes256 -passout pass:test_key -out config/jwt/private-test.pem 4096
RUN openssl rsa -in config/jwt/private-test.pem -passin pass:test_key -pubout -out config/jwt/public-test.pem

EXPOSE ${PORT}

ENTRYPOINT [".deployment/shared/entrypoint.sh"]

HEALTHCHECK --start-period=5s --interval=10s --timeout=5s --retries=8 CMD curl --fail http://localhost:2114/health?plugin=http || exit 1

###########################################
# Build scheduler container
###########################################
FROM base AS scheduler

ARG ROOT=/var/www
ARG ENVIRONMENT=prod
# Accepted values: app, scheduler, testing
ARG CONTAINER_MODE=scheduler

ENV CONTAINER_MODE=${CONTAINER_MODE} \
    ROOT=${ROOT} \
    ENVIRONMENT=${ENVIRONMENT}

WORKDIR $ROOT

RUN wget -q "https://github.com/aptible/supercronic/releases/download/v0.1.12/supercronic-linux-amd64" \
               -O /usr/bin/supercronic \
          && chmod +x /usr/bin/supercronic \
          && mkdir -p /etc/supercronic \
          && mkdir -p /var/log/supercronic;

COPY . ${ROOT}
COPY --from=deployment_vendor ${ROOT}/vendor vendor

COPY .deployment/${ENVIRONMENT}/supercronic/* /etc/supercronic/
COPY .deployment/${ENVIRONMENT}/app/conf.d/php.ini /usr/local/etc/php/conf.d/app.ini
COPY .deployment/${ENVIRONMENT}/app/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

RUN chmod +x .deployment/shared/entrypoint.sh
RUN cat .deployment/shared/utilities.sh >> ~/.bashrc

ENTRYPOINT [".deployment/shared/entrypoint.sh"]
