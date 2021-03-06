<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="vigi";
$filetab=$tabpath.DIRECTORY_SEPARATOR."ordinanze";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$id=$_REQUEST['id'];
$form="ordinanze";
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
<title>Ordinanze - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Tyvigi" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::loadCss();
    utils::loadJS();
?>

<script LANGUAGE="JavaScript">
var infrazioni = <?php echo json_encode($infrazioni);?>

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
        $titolo=sprintf("Ordinanza di %s del %s",$tabella->array_dati[$i]['tipo'],$tabella->array_dati[$i]['data_ordinanza']);
    }
    elseif ($modo=="edit"){	
        $filtro="id=$id";
        $tabella->set_dati($filtro);
        $titolo=sprintf("Ordinanza di %s del %s",$tabella->array_dati[0]['tipo'],$tabella->array_dati[0]['data_ordinanza']);
    }
    else{
        $titolo="Inserisci nuova ordinanza";
    }
?>	
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
		$numrec=$tabella->set_dati("pratica=$idpratica");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Elenco Ordinanze</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
		<?php
                    
                    for($i=0;$i<$numrec;$i++){
                        $titolo=sprintf("Ordinanza di %s del %s",$tabella->array_dati[$i]['tipo'],$tabella->array_dati[$i]['data_ordinanza']);
                        $tabella->set_titolo($titolo,"modifica",array("id"=>""));
                        $tabella->curr_record=$i;
                        $tabella->idtabella=$tabella->array_dati[$i]['id'];
                        $tabella->get_titolo();
                        $tabella->tabella();	
                    }
		print "</td></tr><tr><td>";
                
                $tabella->set_titolo("Aggiungi una nuova Ordinanza","nuovo");
                $tabella->get_titolo();
                print "<BR>";
		if ($tabella->editable) print($tabella->elenco_stampe());
               
                print "</td></tr></table>";
    }
?>

</body>
</html>
