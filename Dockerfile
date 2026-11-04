# PHP
FROM php:7.4-apache

# Modules apache
RUN a2enmod headers deflate expires rewrite
EXPOSE 80

# Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# RUN composer --version

# Permet de basculer vers les dépôts d'archive de Debian Bullseye
RUN echo "deb http://archive.debian.org/debian/ bullseye main" > /etc/apt/sources.list \
    && echo "deb http://archive.debian.org/debian-security/ bullseye-security main" >> /etc/apt/sources.list

# Paquets nécessaires à l'installation de WordPress/WordPlate + utiles
RUN apt-get update -o Acquire::Check-Valid-Until=false \
    && apt-get install -y -o Acquire::Check-Valid-Until=false zip unzip vim

# Extensions MySQL pour PHP/WordPress
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Virtualhost
# COPY Docker-vhost.conf /etc/apache2/sites-enabled/docker-vhost-wp.conf
# Copie notre vhost dans sites-available
COPY Docker-vhost.conf /etc/apache2/sites-available/docker-vhost-wp.conf
# Désactive le site par défaut et active le notre
RUN a2dissite 000-default && a2ensite docker-vhost-wp

# Redémarrage de Apache pour tenir compte des modifications + modules installés
# RUN service apache2 restart

# Dossier de travail
WORKDIR /var/www/html