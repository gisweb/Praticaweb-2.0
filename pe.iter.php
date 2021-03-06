<?php
include_once("login.php");
include "./lib/tabella_h.class.php";
$tabpath="pe";
//print_array($_REQUEST);
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]) && $_REQUEST["mode"])?($_REQUEST["mode"]):('view');
$today=date('j-m-y'); 
$titolo="Iter - ".$_SESSION["TITOLO_".$idpratica];
$pr=new pratica($idpratica);
$pr->createStructure();

if (isset($_POST["azione"]) && $_POST["azione"]){
	//$id=$_POST["idrow"];
	$active_form=$_REQUEST["active_form"];
	if($_SESSION["ADD_NEW"]!==$_POST)
		unset($_SESSION["ADD_NEW"]);//serve per non inserire piÃ¹ record con f5
	if (isset($array_dati["errors"])) //sono al ritorno errore
		$Errors=$array_dati["errors"];
	else{
		include "db/db.pe.iter.php";
	}
	$_SESSION["ADD_NEW"]=$_POST;				
}
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);



?>

<html>
<head>
<title>Iter - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php
	utils::loadJS();
	utils::loadCss(Array('iter'));
?>
<script language="javascript">
function confirmSubmit()
{
	document.getElementById("azione").value="Salva";
	return true ;
}
function elimina(id){
	var agree=confirm('Sicuro di voler eliminare definitivamente la riga selezionata?');
	if (agree){
		document.getElementById("azione").value="Elimina";
		document.getElementById("idriga").value=id;
		document.iter.submit();
	}
}
</script>
</head>
<body>
<?php
if (($modo=="edit") or ($modo=="new") ){
	//---<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  EDITA ELENCO ITER >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------------------------------>
		$tabella=new tabella_h("$tabpath/iter",$modo);
		include "./inc/inc.page_header.php";?>
		
		<form method="post" name="iter" action="pe.iter.php">		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">			
		<tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Modifica elenco eventi della pratica</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr>
			  <td>
				  <table cellPadding="2" border="0" class="stiletabella">

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
							<input  class="hexfield1" style="width:130px" type="submit" value="Carica Documento" onclick="NewWindow('stp.carica_documento.php?schema=pe&pratica=<?=$idpratica?>','documento',500,200);" >
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
		  </tr>
		  

		  
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?php
                                $numrows=$tabella->set_dati("pratica=$idpratica");
				  if ($numrows)  print $tabella->elenco();?>	
				<input type="hidden" name="azione" id="azione" value="aggiungi">
				<input type="hidden"  id="idriga" name="idriga" value="0">
				<input type="hidden" name="mode" value="new">
				<INPUT type="hidden" name="pratica" value="<?=$idpratica?>">
				<INPUT type="hidden" name="chk" value="">
				<INPUT type="hidden" name="config_file" value="pe/iter.tab">
					<input name="active_form" type="hidden" value="pe.iter.php">
				<br><br><br>
				<!-- fine contenuto-->			
			</td>
		  </tr>
		  
		  <tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   </tr>  
		</TABLE>
		</form>		
		
		<TABLE>
		<FORM method="post" action="praticaweb.php">	
			<tr>
				<td><input name="active_form" type="hidden" value="pe.iter.php">
				<input name="pratica" type="hidden" value="<?=$idpratica?>"></td>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Chiudi"></td>
			</tr>
		</FORM>		
		</TABLE>
	<?php
}	
else{
	//-<<<<<<<<<<<<<<<<<<<<<< VISUALIZZA ITER >>>>>>>>>>>>>>>>>>>>>>>>>>>----------------------->	
		$tabella=new tabella_h("$tabpath/iter_pratica",$modo);

		$nrec=$tabella->set_dati("pratica = $idpratica");	?>			
		<H2 class="blueBanner">Iter della pratica</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
					$tabella->set_titolo($titolo);
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
	
<?php
}
?>		

</body>
</html>
