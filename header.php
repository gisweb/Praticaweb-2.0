<?php
include "login.php";
$url="stampe/";
$nome_file="anagrafe_tributaria.txt";
$total=STAMPE.$nome_file;
//echo "$total di dimensione ".filesize($total);exit;
$f = fopen($total,'r');
$data=fread($f,filesize($total));
fclose($f);
header("Pragma: no-cache");
header("Expires: 0");
header("Content-Type: text/plain");
header("Content-Length: ".filesize($total));
header("Content-Disposition: attachment; filename=$nome_file");
echo $data;
?>