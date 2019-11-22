FROM ubuntu:bionic

ARG GIT_REPO


WORKDIR /var/www/node

## install nginx
RUN export DEBIAN_FRONTEND=noninteractive && useradd nginx && apt-get update -qq && apt-get install -y -qq git nginx  && rm -rf /var/lib/apt/lists/* 

## install node
RUN mkdir -p /var/www && cd /var/www && git clone ${GIT_REPO} && cd node && chmod 777 tmp -R


COPY healthcheck/healthcheck /usr/local/bin/healthcheck
RUN chmod 755 /usr/local/bin/healthcheck

HEALTHCHECK --interval=25s --timeout=3s --start-period=120s CMD healthcheck

HEALTHCHECK --interval=5m --timeout=3s CMD curl --fail http://localhost || exit 1


CMD ["nginx"]

EXPOSE 80 443
