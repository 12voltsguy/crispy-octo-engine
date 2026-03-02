#!/bin/sh

apk update
apk add php8 php8-dev php8-pear php8-pdo php8-openssl autoconf make g++
apk add unixodbc-dev

ln -s /usr/bin/php8 /usr/bin/php
ln -s /usr/bin/phpize8 /usr/bin/phpize
ln -s /usr/bin/pecl8 /usr/bin/pecl
ln -s /usr/bin/php-config8 /usr/bin/php-config

curl -O https://download.microsoft.com/download/b/9/f/b9f3cce4-3925-46d4-9f46-da08869c6486/msodbcsql18_18.0.1.1-1_amd64.apk
apk add --allow-untrusted msodbcsql18_18.0.1.1-1_amd64.apk

apk add php8-apache2 apache2
pecl install sqlsrv
pecl install pdo_sqlsrv
echo extension=pdo_sqlsrv.so >> `php --ini | grep "Scan for additional .ini files" | sed -e "s|.*:\s*||"`/10_pdo_sqlsrv.ini
echo extension=sqlsrv.so >> `php --ini | grep "Scan for additional .ini files" | sed -e "s|.*:\s*||"`/20_sqlsrv.ini