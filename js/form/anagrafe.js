$(document).ready(function(){
    $('#btn-close').button({
        icons:{primary:'ui-icon-close'},
    }).bind('click',function(event){
        event.preventDefault();
        window.close();
    });
    $('#btn-search').button({
        icons:{primary:'ui-icon-search'}
    }).bind('click',function(event){
        var step = 50;
        event.preventDefault();
        var totali = data[$('#anno').val()][$('#tipo_pratica').val()];
        var cicli = Math.ceil(totali / step);
        var tipo = $('#tipo_pratica').val();
        var anno = $('#anno').val();
        for(i=0;i<cicli;i++){
            $.ajax({
                url:'services/xAnagrafe.php',
                async:false,
                data:{offset:i*step,limit:step,anno_riferimento:anno,tipo_richiesta:tipo},
                type:'POST',
                success:function(data,textStatus,jqXHR){
                    
                }
            });
        }    
    return;
        
    });
    $('#anno').bind('change',function(event){
        event.preventDefault();
        if ($(this).val() >= 0 && $('#tipo_pratica').val() >= 0)
            $('#counter').html(data[$(this).val()][$('#tipo_pratica').val()]);
        else
            $('#counter').html('Nessun Valore')
    });
    $('#tipo_pratica').bind('change',function(event){
        event.preventDefault();
        if ($(this).val() >= 0 && $('#anno').val() >= 0)
            $('#counter').html(data[$('#anno').val()][$(this).val()]);
        else
            $('#counter').html('Nessun Valore')
    });
});