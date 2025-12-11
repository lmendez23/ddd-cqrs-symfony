#!/bin/sh
set -e

if [ "$APP_ENV" != 'prod' ]; then
  composer install --prefer-dist --no-progress --no-interaction
fi

if grep -q ^DATABASE_URL= .env; then
  if [ "$(find ./migrations -iname '*.php' -print -quit)" ]; then
    bin/console doctrine:migrations:migrate --no-interaction
  fi
fi

if command -v setfacl >/dev/null 2>&1; then
  setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
  setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
else
  chown -R www-data:www-data var || true
  chmod -R g+rwX var || true
fi

/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
