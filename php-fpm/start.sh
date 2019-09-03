#!/bin/bash

# finalize setup
echo "php ready";

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

# php
php-fpm
