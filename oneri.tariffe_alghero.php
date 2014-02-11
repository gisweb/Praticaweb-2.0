<?php
include_once("login.php");
include_once "./lib/tabella_v.class.php";
include_once "./lib/tabella_h.class.php";
$titolo=$_SESSION["TITOLO_$idpratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$tabpath="oneri";
if (in_array($_POST["azione"],Array("Salva","Annulla"))) {
	include("./db/db.oneri.tariffe_alghero.php");
	if (!$Errors) 
		$modo="view";
}    
?>
<html>
<head>
<title>Rate - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>
<script LANGUAGE="JavaScript">
function confirmSubmit()
{
var msg='Sicuro di voler eliminare definitivamente la rata corrente?';
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
include "./inc/inc.page_header.php";
if (in_array($modo,Array("edit","new"))){
		
		$id=$_POST["id"];
		$tabella=new tabella_v("$tabpath/tariffe_alghero",$modo);?>	
		
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM id="tariffe_alghero" name="tariffe_alghero" method="post" action="oneri.tariffe_alghero.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Rata</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
			<?if($Errors){
					$tabella->set_errors($Errors);
					$tabella->set_dati($_POST);
				}
				elseif ($modo=="edit"){	
					$tabella->set_dati("id=$id");
				}
				$tabella->edita();?>				
				<!-- fine contenuto-->
			</td>
		  </tr> 
		</TABLE>
		<table>
		<input name="mode" type="hidden" value="<?=$modo?>">
		<input name="active_form" type="hidden" value="oneri.tariffe_alghero.php">
				
		</FORM>			

<?}
elseif($modo=="view"){?>
    <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">
		<TR>
			<TD>
			<?php
                $tabella=new tabella_v("$tabpath/tariffe_alghero",$modo);
				$tabella->set_titolo("Tariffe Oneri",'modifica',Array("mode"=>"edit"));
				$tabella->set_dati("id=$_REQUEST[id]");
				
				$tabella->elenco();?>
				
			</TD>
			
		</TR>
	</TABLE>
<?php   
}
else{
		//-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
?>
		<H2 class="blueBanner">Elenco Tariffe</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?$tabella=new tabella_h("$tabpath/tariffe_alghero","list");
				$nrec=$tabella->set_dati("true");
				$tabella->set_titolo("Tariffe Oneri","nuovo",array("id"=>""));
				$tabella->get_titolo();
                $tabella->elenco();
				
	
					?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
        <button id="btn_new" /><button id="btn_close" />
        <script>
            
        $('#btn_new').button({
              icons:{
                 primary:'ui-icon-circle-plus '
              },
              label:'Nuovo Anno Oneri'
           }).click(function(){
               alert('');
              $.ajax({
                  'url':'./services/xServer.php',
                  'data':{'action':'nuovi_oneri','inizio':'01/01/2012'},
                  'dataType':'JSON',
                  'type':'POST',
                  'success':function(data, textStatus, jqXHR){
                      location.reload();
                  }
                  
              });
           });
       
            
           $('#btn_close').button({
              icons:{
                 primary:'ui-icon-circle-close '
              },
              label:'Chiudi'
           }).click(function(){
              window.opener.focus();
              window.close();
           });
        </script>
<?}?>

</body>
</html>

