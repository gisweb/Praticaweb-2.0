<?include_once "login.php";
$campo=trim($_GET["campo"]);
$sql=$_GET["s"];
$self=$_SERVER["PHP_SELF"];
$schema=$_GET["schema"];
switch($campo) {
	case 'comune':
	case 'comuned':
	case 'citta':
	case 'comunato':
		include "elenco.comuni.php";
		exit;
	case 'vincolo':
		include "elenco.zone_vincolo.php";
		exit;				
	case 'via':
		$tabella="$schema.e_vie";
		break;
	case 'titolo':
	case 'titolod':
		$tabella="pe.soggetti";
		break;
	case 'parere':
		$tabella="pe.pareri";
		break;
	case 'notaio':
		$tabella="pe.asservimenti";
		break;
	case 'destuso1':
	case 'destuso2':
		$campo="destuso";
		$tabella='pe.e_destuso';
		break;
	case 'dest_uso':
		$campo="destuso";
		$tabella='cn.e_destuso';
		break;
	case 'motivo':
		$tabella="pe.sopralluoghi";
		break;
	case 'motivo_v':
		$tabella="vigi.sopralluoghi";
		break;
	case 'origine':
		$tabella="vigi.esposti";
		break;
	case 'intervento':
		$tabella="pe.e_intervento";
		break;	
	case 'nota':
		$tabella="pe.e_voci_iter";
		break;			
	case 'sede1':
		$tabella="ce.commissione";
		break;
}
$sql="select distinct $campo as valore from $tabella where $campo ilike '$sql%' order by $campo;";
//echo $sql;
$campo=$_GET["campo"];//riprendo il valore nel caso lo avessi cambiato
$db=new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$db->sql_query ($sql);
$elenco=$db->sql_fetchrowset();
$nrec=$db->sql_numrows();
?>

<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<style type="text/css">
<!--
body,td,th {
	font-family: Georgia, Times New Roman, Times, serif;
	color: #000000;
}
a:link {
	color: #0000FF;
}
a:visited {
	color: #0000FF;
}
a:hover {
	color: #FF9966;
}
a:active {
	color: #0000FF;
}
body {
	background-color: #FFFFDF;
}
-->
</style>

<script>
function setdata(valore) 
{
	parent.document.getElementById('<?=$campo?>').value=valore;
	parent.document.getElementById("dwindow").style.display="none";
	parent.document.getElementById("cframe").src="";
}
</script>
</head>
<body >

<FONT Verdana, Geneva, Arial, sans-serif size="-1">
<?
for($i=0;$i<$nrec;$i++){
	$valore= htmlentities($elenco[$i]["valore"]);
	$jsvalore=str_replace("'","\'",$valore);
	print("<a href=\"javascript:setdata('$jsvalore')\">$valore</a><br>");	
}
?>
</FONT>
<br>
<form method="get" action="<?$self?>">
<input type="hidden" name="campo" value="<?=$campo?>">
<input type="button" value="Chiudi" onclick="javascript:parent.document.getElementById('dwindow').style.display='none';parent.document.getElementById('cframe').src='';">
<input type="submit" value="Elenco completo">
</form>

</body>
</html>
