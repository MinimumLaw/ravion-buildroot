#!/bin/sh

serviceAP="SpeakServise" # SSID wifi AP for search
nowWIFI=`iwconfig wlan0 | grep ESSID | awk -F '"' {'print $2'}`
if [ "$nowWIFI" == "$serviceAP" ]; then
exit 1
fi
searCH=`sudo iwlist wlan0 scanning |grep ESSID|grep $serviceAP| awk -F '"' {'print $2'}`
if [ "$serviceAP" == "$searCH" ]; then
echo "Switch WIFI network to $serviceAP"
sudo ifconfig wlan0 down
sleep 1
sudo ifconfig wlan0 up
sleep 2
sudo iwconfig wlan0
checkWIFI=`iwconfig wlan0 | grep ESSID | awk -F '"' {'print $2'}`
if [ "$serviceAP" == "$checkWIFI" ]; then
echo "Done"
else
echo "Fail - now network $checkWIFI"
fi fi
