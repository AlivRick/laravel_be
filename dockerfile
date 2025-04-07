FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Cài đặt ImageMagick và PHP Imagick extension
RUN apt-get update && apt-get install -y \
    imagemagick \
    libmagickwand-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài đặt PHP Imagick extension
RUN pecl install imagick \
    && docker-php-ext-enable imagick
    
# Thêm dòng này để cấp quyền từ đầu
RUN chown -R www-data:www-data /var/www && chmod -R 775 /var/www

CMD ["php-fpm"]