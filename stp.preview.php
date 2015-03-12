<?php
require_once "login.php";
require_once APPS_DIR."/lib/stampe.word.class.php";
$pratica=$_REQUEST["pratica"];
$modello=$_REQUEST["modello"];
$doc=new wordDoc($modello,$pratica);
$doc->createDoc(1);
?>