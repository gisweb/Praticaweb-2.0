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
?>
<html>
<head>
    <title>ELENCO MODELLI DI STAMPA</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?php
	utils::loadJS();
	utils::loadCss();
?>
    <script src="/js/jquery.cookie.js" type="text/javascript"></script>

    <link href="/css/ui.dynatree.css" rel="stylesheet" type="text/css">
    <script src="/js/jquery.dynatree.js" type="text/javascript"></script>
    <script>
        var jsonDataArr = <?php echo $data?>;
        
          $(document).ready(function(){
            $("#btn_close").button({
                label:'Chiudi',
                icons:{'primary':'ui-icon-circle-close'}
            }).bind('click',function(event){
                event.preventDefault();
                window.close();
            });
            $("#tree").dynatree({
                //title : 'Elenco dei campi unione',
                children: jsonDataArr,
                imagePath: "../images/"
            });
          });
    </script>
</head>
<body>
<?include "./inc/inc.page_header.php";	?>
<h2 class="blueBanner">Elenco dei campi unione dei modelli di stampa</h2>
<div id="tree" style="margin-bottom: 10px;padding:10px;"></div>
<div id="btn_close" style="margin-left:5px;margin-bottom: 20px;"/>
</body>
