<?php
$cmd = "sudo /usr/local/bin/call ".$_GET["num"]." ".$_GET["type"];
exec($cmd, $o, $ret);
echo $ret;
?>
