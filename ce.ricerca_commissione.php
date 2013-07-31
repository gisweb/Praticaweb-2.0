<?php
include_once("login.php");
include "./lib/tabella_v.class.php";
//Attenzione funzione relazione tra il file elenco e 

$tabpath="ce";
$pratichexpagina=5;
$offset=0;
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
if ($_REQUEST["comm_paesaggio"]=="1") $commissione="comm_paesaggio";
elseif ($_REQUEST["comm"]=="1") $commissione="comm";

   
else $commissione="comm";

if ($_POST["pag"]){
	$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
	//pagina con i risultati al primo giro faccio tutta la query poi mi porto dietro l'array delle pratiche trovate
	$pagenum=$_POST["pag"];
	$pratichexpagina=$_POST["xpag"];
	$elenco=$_POST["elenco"];
	$criterio=$_POST["criterio"];
	
	if (!isset($elenco)){		
		//se non ho ancora fatto la query la costruisco
		include_once "./db/db.pe.queryricerca.php";
		$db->sql_query ($sqlRicerca);//trovo l'elenco degli id delle pratiche che mi interessano
		print_debug($sqlRicerca);
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
		if ($totrec==1 and $modo!=="cancella"){
			
			$idpratica=$elenco_pratiche[0];
			echo "Commissione $idpratica";
			?><html><body>
				<script language="javascript">
					document.location='praticaweb.php?<?=$commissione?>=1&pratica=<?=$idpratica?>';
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

<?include "./inc/inc.page_header.php";?>

<H2 class=blueBanner>Esito della ricerca&nbsp;&nbsp;<font size=-1 color=#000000>Risultati <b><?=$offset+1?></b> - <b><?=$prat_max?></b> su <?=$totrec?> <b></b></font></H2>
<p><font size="-2"><b>criteri di ricerca:</b> <?=$criterio?></font></p>

<form name="result" method="post" action="ce.ricerca_commissione.php">	
	<?include "ce.elenco_commissioni.php";?>
	<input type="hidden" name="pag"> 
	<input type="hidden" name="xpag" value="<?=$pratichexpagina?>">
	<input type="hidden" name="elenco" value="<?=$elenco?>">
	<input type="hidden" name="criterio" value="<?=$criterio?>">
	<input type="hidden" name="mode" value="<?=$modo?>">
       <input type="hidden" name="<?=$commissione?>" value="1">
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
	<?if ($modo=="cancella") {?><input class="hexfield" type="submit" name="azione" value="Cancella" onclick="return confirm('Sicuro di voler procedere con l\'eliminazione ?');"><?}?>
	<input  class="hexfield"  type="button" value="Annulla" onClick="javascript: document.location='ce.ricerca_commissione.php?<?=$commissione?>=1<?if ($_REQUEST["mode"]=="cancella") print("&mode=cancella");?>'" >
	</form>
	
      <IMG height=1 src="images/gray_light.gif" width="100%"  vspace=1><BR>      
	  <!-- ### FOOTER INCLUDE ######################################################################### -->
      <P class=footer><IMG height=1 alt="" src="images/pixel.gif"  vspace=4><BR>

      </P>

</body></html>

<?
		
		exit;
	}
	else{
		$notfound=1;
	}  // END IF TROVATE

}

?>
<html>
<head>
<title>Ricerca Commissioni</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body>
<?php
	include "./inc/inc.page_header.php";
	if ($_POST["azione"]=="Cancella"){
		$idcomm=$_POST["ref"];
		include "./db/db.ce.elimina_comm.php";
	}
	?>
 	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<FORM id="ricerca" name="ricerca" method="post" action="<?=$self?>">		  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Ricerca Commissioni</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 			
				<!-- ricerca base pratica -->
				<?
				if ($notfound) echo("<p><b>La ricerca non ha dato alcun risultato</b></p>");
				$tabella=new tabella_v("$tabpath/ricerca_commissioni",'new');
				//$tabella_avanzata=new tabella_v("ricerca_avanzata.tab");
				if((!$_GET["new"]) ||($notfound))
					//$tabella->set_dati($_SESSION["RICERCA"]);
				$tabella->edita();?>
				

				</td>
		  </tr>
			<?//if ($modo=="cancella") 
				print("\n<tr>\n<td><input name=\"all\" type=\"checkbox\"><b>Seleziona tutte le commissioni</b>\n</td>\n</tr>\n")
			?>
		  <tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   </tr>  
		</TABLE>
		<table class="stiletabella" cellpadding="2" cellspacing="2">
			<tr>
				<td>
					<input name="active_form" type="hidden" value="ce.ricerca_commissioni.php">
					<input name="pag" type="hidden" value="1">
					<b>Tipo di ricerca:</b>
				</td>
				<td>
					<b>Commissioni per pagina:</b>
				</td>
			</tr>
			<tr>
				<td>
					<select name="tiporicerca">
						<option value="1" selected>Tutti i criteri devono essere verificati</option>
						<option value="0">Almeno un criterio deve essere verificato</option>
					</select>
				</td>
				<td>
                                          <input type="hidden" name="<?=$commissione?>" value="1">
						<input class="textbox" name="xpag" type="text" size="3" value="<?=$pratichexpagina?>">
						<input name="azione" style="width=120px" type="submit" class="hexfield1" tabindex="14" value="Avvia ricerca >>>">
				</td>
			</tr>
			<tr>
				<td>
				<input  name=""  id="" class="hexfield1"  type="button" value="  Esci  " onClick="javascript:window.open('index.php','indexPraticaweb');window.opener.focus();window.close()"> </td>
			<td></td>
			</tr>
			<input type="hidden" name="mode" value="<?=$modo?>">
		</FORM>	
		</table>	
		<?include "./inc/inc.window.php"; // contiene la gesione della finestra popup?>
</body>
</html>
