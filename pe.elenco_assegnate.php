<?php
include_once("login.php");
$tabpath="pe";
$notfound=0;
//Attenzione funzione relazione tra il file elenco e 
$pratichexpagina=20;
$offset=0;
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");


	//pagina con i risultati al primo giro faccio tutta la query poi mi porto dietro l'array delle pratiche trovate
	$pagenum=($_POST["pag"])?($_POST["pag"]):(1);
	$elenco=$_POST["elenco"];

	if (!isset($elenco)){		
		//se non ho ancora fatto la query la costruisco
		$sqlRicerca="SELECT pratica,max(tmsins) FROM pe.wf_transizioni WHERE utente_fi=$_SESSION[USER_ID] group by 1 order by 2 DESC LIMIT 100;";	
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
	//così faccio una query in più la prima volta ma evito di fare una query pesante ad ogni pagina

	
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

<?
if ($elenco_pratiche){
		$totrec=count($elenco_pratiche);		
		if ($totrec==1){
			$idpratica=$elenco_pratiche[0];
			?><html><body>
				<script language="javascript">
					document.location='praticaweb.php?pratica=<?=$idpratica?>';
				</script></body></html>
		<?	
			exit;
		}
		$pages=intval($totrec/$pratichexpagina); 
		if ($totrec%$pratichexpagina) $pages++; 
		$offset=($pagenum-1)*$pratichexpagina;		
		$prat_max=$offset+$pratichexpagina;		
		if($prat_max > $totrec) $prat_max=$totrec;
		include "pe.elenco_pratiche.php";
?>
	<form name="result" method="post" action="pe.ricerca.php">
	  <input type="hidden" name="pag" value=""> 
	  <input type="hidden" name="xpag" value="<?=$pratichexpagina?>">
	  <input type="hidden" name="elenco" value="<?=$elenco?>">
	  <input type="hidden" name="criterio" value="<?=$criterio?>">
	 <table border=0 cellpadding=0 width=1% cellspacing=4 align=center>
	<tr>
	<td valign="bottom" nowrap class="selezione">Pagina dei risultati:&nbsp;<td>
	<?
	
	for ($i=1;$i<$pages+1;$i++){
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
	
      



<?
		exit;
	}
	else{
		print "<p><b>Nessuna Pratica Assegnata</b></p>";
	}  // END IF TROVATE


?>

<IMG height=1 src="images/gray_light.gif" width="100%"  vspace=1><BR>      
	  <!-- ### FOOTER INCLUDE ######################################################################### -->
      <P class=footer><IMG height=1 alt="" src="images/pixel.gif"  vspace=4><BR>
<input  class="hexfield"  type="button" value="Chiudi" onClick="javascript: window.opener.focus();window.close()" >
      </P>
</body></html>
