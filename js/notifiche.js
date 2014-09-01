function setInfoScadenze(data){
    var text = '';
    var idObject = '#msg-scadenze';
    $(idObject).removeClass('underline-cursor');
    $(idObject).unbind('click');
    if ('errore' in data){
        text = 'Si è verificato un errore';
    }
   else{
        if (data['totali']==0){
            text = 'Nessuna scadenza';
        }
        else{
            $(idObject).addClass('underline-cursor');
            $(idObject).bind('click',loadInfoScadenze);
            text=sprintf('<span class="ui-icon ui-icon-notice" style="display:inline-block;margin-right:10px;"></span>Sono presenti %d scadenze da controllare',data['totali']);
        }
    }
    $(idObject).html(text);
}
function setInfoVerifiche(data){
    var text = '';
    var idObject = '#msg-verifiche';
    $(idObject).removeClass('underline-cursor');
    $(idObject).unbind('click');
    if ('errore' in data){
        text = 'Si è verificato un errore';
    }
   else{
        if (data['totali']==0){
            text = 'Nessuna verifica';
        }
        else{
            $(idObject).addClass('underline-cursor');
            $(idObject).bind('click',loadInfoVerifiche);
            text=sprintf('<span class="ui-icon ui-icon-notice" style="display:inline-block;margin-right:10px;"></span>Sono presenti %d verifiche da eseguire',data['totali']);
        }
    }    
    $(idObject).html(text);
}
function setInfoAnnotazioni(data){
    var text = '';
    if ('errore' in data){
        text = 'Si è verificato un errore';
    }
   else{
        if (data['totali']==0){
            text = 'Nessuna annotazione';
        }
        else{
            text=sprintf('<span class="ui-icon ui-icon-notice" style="display:inline-block;margin-right:10px;"></span>Sono presenti %d annotazioni',data['totali']);
        }
    }    
    $('#msg-verifiche').html(text);
}
function notifiche(){
    var data = {action:'notify'};
    $.ajax({
        url: serverUrl,
        async: true,
        data: data,
        dataType: 'json',
        type:'POST',
        success: function(data,textStatus,jqXHR){
            
            if ('msg-scadenze' in data){
                scadenze=data['msg-scadenze'];
                setInfoScadenze(data['msg-scadenze'])
            }
            if ('msg-verifiche' in data){
                verifiche = data['msg-verifiche'];
                setInfoVerifiche(data['msg-verifiche'])
            }
            if ('msg-annotazioni' in data){
                annotazioni = data['msg-annotazioni'];
                setInfoAnnotazioni(data['msg-annotazioni'])
            }
        }
    })
}
function loadInfoScadenze(){
        var rows=[];
        $.each(scadenze['data'],function(k,v){
            var text = sprintf('<li><a class="underline-cursor" data-href="praticaweb.php" data-pratica="%d" data-target="praticaweb" data-active_form="pe.scadenze.php" data-id="%d">Pratica n° %s : "%s".<br>%s</a></li>',v['pratica'],v['id'],v['numero'],v['oggetto'],v['nome']);
            rows.push(text);
        });
        var html = '<ol>';
        html += rows.join('');
        html += '</ol>';
        $('#message-div').html(html);
        $('#message-div').dialog({
            title:'Scadenze',
            width:800,
            height:400
            
        });
        $.each($("#message-div .underline-cursor"),function(k,v){
            $(v).bind('click',function(event){
                event.preventDefault();
                var data=$(v).data();
                $('#message-div').dialog("close");
                linkToView(data['href'],data);
            });
        })

}
function loadInfoVerifiche(){
        var rows=[];
        $.each(verifiche['data'],function(k,v){
            var text = sprintf('<li><a class="underline-cursor" data-href="praticaweb.php" data-pratica="%d" data-target="praticaweb" data-active_form="pe.verifiche.php" data-id="%d">Pratica n° %s : "%s".<br>Verifica : %s</a></li>',v['pratica'],v['id'],v['numero'],v['oggetto'],v['nome']);
            rows.push(text);
        });
        var html = '<ol>';
        html += rows.join('');
        html += '</ol>';
        $('#message-div').html(html);
        $('#message-div').dialog({
            title:'Verifiche',
            width:800,
            height:400
            
        });
        $.each($("#message-div .underline-cursor"),function(k,v){
            $(v).bind('click',function(event){
                event.preventDefault();
                var data=$(v).data();
                $('#message-div').dialog("close");
                linkToView(data['href'],data);
            });
        })

}
$(document).ready(function(){
    setInfoScadenze(scadenze);
    setInfoVerifiche(verifiche);
    setInterval(notifiche,3600000);
    
});