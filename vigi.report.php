<?php
require_once('login.php');
require_once APPS_DIR.'lib/tabella_v.class.php';
$tabpath="vigi";
?>
<html>
<head>

<title>Report Pratiche di Vigilanza</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php
utils::loadJS(Array("jquery.easyui.min","locale/easyui-lang-it","searchResultView.$tabpath","init.search.$tabpath","form/report.$tabpath"));
utils::loadCss(Array("default/easyui","icon"));
?>

<!--<script type="text/javascript" src="/js/datagrid-detailview.js"></script>-->

<style>
    .c-label{
        font-weight: bold;
    }
</style>
</head>
<body>
<?php include "./inc/inc.page_header.php";?>
    <FORM id="ricerca" name="ricerca" method="post" action="vigi.report.php">
 	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Report Mensile Pratiche di Vigilanza</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 			
				<!-- ricerca base pratica -->
<?php

$tabella=new tabella_v("$tabpath/ricerca_report.tab",'standard');
$tabella->edita();

?>
			</td>
		  </tr>
		  <tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   </tr>  
        </table>
            <div style="margin-top:20px;">
                <button style=";margin-left:20px;" id="btn-close">Chiudi</button>
                <button style=";margin-left:20px;" id="avvia-ricerca">Avvia Ricerca</button>
            </div>                    
    </FORM>
<div id="result-container" >
    <table id="result-table" width="100%">
    </table>
    <div style="margin-top:20px;">
        <button id="btn-back">Torna alla Ricerca</button>
        <button id="btn-report">Crea Report</button>
        
        <input type="hidden" id="elenco" value=""/>
    </div>
</div>   
    
</body>
</html>
