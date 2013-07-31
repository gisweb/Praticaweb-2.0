<?
include_once("login.php");
$tabpath="vigi";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$idpratica=$_REQUEST["pratica"];
$config_file="$tabpath/esposti";
include "./lib/tabella_v.class.php";
?>
<html>
<head>
<title>Esposti</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<SCRIPT language="javascript">
function aggiungi_riferimento(id,pratica){
	parent.window.document.location="vigi.esposti.php?mode=new&rif="+id+"&pratica="+pratica;
}
</SCRIPT>
</head>
<body  background="">
<?	
	if (($modo=="edit") or ($modo=="new")){
		if ($modo=="new"){
			unset($_SESSION["ADD_NEW"]);
			//$config_file.="_new.tab";
		}

		$tabella=new tabella_v($config_file,$modo);	
		include "./inc/inc.page_header.php";
		$id=$_POST["id"];?>
	<FORM id="" name="" method="post" action="praticaweb.php">
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
			<tr> 
				<td> 
			<!-- intestazione-->
					<H2 class="blueBanner"><?echo(($modo=="edit")?("Modifica esposto"):("Inserisci nuovo esposto"))?></H2>
			<!-- fine intestazione-->
				</td>
			</tr>
			<tr> 
				<td> 
				<!-- contenuto-->
				<?
				if($Errors){
					$tabella->set_errors($Errors);
					$tabella->set_dati($_POST);
				}
				elseif ($modo=="edit"){	
					$tabella->set_dati("id=$id");
				}
				$tabella->edita();
				?>			
				<!-- fine contenuto-->
				</td>
			</tr>
		</TABLE>

		<input name="active_form" type="hidden" value="vigi.esposti.php">				
		<input name="mode" type="hidden" value="<?=$modo;?>">
				
	</FORM>			
<?	include "./inc/inc.window.php";
}else{
		
		$tabella=new tabella_v($config_file);
		?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Esposti ricevuti</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
	
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?
				$tabella->set_dati("pratica=$idpratica");
				$tabella->set_titolo("Esposto","modifica",array("id"=>$id));
				$tabella->elenco();?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		  
		  <TR> 
			<TD> 
			<!-- tabella nuovo inserimento-->
				<?$tabella->set_titolo("Aggiungi nuovo Esposto ---> clicca su Nuovo","nuovo");
				$tabella->get_titolo();?><BR>
			<!-- fine tabella nuovo inserimento-->
			</TD>
		  </TR>			  
		</TABLE>
<?}?>

</body>
</html>
