<?
//Nota conservo il tipo per poter verificere se Ãš cambiato
include_once("login.php");
$tabpath="pe";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$file_config="$tabpath/avvio_procedimento_amb";
//print_array($_REQUEST);
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
if ($modo=='new'){
	$intestazione='Nuova pratica';
	if ($_POST["infogruppo"])
		$intestazione.=" - Riferimento ".$_POST["infogruppo"] ." " .$_POST["infopratica"];
	elseif ($_POST["riferimento"])
		$intestazione.=" - Nuovo riferimento $newref";
}
else
	$intestazione='Avvio del procedimento e comunicazione responsabile';
include "./lib/tabella_v.class.php";?>

<html>
<head>
<title>Avvio Procedimento - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>

<?if (($modo=="edit") or ($modo=="new")) {

	$tabella=new Tabella_v($file_config,$modo);					
	unset($_SESSION["ADD_NEW"]);	
	include "./inc/inc.page_header.php";?>
	
	<body  background=""  <?=$event?>>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<FORM id="" name="avvioproc" method="post" action="praticaweb.php">		  
		  <tr> 
			<td> 
				<H2 class="blueBanner"><?=$intestazione?></H2>
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
				<input name="active_form" type="hidden" value="pe.avvioproc_amb.php">				
				<input name="refpratica" type="hidden" value="<?=$_POST["refpratica"]?>">
				<input name="riferimento" type="hidden" value="<?=$_POST["riferimento"]?>">				
				<input name="via" type="hidden" value="<?=$_POST["via"]?>">
				<input name="civico" type="hidden" value="<?=$_POST["civico"]?>">
				<input name="ctsezione" type="hidden" value="<?=$_POST["ctsezione"]?>">
				<input name="ctfoglio" type="hidden" value="<?=$_POST["ctfoglio"]?>">
				<input name="ctmappale" type="hidden" value="<?=$_POST["ctmappale"]?>">
				<input name="oldtipo" type="hidden" value="<?=$tabella->get_campo("tipo")?>">
				<input name="mode" type="hidden" value="<?=$modo?>">
				</td>
				
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Salva"></td>
				<?if ($modo=="new"){?>
				<td valign="bottom"><input name="close" type="button" class="hexfield" tabindex="14" value="Annulla" onclick="javascript:NewWindow('index.php','indexPraticaweb',0,0,'yes');window.close();"></td>
				<?}else{?>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Annulla"></td>
				<?}?>
			</tr>
		</FORM>	
		</table>	
<?//include "./inc/inc.window.php"; // contiene la gesione della finestra popup
}else{
?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<body>
		<H2 class="blueBanner">Avvio del procedimento e comunicazione responsabile</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?$tabella=new tabella_v($file_config);
				$tabella->set_titolo("Dati della pratica","modifica");
				$nrec=$tabella->set_dati("pratica=$idpratica");
				$tabella->elenco();
				$tabella->close_db();?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
<?}?>
</body>
</html>
