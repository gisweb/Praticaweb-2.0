<?php
//print_r($_REQUEST);
include_once ("login.php");

$tabpath="oneri";
$file_config="$tabpath/tariffe";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$anno=$_POST["anno"];

if (in_array($_POST["azione"],Array("Salva","Elimina"))) {
	include("./db/db.oneri.e_tariffe.php");
}

$conn=utils::getDb();
$sql="SELECT DISTINCT tabella,funzione,descrizione FROM oneri.e_tariffe";
$stmt=$conn->prepare($sql);
$stmt->execute();
$tabelle=$stmt->fetchAll(PDO::FETCH_ASSOC);
$sql="SELECT DISTINCT anno FROM oneri.e_tariffe";
$stmt=$conn->prepare($sql);
$stmt->execute();
$anni=$stmt->fetchAll(PDO::FETCH_ASSOC);
$sql="SELECT anno,tabella,anno,  funzione, descrizione, tr, a, ie, k, valido_da, valido_a FROM oneri.e_tariffe order by anno,tabella;";
$stmt=$conn->prepare($sql);
$stmt->execute();
$data=$stmt->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);
$titolo="Tariffe Oneri";
$activeForm="oneri.e_tariffe.php";
?>
<html>
<head>
<title>Tariffe Oneri</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS(Array('form/oneri.e_tariffe'));
	utils::loadCss();
