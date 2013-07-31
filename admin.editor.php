<?
include "login.php";
$imm="cipressa.png";
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

/*Parte dedicata a creazione delle liste dei campi unione*/

$sql="SELECT DISTINCT table_name,column_name FROM information_schema.columns WHERE (column_name<>'id' AND column_name<>'pratica' AND column_name<>'chk' AND column_name<>'tmsins' AND column_name<>'tmsupd') AND table_schema='stp'AND table_name IN (SELECT table_name FROM information_schema.views WHERE table_schema='stp')";

if (!$db->sql_query($sql)) echo "$sql<br>";
$ris=$db->sql_fetchrowset();
$viste[]="Seleziona Vista --->";
foreach($ris as $val){
	$viste[]=$val["table_name"];
	$col[$val["table_name"]][]="'".$val["column_name"]."'";
}
$viste=array_unique($viste);

foreach($viste as $val){
	$option_viste.="\n\t\t\t\t<option value=\"$val\">$val</option>";
	
	if ($val!="Seleziona Vista --->"){
		$popolazione=implode(",",$col[$val]);
		$script.="\tcolumns['$val']=new Array($popolazione);\n";
	}
}
/*FINE*/

	$inizio_doc=stripslashes("<html>
<head>
	<LINK media=\"screen\" href=\"./src/modelli.css\" type=\"text/css\" rel=\"stylesheet\"><LINK media=\"print\" href=\"src/styles_print.css\" type=\"text/css\" rel=\"stylesheet\">
	<SCRIPT language=\"javascript\" src=\"src/window.js\" type=\"text/javascript\"></SCRIPT>
</head>
<body>\n\t");
$fine_doc="\n</body>\n</html>";

if ($_POST["modello"]){		//CASO DI NUOVO MODELLO DI DOCUMENTO
	$margini=$_POST["margini"];
	$nome=$_POST["modello"];
	if($_POST["intestazione"]) $intestazione=stripslashes("\n\t\t<TABLE>
<TBODY>
<TR>
<TD><IMG style=\"WIDTH: 150px; HEIGHT: 169px\" height=211 src=\"cipressa.png\" width=47 border=0></TD>
<TD>
<DIV><FONT size=5>
<P align=center>COMUNE DI CIPRESSA</P></FONT><B><FONT size=4>
<P align=center>Provincia di Imperia</P></FONT><FONT size=5>
<P align=center>Ufficio Tecnico</P></FONT><FONT size=1>
<P align=center>Via Matteotti, 9 - 18010 CIPRESSA </P></B></FONT><FONT size=2>
<P></FONT><B><FONT size=1>P.I. 00244820080 - Tel. 0183/98005 - 98531 Fax 0183/98006</P></B></FONT></DIV></TD></TR></TBODY></TABLE>");
	
	$file=MODELLI_DIR.$_REQUEST["file"];
	$margini=21-2*$margini;
	$txt="\n\t<DIV STYLE=\"width:$margini cm;\">$intestazione\n\t</DIV>";
	$testo=$inizio_doc.$txt.$fine_doc;
}
elseif ($_REQUEST["file"]) {
	if ($_REQUEST["stampe"]) {
		$file=STAMPE_DIR.$_REQUEST["file"];
		$stp=1;
	}
	else
		$file=MODELLI_DIR.$_REQUEST["file"];
	$handle = fopen($file, "r");
	$testo=fread($handle,filesize($file));
	fclose($handle);
}
	
if (!$_POST["filename"] and !$_POST["modello"]){
	$filename=explode("/",$file);
	$filename=explode(".",$filename[count($filename)-1]);
	$filename=$filename[0];
}
elseif($_POST["filename"]) $filename=$_POST["filename"];
elseif($_POST["modello"]) $filename=$_POST["modello"];
	
if ($_POST["azione"]=="Salva"){
	$txt=stripslashes($_POST["testo"]);
	if ($_REQUEST["stampe"]) { 
		$file=STAMPE_DIR.$filename.".html";
		$stp=1;
	}
	else
		$file=MODELLI_DIR.$filename.".html";
	$testo=$inizio_doc.$txt.$fine_doc;
	//Salvataggio del file
	$handle = fopen($file, "w+");
	fwrite($handle,$testo);
	fclose($handle);
}

?>
<html>
<head>
<title>editor</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language="JavaScript">
var bHtmlMode = false;
var str_iFrameDoc = (document.all)? "document.frames(\"Composition\").document\;": "document.getElementById(\"Composition\").contentDocument\;";
var columns=new Array();
// Inizializzazione
onload = initialize;
function initialize() {

	iFrameDoc = eval(str_iFrameDoc);
	frames.Composition.document.write(document.getElementById("testo").value);
	iFrameDoc.designMode = "On";
	document.getElementById("switchMode").checked = false;
	if (!document.all) {
	    document.getElementById("taglia").style.visibility = "hidden";
	    document.getElementById("copia").style.visibility = "hidden";
	    document.getElementById("incolla").style.visibility = "hidden";
	}
}

// Porta il focus al riquadro di testo
function setFocus() {
if (document.all)
	document.frames("Composition").focus();
else
	document.getElementById('Composition').contentWindow.focus()
return;
}

// Controlla se la toolbar Ãš abilitata nella modalitÃ  testo
function validateMode() {
	if (! bHtmlMode)
		return true;
	alert("Deselezionare \"Visualizza HTML\" per utilizzare le barre degli strumenti");
	
	setFocus();
	return false;
}

// Formatta il testo
function formatC(what,opt) {
	if (!validateMode())
		return;

	iFrameDoc = eval(str_iFrameDoc);
	iFrameDoc.execCommand(what,false,opt);
 
	setFocus();
}

//Scambia tra la modalitÃ  testo e la modalitÃ  HTML.
function setMode(newMode) {
	var testo;
	
	bHtmlMode = newMode;
	
	iFrameDoc = eval(str_iFrameDoc);
	riquadro = iFrameDoc.body;
	
	if (document.all) {
		if (bHtmlMode) {
			testo = riquadro.innerHTML;
			riquadro.innerText = testo; 
		} else {
			testo = riquadro.innerText;
			riquadro.innerHTML = testo;
		}
		
	} else if(document.getElementById && document.createTextNode) {
		if (bHtmlMode) {
			testo = document.createTextNode(riquadro.innerHTML);
			riquadro.innerHTML = "";
			riquadro.appendChild(testo);
		} else {
			testo = document.createRange();
			testo.selectNodeContents(riquadro);
			riquadro.innerHTML = testo.toString();
		}	
	}

	setFocus();
}
function salva(){
	iFrameDoc = eval(str_iFrameDoc);
	riquadro = iFrameDoc.body;
	txt = riquadro.innerHTML;
	dati.testo.value=txt;
	//alert(txt)
}
function popola(table){
	col=document.getElementById('colonne');
	for(i=0;i<columns[table].length;i++){
		newOpt = document.createElement("OPTION");
		newOpt.text = columns[table][i];
		newOpt.value = columns[table][i];
		col.options.add(newOpt);
	}
}
function insert_tag(flag,value){
	col=document.getElementById('colonne');
	viste=document.getElementById('viste');
	if (flag=="C"){
		n=col.options.length;
		newOpt = document.createElement("OPTION");
		newOpt.text='Seleziona campo -- >';
		newOpt.value='';
		col.options.add(newOpt);
		for(j=0;j<n;j++){
			col.remove(0);
		}
		
		iFrameDoc = eval(str_iFrameDoc);
		riquadro = iFrameDoc.body;
		//
		//pippo=document.createTextNode(testo);
		document.createElement("<span id='"+value+"'>"+value+"</span> ");
		
		
		col.selectedIndex=0;
		viste.selectedIndex=0;
	}
	
}
function indenta(direzione){
	var txt = parent.frames.Composition.document.selection.createRange();
	left = "";
		while ((left == "") || (left == "")){
			left=prompt ("Inserire il margine sinistro ?", "");
		}

	var indent="<BLOCKQUOTE STYLE='margin-right:0cm;margin-left:"+left+"cm;'>\n\t"+txt.htmlText+"\n</BLOCKQUOTE>\n";
	txt.pasteHTML(indent);
	txt.select()
	parent.frames.Composition.focus();
}
function insert_ciclo(){

	var txt = parent.frames.Composition.document.selection.createRange();
	alert(txt.text);
	ciclo="\n\r<SPAN class=ciclo name=ciclo>"+txt.htmlText+"</SPAN>\n\r";
	//txt.pasteHTML(ciclo);
	alert(ciclo+' -- '+ciclo.length);
	txt.select()
	parent.frames.Composition.focus();
}
function struct(){
//var txt=parent.frames.Composition.document.body;
alert(parent.frames.Composition.document.body.text);
}
</script>
</head>

<body bgcolor="#FFFFFF" topmargin="0" marginheight="2">
<form action="admin.editor.php" method="POST" name="dati">
<input type="hidden" name="blck" value="10">
<table class="stiletabella" width="100%">
	<tr>
		<td colspan="2"><H2 class="bluebanner">Editor dei Modelli di Stampa</H2></td>
	</tr>
	<tr>
		<td>Nome del Modello  <input type="text" name="filename" value="<?=$filename?>"></td>
		<td><input type="submit" class="hexfield" name="azione" value="Salva" onclick="salva();">
			
			<div id="dwindow" style="position:absolute;background-color:#EBEBEB;cursor:hand;left:0px;top:0px;display:none;z-index:0">
				<div align="right" style="background-color:navy">
					<img src="images/wmax.gif" id="maxname" onClick="maximize()">
					<img src="images/wclose.gif" onClick="closeit()">
				</div>
				<div id="dwindowcontent" style="height:100%">
					<iframe id="cframe" src="" width=100% height=100%></iframe>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="button" class="hexfield" value="Chiudi" onclick="window.opener.focus();window.close();"></td>
	</tr>
</table>
<textarea name="testo" id="testo" cols="1" rows="1" style="display:none"><?=$testo?></textarea>
<input type="hidden" name="stampe" value=<?=$stp?>>
</form>
<hr>
<form action="#" method="POST" name="composeForm">
<table border="0" cellpadding="5" cellspacing="0" width="100%" class="stiletabella">
	<tr>
		<td >
			<?include "admin.pulsanti.php"?>
			<center>
			<iframe class="Composition" STYLE="width:25cm;" id="Composition" height="300" src="">
			</iframe>
			</center>
		</td>
	</tr>
</table>
</body>
</html>