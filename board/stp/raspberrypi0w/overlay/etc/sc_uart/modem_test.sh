#!/bin/sh

##ѕровер€ем что там с модемом и тр€сем его если беда
timeout=300
while [ 1 = 1 ]; do
sleep $timeout
havePING=`ping -I ppp0 -c 4 -q 95.161.142.79| tail -2| head -1| awk -F "%" {'print $1'}| awk {'print $NF'}`
if [ "$havePING" != "0" ]; then
havePING=`ping -I ppp0 -c 4 -q 95.161.142.79| tail -2| head -1| awk -F "%" {'print $1'}| awk {'print $NF'}`
        if [ "$havePING" == "100" ]; then
        sudo echo "1" > /sys/class/gpio/gpio23/value
        sleep 1
        sudo echo "0" > /sys/class/gpio/gpio23/value
        sleep 1
        sudo echo "1" > /sys/class/gpio/gpio23/value
        sleep 1
        sudo echo "0" > /sys/class/gpio/gpio23/value
        fi
fi
done