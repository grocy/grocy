FROM php:7.2-fpm-alpine
LABEL maintainer="Talmai Oliveira <to@talm.ai>"

RUN docker-php-ext-install opcache

#COPY grocy.tar.gz .
RUN echo $PWD
RUN mkdir /var/www/html/grocy-dev
ADD ./ /var/www/html/grocy-dev/
RUN     apk update && \
        apk upgrade && \
        apk add --no-cache --update yarn git python py-pip wget freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev && \
        docker-php-ext-configure gd \
            --with-gd \
            --with-freetype-dir=/usr/include/ \
            --with-png-dir=/usr/include/ \
            --with-jpeg-dir=/usr/include/ && \
        NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
        docker-php-ext-install -j${NPROC} gd && \
        mkdir -p /www && \
    # Set environments
        sed -i "s|;*daemonize\s*=\s*yes|daemonize = no|g" /usr/local/etc/php-fpm.conf && \
        sed -i "s|;*listen\s*=\s*127.0.0.1:9000|listen = 9000|g" /usr/local/etc/php-fpm.conf && \
        sed -i "s|;*listen\s*=\s*/||g" /usr/local/etc/php-fpm.conf && \
        sed -i "s|;*chdir\s*=\s*/var/www|chdir = /www|g" /usr/local/etc/php-fpm.d/www.conf && \
        #echo "[opcache]" >> /usr/local/etc/php-fpm.d/www.conf && \
        #echo "zend_extension=opcache.so" >> /usr/local/etc/php-fpm.conf && \
        #echo "opcache.enable=1" >> /usr/local/etc/php-fpm.d/www.conf && \
        #echo "opcache.memory_consumption=128" >> /usr/local/etc/php-fpm.d/www.conf && \
        #echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php-fpm.d/www.conf && \
        wget https://getcomposer.org/installer -O - -q | php -- --quiet && \
        #pip install lastversion==0.2.4 && \
        #mkdir -p /tmp/download && \
        #wget -t 3 -T 30 -nv -O "grocy.tar.gz" $(lastversion --source grocy/grocy) && \
        #tar xzf grocy.tar.gz && \
        #rm -f grocy.tar.gz && \
        cd grocy-dev && \
        echo $PWD && \
        ls -lrt . && \
        mv public /www/public && \
        mv controllers /www/controllers && \
        mv data /www/data && \
        mv helpers /www/helpers && \
        mv localization/ /www/localization && \
        mv middleware/ /www/middleware && \
        mv migrations/ /www/migrations && \
        mv publication_assets/ /www/publication_assets && \
        mv services/ /www/services && \
        mv views/ /www/views && \
        mv .yarnrc /www/ && \
        mv *.php /www/ && \
        mv *.json /www/ && \
        mv composer.* /root/.composer/ && \
        mv *yarn* /www/ && \
        mv *.sh /www/ && \
    # Cleaning up
        rm -rf /tmp/download && \
        rm -rf /var/cache/apk/*


# run php composer.phar with -vvv for extra debug information
RUN cd /var/www/html && \
        php composer.phar --working-dir=/www/ -n install && \
        cp /www/config-dist.php /www/data/config.php && \
        cd /www && \
        yarn install && \
        if [ -d /www/data/viewcache ]; then rm -rf /www/data/viewcache; fi && \
        mkdir /www/data/viewcache && \
        chown www-data:www-data -R /www/

# Set Workdir
WORKDIR /www/public

# Expose volumes
#VOLUME ["/www"]

# Expose ports
EXPOSE 9000
