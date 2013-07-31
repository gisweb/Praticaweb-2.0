<?include_once("login.php");
$tabpath="cdu";
//Attenzione funzione relazione tra il file elenco e 
$pratichexpagina=5;
$offset=0;
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

if ($_POST["pag"]){
	//pagina con i risultati al primo giro faccio tutta la query poi mi porto dietro l'array delle pratiche trovate
	$pagenum=$_POST["pag"];
	$pratichexpagina=$_POST["xpag"];
	$elenco=$_POST["elenco"];
	$criterio=$_POST["criterio"];

	if (!isset($elenco)){		
		//se non ho ancora fatto la query la costruisco
		include_once "./db/db.cdu.queryricerca.php";	
		//echo $sqlRicerca;
		$db->sql_query ($sqlRicerca);//trovo l'elenco degli id delle pratiche che mi interessano
		$elenco_pratiche=$db->sql_fetchlist("pratica");
		if ($elenco_pratiche) $elenco=implode(",",$elenco_pratiche);
		$_SESSION["RICERCA"]=$_POST;
	} 
	else{
		//sono al secondo giro ho l'elenco delle pratiche per la query
		$elenco_pratiche=explode(",",$elenco);
	}		
	//cosÃ¬ faccio una query in piÃ¹ la prima volta ma evito di fare una query pesante ad ogni pagina

	if ($elenco_pratiche){
		$totrec=count($elenco_pratiche);		
		if ($totrec==1){
			$idpratica=$elenco_pratiche[0];
			?><html><body>
				<script language="javascript">
					document.location='praticaweb.php?cdu=1&pratica=<?=$idpratica?>';
				</script></body></html>
		<?	
			exit;
		}
		$pages=intval($totrec/$pratichexpagina); 
		if ($totrec%$pratichexpagina) $pages++; 
		$offset=($pagenum-1)*$pratichexpagina;		
		$prat_max=$offset+$pratichexpagina;		
		if($prat_max > $totrec) $prat_max=$totrec;
?>
<html>
<head>
<title>Risultato Ricerca</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language="javascript">
function paginasucc(pg){
	document.result.pag.value=pg
	document.result.submit();
}
</script>
</head>
<body link="#0000FF" vlink="#0000FF" alink="#0000FF">
<?include "./inc/inc.page_header.php";	?>
<H2 class=blueBanner>Esito della ricerca&nbsp;&nbsp;<font size=-1 color=#000000>Risultati <b><?=$offset+1?></b> - <b><?=$prat_max?></b> su <?=$totrec?> <b></b></font></H2>
<p><font size="-2"><b>criteri di ricerca:</b> <?=$criterio?></font></p>

<?include "cdu.elenco_richieste.php";?>
	<form name="result" method="post" action="cdu.ricerca.php">
	  <input type="hidden" name="pag" value=""> 
	  <input type="hidden" name="xpag" value="<?=$pratichexpagina?>">
	  <input type="hidden" name="elenco" value="<?=$elenco?>">
	  <input type="hidden" name="criterio" value="<?=$criterio?>">
	 <table border=0 cellpadding=0 width=1% cellspacing=4 align=center>
	<tr>
	<td valign="bottom" nowrap class="selezione">Pagina dei risultati:&nbsp;<td>
	<?for ($i=1;$i<$pages+1;$i++){
		if ($i==$pagenum)
			$numpag="<font color=#FF0000>$i</font>";
		else
			$numpag=$i;
		?> 
		<td><a href="javascript:paginasucc(<?=$i?>)"><br><?=$numpag?></a></td>
		<?}?>
	</tr>
	</table>
	</form>
	
      <IMG height=1 src="images/gray_light.gif" width="100%"  vspace=1><BR>      
	  <!-- ### FOOTER INCLUDE ######################################################################### -->
      <P class=footer><IMG height=1 alt="" src="images/pixel.gif"  vspace=4><BR>
<input  class="hexfield"  type="button" value="Annulla" onClick="javascript: document.location='cdu.ricerca.php'" >
      </P>

</body></html>

<?
		exit;
	}
	else{
		$notfound=1;
	}  // END IF TROVATE

}

include "./lib/tabella_v.class.php";
?>
<html>
<head>
<title>Ricerca pratica</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body>
<?include "./inc/inc.page_header.php";?>
    <FORM id="ricerca" name="ricerca" method="post" action="cdu.ricerca.php">
 	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Ricerca pratiche</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 			
				<!-- ricerca base pratica -->
				<?
				if ($notfound) echo("<p><b>La ricerca non ha dato alcun risultato</b></p>");
				$tabella=new tabella_v("$tabpath/ricerca.tab",'new');	
				if((!$_REQUEST["new"]) ||($notfound))
					$tabella->set_dati($_SESSION["RICERCA"]);
                
				$tabella->edita();?>
				</td>
		  </tr>
		  <tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   </tr>  
		</TABLE>
		<table class="stiletabella" cellpadding="2" cellspacing="2">
			<tr>
				<td>
					<b>Pratiche per pagina:</b>
				</td>
			</tr>
			<tr>
				<td>
						<input class="textbox" name="xpag" type="text" size="3" value="<?=$pratichexpagina?>">
						<input name="azione" style="width:120px" type="submit" class="hexfield1" tabindex="14" value="Avvia ricerca >>>">
				</td>
			</tr>
			<tr>
				<td>
					<input name="active_form" type="hidden" value="cdu.ricerca.php">
					<input name="pag" type="hidden" value="1">
					
				</td>
			</tr>
			
			
			<tr>
				<td>
				<input  name=""  id="" class="hexfield1"  type="button" value="  Chiudi  " onClick="javascript:window.open('index.php','indexPraticaweb');window.close()"></td>
			<td></td>
			</tr>
			
			
		
		</table>
		</FORM>		
		<?$db->sql_close();?>
</body>
</html>
