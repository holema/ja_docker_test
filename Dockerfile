FROM thecodingmachine/php:7.4.27-v4-apache-node16
ENV PHP_EXTENSION_LDAP=1
ENV PHP_EXTENSION_INTL=1
USER root
RUN usermod -a -G www-data docker
COPY . /var/www/html
RUN npm install
RUN composer install
RUN mkdir -m 777 -p public/build
RUN npm run build
RUN chmod -R 775 public/build
RUN mkdir -p var/cache
RUN chown -R www-data:www-data var
RUN chmod -R 775 var

USER docker