FROM php:7.3-fpm

ARG GIT_REPO=https://github.com/arionum/node.git
ARG MYSQL_DATABASE
ARG MYSQL_USER
ARG MYSQL_PASSWORD

WORKDIR /var/www

## php-fpm configuration
COPY php.ini /usr/local/etc/php/conf.d/php.override.ini

## php-fpm checker
COPY php-fpm-healthcheck/php-fpm-healthcheck /usr/local/bin/php-fpm-healthcheck
RUN chmod 755 /usr/local/bin/php-fpm-healthcheck

## dependencies for php modules and node installation
RUN export DEBIAN_FRONTEND=noninteractive && apt-get update -qq && \
apt-get install -y -qq libgmp-dev git procps curl mariadb-client libfcgi-bin && rm -rf /var/lib/apt/lists/* && \
ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h

## install required modules
RUN docker-php-ext-install gmp pdo_mysql mbstring bcmath

## clone arionum node code
RUN mkdir -p /var/www && cd /var/www && rm -Rf node && git clone ${GIT_REPO} && cd node && chmod 777 tmp -R && mkdir -p /var/log && cd /var/log && touch aro.log && chmod 777 aro.log && chown www-data:www-data aro.log

## configure node
RUN curl -s -o /root/config.inc.php https://raw.githubusercontent.com/arionum/node/master/include/config-sample.inc.php > /dev/null && \
sed -i -r "s/ENTER-DB-NAME/$MYSQL_DATABASE/g" /root/config.inc.php && \
sed -i -r "s/ENTER-DB-USER/$MYSQL_USER/g" /root/config.inc.php && \
sed -i -r "s/ENTER-DB-PASS/$MYSQL_PASSWORD/g" /root/config.inc.php && \
sed -i -r "s/localhost/arionum-mariadb/g" /root/config.inc.php && \
echo ''>>/root/config.inc.php && \
echo '$_config["enable_logging"] = true;'>>/root/config.inc.php && \
echo '$_config["log_file"] = "/var/log/aro.log";'>>/root/config.inc.php && \
echo '$_config["log_verbosity"] = 3;'>>/root/config.inc.php && \
echo '$_config["allow_hostname_change"] = true;'>>/root/config.inc.php && \
echo '$_config["passive_peering"] = true;'>>/root/config.inc.php && \
echo '$_config["max_peers"] = 1000;'>>/root/config.inc.php && \
echo '$_config["sanity_interval"] = 900;'>>/root/config.inc.php

WORKDIR /var/www/node

COPY start.sh /root/start.sh

RUN chmod 0755 /root/start.sh

EXPOSE 9000

HEALTHCHECK --interval=25s --timeout=3s --start-period=120s \
    CMD php-fpm-healthcheck


ENTRYPOINT ["/root/start.sh"]

# CMD ["php-fpm"]
