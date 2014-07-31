<?php
//print_r($_REQUEST);
include_once ("login.php");

$tabpath="oneri";
$file_config="$tabpath/tariffe";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$anno=$_POST["anno"];

if ($_POST["azione"]=="Salva") {
	include("./db/db.oneri.e_tariffe.php");
	if (!$Errors) {
		$modo="view";
	}
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
	utils::loadJS();
	utils::loadCss();
?>
</head>
<BODY>
<?php   
include "./inc/inc.page_header.php";
if ($modo=="new" || $modo=="edit") {
$row=<<<EOT
        <tr>
            <td><input type="text" name="%s[tr]" id="%s-tr" value="%s" class="textbox" size="20"/></td>
            <td><input type="text" name="%s[a]" id="%s-a" value="%s" class="textbox" size="20"/></td>
            <td><input type="text" name="%s[ie]" id="%s-ie" value="%s" class="textbox" size=""/></td>
            <td><input type="text" name="%s[k]" id="%s-k" value="%s" class="textbox" size=""/></td>
        </tr>
EOT;
	$title=($modo=="new")?("Inserimento nuove tariffe oneri"):("Tariffe Oneri dell'anno $anno");
	unset($_SESSION["ADD_NEW"]);	
?>
    <FORM method="post" action="<?php echo $activeForm;?>">
<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
			
            <tr> 
                    <!-- riga finale -->
                    <td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
            </tr>
            <tr>
                    <TD valign="bottom" height="50">
                    <input name="azione" type="submit" class="hexfield" tabindex="14" value="Salva">
                    <input name="azione" type="submit" class="hexfield" tabindex="14" value="Annulla">
                    </TD>
            </tr>
            <input type="hidden" name="mode" value="<?=$modo?>">
			
		
        </TABLE>
    </FORM>

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
                    <button class="button_titolo ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" id="btn_25050" role="button" aria-disabled="false"><span class="ui-button-icon-primary ui-icon ui-icon-plusthick"></span><span class="ui-button-text">Nuovo</span></button>
                    <script>
                        jQuery('#btn_25050').button({
                            icons:{
                                primary:'ui-icon-plusthick'
                            },
                            label:'Nuovo'
			}).click(function(){
				$('#new_form').submit();
			});
                    </script>
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