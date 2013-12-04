<?php
require_once '../login.php';
$offset=($_REQUEST["offset"])?($_REQUEST["offset"]):('0');
$sql="SELECT id FROM stp.e_modelli WHERE form='pe.avvioproc' AND id >199 ORDER BY nome LIMIT 50 OFFSET $offset;";
$dbconn->sql_query($sql);
$ris=$dbconn->sql_fetchlist('id');

require_once APPS_DIR."lib/stampe.word.class.php";
$idpratica=19001;
for ($i=0;$i<count($ris);$i++){
    $id_modello=$ris[$i];
    $doc=new wordDoc($id_modello,$idpratica);
    $doc->createDoc();
}
?>