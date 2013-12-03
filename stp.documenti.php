<?php
//print_r($_REQUEST);
include_once("login.php");
include "./lib/tabella_h.class.php";
include "./lib/tabella_v.class.php";
$tabpath="stp";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$id=(isset($_REQUEST["id"]))?($_REQUEST["id"]):(null);
$idpratica=$_REQUEST["pratica"];
$file_config="$tabpath/documenti";

$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

?>
<html>
<head>
    <title>ELENCO DOCUMENTI</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
	
</head>
<body>
<?php
 if (in_array($modo,Array("edit","new"))) {
    
	$tabella=new Tabella_v($file_config,$modo);
    
    if(isset($Errors) && $Errors){
        $tabella->set_errors($Errors);
        $tabella->set_dati($_POST);
        $intestazione="Documento ".$_REQUEST["nome"];
    }
    elseif ($modo=="edit"){	
        $tabella->set_dati("id=$id");
        $intestazione="Documento ".$tabella->array_dati[0]["nome"];
        
    }
    else{
        $tabella->set_dati($_POST);
        $intestazione="Carica nuovo documento di stampa";
    }
	unset($_SESSION["ADD_NEW"]);	
	include "./inc/inc.page_header.php";
?>
<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	<FORM id="" ENCTYPE="multipart/form-data" name="modelli" method="post" action="praticaweb.php" >
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
				<H2 class="blueBanner"><?=$intestazione?></H2>
				<?
				$tabella->edita();?>			  
			</td>
		  </tr>

		</TABLE>
        <input name="active_form" type="hidden" value="stp.documenti.php">				
        <input name="mode" type="hidden" value="<?=$modo?>">
        <input name="id" type="hidden" value="<?=$id?>">
    </FORM>
<?php
}
else{
    $tabella=new Tabella_h("$tabpath/documenti",'list');
    
   
?>
    <H2 class="blueBanner">Elenco dei documenti della pratica</H2><form method="post" name="modelli" action="">
        <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
        
<?php	



		//Visualizzare solo quelli inerenti il form e opzioni 
		$num_doc=$tabella->set_dati("pratica='$idpratica' AND NOT form IN ('cdu.vincoli')");
                $tabella->set_titolo("Elenco Documenti prodotti","nuovo");

		
		?>
                <tr> 
                  <td> 
                  <!--  intestazione-->
                      <?php
                        $tabella->get_titolo();
                          if ($num_doc) 
                              $tabella->elenco();
                          else
                              print ("<p><b>Nessun Documento per questa pratica</b></p>");

                      ?>
                  <!-- fine intestazione-->
                  
                  </td>
                </tr>

                
        </TABLE>
<?php
}
?>
</body>
</html>
