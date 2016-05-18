<?php
$azione=$_POST["azione"];
$modo=$_REQUEST['mode'];
$prm=(isset($_POST['parametri']))?($_POST['parametri']):(0);
if (in_array($azione,Array("Salva","Elimina")) && !$prm)
	include_once "./db/db.savedata.php";


$active_form="pe.progetto.php?pratica=$idpratica";

$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
if($modo=='new' && $azione=='Aggiungi'){
	$parametro=$_REQUEST['parametro'];
	$conforme=$_REQUEST['conforme'];
	$valore=addslashes($_REQUEST['valore']);
	$sql="insert into pe.parametri_prog (parametro,pratica,valore,conforme,chk,uidins,tmsins) values ($parametro,$idpratica,'$valore',$conforme,1,".$_SESSION["USER_ID"].",".time().");";
	$db->sql_query($sql);
}
elseif($modo=='new' && $azione=='Salva'){
	
	$sql="SELECT id,nome FROM pe.e_parametri WHERE default_ins=1;";
	$db->sql_query($sql);
	$ris=$db->sql_fetchrowset();
	for($i=0;$i<count($ris);$i++){
		$sql="insert into pe.parametri_prog (parametro,pratica,conforme,chk,uidins,tmsins) values (".$ris[$i]["id"].",$idpratica,1,1,".$_SESSION["USER_ID"].",".time().");";
		//echo "<p>$sql</p>";
		$db->sql_query($sql);
	}
	
}
elseif($prm && $azione=='Salva'){
	$vals=$_POST['valore'];
	$conformita=$_POST['conforme'];
	foreach($vals as $key=>$val){
		$conf=($conformita[$key])?('1'):('0');
		$v=(strlen($val))?("'$val'"):('null');
		$sql="UPDATE pe.parametri_prog SET valore=$v,conforme=$conf WHERE id=$key";
		$db->sql_query($sql);
	}
    /*Parametri Calcolati*/
    appUtils::setPrmProgCalcolati($idpratica,$vals);
	$modo='view';
	
}
elseif ($_REQUEST['azione']=='Elimina'){
	$sql=($prm)?("DELETE FROM pe.parametri_prog WHERE id=".$_REQUEST['id'].";"):("DELETE FROM pe.parametri_prog WHERE pratica=$idpratica;");
	$db->sql_query($sql);
	/*echo "<p>$sql</p>";*/
}

?>