?>
</head>
<BODY>
<?php   
include "./inc/inc.page_header.php";
if ($modo=="new" || $modo=="edit") {
$anno=$_REQUEST["anno"];    
$rows[]=<<<EOT
        <tr bgcolor="#E7EFFF">
            <th align='center' width="35%"><font size='1' face='Verdana' color='#415578'><b>Descrizione</b></font></th>
            <th align='center' width="5%"><font size='1' face='Verdana' color='#415578'><b>TR</b></font></th>
            <th align='center' width="5%"><font size='1' face='Verdana' color='#415578'><b>A</b></font></th>
            <th align='center' width="5%"><font size='1' face='Verdana' color='#415578'><b>IE</b></font></th>
            <th align='center' width="5%"><font size='1' face='Verdana' color='#415578'><b>K</b></font></th>
        </tr>
EOT;
$row=<<<EOT
        <tr>
            <td style="padding:5px;">
                <input type="hidden" name="data[%s][funzione]" id="%s-funzione" value="%s"/>
                <input type="hidden" name="data[%s][descrizione]" id="%s-descrizione" value="%s"/>
                %s
            </td>
            <td style="padding:5px;"><input type="text" name="data[%s][tr]" id="%s-tr" value="%s" class="textbox" data-validation="number" size="15"/></td>
            <td style="padding:5px;"><input type="text" name="data[%s][a]" id="%s-a" value="%s" class="textbox" data-validation="number" size="15"/></td>
            <td style="padding:5px;"><input type="text" name="data[%s][ie]" id="%s-ie" value="%s" class="textbox" data-validation="number" size="5"/></td>
            <td style="padding:5px;"><input type="text" name="data[%s][k]" id="%s-k" value="%s" class="textbox" data-validation="number" size="5"/></td>
        </tr>
EOT;
    $d=($anno)?($data[$anno]):(end($data));

    foreach($d as $val){
        
        if($modo=="new"){
            $anno=$val["anno"]+1;
            $riga=sprintf($row,$val["tabella"],$val["tabella"],$val["funzione"],$val["tabella"],$val["tabella"],$val["descrizione"],$val["descrizione"],$val["tabella"],$val["tabella"],"0",$val["tabella"],$val["tabella"],$val["a"],$val["tabella"],$val["tabella"],$val["ie"],$val["tabella"],$val["tabella"],$val["k"]);
        }
        else{
            $riga=sprintf($row,$val["tabella"],$val["tabella"],$val["funzione"],$val["tabella"],$val["tabella"],$val["descrizione"],$val["descrizione"],$val["tabella"],$val["tabella"],$val["tr"],$val["tabella"],$val["tabella"],$val["a"],$val["tabella"],$val["tabella"],$val["ie"],$val["tabella"],$val["tabella"],$val["k"]);
            $inizio=$val["valido_da"];
            $fine=$val["valido_a"];
        }
        
        $rows[]=$riga;
    }

	$title=($modo=="new")?("Inserimento nuove tariffe oneri"):("Tariffe Oneri dell'anno $anno");
	unset($_SESSION["ADD_NEW"]);	

$righe=implode("",$rows);    
$tabella=<<<EOT
    <FORM method="post" id="data-form" action="$activeForm">

	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">
            $righe
            <tr >
                <td colspan="5" style="padding-top:20px;">
                    <label for="anno"><b>Anno</b></label>
                    <input type="text" class="textbox" id="anno" size="4" name="anno" value="$anno" style="margin-right:10px;" data-validation="number"/>
                
                    <label for="valido_da"><b>Inizio Validità</b></label>                    
                    <input type="text" class="textbox textbox-data" size="10" id="valido_da" name="valido_da" value="$inizio" style="margin-right:10px; data-validation="date""/>
                
                     <label for="valido_a"><b>Fine Validità</b></label>
                    <input type="text" class="textbox textbox-data" size="10" id="valido_a" name="valido_a" value="$fine" style="margin-right:10px; data-validation="date""/>
                </td>
            </tr>	
            <tr> 
                    <!-- riga finale -->
                    <td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
            </tr>
            <tr>
                    <TD valign="bottom" height="50">
                        <span id="btn-annulla"></span>
                        <span id="btn-elimina"></span>
                        <span id="btn-salva"></span>
                    </TD>
            </tr>
            <input type="hidden" name="mode" id="mode" value="$modo">
            <input type="hidden" name="azione" id="azione" value="">
		
        </TABLE>
    </FORM>
EOT;
print $tabella;
?>
<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA LIST  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->

<?php
} else {
    $heads[]="<th width='3%' align='center'><font size='1' face='Verdana' color='#415578'><b>info</b></font></th>";
    $heads[]="<th align='left'><font size='1' face='Verdana' color='#415578'><b>Anno</b></font></th>";
    foreach($tabelle as $val) $heads[]=sprintf("<th align='left'><font size='1' face='Verdana' color='#415578'><b>%s</b></font></th>",$val["funzione"]);
    $head=implode("\n\t\t\t\t\t\t\t\t",$heads);
    foreach($data as $anno=>$d){
        
        $valuta=($anno<=2001)?('£'):('&euro;');
        //$cells[]=sprintf("<td valign='middle' align='center' style='width:3%' class='printhide'><a href=\"javascript:linkToView('$activeForm',{anno:%s})\"><img border='0' src='images/pencil.png' title='Visualizza'/>&nbsp;</a></td>",$anno);
        $cells[]="<td align='center'><a href=\"javascript:linkToEdit('$activeForm',{anno:$anno})\"><span class='ui-icon ui-icon-pencil'/></a></td>";
        $cells[]="<td>$anno</td>";
        foreach($d as $k=>$v){
            $cells[]=sprintf("<td>%s %s</td>",$v["tr"],$valuta);
        }

        $rows[]="<tr>".implode("\n\t\t\t\t\t\t\t\t\t",$cells)."</tr>";
        $cells=Array();
    }
    $tableData=implode("",$rows);
    $html=<<<EOT
    <H2 class="blueBanner">$titolo</H2>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="stiletabella">		
	<tbody>
            <tr> 
		<td class="titolo">Elenco delle Tariffe</td>
                <td>
                    <form action="/$activeForm" id="new_form"  method="post">
                        <input type="hidden" value="new" name="mode">
                    </form>
                    <span id="btn-nuovo"></span>

                 </td>
            </tr>
            <tr>
                <td>
                    <table width="90%" cellspacing="1" cellpadding="1" border="0" class="stiletabella dt" id="">
                        <tr  bgcolor="#E7EFFF">
                            $head
                        </tr>
                        $tableData
                    </table>
		</td>
            </tr>
	</table>    
EOT;
}
print $html;
?>
</BODY>
</html>