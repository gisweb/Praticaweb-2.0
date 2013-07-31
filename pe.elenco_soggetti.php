<?
include_once("login.php");
include_once "./lib/tabella_h.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$img="volture";
$titpag=$_SESSION["TITOLO_$idpratica"];
?>
<html>
<head>
<title><?=$titolo." - ".$titpag?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>	
<SCRIPT language="javascript" src="src/window.js" type="text/javascript"></SCRIPT>
<script language="javascript">
function link(id,ruolo){
	window.location="pe.scheda_soggetto.php?id="+id +"&ruolo="+ ruolo +"&pratica=<?=$idpratica?>";
}
</script>

</head>

<body>
<H2 class=blueBanner>Elenco dei soggetti interessati</H2>
<TABLE cellPadding=0 cellspacing=0 border=0 class="stiletabella" width="100%">		

<?$i=0;
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$db->sql_query ("select * from pe.e_ruoli order by ordine;");
$elenco_ruoli = $db->sql_fetchrowset();
//print_array($elenco_ruoli);
$tabella_attuali=new Tabella_h("$tabpath/soggetto",'list');
$tabella_variati=new Tabella_h("$tabpath/soggetto",'list');
$tabella_variati->set_color("#FFFFFF","#FF0000",0,0);

	foreach($elenco_ruoli as $row){
		$ruolo=$row["ruolo"];
		$titolo=$row["titolo"];
		(($ruolo=="proprietario") || ($ruolo=="richiedente") || ($ruolo=="concessionario"))?($img="volture"):($img="variazioni");
		//$tabella_attuali->set_tag($ruolo);
		$tabella_attuali->params=Array('ruolo'=>$ruolo);
		$tabella_variati->params=Array('ruolo'=>$ruolo);
		//$tabella_variati->set_tag('v'.$ruolo);
		//$num_attuali=$tabella_attuali->set_dati("voltura=0 and $ruolo=1 and pratica=$idpratica");
		//$num_variati=$tabella_variati->set_dati("voltura=1 and $ruolo=1 and pratica=$idpratica");
		$num_attuali=$tabella_attuali->set_dati("$ruolo=1 and pratica=$idpratica");
		$num_variati=$tabella_variati->set_dati("$ruolo=-1 and pratica=$idpratica");		
		if ($num_attuali+$num_variati){
			$tabella_attuali->set_titolo($titolo);?>
	<tr>
		<td><?$tabella_attuali->get_titolo()?></td>
	</tr>
	<tr> 
		<td valign=top> 
			<!-- contenuto-->
				<?if ($num_attuali) $tabella_attuali->elenco();
					if ($num_variati){
						$i++;?>
				<SPAN id="close<?=$i?>" style="DISPLAY: none">
				<IMG onclick="invisibile(document.all.variati<?=$i?>,document.all.close<?=$i?>,document.all.open<?=$i?>)" src="images/<?=$img?>_fix.gif"></SPAN>
				<SPAN id="open<?=$i?>">
					<IMG onclick="visibile(document.all.variati<?=$i?>,document.all.close<?=$i?>,document.all.open<?=$i?>)" src="images/<?=$img?>.gif" >
				</SPAN>
				<SPAN id="variati<?=$i?>" style="DISPLAY: none">
				<?$tabella_variati->elenco();?>
				</SPAN>
				<?}?>
			<!-- fine contenuto-->
			<br>
		</td>
	</tr>
	<?	} //end if
	} // end foreach?>
	
	<?if ($tabella_attuali->editable) {?>
	<!-- riga per nuovo inserimento-->
	<tr> 
		<td>
		<form method="post" target="_parent" action="pe.scheda_soggetto.php">
			<table  class="printhide" width="100%" cellPadding=0  cellspacing=2 align="left">
				
				<input type="hidden" name="mode" value="addnew">
				<input type="hidden" name="pratica" value="<?php echo $idpratica;?>">
				<tr>
					<td width="90%" bgColor="#728bb8"><font face="Verdana" color="#ffffff" size="2"><b>Aggiungi un nuovo soggetto</b></font></td>
					<td>
						<button class="" id="btn_new"></div>
						<script>
							$('#btn_new').button({
								icons:{
									primary:'ui-icon-plus'
								},
								label:'Nuovo'
							}).click(function(){
								$(this).parents('form:first').submit();	
							});
						</script>
					</td>
				</tr>
				
			</table>
		</form>		
		</td>
	</tr>	
	<?}?>
</TABLE>
</body>
</html>
