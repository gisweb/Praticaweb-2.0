<?php
include_once("login.php");
include_once "./lib/tabella_v.class.php";
$tabpath="pe";
$self=$_SERVER["PHP_SELF"];
$idpratica=$_REQUEST["pratica"];
$idallegato=$_REQUEST["id"];
$idfile=$_REQUEST["idfile"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('list');
$titolo=$_SESSION["TITOLO_$idpratica"];
$form=$_POST["form"];
$dbh=  utils::getDb();
$sql =  "SELECT 'Elenco Allegati del documento : ' || coalesce(A.nome,'') as titolo FROM pe.e_documenti A INNER JOIN pe.allegati B ON(B.documento=A.id) WHERE B.id=".substr($idallegato,4);
$sth = $dbh->prepare($sql);
$sth->execute();
$tit = $sth->fetchColumn();

appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

?>
<html>
<head>
<title>Scheda - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>
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
<?php
if ($modo=="edit" || $modo=='new') { 
		include "./inc/inc.page_header.php";
		$tabella=new tabella_v("$tabpath/doc_dettaglio",$modo);
		if (strpos($idallegato,'all_')!==FALSE) $idallegato=substr($idallegato,4);
		$tabella->set_dati("idfile=$idfile");
		if (isset($_POST["allegato"])) $allegato=$_POST["allegato"];
		else
			$allegato=$tabella->array_dati[0]['documento'];
		unset($_SESSION["ADD_NEW"]);
			?>
	<FORM id="" name="" method="post" action="praticaweb.php" enctype="multipart/form-data">	
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

			
		 
<?php 
                    
?>	 	
	</table>

		  </td>
		  </tr>

		</TABLE>		
				<input name="active_form" type="hidden" value="pe.scheda_documento.php">	
				<input name="id" type="hidden" value="<?=$idallegato?>">
                <input name="idfile" type="hidden" value="<?=$idfile?>">
				<input name="mode" type="hidden" value="<?=$modo?>">				
				<input name="pratica" type="hidden" value="<?=$idpratica?>">
</FORM>

<?php
}else if ($modo=="view"){		
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
	   <?php
           if(!isset($db))
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
		<?php }	
		if(!$nfile)
			print ("<tr><td>&nbsp;&nbsp;Nessun file allegato<p></p></td></tr>");
		?>	 	
	</table>
		  </td>
		  </tr> 
		</TABLE>	
		
<?php	
}
else {
    include_once "./lib/tabella_h.class.php";
    $tabella=new tabella_h("$tabpath/doc_dettaglio",'list');
    $idallegato=substr($idallegato,4);
    $tabella->set_titolo($tit,"nuovo",array("mode"=>"new","titolo"=>$titolo,"id"=>$idallegato,"pratica"=>$idpratica));
    $numrows=$tabella->set_dati("id=$idallegato AND coalesce(idfile,0)>0");
    
    $tabella->get_titolo();
    if ($numrows)
        $tabella->elenco();
    else
        print "<p><b>Nessun allegato caricato</b></p>";
}//end switch
?>

</body>
</html>
