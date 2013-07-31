<?
include_once("login.php");
include "./lib/tabella_h.class.php";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
?>
<html>
<head>
<title>Provvedimento - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body>
<?$tipo=array("sanzione"=>"Sanzione Amministrativa","sospensione"=>"Ordinanza di sospensione lavori","demolizione"=>"Ordinanza di demolizione");
$idtipo=$_POST["tipo"];
//if ($_POST["mode"]=="new"){
		$tabella=new tabella_h($idtipo,'new');	
		include "./inc/inc.page_header.php";?>
	
		<!-- <<<<<<<<<<<<<<<<<<<<<   NUOVO PROVVEDIMENTO  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<FORM id="" name="" method="post" action="praticaweb.php">		  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner"><?echo("Inserisci Nuova ".$tipo[$idtipo])?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?$tabella->edita();?>
				<!-- fine contenuto-->
			</td>
		  </tr>
		  <tr> 
				<!-- riga finale -->
				<td align="center"><img src="images/gray_light.gif" height="2" width="99%"></td>
		   </tr>  
		</TABLE>
		<table>
			<tr>
				<td><input name="pratica" type="hidden" value="<?=$idpratica?>"></td>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Invia"></td>
				<td valign="bottom"><input name="pippo" type="button" class="hexfield" tabindex="14" value="Annulla" onclick="window.location='praticaweb.php?active_form=vigi.provvedimenti.php&pratica=<?=$idpratica?>&mode=view'"></td>			</tr>
		</FORM>		
		</table>	