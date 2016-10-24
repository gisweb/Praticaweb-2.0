<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$filetab="$tabpath/pareri";
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

?>
<html>
<head>
<title>Pareri - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::loadCss();
    utils::loadJS();
?>

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
<?php


$form="pareri";
if (($modo=="edit") or ($modo=="new")){
		include "./inc/inc.page_header.php";
		unset($_SESSION["ADD_NEW"]);
                $tabella=new tabella_v($filetab,$modo);
		if ($modo=="edit"){
			$id=$_POST["id"];
			$filtro="id=$id";
                        $tabella->set_dati($filtro);
                        $db=appUtils::getDB();
                        $titolo=$db->fetchColumn('SELECT nome FROM pe.e_enti inner join pe.pareri on(pareri.ente=e_enti.id) WHERE pareri.id=?',Array($id),0);
		}
		else{
			$titolo="Inserisci nuovo parere";
		}

		//aggiungendo un nuovo parere uso pareri_edit che contiene anche l'elenco degli ENTI
		?>	
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

        
		$tabella->edita();
		?>
		<!-- fine contenuto-->
								</TD>
						</TR>

				</TABLE>
		<input name="active_form" type="hidden" value="pe.pareri.php">
		<input name="mode" type="hidden" value="<?=$modo?>">

		</FORM>	
	<?php
        include "./inc/inc.window.php";
		
	}else{
		$tabella=new tabella_v("$tabpath/pareri");
		$tabella->set_errors($errors);
		$numrec=$tabella->set_dati("pratica=$idpratica");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Elenco pareri</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
			<?php
                                $tabella->set_titolo("nome_ente","modifica",array("nome_ente"=>"","id"=>""));
				for($i=0;$i<$numrec;$i++){
					$tabella->curr_record=$i;
					$tabella->idtabella=$tabella->array_dati[$i]['id'];
					$tabella->get_titolo();
					$tabella->tabella();
					//$tabella->elenco_stampe($form);	
				}
		print "</td></tr><tr><td>";
                
                $tabella->set_titolo("Aggiungi un nuovo Parere","nuovo");
                $tabella->get_titolo();
                print "<BR>";
		if ($tabella->editable) print($tabella->elenco_stampe());
               
                print "</td></tr></table>";
    }
?>

</body>
</html>
