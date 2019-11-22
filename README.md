
# Arionum-Node in Docker

Docker running Nginx, PHP-FPM, MariaDB and Arionum cryptocurrency node

## ubuntu - docker instalation

```bash 
sudo bash install-docker-ce.sh
```

## Preparation

- create directory for persistent data

```bash
mkdir -p ~/data/arionum-mariadb-data/
```

## Usage 

-starts all containers in background

```bash
docker-compose up --build -d
```

## Stop all containers

```bash
docker stop $(docker ps -a -q)
```

## Interact with Arionum-Node

- exposed port is 80

Open link at [localhost:80](http://localhost:80) or your external IP address


## Update node code

- display running containers

```bash
docker ps
```

- locate php-fpm container id or container name and perform update

```bash
docker exec -ti <PHP_FPM_CONTAINER_ID> git pull
```
- or you can try to automaticaly select container id

```bash
docker exec -ti `docker ps|grep arionum|grep php-fpm|awk '{print $1}'` git pull
```

## Troubleshooting

- manualy run sanity

```bash
docker exec -ti <PHP_FPM_CONTAINER_ID> php sanity.php
```
```bash
docker exec -ti `docker ps|grep arionum|grep php-fpm|awk '{print $1}'` php sanity.php
```

- remove sanity-lock

```bash
docker exec -ti <PHP_FPM_CONTAINER_ID> rm tmp/sanity-lock
```
```bash
docker exec -ti `docker ps|grep arionum|grep php-fpm|awk '{print $1}'` rm tmp/sanity-lock
```

- remove last 100 blocks

```bash
docker exec -ti <PHP_FPM_CONTAINER_ID> php util.php pop 100
```
```bash
docker exec -ti `docker ps|grep arionum|grep php-fpm|awk '{print $1}'` php util.php pop 100
```

- clean database

```bash
docker exec -ti <PHP_FPM_CONTAINER_ID> php util.php clean
```
```bash
docker exec -ti `docker ps|grep arionum|grep php-fpm|awk '{print $1}'` php util.php clean
```
