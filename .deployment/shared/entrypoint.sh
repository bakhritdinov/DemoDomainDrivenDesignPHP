#!/usr/bin/env bash
set -e

container_mode=${CONTAINER_MODE:-app}
echo "Container mode: $container_mode"

php() {
  su app -c "php $*"
}

initialStuff() {
  php /var/www/bin/console doctrine:cache:clear-metadata --env=${ENVIRONMENT:-prod}
  php /var/www/bin/console doctrine:migrations:migrate -n --env=${ENVIRONMENT:-prod}
  php /var/www/bin/console cache:clear --env=${ENVIRONMENT:-prod}
}

initialApplicationServer() {
  vendor/bin/rr get --location bin/
  chmod +x /var/www/bin/rr
}

if [ "$1" != "" ]; then
    exec "$@"
elif [ "$container_mode" = "app" ]; then
    initialApplicationServer
    initialStuff
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.app.conf
elif [ "$container_mode" = "testing" ]; then
    initialApplicationServer
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.testing.conf
elif [ "$container_mode" = "scheduler" ]; then
    initialStuff
    exec supercronic /etc/supercronic/symfony
else
    echo "Container mode mismatched."
    exit 1
fi
