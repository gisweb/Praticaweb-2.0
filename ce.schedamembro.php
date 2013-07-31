<?
include_once("login.php");
include_once "./lib/tabella_v.class.php";
//print_r($_GET);

$active_form="ce.schedamembro.php";
$tabpath="ce";
$file_config="$tabpath/esiti_commissione";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');$idcomm=$_REQUEST["pratica"];
$id=$_REQUEST["id_persona"];
if ($_REQUEST["comm"]==1) $commissione="comm";
else if ($_REQUEST["comm_paesaggio"]==1) $commissione="comm_paesaggio"

?>
<html>
<head>
<title>Scheda Informativa </title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>

</head>
<body>
<?
	include_once "./inc/inc.page_header.php";
	echo "<br>";
	$tabella=new Tabella_v("$tabpath/schedamembro",'edit');
	$tabella->set_dati("id=$id and commissione=$idcomm");
	$tabella->set_titolo("Dati Personali");
	$tabella->get_titolo();
?>
	<FORM name="commissione" method="post" action="praticaweb.php">
	<?
		$tabella->edita();
?>
	<input type="hidden" name="mode" value="<?=$modo?>">
	<input type="hidden" name="<?=$commissione?>" value="1">
	<input type="hidden" name="active_form" value="<?=$active_form?>">
	</FORM>
</body>
</html>