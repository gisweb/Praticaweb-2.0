$(document).ready(function(){
    $("#dialog").dialog({
        modal:true,
        autoOpen: false,
        title:'Finestra di caricamento dei dati',
        width:600,
        height:200
    })
    var progressbar = $( "#progressbar" ),
        progressLabel = $( ".progress-label" );
    $( "#progressbar" ).progressbar({
        value: false,
        change: function() {
            progressLabel.text( progressbar.progressbar( "value" ) + "%" );
        },
        complete: function() {
            $( ".progress-label" ).text( "Complete!" );
        }
    });
    
    $('#btn-close').button({
        icons:{primary:'ui-icon-close'},
    }).bind('click',function(event){
        event.preventDefault();
        window.close();
    });
    $('#btn-search').button({
        icons:{primary:'ui-icon-search'}
    }).bind('click',function(event){
        var step = 20;
        event.preventDefault();
        $(this).attr("disabled","disabled");
        $('#btn-close').attr("disabled","disabled");
        
        $('#table_result').html('');
        $("#progressbar").progressbar( "value", 0 );
        var totali = data[$('#anno').val()][$('#tipo_pratica').val()];
        var cicli = Math.ceil(totali / step);
        var tipo = $('#tipo_pratica').val();
        var anno = $('#anno').val();
        $("#num-tot").html(totali);
        /*Chiamata record di testa*/
        $.ajax({
            url:'services/xAnagrafe.php',
            async:false,
            data:{mode:'testa',anno_riferimento:anno,tipo_richiesta:tipo,filename:'anagrafe_trib.txt'},
            type:'POST',
            success:function(data,textStatus,jqXHR){
                var testo = $('#table_result').html();
                if (data['errori']>0){
                    testo+=data['html'];
                    $('#table_result').html(testo);
                }
            }
        });
        $( "#dialog" ).dialog( "open" );
        var err=0;
        var processed = 0;
        var discarded = 0
        for(i=0;i<cicli;i++){
            $.ajax({
                url:'services/xAnagrafe.php',
                async:false,
                data:{mode:'dati',offset:i*step,limit:step,anno_riferimento:anno,tipo_richiesta:tipo,filename:'anagrafe_trib.txt',error:err,processed:processed,discarded:discarded},
                type:'POST',
                success:function(data,textStatus,jqXHR){
                    var testo = $('#table_result').html();
                    if (data['errori']>0){
                        testo+=data['html'];
                        $('#table_result').html(testo);
                        $("#num-error").html(totali);
                    }
                    processed=data["processed"];
                    discarded = data["discarded"];
                    var val = parseInt(((processed/totali)*100)) || 0;
                    err=data['errori'];
                    $('#num-discarded').html(data["discarded"]);
                    $('#num-processed').html(data["processed"]);
                    $('#num-error').html(err);
                    $('#num-perc-error').html(((err/processed)*100).toFixed(2) + "%");
                    $("#progressbar").progressbar( "value", val );
                    
                }
            });
        }
        /*Chiamata record di coda*/
        $.ajax({
            url:'services/xAnagrafe.php',
            async:false,
            data:{mode:'coda',anno_riferimento:anno,tipo_richiesta:tipo,filename:'anagrafe_trib.txt'},
            type:'POST',
            success:function(data,textStatus,jqXHR){
                var testo = $('#table_result').html();
                if (data['errori']>0){
                    testo+=data['html'];
                    $('#table_result').html(testo);
                }
            }
        });
        $(this).removeAttr('disabled');
        $('#btn-close').removeAttr('disabled');
        $( "#dialog" ).dialog( "close" );
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