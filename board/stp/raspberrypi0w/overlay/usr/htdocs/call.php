<?php
$cmd = "sudo call ".$_GET["num"]." ".$_GET["type"];
exec($cmd, $o, $ret);
echo $ret;
?>
