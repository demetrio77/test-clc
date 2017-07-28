#!/bin/bash

set -e

LIST_VARIABLE=(DB_HOST DB_NAME DB_USER DB_PASS \
BASE_DOMAIN \
DEVELOPMENT \
)

[[ -f /etc/apache2/conf-enabled/env.conf ]] && rm /etc/apache2/conf-enabled/env.conf

for VAR in ${LIST_VARIABLE[@]}
do
	[[ ! -z ${VAR} ]] && echo SetEnv ${VAR} `printenv ${VAR}` >> /etc/apache2/conf-enabled/env.conf
done

# Для настроек доменов в Apache
echo export BASE_DOMAIN_RAW=`cut -d ":" -f 1 <<< ${BASE_DOMAIN}` > /etc/apache2/envmy

# Настройка Apache
rm -rf /etc/apache2/sites-enabled/*
cp -s /etc/apache2/sites-available/apache.conf /etc/apache2/sites-enabled/
trap "apache2ctl stop; exit" SIGINT SIGTERM

#########################
# apache2-foreground

: "${APACHE_CONFDIR:=/etc/apache2}"
: "${APACHE_ENVVARS:=$APACHE_CONFDIR/envvars}"
if test -f "$APACHE_ENVVARS"; then
	. "$APACHE_ENVVARS"
fi

# Apache gets grumpy about PID files pre-existing
: "${APACHE_PID_FILE:=${APACHE_RUN_DIR:=/var/run/apache2}/apache2.pid}"
rm -f "$APACHE_PID_FILE"

exec apache2 -DFOREGROUND "$@"
