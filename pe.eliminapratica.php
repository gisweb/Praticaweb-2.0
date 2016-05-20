<?php
include_once ("login.php");
include_once "./lib/tabella_v.class.php";


?>
<html>
<head>
	<TITLE>Elimina Pratica Esistente</TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::loadJS(Array('easyloader','init.search'));
    utils::loadCss(Array("default/easyui","icon"));
?>
<script>
        easyloader.base='./js/';
        easyloader.css=false;
        easyloader.load('datagrid');
        easyloader.locale='it';     
</script>
<?php
    utils::loadJS(Array('form/pe.eliminapratica'));
?>
</head>
<body>

<?php
include "./inc/inc.page_header.php";	
?>
    
    <div id="ricerca">
        <H2 class="blueBanner">Ricerca pratiche da eliminare</H2>
        <div>
<?php
    $tabella=new tabella_v("pe/ricerca.tab",'standard');
    $tabella->edita();
?>
        </div>
        <div style="margin-top:20px;">
              <select id="op" class="textbox">
                  <option value="AND">Tutte le opzioni devono essere verificate</option>
                  <option value="OR">Almeno una opzione deve essere verificata</option>
              </select>
              <button style=";margin-left:20px;" id="btn-close">Chiudi</button>
              <button style=";margin-left:20px;" id="avvia-ricerca">Avvia Ricerca</button>
          </div>            
    </div>
    <div id="result-container">
        <table id="result-table" width="100%">
        </table>
        <div style="margin-top:20px;">
            <button id="btn-back">Torna alla Ricerca</button>
            <button id="btn-delete">Elimina Pratica</button>
        </div>
        <div id="delete-dialog"></div>
    </div>
</body>
</html>