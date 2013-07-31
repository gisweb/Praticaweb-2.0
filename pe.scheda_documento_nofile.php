<?
include_once("login.php");
include_once "./lib/tabella_v.class.php";
$tabpath="pe";
$self=$_SERVER["PHP_SELF"];
$idpratica=$_REQUEST["pratica"];
$idallegato=$_REQUEST["id"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$titolo=$_SESSION["TITOLO_$idpratica"];
$form=$_POST["form"];
?>
<html>
<head>
<title>Scheda - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language=javascript>

function elimina(id){
	var agree=confirm('Sicuro di voler eliminare l\'allegato selezionato?');
	if (agree){
		document.getElementById("delete").value=id;
		document.ubicazione.submit();
	}
}
</script>

</head>
<body  background="">
<?
if ($modo=="edit") { 
		include "./inc/page_header.inc";	
		$tabella=new tabella_v("$tabpath/doc_dettaglio",$modo);
		unset($_SESSION["ADD_NEW"]);
			?>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<FORM id="" name="" method="post" action="praticaweb.php">		  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Scheda documento allegato:&nbsp;<?=$_POST["allegato"]?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?
				$tabella->set_dati("id=$idallegato");
				$tabella->edita();
				?>
				<!-- fine contenuto-->
			</td>
		  </tr>		  
		  <tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   </tr>  

		</TABLE>		
		
		<table>
			<tr>
				<input name="active_form" type="hidden" value="pe.scheda_documento_nofile.php">	
				<input name="id" type="hidden" value="<?=$idallegato?>">
				<input name="mode" type="hidden" value="<?=$modo?>">				
				<input name="pratica" type="hidden" value="<?=$idpratica?>">
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Salva"></td>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Annulla"></td>				  </FORM>	
			</tr>
		</table>			
<?
}else{		
//<<<<<<<<<<<<<<<<<<<<<   MODALITA' VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->	
		$tabella=new tabella_v("$tabpath/doc_dettaglio");
		$id=substr($idallegato,4);
		$tabella->set_dati("id = $id");
		$nome_doc=$tabella->get_campo("documento");
		?>
		<H2 class="blueBanner">Scheda allegato:&nbsp; <?=$nome_doc?></H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?//aggiunto il campo costante tabella = richiedenti/tecnici per portarmi sul form il nome della tabella
				$tabella->set_titolo("Scheda allegato","modifica",array("allegato"=>$nome_doc,"id"=>$id,"pratica"=>""));
				$tabella->elenco();?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		   <tr>
			  <td><br><br>
				  &nbsp;<input  name=""  id="" class="hexfield1" style="width:70px" type="button" value="chiudi"  onClick="javascript:window.location='pe.allegati.php?pratica=<?=$_REQUEST["pratica"]?>'" >
			  </td>
			</tr>
		</TABLE>	
		
<?	} //end switch?>

</body>
</html>
