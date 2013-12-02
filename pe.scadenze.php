<?php
include_once("login.php");
require_once APPS_DIR."lib/tabella_h.class.php";
require_once APPS_DIR."lib/tabella_v.class.php";
$tabpath="pe";

$idpratica=$_REQUEST["pratica"];
$id=$_REQUEST["id"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('list');
$today=date('j-m-y'); 
$filetab="$tabpath/scadenze";
?>

<html>
<head>
<title>Scadenze - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body>
<?php
if(in_array($modo,Array("edit","new"))){
//-<<<<<<<<<<<<<<<<<<<<<< VISUALIZZA ITER >>>>>>>>>>>>>>>>>>>>>>>>>>>----------------------->	
    include "./inc/inc.page_header.php";
    unset($_SESSION["ADD_NEW"]);		
    $tabella=new tabella_v($filetab,$modo);
    if($Errors){
        $tabella->set_errors($Errors);
        $tabella->set_dati($_POST);
        $titolo="";
    }
    elseif ($modo=="edit"){	
        $tabella->set_dati("id=$id");
    }
    ?>	
    <!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <FORM height=0 method="post" action="praticaweb.php">
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		 
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
		<input name="active_form" type="hidden" value="pe.scadenze.php">
		<input name="mode" type="hidden" value="<?=$modo?>">

		</FORM>	
<?php
include "./inc/inc.window.php";
}
else{
    
    $tabella=new tabella_h("$tabpath/scadenze","list");
    $tabella->set_titolo("Elenco degle Scadenze","nuovo");
    $tabella->set_dati("pratica=$idpratica");
?>    
    <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
        <TR> 
            <TD> 
<?php
    $tabella->get_titolo();
    if ($tabella->num_record) 
            $tabella->elenco();
    else{
        print ("<p><b>Nessun Scadenza Impostata</b></p>");
    }
    
?>
            </TD>
        </TR>
    </TABLE>
<?php        
}
?>
</body>
</html>
