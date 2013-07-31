<?
//Stesso codice che utilizzo in ubicazione.php, progetto.php, asservimento.php
include_once ("login.php");
$tabpath="pe";
include_once "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');

$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];

if ($_POST["azione"]){
	$idrow=$_POST["idriga"];
	$active_form=$_REQUEST["active_form"];
	if($_SESSION["ADD_NEW"]!==$_POST){
		unset($_SESSION["ADD_NEW"]);//serve per non inserire piÃ¹ record con f5
		if (isset($array_dati["errors"])) //sono al ritorno errore
			$Errors=$array_dati["errors"];
		else
			include_once "./db/db.pe.asservimenti_mappali.php";
		$_SESSION["ADD_NEW"]=$_POST;		
	}
}

?>

<html>
<head>
<title>Asservimento - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language="javascript">
function confirmSubmit()
{
	document.getElementById("azione").value="Salva";
	return true ;
}

function elimina(id){
	var agree=confirm('Sicuro di voler eliminare definitivamente la riga selezionata?');
	
	if (agree){
		
		$("#btn_azione").val("Elimina");
		$("#idriga").val(id);
		$("#asserviti").submit();
	}
	
}

function link(id){
	window.location='pe.nomappa.php?mapkey='+id;
}

</script>
</head>
<body>
<?if (($modo=="edit") or ($modo=="new")){
	//---<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  EDITA MAPPALI ASSERVITI >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------------------------------>
	if($_POST["mappali"]){
		$asservimento=$_REQUEST["asservimento"];
		$tabellav=new tabella_v("$tabpath/asservimenti_mappali",'new');
		$tabellav->set_titolo(null,null,Array("asservimento"=>$asservimento));
		$tabellah=new tabella_h("$tabpath/asservimenti_mappali",'edit');
		
		$tabellav->set_errors($errors);
		include "./inc/inc.page_header.php";?>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Modifica elenco mappali asserviti</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<form method=post id = "asserviti" name="asserviti" action="pe.asservimenti.php">
				<input type="hidden" name="idriga" id="idriga" value="0">
				<input name="active_form" type="hidden" value="pe.asservimenti.php">
				<input type="hidden" name="mode" value="new">
				<input type="hidden" name="mappali" value="1">
				<?
				if($Errors){
					$tabellav->set_errors($Errors);
					$tabellav->set_dati($_POST);
				}
				  $tabellav->edita();
				  $numrows=$tabellah->set_dati("pratica=$idpratica and asservimento=$asservimento");
				  if ($numrows)  $tabellah->elenco();?>
				
				</form>
				<!-- fine contenuto-->			

			</td>
		  </tr> 
		</TABLE>
		
		<TABLE>
		<FORM method="post" action="praticaweb.php">	
			<tr>
				<td><input name="active_form" type="hidden" value="pe.asservimenti.php">
				<input name="pratica" type="hidden" value="<?=$idpratica?>"></td>
				
			</tr>
		</FORM>		
		</TABLE>
	<?}else{
	//---<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  EDITA ASSERVIMENTO >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------------------------------>	
		unset($_SESSION["ADD_NEW"]);
		$tabella_asservimento=new tabella_v("$tabpath/asservimenti",$modo);
		include "./inc/inc.page_header.php";?>
	<FORM method="post" action="praticaweb.php">	
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="95%" >		
			<tr>
				<!-- intestazione-->
				<td><H2 class=blueBanner>Modifica Asservimento</H2></td>
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
		<input name="active_form" type="hidden" value="pe.asservimenti.php">
		<input name="mode" type="hidden" value="<?php echo $modo?>">
		<input name="pratica" type="hidden" value="<?php echo $idpratica?>">
	</form>
	<?php include "./inc/inc.window.php"; // contiene la gesione della finestra popup
	}
}	
else{
	//-<<<<<<<<<<<<<<<<<<<<<< VISUALIZZA DATI ASSERVIMENTO E MAPPALI ASSERVITI>>>>>>>>>>>>>>>>>>>>>>>>>>>----------------------->	
		?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   ASSERVIMENTO  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Mappali asserviti</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?
				$tabella_asservimento=new tabella_v("$tabpath/asservimenti");
		        $nrec=$tabella_asservimento->set_dati("pratica = $idpratica order by data_reg");	

					if ($nrec){
						$tabella_asservimento->set_titolo("Asservimento","modifica",array("id"=>""));
						for($i=0;$i<$nrec;$i++){
							$tabella_asservimento->curr_record=$i;
							$tabella_asservimento->set_titolo("Asservimento ".$tabella_asservimento->array_dati[$i]['numero'],"modifica",array("id"=>""));
							$tabella_asservimento->idtabella=$tabella_asservimento->array_dati[$i]['id'];
							$tabella_asservimento->get_titolo();
							$tabella_asservimento->tabella();
							$tabella_asserviti=new tabella_h("$tabpath/asservimenti_mappali");
							//$tabella_asserviti->set_titolo("Elenco dei mappali asserviti","modifica",array("mappali"=>"1"array("titolo"=>$titolo,"tab_new"=>$file_tab."_new","tab_edit"=>$file_tab."_edit")));
							$tabella_asserviti->set_titolo("Elenco dei mappali asserviti","modifica",array("mappali"=>1,"asservimento"=>$tabella_asservimento->array_dati[$i]['id']));
							$nrow=$tabella_asserviti->set_dati("pratica=$idpratica and asservimento=".$tabella_asservimento->array_dati[$i]['id']);
							$tabella_asserviti->get_titolo();
							if($nrow)
								$tabella_asserviti->elenco();
							else
								print ("<p><b>Nessun mappale asservito</b></p>");
						}
						
						
					}
				
					$tabella_asservimento->set_titolo("Nuovo Asservimento","nuovo");
					$tabella_asservimento->get_titolo();
					if (!$nrec) print ("<p><b>Nessun asservimento</b></p>");
								
					?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
	
<?}?>		

</body>
</html>
