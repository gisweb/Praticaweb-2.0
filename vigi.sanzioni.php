<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="vigi";
$filetab=$tabpath.DIRECTORY_SEPARATOR."sanzioni";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$id=$_REQUEST['id'];
$form="sanzioni";
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);
$db=appUtils::getDB();
$sql="SELECT A.*,B.nome FROM vigi.infrazioni A INNER JOIN vigi.e_violazioni B ON(A.tipo=B.id) WHERE pratica = $idpratica;";
$res=$db->fetchAll($sql);
$infrazioni=Array("id"=>0,"opzione"=>"Seleziona ====>");
foreach($res as $val){
	$descrizione = sprintf("Infrazione %s Verbale n° %s del %s",$val["nome"],$val["numero_verbale"],$val["data_verbale"]);
    $infrazioni[]=Array("id"=>$val["id"],"opzione"=>$descrizione);
}

?>
<html>
<head>
<title>Sanzioni - <?=$_SESSION["TITOLO_".$idpratica]?></title>
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
var infrazioni = <?php echo json_encode($infrazioni);?>
</script>
</head>
<body>
<?php

if (($modo=="edit") or ($modo=="new")){
        include "./inc/inc.page_header.php";
        unset($_SESSION["ADD_NEW"]);
        if ($modo=="edit"){
            $filtro="id=$id";
            $titolo="";
        }
        else{
            $titolo="Inserisci nuova sanzione";
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
                <TD>
						<!-- contenuto-->
		<?php
		if($Errors){
                    $tabella->set_errors($Errors);
                    $tabella->set_dati($_POST);
                    $titolo="";
                }
                elseif ($modo=="edit"){	
                   $tabella->set_dati($filtro);
                   $titolo="";
                }
                $tabella->edita();
		?>
		<!-- fine contenuto-->
                </TD>
            </TR>
        </TABLE>
		<input name="active_form" type="hidden" value="vigi.<?php echo $form;?>.php">
		<input name="mode" type="hidden" value="<?=$modo?>">
                <input name="vigi" type="hidden" value="1">
                <input name="pratica" type="hidden" value="<?php echo $idpratica;?>">

		</FORM>	
	<?php
        include "./inc/inc.window.php";
		
	}else{
		$tabella=new tabella_v($filetab);
		$tabella->set_errors($errors);
		$numrec=$tabella->set_dati("pratica=$idpratica;");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Elenco Sanzioni</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
		<?php
                    $tabella->set_titolo("Sanzione","modifica",array("tipo_sanzione"=>"","id"=>""));
                    for($i=0;$i<$numrec;$i++){
                            $tabella->curr_record=$i;
                            $tabella->idtabella=$tabella->array_dati[$i]['id'];
                            $tabella->get_titolo();
                            $tabella->tabella();	
                    }
		print "</td></tr><tr><td>";
                
                $tabella->set_titolo("Aggiungi una nuova Sanzione","nuovo");
                $tabella->get_titolo();
                print "<BR>";
		if ($tabella->editable) print($tabella->elenco_stampe());
               
                print "</td></tr></table>";
    }
?>

</body>
</html>
