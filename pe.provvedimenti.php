<?
include_once("login.php");
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$idpratica=$_REQUEST["pratica"];

if (isset($_POST["id_soggetto"])){
	include "db/db.pe.notifiche.php";
	$modo="edit";
	
}

$tabpath="pe";
$config_file="$tabpath/provvedimenti";
include_once "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";
?>
<html>
<head>
<title>Provvedimenti</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript">
	function elimina(id){
		invia.azione.value="Elimina";
		invia.id_soggetto.value=id;
		if (confirm('Sei sicuro di voler eliminare questa riga?')) invia.submit();
	}
</SCRIPT>
</head>
<body  background="">

<?	
if ($_POST["mode"]=="tmp_new"){
	include "./inc/inc.page_header.php";	
	unset($_SESSION["ADD_NEW"]);?>
		
		<!-- <<<<<<<<<<<<<<<<<<<<<   SCELTA DEL NUOVO PROVVEDIMENTO  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Nuovo provvedimento</H2>
		<TABLE cellPadding=3  cellspacing=2 border=0 class="stiletabella" width="99%" align="center">		
		<FORM id="" name="" method="post" action="pe.provvedimenti.php">		  
		  <tr> 
				<td colspan=2> 
					<h3>scegli il tipo di provvedimento:</h3>
				</td>
			<tr>
				<td width=10>
					<input type=radio name=tipo value="1" checked>
				</td>
				<td width=100%>
					<font size=1><b>Sanzione amministrativa</b></font>
				</td>
			</tr>
			<tr>
				<td >
					<input type=radio name=tipo value="2">
				</td>
				<td>
					<font size=1><b>Ordinanza di sospensione lavori</b></font>
				</td>
			</tr>			
			<tr>
				<td >
					<input type=radio name=tipo value="3">
				</td>
				<td>
					<font size=1><b>Ordinanza di demolizione</b></font>
				</td>
			</tr>				
		  <tr> 
				<!-- riga finale -->
				<td colspan=2 align="center"><img src="images/gray_light.gif" height="2" width="99%"></td>
		   </tr>  
		</TABLE>
		<table>
			<tr>
				<td><input name="pratica" type="hidden" value="<?=$idpratica?>">
				    <input name="mode" type="hidden" value="new">
					<input name="id" type="hidden" value="0">
				</td>
				<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Avanti"></td>
				<!--<td valign="bottom"><input name="azione" type="submit" class="hexfield" tabindex="14" value="Annulla"></td>	-->
			</tr>
		</FORM>		
		</table>	
<?}
		/*-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>---*/

