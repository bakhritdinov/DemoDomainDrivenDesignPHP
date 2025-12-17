#!/usr/bin/env bash
set -e

container_mode=${CONTAINER_MODE:-app}
env=${ENVIRONMENT:-prod}

echo "Starting container in [$container_mode] mode (Env: $env)"

php_run() {
  su app -c "php $*"
}

initialStuff() {
  echo "Running migrations..."
  php_run /var/www/bin/console doctrine:cache:clear-metadata --env=env
  php_run /var/www/bin/console doctrine:migrations:migrate -n --env=env
  php_run /var/www/bin/console cache:clear --env=env
}

initialApplicationServer() {
  echo "Preparing RoadRunner..."
  mkdir -p /var/www/bin
  vendor/bin/rr get --location /var/www/bin/
  chmod +x /var/www/bin/rr
}

if [ "$1" != "" ]; then
    exec "$@"

elif [ "$container_mode" = "app" ] || [ "$container_mode" = "testing" ]; then
    initialApplicationServer
    if [ "$container_mode" = "app" ]; then
        initialStuff
    fi
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.app.conf

elif [ "$container_mode" = "scheduler" ]; then
    initialStuff
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.scheduler.conf
else
    echo "Error: CONTAINER_MODE '$container_mode' is not supported."
    exit 1
fi
