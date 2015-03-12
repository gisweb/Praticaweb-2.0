<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$id=$_REQUEST["id"];
?>
<html>
<head>
<title>Annotazioni - <?=$_SESSION["TITOLO_".$idpratica]?></title>
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


if (($modo=="edit") or ($modo=="new")){
    include "./inc/inc.page_header.php";
    unset($_SESSION["ADD_NEW"]);
    $filetab="$tabpath/annotazioni";
    $tabella=new tabella_v($filetab,$modo);

    if ($modo=="edit"){
            $titolo="Nota";
            $filetab="$tabpath/annotazioni";
            $tabella->set_dati("id=$id");
    }
    else{  
            $titolo="Inserisci nuova Nota";
            $tabella->set_dati($_POST);
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
        <input name="active_form" type="hidden" value="pe.annotazioni.php">
        <input name="mode" type="hidden" value="<?=$modo?>">

    </FORM>	
<?php
        include "./inc/inc.window.php";
		
	}else{
		$tabella=new tabella_h("$tabpath/annotazioni","list");
		
		$numrec=$tabella->set_dati("pratica=$idpratica");?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <H2 class="blueBanner">Annotazioni</H2>
    <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
      <TR> 
            <TD> 
			<!-- tabella nuovo inserimento-->
<?php
        $tabella->set_titolo("Aggiungi una nuova Nota","nuovo",Array("utente"=>$_SESSION["USER_ID"]));
        $tabella->get_titolo();
        if ($tabella->num_record) 
            $tabella->elenco();
        else
            print ("<p><b>Nessuna Nota Salvata</b></p>");
        print "<BR>";
	//if ($tabella->editable) print($tabella->elenco_stampe());
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
