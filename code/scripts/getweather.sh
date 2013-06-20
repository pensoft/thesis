#!/bin/bash

cd /var/www/lastmin/items/weather

wget -r -nH --cut-dirs=100 --random-wait --wait 4 --user-agent="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)" \
http://xml.weather.yahoo.com/forecastrss?p=BUXX0005\&u=c \
http://xml.weather.yahoo.com/forecastrss?p=BUXX0007\&u=c \
http://xml.weather.yahoo.com/forecastrss?p=BUXX0004\&u=c \
http://xml.weather.yahoo.com/forecastrss?p=BUXX0001\&u=c \
http://xml.weather.yahoo.com/forecastrss?p=BUXX0009\&u=c \
http://xml.weather.yahoo.com/forecastrss?p=BUXX0011\&u=c

