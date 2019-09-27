FROM ubuntu:bionic

ARG GIT_REPO

WORKDIR /var/www/node

## install nginx
RUN export DEBIAN_FRONTEND=noninteractive && useradd nginx && apt-get update -qq && apt-get install -y -qq git nginx  && rm -rf /var/lib/apt/lists/* 

## install node
RUN mkdir -p /var/www && cd /var/www && git clone ${GIT_REPO} && cd node && chmod 777 tmp -R

CMD ["nginx"]

EXPOSE 80 443
