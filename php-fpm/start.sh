#!/bin/bash

#configure php
cd /usr/local/etc/php-fpm.d

if [ ! -d arionum ]; then
  mkdir arionum;
  cd arionum;
  echo ''>www.conf;
  echo 'pm.status_path = /status'>>www.conf;
  echo 'ping.path = /ping'>>www.conf;
  cd ..;
  cat arionum/www.conf >> www.conf;
fi

cd /var/www/

#clone node if there is no directory existing
if [ ! -d node ]; then
  git clone https://github.com/arionum/node
fi

#copy default config
cp /root/config.inc.php /var/www/node/include/

cd /var/www/node
# finalize setup
echo "initializing...";

curl -s -o peers.txt -L https://api.arionum.com/peers.txt > /dev/null
PEERS=`cat peers.txt`;

CONFIG_TMP=/tmp/peers.config.php

echo '$_config["initial_peer_list"] = [' > $CONFIG_TMP

for ROW in ${PEERS};
 do
   # PEER=`echo $ROW |cut -d '-' -f 1`
   echo "'$ROW',">>$CONFIG_TMP
 done

sed -i '$ s/,//' $CONFIG_TMP

echo '];' >> $CONFIG_TMP

cat $CONFIG_TMP>>/var/www/node/include/config.inc.php
rm $CONFIG_TMP

current_dir=`pwd`;

cd /var/www/node/
# curl -s -o import.php -L https://www.ariochain.info/import.txt > /dev/null
# sleep 60s;

# start php service
php-fpm

cd $current_dir;


