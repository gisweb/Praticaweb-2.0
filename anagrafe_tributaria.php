<?php
require_once 'login.php';
$conn = utils::getDb();
$sql="SELECT DISTINCT anno_riferimento as anno FROM anagrafe_tributaria.pratiche WHERE coalesce(anno_riferimento,0)>0 ORDER BY 1;";
$stmt=$conn->prepare($sql);
$stmt->execute();
$anni=$stmt->fetchAll();
$sql="SELECT anno_riferimento,tipo_richiesta,coalesce(totale,0) as totale FROM (SELECT anno_riferimento,tipo_richiesta FROM (SELECT 0 as tipo_richiesta UNION SELECT 1 as tipo_richiesta) A,(SELECT DISTINCT anno_riferimento FROM anagrafe_tributaria.pratiche WHERE coalesce(anno_riferimento,0)>0) B ORDER BY 1,2) X LEFT JOIN 
(SELECT anno_riferimento,tipo_richiesta, coalesce(count(*),0) as totale FROM anagrafe_tributaria.pratiche WHERE coalesce(anno_riferimento,0)>0 GROUP BY 1,2) Y USING (anno_riferimento,tipo_richiesta)
ORDER BY 1,2;";
$stmt=$conn->prepare($sql);
$stmt->execute();
$res=$stmt->fetchAll(PDO::FETCH_ASSOC);
for($i=0;$i<count($res);$i++){
    $d=$res[$i];
    $tot[$d["anno_riferimento"]][(string)($d["tipo_richiesta"]+1)]=(int)$d["totale"];
}
foreach($tot as $k=>$v) 
    $totali[$k]=Array((string)($tot[$k][1]+$tot[$k][2]),(string)$tot[$k][1],(string)$tot[$k][2]);
foreach($anni as $val){
    $val=$val[0];
    $html["anno"].="<option value=\"$val\">$val</option>";
}    
$html["anno"]="<select id=\"anno\" name=\"anno\" class=\"textbox\"  style=\"$sel_size\"><option value=\"-1\" selected>Seleziona ===></option><option value=\"0\">Tutti</option>".$html["anno"]."</select>";
for($i=5;$i<=30;$i=$i+5) 
    $html["limit"].="<option value=\"$i\">$i</option>";
?>
<HTML>
<HEAD>
    <TITLE><?=$titolo?></TITLE>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?php
	utils::loadJS(Array('form/anagrafe'));
	utils::loadCss(Array('anagrafe'));
    ?>
    <script>
        var data = <?php echo json_encode($totali);?>
    </script>
</HEAD>
<BODY>
    <DIV id="search">
        <h2 class="blueBanner">Pagina di ricerca dell'anagrafe tributaria</h2>
        <table width="80%" class="stiletabella">
            <tr>
		<td width="10%" bgColor="#728bb8">Tipo di Pratica</td>
                <td width="20%">
                    <select style="<?=$sel_size?>" class="textbox" name="tipo_pratica" id="tipo_pratica">
                        <option value="-1" selected>Seleziona ===></option>
                        <option value="0">Tutte</option>
                        <option value="1">Permessi di Costruire</option>
                        <option value="2">D.I.A.</option>
                    </select>
                </td>
                <td rowspan="2" valign="top"><div>Totale Pratiche : <span id="counter"></span></div> </td>
                 
            </tr>
            <tr>
                <td bgColor="#728bb8">Anno</td>
                <td><?=$html["anno"];?></td>
            </tr>
            <tr>
		<td colspan="3" style="padding-top:10px;"><hr>
                    <button style="margin-right:40px;" id="btn-close">Chiudi</button>
                    <!--<input type="submit" value="Avvia Ricerca" name="azione" class="hexfield" style="width:120px;" onclick="return valida();">-->
                    <button  id="btn-search">Avvia Ricerca</button>
                </td>
            </tr>
        </table>
	<input type="hidden" value="search" name="mode">
	<input type="hidden" value="0" name="offset">

    </DIV>
    <DIV id="message"></DIV>
    <DIV id="result">
        <table id="table_result" width="99%"></table>
        
    </DIV>
</BODY>
</HTML>    