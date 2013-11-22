<?php
include_once("login.php");
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$titolo=$_SESSION["TITOLO_$idpratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$azione=$_REQUEST["azione"]; 
if ($azione=="Aggiungi"){ 
	$idrow=$_POST["idriga"];
	$active_form=$_REQUEST["active_form"];
	if($_SESSION["ADD_NEW"]!==$_POST)
		unset($_SESSION["ADD_NEW"]);//serve per non inserire piÃ¹ record con f5
	if (isset($array_dati["errors"])) //sono al ritorno errore
		$Errors=$array_dati["errors"];
	elseif($azione=="Aggiungi")
			//include_once "./db/db.pe.vincoli.php";
			include_once "./db/db.savedata.php";
	$_SESSION["ADD_NEW"]=$_POST;				
}
elseif ($azione=="Elimina"){
	$_POST["id"]=$_POST["idriga"];
	include_once "./db/db.savedata.php";
}

$sqlElencoVincoli="SELECT DISTINCT vincolo.nome_vincolo, COALESCE(vincolo.descrizione, vincolo.nome_vincolo) AS descrizione, vincolo.ordine FROM vincoli.vincolo INNER JOIN vincoli.tavola USING (nome_vincolo) WHERE tavola.pe = 1 ORDER BY vincolo.ordine";

?>
<html>
<head>

<title>Vincoli - <?=$_SESSION[$idpratica]["TITOLO"]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<SCRIPT language="javascript" src="src/http_request.js" type="text/javascript"></SCRIPT>
<SCRIPT language="javascript" src="src/x_core.js" type="text/javascript"></SCRIPT>
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
		document.vincoli.submit();
	}
}

function link(id){
	window.location="pe.scheda_normativa.php?id="+id;
}
	
</script>
</head>
<body  background="" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
<?php
if (($modo=="edit") or ($modo=="new")){
	include_once "./lib/tabella_h.class.php";
	include_once "./lib/tabella_v.class.php";
	$tabellav=new tabella_v("$tabpath/zone_pratica",'new');
	$tabellah=new tabella_h("$tabpath/zone_pratica",'edit');
	$tabellav->set_errors($errors);
	include "./inc/inc.page_header.php";?>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<TR> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Modifica elenco vincoli</H2>
			<!-- fine intestazione-->
			</td>
		  </TR>
		  <TR> 
			<td> 
				<!-- contenuto-->
				<form method="post" name="vincoli" action="pe.vincoli.php">
				
				<input type="hidden" name="idriga" id="idriga" value="0">
				<input name="active_form" type="hidden" value="pe.vincoli.php">
				<input type="hidden" name="mode" value="new">

				<?
				if($Errors){
					$tabellav->set_errors($Errors);
					$tabellav->set_dati($_POST);
				}
				  $tabellav->edita();?>
				</form>
				<!-- fine contenuto-->			
			</td>
		  </TR>
	<?php
		$db=$tabellah->get_db();
		
		$db->sql_query ($sqlElencoVincoli);

		$elenco_vincoli = $db->sql_fetchrowset();
		foreach($elenco_vincoli as $row){
			$vincolo=$row["nome_vincolo"];
			$nome_vincolo=($row["descrizione"])?($row["descrizione"]):($row["nome_vincolo"]);	
			$num_zone=$tabellah->set_dati("pratica=$idpratica and vincolo='$vincolo'");
		?>
		  <TR> 
			<td> 
			<?php
				print ("<b>$nome_vincolo</b>");
				if ($num_zone) 
					$tabellah->elenco();
				else
					print ("<p>L'intervento non risulta soggetto al vincolo</p>");
			?>
			</td>
		  </TR>
<?}// end for?>
	</TABLE>
		
	</FORM>			
		<?php
		
	}//end if

else{
//se non ci sono zone assegnate le assegno qui
//if(NEW_VINCOLI!=1) include_once "db/db.pe.assegna_vincoli.php";
include_once "./lib/tabella_h.class.php";
$tabella_zone=new Tabella_h("$tabpath/zone_pratica"); 
$db=$tabella_zone->get_db();
if(NEW_VINCOLI==1) $db->sql_query ($sqlElencoVincoli);
else $db->sql_query ("select * from pe.e_vincoli order by ordine;");
$elenco_vincoli = $db->sql_fetchrowset();?>
<H2 class="blueBanner">Vincoli presenti</H2>
<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">	
<TR><TD>
<?php
$tabella_zone->set_titolo("Elenco delle zone e dei vincoli","modifica");
$tabella_zone->get_titolo();
foreach($elenco_vincoli as $row){ 
	$vincolo=$row["nome_vincolo"];
	$nome_vincolo=($row["descrizione"])?($row["descrizione"]):($row["nome_vincolo"]);	
	$num_zone=$tabella_zone->set_dati("pratica=$idpratica and vincolo='$vincolo'");
	//$tabella_zone->set_titolo($nome_vincolo);
?>	</TD>
		</TR>
		  <TR> 
			<TD> 
			<!--  intestazione-->
				<?//$tabella_zone->get_titolo();
				print ("<b>$nome_vincolo</b>");
				if ($num_zone) 
						$tabella_zone->elenco();
					else
						print ("<p>L'intervento non risulta soggetto al vincolo</p>");?>
			<!-- fine intestazione-->
			</TD>
		  </TR>
<?php }// end for
echo "</TABLE>";
}//end if
?>
</body>
</html>
