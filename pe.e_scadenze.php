<?php
include_once("login.php");

$Errors=null;
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$id=(isset($_REQUEST["id"]))?($_REQUEST["id"]):('');
$tabpath="pe";
$formaction="pe.e_scadenze.php";
include "db/db.pe.e_scadenze.php";
$file_config="e_scadenze.tab";
switch ($modo) {
	case "new" :
		$tit="Inserimento nuova Scadenza";
		break;
	case "edit" :
		$tit="Modifica dati scadenza";
		break;
	case "view" :
		$tit="Dettagli sulla scadenza";
		break;
	default :
		$tit="Elenco scadenze";
		break;
}
?>
<html>
<head>
    <title>Elenco Scadenze</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
    <SCRIPT language="javascript" type="text/javascript">
        function confirmSubmit(){
            return confirm('Sei sicuro di voler eliminare questo stato?');
        }
    </SCRIPT>

    </head>
    <body>
<?php 
include "./inc/inc.page_header.php";
?>
<H2 class="blueBanner"><?php echo "$tit";?></H2>
<?
	if (($modo=="edit") or ($modo=="new")){
		$tabella=new tabella_v("$tabpath/$file_config",$modo);
		unset($_SESSION["ADD_NEW"]);
		?>	
		<FORM id="stati" name="utenti" method="post" action="<?php echo $formaction; ?>">
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="75%">		
				  
		<tr> 
			<td> 
				<!-- contenuto-->
				<?php
                  
				  if ($id)	{
                     //print_array($Errors);
					 if ($Errors)
						$tabella->set_errors($Errors);
					 if (!count($Errors)) $tabella->set_dati("id=$id");
					 else
						$tabella->set_dati($_POST);
				}
				$tabella->edita();?>
				<!-- fine contenuto-->
			</td>
		  </tr> 
		</TABLE>
		<input name="active_form" type="hidden" value="pe.e_enti.php">
        <input name="mode" type="hidden" value="<?=$_POST["mode"]?>">
        <input name="id" type="hidden" value="<?=$id?>">
		</FORM>		

		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA   >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
<?
}elseif($modo=="view") {
		$tabella=new Tabella_v("$tabpath/$file_config",$modo);
		
		?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<script language="javascript">
			$(document).ready(function(){
				$('#btn_vai').button({
					label:'Modifica le Scadenze',
					icons:{}
				}).bind('click',function(event){
					event.preventDefault();
					linkToList('pe.e_tipopratica_scadenze.php',{codice:$('#codice').val()});
				});
			});
		</script>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
			<TR> 
				<TD> 
				<!-- contenuto-->
			  <?$tabella->set_titolo("Scadenza","modifica",Array("id"=>$id));
				$tabella->set_dati("id=".$id);
				$tabella->get_titolo();				
				$tabella->tabella();
			  ?>			
				</TD>
			</TR>
		</TABLE>
		
<?
print "<input id='codice' type='hidden' value='".$tabella->array_dati[0]["codice"]."'/>";
}
else {
	$tabella=new Tabella_h("$tabpath/$file_config",'list');
	$tabella->set_titolo("Elenco degle Scadenze","nuovo");
	$tabella->set_dati();
	
	?>
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		<TR> 
			<TD> 
				
				<?php
                $tabella->get_titolo();
				$tabella->elenco();?>
			</TD>
		</TR>
	</TABLE>
   <button id="btn_close" />
   <script>
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
	<?
	}?>
	
</body>
</html>
