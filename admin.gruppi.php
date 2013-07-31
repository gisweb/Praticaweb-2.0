<?
include_once("./login.php");

if ($_SESSION["PERMESSI"] > 2){ 
   include_once HOME;
   exit;
}
$errors=null;
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$id=(isset($_REQUEST["id"]))?($_REQUEST["id"]):('');
$tabpath="admin";
$formaction="admin.gruppi.php";
include "db/db.admin.gruppi.php";
$id=($id)?($id):($_SESSION['ADD_NEW']);
switch ($modo) {
	case "new" :
		$file_config="gruppi";
		$tit="Inserimento Nuovo Gruppo di Utenti";
		break;
	case "edit" :
		$file_config="gruppi";
		$tit="Modifica Dati di Registrazione";
		break;
	case "view" :
		$file_config="gruppi";
		$tit="Visualizzazione dei Dati di Registrazione";
		break;
	default :
		$file_config="gruppi";
		$tit="Gestione dei Gruppi";
		break;
}
?>
<html>
<head>
<title>Gestione Gruppi di Utenti di PraticaWeb</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>

<SCRIPT language=javascript>
function link(i){
	document.invia.id.value=i;
	document.invia.submit();
}
</SCRIPT>

</head>
<body>

<?php include "./inc/inc.page_header.php";?>
<H2 class="blueBanner"><?=$tit?></H2>
<?
	if (($modo=="edit") or ($modo=="new")){
		$tabella=new tabella_v("$tabpath/$file_config.tab",$modo);
		unset($_SESSION["ADD_NEW"]);
		$id=$_POST["id"];?>	
		<FORM id="utenti" name="utenti" method="post" action="<?php echo $formaction ?>">
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="75%">		
				  
		<tr> 
			<td> 
				<!-- contenuto-->
				<?php
				  if ($id)	{
					 if ($errors)
						$tabella->set_errors($errors);
					 if (!count($errors)) $tabella->set_dati("id=$id");
					 else
						$tabella->set_dati($_POST);
				}
				$tabella->edita();?>
				<!-- fine contenuto-->
			</td>
		  </tr> 
		</TABLE>
		<input name="active_form" type="hidden" value="admin.gruppi.php">
        <input name="mode" type="hidden" value="<?=$_POST["mode"]?>">
        <input name="id" type="hidden" value="<?=$id?>">
		</FORM>		

		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA   >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
<?
}elseif($modo=="view") {
	
	$tabella=new tabella_v("$tabpath/$file_config",$modo);
?>
	
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="75%">				  
		<tr> 
			<td> 
				<!-- contenuto-->
			<?					
				$tabella->set_titolo("Dati del gruppo di utenti","modifica",array("id"=>$id));
				$tabella->get_titolo();
				$tabella->set_dati("id=$id");
				$tabella->edita();
			?>
				<!-- fine contenuto-->
			</td>
		  </tr>
		  
		
		</TABLE>
		

<?}else{		//-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA TUTTI DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
?>	
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?$tabella=new tabella_h("$tabpath/$file_config",'list');
				
				$tabella->set_titolo("Elenco dei gruppi di Utenti","nuovo",array("id"=>""));
				$tabella->get_titolo();
				$tabella->set_dati("true");
				?>
				<form name="invia" action="<?php echo $formaction ?>" method="POST">
				<?
				$tabella->elenco();?>
			<!-- fine contenuto-->
					<input type="hidden" name="id" value="">
					<input type="hidden" name="mode" value="view">
				</form>
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
<?}?>

</body>
</html>
