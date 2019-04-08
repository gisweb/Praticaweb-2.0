<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
$_SESSION["USER_ID"]=1;
require_once '/apps/praticaweb-2.0/login.php';
require_once APPS_DIR.'lib/pratica.class.php';
$pratica=new pratica();
$db=$pratica->db1;
$ris=$db->fetchAll("SELECT DISTINCT id,numero FROM pe.avvioproc");
for($i=0;$i<count($ris);$i++){
	$pratica=new pratica($ris[$i]['id']);
	$pratica->nuovaPratica();
	echo "$i) Creata struttura per pratica n° ".$ris[$i]["numero"]."\n";
	$pratica->db1->close();
}

?>
