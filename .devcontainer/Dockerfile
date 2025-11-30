FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
	git \
	curl \
	libpng-dev \
	libonig-dev \
	libxml2-dev \
	libzip-dev \
	libicu-dev \
	sqlite3 \
	libsqlite3-dev \
	unzip \
	nodejs \
	npm \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd \
	&& docker-php-ext-install -j$(nproc) \
	gd \
	intl \
	pdo \
	pdo_sqlite \
	mbstring \
	zip \
	fileinfo

# Enable Apache modules
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Yarn
RUN npm install -g yarn

# Set working directory
WORKDIR /workspaces/grocy

# Configure Apache to point to public directory
ENV APACHE_DOCUMENT_ROOT=/workspaces/grocy/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set permissions for data directory
RUN mkdir -p /workspaces/grocy/data && chmod 777 /workspaces/grocy/data
