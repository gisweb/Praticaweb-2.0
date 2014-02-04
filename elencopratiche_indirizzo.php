<?php
require_once "login.php";
require_once APPS_DIR.'utils/searchQuery.php';
$filter[]=($_REQUEST["via"])?("via ilike '".$_REQUEST["via"]."'"):('true');
$filter[]=($_REQUEST["civico"])?("civico ilike '".$_REQUEST["civico"]."'"):('true');
$filter[]=($_REQUEST["interno"])?("interno ilike '".$_REQUEST["interno"]."'"):('true');
$db=appUtils::getDb();
//print_array($filter);
?>

<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::writeJS();
    utils::writeCSS();
    utils::writeJS(Array('jquery.easyui.min','easyui-lang-it'));
    $filtro=implode(' AND ',$filter);
    $q=$query["pratiche-civico"];
    $sql=sprintf($q,$filtro);
    $res=$db->fetchAll($sql);
    $result=appUtils::groupData('pratiche-civici',$res);
?>
<link rel="stylesheet" type="text/css" href="/css/default/easyui.css">
<link rel="stylesheet" type="text/css" href="/css/icon.css">
<script>
    var pratiche=<?php echo json_encode($result);?>;
    
    $(document).ready(function(){
        console.log(pratiche);
        $('#result-civici').tree({
                title:'Elenco dei modelli di stampa',
                data:pratiche,
                formatter:function(node){
                    
                    if (node.children)
                        return sprintf('<b>%(text)s</b>',node);
                        
                    else
                        return sprintf('<input type="radio" value="%(id)s" name="id" class="stiletabella" style="padding:10px;">%(text)s</input>',node);
                }
                
            });
        $('#seleziona').button().bind('click',function(event){
            event.preventDefault();
            var id=$("input[name='id']:checked").val();
            window.parent.$('#pratica').val(id);
        });
    });
</script>
</head>
<body >

    <div id="res" >
        <div id="container-civici">
            <ul id="result-civici" width="100%">
                
           </ul>
            <button id="seleziona">Seleziona</button>
        </div> 


	
</body>
</html>