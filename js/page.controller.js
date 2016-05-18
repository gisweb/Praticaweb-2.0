$(document).ready(function(){
    function getkeys(foo){
        var keys = $.map(foo, function(item, key) {
            return key;
        }); 
        return keys;
    }
   $.each($('.textbox-date'),function(k,v){
       $(v).datepicker({
            dateFormat:'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
        $(v).datepicker("option","defaultDate",$(v).attr('data-defaultDate'));
        $(v).datepicker("option","yearRange",$(v).attr('data-yearRange'));
   });
   $('[title]').tooltip({ tooltipClass: "textbox" });
   $.each($('[data-change]'),function(k,v){
       var fn = $(v).attr('data-change');
       $(v).bind('change', function(){
           eval(fn+'($(v));');
       });
   });
   $.each($('.stampe'),function(k,v){
       var rnd = Math.random().toString(36).substring(7);
       $(v).bind('click',function(event){
           event.preventDefault();
           var d=$(this).data();
           var url=encodeURIComponent(d['url'])+'?random='+rnd;
           window.open(window.parent.url_documenti+url,'stampe');
       });
   });
   $.each($('.allegati'),function(k,v){
       var rnd = Math.random().toString(36).substring(7);
       $(v).bind('click',function(event){
           event.preventDefault();
           var d=$(this).data();
           //var url=d['url']+'?random='+rnd;
           //window.open(window.parent.url_allegati+url,'stampe');
           var url='/openDocument.php?type=allegati&pratica=' + d['pratica']+ '&id=' + d['id'];
           window.open(url,'stampe');
       });
   });
    $("#btn_elenco_pratiche_indirizzi").button({
        icons: {
            primary: "ui-icon-gear"
        }
    }).bind('click',function(event){
       
		var baseURL='/elencopratiche_indirizzo.php'
		event.preventDefault();
		var via=$("#via").val();
		var civico=$("#civico").val();
		var interno=$("#interno").val();
		var url=baseURL + '?via='+encodeURIComponent(via)+'&civico='+encodeURIComponent(civico)+'&interno='+encodeURIComponent(interno);
		if (via){
			$('#waiting').dialog({
				width:400,
				height:200,
				title:'Messaggio'
			}).text('Attendere prego, caricamento dei dati in corso....');
			$('#result').html('<iframe frameBorder=0 style="width:99%;height:99%;OVERFLOW: visible;" marginWidth=0  marginHeight=0 src="' + url + '"></iframe>');
		}
		else
			$('<div>Selezionare una via</div>').dialog({
				width:300,
				height:200,
				title:'Attenzione'
			})
	});
        $(document).keypress(function(event){
            
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                if (!$(document.activeElement).is('textarea')){
                    event.preventDefault();
                    if ($("#azione-salva"))  $("#azione-salva").click(); 
                    else if ($("#print_btn")) $("#print_btn").click();
                    else if ($("#avvia-ricerca")) $("#avvia-ricerca").click();
                    else if ($("#print_btn")) $("#print_btn").click();
                    
                }
            }

        });
        $("[data-plugins='open-page']").bind('click',function(event){
            var prms=$(this).data();
            var form='<form action="'+prms['action']+'" method="POST" id="submitFrm"></form>';
            delete prms['plugins'];
            delete prms['action'];
            if (!window.parent){
                $(form).appendTo('body');
                $.each(prms,function(k,v){
                    $('<input type="hidden" name="'+k+'" value="'+v+'">').appendTo($('#submitFrm'));
                });
                 $('#submitFrm').submit();
            }
            else{
                $(form).appendTo($('body',window.parent.document));
                $.each(prms,function(k,v){
                    $('<input type="hidden" name="'+k+'" value="'+v+'">').appendTo($('#submitFrm',window.parent.document));
                });
                $('#submitFrm',window.parent.document).submit();
            }
            
            
        });
        $("[data-plugins='loadIntoIFrame']").bind('click',function(event){
            var form='<form action="praticaweb.php" method="POST" id="submitFrm"></form>';
            $(form).appendTo($('body',window.parent.document));
            var prms = $(this).data();
            $.each(prms,function(k,v){
                $('<input type="hidden" name="'+k+'" value="'+v+'">').appendTo($('#submitFrm',window.parent.document));
            });
            $('#submitFrm',window.parent.document).submit();
        });
        
        $("[data-class]").each(function(k,v){
            var data = $(v).data();
            $(v).addClass(data['class']);
        });
        
        $("[data-plugins='inserisci-associazione-doc']").each(function(k,v){
            $(v).attr("title","Assegna documento ad una pratica edilizia");
            $(v).bind('click',function(event){
            console.log($(this));
            event.preventDefault();
            var dd = $(this).data();
            var id = dd['value'];
            $('#storage-documento').val(id);   
                $('#associa-pratica').dialog({
                    title: "Collega documento a pratica",
                    height: 600,
                    width:800
                });
            });
        });
        $("[data-plugins='elimina-associazione-doc']").each(function(k,v){
            
            
        });
        $("[data-plugins='menu']").bind('click',function(event){
            event.preventDefault();
            
            var url = sprintf("/forms/%(app)s/%(page)s.php?pratica=%(pratica)s",$(this).data());
            $("#myframe").attr('src',url);
            resizeCaller()
            scroll(0,0)
        });
        $("[data-plugins='openDocument']").bind('click',function(event){
            var data = $(this).data();
            if (!$('#frm-plugin').length) 
                $('body').append('<form target="_new" id="frm-plugin" method="POST" action=""></form>');
            $('#frm-plugin').html('');
            $.each(data,function(k,v){
                  $('#frm-plugin').append(sprintf('<input type="hidden" name="%s" value="%s"/>',k,v));
            });
            $('#frm-plugin').attr('action',data['url']);
            $('#frm-plugin').submit();
        });
        
        $('#associa').button({
            label:"Collega alla Pratica",
            icons:{
                primary:"ui-icon-disk"
            }
        }).bind('click',function(event){
            var n = $('#numero').val();
            var documento = $('#documento').val();
            var id = $('#storage-documento').val();
            if (!n) {
                alert('Inserire un numero di pratica');
                return;
            }
            if (!documento) {
                alert('Attenzione selezionare una tipologia di documento');
                return;
            }
            $.ajax({
                url:'/services/xServer.php',
                method:'POST',
                data:{'numero_pratica':n,'action':'invia_documento','id':id,'documento':documento,'assoc_schema':'pe','assoc_table':'allegati'},
                success:function(data){
                    if(data["success"]==1){
                        $('#message-dialog').html();
                        $('#associa-pratica').dialog('close');
                    }
                    else{
                        $('#message-dialog').html(data['message']);
                    }
                }
            });
        });
        $("[data-plugins='field-disabled']").each(function(k,v){
            var params=$(v).data();
            var id = 'edit-' + $(v).attr('id');
            var icon = 'icon-' + $(v).attr('id');
            $(v).attr('disabled','disabled');
            if ('editable' in params && params['editable']=='1'){
                $(v).parent().append('<a title="Abilita/Disabilita" href="#" id="' + id + '" data-toggle="1" style="display:inline-block;"><span id="' + icon +'" class="ui-icon ui-icon-pencil"/></a>');
                $('#'+id).bind('click',function(event){
                    event.preventDefault();
                    if ($(v).attr('disabled')=='disabled') {
                        $(v).removeAttr('disabled');
                        $('#'+icon).removeClass('ui-icon-pencil');
                        $('#'+icon).addClass('ui-icon-cancel');
                    }
                    else{
                        $(v).attr('disabled','disabled');
                        $('#'+icon).removeClass('ui-icon-cancel');
                        $('#'+icon).addClass('ui-icon-pencil');
                    }
                });
                
            }
            
            
        });
        // Plugin Autosuggest Pratica
         $("[data-plugins='suggest-pratica']").each(function(k,v){
            var params=$(v).data();
            var data = new Object();
            $(v).autocomplete({
                    source: function( request, response ) {
                            data.term = request.term;
                            data.field = $(v).attr('name');
                            /*var flds=[$params];
                            if ($.isArray(flds)){
                                $.each(flds,function(i,k){
                                    var v=jQuery('[name=\''+k+'\']').val();
                                    if (v){
                                        data[k]=v;
                                    }
                                });
                            }*/

                            $.ajax({
                                url:suggestUrl,
                                dataType:'json',
                                type:'POST',
                                data:data,
                                success:response
                            });

                        },
                    /*select:$selectFN,*/
                    minLength:1
            });
            /*jQuery('#toggle_$campo').button({
                    icons: {
                            primary: "ui-icon-circle-triangle-s"
                    },
                    text:false
            }).click(function(){
                    jQuery('#$campo').autocomplete('search');
                    return false;
            });*/
        });
        //Metto il focus sul primo input o textarea
        $("form").find('input[type=text],textarea').not('.textbox-date').filter(':visible:first').focus();
        $("[data-plugins='tipo_soggetto']").bind('change',function(event){
            verificaRuoloSoggetti();
        });
        
        $("[data-plugins='link']").bind('click',function(event){
            
        });
});

