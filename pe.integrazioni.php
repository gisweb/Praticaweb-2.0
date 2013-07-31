<?
include_once("login.php");
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";

$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]) && $_REQUEST["mode"])?($_REQUEST["mode"]):('view');

$form="integrazioni";
$titolo=$_SESSION["TITOLO_$idpratica"];
?>
<html>
<head>

<title>Integrazione - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body  background="" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
<?
if (($modo=="edit") or ($modo=="new")) {
	$_SESSION["ADD_NEW"]=0;
	$id_integrazione=(isset($_POST["id"]) && $_POST["id"])?($_POST["id"]):(null);
	$nomeiter=$_POST["nomeiter"];
	$iter=$_POST["iter"];
	$tabella_integrazione=new tabella_v("$tabpath/integrazioni.tab",$modo);
	$tabella_integrati=new tabella_h("$tabpath/doc_integrati.tab",$modo);
	$tabella_mancanti=new tabella_h("$tabpath/doc_integrati.tab",$modo);
	$num_integrati=$tabella_integrati->set_dati("integrazione=$id_integrazione and iter=$iter and mancante=0");
	$num_mancanti=$tabella_mancanti->set_dati("pratica=$idpratica and iter=$iter and mancante=1");
	//$tabella_integrati->set_titolo("Documenti integrati o sostituiti");
	//$tabella_mancanti->set_titolo("Documenti mancanti");
	$tabella_mancanti->set_color("#E7EFFF","#FF0000",0,0);
	include "./inc/inc.page_header.php";
	//-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	?>
	<FORM id="" name="" method="post" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Integrazione documenti fase: <?=$nomeiter?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?//$tabella->set_errors($errors);
				$tabella_integrazione->set_dati("id=$id_integrazione");
				$tabella_integrazione->edita();
				if ($num_integrati){
					//$tabella_integrati->get_titolo();
					print ("<p><b>Documenti integrati o sostituiti</b></p>");
					$tabella_integrati->elenco();
				}
				if ($num_mancanti){
					//$tabella_mancanti->get_titolo();
					print ("<p><b>Documenti mancanti</b></p>");
					$tabella_mancanti->elenco();
				}?>			  
			</td>
		  </tr>
		</TABLE>
		<input name="active_form" type="hidden" value="pe.integrazioni.php">
		<input name="integrazione" type="hidden" value="<?=$id_integrazione?>">				
		<input name="iter" type="hidden" value="<?=$iter?>">				
		<input name="mode" type="hidden" value="<?=$modo?>">	
	</FORM>
		
<?}else{
//   -- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
$tabella_integrazione=new tabella_v("$tabpath/integrazioni.tab");
$tabella_integrati=new Tabella_h("$tabpath/doc_integrati.tab");
$tabella_mancanti=new Tabella_h("$tabpath/doc_mancanti.tab");
$tabella_mancanti->set_color("#E7EFFF","#FF0000",0,0);
$db=$tabella_integrati->get_db();
$db->sql_query ("select * from pe.e_iter order by ordine;");
$elenco_iter = $db->sql_fetchrowset();?>

<H2 class="blueBanner">Elenco documenti allegati alla pratica</H2>
<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
 <tr> 
  <td> 
<?
$flag=0;
foreach ($elenco_iter as $row){
	$iter=$row["id"];
	$nomeiter=$row["nome"];
	//cerco i mancanti se esistono gli integrati metto integrazione in modifica, se non esistono gli integrati metto nuova integrazione
	$num_integrazioni=$tabella_integrazione->set_dati("pratica=$idpratica and iter=$iter order by id");
	$num_mancanti=$tabella_mancanti->set_dati("pratica=$idpratica and iter=$iter and mancante=1");
	if ($num_mancanti or $num_integrazioni) $flag=1;
	for($i=0;$i<$num_integrazioni;$i++){
		$tabella_integrazione->curr_record=$i;
		$data=$tabella_integrazione->get_data("data_integ");
		$tabella_integrazione->set_titolo("$data Integrazione documenti $nomeiter","modifica",array("iter"=>$iter,"nomeiter"=>$nomeiter,"id"=>""));
		$tabella_integrazione->get_titolo();
		$tabella_integrazione->tabella();
		$id_integrazione=$tabella_integrazione->get_campo("id");
		$num_integrati=$tabella_integrati->set_dati("integrazione=$id_integrazione and mancante=0");
		//$tabella_integrazione->elenco_stampe($form);		
		if ($num_integrati) 
			$tabella_integrati->elenco();
		echo("<br>");
	}
	if ($num_mancanti){
		$tabella_integrazione->set_titolo("Aggiungi nuova Integrazione $nomeiter","nuovo",array("iter"=>$iter,"nomeiter"=>$nomeiter));
		$tabella_integrazione->get_titolo();
		$tabella_mancanti->elenco();
		echo("<br>");
	}	
}
if (!$flag) echo "<p><b>Nessun documento da integrare</b></p>";?>
  </td>
 </tr>
</TABLE>
<?
print $tabella_integrazione->elenco_stampe("pe.allegati");
}//end if?>
</body>
</html>
