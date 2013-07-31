<?

$idpratica=$_POST["pratica"]; 
if ($_POST["azione"]=="Salva"){
	$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
	$conformi=$_POST["conforme"];
	if($conformi)
		$conformi=implode(",",array_keys($_POST["conforme"]));
	$sql="update pe.parametri_prog set conforme=1 where parametro in ($conformi) and pratica=$idpratica" ;
	if (DEBUG) echo $sql;
	$db->sql_query ($sql);
	$sql="update pe.parametri_prog set conforme=0 where pratica=$idpratica";
	if($conformi) $sql.="and parametro not in ($conformi)";
	if (DEBUG) echo $sql;
	$db->sql_query ($sql);
}

$active_form="pe.progetto.php?pratica=$idpratica";
?>