<?
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");	

if ($_POST["idpratica"]) {
	$pratiche=$_POST["idpratica"];
	$idcomm=$_POST["pratica"];
	//$numero=$_POST["numero"];
	$uid=$_SESSION['USER_ID'];
	$sql="SELECT tipo_comm,data_convocazione FROM ce.commissione WHERE id=$idcomm;";
	$db->sql_query($sql);
	print_debug($sql);
	$tipo_comm=$db->sql_fetchfield("tipo_comm");
	$data=$db->sql_fetchfield("data_convocazione");
	for($i=0;$i<count($pratiche);$i++){
		$tmsins=time();
		$sql="INSERT INTO pe.pareri(pratica,ente,data_rich,data_ril,uidins,tmsins) VALUES(".$pratiche[$i].",$tipo_comm,'$data'::date,'$data'::date,$uid,$tmsins)";
		$db->sql_query($sql);
		print_debug($sql);
	}
}
$active_form="ce.ordinegiorno.php?comm=1&pratica=$idpratica";
?>