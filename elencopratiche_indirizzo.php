<?php
require_once "login.php";
require_once APPS_DIR.'utils/searchQuery.php';
$filter[]=($_REQUEST["via"])?("via ilike '".addslashes($_REQUEST["via"])."'"):('true');
$filter[]=($_REQUEST["civico"])?("civico ilike '".$_REQUEST["civico"]."'"):('true');
$filter[]=($_REQUEST["interno"])?("interno ilike '".$_REQUEST["interno"]."'"):('true');
$db=appUtils::getDb();

?>

<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php

    utils::loadJS(Array('jquery.easyui.min','easyui-lang-it'));
    utils::loadCSS(Array('default/easyui','icon'));
    $filtro=implode(' AND ',$filter);
    $q=$query["pratiche-civico"];
	
    $sql=sprintf($q,$filtro);
	//echo $sql;return;
    $res=$db->fetchAll($sql);
    $result=appUtils::groupData('pratiche-civici',$res);
?>
<script>
    var pratiche=<?php echo json_encode($result);?>;
    
    $(document).ready(function(){
        if (pratiche.length>0){
                    $('#result-civici').tree({
                                    title:'Elenco dei modelli di stampa',
                                    data:pratiche,
                                    formatter:function(node){

                                            if (node.children)
                                                    return sprintf('<b>%(text)s</b>',node);

                                            else
                                                    return sprintf('<input type="radio" value="%(numero)s" data-interno="%(interno)s" data-civico="%(civico)s" name="id" class="stiletabella" style="padding:10px;">%(text)s</input>',node);
                                    },
                                    onLoadSuccess:function(){
                                            window.parent.$('#waiting').dialog('close');
                                            window.parent.$('#result').dialog({
                                                    width:800,
                                                    height:600,
                                                    title:'Cartellina dell\'indirizzo'
                                            });
                                    }
                            });
            }
            else{
                    window.parent.$('#waiting').dialog('close');
                    $('#container-civici').html('<b>Nessuna pratica trovata </b>');
                    $('#seleziona').text('Chiudi');
                    window.parent.$('#result').dialog({
                                    width:800,
                                    height:600,
                                    title:'Cartellina dell\'indirizzo'
                            });


            }
        $('#seleziona').button().bind('click',function(event){
            event.preventDefault();
            var id=$("input[name='id']:checked").val();
			if (id){
				var d = $("input[name='id']:checked").data();
				var i = d["interno"].toString().replace('n.i.','');
				var c = d["civico"].toString().replace('n.c.','');
				window.parent.$('#rif_pratica').val(id);
				window.parent.$('#interno').val(i);
				window.parent.$('#civico').val(c);
			}
			window.parent.$('#result').dialog('close');
        });
		
    });
</script>
</head>
<body >

    <div id="container-civici" style="height:450px;margin-bottom:10px;overflow:auto;">
		<ul id="result-civici" width="100%">
			
	   </ul>
		
    </div> 
	<button id="seleziona">Seleziona</button>


	
</body>
</html>