<?
//Stesso codice che utilizzo in ubicazione.php, progetto.php, asservimento.php
include_once("login.php");
$tabpath="pe";
include_once "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";

$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]) && $_REQUEST["mode"])?($_REQUEST["mode"]):('view');
$titolo=$_SESSION["TITOLO_$idpratica"];
$azione=(isset($_POST["azione"]) && $_POST['azione'])?($_POST['azione']):(null);
if ($azione){
	if($_SESSION["ADD_NEW"]!==$_POST){
			unset($_SESSION["ADD_NEW"]);//serve per non inserire più record con f5
		$idrow=$_POST["idriga"];
		$active_form=$_REQUEST["active_form"];
		if (isset($array_dati["errors"])) //sono al ritorno errore
			$Errors=$array_dati["errors"];
		else{
			$sql="SELECT foglio,mappale from pe.cterreni where id=$idrow;";
			$dbconn->sql_query($sql);
			$partic=$dbconn->sql_fetchrow();
			
            include_once "./db/db.savedata.php";
			if($_REQUEST['config_file']=='pe/catasto_terreni.tab') 
                include_once "db/db.pe.assegna_vincoli.php";   //aggiunto per aggiungere o eliminare vincoli dei mappali
			
		}
	}	$_SESSION["ADD_NEW"]=$_POST;
}

?>
<html>
<head>
<title>Ubicazione - <?=$_SESSION[$idpratica]["TITOLO"]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language=javascript>
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
		$('#ubicazione').submit();
	}
}

</script>
</head>

<body>

<?
if (($modo=="edit") or ($modo=="new")){
		$tab_new=$_POST["tab_new"].".tab";
		$tab_edit=$_POST["tab_edit"].".tab";
		$titolo=$_POST["titolo"];	
		include "./inc/inc.page_header.php";
		$tabellav=new tabella_v($tab_new,'new');
		$tabellah=new tabella_h($tab_edit,'edit');
?>
		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner"><?=$_SESSION[$idpratica]["TITOLO"]?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
			<form method=post id="ubicazione" action="pe.ubicazione.php">
				<input type="hidden" name="idriga" id="idriga" value="0">
				<input type="hidden" name="mode" value="new">
				<input name="active_form" type="hidden" value="pe.ubicazione.php">
				<input type="hidden" name="tab_new" value=<?=$_POST["tab_new"]?>>
				<input type="hidden" name="tab_edit" value=<?=$_POST["tab_edit"]?>>
				<input type="hidden" name="titolo" value=<?=$_POST["titolo"]?>>
				<?
				
				
				if($Errors){
					$tabellav->set_errors($Errors);
					$tabellav->set_dati($_POST);
				}
				  $tabellav->edita();
				  $numrows=$tabellah->set_dati("pratica=$idpratica");
				  if ($numrows)  $tabellah->elenco();?>
			</form>
				<!-- fine contenuto-->
			</td>
		  </tr> 
		</TABLE>
			
		<?php include "./inc/inc.window.php"; // contiene la gesione della finestra popup

}else{// modalità vedi
?>


		<H2 class="blueBanner">Ubicazione dell'intervento</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		<?$array_file_tab=array("$tabpath/indirizzi","$tabpath/catasto_terreni","$tabpath/catasto_urbano");
		$array_titolo=array("Indirizzi","Catasto Terreni","Catasto Urbano");
		for($i=0;$i<3;$i++){
			  $file_tab=$array_file_tab[$i];
			  $titolo=$array_titolo[$i];?>
		  <tr> 
			<td> 
			<!--  intestazione-->
			<?$tabella=new Tabella_h($file_tab);
			  $tabella->set_titolo($titolo,"modifica",array("titolo"=>$titolo,"tab_new"=>$file_tab,"tab_edit"=>$file_tab));
			  $numrows=$tabella->set_dati("pratica=$idpratica;");
			  $tabella->get_titolo();
				  if ($numrows)	
					$tabella->elenco();
				else
					print ("<p><b>Elenco vuoto</b></p>");
			?>
			<!-- fine intestazione-->
			<br>
			</td>
		  </tr>
		<?}?>
		  
		</table>
<?}?>

</body>
</html>
