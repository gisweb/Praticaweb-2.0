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
    <SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
    <script src="/js/jquery.cookie.js" type="text/javascript"></script>

    <link href="/css/ui.dynatree.css" rel="stylesheet" type="text/css">
    <script src="/js/jquery.dynatree.js" type="text/javascript"></script>
    <script>
        var jsonDataArr = <?php echo $data?>;
        
          $(document).ready(function(){
            $("#tree").dynatree({
                //title : 'Elenco dei campi unione',
                children: jsonDataArr,
                imagePath: "../images/"
            });
          });
    </script>
</head>
<body>
<div id="tree"></div>

</body>
