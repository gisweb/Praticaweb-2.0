<?
include("login.php");

$prat=$_REQUEST['pratica'];
$pr=new pratica($prat,0);
include "./db/db.savedata.php";
/* Procedura di cancellazione di tutti i record collegati alla pratica */
$pratica=$_POST["pratica"];
$table[]="cdu.mappali";
$table[]="cdu.richiesta";
$table[]="ce.discusse";
$table[]="oneri.calcolati";
$table[]="oneri.fidi";
$table[]="oneri.monetizzazione";
$table[]="oneri.rate";
$table[]="oneri.totali";
$table[]="pe.abitabi";
$table[]="pe.allegati";
$table[]="pe.asservimenti_prat";
/*$table[]="pe.asservimenti";
$table[]="pe.asservimenti_map";*/
$table[]="pe.cterreni";
$table[]="pe.curbano";
$table[]="pe.file_allegati";
$table[]="pe.indirizzi";
$table[]="pe.infodia";
$table[]="pe.integrazioni";
$table[]="pe.iter";
$table[]="pe.lavori";
$table[]="pe.menu";
$table[]="pe.parametri_prog";
$table[]="pe.pareri";
$table[]="pe.progetto";
$table[]="pe.proroga";
$table[]="pe.recenti";
$table[]="pe.soggetti";
$table[]="pe.sopralluoghi";
$table[]="pe.titolo";
$table[]="pe.vincolo";
$table[]="pe.volture";
$table[]="stp.stampe";
$table[]="vigi.dest_provvedimenti";
$table[]="vigi.esposti";
$table[]="vigi.ordinanze";
$table[]="vigi.sanzioni";
$table[]="vigi.sopralluoghi";

// Caso degli asservimenti: se è relativo ad una sola pratica elimino tutto altrimenti cancello solo il riferimento a quella pratica
/*$db->sql_query("SELECT asservimento FROM pe.asservimenti_prat WHERE pratica=$pratica;");
$asservimento=$db->sql_fetchfield("asservimento");
$sql="SELECT count(*) as num_prat FROM pe.asservimenti_prat WHERE asservimento=$asservimento";
$db->sql_query($sql);
$num_prat=$db->sql_fetchfield("num_prat");
if ($num_prat==1){
	$db->sql_query("DELETE  FROM pe.asservimenti WHERE id=$asservimento");
	$db->sql_query("DELETE  FROM pe.asservimenti_map WHERE asservimento=$asservimento");
}
else{
	
}
for($i=0;$i<count($table);$i++){
	$sql="DELETE  FROM ".$table[$i]." WHERE pratica=".$pratica.";";
	$db->sql_query($sql);
}*/

?>
