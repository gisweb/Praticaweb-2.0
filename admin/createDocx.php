<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../login.php';
$modello=$_REQUEST["modello"];
require_once APPS_DIR."lib/stampe.word.class.php";
$idpratica=19001;
for ($i=0;$i<count($ris);$i++){
    $id_modello=$ris[$i];
    $doc=new wordDoc($id_modello,$idpratica);
    $doc->createDoc();
}

?>