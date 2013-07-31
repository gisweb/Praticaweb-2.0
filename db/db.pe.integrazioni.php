<?
include "./db/db.savedata.php";
$modo=(isset($_REQUEST["mode"]) && $_REQUEST["mode"])?($_REQUEST["mode"]):('view');
if($_REQUEST['azione']=='Salva'){
	$integrazione=($modo=='new')?($_SESSION['ADD_NEW']):($_REQUEST['integrazione']);
	foreach($_REQUEST as $k=>$v){
		if (strpos($k,'all_')===0){
			$idallegato=str_replace('all_','',$k);
			
			$sql=<<<EOT
UPDATE pe.allegati SET mancante=0,integrato=0,sostituito=0 WHERE id=$idallegato;
UPDATE pe.allegati SET $v=1,integrazione=$integrazione WHERE id=$idallegato;
EOT;
			$db->sql_query($sql);
			//echo "<p>$sql</p>";
		}
	}
}
$active_form=$_POST["active_form"]."?pratica=$idpratica";
?>