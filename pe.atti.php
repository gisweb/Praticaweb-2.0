<?
//Stesso codice che utilizzo in ubicazione.php, progetto.php, asservimento.php
include_once ("login.php");
$tabpath="pe";
include_once "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');

$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];


?>

<html>
<head>
<title>Atti - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body>
<?if (($modo=="edit") or ($modo=="new")){
	
	//---<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  EDITA ATTO >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------------------------------>	
		unset($_SESSION["ADD_NEW"]);
		$tabella_asservimento=new tabella_v("$tabpath/atti",$modo);
		include "./inc/inc.page_header.php";?>
	<FORM method="post" action="praticaweb.php">	
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="95%" >		
			<tr>
				<!-- intestazione-->
				<td><H2 class=blueBanner>Modifica Atto</H2></td>
			</tr>
			<tr> 
				<td> 
	<!-- contenuto-->
	<?
	if($Errors){
		$tabella_asservimento->set_errors($Errors);
		$tabella_asservimento->set_dati($_POST);
	}
	elseif ($modo=="edit"){	
		$tabella_asservimento->set_dati("id=".$_REQUEST["id"]);
	}
	$tabella_asservimento->edita();?>	
	<!-- fine contenuto-->
				</td>
			</tr>
		   
		</TABLE>
		<input name="active_form" type="hidden" value="pe.atti.php">
		<input name="mode" type="hidden" value="<?php echo $modo?>">
		<input name="pratica" type="hidden" value="<?php echo $idpratica?>">
	</form>
	<?php include "./inc/inc.window.php"; // contiene la gesione della finestra popup
	
}	
else{
	//-<<<<<<<<<<<<<<<<<<<<<< VISUALIZZA DATI ASSERVIMENTO E MAPPALI ASSERVITI>>>>>>>>>>>>>>>>>>>>>>>>>>>----------------------->	
		?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   ASSERVIMENTO  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Atti e Titoli</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?
				$tabella_asservimento=new tabella_v("$tabpath/atti");
		        $nrec=$tabella_asservimento->set_dati("pratica = $idpratica order by data_reg");	

					if ($nrec){
						$tabella_asservimento->set_titolo("Atto","modifica",array("id"=>""));
						for($i=0;$i<$nrec;$i++){
							$tabella_asservimento->curr_record=$i;
							$tabella_asservimento->idtabella=$tabella_asservimento->array_dati[$i]['id'];
							$tabella_asservimento->get_titolo();
							$tabella_asservimento->tabella();
						}
					}
				
					$tabella_asservimento->set_titolo("Nuovo Atto","nuovo");
					$tabella_asservimento->get_titolo();
					if (!$nrec) print ("<p><b>Nessun atto</b></p>");
								
					?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
	
<?}?>		

</body>
</html>