elseif ($modo=="edit" or $modo=="new"){
		
		include "./inc/inc.page_header.php";
		//$idpratica=$_POST["pratica"];
		$id=$_POST["id"];
		$id_tipo=$_POST["tipo"];
		//Carico titoli e form diversi a seconda del tipo di provvedimento
		switch ($id_tipo) {
			case 1:
				$config_file="sanzioni";
				$titolo="Sanzione amministrativa";
				break;
			case 2:
				$config_file="ord_sospensione";
				$titolo="Ordinanza di sospensione dei lavori";
				break;
			case 3:
				$config_file="ord_demolizione";
				$titolo="Ordinanza di demolizione";
				break;
		}
		//echo ""
		$config_file="$tabpath/$config_file";
		$tabella=new tabella_v($config_file,$modo);
		$tabella->set_titolo($titolo);
		$tabella->get_titolo();
		?>
		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<FORM id="" name="" method="post" action="praticaweb.php">		  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner"><?print(($modo=="edit")?("Modifica provvedimento"):("Inserisci nuovo provvedimento"))?></H2>
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
				$tabella->edita();
				?>	
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
				<td>
					<input name="active_form" type="hidden" value="pe.provvedimenti.php">				
					<input name="mode" type="hidden" value="<?=$_POST["mode"]?>">
					<input name="tipo" type="hidden" value="<?=$id_tipo?>">
				</td>
				
			</tr>
			<tr>
				<TD>
				<input name="azione" type="submit" class="hexfield" tabindex="14" value="Salva">
				<?if ($modo=="edit") {?><input name="azione" type="submit" class="hexfield" tabindex="14" value="Elimina"><?}?>
				<input name="azione" type="submit" class="hexfield" tabindex="14" value="Annulla">
				</TD>
			</tr>
		</table>
		</form>
		<form action="pe.provvedimenti.php" name="invia" id="invia" method="POST">
		<?//Aggiungo la parte delle notifiche solo se il tipo di provvedimento Ãš diverso dalla sanzione amministrativa
		if ($_POST["tipo"]!=1 and $modo=="edit") {
		echo "<H2 class=\"blueBanner\">Elenco Soggetti </H2>";
		echo "\t<table width=\"50%\">
			<tr>
				<td valign=\"top\"  width=\"50%\">
				<H2 class=\"blueBanner\" align=\"center\" style=\"background:#728bb8;color:#FFFFFF;FONT-WEIGHT: bold\">Soggetti interessati</H2>";
		$tabella=new tabella_h("$tabpath/notifiche",'edit');
		$nrec=$tabella->set_dati("pratica=$idpratica and not id in (select id_soggetto from pe.dest_provvedimenti where pratica=$idpratica and id_provv=$id)");
		if ($nrec>0) $tabella->elenco();
		else
			echo "Nessun soggetto da notificare";
						
		echo "\t\t\t\t</td></tr>
				<tr><td valign=\"top\" width=\"50%\">
				<H2 class=\"blueBanner\" align=\"center\" style=\"background:#728bb8;color:#FFFFFF;FONT-WEIGHT: bold\">Soggetti notificati</H2>";
		
					$tabella=new tabella_h("$tabpath/notificati",'edit');
					$nrec_notificati=$tabella->set_dati("pratica=$idpratica and id_provv=$id");
					if ($nrec_notificati>0) $tabella->elenco();
					else
						echo "Nessun soggetto notificato";
		
				
		echo "\t\t\t\t</td>
			</tr>
		</table>";
		?>
		<br>
		<?if ($nrec) {?><input name="" type="submit" class="hexfield" tabindex="14" value="Aggiungi" onclick="invia.azione.value='Salva'"><?}?>
		<input name="active_form" type="hidden" value="pe.provvedimenti.php">				
		<input name="mode" type="hidden" value="new">
		<input name="tipo" type="hidden" value="<?=$id_tipo?>">
		<input name="pratica" type="hidden" value="<?=$idpratica?>">
		<input name="id" type="hidden" value="<?=$id?>">
		<input name="id_soggetto" type="hidden" value="">
		<input name="config_file" type="hidden" value="pe/pe.notifiche.tab"> 
		<input name="azione" type="hidden" value="">
		</form>
<?}}else{
		//$tabella=new tabella_v("provvedimenti_view.tab");
		?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Provvedimenti</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
	
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?
				
				$tabella_sanzioni=new tabella_v("$tabpath/sanzioni",'edit');
				$tabella_ord_sospensione=new tabella_v("$tabpath/ord_sospensione");
				$tabella_ord_demolizione=new tabella_v("$tabpath/ord_demolizione");
				
				$tabella_sanzioni->set_titolo("Sanzioni amministrative","modifica",array("tipo"=>1,"id"=>$tabella_sanzioni->get_campo["id"],"id_provv"=>$tabella_sanzioni->get_campo["id"]));
				
				$tabella_sanzioni->set_dati("pratica=$idpratica");
				$nrec_sosp=$tabella_ord_sospensione->set_dati("pratica=$idpratica and tipo=2;");
				$nrec_dem=$tabella_ord_demolizione->set_dati("pratica=$idpratica and tipo=3;");
				
				$tabella_sanzioni->elenco();
		//Per ogni ordinanza di sospensione creo la tabella  poi scrivo tutti i destinatari delle notifiche
				for ($i=0;$i<$nrec_sosp;$i++){
					$tabella_ord_sospensione->curr_record=$i;
					$tabella_ord_sospensione->set_titolo("Ordinanza di sospensione","modifica",array("tipo"=>2,"id"=>$tabella_ord_sospensioni->get_campo["id"]));
					$tabella_ord_sospensione->get_titolo();
					$tabella_ord_sospensione->tabella();
		//Destinatari notifiche
					$tabellah=new tabella_h("$tabpath/notifiche");
					$num_rec=$tabellah->set_dati("pratica=$idpratica and id_provv=".$tabella_ord_sospensione->get_campo("id"));
					if ($num_rec) $tabellah->elenco();
					
				}
		//idem come sopra
				for ($i=0;$i<$nrec_dem;$i++){
					$tabella_ord_demolizione->curr_record=$i;
					$tabella_ord_demolizione->set_titolo("Ordinanza di demolizione","modifica",array("tipo"=>3,"id"=>$tabella_ord_demolizioni->get_campo["id"]));
					$tabella_ord_demolizione->get_titolo();
					$tabella_ord_demolizione->tabella();
					
					$tabellah=new tabella_h("$tabpath/notifiche");
					$num_rec=$tabellah->set_dati("pratica=$idpratica and id_provv=".$tabella_ord_demolizione->get_campo("id"));
					if ($num_rec) $tabellah->elenco();
				}
				
				?>

			<!-- fine contenuto-->
			 </TD>
	      </TR>
				<!-- se c'e qualche record aggiungo la gestione delle notifiche-->

		  <TR> 
			<TD> 
			<!-- tabella nuovo inserimento-->
				<?	if (!isset($tabellah)) $tabellah=new tabella_h("$tabpath/notifiche");
					$tabellah->set_titolo("Aggiungi nuovo Provvedimento ---> clicca su Nuovo","nuovo",array("mode"=>"tmp_new"));
					print $tabellah->get_titolo();?><BR>
			<!-- fine tabella nuovo inserimento-->
			</TD>
		  </TR>			  
		</TABLE>
<?}?>

</body>
</html>
