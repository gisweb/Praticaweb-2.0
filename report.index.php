<?php
include_once("login.php");
require_once APPS_DIR."/lib/tabella_v.class.php";
require_once APPS_DIR."/lib/tabella_h.class.php";
$tabpath="report";
$azione=strtolower($_REQUEST["azione"]);
$filter="";
$cols=Array();
if(in_array($azione,Array('cerca','report'))){
    include APPS_DIR."db/search.report.ap.php";
    $risultati=new tabella_h("$tabpath/report_ap.tab",'list');
    $risultati->set_dati($filter);
    $cols=$risultati->defColDataTable();
}
if ($azione=='report'){
    require_once APPS_DIR."plugins/PHPExcel.php";
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $objPHPExcel = $objReader->load("test.xlsx");
    $objPHPExcel->getActiveSheet()->fromArray($risultati->array_dati);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('output.xlsx');
}
?>
<html>
<head>
<title>Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language="javascript">


var data=<?php echo json_encode($risultati->array_dati);?>;

var aoCols=<?php echo json_encode($cols);?>;

function search(obj){
    if (!$('#tipo').val()){
        $('<div> <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>Selezionare una tipologia di pratica!</div>').dialog({'title':'Attenzione','modal':true});
        return false;
    }
    return true;
}
function printReport(){
    if (!$('#tipo').val()){
        $('<div> <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>Selezionare una tipologia di pratica!</div>').dialog({'title':'Attenzione','modal':true});
        return false;
    }
    return true;
}
$(document).ready(function(){
    $('#risultati').dataTable({ 
        aaData:data,
        aoColumns:aoCols,
        bJQueryUI:true,
        oLanguage: {
            sUrl: "/js/dataTable.lang.it.js"
        },
        sScrollY:'350px',
        sDom:'<"toolbar"><"H"f>t<"F"lip>'
    });
});
</script>
<style>
    #risultati th{
        font-size:13px;
        font-weight:bold;
    }
    #risultati td{
        font-size:12px;
    }
</style>
</head>
<body>
<?php
include "./inc/inc.page_header.php";
?>
    <h2 class="blueBanner">Parametri di Ricerca</h2>
<?php
    
    $tabella=new tabella_v("$tabpath/report_ap.tab",'search');

    if (strtolower($azione)=='cerca'){
        $tabella->set_dati($_REQUEST);
    }
    echo "<form id=\"frmSearch\" method=\"POST\">";
    $tabella->edita();
    echo "</form>";
    if ($azione=="cerca"){
        echo "<table id=\"risultati\" style=\"width:98%;\">";
        
        /*$risultati->set_titolo("Risultati della Ricerca");
        $risultati->get_titolo();
        $risultati->elenco();*/
        echo "</table>";

    }
?>
</body>
</html>