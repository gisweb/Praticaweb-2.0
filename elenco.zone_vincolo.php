<?include_once "login.php";
$sql=$_GET["s"];
$val=$_GET["val"]; 
$self=$_SERVER["PHP_SELF"];
$campo=trim($_GET["campo"]);
$tabella=trim($_GET["tabella"]);
if(NEW_VINCOLI==1)
	$sql="select distinct nome_zona as zona,coalesce(sigla,'')||' - '||coalesce(descrizione,'') as descrizione from vincoli.zona where nome_vincolo ilike '$val[0]%' and nome_tavola ilike '$val[1]%';";
else 
	$sql="select zona,coalesce(descrizione,'') as descrizione from $schema.e_vincoli_zone where vincolo ilike '$sql%';";
$campo=$_GET["campo"];//riprendo il valore nel caso lo avessi cambiato
$db=new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$db->sql_query ($sql);
$elenco=$db->sql_fetchrowset();
$nrec=$db->sql_numrows();
//echo $sql;
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
function setdata(zona) 
{
	//parent.document.getElementById('nome_zona').value=nome_zona;
	parent.document.getElementById('zona').value=zona;
	parent.document.getElementById("dwindow").style.display="none";
	parent.document.getElementById("cframe").src="";
}
</script>
</head>
<body >

<FONT Verdana, Geneva, Arial, sans-serif size="-1">
<?
for($i=0;$i<$nrec;$i++){
	$desc= htmlentities($elenco[$i]["descrizione"]);
	$zona= htmlentities($elenco[$i]["zona"]);
	$jszona=str_replace("'","\'",$zona);
	$nome_zona= htmlentities($elenco[$i]["nome_zona"]);
	print("<a href=\"javascript:setdata('$zona')\">$desc</a><br>");
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