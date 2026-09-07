FROM php:8.2-apache

RUN docker-php-ext-install mysqli \
    && a2enmod rewrite

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

CMD sed -i -e "s/^Listen 80/Listen ${PORT:-8080}/" -e "s/\*:80/*:${PORT:-8080}/" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf \
    && apache2-foreground