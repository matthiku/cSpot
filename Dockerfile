FROM ubuntu:16.04
MAINTAINER Chris Walsh <chris24walsh@gmail.com>
LABEL Description="LAMP stack for the cSpot (Church Services Online Planning Tool), based on Ubuntu 16.04 LTS. Includes dependencies, such as PHP7.2 and composer. Derived from the generic PHP 7.0 LAMP stack image maintained at fauria/lamp, https://hub.docker.com/r/fauria/lamp/dockerfile" \
    License="Apache License 2.0" \
    Version="1.0"


### Install project dependencies

# Update/upgrade apt packages
RUN apt-get update && apt-get upgrade -y

# Install repo required for php7.2
RUN apt-get install -y python-software-properties software-properties-common apt-utils \
    && LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php \
    && apt-get update

# Install php7.2 and plugins
RUN apt-get install -y \
    php7.2 \
    php7.2-curl \
    php7.2-dom \
    php7.2-mbstring \
    php7.2-mysql \
    php7.2-zip \
    unzip

# Install apache, mysql and composer
RUN apt-get install -y apache2 libapache2-mod-php7.2 \
    mariadb-common mariadb-server mariadb-client

# Install latest composer manually
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'e0012edf3e80b6978849f5eff0d4b4e4c79ff1609dd1e613307e16318854d24ae64f26d17af3ef0bf7cfb710ca74755a') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && mv composer.phar /usr/bin/composer \
    && php -r "unlink('composer-setup.php');"


### Install cSpot

# Copy the project to the apache root directory
COPY ./ /var/www/html/cSpot/

# Set the project working directory
WORKDIR /var/www/html/cSpot

# (???) Have to copy the below folder from lower-case to upper-case, since it is referenced as upper-case in the project files
RUN cp -r app/ App/

# Create mysql database/user and initialise the db
RUN service mysql start \
    && cat docker/cspot-mysql.sql | mysql \
    && cp docker/.env.example .env \
    && composer install \
    && php artisan key:generate \
    && echo "admin@example.com" | php artisan migrate

# Set up project for Apache and create Virtualhost for cspot
RUN cp docker/cspot-apache.conf /etc/apache2/sites-available \
    && chown -R www-data:www-data ./ \
    && chmod -R 755 ./storage \
    && a2dissite 000-default.conf \
    && a2ensite cspot-apache.conf \
    && a2enmod rewrite \
    && service apache2 restart


### Start the app

# Open port 80 for the webserver
EXPOSE 80

# Start mysql, apache, set the administrator password if supplied, and tail the logs (to keep the container shell open)
CMD service mysql start \
    && if [ $admin_email ]; then echo "update cspot.users set email='${admin_email}' where id=1;" | mysql; fi \
    && service apache2 start \
    && tail -f /var/log/apache2/*
