<?
include "login.php";
include("./src/fckeditor/fckeditor.php") ;

$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
//if($_SESSION["PERMESSI"]==1) print_array($_REQUEST);

$sql="SELECT id,nome,descrizione FROM stp.css order by ordine";
$db->sql_query($sql);
$css_nome=$db->sql_fetchlist('nome');
$css_desc=$db->sql_fetchlist('descrizione');
$css_id=$db->sql_fetchlist('id'); 
for($i=0;$i<count($css_desc);$i++) $css_desc[$i]=addslashes(trim($css_desc[$i]));

/*GESTIONE DEL FILE*/
if ($_REQUEST["file"]){
	$file=$_REQUEST["file"];
	$tipo=$_REQUEST["tipo"];
}
elseif ($_REQUEST["id_doc"]){
	$sql="SELECT file_doc FROM stp.stampe WHERE id=".$_REQUEST['id_doc'];
	$db->sql_query($sql);
	$file=$db->sql_fetchfield('file_doc');
	$tipo="documenti";
	$id_doc=$_REQUEST["id_doc"];
	$id=$_REQUEST["id"];
}
elseif($_REQUEST["id_modelli"]){
	$id_modelli=$_REQUEST["id_modelli"];
	$sql="SELECT e_modelli.nome,form,testohtml,css_id,definizione,css.descrizione as css_desc FROM stp.e_modelli LEFT JOIN stp.css on (css_id=css.id) WHERE e_modelli.id=$id_modelli";
	$db->sql_query($sql);
	$file=$db->sql_fetchfield('nome');
	$form=$db->sql_fetchfield('form');
	$testo=$db->sql_fetchfield('testohtml');
	$css_modello=$db->sql_fetchfield('css_id'); 
	$definizione=$db->sql_fetchfield('definizione'); 
	$mess_css=$db->sql_fetchfield('css_desc'); 
	$tipo="modelli";	
}
elseif($_REQUEST["mode"]=="new") $tipo="modelli";
if ($_REQUEST["form"]) $form=$_REQUEST["form"];
if ($tipo=="modelli"){
	
	/*SELEZIONE DELLE VISTE DAL DATABASE*/
	$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
	$sql="(SELECT 'Seleziona -->' as nome,'' as tipo,'Seleziona -->' as alias_nome,'' as descrizione,1 as ord) UNION (SELECT DISTINCT nome_vista as nome,tipo,alias_nome_vista,descrizione_vista,2 as ord FROM stp.colonne) order by ord,nome;";
	$db->sql_query($sql);
	$tables=$db->sql_fetchlist('nome');
	$types=$db->sql_fetchlist('tipo');
	$alias=$db->sql_fetchlist('alias_nome');
	for($i=0;$i<count($tables);$i++){
		$value=$types[$i].".".$tables[$i];
		$tmp=explode("_",$tables[$i]);
		$str_name=ucwords($alias[$i]);
		$print_options.="\t\t\t\t\t\t\t\t\t<option value=\"$value\">$str_name</option>\n";
	}
	$sql="SELECT CASE (tipo) WHEN 'FUNCTION' THEN 'FN_'||nome_vista ELSE nome_vista end as nome,nome as 		colonna,alias_nome as alias_colonna,descrizione,visibile,tipo FROM stp.colonne WHERE visibile=1 order by nome_vista,nome;";
	//echo "<p>$sql</p>";
	if($db->sql_query($sql)){
		$ris=$db->sql_fetchrowset();
		for($i=0;$i<count($ris);$i++){
			$cols[$ris[$i]["nome"]][]=Array("colonna"=>$ris[$i]["colonna"],"alias"=>$ris[$i]["alias_colonna"],"descrizione"=>$ris[$i]["descrizione"]);
		}
	}
}

$dir=($tipo=="modelli")?(MODELLI):(STAMPE);
$action=($tipo=="modelli")?("window.location='stp.elenco_modelli.php?tipo=html'"):("document.forms['dati'].action='stp.stampe.php';document.forms['dati'].submit();");

if ($_POST["azione"] and $_POST["azione"]!=="Annulla" ){
	include "./db/db.stp.editor.php";
}
else{
	$testo='<html>
		
	<body>
		'.$testo.'
	</body>
</html>	';
	}

/*FINE GESTIONE DEL FILE*/
?>
<html>
<head>
<title>Editor</title>
<link href="src/screen.css" rel="stylesheet" type="text/css">
<LINK media="screen" href="./src/modelli.css" type="text/css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="./src/fckeditor/fckeditor.js"></script>
<script language="javascript" type="text/javascript" src="./src/ajax.js"></script>
<script language="javascript" type="text/javascript" src="./src/ajax2.js"></script>
<script language="javascript" type="text/javascript" src="./src/x_core.js"></script>
<script language="javascript" type="text/javascript" src="./js/LoadLibs.js"></script>
<script language="javascript" type="text/javascript">

