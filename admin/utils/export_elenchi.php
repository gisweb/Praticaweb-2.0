<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../../login.php';
$db=appUtils::getDb();
$sql="SELECT table_schema||'.'||table_name as tabella from information_schema.tables where table_type='BASE TABLE' and (table_schema in ('pe','oneri','stp','admin') and table_name ~* '^e[_]{1}(\w)*') or (table_name,table_schema) = ('users','admin') order by 1";
$tables=$db->fetchAll($sql);
$localDir=Array("db","export_file");
$exportDir=DATA_DIR.implode(DIRECTORY_SEPARATOR,$localDir).DIRECTORY_SEPARATOR;

?>

<html>
    <head>
        <title>ESPORTAZIONE ELENCHI</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <?php
        utils::writeJS();
        utils::writeCSS();
        ?>
        <script language="javascript">
            $(document).ready(function(){
                $("#btn_export").button({
                    icons:{primary:'ui-icon-gear'},
                    label:'Esporta'
                }).bind('click',function(event){
                    event.preventDefault();
                    var d = {tabella:new Array(),action:'export-table'};
                    $('.export-table:checked').each(function(k,v){
                        d['tabella'].push($(v).val());
                    });
                    console.log(d);
                    $.ajax({
                       url:'/services/xServer.php',
                       data:d,
                       type:'JSON',
                       method:'POST',
                       success:function(data){
                           console.log(data);
                       }
                    });
                });
            });
        </script>
    </head>
<body>
    <H3>ELENCO DELLE TABELLE ESPORTABILI</H3>    

        <div class="" id="export">        
<?php

    echo "<table style=\"width:1000px\">";
    for($i=0;$i<count($tables);$i++){
        $tabella=$tables[$i]["tabella"];
        
        if ((int)($i%5)==0) echo "<tr>";
        echo "<td style=\"width:200px;\"><input type=\"checkbox\" class=\"export-table\" value=\"$tabella\" id=\"$tabella\"/>$tabella</input></td>";
        if ((int)($i%5)==4) echo "</tr>";
    }
    echo "</table>";
?>
            <button id="btn_export"></button>
        </div>  
    
</body>