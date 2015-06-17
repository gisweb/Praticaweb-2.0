<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

?>
<html>
<head>
<title>SOSPENSIONI - <?=$_SESSION["TITOLO_".$idpratica]?></title>
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
var msg='Sicuro di voler eliminare definitivamente la sospensione corrente?';
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

$form="sospensioni";
$filetab="$tabpath/sospensioni";
$tabella=new tabella_v($filetab,$modo);
if (($modo=="edit") or ($modo=="new")){
    include "./inc/inc.page_header.php";
    unset($_SESSION["ADD_NEW"]);
    if ($modo=="edit"){
            $id=$_POST["id"];
            $titolo=$_POST["nome_ente"];
            $filtro="id=$id";
    }
    else{            
        $titolo="Inserisci nuova sospensione";
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
    if ($Errors){
        $tabella->set_errors($Errors);
        $tabella->set_dati($_REQUEST);
    }
    elseif($modo=="edit")
        $tabella->set_dati($filtro);
    $tabella->edita();
?>
<!-- fine contenuto-->
                </TD>
            </TR>

        </TABLE>
        <input name="active_form" type="hidden" value="pe.sospensioni.php">
        <input name="mode" type="hidden" value="<?=$modo?>">

    </FORM>	
<?php
}        else{
		
		
		$numrec=$tabella->set_dati("pratica=$idpratica");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <H2 class="blueBanner">Elenco Sospensioni</H2>
    <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
      <TR> 
            <TD> 
            <!-- contenuto-->
<?php
    $tabella->set_titolo("Sospensione","modifica",array("id"=>""));
    for($i=0;$i<$numrec;$i++){
            $tabella->curr_record=$i;
            $tabella->idtabella=$tabella->array_dati[$i]['id'];
            $tabella->get_titolo();
            $tabella->tabella();
            	
    }
?>
            <!-- fine contenuto-->
             </TD>
  </TR>
      <TR> 
            <TD> 
			<!-- tabella nuovo inserimento-->
<?php
        $tabella->set_titolo("Aggiungi una nuovo Sospensione","nuovo");
        $tabella->get_titolo();
        print "<BR>";
	if ($tabella->editable) print($tabella->elenco_stampe());
?>
<!-- fine tabella nuovo inserimento-->
			</TD>
		  </TR>			  
		</TABLE>
<?php
}
?>

</body>
</html>
