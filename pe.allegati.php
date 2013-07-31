<?include_once("login.php");
include "./lib/tabella_h.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]) && $_REQUEST["mode"])?($_REQUEST["mode"]):('view');
$titolo=$_SESSION["TITOLO_$idpratica"];
?>
<html>
<head>
<title>Allegati - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<style>
    .ui-state-hover,.ui-state-active,.ui-state-focus{
        background:url("css/start/images/ui-bg_glass_45_0078ae_1x400.png");
        color:white;
    }
</style>
</head>
<body  background="" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
<?php
    if ($modo=="edit"){
		$_SESSION["ADD_NEW"]=0;
		$iter=$_POST["iter"];
		$nomeiter=$_POST["nomeiter"];	
		include "./inc/inc.page_header.php";
		$tabella_allegati=new tabella_h("$tabpath/doc_allegati",$modo);
		$tabella_elenco=new tabella_h("$tabpath/doc_elenco",$modo);
        $dummytable=new tabella_h("buttons",'new');
		$num_allegati=$tabella_allegati->set_dati("pratica=$idpratica and iter=$iter and (allegato=1 or mancante=1)");
		$num_elenco=$tabella_elenco->set_dati("iter=$iter and id not in (select 'doc_'||documento::varchar as id from pe.allegati inner join pe.e_documenti on(e_documenti.id=documento) where iter=$iter and (allegato=1 or integrato=1 or mancante=1) and pratica=$idpratica)");
		
		$tabella_allegati->set_titolo($nomeiter);
		$tabella_elenco->set_titolo($nomeiter);
		$tabella_allegati->set_color("#728bb8","#FFFFFF",0,0);
        ?>
		<H2 class=blueBanner>Gestione allegati:&nbsp;<?=$nomeiter?></H2>
        <FORM method="POST" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
			<tr> 
                <td> 
			<!-- contenuto-->		
			<?php if (!$num_elenco){?>
			<h2>l'elenco dei documenti per la fase <?echo($nomeiter);?> è vuoto. Inserire un elenco di documenti</h2><br>
			<input name="azione" type="submit" class="hexfield" tabindex="14" value="Chiudi">
			<input name="active_form" type="hidden" value="pe.allegati.php">
			<input name="pratica" type="hidden" value="<?=$idpratica?>">
			
			</td></tr></table>
			<?php
                return;
			}else{
                $jsActivate='';
				if ($num_allegati){
                    $jsActivate="$('#accordion').accordion('activate',0); ";
					$tabella_allegati->elenco();
				}
				else
					print ("<p><b>Nessun Documento Allegato</b></p>");
				if ($num_elenco){?>
				<div id="accordion" style="">
				<h3><a href="#">Elenco Allegati</a></h3>
				<div>
					<?php $tabella_elenco->elenco();?>
				</div>
				</div>
				<script>
					$("#accordion").accordion({
                        collapsible: true,
                        disabled:false
                    });
                    
                    <?php echo $jsActivate;?>
				</script>
				
				
				<?}?>
			<!-- fine contenuto-->
			 </td>
	      </tr>

		</TABLE>
    <input name="active_form" type="hidden" value="pe.allegati.php">
	<input name="pratica" type="hidden" value="<?=$idpratica?>">        
<?php
    print $dummytable->set_buttons();
?>
    </FORM>	
	<?}//end if?> 
		
<?}else{// VISTA DATI
$tabella_allegati=new Tabella_h("$tabpath/doc_allegati");
$tabella_mancanti=new Tabella_h("$tabpath/doc_mancanti");
$tabella_mancanti->set_color("#FFFFFF","#FF0000",0,0);
$db=$tabella_allegati->get_db();
$db->sql_query ("select * from pe.e_iter order by ordine;");
$elenco_iter = $db->sql_fetchrowset();
?>

<H2 class="blueBanner">Elenco documenti allegati alla pratica</H2>
<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		

<?	foreach ($elenco_iter as $row){
		$iter=$row["id"];
		$nomeiter=$row["nome"];
		//Visualizzare solo quelli inerenti il tipo di pratica e opzioni (es se richiesta voltura)
		$num_allegati=$tabella_allegati->set_dati("pratica=$idpratica and iter=$iter and (allegato=1 or integrato=1)");
		$num_mancanti=$tabella_mancanti->set_dati("pratica=$idpratica and iter=$iter and mancante=1");
		$tabella_allegati->set_titolo($nomeiter,"modifica",array("iter"=>$iter,"nomeiter"=>$nomeiter));
		$tabella_allegati->set_tag($idpratica);
		$tabella_mancanti->set_tag($idpratica);
		?>
		  <tr> 
			<td> 
			<!--  intestazione-->
				<?$tabella_allegati->get_titolo();
					if ($num_allegati) 
						$tabella_allegati->elenco();
					else
					print ("<p><b>Nessun Documento Allegato</b></p>");
					if ($num_mancanti) 
						$tabella_mancanti->elenco();
				?>
			<!-- fine intestazione-->
			<br>
			</td>
		  </tr>
<?	}
	
// end for
if ($tabella_allegati->editable){
?>
	<tr>
		<td>
		<hr>
			<form method="post" target="_parent" action="stp.stampe.php">
				<input type="hidden" name="form" value="pe.allegati">
				<input type="hidden" name="procedimento" value="">
				<input type="hidden" name="pratica" value="<?php echo $idpratica?>">
				<table class="stiletabella" width="90%" border=0>
					<tr>
						<td align="right" valign="bottom">
							<input type="image" src="images/printer_edit.png" alt="Modifica elaborati">
						</td>
					</tr>
				</table>
			</form></td>
	</tr>
</TABLE>



<?
}
}//end if?>

</body>
</html>
