<html>
<head>
    <title>Test EasyUI</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    require_once "login.php";
    utils::writeCSS();
    utils::writeJS();
?>
    <link rel="stylesheet" type="text/css" href="/css/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="/css/icon.css">
    <script type="text/javascript" src="/js/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="/js/locale/easyui-lang-it.js"></script>
    <script type="text/javascript" src="/js/datagrid-detailview.js"></script>
    <style>
        div.center {
            width:100%; 
            padding-top:3%;
            padding-left:15%; 
            padding-right:15%;
          }
    </style>
    <script>
        $(document).ready(function(){
            var colsDef={
                civici:[[
                    {title:'',field:'viacivico'},
                    {title:'Via',field:'via',sortable:true,width:500},
                    {title:'Civico',field:'civico',sortable:true,width:100}
                ]],
                pratica:[]
            }
            $('#report-table').datagrid({
                url:'/services/xSearchNew.php',
                queryParams:{ricerca:'civici'},
                method:'post',
                //nowrap:false,
                columns:colsDef['civici'],
                fitColumns:true,
                pagination:true,
                autoRowHeight:true,
                view: detailview,
                detailFormatter:function(index,row){
                    return '<div style="padding:2px"><table class="ddv"></table></div>';
                },
                onExpandRow: function(index,row){
                    var ddv = $(this).datagrid('getRowDetail',index).find('table.ddv');
                    ddv.datagrid({
                        url:'/services/xSearchNew.php',
                        queryParams:{ricerca:'pratiche-civico',viacivico:row.viacivico},
                        fitColumns:true,
                        singleSelect:true,
                        rownumbers:true,
                        loadMsg:'',
                        height:'auto',
                        columns:[[
                            {field:'orderid',title:'Order ID',width:100},
                            {field:'quantity',title:'Quantity',width:100},
                            {field:'unitprice',title:'Unit Price',width:100}
                        ]],
                        onResize:function(){
                            $('#dg').datagrid('fixDetailRowHeight',index);
                        },
                        onLoadSuccess:function(){
                            setTimeout(function(){
                                $('#dg').datagrid('fixDetailRowHeight',index);
                            },0);
                        }
                    });
                    $('#dg').datagrid('fixDetailRowHeight',index);
                }
            });
        });
    </script>
</head>
<body>
    <div class="center">
        <table id="report-table" style="width:800px;"></table>
        
<!-- <table class="easyui-datagrid" title="Basic DataGrid"  style="width:800px;"
data-options="singleSelect:true,collapsible:true,url:'/services/xSearchNew.php',queryParams:{ricerca:'civici'},method:'post',pagination:true">
    <thead>
        <tr>
            <th field="viacivico" align="right"></th>
            <th field="via" data-options="sortable:true" width="400px">Via</th>
            <th field="civico" width="150px">Civico</th>
            <!--
            <th field="tipo_pratica" align="right">Tipo di Pratica</th>
            <th field="intervento">Intervento</th>
            <th field="oggetto" >Oggetto</th>
        </tr>
    </thead>
</table>  -->
    </div>
</body>
</html>