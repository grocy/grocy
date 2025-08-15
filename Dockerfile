# Use the same base image as linuxserver/grocy for consistency
FROM ghcr.io/linuxserver/baseimage-alpine-nginx:3.21

# Set the default language to Chinese
ENV GROCY_DEFAULT_LOCALE=zh_CN

# Install build and runtime dependencies, same as linuxserver/grocy
RUN \
  echo "**** install build packages ****" && \
  apk add --no-cache --virtual=build-dependencies \
    git \
    yarn \
    composer && \
  echo "**** install runtime packages ****" && \
  apk add --no-cache \
    php83-gd \
    php83-intl \
    php83-ldap \
    php83-opcache \
    php83-pdo \
    php83-pdo_sqlite \
    php83-tokenizer \
    php83-fileinfo \
    php83-ctype \
    php83-zlib \
    php83-mbstring && \
  echo "**** configure php-fpm to pass env vars ****" && \
  sed -E -i 's/^;?clear_env ?=.*$/clear_env = no/g' /etc/php83/php-fpm.d/www.conf && \
  grep -qxF 'clear_env = no' /etc/php83/php-fpm.d/www.conf || echo 'clear_env = no' >> /etc/php83/php-fpm.d/www.conf

# Set up the application directory
RUN mkdir -p /app/www/

# Copy the application source code into the container
# We are in the root of the project, so this copies everything
COPY . /app/www/

# Remove the original data directory from the source and symlink it to the /config volume's data subdir.
# This ensures Grocy uses the persistent storage for its database and config,
# and is compatible with the volume structure from linuxserver/grocy.
RUN rm -rf /app/www/data && \
    ln -s /config/data /app/www/data

# Install dependencies and perform cleanup
RUN \
  echo "**** install composer packages ****" && \
  composer install -d /app/www --no-dev --optimize-autoloader && \
  echo "**** install yarn packages ****" && \
  cd /app/www && \
  yarn --production && \
  yarn cache clean && \
  echo "**** cleanup build packages ****" && \
  apk del --purge \
    build-dependencies && \
  rm -rf \
    /tmp/* \
    $HOME/.cache \
    $HOME/.composer

# The linuxserver.io base image handles nginx config and service startup.
# It's pre-configured to serve a PHP application from /app/www/public.
# It also handles the PUID/PGID logic for permissions on /config volume.
# So, we don't need to copy nginx configs or write startup scripts.

# Expose standard web ports
EXPOSE 80 443

# Define the volume for persistent data
VOLUME /config
