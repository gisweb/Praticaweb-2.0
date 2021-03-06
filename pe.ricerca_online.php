<?php
require_once('login.php');
require_once APPS_DIR.'lib/tabella_v.class.php';
$baseFilterFile = APPS_DIR."searchFilter.online.php";
$localFilterFile = DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."include".DIRECTORY_SEPARATOR."searchFilter.online.php";
?>
<html>
<head>

<title>Elenco Pratiche Presentate Online</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php
utils::loadJS(Array("jquery.easyui.min","locale/easyui-lang-it","init.search","form/pe.ricerca_online"));
utils::loadCss(Array("default/easyui","icon"));
?>

<!--<script type="text/javascript" src="/js/datagrid-detailview.js"></script>-->

<style>
    .c-label{
        font-weight: bold;
    }
    label.title{
        font-size:13px;
        font-weight: bold;
        color:#0e2d5f;
    }
    label.value{
        font-size:11px;
        
        color:#0e2d5f;
    }
    input[type=radio]{
        background-color:#0e2d5f;
        display: inline-block;  
        border-radius: 10px;
        width: 10px;
        height: 10px;
    }
</style>
</head>
<body>
<?php include "./inc/inc.page_header.php";?>

<div id="result-container" >
    <table width="100%">
        <tr>
            <td width="90%" valign="top">
                <table id="result-table" width="100%">
                </table>
            </td>
            <td valign="top">
<?php
    if (file_exists($localFilterFile)){
        require_once $localFilterFile;
    }
    else{
        require_once $baseFilterFile;
    }
?>                

            </td>
        </tr>
    </table>
    
    <div style="margin-top:20px;">
        <button id="btn-close">Chiudi</button>
        <button id="btn-reload">Ricarica Pagina</button>
        <button id="btn-report">Crea Report</button>
        
        <input type="hidden" id="elenco" value=""/>
    </div>
</div>   
    
</body>
</html>
