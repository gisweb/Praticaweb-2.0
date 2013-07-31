<?
include_once("login.php");
include "./lib/tabella_h.class.php";
$tabpath="cdu";

$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$titolo=$_SESSION["TITOLO_$idpratica"];
$today=date('j-m-y'); 


if ($_POST["azione"]){
	//$id=$_POST["idrow"];
	$active_form=$_REQUEST["active_form"];
	if($_SESSION["ADD_NEW"]!==$_POST)
		unset($_SESSION["ADD_NEW"]);//serve per non inserire piÃ¹ record con f5
	if (isset($array_dati["errors"])) //sono al ritorno errore
		$Errors=$array_dati["errors"];
	else{
		include "db/db.cdu.iter.php";
	}
	$_SESSION["ADD_NEW"]=$_POST;				
}

$titolo="Iter della pratica";

?>

<html>
<head>
<title>Iter - <?=$titolo?></title>
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
		$('#iter').submit();
	}
}
</script>
</head>
<body>
<?if (($modo=="edit") or ($modo=="new") ){
	//---<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  EDITA ELENCO ITER >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------------------------------>
		$tabella=new tabella_h("$tabpath/iter",'edit');
		include "./inc/inc.page_header.php";?>
		
	<form method="post" name="iter" id="iter" action="cdu.iter.php">		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">			
		<!--<tr> 
			<td> 

				<H2 class="blueBanner">Modifica elenco eventi della pratica</H2>

			</td>
		  </tr>
		  <tr>
			  <td>
				  <table cellPadding="2" border="0" class="stiletabella">
				  <tr>
					  <td colspan="4">
					  <select style="width:650px" class="textbox"  name="evento"  id="evento" onmousewheel="return false"  onchange="javascript:document.iter.nota_edit.value=document.iter.evento.options[document.iter.evento.selectedIndex].text">
								  <option>Seleziona da elenco >>></option>
								<option >Avvio del procedimento</option>
								<option >Richiesta documentazione integrativa</option>
								<option >Pratica presa in carico dall'utente <?=$_SESSION["USER_NAME"]?></option>
							</select>
					  </td>
				  </tr>
					<tr>
						<td width="130" height="24" bgColor="#728bb8"><font color="#ffffff"><b>Evento</b></font></td>
						<td valign="middle" colspan="3">
							<textarea cols="62" rows="2" name="nota_edit" id="nota_edit"></textarea>
						</td>
					</tr>
					<tr>
						<td height="24" valign="top" bgColor="#728bb8"><font color="#ffffff"><b>Data</b></font></td>
						<td width="66" valign="top"><INPUT  maxLength="10" size="10"  class="textbox" name="data" id="data" value="<?=$today?>"></td>
						<td  valign="top"><input type="checkbox"  name="pubblico" checked>
					    <b>Commento pubblicato</b></td>				
						<td  valign="top">
							<input  name="aggiungi"  id="aggiungi" class="hexfield1" style="width:130px" type="submit" value="Aggiungi" onclick="return confirmSubmit()" >
							<input  class="hexfield1" style="width:130px" type="button" value="Carica Documento" href="#" onclick="NewWindow('stp.carica_documento.php?schema=ce&pratica=<?=$idpratica?>','documento',500,200);" >
						</td>
					</tr>
				</table>
				<input type="hidden" name="utente" value="<?=$_SESSION["USER_NAME"]?>">
			  <br>
					<table width="90%">		  	
						<tr>
							<td  bgColor="#728bb8" ><font face="Verdana" color="#FFFFFF" size="2">
								<b>Elenco degli eventi registrati</b></font>	
							</td>
						</tr>
					</table>					
			  </td>
		  </tr>-->
		  

		  
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?
				$numrows=$tabella->set_dati("pratica=$idpratica");
				if ($numrows){
					$tabella->set_titolo("Eventi");
					$tabella->get_titolo(); 
					$tabella->elenco();
					
				}
				else{
					print '<H2 class="blueBanner">Iter della pratica</H2>';
					$tabella->set_titolo("Eventi");
					$tabella->print_titolo(); 
					print ("<p><b>Nessun evento</b></p>");
					print $tabella->set_buttons();
				}
				
				
				?>	
				<input type="hidden"  id="idriga" name="idriga" value="0">
				<input type="hidden" name="mode" value="new">
				<INPUT type="hidden" name="pratica" value="<?=$idpratica?>">
				<INPUT type="hidden" name="chk" value="">
				<INPUT type="hidden" name="cdu" value="1">
				<INPUT type="hidden" name="config_file" value="<?=$tabpath?>/iter.tab">
				<br><br><br>
				<!-- fine contenuto-->			
			</td>
		  </tr>
		  
		   
		</TABLE>
	</form>		
	<?
}	
else{
	//-<<<<<<<<<<<<<<<<<<<<<< VISUALIZZA ITER >>>>>>>>>>>>>>>>>>>>>>>>>>>----------------------->	
		$tabella=new tabella_h("$tabpath/iter");
		
		$nrec=$tabella->set_dati("pratica = $idpratica");	?>			
		<H2 class="blueBanner">Iter della pratica</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?
					$tabella->set_titolo($titolo,"modifica");
					$tabella->get_titolo();
					if ($nrec)	
						$tabella->elenco();
					else
						print ("<p><b>Nessun evento</b></p>");			
					?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
	
<?}?>		

</body>
</html>
