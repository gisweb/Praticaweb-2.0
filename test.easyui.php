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

    <script>
        $(document).ready(function(){
            var colsDef={
                civici:[[
                    {title:'Indirizzo',field:'indirizzo',sortable:true,width:1000},
                    //{title:'Via',field:'via',sortable:true,width:500},
                    //{title:'Civico',field:'civico',sortable:true,width:100}
                ]],
                pratica:[[
                    {title:'Numero',field:'numero',sortable:true,width:100},
                    {title:'Protocollo',sortable:true,field:'protocollo',width:100},
                    {title:'Data Prot.',sortable:true,field:'data_protocollo',width:100},
                    {title:'Tipo',field:'tipo_pratica',sortable:true,width:150},
                    {title:'Intervento',sortable:true,field:'intervento',width:100},
                ]],
                default_cols:[[
                    {title:'',sortable:true,field:'',width:100},
                ]]
                
            }
            $('#report-table').datagrid({
                title:'Elenco degli indirizzi delle pratiche',
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
                        columns:colsDef['pratica'],
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
    <?php 
include "./inc/inc.page_header.php";
?>
<table id="report-table" style="width:1000px;margin-top:20px;margin-left:20px;"></table>
        
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