<?include_once "login.php";
$campo=trim($_GET["campo"]);
$sql=$_GET["s"];
$self=$_SERVER["PHP_SELF"];?>

<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" /><style type="text/css">
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
</head>
<body >
<script>
function loaddata(nome,cap,prov) 
{
<?if($campo=='comune') { ?>		
	parent.document.getElementById('comune').value=nome;
	parent.document.getElementById('cap').value=cap;
	parent.document.getElementById('prov').value=prov; 
<? } elseif($campo=='comunato') { ?>	
	parent.document.getElementById('comunato').value=nome;
	parent.document.getElementById('provnato').value=prov;
<? } elseif($campo=='comuned') { ?>	
	parent.document.getElementById('comuned').value=nome;
	parent.document.getElementById('capd').value=cap;
	parent.document.getElementById('provd').value=prov;
<? } elseif($campo=='citta') { ?>		
	parent.document.getElementById('citta').value=nome;
	parent.document.getElementById('cap').value=cap;
<? } ?>	
	parent.document.getElementById("dwindow").style.display="none";
	parent.document.getElementById("cframe").src="";
}

</script>

<FONT Verdana, Geneva, Arial, sans-serif size="-1">
<? 
if($sql){
	$db=new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database");	
	$sql="select distinct nome,cap,sigla_prov from pe.e_comuni where nome ilike '$sql%' order by nome;";
	$db->sql_query ($sql);
	$comuni=$db->sql_fetchrowset();
	$nrec=$db->sql_numrows();
	for($i=0;$i<$nrec;$i++){
		$nomecomune=htmlentities($comuni[$i]["nome"]);
		$nomecomune=str_replace("'","\'",$nomecomune);
		print("<a href=\"javascript:loaddata('$nomecomune','".$comuni[$i]["cap"]."','".$comuni[$i]["sigla_prov"]."')\">");	
		$prov=$comuni[$i]["sigla_prov"];
		($prov)?($prov=" ($prov)"):($prov);
		print ($comuni[$i]["nome"].$prov."</a><br>");
	}
	?>
	</FONT>
	<br>
	<form method="get" action="<?$self?>">
	<input type="hidden" name="campo" value="<?=$campo?>">
	<input type="button" value="Chiudi" onclick="javascript:parent.document.getElementById('dwindow').style.display='none';parent.document.getElementById('cframe').src='';">
	<input type="submit" value="Aggiorna la ricerca">
	</form>
<?
}else{
	$lett=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	for($i=0;$i<count($lett);$i++){
		print("<a href=\"elenco.comuni.php?campo=$campo&s=".$lett[$i]."\">[".$lett[$i]."]</a><br>");
	}
}

?>


</body>
</html>