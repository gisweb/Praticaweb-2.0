<?php
require_once "../../login.php";

$tabpath="vigi";
$filetab=$tabpath.DIRECTORY_SEPARATOR."infrazioni";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$id=$_REQUEST['id'];
$form="infrazioni";
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

?>
<html>
<head>
<title>Abusi - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Tyvigi" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::loadCss();
    utils::loadJS();
?>

<script LANGUAGE="JavaScript">
function confirmSubmit()
{
var msg="Sicuro di voler eliminare definitivamente l'infrazione corrente?";
var agree=confirm(msg);
if (agree)
	return true ;
else
	return false ;
}

</script>
</head>
<body>
<?php

if (($modo=="edit") or ($modo=="new")){
    include "./inc/inc.page_header.php";
    unset($_SESSION["ADD_NEW"]);
    $tabella=new tabella_v($filetab,$modo);
    if($Errors){
        $tabella->set_errors($Errors);
        $tabella->set_dati($_POST);
        $titolo="Infrazione";
    }
    elseif ($modo=="edit"){	
        $filtro="id=$id";
        $tabella->set_dati($filtro);
        $titolo="Infrazione";
    }
    else{
        $titolo="Inserisci nuova Infrazione";
    }
?>	
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <FORM height=0 method="post" action="/praticaweb.php">
        <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
            <TR> <!-- intestazione-->
                <TD><H2 class="blueBanner"><?=$titolo?></H2></TD>
            </TR> 
            <TR>
                <TD>
						<!-- contenuto-->
		<?php
		
                $tabella->edita();
		?>
		<!-- fine contenuto-->
                </TD>
            </TR>
        </TABLE>
		<input name="active_form" type="hidden" value="vigi.<?php echo $form;?>.php">
		<input name="mode" type="hidden" value="<?=$modo?>">
		<input name="pratica" type="hidden" value="<?php echo $idpratica;?>">
		<input name="vigi" type="hidden" value="1">

		</FORM>	
	<?php
        include "./inc/inc.window.php";
		
	}else{
		$tabella=new tabella_v($filetab);
		$tabella->set_errors($errors);
		$numrec=$tabella->set_dati("pratica=$idpratica");
		?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Elenco delle Infrazioni</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
		<?php
                    
                    for($i=0;$i<$numrec;$i++){
                        $titolo=sprintf("Infrazioni");
                        $tabella->set_titolo($titolo,"modifica",array("id"=>""));
                        $tabella->curr_record=$i;
                        $tabella->idtabella=$tabella->array_dati[$i]['id'];
                        $tabella->get_titolo();
                        $tabella->tabella();	
                    }
		print "</td></tr><tr><td>";
                
                $tabella->set_titolo("Aggiungi una nuova Infrazione","nuovo");
                $tabella->get_titolo();
                print "<BR>";
		if ($tabella->editable) print($tabella->elenco_stampe());
               
                print "</td></tr></table>";
    }
?>

</body>
</html>
