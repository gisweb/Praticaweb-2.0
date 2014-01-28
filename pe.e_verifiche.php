<?php
require_once 'login.php';
require_once APPS_DIR.'lib/tabella_v.class.php';

$db=appUtils::getDb();
$sql="(SELECT null::varchar as codice,'Seleziona un tipo di verifica' as nome,-1 as ordine) UNION ALL (SELECT codice,nome,ordine FROM pe.e_verifiche WHERE sorteggio=1 and enabled=1) order by ordine,nome;";
$result=$db->fetchAll($sql);
for($i=0;$i<count($result);$i++){
    $options[]=sprintf('<option value="%s">%s</option>',$result[$i]["codice"],$result[$i]["nome"]);
}
$select='<select id="tipo" class="textbox">'.implode('',$options).'</select>';
?>
<html>
<head>

<title>Elenco delle verifiche</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!--<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>-->
<?php
utils::writeJS();
utils::writeJS(Array("jquery.easyui.min","locale/easyui-lang-it","searchResultView","init.search","message"));
utils::writeCSS();
?>
<link rel="stylesheet" type="text/css" href="/css/default/easyui.css">
<link rel="stylesheet" type="text/css" href="/css/icon.css">
<!--<script type="text/javascript" src="/js/datagrid-detailview.js"></script>-->

<style>
    .c-label{
        font-weight: bold;
    }
</style>

<script language="javascript">
    function alert_message(m){
        $.messager.show({
                title:'Attenzione',
                msg:pwMessage[m],
                showType:'show',
                style:{
                    right:'',
                    bottom:''
                }
            });
    }
    function checkDraw(t){
        var result;
        $.ajax({
            url:serverUrl,
            async:false,
            method:'POST',
            data:{action:'check-draw',tipo:t},
            dataType:'JSON',
            success:function(data){
                result=data['sorteggiato'];

            }
        });
        return result;
    }
    function loadDatagrid(){
        $('#result-table').datagrid({
                title:'Elenco Pratiche Sorteggiate',
                url:searchUrl,
                method:'post',
                nowrap:false,
                columns:colsDef['draw'],
                fitColumns:false,
                pagination:true,
                autoRowHeight:true,

                queryParams:{data:{},action:'list-draw'},
            });
    }
    $(document).ready(function(){
        $(".textbox").bind("keyup",function(event){
            if(event.keyCode == 13){
                $("#avvia-ricerca").click();
            }
        });
        //$( "#result-container" ).hide();
        $('#btn-report').button({
            icons:{primary:'ui-icon-document'}
        }).bind('click',function(event){
            event.preventDefault();
            $('#frm-report').remove();
            $('body').append('<form id="frm-report" action="./services/xReport.php" method="POST" target="reportPraticaweb"><input type="hidden" value="" name="elenco" id="elencopratiche"/></form>')
            $('#elencopratiche').val($('#elenco').val())
            $('#frm-report').submit();
        });
        $('#btn-back').button({
            icons:{primary:'ui-icon-arrowreturnthick-1-w'}
        }).bind('click',function(event){
            event.preventDefault();
            $( "#result-container" ).hide( 'slide', 500 );
            $( "#ricerca" ).show( 'slide', 500 );
        });
        $('#btn-close').button({
            icons:{primary:'ui-icon-circle-close'}
        }).bind('click',function(event){
            event.preventDefault();
            closeWindow();
        });
        
        $('#btn-draw').button({
            icons:{primary:'ui-icon-search'}
        }).bind('click',function(event){
            event.preventDefault();
            var tipo_draw=$('#tipo').val();
            var tipo_text=$("#tipo option:selected").text();
            var r = checkDraw(tipo_draw);
            if (r) {
                if (!$.messager.confirm('Attenzione', sprintf(pwMessage['raffled'],{testo:tipo_text}))) return;
            }
            if (!tipo_draw) {
                alert_message('no_drawtype_selected');
                return;
            }
            $.ajax({
                url:serverUrl,
                method:'POST',
                data:{action:'draw',tipo:tipo_draw},
                dataType:'JSON',
                success:function(data){
                    loadDatagrid();
                   
                }
            });
        });
        loadDatagrid();
    });
    var result={};

var dataPost={};
</script>
</head>
<body>
<?php include "./inc/inc.page_header.php";?>
    
<div id="result-container" >
    <table id="result-table" width="100%">
    </table>
    <div style="margin-top:20px;">
        <button id="btn-close">Chiudi</button>
        <button id="btn-draw">Sorteggia Pratiche</button>
        <?php echo $select;?>
        <input type="hidden" id="elenco" value=""/>
    </div>
</div>   
    
</body>
</html>
