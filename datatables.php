<?php
require_once "login.php";
require_once "./lib/tabella_v.class.php";
$tabellav=new tabella_v('ricerca_test.tab','new');
$tabellav->set_titolo('Ricerca Pratiche');
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
  
  <style>
    #resultTable td{
      font-size:11px;
      font-family:Verdana,Geneva,Arial,sans-serif;
    }
    #resultTable th{
      font-size:12px;
      font-weight:bold
      font-family:Verdana,Geneva,Arial,sans-serif;
    }
    .bottom{
      font-size:12px;
      font-family:Verdana,Geneva,Arial,sans-serif;
    }
    #resultTable{
      width:100% !important;
    }
  </style>
  <SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
  <script src="js/init.search.dataTables.js"></script>
</head>
<body>
<?php
include "./inc/inc.page_header.php";
?>
    <div id="container" style="width:99%;">
      <div id="tabs" style="width:100%;">
        <div id="searchTab">
          <?php
            $tabellav->get_titolo();
            $tabellav->edita();
          ?>
          
        </div>
        <div id="resultTab" style="width:100%;display:none;">
          <table id="resultTable" class="display" cellspacing="0" cellpadding="0" border="0"> </table>
            <button id="back"></button>
            <script>
                $('#back').button({
                    'icons':{'primary':'ui-icon-close'},
                    label:'Indietro'
                }).click(function(){
                    $('#searchTab').show();
                    $('#resultTab').hide();
                });
            </script>
        </div>
      </div>
    </div>
</body>
</html>