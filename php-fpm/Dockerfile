FROM php:7.2-fpm

ARG GIT_REPO=https://github.com/arionum/node.git
ARG MYSQL_DATABASE
ARG MYSQL_USER
ARG MYSQL_PASSWORD

WORKDIR /var/www/node

## php-fpm configuration
COPY php.ini /usr/local/etc/php/conf.d/php.override.ini

## dependencies for php modules and node installation
RUN export DEBIAN_FRONTEND=noninteractive && apt-get update -qq && \
apt-get install -y -qq libgmp-dev git procps curl mariadb-client && rm -rf /var/lib/apt/lists/* && \
ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h

## install required modules
RUN docker-php-ext-install gmp pdo_mysql mbstring bcmath

## clone arionum node code
RUN mkdir -p /var/www && cd /var/www && git clone ${GIT_REPO} && cd node && chmod 777 tmp -R

## configure node
RUN cd /var/www/node/include/ && mv config-sample.inc.php config.inc.php && \
sed -i -r "s/ENTER-DB-NAME/$MYSQL_DATABASE/g" /var/www/node/include/config.inc.php && \
sed -i -r "s/ENTER-DB-USER/$MYSQL_USER/g" /var/www/node/include/config.inc.php && \
sed -i -r "s/ENTER-DB-PASS/$MYSQL_PASSWORD/g" /var/www/node/include/config.inc.php && \
sed -i -r "s/localhost/arionum-mariadb/g" /var/www/node/include/config.inc.php && \
echo '$_config["passive_peering"] = true;'>>/var/www/node/include/config.inc.php && \
echo '$_config["sanity_rebroadcast_height"] = 1;'>>/var/www/node/include/config.inc.php && \
echo '$_config["transaction_propagation_peers"] = 100;'>>/var/www/node/include/config.inc.php && \
echo '$_config["max_peers"] = 100;'>>/var/www/node/include/config.inc.php && \
echo '$_config["sanity_interval"] = 900;'>>/var/www/node/include/config.inc.php

COPY start.sh /root/start.sh
COPY import.php /var/www/node/import.php

RUN chmod 0755 /root/start.sh

ENTRYPOINT /root/start.sh

EXPOSE 9000
