<?
include_once ("login.php");
if ($_SESSION["PERMESSI"]>2){
	include_once HOME;
	exit;
}
include "./lib/tabella_h.class.php";
$tabpath="oneri";
$file_config="$tabpath/elenco_tariffe";
?>
<html>
<head>
<title>Aggiornamento tariffe Oneri</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script>
	function link(id){
		var frm = document.getElementById("frm_anno");
		document.getElementById("anno").value=id;
		frm.submit();
	}
</script>
</head>
<body>
<?
include "./inc/inc.page_header.php";
$tabella=new Tabella_h($file_config);
$tabella->set_titolo("Elenco Tariffe Oneri","Nuovo");
$tabella->set_dati();
$tabella->get_titolo("oneri.tariffe.php");
//echo "<pre>";print_r($tabella);echo "</pre>";
$tabella->elenco();
?>
<form action="oneri.tariffe.php" method="POST" name="frm_anno" id="frm_anno">
	<input type="hidden" name="anno" value="" id="anno">
	<input type="hidden" name="mode" value="view">
</form>
</body>
</html>