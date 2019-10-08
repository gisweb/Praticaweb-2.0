
    $(document).ready(function(){
        $(".textbox").bind("keyup",function(event){
            if(event.keyCode == 13){
                $("#avvia-ricerca").click();
            }
        });
        $( "#result-container" ).hide();
        $('#btn-delete').button({
            icons:{primary:'ui-icon-trash'},
            disabled: true
        }).bind('click',function(event){
            event.preventDefault();
            var d = $("input[type=radio]:checked").data();
            var pr = $("input[type=radio]:checked").attr('id');
            var t = sprintf(pwMessage['delete_pratica'],d);
            $("#delete-dialog").html(t);
            $("#delete-dialog").dialog({
                title:"Conferma la cancellazione",
                resizable: false,
                width:600,
                height:200,
                modal: true,
                buttons: {
                    Elimina: function() {
                      var pr = $("input[type=radio]:checked").attr('id');
                        $.ajax({
                            url:serverUrl,
                            type    : 'POST',
                            data    : {action:'delete-pratica',pratica:pr},
                            dataType:'json',
                            success : function(data, textStatus, jqXHR){
                                $('#result-table').datagrid('reload');
                                $("#delete-dialog").dialog('close');
                            }
                        });
                    },
                    Annulla: function() {
                      $( this ).dialog( "close" );
                    }
                }
            });
            
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
        
        $('#avvia-ricerca').button({
            icons:{primary:'ui-icon-search'}
        }).bind('click',function(event){

            event.preventDefault();
            var oper=$('#op').val();
            dataPost=getSearchFilter();
            $('#ricerca').hide('slide',500);
            $('#result-container').show('slide',500);
            $('#result-table').datagrid({
                title:'Risultato della ricerca',
                url:searchUrl,
                method:'post',
                nowrap:false,
                columns:colsDef['delete'],
                fitColumns:false,
                pagination:true,
                autoRowHeight:true,

                queryParams:{data:dataPost,action:'search',op:oper},
                /*view: myview,
                detailFormatter:function(index,row){
                    return '<div class="ddv" style="padding:5px 0;background-color:#EEF7FF"></div>';
                },*/
                onLoadSuccess:function(data){
                    $('#btn-delete').button('disable');
                    $(".delete-radio").bind('change',function(event){
                        $('#btn-delete').button('enable');
                        $('#btn-delete').attr('data-testo',$(this).attr('data-testo'));
                        $('#btn-delete').attr('data-id',$(this).attr('id'));
                    });
                }
                
            });
        });
    });
    var result={};

var dataPost={};
