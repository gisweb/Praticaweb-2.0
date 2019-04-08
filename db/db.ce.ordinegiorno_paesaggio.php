<?php
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
                $sql= "SELECT max(coalesce(ordine,0))+1 as ordine FROM pe.pareri WHERE data_rich='$data'::date AND ente=$tipo_comm;";
                $db->sql_query($sql);
                $ordine = $db->sql_fetchfield("ordine");
		$tmsins=time();
		$sql="INSERT INTO pe.pareri(pratica,ente,data_rich,data_ril,ordine,uidins,tmsins) VALUES(".$pratiche[$i].",$tipo_comm,'$data'::date,'$data'::date,$ordine,$uid,$tmsins)";
		$db->sql_query($sql);
		echo "<p>$sql</p>";
	}
}
$active_form="ce.ordinegiorno_paesaggio.php?comm_paesaggio=1&pratica=$idpratica";
?>
