<?php
require_once('login.php');
require_once APPS_DIR.'lib/tabella_v.class.php';
?>
<html>
<head>

<title>Elenco Pratiche Istruttore</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php
utils::loadJS(Array("jquery.easyui.min","locale/easyui-lang-it","init.search","form/pe.ricerca_pratiche_resp"));
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
                
                <table>
                    <tr>
                         <input type="hidden" datatable="pe.avvioproc" id="op_pe-avvioproc-istr" class="search text" name="resp_proc" value="equal">
                         <input type="hidden" value=<?php echo $_SESSION["USER_ID"]; ?> id="1_pe-avvioproc-istr" name="resp_proc" class="text">
                        <td valign="middle">
                            <label for="istruita" class="title">Pratica Vista dal responsabile</label><br/>
                            <input type="hidden" datatable="pe.vista_assegnate" id="op_pe-vista_assegnate-istruita" class="search text check" name="istruita" value="equal">                           
                            <input type="radio" value="0" id="1_pe-vista_assegante-istruita" name="istruita"  data-plugins="dynamic-search" checked="checked">
                            <label for="1_pe-vista_assegante-istruita" class="value">No</label><br/>
                            <input type="radio" value="1" id="2_pe-vista_assegante-istruita" name="istruita"  data-plugins="dynamic-search">
                            <label for="2_pe-vista_assegante-istruita" class="value">SI</label><br/>
                            <input type="radio" value="" id="3_pe-vista_assegante-istruita" name="istruita"  data-plugins="dynamic-search">
                            <label for="3_pe-vista_assegante-istruita" class="value">Tutte</label><br/>
                        </td>
                    </tr>
                </table>
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