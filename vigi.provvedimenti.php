<?
include_once("login.php");

$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$idpratica=$_REQUEST["pratica"];

if (isset($_POST["id_soggetto"])){
	include "db/db.vigi.notifiche.php";
	$modo="edit";
	
}

$tabpath="vigi";
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
if ($modo=="edit" or $modo=="new"){
		if ($modo=='new') unset($_SESSION["ADD_NEW"]);
		include "./inc/inc.page_header.php";
		//$idpratica=$_POST["pratica"];
		$id=$_POST["id"];
		$id_tipo=$_POST["tipo"];
		//Carico titoli e form diversi a seconda del tipo di provvedimento
		switch ($id_tipo) {
			case 1:
				$config_file="sanzioni.tab";
				$titolo="Sanzione amministrativa";
				break;
			case 2:
				$config_file="ord_sospensione.tab";
				$titolo="Ordinanza di sospensione dei lavori";
				break;
			case 3:
				$config_file="ord_demolizione.tab";
				$titolo="Ordinanza di demolizione";
				break;
		}
		//echo ""
		$config_file="$tabpath/$config_file";
		$tabella=new tabella_v($config_file,$modo);
		$tabella->set_titolo($titolo);
		?>
		<FORM id="" name="" method="post" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
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
		</TABLE>
		<input name="active_form" type="hidden" value="vigi.provvedimenti.php">				
		<input name="mode" type="hidden" value="<?=$_POST["mode"]?>">
		<input name="tipo" type="hidden" value="<?=$id_tipo?>">

		</form>
<?}else{
		//$tabella=new tabella_v("provvedimenti_view.tab");
		?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Provvedimenti</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
	
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?
				
				$tabella_sanzioni=new tabella_v("$tabpath/sanzioni",$modo);
				$tabella_ord_sospensione=new tabella_v("$tabpath/ord_sospensione",$modo);
				$tabella_ord_demolizione=new tabella_v("$tabpath/ord_demolizione",$modo);
				
				$tabella_sanzioni->set_titolo("Sanzioni amministrative","modifica",array("tipo"=>1,"id"=>$tabella_sanzioni->get_campo["id"],"id_provv"=>$tabella_sanzioni->get_campo["id"]));
				
				$nrec_sanz=$tabella_sanzioni->set_dati("pratica=$idpratica");
				$nrec_sosp=$tabella_ord_sospensione->set_dati("pratica=$idpratica and tipo=2;");
				$nrec_dem=$tabella_ord_demolizione->set_dati("pratica=$idpratica and tipo=3;");
				
				$tabella_sanzioni->elenco();
				$tabella_sanzioni->set_titolo("Nuova Sanzione amministrativa","nuovo",array("tipo"=>1));
				$tabella_sanzioni->get_titolo();
				if (!$nrec_sanz) echo "<p><b>Nessuna Sanzione Amministrativa</b></p>";
				print '<div class="button_line"></div>';
		//Per ogni ordinanza di sospensione creo la tabella  poi scrivo tutti i destinatari delle notifiche
				for ($i=0;$i<$nrec_sosp;$i++){
					$tabella_ord_sospensione->curr_record=$i;
					$tabella_ord_sospensione->set_titolo("Ordinanza di sospensione","modifica",array("tipo"=>2,"id"=>$tabella_ord_sospensioni->get_campo["id"]));
					$tabella_ord_sospensione->get_titolo();
					$tabella_ord_sospensione->tabella();
				
		//Destinatari notifiche
					//$tabellah=new tabella_h("$tabpath/notifiche.tab");
					//$num_rec=$tabellah->set_dati("pratica=$idpratica and id_provv=".$tabella_ord_sospensione->get_campo("id"));
					//if ($num_rec) $tabellah->elenco();
					
				}
				$tabella_ord_sospensione->set_titolo("Nuova Ordinanza di sospensione","nuovo",array("tipo"=>2));
				$tabella_ord_sospensione->get_titolo();
				if (!$nrec_sosp) echo "<p><b>Nessuna Ordinanza di Sospensione</b></p>";
				print '<div class="button_line"></div>';
		//idem come sopra
				for ($i=0;$i<$nrec_dem;$i++){
					$tabella_ord_demolizione->curr_record=$i;
					$tabella_ord_demolizione->set_titolo("Ordinanza di demolizione","modifica",array("tipo"=>3,"id"=>$tabella_ord_demolizioni->get_campo["id"]));
					$tabella_ord_demolizione->get_titolo();
					$tabella_ord_demolizione->tabella();
					
					//$tabellah=new tabella_h("$tabpath/notifiche.tab");
					//$num_rec=$tabellah->set_dati("pratica=$idpratica and id_provv=".$tabella_ord_demolizione->get_campo("id"));
					//if ($num_rec) $tabellah->elenco();
				}
				$tabella_ord_demolizione->set_titolo("Nuova Ordinanza di Demolizione","nuovo",array("tipo"=>3));
				$tabella_ord_demolizione->get_titolo();
				if (!$nrec_dem) echo "<p><b>Nessuna Ordinanza di Demolizione</b></p>";
				?>

			<!-- fine contenuto-->
			 </TD>
	      </TR>
				<!-- se c'e qualche record aggiungo la gestione delle notifiche-->

		  		  
		</TABLE>
<?}?>

</body>
</html>
