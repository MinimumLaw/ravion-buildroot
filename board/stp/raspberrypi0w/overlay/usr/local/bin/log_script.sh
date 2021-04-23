#!/bin/sh
####Config#### v0.1
config=/etc/logger/logger.conf
log=/overlay/update/logLife
dailyReboot=`grep dailyReboot /etc/logger/logger.conf | awk -F "=" {'print $2'}|tr -d '\r\n'`
busyCPU=`grep busyCPU /etc/logger/logger.conf | awk -F "=" {'print $2'}|tr -d '\r\n'`
minRAM=`grep minRAM /etc/logger/logger.conf | awk -F "=" {'print $2'}|tr -d '\r\n'`
####Functions####
configOut() {
echo -e "Daily reboot in $dailyReboot hours\nCritical CPU load - $busyCPU\nCritical free RAM - $minRAM"|tee -a $log
}
swapLog() {
numLogs=`ls|grep logLife|wc -l`
mv $log ${log}.$numLogs
}
restartSystem() {
echo -e "$(date +%Y-%m-%d\_%H:%M:%S) - $@"|tee -a $log
swapLog
sudo reboot ####### --------------------------------------------- uncomment if want enable reboot
}
checkMem() {
freeMem=`cat /proc/meminfo | grep "MemFree:" | awk {'print $2'}`
if [ "$freeMem" -le "$minRAM" ]; then
echo -e "$(date +%Y-%m-%d\_%H:%M:%S) - Out of memory, now is $freeMem kB."|tee -a $log
swapLog
sudo reboot ####### --------------------------------------------- uncomment if want enable reboot
fi
}
checkWwan() {
ipWwan=`ip addr show dev wwan0|grep -w inet|awk {'print $2'}|awk -F "/" {'print $1'}|awk -F "." {'print $1'}`
if [ "x$ipWwan" != "x" ]; then
	if [ "$ipWwan" == "169" ]; then
	echo -e "$(date +%Y-%m-%d\_%H:%M:%S) - Modem is die, restart service."|tee -a $log
	qmi-network /dev/cdc-wdm0 stop
	qmi-network /dev/cdc-wdm0 start
	ifconfig wwan0 down
	ifconfig wwan0 up
	fi
fi
}

rfkillOut() {
tenMin=`echo $lifetime | awk -F ":" {'print $2'} | cut -c 2- | sed 's/,//g'`
if [ "$tenMin" == "0" ]; then
rfkill list | tee -a $logfile
fi
}
oomKillerConf() {
nowScore=`cat /proc/$pidCron/oom_score_adj`
if [ "$nowScore" == "0" ]; then
sudo echo -200 > /proc/$pidCron/oom_score_adj && nowScore=`cat /proc/$pidCron/oom_score_adj`
echo -e "Change priority for CRON - now is $nowScore" | tee -a $log
else
echo -e "Now priority for CRON - $nowScore" | tee -a $log
fi
}
####Data collection#####

lifetime=`uptime | awk -F "up " {'print $2'} | awk -F " load" {'print $1'}| sed 's/ //g'`
load15now=`uptime|awk {'print $NF'}|awk -F "." {'print $1'}`
pidKalug=`pidof sc_uart`
pidCron=`pidof crond`
tempCPU=`cat /sys/class/thermal/thermal_zone0/temp | cut -c -2`

####Script####
if [ "$lifetime" == "1min," ]; then echo -e "\n\nStart system"|tee -a $log;oomKillerConf;configOut; fi
echo -e "---------------\n $(date +%Y-%m-%d\_%H:%M:%S) --- uptime $lifetime
CPU 15 min - $load15now
tempCPU - $tempCPU" | tee -a $log
cat /proc/meminfo | head -12 | grep -v "SwapCached" | tee -a $log
rfkillOut
if [ "x$pidKalug" != "x" ]; then
echo "Pid SC_UART - $pidKalug" | tee -a $log
else
echo "SC_UART not worked ! ! !" | tee -a $log
fi
if [ "x$pidCron" != "x" ]; then
echo "Pid CROND - $pidCron" | tee -a $log
else
restartSystem CRONTAB not worked ! ! !\\nSystem need to restart... Restarting
fi
if [ "$load15now" -ge "$busyCPU" ]; then
restartSystem CPU load averege is more thet $busyCPU\\nSystem need to restart
fi
checkMem
checkWwan
if [ "$(echo $lifetime|awk -F ":" {'print $1'})" == "$dailyReboot" ]; then
restartSystem Restarted system at $dailyReboot hours of work
fi