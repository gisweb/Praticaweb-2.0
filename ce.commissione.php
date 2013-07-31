<?
include_once("login.php");
$tabpath="ce";
//$form="commissione";
$tornaacasa="
	<script language=javascript>
		parent.location='index.php';
	</script>";
	
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$idcomm=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idcomm"];
$file_config="$tabpath/convocazione";
$title="Commissione Edilizia";
$membri="$tabpath/membri.tab";

include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";

?>
<html>
<head>
<title>Convocazione Commmissione- <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>

<SCRIPT language=javascript>
function link(id){
	loc="ce.schedamembro.php?mode=view&comm=1&pratica=<?=$idcomm?>&id_persona="+id;
	window.parent.location=loc;
	//window.open(pagina,'new',"HEIGHT=160,WIDTH=400,menubar=false,toolbar=false,scrollbars=false,title=false,resizable=false");
}
function elimina(id){

	document.commissione.membro.value=id;
	if (confirm("sei sicuro di voler eliminare questa riga?")) document.commissione.submit();
}

</SCRIPT>
</head>

<?php
if (($modo=="edit") or ($modo=="new")) {
	$_SESSION["ADD_NEW"]=0;	
	if ($_GET["head"]!="no") include_once "./inc/inc.page_header.php";
	?>
	<body  background="" >
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		
		
		<H2 class="blueBanner">Creazione o modifica della  <?=$title?></H2>
			<!-- fine intestazione-->
	<FORM name="commissione" method="post" action="praticaweb.php">
				<!-- contenuto-->
				<?php
				$tabella=new Tabella_v($file_config,$modo);
				if ($modo=="edit") {
					$tabella->set_dati("id=$idcomm");
					$tabella->set_errors($Errors);
					$tabella_partecipanti=new Tabella_h("$tabpath/membri_commissione",'edit');
					//$tabella_non_partecipanti=new Tabella_h("$tabpath/non_membri_commissione",'edit');
					$tabella_partecipanti->set_dati("commissione=$idcomm");
					//$tabella_non_partecipanti->set_dati("commissione=$idcomm");
				}
				else {
					$tabella_partecipanti=new Tabella_h("ce/elenco_membri",'list');
					$tabella_partecipanti->set_dati("id>0");
				}
				
				$tabella->set_titolo("Convocazione della commissione");
				//$tabella->get_titolo();

				$tabella->edita();
				echo "\n<H2 class=\"blueBanner\">Soggetti partecipanti</H2>\n";
				$tabella_partecipanti->elenco();?>

				
		<input name="active_form" type="hidden" value="ce.commissione.php">
		<input name="mode" type="hidden" value="<?=$modo?>">
		<input name="comm" type="hidden" value=1>
		<input name="id" type="hidden" value="<?=$idcomm?>">
		<input name="pratica" type="hidden" value="<?=$idcomm?>">
		<input name="membro" type="hidden">
				
	</FORM>	
<?php
include "./inc/inc.window.php"; // contiene la gestione della finestra popup
}else{
		
		unset($_SESSION["ADD_NEW"]);
		$tabella=new Tabella_v($file_config,'view');
		$tabellah=new Tabella_h('ce/membri_commissione','view');
?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
<body>
		<H2 class="blueBanner"> <?=$title?></H2>
		
			<!-- contenuto-->
				<?$tabella->set_titolo("Dati della commissione","modifica",Array("tipo"=>$tipo));
				$tabella->set_dati("pratica=$idcomm"); 
				$tabellah->set_titolo("Soggetti partecipanti");
				$tabellah->set_dati("commissione=$idcomm order by id_ruolo");
				$tabella->get_titolo();
				$tabella->tabella();
				$tabellah->get_titolo();
				$tabellah->elenco();
				
				
				$tabellav=new tabella_v("$tabpath/stampa.tab");
				$tabellav->set_dati("id>0");
				print($tabellav->elenco_stampe("ce.commissione"));
				}
			
?>
			<!-- fine contenuto-->
</body>
</html>
