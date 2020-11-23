FROM php:7.4.5-fpm

RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev ssh libssh2-1-dev

RUN pecl update-channels && pecl install xdebug
RUN pecl update-channels && pecl install ssh2-1.2
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-enable ssh2
RUN docker-php-ext-install curl bcmath opcache pdo_mysql

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

COPY xdebug_conf /
RUN cat /xdebug_conf >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/returns-staging-deployer

CMD ["php-fpm"]
