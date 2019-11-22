#!/bin/bash

apt-get remove docker docker-engine docker.io containerd runc -y

apt-get update

apt-get upgrade -y

apt-get install apt-transport-https ca-certificates curl gnupg-agent software-properties-common git -y
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add - 
apt-key fingerprint 0EBFCD88
add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"
apt-get update
apt-get install docker-ce docker-ce-cli containerd.io docker-compose -y

cd

git clone https://github.com/ariochain/docker-arionum-node
mkdir -p ~/data/arionum-mariadb-data/
cd docker-arionum-node/

docker-compose up --build -d
