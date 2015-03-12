<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


include_once "./login.php";
include_once "lib/stampe.word.class.php";

$doc=new wordDoc(0,0);
$data=$doc->viewFieldList();
//$data=  appUtils::groupData("print-field",$data);
?>
<html>
<head>
    <title>ELENCO MODELLI DI STAMPA</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
 <?php
    utils::loadJS(Array('jquery.easyui.min','easyui-lang-it'));
    utils::loadCSS(Array('default/easyui','icon'));
    print_array($data1);
?>
    
    <script>
        var jsonDataArr = <?php echo json_encode($data);?>;
        
          $(document).ready(function(){
            $("#btn_close").button({
                label:'Chiudi',
                icons:{'primary':'ui-icon-circle-close'}
            }).bind('click',function(event){
                event.preventDefault();
                window.close();
            });
            $('#tree').tree({
                data:jsonDataArr,
                animate:true
            });
          });
    </script>
</head>
<body>
<?php 
include "./inc/inc.page_header.php";	
?>
<h2 class="blueBanner">Elenco dei campi unione dei modelli di stampa</h2>
<ul id="tree" style="margin-bottom: 10px;padding:10px;"></ul>
<div id="btn_close" style="margin-left:5px;margin-bottom: 20px;"/>
</body>
