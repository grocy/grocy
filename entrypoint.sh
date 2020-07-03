#!/bin/bash
APACHE_DOCUMENT_ROOT="/var/www/html/public"

sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf
sed -ri -e "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

composer install
yarn install

usermod -u $DATA_UID www-data
groupmod -g $DATA_UID www-data

exec ${@:-apache2-foreground}