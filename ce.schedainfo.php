<?
include_once("login.php");
include_once "./lib/tabella_v.class.php";
//print_r($_GET);
$id=$_GET["id_persona"];
?>
<html>
<head>
<title>Scheda Informativa </title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>

</head>
<body bgcolor="">
<?
	//echo "$id<br>";
	echo "\n<b>\n";
	$tabella=new Tabella_v("ce/schedainfo");
	$tabella->set_dati("id=$id");
	$tabella->set_titolo("Dati Personali");
	$tabella->get_titolo();
	
	$tabella->edita();
	print("\n</b>\n<input class=\"hexfield1\" style=\"width:80px;margin-top:10;\" type=\"button\" value=\"Chiudi\" onclick=\"javascript:window.close();\" >");
	
?>
</body>
</html>