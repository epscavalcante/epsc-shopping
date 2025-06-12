FROM php:8.4-cli

# set your user name, ex: user=ourname
ARG user=application
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install zip -y

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Enable xDebug
RUN pecl install xdebug && docker-php-ext-enable xdebug && \
    echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN curl https://frankenphp.dev/install.sh | sh \
    && mv frankenphp /usr/local/bin

# Set working directory
WORKDIR /var/www

USER $user

CMD [ "tail", "-f", "/dev/null" ]

# CMD [ "frankenphp", "php-server", "-r", "public/" ]