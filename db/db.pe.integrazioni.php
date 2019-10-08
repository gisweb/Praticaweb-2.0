<?php
include "./db/db.savedata.php";
require_once LIB."menu.class.php";

$modo=(isset($_REQUEST["mode"]) && $_REQUEST["mode"])?($_REQUEST["mode"]):('view');
if($_REQUEST['azione']=='Salva'){
	$menu = new Menu("pratica","pe");
	$menu->add_menu($idpratica,45);
	
	$integrazione=($modo=='new')?($_SESSION['ADD_NEW']):($_REQUEST['integrazione']);
	$prot = ($_REQUEST["prot_integ"])?(sprintf("'%s'",$_REQUEST["prot_integ"])):("NULL::varchar");
	$data_prot = ($_REQUEST["data_integ"])?(sprintf("'%s'::date",$_REQUEST["data_integ"])):("NULL::date");
	foreach($_REQUEST as $k=>$v){
		if (strpos($k,'all_')===0){
			$idallegato=str_replace('all_','',$k);
			
			$sql=<<<EOT
UPDATE pe.allegati SET mancante=0,integrato=0,sostituito=0 WHERE id=$idallegato;
UPDATE pe.allegati SET protocollo=$prot,data_protocollo=$data_prot,$v=1,integrazione=$integrazione WHERE id=$idallegato;
EOT;
			$db->sql_query($sql);
			echo "<p>$sql</p>";
		}
	}
}
$active_form=$_POST["active_form"]."?pratica=$idpratica";
?>