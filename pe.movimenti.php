<?php
//Stesso codice che utilizzo in ubicazione.php, progetto.php, asservimento.php
include_once("login.php");
$userid=$_SESSION['USER_ID'];
$tabpath="pe";
include_once "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";
//print_array($_REQUEST);
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]) && $_REQUEST["mode"])?($_REQUEST["mode"]):('view');
$titolo=$_SESSION["TITOLO_$idpratica"];
$azione=(isset($_POST["azione"]) && $_POST['azione'])?($_POST['azione']):(null);
$sqlPermission="(uidins=$userid or $userid = (select resp_proc from pe.avvioproc where pratica=$idpratica))";
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

if ($azione){
	if($_SESSION["ADD_NEW"]!==$_POST){
			unset($_SESSION["ADD_NEW"]);//serve per non inserire pi� record con f5
		$idrow=$_POST["idriga"];
		$active_form=$_REQUEST["active_form"];
		if (isset($array_dati["errors"])) //sono al ritorno errore
			$Errors=$array_dati["errors"];
		else{	
            		include_once "./db/db.savedata.php";
			if ($azione=='Aggiungi'){
				$newid=$_SESSION["ADD_NEW"];
				$sql="UPDATE pe.wf_transizioni SET utente_in=$userid WHERE id=$newid;";
				$dbconn->sql_query($sql);
			}
		}
	}	$_SESSION["ADD_NEW"]=$_POST;
}

?>
<html>
<head>
<title>Movimenti Pratica - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::loadCss();
    utils::loadJS();
?>
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
		$('#assegnazione').submit();
	}
}

</script>
</head>

<body>

<?php
if (in_array($modo,Array("edit","new"))){
		$tab_new="pe/".$_POST["tab_new"].".tab";
		$tab_edit="pe/".$_POST["tab_edit"].".tab";
		$titolo=$_POST["titolo"];	
		include "./inc/inc.page_header.php";
		$tabellav=new tabella_v($tab_new,'new');
		$tabellah=new tabella_h($tab_edit,'edit');
?>
		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner"><?=$titolo?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
			<form method=post id="assegnazione" action="pe.movimenti.php">
				<input type="hidden" name="idriga" id="idriga" value="0">
				<input type="hidden" name="pratica" value=<?=$_REQUEST["pratica"]?>>
				<input type="hidden" name="mode" value="new">
				<input name="active_form" type="hidden" value="pe.movimenti.php">
				<input type="hidden" name="tab_new" value=<?=$_POST["tab_new"]?>>
				<input type="hidden" name="tab_edit" value=<?=$_POST["tab_edit"]?>>
				<input type="hidden" name="titolo" value=<?=$_POST["titolo"]?>>
				<?php
				
				if($Errors){
					//print_array($Errors);
					$tabellav->set_errors($Errors);
					$tabellav->set_dati($_POST);
				}
                                  $tabellav->set_dati(Array("data"=>date('d-m-Y')));  
				  $tabellav->edita();
				  $numrows=$tabellah->set_dati("pratica=$idpratica  and $sqlPermission and not codice in ('ardp','aitec','aiamm','raitec','raiamm','rardp')");
				  if ($numrows)  $tabellah->elenco();	
					?>
			</form>
				<!-- fine contenuto-->
			</td>
		  </tr> 
		</TABLE>
			
		<?php include "./inc/inc.window.php"; // contiene la gesione della finestra popup

}else{// modalità vedi
?>


		<H2 class="blueBanner">Movimenti Pratica</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		<?php
		
		?>
		  <tr> 
			<td> 
			<!--  intestazione-->
			<?php
				$file_tab="movimenti_pratiche";
				$titolo="Movimenti Pratica";
				$tabella=new tabella_h('pe/'.$file_tab,'view');
				//print_array($tabella);
				$tabella->set_titolo($titolo,"modifica",array("titolo"=>$titolo,"tab_new"=>$file_tab,"tab_edit"=>$file_tab,"pratica"=>$idpratica));
				$numrows=$tabella->set_dati("pratica=$idpratica");
				$tabella->get_titolo(null,true);
					if ($numrows)	
						$tabella->elenco();
					else
						print ("<p><b>Nessuna Movimento</b></p>");
			?>
			<!-- fine intestazione-->
			<br>
			</td>
		  </tr>
		
		  
		</table>
<?php
}?>

</body>
</html>
