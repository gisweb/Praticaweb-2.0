$(document).ready(function(){
    var mode = $('#mode').val();

    if (mode=='new' || mode == 'edit') $('#tipo').trigger('change');
    if (mode == 'view'){
        var d = $('#cartella').data();
        $('#cartella').bind('click',function(event){
            event.preventDefault();
            $.ajax({
                url:serverUrl,
                type:'POST',
                dataType:'json',
                data:{action:'list-pratiche-folder',pratica:d['pratica'],value:d['cartella']},
                success:function(data){
                    var text = new Array();
                    var row = '';
                    if (data.length>0){
                        text.push('<div id="result-dialog"><ol>');
                        for(i=0;i<data.length;i++){
                            row = sprintf('<li><a style="text-decoration:none;" href="praticaweb.php?pratica=%(pratica)s" target="_new">%(tipo)s n°%(numero)s del %(data)s</a></li>',data[i]);
                            text.push(row)
                        }
                        text.push('</ol></div>');
                    }
                    else{
                        text.push('<b>Nessuna pratica nel faldone</b>');
                    }
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
    
$("select[data-plugins='select2']").each(function(){
        var data =  $(this).data();
        var id = this.id;
        $(this).select2(
        {
            ajax: {
                url:data["src"],
                dataType: 'json',
                method:'POST',
                delay: 250,
                data: function (params) {
                  return {
                    q: params.term, // search term
                    page: params.page,
                    field:data['field']
                  };
                },
                processResults: function (data, page) {
                  // parse the results into the format expected by Select2.
                  // since we are using custom formatting functions we do not need to
                  // alter the remote JSON data
                  var result = new Array();
                  for (i=0;i<data.length;i++) {
                    result.push({id:data[i]['id'],text:data[i]['label']});
                  }
                  return {
                    results: result
                  };
                }
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 1
        });
    });
        
});
