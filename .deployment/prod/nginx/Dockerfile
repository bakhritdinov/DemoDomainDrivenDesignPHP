FROM nginx

RUN apt update && apt install -y python3-certbot-nginx \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /etc/nginx/conf.d/default.conf

COPY .deployment/prod/nginx/conf.d/ /etc/nginx/conf.d/
