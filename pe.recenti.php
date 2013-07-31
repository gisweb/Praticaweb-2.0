<?include_once("login.php");?>
<html>
<head>
<title>Pratiche recenti</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body link="#0000FF" vlink="#0000FF" alink="#0000FF">
<?include "./inc/inc.page_header.php";?>
<H2 class=blueBanner>Ultime pratiche aperte</H2>
<?
$userid=$_SESSION["USER_ID"];
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al dadabase");
$db->sql_query ("select pratica from pe.recenti where utente=$userid order by data desc");

$elenco_pratiche=$db->sql_fetchlist("pratica");
$prat_max=count($elenco_pratiche);
if (!$elenco_pratiche){
	print("<p>Nessuna pratica aperta di recente dall'utente<p></body></html>");
	exit;
}
$offset=0;

include "pe.elenco_pratiche.php";
?>
				<input  name=""  id="" class="hexfield1"  type="button" value="  Chiudi  " onClick="javascript:window.open('index.php','indexPraticaweb');window.close()"></td>
</BODY>
</HTML>
