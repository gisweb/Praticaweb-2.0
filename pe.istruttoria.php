<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$user=$_SESSION["USER_ID"];
$data=date("d/m/Y");

$pr=new pratica($idpratica);

$filetab="$tabpath/istruttoria";
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

?>
<html>
<head>
<title>Pareri - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>
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
<?php

$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$form="pareri";
if (($modo=="edit") or ($modo=="new")){
    include "./inc/inc.page_header.php";
    unset($_SESSION["ADD_NEW"]);
    //$filetab="$tabpath/istruttoria";
    if ($modo=="edit"){
            $id=$_POST["id"];
            $titolo="Istruttoria";
            $filtro="id=$id";
    }
    else{
            $titolo="Inserisci nuovo parere";
    }

    //aggiungendo un nuovo parere uso pareri_edit che contiene anche l'elenco degli ENTI
    $tabella=new tabella_v($filetab,$modo);
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
		if($modo=="edit")
				$tabella->set_dati($filtro);
		else{
			$dati=Array("istruttore"=>$_REQUEST["istruttore"],"data_ril"=>$_REQUEST["data_ril"]);
			$tabella->set_dati($dati);
		}
		$tabella->edita();
		?>
		<!-- fine contenuto-->
								</TD>
						</TR>

				</TABLE>
			<input name="active_form" type="hidden" value="pe.istruttoria.php">
			<input name="mode" type="hidden" value="<?=$modo?>">	
		</FORM>	
	<?php
        include "./inc/inc.window.php";
		
	}else{
		$tabella=new tabella_v($filetab);
		$tabella->set_errors($errors);
		$numrec=$tabella->set_dati("pratica=$idpratica");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Elenco pareri</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
				if ($numrec==0){
						$tabella->set_titolo("Aggiungi un nuovo Parere","nuovo",Array("istruttore"=>$user,"data_ril"=>$data));
						$tabella->get_titolo();
				}
				else{
						$tabella->set_titolo("Istruttoria","modifica",array("nome_ente"=>"","id"=>""));
						$tabella->get_titolo();
						$tabella->tabella();
				}	
				?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>  
		</TABLE>
<?php
}
?>

</body>
</html>
