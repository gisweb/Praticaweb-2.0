<html>
<head>
    <title>Elenco delle Pratiche per Civico</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    require_once "login.php";
    utils::loadCss();
    utils::loadJS();
?>
    <link rel="stylesheet" type="text/css" href="/css/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="/css/icon.css">
    <script type="text/javascript" src="/js/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="/js/locale/easyui-lang-it.js"></script>
   
    <style>
        td.civici{
            padding: 5px;
        }
        div.civici{
            margin:10px;
        }
    </style>
    <script>
        function moveTo(){
            var t = $('#civici');
            var node = t.tree('getSelected');
            $('#dlg').dialog('open');
        }
        function collapse(){
            var node = $('#civici').tree('getSelected');
            $('#civici').tree('collapse',node.target);
        }
        function expand(){
            var node = $('#civici').tree('getSelected');
            $('#civici').tree('expand',node.target);
        }
        $(document).ready(function(){
            
        });
    </script>
</head>
<body>
    <?php 
include "./inc/inc.page_header.php";
?>
     
<div class="civici"><ul id="civici" class="easyui-tree"  data-options="
            url: '/services/xTree.php?ricerca=civici-pratiche',
            method: 'post',
            animate: true,
            
            onContextMenu: function(e,node){
                e.preventDefault();
                $(this).tree('select',node.target);
                $('#mm').menu('show',{
                    left: e.pageX,
                    top: e.pageY
                });
            }
        "></ul>
</div>
<div id="mm" class="easyui-menu" style="width:120px;">
    <div onclick="moveTo()" data-options="iconCls:'icon-add'">Sposta In</div>
    <div onclick="expand()">Expand</div>
    <div onclick="collapse()">Collapse</div>
</div>   
    <div id="dlg" class="easyui-dialog" title="Finestra di modifica dell'indirizzo della pratica" style="width:600px;height:200px;padding:10px"
            data-options="
                iconCls: 'icon-edit',
                modal: true,
                closed: true,
                buttons: '#dlg-buttons'
            ">
        <input id="via" class="easyui-combobox" data-options="
            width:250,
            valueField: 'id',
            textField: 'label',
            url: '/services/xSuggest.php?field=via',
            method:'post',
            panelHeight:'auto',
            onSelect: function(rec){
                var url = '/services/xSuggest.php?field=civico&via='+rec.id;
                $('#civico').combobox('reload', url);
            }">
        <input id="civico" class="easyui-combobox" data-options="width:150,valueField:'id',textField:'label',panelHeight:'auto'">
    </div>
    <div id="dlg-buttons">
        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="javascript:alert('save')">Salva</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="javascript:$('#dlg').dialog('close')">Chiudi</a>
    </div>
            

</body>
</html>