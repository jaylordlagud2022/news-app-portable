# Use an official PHP runtime as a parent image
FROM php:8.0-fpm

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy composer.json and composer.lock into the container at /var/www/html
COPY composer.json composer.lock /var/www/html/

# Install any needed packages specified in composer.json
RUN apt-get update && apt-get install -y \
    git \
    libzip-dev \
    && docker-php-ext-install zip pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Run composer install to install dependencies
COPY . .

RUN composer install
RUN php artisan key:generate

# Copy the contents of the local directory into the container at /var/www/html

# Ensure that the storage and bootstrap/cache directories are writable
RUN chown -R www-data:www-data storage bootstrap/cache

# Make port 80 available to the world outside this container
EXPOSE 80

# Define environment variable
ENV NAME laravel-docker

# Start PHP-FPM and Laravel serve
CMD ["bash", "-c", "php-fpm & php artisan serve --host=0.0.0.0 --port=8000"]
