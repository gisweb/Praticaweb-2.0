<?
include_once("login.php");
include_once "./lib/tabella_v.class.php";
$tabpath="pe";
$self=$_SERVER["PHP_SELF"];
$idpratica=$_REQUEST["pratica"];
$idallegato=$_REQUEST["id"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$titolo=$_SESSION["TITOLO_$idpratica"];
$form=$_POST["form"];
if(is_uploaded_file($_FILES['myfile']['tmp_name'])){//operazione di upload 
	if (!$_SESSION["ADD_NEW"]){//inserisco solo se non ho già  inserito il dato
		include_once "./lib/upload.php";//gestione dell'upload del file
	}
}


?>
<html>
<head>
<title>Scheda - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language=javascript>

function elimina(id){
	var agree=confirm('Sicuro di voler eliminare l\'allegato selezionato?');
	if (agree){
		document.getElementById("delete").value=id;
		document.ubicazione.submit();
	}
}
</script>

</head>
<body  background="">
<?
if ($modo=="edit") { 
		include "./inc/inc.page_header.php";
		$tabella=new tabella_v("$tabpath/doc_dettaglio",$modo);
		if (strpos($idallegato,'all_')!==FALSE) $idallegato=substr($idallegato,4);
		$tabella->set_dati("id=$idallegato");
		if (isset($_POST["allegato"])) $allegato=$_POST["allegato"];
		else
			$allegato=$tabella->array_dati[0]['documento'];
		unset($_SESSION["ADD_NEW"]);
			?>
	<FORM id="" name="" method="post" action="praticaweb.php">	
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
			  
			<tr> 
				<td> 
			<!-- intestazione-->
					<H2 class="blueBanner">Scheda documento allegato:&nbsp;<?php echo $allegato;?></H2>
			<!-- fine intestazione-->
				</td>
			</tr>
			<tr> 
				<td> 
				<!-- contenuto-->
<?php
$tabella->edita();
?>
				<!-- fine contenuto-->
				</td>
			</tr>		  

			<tr>       
				<td width="600"> 
			   <br><b>  Elenco file allegati:</b>
       <table class="stiletabella" width=100% border="0" cellpadding=1 cellspacing=0>
	   <?
	   //Elenco degli allegati o come icona o come tumbnail
		   if(!isset($db))
				$db=$tabella->get_db();
            $sql="SELECT numero FROM pe.avvioproc WHERE pratica=$idpratica;";
            $db->sql_query($sql);
            $numero=$db->sql_fetchfield('numero');
            $numero=preg_replace("|([^A-z0-9\-]+)|",'',str_replace('/','-',str_replace('\\','-',$numero)));
            
			$sql="select * from pe.file_allegati where pratica=$idpratica and allegato=$idallegato and form='allegati' order by ordine";
			$db->sql_query ($sql);
			$elenco = $db->sql_fetchrowset();
			$nfile=$db->sql_numrows();
			for($i=0;$i<$nfile;$i++){
			
				if ($elenco[$i]["tipo_file"]=="application/pdf")
					$immagine="images/pdf_icon.jpg";
				elseif ($elenco[$i]["tipo_file"]=="application/vnd.sun.xml.writer")
					$immagine="images/openoffice2.jpg";
				elseif ($elenco[$i]["tipo_file"]=="application/msword") 
					$immagine="images/msword.jpg";
				elseif ($elenco[$i]["tipo_file"]=="image/jpeg") 
					$immagine=$pr->url_allegati."tmb/tmb_".$elenco[$i]["nome_file"]; 
				else
					$immagine="images/boxin.gif";
			?>   
	   
          <tr> 
            <td align="left" colspan=3 height="6"><img src="images/blue.gif" height="2" width="100%"></td>
          </tr>
          <tr > 
            <td  width="134" align="center" valign="middle" rowspan="3"><img src="<?=$immagine?>" ></td>
            <td height="20" valign="top"><b>Nome del file: </b><?=$elenco[$i]["nome_file"]?></td>
            <td  valign="top" align="right">Elimina file allegato <input type="checkbox" name="elimina[<?=$elenco[$i]["id"]?>]"></td>
          </tr>
		  <tr > 
            <td height="11"   valign="baseline" align="left"><b>Descrizione:</b></td>
            <td  valign="baseline"  align="right" height="11">Ordine elenco 
              <input type=text maxLength="1" size="1"  class="textbox" value=<?=$elenco[$i]["ordine"]?> name="ordine[<?=$elenco[$i]["id"]?>]">
            </td>
          </tr>
          <tr > 
            <td height="20" colspan="2" valign="top" align="left"><textarea cols="55" rows="3" name="descrizione[<?=$elenco[$i]["id"]?>]" ><?=$elenco[$i]["note"]?></textarea></td>
          </tr>
		 
		<?}	?>	 	
	</table>

		  </td>
		  </tr>
		  
		  <tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   </tr>  

		</TABLE>		
				<input name="active_form" type="hidden" value="pe.scheda_documento.php">	
				<input name="id" type="hidden" value="<?=$idallegato?>">
				<input name="mode" type="hidden" value="<?=$modo?>">				
				<input name="pratica" type="hidden" value="<?=$idpratica?>">
</FORM>
<form name="allegati" enctype="multipart/form-data" action="<?=SELF?>" method="POST">
<div style="margin-left:10px;"><font size=-1>Aggiungi file allegato:</font><br>
			<input name="id" type="hidden" value="<?=$idallegato?>">
			<input name="mode" type="hidden" value="<?=$modo?>">		
			<input name="pratica" type="hidden" value="<?=$idpratica?>">		
			<input name="delete" id="delete" type="hidden" value="">	
			<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
			<input type="hidden" name="form" value="allegati">
			<input name="myfile" type="file" style="width:400px;FONT: 11px/1.3em"  >&nbsp;
			<div id="btn_upload"></div>
			<script>
				$('#btn_upload').button({
					icons:{
						primary:'ui-icon-document'	
					},
					label:'Carica File'
				}).click(function(){
					$(this).parents('form:first').submit();	
				});
			</script>
</div>
</form>
<?
}else{		
//<<<<<<<<<<<<<<<<<<<<<   MODALITA' VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->	
		$tabella=new tabella_v("$tabpath/doc_dettaglio");
		$id=substr($idallegato,4);
		$tabella->set_dati("id = $id");
		$nome_doc=$tabella->get_campo("documento");
		?>
		<H2 class="blueBanner">Scheda allegato:&nbsp; <?=$nome_doc?></H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?//aggiunto il campo costante tabella = richiedenti/tecnici per portarmi sul form il nome della tabella
				$tabella->set_titolo("Scheda allegato","modifica",array("allegato"=>$nome_doc,"id"=>$id,"pratica"=>""));
				$tabella->elenco();?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		  		 <tr>
		       
			   <td width="100%"> <br><br>
		<b>  &nbsp;Elenco file allegati:</b>
       <table class="stiletabella" width=100% border="0" cellpadding=1 cellspacing=0>
	   <?if(!isset($db))
				$db=$tabella->get_db();
			$pr=new pratica($idpratica);
            $sql="SELECT numero FROM pe.avvioproc WHERE pratica=$idpratica;";
            $db->sql_query($sql);
            $numero=$db->sql_fetchfield('numero');
            $numero=preg_replace("|([^A-z0-9\-]+)|",'',str_replace('/','-',str_replace('\\','-',$numero)));
       
			$sql="select * from pe.file_allegati where allegato=$id order by ordine";
			$db->sql_query ($sql);
			$elenco = $db->sql_fetchrowset();
			//print_r($elenco);
			$nfile=$db->sql_numrows();
			for($i=0;$i<$nfile;$i++){
				
				if ($elenco[$i]["tipo_file"]=="application/pdf")
					$immagine="images/pdf_icon.jpg";
				elseif ($elenco[$i]["tipo_file"]=="application/vnd.sun.xml.writer")
					$immagine="images/openoffice2.jpg";
				elseif ($elenco[$i]["tipo_file"]=="application/msword") 
					$immagine="images/msword.jpg";
				elseif ($elenco[$i]["tipo_file"]=="image/jpeg") 
					$immagine=$pr->url_allegati."tmb/tmb_".$elenco[$i]["nome_file"]; 
				else
					$immagine="images/boxin.gif";
			?>   
	   	    <tr> 
				<td align="left" colspan="2" height="6"><img src="images/blue.gif" height="2" width="100%"></td>
			</tr>	  
		    <tr > 
				<td  width="134" align="center" valign="middle" height="120"><a target="_new"  href="<?=$pr->url_allegati. $elenco[$i]["nome_file"]?>"><img src="<?=$immagine?>" ></a></td>
				<td valign="middle" align="left" colspan="2"><b>Descrizione: </b><br><?=$elenco[$i]["note"]?></td>
			</tr>
		<?}	
		if(!$nfile)
			print ("<tr><td>&nbsp;&nbsp;Nessun file allegato<p></p></td></tr>");
		?>	 	
	</table>
		  </td>
		  </tr> 
		</TABLE>	
		
<?	} //end switch?>

</body>
</html>
