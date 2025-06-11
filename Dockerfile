FROM php:8.3-apache

# Instala dependências de sistema
RUN apt-get update && apt-get install -y \
    libbz2-dev \
    libcurl4-openssl-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    libzip-dev \
    libmcrypt-dev \
    libc-client-dev \
    libkrb5-dev \
    libonig-dev \
    unzip \
    git

# Extensões do PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap

RUN docker-php-ext-install \
    bz2 \
    curl \
    fileinfo \
    gettext \
    intl \
    mbstring \
    exif \
    mysqli \
    pdo_mysql \
    soap \
    zip

# Ativa o mod_rewrite
RUN a2enmod rewrite

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Limpa cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
