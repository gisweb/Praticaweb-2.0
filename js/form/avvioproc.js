$(document).ready(function(){
    var mode = $('#mode').val();

    if (mode=='new') $('#tipo').trigger('change');
    if (mode == 'view'){
        var d = $('#cartella').data();
        $('#cartella').bind('click',function(event){
            event.preventDefault();
            
            $("#cartella").append('');
            var progressbar = $( "#progressbar" ),
                progressLabel = $( ".progress-label" );

            progressbar.progressbar({
              value: false,
              height:60
            });
            $.ajax({
                url:serverUrl,
                type:'POST',
                dataType:'json',
                data:{action:'list-pratiche-folder',pratica:d['pratica'],value:d['cartella']},
                success:function(data){
                    var text = new Array();
                    var row = '';
                    text.push('<div id="result-dialog"><ol>');
                    for(i=0;i<data.length;i++){
                        
                        row = sprintf('<li><a style="text-decoration:none;" href="praticaweb.php?pratica=%(pratica)s" target="_new">%(tipo)s n°%(numero)s del %(data)s</a></li>',data[i]);
                        text.push(row)
                    }
                    text.push('</ol></div>');
                    
                    $(text.join('')).dialog({
                        title:'Pratiche edilizie correlate',
                        width:600,
                        height: 400,
                        modal: true
                    });
                }
            })
        });
    }
        
});