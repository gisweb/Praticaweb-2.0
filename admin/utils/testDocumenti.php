<?php
require_once '../../login.php';
//error_reporting(E_ALL);
$offset=($_REQUEST["offset"])?($_REQUEST["offset"]):('0');
$sql="SELECT id FROM stp.e_modelli WHERE form='pe.avvioproc' AND id >199 ORDER BY nome LIMIT 10 OFFSET $offset;";
$dbconn->sql_query($sql);
$ris=$dbconn->sql_fetchrowset();
echo $sql;
require_once APPS_DIR."lib/stampe.word.class.php";
$idpratica=19029;
$pr=new pratica($idpratica);
echo "<ol>";
for ($i=0;$i<count($ris);$i++){
    echo "<li>";
    $id_modello=$ris[$i]['id'];
    $doc=new wordDoc($id_modello,$idpratica);
    $sql="INSERT INTO stp.stampe(pratica,modello,file_doc,form,utente_doc,data_creazione_doc,chk) VALUES($idpratica,$id_modello,'$idpratica-$doc->modello','pe.avvioproc','mamo',CURRENT_DATE,1);";
    if (!$dbconn->sql_query($sql)) echo "<p>$sql</p>";
	else
		echo "<a target='_new' href='$pr->url_documenti$idpratica-$doc->modello'>$idpratica-$doc->modello</a>";
    $doc->createDoc();
    echo "</li>";
}
echo "</ol>";
?>