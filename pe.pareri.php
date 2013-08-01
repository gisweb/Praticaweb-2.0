<?
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
?>
<html>
<head>
<title>Pareri - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<SCRIPT language="javascript" src="src/http_request.js" type="text/javascript"></SCRIPT>

<script LANGUAGE="JavaScript">
function confirmSubmit()
{
var msg='Sicuro di voler eliminare definitivamente il parere corrente?';
var agree=confirm(msg);
if (agree)
	return true ;
else
	return false ;
}

</script>
</head>
<body  background="">
<?


$form="pareri";
if (($modo=="edit") or ($modo=="new")){
		include "./inc/inc.page_header.php";
		unset($_SESSION["ADD_NEW"]);
		if ($modo=="edit"){
			$id=$_POST["id"];
			$titolo=$_POST["nome_ente"];
			$filetab="$tabpath/pareri";
			$filtro="id=$id";
		}
		else{
			$filetab="$tabpath/pareri";
			$titolo="Inserisci nuovo parere";
		}

		//aggiungendo un nuovo parere uso pareri_edit che contiene anche l'elenco degli ENTI
		$tabella=new tabella_v($filetab,$modo);?>	
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM height=0 method="post" action="praticaweb.php">
				<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
						<TR> <!-- intestazione-->
								<TD><H2 class="blueBanner"><?=$titolo?></H2></TD>
						</TR> 
						<TR>
								<td>
						<!-- contenuto-->
		<?php
		if($modo=="edit")
				$tabella->set_dati($filtro);
        
		$tabella->edita();
		?>
		<!-- fine contenuto-->
								</TD>
						</TR>

				</TABLE>
		<input name="active_form" type="hidden" value="pe.pareri.php">
		<input name="mode" type="hidden" value="<?=$modo?>">

		</FORM>	
	<?include "./inc/inc.window.php";
		
	}else{
		$tabella=new tabella_v("$tabpath/pareri");
		$tabella->set_errors($errors);
		$numrec=$tabella->set_dati("pratica=$idpratica and ente<>1");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Elenco pareri</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?$tabella->set_titolo("nome_ente","modifica",array("nome_ente"=>"","id"=>""));
				for($i=0;$i<$numrec;$i++){
					$tabella->curr_record=$i;
					$tabella->idtabella=$tabella->array_dati[$i]['id'];
					$tabella->get_titolo();
					$tabella->tabella();
					//$tabella->elenco_stampe($form);	
				}
					?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		  <TR> 
			<TD> 
			<!-- tabella nuovo inserimento-->
				<?php
                $tabella->set_titolo("Aggiungi un nuovo Parere","nuovo");
                $tabella->get_titolo();
                print "<BR>";
				if ($tabella->editable) print($tabella->elenco_stampe("pe.pareri"));
                ?>
			<!-- fine tabella nuovo inserimento-->
			</TD>
		  </TR>			  
		</TABLE>
<?}?>

</body>
</html>
