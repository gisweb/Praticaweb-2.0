<?
session_start();
if (!isset($_SESSION["PROPR_PRATICA_$idpratica"])){
	$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
	
	/*$sql="SELECT resp_istr FROM cn.avvioproc WHERE pratica=$idpratica;";
    
	$db->sql_query($sql);
	$resp_istr=$db->sql_fetchfield("resp_istr");
	if ($_SESSION["USER_ID"]==$resp_istr)
		$_SESSION["PROPR_PRATICA_$idpratica"]="SI";
	else
		$_SESSION["PROPR_PRATICA_$idpratica"]="NO";
    */
	$sql="SELECT * FROM  pe.responsabili_pratica WHERE pratica=$idpratica;";	
    $db->sql_query($sql);
	$owners=$db->sql_fetchlist('userid');
    if(in_array($_SESSION['USER_ID'],$owners)){
        $_SESSION["PROPR_PRATICA_$idpratica"]=1;
    }
    else{
        $_SESSION["PROPR_PRATICA_$idpratica"]=0;
    }
}
?>