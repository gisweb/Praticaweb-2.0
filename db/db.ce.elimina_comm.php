<?
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$sql_CE="delete from ce.commissione where ";
$sql_PD="delete from ce.discusse where ";
$sql_PC="delete from ce.partecipanti where ";
foreach($idcomm as $id){
	$filter.="id=$id or ";
	$filter1.="commissione=$id or ";
}
$filter=substr($filter,0,strlen($filter)-3);
$filter1=substr($filter1,0,strlen($filter1)-3);
		
$sql_CE.=$filter;
$sql_PD.=$filter1;
$sql_PC.=$filter1;
		
if (!$db->sql_query($sql_CE)) echo "<br>$sql_CE<br>ERRORE NELLA CANCELLAZIONE!";
if (!$db->sql_query($sql_PD)) echo "<br>$sql_PD<br>ERRORE NELLA CANCELLAZIONE!";
if (!$db->sql_query($sql_PC)) echo "<br>$sql_PC<br>ERRORE NELLA CANCELLAZIONE!";
?>