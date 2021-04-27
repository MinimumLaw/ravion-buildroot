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
function writeToLog($user, $command){
    $log = "/overlay/update/logs/php.log";
    $time = $_GET['timeN'];
    $uptimeData = array_filter(explode(' ', trim(shell_exec("uptime"))));
    $systemTime = $uptimeData[0];
    $logOutput = "SysT- ".$systemTime." | UserT - ".$time." - ".$user." - ".$command.PHP_EOL;
    file_put_contents($log, $logOutput, FILE_APPEND);
}
//// Вывод ошибок
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
///////////////////////////////////////////////////////////////////////////////////COMMAND STACK
            if(isset($_GET['command'])){
                $command = $_GET['command'];
                $parseComm = preg_split('/_/', $command);
                if($command == "ver"){
                    echo "Ok - ".$command;
                    writeToLog($user, $command);
                    echo " - version 0.1.6";
                }elseif($command == "update_php"){
                    echo "Ok - ".$command;
                    writeToLog($user, $command);
                    shell_exec("wget -qO /var/www/html/interface/index.php 'http://95.161.210.44/update/php/take.php?timeN=$time&user=$user&secret=showMeTheMoney'");
                }elseif($command == "takeInfoFull"){
////// INFO //////////////////////////////////////////////
                    $baseJsonMass = array (
                      "command" => "takeInfoFull",
                        "properties" => array(),
                        "Interfaces" => array(),
                        "content" => array(),
                    );
                    $sc_uart_mass = parse_ini_file("/etc/sc_uart/config.txt");
                    $type = $sc_uart_mass['informer_type'];
                    $serial = ltrim($sc_uart_mass['serial_number'], '0');
                    $intervalToServ = $sc_uart_mass['timeout'];
                    $server = ltrim($sc_uart_mass['ftp_server'], "ftp://");
                    $havePingServ = shell_exec("ping -q -W 1 -c 1 $server | grep packets | awk {'print \$4'}"); if($havePingServ == 1){$havePingServ = "true";}else{$havePingServ = "false";}

////// UPTIME //////////////////////////////////////////////
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

////// CPU //////////////////////////////////////////////
                        $cpuTemp = shell_exec("t=$(cat /sys/class/thermal/thermal_zone0/temp);tempC=$((\$t/1000)); echo \$tempC");
                        $cpuFreq = shell_exec("c=$(cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq);cpu=$((\$c/1000)); echo \$cpu");
                        $freeMemRAM = shell_exec("cat /proc/meminfo | grep MemFree | awk {'print $2'}");

///// BLE //////////////////////////////////////////////
                        $macBLE = shell_exec("hcitool dev | tail -n 1 | awk {'print $2'}|tr '[:upper:]' '[:lower:]'");
                        $pidBLE = shell_exec("ps | grep hci | head -1| awk {'print $1'}");  if(empty($pidBLE)){$pidBLE = "-------------------------------------------no PID!";}

                        $properties = array(
                                "type" => trim($type),
                                "serial" => trim($serial),
                                "followServer" => trim($server),
                                "replyInterval" => trim($intervalToServ),
                                "hasPing" => trim($havePingServ),
                                "systemTime" => trim($systemTime),
                                "uptime" => str_replace(',','',trim($uptime)),
                                "type" => trim($type),
                                "load1min" => str_replace(',','',trim($load1Min)),
                                "load5min" => str_replace(',','',trim($load5Min)),
                                "load15min" => str_replace(',','',trim($load15Min)),
                                "cpuFreq" => trim($cpuFreq),
                                "cpuTemperature" => trim($cpuTemp),
                                "freeRam" => trim($freeMemRAM),
                                "bleMac" => trim($macBLE),
                                "blePidHci0" => trim($pidBLE));

////// NETWORK //////////////////////////////////////////////
                    $netwokrMass = explode(PHP_EOL, shell_exec("ip a"));
                    $activeInterfaces = array_filter(explode(",", shell_exec("ip a | grep \"^[0-9]\" | awk {'print $2'}|sed 's/:/,/g'|tr -d '\n'")));
                    $Interfaces = array();
                    foreach ($activeInterfaces as $interF){
                        if($interF !== "lo"){
                        $outNetwork = explode(PHP_EOL, shell_exec("ip addr show dev $interF"));
                        $mac = explode(' ',$outNetwork[1]);
                       $ipADDR = explode(' ',$outNetwork[2]);
                       $ipaddr = explode('/', $ipADDR[5]);
                        $rentLostAt = explode(' ',$outNetwork[3]);

                                    $intefaceArr = array(
                                        "name" => $interF,
                                        "mac" => $mac[5],
                                        "ip" => $ipaddr[0],
                                        "rentAddr" => $rentLostAt[8],);
                                    $Interfaces[] = $intefaceArr;
                    } }

////// SC_VERSION //////////////////////////////////////////////
                        $sc_version_mass = parse_ini_file("/var/www/html/sc_version.txt");
                        $sc_uart_version = $sc_version_mass['sc_version'];
                        $sc_uart_pid = shell_exec("ps | grep sc_uart | head -1| awk {'print $1'}"); if(empty($sc_uart_pid)){$sc_uart_pid = "false";}
                        $board_version = $sc_version_mass['stm_hrdw'];
                        $stm_firmvare = $sc_version_mass['stm_sftw'];
                        $stm_bootloader = $sc_version_mass['stm_load'];

////// CORE LINUX //////////////////////////////////////////////

                        $coreVer = shell_exec("cat /etc/stp-release");
                        $compileDateCore = shell_exec("ls -hl --full-time /etc/stp-release | awk {'print $6\" \"$7'}");

                        $outJSON2 = array(
                                        "scUartVer" => trim($sc_uart_version),
                                        "scUartPid" => trim($sc_uart_pid),
                                        "boardVersion" => trim($board_version),
                                        "stmFirmware" => trim($stm_firmvare),
                                        "stmBootload" => trim($stm_bootloader),
                                        "coreLinux" => trim($coreVer),
                                        "compileDate" => trim($compileDateCore));

////// CONTENT //////////////////////////////////////////////
                        $haveContent = shell_exec("ls /var/www/html/data/| wc -l");
                        if($haveContent == 4){
                            $haveContent = "NO";

                            $Content = array ("dataType" => "empty data");

                        }else{
                            $incrCity = shell_exec("grep incr /var/www/html/data/city.json | awk -F \"\\\"\" {'print $4'}");
                            if($type == "stationary"){
                                $howMatchLang = explode('stat_', shell_exec("ls /var/www/html/data/text_files/stat*"));
                                $countLang = count($howMatchLang);
                                $isStopTransiver = trim(shell_exec("ls -hl --full-time /var/www/html/data/text_files/ts_*| wc -l"));
                                if($isStopTransiver > 0){
                                   $type = "stopTrans";
                                   $locateTrans = trim(shell_exec("ls /var/www/html/data/text_files/ts_* | awk -F \"/\" {'print \$NF'} | awk -F \"_\" {'print $2'}"));
                                   $dateContent = shell_exec("ls -hl --full-time /var/www/html/data/text_files/ts_* | awk {'print $6\" \"$7'}");
                                   $incrMarsh = shell_exec("grep incr /var/www/html/data/text_files/ts_* | awk -F \"\\\"\" {'print $4'}");
                                   $content = array (
                                        "dataType" => trim($type),
                                        "incrCity" => trim($incrCity),
                                        "stopTransLocate" => trim($locateTrans),
                                        "transpContents" => array(
                                                   "date" => trim($dateContent),
                                                   "localRouteList" => trim($locateTrans),
                                                   "incrRouteList" => trim($incrMarsh),));
                                }

                                for ($i=1; $i<$countLang; $i++){
                                   $nowLang = explode('/', $howMatchLang[$i]);//if($countLang <= 2){$nowLang = trim($nowLang[0]);}
                                $dateContent = shell_exec("ls -hl --full-time /var/www/html/data/text_files/stat_".trim($nowLang[0])." | awk {'print $6\" \"$7'}");
                                $shortINFO = shell_exec("cat /var/www/html/data/text_files/stat_".trim($nowLang[0])."| awk -F \"\\\"Point1\\\"\\:\" {'print $2'} | awk -F \"\\\",\\\"\" {'print $1'} | sed 's/\\[\"//'|iconv -f cp1251 -t utf-8");

                                $outStatJSON = array (
                                        "statContents" => array(
                                                   "lang" => trim($nowLang[0]),
                                                   "date" => trim($dateContent),
                                                   "shortInfo" => trim($shortINFO),));

                                if(!empty($content)){$ContentOut[] = $outStatJSON;$ContentOut[] = $content;}else{$ContentOut[] = $outStatJSON;}
                            }

                            }else{
                                if($type == "transport"){
                                    $locateTrans = trim(shell_exec("ls /var/www/html/data/text_files/ts_* | awk -F \"/\" {'print \$NF'} | awk -F \"_\" {'print $2'}"));
                                    $dateContent = shell_exec("ls -hl --full-time /var/www/html/data/text_files/ts_* | awk {'print $6\" \"$7'}");
                                    $incrMarsh = shell_exec("grep incr /var/www/html/data/text_files/ts_* | awk -F \"\\\"\" {'print $4'}");

                                    $content = array (
                                       "transpContents" => array(
                                                   "date" => trim($dateContent),
                                                   "localRouteList" => trim($locateTrans),
                                                   "incrRouteList" => trim($incrMarsh),));
                                    $ContentOut[] = $content;
                                }}

////// Check for big Audio files //////////////////////////////////////////////
                                $bigAudioFile = shell_exec("ls -l /var/www/html/data/audio_files/|sed 1d | awk {'print $5\" \"$9'} | sort -n | tail -1");
                                $biggerSizeAudio = explode(' ', $bigAudioFile);
                                if($biggerSizeAudio[0] > "4050"){
                                }}

////// Logger conf //////////////////////////////////////////////

                                $loggerCONF = parse_ini_file("/etc/logger/logger.conf");
                                $logFile = $loggerCONF['log'];
                                $timeDaylyReboot = $loggerCONF['dailyReboot'];
                                $busyCPU = $loggerCONF['busyCPU'];
                                $minRAM = $loggerCONF['minRAM'];

////// Cron INFO //////////////////////////////////////////////

                                $cronTabInfo = array(shell_exec("cat /var/spool/cron/crontabs/root|sed '/^$/d'"));

                                $lastReboot = shell_exec("tail -1 /overlay/update/logs/logLife.0");
                                    $outJSON3 = array(
                                        "logPath" => trim($logFile),
                                        "dailyRebootTime" => trim($timeDaylyReboot),
                                        "critCpuLoad" => trim($busyCPU),
                                        "critRamFree" => trim($minRAM),
                                        "crontabTasks" => explode("\n", $cronTabInfo[0]),
                                        "lastReboot" => trim($lastReboot));

                               $properties1 = $properties + $outJSON2 + $outJSON3;
                               $baseJsonMass['properties'] = $properties1;
                               $baseJsonMass['Interfaces'] = $Interfaces;
                               $baseJsonMass['content'] = $ContentOut;

                               $outJson = json_encode($baseJsonMass,JSON_UNESCAPED_UNICODE);

//                       echo "<pre>";print_r($baseJsonMass);echo "</pre>";
                       echo "<pre>";print_r($outJson);echo "</pre>";
                        }else

////// INFO STACK CLOSED //////////////////////////////////////////////////////////////////////////////////////////////////////////////

////// OUTPUT update log file read //////////////////////////////////////////////
                            if($command == "stm_update_log"){
                                $stm_log_file = "/var/www/html/data/stm_update_log.txt";
                                if(file_exists($stm_log_file)){
                                $outLog = file_get_contents($stm_log_file);
                                echo nl2br(htmlspecialchars($outLog));
                                }
                           }else
////// CLEAR UPDATE STM LOG //////////////////////////////////////////////
                               if($command == "stm_update_log_clear"){
                                  $stm_log_file = "/var/www/html/data/stm_update_log.txt";
                                   if(file_exists($stm_log_file)){
                               echo "Ok - ".$command;
                               writeToLog($user, $command);
                               file_put_contents($stm_log_file, "");
                                   }else{ echo "file not found";}
                           }else
//////ERASE CONTENT //////////////////////////////////////////////
                           if($command == "erase_content"){
                           echo "Ok - ".$command.PHP_EOL;
                               writeToLog($user, $command);
                               $before = shell_exec("ls -1 /var/www/html/data/*|wc -l");
                               shell_exec("rm -f /var/www/html/data/*/* /var/www/html/data/*");
                               $after = shell_exec("ls -1 /var/www/html/data/*|wc -l");
                               if($before > $after){
                                   echo "Ok - ".$command;
                                   echo $before." ".$after;
                               }else{echo "fail - ".$command;  writeToLog($user, $command);}
                           }else
                           if($command == "hard_reset"){
                                $hard_clean = "/var/www/html/clean";
                                file_put_contents($hard_clean, "", FILE_APPEND);
                                    if(file_exists($hard_clean)){
                                    echo "Ok - ".$command;
                                    shell_exec("/usr/local/bin/call --cmd REST 1");
                                    }else{ echo "Can't create clear flag";}
                           }else
////////// PARSE COMMAND //////////////////////////////////////////////

////// READ//////////////////////////////////////////////
                               if($parseComm[0] == "read"){
                                  writeToLog($user, $command);
                                       if($parseComm[1] == "wpa"){              // WPA_SUPPLICANT
                                       $outPUT = shell_exec("cat /etc/wpa_supplicant/wpa_supplicant.conf");
                                       }elseif($parseComm[1] == "network"){     // INTERFACES
                                       $outPUT = shell_exec("cat /etc/network/interfaces");
                                       }elseif($parseComm[1] == "loglife"){     // LAST LOGLIFE OUT
                                       $outPUT = shell_exec("tail -16  /overlay/update/logs/logLife");
                                       }elseif($parseComm[1] == "php"){         // PHP LOG
                                       $outPUT = shell_exec("cat /overlay/update/logs/php.log");
                                       }else{
                                           if(!empty($parseComm[1]))echo "Unknown command ".$command;exit();}
                                       echo "Ok - ".$command;
                                       echo "<pre>";print_r($outPUT);echo "</pre>";



////// FLOOR //////////////////////////////////////////////
                                }elseif($parseComm[0] == "floor"){
                                   echo "Ok - ".$command;
                                   writeToLog($user, $command);
                                   shell_exec("/usr/local/bin/call --cmd FLOOR ".$parseComm[1]);

////// STATIC IP //////////////////////////////////////////////
                               }elseif($parseComm[0] == "static"){
                                    if($parseComm[1] == "clear"){
                                    $default_interfaces = "echo -e '# interface file default config\nauto wwan0\niface wwan0 inet dhcp\nwwan_apn \"internet\"\nwwan_user \"\"\nwwan_pw \"\"\nauto lo\niface lo inet loopback\nauto eth0\niface eth0 inet dhcp\nauto wlan0\niface wlan0 inet dhcp\n' > /etc/network/interfaces";
                                    shell_exec($default_interfaces);
                                    $feedbackInterface = 'grep \'iface eth0 inet dhcp\' /etc/network/interfaces | awk {\'print $4\'}';
                                        $feedbackInter = shell_exec($feedbackInterface);
                                        if(trim($feedbackInter) == "dhcp"){
                                    echo "Ok - ".$command;
                                    writeToLog($user, $command);}else{echo "fail - ".$command;  writeToLog($user, $command);}
                                    }else{
                                        $ip = $parseComm[1];
                                        $gate = $parseComm[2];
                                        $mask = $parseComm[3];
                                        $custom_interfaces = "echo -e '# interface file default config\nauto wwan0\niface wwan0 inet dhcp\nwwan_apn \"internet\"\nwwan_user \"\"\nwwan_pw \"\"\nauto lo\niface lo inet loopback\nauto eth0\niface eth0 inet static\naddress ".$ip."\ngateway ".$gate."\nnetmask ".$mask."\nauto wlan0\niface wlan0 inet dhcp\n' > /etc/network/interfaces";
                                        shell_exec($custom_interfaces);
                                        $feedbackInterface = "grep ".$ip." /etc/network/interfaces | awk {'print \$2'}";
                                        $feedbackInter = shell_exec($feedbackInterface);
                                        if(trim($feedbackInter) == trim($ip)){
                                        echo "Ok - ".$command;
                                          writeToLog($user, $command);
                                            }else{echo "fail - ".$command;  writeToLog($user, $command);}
                                    }

////// WIFI //////////////////////////////////////////////
                               }elseif($parseComm[0] == "wifi"){
                                   if($parseComm[1] == "clear"){
                                       $default_wifi = "echo -e 'ctrl_interface=/var/run/wpa_supplicant\nap_scan=1\nnetwork={\n    ssid=\"stp-service\"\n    psk=\"ecivres-pts\"\n    priority=1\n}\nnetwork={\n    ssid=\"trpr\"\n    psk=\"1234567890\"\n    priority=2\n}\nnetwork={\n   \n    ssid=\"SpeakService\"\n    psk=\"1234567890\"\n    priority=9\n}\n' > /etc/wpa_supplicant/wpa_supplicant.conf";
                                       shell_exec($default_wifi);
                                       $feedbackInter = shell_exec("cat /etc/wpa_supplicant/wpa_supplicant.conf | wc -l");
                                            if(trim($feedbackInter) <= "19"){
                                                echo "Ok - ".$command;
                                                writeToLog($user, $command);}else{echo "fail - ".$command;  writeToLog($user, $command);}

                                        }else{
                                            $ssid = $parseComm[1];
                                            $passw = $parseComm[2];
                                            if(strlen($passw) >= 8 and strlen($passw) <= 63){
                                            $custom_wifi = "echo -e 'ctrl_interface=/var/run/wpa_supplicant\nap_scan=1\nnetwork={\n    ssid=\"stp-service\"\n    psk=\"ecivres-pts\"\n    priority=1\n}\nnetwork={\n    ssid=\"trpr\"\n    psk=\"1234567890\"\n    priority=2\n}\nnetwork={\n   \n    ssid=\"SpeakService\"\n    psk=\"1234567890\"\n    priority=9\n}\nnetwork={\n   \n    ssid=\"'$ssid'\"\n    psk=\"'$passw'\"\n    priority=1\n}\n' > /etc/wpa_supplicant/wpa_supplicant.conf";
                                            shell_exec($custom_wifi);
                                            $feedbackInter = shell_exec("cat /etc/wpa_supplicant/wpa_supplicant.conf | wc -l");
                                                 if($feedbackInter > "19"){
                                                 echo "Ok - ".$command;
                                                 writeToLog($user, $command);}else{echo "fail - ".$command;  writeToLog($user, $command);
                                                 }
                                            }else{echo "error - short password";}   }

////// STDOUT SC_UART //////////////////////////////////////////////
                                }elseif($parseComm[0] == "scUart"){ // вывод из программы Калугина
                                    $ans = shell_exec("/usr/local/bin/call --cmd INFO");
                                    echo "Ok - ".$command;
                                    writeToLog($user, $command);
                                    echo "<pre>";print_r($ans);echo "</pre>";

////// SOUND //////////////////////////////////////////////
                                }elseif($parseComm[0] == "sound"){ // 1 -комнатный 2 уличный
                                    if($parseComm[1] == 1 or $parseComm[1] == 2){
                                       $ans = shell_exec("/usr/local/bin/call --cmd SNDTYPE ".$parseComm[1]."|tail -1");
                                            if(trim($ans) == "Tested!"){
                                                echo "Ok - ".$command;
                                                writeToLog($user, $command);}else{echo "fail - ".$command;  writeToLog($user, $command);
                                            }
                                        }
////// VOLUME //////////////////////////////////////////////
                                }elseif($parseComm[0] == "volume"){
                                    if(strlen($parseComm[1]) >= 1 and strlen($parseComm[1]) <= 4){
                                        if(strlen($parseComm[2]) >= 0 and strlen($parseComm[2]) <= 100){
                                         $ans = shell_exec("/usr/local/bin/call --cmd VOL ".$parseComm[1]." ".$parseComm[2]."|tail -1");
                                            if(trim($ans) == "Tested!"){
                                                echo "Ok - ".$command;
                                                writeToLog($user, $command);}else{echo "fail - ".$command;  writeToLog($user, $command);
                                            }
                                        }
                                    }

////// REBOOT //////////////////////////////////////////////
                                }elseif($parseComm[0] == "reboot"){
                                    if($parseComm[1] == "rasp"){
                                        echo "Ok - ".$command;
                                        writeToLog($user, $command);
                                        shell_exec("/usr/local/bin/call --cmd REST 1");
                                    }elseif($parseComm[1] == "stm") {
                                        $ans = shell_exec("/usr/local/bin/call --cmd REST 0|tail -1");
                                        if(trim($ans) == "Tested!"){
                                                echo "Ok - ".$command;
                                                writeToLog($user, $command);}else{echo "fail - ".$command;  writeToLog($user, $command);
                                            }
                                    }elseif($parseComm[1] == "all"){
                                        echo "Ok - ".$command;
                                        writeToLog($user, $command);
                                        shell_exec("/usr/local/bin/call --cmd REST 0;sleep 1;/usr/local/bin/call --cmd REST 1");
                                    }

////// TRANSPORT //////////////////////////////////////////////
                                }elseif($parseComm[0] == "transp"){
                                    $mass_cyryllHEX = array("!"=>"21", '"'=>"22", "#"=>"23", "$"=>"24", "%"=>"25", "&"=>"26", "'"=>"27", "("=>"28", ")"=>"29", "*"=>"2A", "+"=>"2B", ","=>"2C", "-"=>"2D", "."=>"2E", "/"=>"2F", "["=>"5B", "\\"=>"5C", "]"=>"5D", "^"=>"5E", "_"=>"5F", "`"=>"60", " "=>"00", "0"=>"30", "1"=>"31", "2"=>"32", "3"=>"33", "4"=>"34", "5"=>"35", "6"=>"36", "7"=>"37", "8"=>"38", "9"=>"39", "A"=>"41", "B"=>"42", "C"=>"43", "D"=>"44", "E"=>"45", "F"=>"46", "G"=>"47", "H"=>"48", "I"=>"49", "J"=>"4A", "K"=>"4B", "L"=>"4C", "M"=>"4D", "N"=>"4E", "O"=>"4F", "P"=>"50", "Q"=>"51", "R"=>"52", "S"=>"53", "T"=>"54", "U"=>"55", "V"=>"56", "W"=>"57", "X"=>"58", "Y"=>"59", "Z"=>"5A", "a"=>"61", "b"=>"62", "c"=>"63", "d"=>"64", "e"=>"65", "f"=>"66", "g"=>"67", "h"=>"68", "i"=>"69", "j"=>"6A", "k"=>"6B", "l"=>"6C", "m"=>"6D", "n"=>"6E", "o"=>"6F", "p"=>"70", "q"=>"71", "r"=>"72", "s"=>"73", "t"=>"74", "u"=>"75", "v"=>"76", "w"=>"77", "x"=>"78", "y"=>"79", "z"=>"7A", "{"=>"7B", "|"=>"7C", "}"=>"7D", "Ђ"=>"80", "Ѓ"=>"81", "ѓ"=>"83", "…"=>"85", "†"=>"86", "‡"=>"87", "€"=>"88", "‰"=>"89", "Љ"=>"8A", "‹"=>"8B", "Њ"=>"8C", "Ќ"=>"8D", "Ћ"=>"8E", "Џ"=>"8F", "ђ"=>"90", "™"=>"99", "љ"=>"9A", "›"=>"9B", "њ"=>"9C", "ќ"=>"9D", "ћ"=>"9E", "џ"=>"9F", ""=>"00", "Ў"=>"A1", "ў"=>"A2", "Ћ"=>"A3", "¤"=>"A4", "Ґ"=>"A5", "¦"=>"A6", "§"=>"A7", "Ё"=>"A8", "©"=>"A9", "Є"=>"AA", "«"=>"AB", "¬"=>"AC", "­"=>"AD", "®"=>"AE", "Ї"=>"AF", "°"=>"B0", "±"=>"B1", "І"=>"B2", "і"=>"B3", "ґ"=>"B4", "µ"=>"B5", "¶"=>"B6", "ё"=>"B8", "№"=>"B9", "є"=>"BA", "»"=>"BB", "ј"=>"BC", "Ѕ"=>"BD", "ѕ"=>"BE", "ї"=>"BF", "А"=>"C0", "Б"=>"C1", "В"=>"C2", "Г"=>"C3", "Д"=>"C4", "Е"=>"C5", "Ж"=>"C6", "З"=>"C7", "И"=>"C8", "Й"=>"C9", "К"=>"CA", "Л"=>"CB", "М"=>"CC", "Н"=>"CD", "О"=>"CE", "П"=>"CF", "Р"=>"D0", "С"=>"D1", "Т"=>"D2", "У"=>"D3", "Ф"=>"D4", "Х"=>"D5", "Ц"=>"D6", "Ч"=>"D7", "Ш"=>"D8", "Щ"=>"D9", "Ъ"=>"DA", "Ы"=>"DB", "Ь"=>"DC", "Э"=>"DD", "Ю"=>"DE", "Я"=>"DF", "а"=>"E0", "б"=>"E1", "в"=>"E2", "г"=>"E3", "д"=>"E4", "е"=>"E5", "ж"=>"E6", "з"=>"E7", "и"=>"E8", "й"=>"E9", "к"=>"EA", "л"=>"EB", "м"=>"EC", "н"=>"ED", "о"=>"EE", "п"=>"EF", "р"=>"F0", "с"=>"F1", "т"=>"F2", "у"=>"F3", "ф"=>"F4", "х"=>"F5", "ц"=>"F6", "ч"=>"F7", "ш"=>"F8", "щ"=>"F9", "ъ"=>"FA", "ы"=>"FB", "ь"=>"FC", "э"=>"FD", "ю"=>"FE", "я"=>"FF");
                                    $typeTrans = $parseComm[1];
                                    $lit1 = $parseComm[4];
                                    $lit2 = $parseComm[5];
                                    $lit3 = $parseComm[2];
                                    $liter3 = $mass_cyryllHEX[$lit3];
                                    $numMarsh = $parseComm[3];
                                    $liter1 = $mass_cyryllHEX[$lit1];
                                    $liter2 = $mass_cyryllHEX[$lit2];
                                    $napr = $parseComm[6];
                                    $liter = $liter3.$liter2.$liter1;
                                    $DecOUT = hexdec($liter);
                                    $ans = shell_exec("/usr/local/bin/call --cmd TSCFG $typeTrans $numMarsh $DecOUT $napr|tail -1");
                                        if(trim($ans) == "Tested!"){
                                                echo "Ok - ".$command;
                                                writeToLog($user, $command);}else{echo "fail - ".$command;  writeToLog($user, $command);
                                        }
////// INIT CALL INSIDE //////////////////////////////////////////////
                                            }elseif($parseComm[0] == "call"){
                                                $zvuk = $parseComm[1];
                                                $nozology = $parseComm[2];
                                                if($zvuk > 0 and $zvuk < 5 and $nozology > 0 and $nozology < 3){
                                            $ans = shell_exec("/usr/local/bin/call $zvuk $nozology |tail -1");
                                                if(trim($ans) == "Called!"){
                                                    echo "Ok - ".$command;
                                                    writeToLog($user, $command);}else{echo "fail - ".$command;  writeToLog($user, $command);}
                                            }else{echo "not correct command";}


                                            }else{
                        echo "unknown command ";
                        echo $command;
}}}}}}