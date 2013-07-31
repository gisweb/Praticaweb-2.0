<?
include_once("login.php");
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$idpratica=$_REQUEST["pratica"];
$tabpath="vigi";
$config_file="$tabpath/sopralluoghi";
$host=$_SERVER["HTTP_HOST"];
$modal=$_POST["modal"];
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";
?>
<html>
<head>
<title>Sopralluogo</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language=javascript>
function link(file){
	window.open("foto/"+file,'new',"HEIGHT=400,WIDTH=500,menubar=false,toolbar=false,scrollbars=false,title=false");
}
function apri_foto(file,w,h){
	window.open("foto/"+file,'new',"HEIGHT="+h+",WIDTH="+w+",menubar=false,toolbar=false,scrollbars=false,title=false");
}
function aggiungi_foto(){
idpr=pippo.pratica.value;
spr=pippo.id_sopralluoghi.value;
host=pippo.host.value;

window.open('http://'+host+'/praticaweb/carica_foto.php?pratica='+idpr+'&form=vigilanza&id_sopralluoghi='+spr,'Documento','Height=200,Width=550,toolbar=no,resizable=no');
}
</script>
</head>
<body  background="">
	<?
	
	//<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDIT DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->

	if ($modo=="edit" or $modo=="new" ) {
		include "./inc/inc.page_header.php";
		print("<H2 class=\"blueBanner\">Nuovo Sopralluogo</H2>");
		$id=$soprall=$_POST["id_sopralluoghi"];

		//if ($modo=="edit") $config_file.="v";
		$tabella=new tabella_v($config_file,$modo);
		$tabella->set_errors($Errors);
		if ($modo=="edit") $tabella->set_dati("id=".$_POST["id_sopralluoghi"]);
		else
			unset($_SESSION["ADD_NEW"]);
		
		print "<form method=\"post\" action=\"praticaweb.php\">";
		
		$tabella->edita();
		
		if ($modal) {
			if (strlen($_POST["descrizione"])) $desc=$_POST["descrizione"];
			$uploaddir = getcwd()."/foto/";
			$form="vigilanza";
			$idallegato=$_POST["id_sopralluoghi"];
			include "./lib/upload.php";
		}
	?>
	
	<br>
		<input name="active_form" type="hidden" value="vigi.sopralluoghi.php">				
		<input name="id_sopralluoghi" type="hidden" value="<?=$soprall;?>">
		<input name="host" type="hidden" value="<?=$host;?>">
		<input name="mode" type="hidden" value="<?=$modo?>">
		<input name="id" type="hidden" value="<?=$id;?>">

	</form>
	<br>
	<br>
	<?php
		
	include "./inc/inc.window.php"; // contiene la gesione della finestra popup
	}
	else{
	
		//<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	
			print("<H2 class=\"blueBanner\">Sopralluoghi effettuati</H2>");

			$tabella=new tabella_v($config_file,$modo);
			$nrec=$tabella->set_dati("pratica=$idpratica order by data");
			
			for($i=0;$i<$nrec;$i++){
				$tabella->curr_record=$i;
				$dati_id=$tabella->get_campo("id");
				$dati_data=$tabella->get_campo("data");
				//$dati_soprall=$tabella->get_campo("vigilanza");
				$tabella->idtabella=$dati_id;
				$tabella->set_titolo("Sopralluogo del ".$dati_data,"modifica",array("id_sopralluoghi"=>$dati_id));
				$tabella->get_titolo();
				$tabella->tabella();
				print "<div class=\"button_line\"></div>\n";
				
				$tabella->elenco_stampe("vigilanza");
			}
			
			$tabella->set_titolo("Nuovo Sopralluogo","nuovo");
			$tabella->get_titolo();
	}?>

</body>
</html>

