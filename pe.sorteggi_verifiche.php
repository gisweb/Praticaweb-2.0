<?php
require_once 'login.php';
require_once APPS_DIR.'lib/tabella_v.class.php';
$now=date("d-m-Y");
$db=appUtils::getDb();
$sql="(SELECT null::varchar as codice,'Seleziona un tipo di verifica' as nome,-1 as ordine,'Nessuno' as tipo_sorteggio,null as totale_sorteggi) UNION ALL (SELECT codice,nome,ordine,tipo_sorteggio,totale_sorteggi FROM pe.e_verifiche WHERE sorteggio=1 and enabled=1) order by ordine,nome;";
$result=$db->fetchAll($sql);
for($i=0;$i<count($result);$i++){
    $options[]=sprintf('<option value="%s">%s</option>',$result[$i]["codice"],$result[$i]["nome"]);
    $selectData[$result[$i]["codice"]]=($result[$i]["tipo_sorteggio"]=='Totali')?($result[$i]["totale_sorteggi"]." Pratiche"):(($result[$i]["totale_sorteggi"]*100)."% delle pratiche");
}
$select=implode('',$options);
?>
<html>
<head>

<title>Elenco delle verifiche</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
//utils::loadJS();
utils::loadJS(Array("jquery.easyui.min","locale/easyui-lang-it","searchResultView","init.search","message","form/pe.sorteggi_verifiche"));
utils::loadCss();
?>
<link rel="stylesheet" type="text/css" href="/css/default/easyui.css">
<link rel="stylesheet" type="text/css" href="/css/icon.css">
<!--<script type="text/javascript" src="/js/datagrid-detailview.js"></script>-->

<style>
    .c-label{
        font-weight: bold;
    }
</style>
</head>
<script>
var selectInfo=<?php echo json_encode($selectData);?>;
</script>
<body>
<?php include "./inc/inc.page_header.php";?>
    
<div id="result-container" >
    <table id="result-table" width="100%">
    </table>
    <div style="margin-top:20px;">
        <button id="btn-close">Chiudi</button>
        <button id="btn-draw">Sorteggia Pratiche</button>
        <select id="tipo" class="textbox">
        <?php echo $select;?>
        </select>
        <input type="text" size="10" class="textbox" value="<?php echo $now;?>" name="data_sorteggio" id="data_sorteggio"/>
        <span style="margin-left:20px;">
            <label for="sorteggiabili" style="color: #000066;font-family: Verdana,Geneva,Arial,sans-serif;font-size: 11px;"><b>Pratiche sorteggiabili : </b>
            <span id="sorteggiabili" style="font-weight:bold;color:red;width:100px;"></span></label>
        </span>
        <span style="margin-left:20px;">
            <label for="info-sorteggi" style="color: #000066;font-family: Verdana,Geneva,Arial,sans-serif;font-size: 11px;"><b>Modalità di sorteggio : </b>
            <span id="info-sorteggi" style="font-weight:bold;color:red;"></span></label>
        </span>
        <input type="hidden" id="elenco" value=""/>
    </div>
</div>   
    
</body>
</html>
