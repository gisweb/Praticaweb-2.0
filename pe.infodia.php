<?
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
$id=$_POST["id"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
?>
<html>
<head>
<title>Informazioni - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body>

<?	if (($modo=="edit") or ($modo=="new")){
		$tabella=new tabella_v("$tabpath/infodia",$modo);
		$tabella->set_errors($errors);
		include "./inc/inc.page_header.php";?>	
		<FORM id="fidi" name="infodia" method="post" action="praticaweb.php">
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Informazioni DIA</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?if ($id)	$tabella->set_dati("pratica=$idpratica");
				$tabella->edita();?>
				<!-- fine contenuto-->
			</td>
		  </tr>
		</TABLE>
	<input name="active_form" type="hidden" value="pe.infodia.php">
	<input name="mode" type="hidden" value="<?=$_POST["mode"]?>">

  </FORM>	

<?}
else{
		$tabella=new tabella_v("$tabpath/infodia");
		$tabella->set_errors($errors);
		unset($_SESSION["ADD_NEW"]);
		?>
		
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Informazioni DIA</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
					if($tabella->set_dati("pratica = $idpratica")){
						$diniego=$tabella->get_campo("diniego");
						//potrebbe servire esporre la proprietà  dati anche il lettura nella classe tabella_v
						if($diniego){
							$tabella=new tabella_v("$tabpath/infodianodia");
							$tabella->set_dati("pratica = $idpratica");
						}
						$tabella->set_titolo("Informazioni Dia","modifica",array("id"=>""));
						$tabella->elenco();
					}
					else{
						$tabella->set_titolo("Aggiungi Informazioni Dia","nuovo");
						$tabella->get_titolo();
					}?>			
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
<?}?>

</body>
</html>
