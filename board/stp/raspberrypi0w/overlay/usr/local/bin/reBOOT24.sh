#!/bin/sh
hW=`ps -A|grep "reBOOT24"|wc -l|tr -d '\n\r'`
if [ "$hW" -le "3" ];then
sleep 20h
/sbin/reboot
fi
