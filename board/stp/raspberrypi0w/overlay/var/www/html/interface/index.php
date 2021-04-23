<?php
if(isset($_GET['timeN'])){
    if(isset($_GET['user'])){
        if(isset($_GET['secret'])){
            $secret = "fasterAnDfaster";
            if($secret == $_GET['secret']){
                ////////////////////////////// Проверка заголовка GET запроса
                //// Параметры
                
               
               $time = $_GET['timeN'];
               $user = $_GET['user'];
                function writeToLog($user, $command)
                {   
                    $log = "/overlay/update/php.log";
                    $time = $_GET['timeN'];
                    $logOutput = $time." - ".$user." - ".$command.PHP_EOL;
                    file_put_contents($log, $logOutput, FILE_APPEND);
}
                
                
            if(isset($_GET['command'])){
                $command = $_GET['command'];
                $parseComm = preg_split('/_/', $command);
                if($command == "info"){
                    $sc_uart_mass = parse_ini_file("/etc/sc_uart/config.txt");
                    $type = $sc_uart_mass['informer_type'];
                    $serial = ltrim($sc_uart_mass['serial_number'], '0')."<br/>";
                    $intervalToServ = $sc_uart_mass['timeout']."<br/>";
                    $server = ltrim($sc_uart_mass['ftp_server'], "ftp://");
                    $havePingServ = shell_exec("ping -q -W 1 -c 1 $server | grep packets | awk {'print \$4'}"); if($havePingServ == 1){$havePingServ = "YES";}else{$havePingServ = "NO!";}  
                    
                    echo "Type = ".$type."<br/>Serial = ".$serial."Follow server = ".$server."<br/>Reply interval = ".$intervalToServ."Have ping = ".$havePingServ."<br/><br/>";
                       // UPTIME
                    $uptimeData = array_filter(explode(' ', trim(shell_exec("uptime"))));
                    $systemTime = $uptimeData[0];
                    $uptime = $uptimeData[2];if(empty($uptime)){$uptime = $uptimeData[3];}
                    $load1Min = $uptimeData[6];
                    if(empty($load1Min)||($load1Min == "average:")){
                        $load1Min = $uptimeData[7];
                        $load5Min = $uptimeData[8];
                        $load15Min = $uptimeData[9];
                    }else{
                    $load5Min = $uptimeData[7];
                    $load15Min = $uptimeData[8];}
                    
                    echo "System time = ".$systemTime."<br/>Uptime = ".$uptime."<br/>Load 1min = ".$load1Min."<br/>Load 5min = ".$load5Min."<br/>Load 15min = ".$load15Min."<br/><br/>";
                    ////// CPU 
                        
                        $cpuTemp = shell_exec("t=$(cat /sys/class/thermal/thermal_zone0/temp);tempC=$((\$t/1000)); echo \$tempC");
                        $cpuFreq = shell_exec("c=$(cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq);cpu=$((\$c/1000)); echo \$cpu");
                        $freeMemRAM = shell_exec("cat /proc/meminfo | grep MemFree | awk {'print $2'}");
                        
                        echo "CPU freq = ".$cpuFreq."<br/>CPU Temperature = ".$cpuTemp."<br/>Free RAM = ".$freeMemRAM."<br/><br/>";
                            ///// BLE
                        
                        $macBLE = shell_exec("hcitool dev | tail -n 1 | awk {'print $2'}|tr '[:upper:]' '[:lower:]'");
                        $pidBLE = shell_exec("ps | grep hci | head -1| awk {'print $1'}");  if(empty($pidBLE)){$pidBLE = "-------------------------------------------no PID!";}
                        echo "Interface BLE:<br/>MAC address = ".$macBLE."<br/>PID hci0 = ".$pidBLE."<br/><br/>";

                    // network
                    $netwokrMass = explode(PHP_EOL, shell_exec("ip a"));
                    $activeInterfaces = array_filter(explode(",", shell_exec("ip a | grep \"^[0-9]\" | awk {'print $2'}|sed 's/:/,/g'|tr -d '\n'")));
                    foreach ($activeInterfaces as $interF){
                        if($interF !== "lo"){
                        echo "Interfase - ".$interF.":<br/>";
                        $outNetwork = explode(PHP_EOL, shell_exec("ip addr show dev $interF"));
                        $mac = explode(' ',$outNetwork[1]);
                        print_r("MAC address = ".$mac[5]."<br/>");
                        $ipaddr = explode(' ',$outNetwork[2]);
                        print_r("IP address = ".$ipaddr[5]."<br/>");
                        $rentLostAt = explode(' ',$outNetwork[3]);
                        print_r("Rent address = ".$rentLostAt[8]."<br/><br/>");
                    } }
                                         
                        ////// sc_version
                        $sc_version_mass = parse_ini_file("/var/www/html/sc_version.txt");
                        $sc_uart_version = $sc_version_mass['sc_version'];
                        $sc_uart_pid = shell_exec("ps | grep sc_uart | head -1| awk {'print $1'}"); if(empty($sc_uart_pid)){$sc_uart_pid = "-------------------------------------------no PID!";}
                        $board_version = $sc_version_mass['stm_hrdw'];
                        $stm_firmvare = $sc_version_mass['stm_sftw'];
                        $stm_bootloader = $sc_version_mass['stm_load'];
                        
                        echo "SC_UART ver. = ".$sc_uart_version."<br/>SC_UART PID = ".$sc_uart_pid."<br/>Board version = ".$board_version."<br/>STM firmware = ".$stm_firmvare."<br/>STM bootload = ".$stm_bootloader."<br/>";
                         
                        //////// CORE LINUX
                        
                        $coreVer = shell_exec("cat /etc/stp-release");
                        $compileDateCore = shell_exec("ls -hl --full-time /etc/stp-release | awk {'print $6\" \"$7'}");
                        
                        echo "CORE Linux = ".$coreVer."<br/>Compile date = ".$compileDateCore."<br/><br/>";
                        
                       
                       ///// CONTENT
                        
                        $haveContent = shell_exec("ls /var/www/html/data/| wc -l");
                        if($haveContent == 4){
                            $haveContent = "NO";
                            echo "Transiver is empty<br/>";
                        }else{
                            $incrCity = shell_exec("grep incr /var/www/html/data/city.json | awk -F \"\\\"\" {'print $4'}");
                            if($type == "stationary"){
                                $howMatchLang = explode('stat_', shell_exec("ls /var/www/html/data/text_files/stat*"));
                                $countLang = count($howMatchLang);//--$countLang;
                                $isStopTransiver = trim(shell_exec("ls -hl --full-time /var/www/html/data/text_files/ts_*| wc -l"));
                                echo "Incriment CITY = ".$incrCity."<br/>";
                                if($isStopTransiver > 0){
                                   $type = "stopTrans";
                                   $locateTrans = trim(shell_exec("ls /var/www/html/data/text_files/ts_* | awk -F \"/\" {'print \$NF'} | awk -F \"_\" {'print $2'}"));
                                   echo "STOP Transiver locate = ".$locateTrans."<br/><br/>";
                                   $dateContent = shell_exec("ls -hl --full-time /var/www/html/data/text_files/ts_* | awk {'print $6\" \"$7'}");
                                   $incrMarsh = shell_exec("grep incr /var/www/html/data/text_files/ts_* | awk -F \"\\\"\" {'print $4'}");
                                   echo "Date of content = ".$dateContent."<br/>Locate marsh list = ".$locateTrans."<br/>Incriment Marsh list = ".$incrMarsh."<br/><br/>";
                                }
                                
                                for ($i=1; $i<$countLang; $i++){
                                   $nowLang = explode('/', $howMatchLang[$i]);
                                echo "Content ".trim($nowLang[0])."<br/>";
                                $dateContent = shell_exec("ls -hl --full-time /var/www/html/data/text_files/stat_".trim($nowLang[0])." | awk {'print $6\" \"$7'}");
                                $shortINFO = shell_exec("cat /var/www/html/data/text_files/stat_".trim($nowLang[0])."| awk -F \"\\\"Point1\\\"\\:\" {'print $2'} | awk -F \"\\\",\\\"\" {'print $1'} | sed 's/\\[\"//'|iconv -f cp1251 -t utf-8");
                                echo "Date of content = ".$dateContent."<br/>Short info = ".$shortINFO."<br/>";
                            }
                            
                            }else{
                                if($type == "transport"){
                                    $locateTrans = trim(shell_exec("ls /var/www/html/data/text_files/ts_* | awk -F \"/\" {'print \$NF'} | awk -F \"_\" {'print $2'}"));
                                    $dateContent = shell_exec("ls -hl --full-time /var/www/html/data/text_files/ts_* | awk {'print $6\" \"$7'}");
                                    echo "Date of content = ".$dateContent."<br/>Locate marsh list = ".$locateTrans."<br/>";
                                }}
                                
                                /// Check for big Audio files 
                                $bigAudioFile = shell_exec("ls -l /var/www/html/data/audio_files/|sed 1d | awk {'print $5\" \"$9'} | sort -n | tail -1");
                                $biggerSizeAudio = explode(' ', $bigAudioFile);
                                if($biggerSizeAudio[0] > "4050"){
                                echo "-----------BIG AUDIO FILE ! ! ! = ".$biggerSizeAudio[0]." ".$biggerSizeAudio[1];

                                }
                                }
                        //// Logger conf
                                
                                $loggerCONF = parse_ini_file("/etc/logger/logger.conf");
                                $logFile = $loggerCONF['log'];
                                $timeDaylyReboot = $loggerCONF['dailyReboot'];
                                $busyCPU = $loggerCONF['busyCPU'];
                                $minRAM = $loggerCONF['minRAM'];
                                
                                echo "<br/>Path of log = ".$logFile."<br/>Time for daily Reboot = ".$timeDaylyReboot." hour<br/>Critical CPU load = ".$busyCPU."<br/>Critical RAM free = ".$minRAM." Kb<br/>";
                        
                                //// Cron INFO
                                
                                $cronTabInfo = shell_exec("cat /var/spool/cron/crontabs/root");
                                echo "Crontab tasks = ".$cronTabInfo."<br/>";
                                $lastReboot = shell_exec("tail -1 /overlay/update/logLife.0");      
                                echo "Last reboot = ".$lastReboot;
                                }
                                
                            // INFO STACK CLOSED
                            // update log file read
                            if($command == "stm_update_log"){
                                $stm_log_file = "/var/www/html/data/stm_update_log.txt";
                                if(file_exists($stm_log_file)){
                                $outLog = file_get_contents($stm_log_file);
                                echo nl2br(htmlspecialchars($outLog));
                                }
                           }
                           //Restart 
                           if($command == "reboot_rasp"){
                               echo "Ok - ".$command;
                               writeToLog($user, $command);
                               shell_exec("/usr/local/bin/call --cmd REST 1");
                           }
                           if($command == "reboot_stm"){
                               echo "Ok - ".$command;
                               writeToLog($user, $command);
                               shell_exec("/usr/local/bin/call --cmd REST 0");
                           }
                           if($command == "reboot_all"){
                               echo "Ok - ".$command;
                               writeToLog($user, $command);
                               shell_exec("/usr/local/bin/call --cmd REST 0;sleep 1;/usr/local/bin/call --cmd REST 1");
                           }
                           /// Clear update log
                               if($command == "stm_update_log_clear"){
                                  $stm_log_file = "/var/www/html/data/stm_update_log.txt"; 
                                   if(file_exists($stm_log_file)){
                               echo "Ok - ".$command;
                               writeToLog($user, $command);
                               file_put_contents($stm_log_file, "");
                                   }else{ echo "file not found";}
                           }
                           //Очистить трансивер
                           if($command == "erase_content"){
                           echo "Ok - ".$command.PHP_EOL;
                               writeToLog($user, $command);
                               $before = shell_exec("ls -1 /var/www/html/data/*|wc -l");
                               shell_exec("rm -f /var/www/html/data/*/* /var/www/html/data/*");
                               $after = shell_exec("ls -1 /var/www/html/data/*|wc -l");
                               if($before > $after){
                                   echo "Ok - ".$command;
                                   echo $before." ".$after;
                               }else {echo "error";}
                           }
                           // Этажи
                          
                           if($parseComm[0] == "floor"){
                               echo "Ok - ".$command;
                               writeToLog($user, $command);
                               shell_exec("/usr/local/bin/call --cmd FLOOR ".$parseComm[1]);
                           }else{
                        echo "неизвестная команда";
                           echo $command;}
                         // COMMAND STACK CLOSED  
                        }else{
                        echo "неизвестная команда";
                        echo $command; 
                }
                        // static IP
                
                if($parseComm[0] == "static"){
                    if($parseComm[1] == "clear"){
                        
                    }else{
                    $ip = $parseComm[1];
                    $gate = $parseComm[2];
                    $mask = $parseComm[3];
                    }
                }
                }}   
            
            //////////////////////////////////////// отсечка проверок заголовка пост запроса
    }}?>
 
