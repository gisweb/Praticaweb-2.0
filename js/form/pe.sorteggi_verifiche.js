function alert_message(m){
    $.messager.show({
            title:'Attenzione',
            msg:sprintf(pwMessage[m]),
            showType:'show',
            style:{
                right:'',
                bottom:''
            }
        });
}
function checkDraw(){
    var result;
    var d = $('#data_sorteggio').val();
    var t = $('#tipo').val();
    $.ajax({
        url:serverUrl,
        async:false,
        method:'POST',
        data:{action:'check-draw',tipo:t,data_sorteggio:d},
        dataType:'JSON',
        success:function(data){
            result=data['sorteggiato'];
            $('#sorteggiabili').html(data['sorteggiabili']);

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
    $('#tipo').bind('change',function(event){
        var v = $(this).val();
        $('#info-sorteggi').html(selectInfo[v]);
        if (v) checkDraw();
    });
    
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
    $('#data_sorteggio').datepicker({
        dateFormat:'dd-mm-yy',
        changeMonth: true,
        changeYear: true
    }).bind('change',function(event){
        var v = $('#tipo').val();
        if ($(this).val() && v) checkDraw();
    });
    $('#btn-draw').button({
        icons:{primary:'ui-icon-search'}
    }).bind('click',function(event){
        event.preventDefault();
        var tipo_draw=$('#tipo').val();
        var tipo_text=$("#tipo option:selected").text();
        var r = checkDraw();
        if (!tipo_draw) {
            alert_message('no_drawtype_selected');
            return;
        }
        var d = $('#data_sorteggio').val();
        if (r) {
            $.messager.confirm('Attenzione', sprintf(pwMessage['raffled'],{testo:tipo_text}),function(resp){
                if (resp){
                    $.ajax({
                        url:serverUrl,
                        method:'POST',
                        data:{action:'draw',tipo:tipo_draw,data_sorteggio:d},
                        dataType:'JSON',
                        success:function(data){
                            //alert_message('draw_done',data[])
                            loadDatagrid();
                            checkDraw();
                        }
                    });
                }
            });

        }
        else{
            $.ajax({
                url:serverUrl,
                method:'POST',
                data:{action:'draw',tipo:tipo_draw,data_sorteggio:d},
                dataType:'JSON',
                success:function(data){
                    if(data["success"]==1){
                        loadDatagrid();
                        checkDraw();
                    }
                    else{
                         alert_message(data["message"]);
                    }
                }
            });
        }
        

    });
    loadDatagrid();
});
    
var result={};
var dataPost={};