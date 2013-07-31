<?
include_once("./login.php");

$errors=null;
include "./lib/tabella_v.class.php";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$id=$_SESSION["USER_ID"];
$tabpath="admin";

include "db/db.admin.pwd.php";
$file_config="pwd";
switch ($modo) {
	case "edit" :
		$tit="Modifica Password Utente ".$_SESSION["NOMINATIVO"];
		break;
	case "view" :
		$tit="Visualizzazione dei Dati di Registrazione";
		break;
}
?>
<html>
<head>
<title>Gestione Password di PraticaWeb</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>


</head>
<body>

<?include "./inc/inc.page_header.php";?>
<H2 class="blueBanner"><?=$tit?></H2>
<?
   
	if ($modo=="edit"){
		$tabella=new tabella_v("$tabpath/$file_config.tab",$modo);

		?>	
		
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM id="utenti" name="utenti" method="post" action="admin.pwd.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="75%">		
				  
		<tr> 
			<td> 
				<!-- contenuto-->
				<?php
				  if ($id)	{
					 if ($errors)
						$tabella->set_errors($errors);
					 if (!count($errors)) $tabella->set_dati(Array("nominativo"=>$_SESSION["NOMINATIVO"]));
					 else
						$tabella->set_dati(Array("nominativo"=>$_SESSION["NOMINATIVO"]));
				}
				$tabella->edita();?>
				<!-- fine contenuto-->
			</td>
		  </tr>
		    
		</TABLE>

   <input name="active_form" type="hidden" value="admin.pwd.php">
   <input name="mode" type="hidden" value="<?=$_REQUEST["mode"]?>">
   <input name="id" type="hidden" value="<?=$id?>">
			
   </FORM>

		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA   >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
<?
}
elseif($modo=="view") {
	$tabella=new tabella_v("$tabpath/$file_config.tab");
	
?>
   
	  <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="75%">				  
		 <tr> 
			<td> 
				<!-- contenuto-->
			<?					
				$tabella->set_titolo("Modifica Password dell'Utente ".$_SESSION["NOMINATIVO"],"modifica",array("id"=>$id));
				$tabella->get_titolo();
				$tabella->set_dati(Array("nominativo"=>$_SESSION["NOMINATIVO"]));
				$tabella->tabella();

			?>
				<!-- fine contenuto-->
			</td>
		  </tr>
		</TABLE>
<button id="btn_close" />
   <script>
	  $('#btn_close').button({
		 icons:{
			primary:'ui-icon-circle-close '
		 },
		 label:'Chiudi'
	  }).click(function(){
		 closeWindow();
	  });
   </script>
   
<?php }?>
</body>
</html>
