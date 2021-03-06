var result={};
var dataPost={};
$(document).ready(function(){
    $("#chk-avanzata").bind("change",function(event){
        event.preventDefault();
        $("#ricerca-avanzata").toggle();
    });
    $(".textbox").bind("keyup",function(event){
        if(event.keyCode == 13){
            $("#avvia-ricerca").click();
        }
    });
    $('#btn-report').button({
        icons:{primary:'ui-icon-document'}
    }).bind('click',function(event){
        event.preventDefault();
        $('#frm-report').remove();
        $('body').append('<form id="frm-report" action="./services/xReport.php" method="POST" target="reportPraticaweb"><input type="hidden" value="online" name="report" id="report"/><input type="hidden" value="" name="elenco" id="elencopratiche"/></form>')
        $('#elencopratiche').val($('#elenco').val());
        $('#frm-report').submit();
    });
    
    $('#btn-close').button({
        icons:{primary:'ui-icon-circle-close'}
    }).bind('click',function(event){
        event.preventDefault();
        closeWindow();
    });

    $('#btn-reload').button({
        icons:{primary:'ui-icon-arrowrefresh-1-w'}
    }).bind('click',function(event){
        event.preventDefault();
        window.location.reload();
    });
    
    $('[data-plugins="dynamic-search"]').bind('change',function(event){
        event.preventDefault();
        dataPost=getSearchFilter();
        $('#result-table').datagrid('load',{data:dataPost,action:'search-online'});
    });
    var oper='AND';
    var dataPost=getSearchFilter();
    var data = new Date();
    var dd = (data.getDate()<10) ? (('0' + data.getDate().toString())) : (data.getDate().toString());
    var mm = ((data.getMonth()+1)<10) ? ('0' + (data.getMonth()+1).toString()) : ((data.getMonth()+1).toString());
    var mn = (data.getMinutes()<10) ? (('0' + data.getMinutes().toString())) : (data.getMinutes().toString());
    var hh = (data.getHours()<10) ? (('0' + data.getHours().toString())) : (data.getHours().toString());
    var tms = ' alle ' + hh + ':' + mn;
    tms += ' del ' + dd + '/' + mm + '/' + data.getFullYear().toString();
    
    $('#result-table').datagrid({
        title:'Pratiche Presentate OnLine' + tms,
        url:searchUrl,
        method:'post',
        nowrap:false,
        columns:colsDef['online'],
        fitColumns:false,
        pagination:true,
        autoRowHeight:true,
        rowStyler: function(index,row){
            return 'color:#0e2d5f';
        },
        queryParams:{data:{},action:'search-online',op:oper},
        onLoadSuccess:function(data){
            if(!data.total) alert('Nessun record trovato');
            $('#elenco').val(data['elenco_id']);
        }

    });

});
