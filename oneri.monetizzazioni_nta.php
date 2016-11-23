 <?php
include_once("login.php");
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$tabpath="oneri";
$titolo="Monetizzazione art.24 delle nta";
$file_conf=$tabpath."/".$_REQUEST["tabella"].".tab";
$elimina="<td valign=\"bottom\"><input name=\"azione\" type=\"submit\" class=\"hexfield\" tabindex=\"14\" value=\"Elimina\"></td>";
$Errors=$array_dati["errors"];
?>
<HTML>
<HEAD>
<title>Monetizzazione - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>
<SCRIPT LANGUAGE="JavaScript">
function confirmSubmit()
{
var msg='Sicuro di voler eliminare definitivamente la sanzione corrente?';
var agree=confirm(msg);
if (agree)
	return true ;
else
	return false ;
}
</SCRIPT>
</HEAD>
<BODY>
</HTML>
 <?php
 if (($modo=="edit") or ($modo=="new")) {
	$tabella=new tabella_v($file_conf,"edit");
	unset($_SESSION["ADD_NEW"]);	
	include "./inc/inc.page_header.php";
?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM id="" name="" method="post" action="praticaweb.php">	
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
			  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner"><?php echo $titolo?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
<?php
	if($Errors){
		$tabella->set_errors($Errors);
		$tabella->set_dati($_POST);
	}
	elseif ($modo=="edit"){	
		$tabella->set_dati("pratica=$idpratica");
	}
	$tabella->edita();
?>	
			</td>
		  </tr>
		  <tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   </tr>  
		</TABLE>
		<div>
			<input name="active_form" type="hidden" value="oneri.monetizzazioni_nta.php">
			<input name="mode" type="hidden" value="<?php echo $modo;?>">


		</div>
				
		</FORM>	
<?php
}else{
//           <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
?>
 
<?php
	$tabellav=new Tabella_v("$tabpath/monetizzazione_standard.tab");//tabella verticale con totali ed estremi di pagamento
	$numrows=$tabellav->set_dati("pratica=$idpratica");//vedo se c'è un record nella tabella monetizzazione
	
?>
	<H2 class="blueBanner">Monetizzazione</H2>
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">
		<TR>
			<TD>
<!--  tabella monetizzazione standard-->
<?php
	$tabellav=new Tabella_v("$tabpath/monetizzazione_standard.tab");//tabella verticale con totali ed estremi di pagamento
	$numrows=$tabellav->set_dati("pratica=$idpratica");//vedo se c'è un record nella tabella monetizzazione
	$tabellah=new Tabella_h("$tabpath/monetizzazione_standard.tab");//visto che esiste  mostro la tabella orizzontale con il dettaglio
	if ($numrows){
		$tabellav->set_titolo("Monetizzazione Standard Urbanistici DM 1444/68","modifica",array("tabella"=>"monetizzazione_standard"));			 
		$tabellav->get_titolo();
		$tabellav->tabella();
	}
	else{// se non c'e un calcolo fatto propongo il menu nuovo 
		$tabellav->set_titolo("Monetizzazione Standard Urbanistici DM 1444/68 >>>","nuovo",array("tabella"=>"monetizzazione_standard"));			 
		$tabellav->get_titolo();
	}
?>
<!--  fine tabella monetizzazione-->
			</TD>
		</TR>  
		<TR>
			<TD>
					<!--  tabella monetizzazione nta-->
<?php
	$tabellav=new Tabella_v("$tabpath/monetizzazione_nta.tab","view");//tabella verticale con totali ed estremi di pagamento
	$numrows=$tabellav->set_dati("pratica=$idpratica");//vedo se c'莶 un record nella tabella monetizzazione
	$tabellah=new Tabella_h("$tabpath/monetizzazione_nta.tab");//visto che esiste  mostro la tabella orizzontale con il dettaglio
	if ($numrows){
		
		$tabellah->set_dati("pratica=$idpratica");
		$tabellah->set_titolo("Monetizzazione Art.24 delle NTA","modifica",array("tabella"=>"monetizzazione_nta"));			 
		$tabellah->get_titolo();
		$tabellah->elenco();
	}
	else{// se non c'e un calcolo fatto propongo il menu nuovo 
		$tabellah->set_titolo("Monetizzazione Art.24 delle NTA >>>","nuovo",array("tabella"=>"monetizzazione_nta"));			 
		$tabellah->get_titolo();
	}
?>
<!--  fine tabella monetizzazione-->
<?php
	print($tabellav->elenco_stampe("oneri.importi"));
?>
			</TD>
	  </TR>
	</TABLE>
<?php
}
?>
</BODY>

</HTML>

