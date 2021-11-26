# Application's image will be based on...
FROM php:8.0-apache

# Since web application should be served from `/public` subdirectory change the document root in Apache configuration
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Make Rewrite engine enabled in Apache
RUN ln -s -t /etc/apache2/mods-enabled ../mods-available/rewrite.load

# Install Composer into the image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install PHP dependencies using helper from https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN install-php-extensions gd intl xdebug zip
