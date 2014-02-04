<?php
require_once "login.php";
?>
<html>
    <head>
        <?php
            utils::writeJS();
            utils::writeCSS();
        ?>
        <script>
            $(document).ready(function(){
                $('#elenco_pratiche').button({
                    label:'pratiche'
                }).bind('click',function(event){
                    var baseURL='/elencopratiche_indirizzo.php'
                    event.preventDefault();
                    var via=$("#via").val();
                    var civico=$("#civico").val();
                    var interno=$("#interno").val();
                    var url=baseURL + '?via='+encodeURIComponent(via)+'&civico='+encodeURIComponent(civico)+'&interno='+encodeURIComponent(interno);
                    $('#result').html('<iframe frameBorder=0 style="width:99%;OVERFLOW: visible;" marginWidth=0  marginHeight=0 src="' + url + '"></iframe>');
                    alert(url);
                });
            });
        </script>
    </head>
    <body>
        <div><input id="via" size="40" value="VIA PIETRO PALEOCAPA"></input></div>
        <div><input id="civico" size="4"></input></div>
        <div><input id="interno" size="3"></input></div>
        <div><input id="pratica" ></input></div>
        <button id="elenco_pratiche"></button>
        <div id="result" style="width:800px;height:600px;">
            
        </div>
    </body>
</html>