<?
if($css_modello) $messaggio=$mess_css;
else $messaggio=$css_desc[0];

$desc_str="var desc=Array('".@implode("','",$css_desc)."');";
echo"$desc_str";?>

/*function preview(idPratica,fName){
	var pratica=xGetElementById(idPratica);
	if (!pratica || !pratica.value){
		alert('Nessun Numero di Pratica inserito.');
		return false;
	}
	else{
		var modello=xGetElementById('idmodello').value;
		var testo=xGetElementById('testo').value;
		var cssObj=xGetElementById('css');
		var css=cssObj.options[cssObj.selectedIndex].value;
		var param='modello='+modello+'&css='+css+'&pratica='+pratica.value+'&testo='+testo;
		xRequest('previewPDF.php',param,'response_preview','POST');
		var f=xGetElementById(fName);
		f.target='_new';
		f.action='previewPDF.php';
		f.submit();
		f.target='';
		f.action='';
	}
}*/
function response_preview(obj){
	if (obj.error){
		alert(obj.error_message);
	}
	else{
		window.open(obj.link);
	}
}


</script>

<?
if ($cols) foreach($cols as $key=>$value){
	$colonne.="Array(";
	for($j=0;$j<count($value);$j++)
		$colonne.="Array('".$value[$j]["alias_colonna"]."','".$value[$j]["colonna"]."','".$value[$j]["descrizione"]."'),";
	$colonne=substr($colonne,0,-1)."),";
}

$colonne=substr($colonne,0,-1);
?>
<script>
	var colonne= new Array(<?=$colonne?>);
