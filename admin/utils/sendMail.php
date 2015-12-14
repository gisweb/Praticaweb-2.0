<?php

require_once "../../login.php";




$info = mailUtils::getHostInfo(1);
$subject="Test Invio Mail con Allegati";
$body=<<<EOT
Prova di invio allegati
EOT;
$pratica=500321;
$pr = new pratica($pratica);
$dir = $pr->documenti;
$allegati = mailUtils::getAttachments(Array(87466,87467));
for($i=0;$i<count($allegati);$i++){
    $allegati[$i]= $dir.DIRECTORY_SEPARATOR.$allegati[$i];
}


$res = mailUtils::sendMail($info[0],Array("marco.carbone.shop@gmail.com"),$subject,$body,$allegati);
print_array($res);
?>