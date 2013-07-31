<?
include "login.php";
$url="stampe/";
$nome_file="anagrafe_tributaria.txt";
$total=STAMPE_DIR.$nome_file;
header("Pragma: no-cache");
header("Expires: 0");
header("Content-Type: text/plain");
header("Content-Length: ".filesize($total));
header("Content-Disposition: attachment; filename=$nome_file");
include("$total");
?>