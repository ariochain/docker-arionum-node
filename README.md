# Arionum-Node in Docker

Docker running Nginx, PHP-FPM, MariaDB and Arionum cryptocurrency node


## Preparation

- create directory for persistent data

```bash
mkdir -p ~/data/arionum-mariadb-data/
```

## Usage  

```bash
docker-compose up
```


## Interact with Arionum-Node

- exposed port is 8080

Open link at [localhost:8080](http://localhost:8080)


## Update node code

- display running containers

```bash
docker ps
```

- locate php-fpm container id or container name and perform update

```bash
docker exec -ti <PHP_FPM_CONTAINER_ID> git pull
```

## Troubleshooting

- manualy run sanity

```bash
docker exec -ti <PHP_FPM_CONTAINER_ID> php sanity.php
```

- remove sanity-lock

```bash
docker exec -ti <PHP_FPM_CONTAINER_ID> rm tmp/sanity-lock
```

- remove last 100 blocks

```bash
docker exec -ti <PHP_FPM_CONTAINER_ID> php util.php pop 100
```

- clean database

```bash
docker exec -ti <PHP_FPM_CONTAINER_ID> php util.php clean
```
