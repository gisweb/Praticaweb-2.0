<?
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');

?>
<html>
<head>
<title>Lavori- <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body  background="">

<?

$tab=$_POST["tabella"];
$id=$_POST["id"];
if (($modo=="edit") || ($modo=="new")) {
	unset($_SESSION["ADD_NEW"]);
	if ($tab=="lavori"){
		$titolo_form="Scadenze Lavori";
		$file_config="$tabpath/lavori";
	}
	elseif ($tab=="proroga"){
		$titolo_form="Proroga";
		$file_config="$tabpath/proroga";
	}
	
	$tabella=new Tabella_v($file_config,$modo);	
	include "./inc/inc.page_header.php";?>
	
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	<FORM id="" name="" method="post" action="praticaweb.php">	
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
			  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner"><?=$titolo_form?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?
				if($Errors){
					$tabella->set_errors($Errors);
					$tabella->set_dati($_POST);
				}
				elseif ($modo=="edit"){	
					$tabella->set_dati("id=$id");
				}
				$tabella->edita();?>			  
			</td>
		  </tr>
		</TABLE>

		<input name="active_form" type="hidden" value="pe.lavori.php">
		<input name="mode" type="hidden" value="<?=$modo?>">
		<input name="tabella" type="hidden" value="<?=$tab?>">
				
	</FORM>	
<?php
}else
{?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Esecuzione Lavori</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
				$tabella=new Tabella_v("$tabpath/lavori");
				
				if($tabella->set_dati("pratica=$idpratica")){
								$tabella->set_titolo("Scadenze lavori","modifica",array("tabella"=>"lavori","id"=>""));
								$tabella->get_titolo();
								$tabella->tabella();
								echo("<div class='button_line'></div>");								
								$tabella_proroga=new tabella_v("$tabpath/proroga");
								$tabella_proroga->set_titolo("Proroga","modifica",array("tabella"=>"proroga","id"=>""));
								$tabella_proroga->set_dati("pratica=$idpratica");
								$tabella_proroga->elenco();
								echo("<div class='button_line'></div>");
								$tabella_proroga->set_titolo("Inserisci nuova proroga","nuovo",array("tabella"=>"proroga"));
								$tabella_proroga->get_titolo();
				}				
				else{
								$tabella->set_titolo("Inserisci dati relativi alle scadenze lavori","nuovo",array("tabella"=>"lavori"));
								print $tabella->get_titolo();
								print ("<p><b>Scadenze lavori non impostate</b></p>");
				}
?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
<?}?>
</body>
</html>
