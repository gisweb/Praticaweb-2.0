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
<title>Titolo - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body  background="">

<?

$tab=$_POST["tabella"];
if (($modo=="edit") || ($modo=="new")) {
	unset($_SESSION["ADD_NEW"]);
	if ($tab=="titolo"){
		$titolo_form="Titolo rilasciato";
		$file_config="$tabpath/titolo";
	}
	elseif ($tab=="volture"){
		$titolo_form="Voltura";		
		$file_config="$tabpath/voltura";
	}
	
	$tabella=new Tabella_v($file_config,$modo);	
	include "./inc/inc.page_header.php";
	?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<FORM id="" name="" method="post" action="praticaweb.php">		  
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
					$tabella->set_dati("pratica=$idpratica");
				}
				$tabella->edita();?>			  
			</td>
		  </tr>
		  <tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   </tr>  
		</TABLE>
		<table>
			<tr>
				<td>
				<input name="active_form" type="hidden" value="pe.titolo_amb.php">
				<input name="mode" type="hidden" value="<?=$modo?>">
				<input name="tabella" type="hidden" value="<?=$tab?>"></td>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Salva"></td>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Annulla"></td>
				<?if($modo=="edit"){?>
					<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Elimina" onClick="return confirmSubmit()"></td>
				<?}?>
			</tr>
		</FORM>	
		</table>	
<?}else{
		$tabella=new Tabella_v("$tabpath/titolo");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Rilascio del titolo</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?if($tabella->set_dati("pratica=$idpratica")){
						$tabella->set_titolo("Rilascio Titolo","modifica",array("tabella"=>"titolo"));
						$tabella->elenco();
						echo("<br>");					
						$tabella_voltura=new tabella_v("$tabpath/voltura");
						$tabella_voltura->set_titolo("Voltura","modifica",array("tabella"=>"volture"));
						$tabella_voltura->set_dati("pratica=$idpratica");
						$tabella_voltura->elenco();
						echo("<br>");					
						$tabella_voltura->set_titolo("Inserisci Voltura","nuovo",array("tabella"=>"volture"));
						$tabella_voltura->get_titolo();
						print("<br>");
						print($tabella->elenco_stampe("pe.titolo_amb"));

						}				
					else{
						$tabella->set_titolo("Inserisci dati relativi al titolo rilasciato","nuovo",array("tabella"=>"titolo"));
						print $tabella->get_titolo();
						print ("<p><b>Nessun titolo rilasciato</b></p>");
						print("<br>");
						print($tabella->elenco_stampe("pe.titolo_amb"));
					}
?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
<?}?>
</body>
</html>