</script>
</head>
<body onload="<?=$body_onload?>">
<?//echo "<pre>";print_r($colonne);?>
<div class="content">
	<form method="post" name="dati" id="modello" action="stp.editor.php">
		<h2 class="blueBanner"><?php print "Modello ".basename($file)." - Form ".array_pop(explode('.',$form));?></h2>
		<table width="100%">
			<tr>
				<td valign="top" width="60%">
					<?if ($tipo=="modelli"){?><div style="background-color:rgb(240,240,238);width:557pt;border-color:rgb(204,204,204); border-width:1 1 0 1px; border-style:solid;font-family:sans serif;padding:3 0 3 0px;">
						<table>
							<tr>
								<td width="20%" valign="bottom" class="intestazione">Tabella</td>
								<td width="20%" valign="bottom" class="intestazione">Campi</td>
								<td width="50%"  colspan="1" rowspan="2"><div id="" class=""></div></td>
								<td>
									<a href="#" onclick="insert_tag('testo',table_name.options[table_name.selectedIndex].value,column_name.options[column_name.selectedIndex].value,table_name.selectedIndex-1,column_name.selectedIndex-1)"><img src="./images/add.gif" class="bottone" alt="Inserimento del Campo Unione selezionato" style="width:26px;height:26px;" onmouseout="this.style.backgroundColor='F0F0EE';this.style.border='1px solid F0F0EE'" onmouseover="this.style.backgroundColor='B6BDD2';this.style.border='1px solid black'"></img></a>
								</td>
								<td>
									<a href="#" onclick="insert_obbl_tag('testo',table_name.options[table_name.selectedIndex].value,column_name.options[column_name.selectedIndex].value,table_name.selectedIndex-1,column_name.selectedIndex-1)"><img src="./images/add.gif" class="bottone" alt="Inserimento del Campo Unione OBBLIGATORIO selezionato.Va inserito all'interno di un ciclo." style="width:26px;height:26px;" onmouseout="this.style.backgroundColor='F0F0EE';this.style.border='1px solid F0F0EE'" onmouseover="this.style.backgroundColor='B6BDD2';this.style.border='1px solid black'"></img></a>
								</td>
								<td>
									<a href="#" onclick="inserisci_data('testo');"><img src="./images/insertdate.gif" class="bottone" alt="Inserimento della data." style="width:26px;height:26px;" onmouseout="this.style.backgroundColor='F0F0EE';this.style.border='1px solid F0F0EE'" onmouseover="this.style.backgroundColor='B6BDD2';this.style.border='1px solid black'"></img></a>
								</td>
							</tr>
							<tr>
								<td>
									<select name="table_name" id="table_name" style="background-color:rgb(240,240,238);font-size:11px" onchange="javascript:set_col(this.options[this.selectedIndex].value,this.selectedIndex)">
										<?=$print_options?>
									</select>
								</td>
								<td>
									<select name="column_name" id="column_name" style="background-color:rgb(240,240,238);font-size:11px" onchange="show_desc(table_name.selectedIndex-1,this.selectedIndex-1)">
										<option value="0">Seleziona ---></option>
									</select>
								</td> 
								<td>
									<a href="#" onclick="inizio_ciclo('testo')"><img src="./images/inizio_ciclo.gif" class="bottone" alt="Inizio Ciclo" style="width:20px;height:20px;" onmouseout="this.style.backgroundColor='F0F0EE';this.style.border='1px solid F0F0EE'" onmouseover="this.style.backgroundColor='B6BDD2';this.style.border='1px solid black'"></img></a>
								</td> 
								<td>
									<a href="#" onclick="fine_ciclo('testo')"><img src="./images/fine_ciclo.gif" class="bottone" alt="Fine Ciclo" style="width:20px;height:20px;" onmouseout="this.style.backgroundColor='F0F0EE';this.style.border='1px solid F0F0EE'" onmouseover="this.style.backgroundColor='B6BDD2';this.style.border='1px solid black'"></img></a>
								</td> 
								<td>
									<a href="#" onclick="inizio_if('testo')"><img src="./images/inizio_ciclo.gif" class="bottone" alt="Inizio Paragrafo condizionale" style="width:20px;height:20px;" onmouseout="this.style.backgroundColor='F0F0EE';this.style.border='1px solid F0F0EE'" onmouseover="this.style.backgroundColor='B6BDD2';this.style.border='1px solid black'"></img></a>
								</td> 
								<td>
									<a href="#" onclick="fine_if('testo')"><img src="./images/fine_ciclo.gif" class="bottone" alt="Fine Paragrafo condizionale" style="width:20px;height:20px;" onmouseout="this.style.backgroundColor='F0F0EE';this.style.border='1px solid F0F0EE'" onmouseover="this.style.backgroundColor='B6BDD2';this.style.border='1px solid black'"></img></a>
								</td> 
							</tr>
							<tr>
								<td>
									<b>Foglio di stile</b>
									<select name="css" id="css" onchange="javascript:xGetElementById('desc').innerHTML=desc[this.selectedIndex];">
			<? for($i=0;$i<count($css_nome);$i++){if($css_modello==$css_id[$i]) {$tmp[]="<option value=\"".$css_id[$i]."\" selected>".$css_nome[$i]."</option>";} else {$tmp[]="<option value=\"".$css_id[$i]."\">".$css_nome[$i]."</option>";} }
			echo @implode("",$tmp);  
			?>
									</select>  
								</td>
								<!--<td colspan="5">
									<button onclick="javascript:preview('idpratica','modello');"><img src="./images/preview.gif" border="0" alt="Anteprima" align="middle"></button>&nbsp;<b>NÂ° Pratica</b><input type="text" style="margin-left:5px;" value="" name="idpratica" id="idpratica">-->
								<td width="50%"  colspan="1" rowspan="2"><div id="desc" class="descrizione"><?=$messaggio?></div></td>
							</tr> 
						</table>
					</div><?}
						$oFCKeditor = new FCKeditor('testo');
						$oFCKeditor->BasePath = 'src/fckeditor/';
						$oFCKeditor->Value = $testo;
						$oFCKeditor->Create();  
						?>
					
					<hr>
					<div style="margin-top:10px;">
						<?if ($file){?><input type="submit" class="hexfield" style="background-color:rgb(204,204,204);margin:0px 0px 0px 10px;" name="azione" value="Salva" onclick="return confirm('Sei sicuro di voler sovrascrivere il modello corrente?')">
						<?}
						else{?>
						Nome del File <input type="hidden" name="file" value="<?=$file?>">  
						<input type="text" class="stiletabella" style="border:1px solid rgb(204,204,204);margin:4px 0px 0px 10px;" name="file" size="40" id="" value="<?=$file?>">
						<input type="submit" class="hexfield" style="background-color:rgb(204,204,204);margin:0px 0px 0px 10px;" name="azione" value="Salva">
						<?}?>
						<?if ($_REQUEST["id_modelli"]){?><input type="submit" name="azione" class="hexfield" style="background-color:rgb(204,204,204);margin:0px 0px 0px 10px;" value="Elimina" onclick="return confirm('Sei sicuro di voler eliminare il modello corrente?')"><?}?>
						<input type="button" class="hexfield" style="background-color:rgb(204,204,204);margin:0px 0px 0px 10px;" value="Chiudi" onclick="<?=$action?>">
					</div>
				</td>
				<td valign="top" width="40%">
					<div id="rif" name="rif" style="visibility:hidden;border:1px solid black;background-color:rgb(240,240,238);"></div>
					<input type="hidden" name="tipo" value="<?=$tipo?>">
					<input type="hidden" name="form" value="<?=$form?>">
					<input type="hidden"  id="idmodello" name="id_modelli" value="<?=$id_modelli?>">
					<input type="hidden" name="id_doc" value="<?=$id_doc?>">
					<input type="hidden" name="id" value="<?=$id?>">
					<input type="hidden" name="pratica" value="<?=$id?>">
				</td>
			</tr>
		</table>
	</form>

</div>
</body>
</html>

