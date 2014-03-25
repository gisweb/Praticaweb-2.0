var baseURL='/gisclient/template/';
var searchUrl='/services/xSearch.php';
var serverUrl='/services/xServer.php';
var suggestUrl='/services/xSuggest.php';


$.widget( "custom.catcomplete", $.ui.autocomplete, {
        _renderMenu: function( ul, items ) {
            var that = this,
			
            currentCategory = "";
			
            $.each( items, function( index, item ) {
                if ( item.category != currentCategory ) {
                    ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                    currentCategory = item.category;
                }
				
                that._renderItemData( ul, item );
            });
        }
    });

function setDatiAutoSuggest(event,ui){
    if (typeof(ui.item.child)!='undefined'){
        $.each(ui.item.child,function(k,v){
            $('#'+k).val(v);  
        });
    }
}

function confirmDelete(obj){
    return confirm('Sei sicuro di voler eliminare il record?')
}
function confirmSpostaVariazioni(obj){
    return confirm('Sei sicuro di voler volturare il record?')
}
function goToView(obj){
    $('#btn_azione').val('Annulla');
    $(obj).parents('form:first').attr('action','praticaweb.php');
    
    $(obj).parents('form:first').submit();
}

function linkToList(url,prms){
    var form='<form method="POST" action="'+url+'" id="submitFrm"></form>';
    $(form).appendTo('body');
    prms['mode']='list';
    $.each(prms,function(k,v){
        
        $('<input type="hidden" name="'+k+'" value="'+v+'">').appendTo($('#submitFrm'));
    });
    $('#submitFrm').submit();
}

function linkToView(url,prms){
    var form='<form action="'+url+'" method="POST" id="submitFrm"></form>';
    $(form).appendTo('body');
    prms['mode']='view';
    $.each(prms,function(k,v){
        $('<input type="hidden" name="'+k+'" value="'+v+'">').appendTo($('#submitFrm'));
    });
    $('#submitFrm').submit();
}
function loadInto(url,prms){
    var form='<form action="praticaweb.php" method="POST" id="submitFrm"></form>';
    $(form).appendTo($('body',window.parent.document));
    //prms['mode']='view';
    prms['active_form']=url;
    prms['ext']=1;
    prms['config_file']='oneri/calcolati.tab';
    $.each(prms,function(k,v){
        $('<input type="hidden" name="'+k+'" value="'+v+'">').appendTo($('#submitFrm',window.parent.document));
    });
    $('#submitFrm',window.parent.document).submit();
}
function linkToEdit(url,prms){
    var form='<form action="'+url+'" method="POST" id="submitFrm"></form>';
    prms['mode']='edit';
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
        
    //var params='';
    //var tmp=Array();
    //$.each(prms,function(k,v){
    //    tmp.push(k+'='+v);
    //});
    //tmp.push('mode=edit');
    //window.parent.location=url+'?'+tmp.join('&');
}
function goToPratica(url,prms){
    var form='<form action="'+url+'" method="POST" id="submitFrm"></form>';
    prms['mode']='view';
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
}

function calcola_cf(){
	var oggetti=Array('cognome','nome','comunato','datanato','sesso');
	var tipo=Array('testo','testo','testo','data','testo');
	var descrizione=Array('il cognome','il nome','il comune di nascita','la data di nascita','il sesso');
	var param={'field':'codfis'};
    var exec=1;
   // param['funz']"funz=codice_fiscale&oggetto=codfis";
    $.each(oggetti,function(i,v){
         
        var val = $('#'+v).val();
        if (val.length==0){
            alert('Inserire '+descrizione[i]);
            exec=0;
            return;
        }
        else
            param[v]=val;
    });
    if (exec==0)
        return;
	$.ajax({
        url     : 'services/xSuggest.php',
        type    : 'POST',
        data    : param,
        dataType:'json',
        success : function(data, textStatus, jqXHR){
            if (data.value){
                $('#codfis').val(data.value);
            }
            else
                alert(data.error);
        }
    });
}

function selectTavola(obj){
    var vincolo=$(obj).val();
    $.ajax({
        url     : 'services/xSuggest.php',
        type    : 'POST',
        data    : {'field':'tavola','term':vincolo},
        dataType:'json',
        success : function(data, textStatus, jqXHR){
            $('#tavola').html('');
            $('#zona').html('');
            for(i=0;i<data.length;i++)
                 $('#tavola').append($('<option>', { value : data[i]['id'] }).text(data[i]['opzione']));
        }
    });
}
function selectZona(obj){
    var tavola=$(obj).val();
    var vincolo=$('#vincolo').val();
    $.ajax({
        url     : 'services/xSuggest.php',
        type    : 'POST',
        data    : {'field':'zona','term':tavola,'vincolo':vincolo},
        dataType:'json',
        success : function(data, textStatus, jqXHR){
            $('#zona').html('');
            for(i=0;i<data.length;i++)
                 $('#zona').append($('<option>', { value : data[i]['id'] }).text(data[i]['opzione']));
        }
    });
}


function closeWindow(){
    window.close();   
}

function NewWindow(url, winname, winwidth, winheight, scroll) {
	
	if (!winwidth)
		  winwidth =screen.availWidth-10;
	if (!winheight)
		  winheight = screen.availHeight-35;
	winprops = 'height='+winheight+',width='+winwidth+',scrollbars='+scroll+',menubar=no,top=0,status=yes,left=0,screenX=0,screenY=0,resizable,close=no';
	
        window.open(url, winname, winprops);
	/*firstWin.windows[winname] = window.open(url, winname, winprops);
        
	if (parseInt(navigator.appVersion) >= 4) { 
		firstWin.windows[winname].window.focus(); 
	}*/
        //return win;
}
function selectOneriAnno(){
    
}
function selectOneriDestUso(){
    
}
function selectOneriIntervento(){
    
}

  function ApriMappa(mapsetid,template,parameters){
		if(!template) template = this.Template;
		var winWidth = window.screen.availWidth-8;
		var winHeight = window.screen.availHeight-55;
		var winName = 'mapset_'+mapsetid;
		template=template +"/";
		if(!parameters) parameters='';
		if(template.indexOf('?')>0)
			template=template + '&';
		else
			template=template + '?';
		var mywin=window.open(baseURL + template + "mapset=" + mapsetid + "&" + parameters, winName,"width=" + winWidth + ",height=" + winHeight + ",menubar=no,toolbar=no,scrollbar=auto,location=no,resizable=yes,top=0,left=0,status=yes");
		mywin.focus();
  }
  
  function ApriDocumento(url){
	  var mywin=window.open(url,'Documenti');
	  mywin.focus();
  };
  function ApriEditor(id){
	  var mywin=window.open('stp.editor_documenti.php?id_doc='+id,'Editor');
	  mywin.focus();
  }
  
  function hideSection(obj){
      
  }
  function fillCtr(obj){
      var prms = $(obj).attr('data-params').split(',');
      for(i=0;i<prms.length;i++){
          var el = $('#'+prms[i]);
          var id = el.attr('id');
          var value = $(obj).val();
          console.log(obj);
          if ($(obj).is('select')){
              el.html('');
              if (typeof(selectdb[id][value])== 'undefined') 
                  el.append($('<option>', { value : '' }).text('Nessun Valore'));
              else{
                  el.append($('<option>', { value : '' }).text('Seleziona =====>'));
                  for(j=0;j<selectdb[id][value].length;j++)
                  el.append($('<option>', { value : selectdb[id][value][j]['id'] }).text(selectdb[id][value][j]['opzione']));
              }
          }
          
      }
  }
  
  
  function getSearchFilter(){
	var searchFilter=new Object();
	$(".search").each(function(index){
            var name=$(this).attr('name');
            var id = $(this).attr('id').replace('op_','');
            var opValue=$(this).val();
            var filter='';
            var t=($(this).hasClass('text'))?('text'):(($(this).hasClass('number'))?('number'):('date'));
            if (!$('#1_'+id).val()){
                filter='';
            }
            else if (opValue == 'between'){
                if(t=='date'){
                    filter=name+" >= '"+$('#1_'+id).val()+"'::date AND "+name +" <= '"+$('#2_'+id).val()+"'::date";
                }
                else{
                    filter=name+" >= "+$('#1_'+id).val()+" AND "+name +" <= "+$('#2_'+id).val();
                }
            }
            else if(opValue == 'equal'){
                 if(t=='date'){
                    filter=name+" = '"+$('#1_'+id).val()+"'::date";
                }
                else if (t=='text'){
                    filter=name+"::varchar ilike '"+$('#1_'+id).val()+"'";
                }
                else{
                    filter=name+" = "+$('#1_'+id).val();
                }
            }
            else if(opValue == 'great'){
                if(t=='date'){
                    filter=name+" > '"+$('#1_'+id).val()+"'::date";
                }
                else{
                    filter=name+" > "+$('#1_'+id).val();
                }
            }
            else if(opValue == 'less'){
                if(t=='date'){
                    filter=name+" < '"+$('#1_'+id).val()+"'::date";
                }
                else{
                    filter=name+" < "+$('#1_'+id).val();
                }
            }
            else if(opValue == 'contains'){
                filter=name+" ilike '%"+$('#1_'+id).val()+"%'";
            }
            else if(opValue == 'startswith'){
                 filter=name+" ilike '"+$('#1_'+id).val()+"%'";
            }
            else if(opValue == 'endswith'){
                 filter=name+" ilike '%"+$('#1_'+id).val()+"'";
            }
            else if(opValue == 'in'){
                var res = [];
                $('#1_'+ id + ' :selected').each(function(i,selected){
                    res.push("'" + $(selected).val() + "'"); 
                })
                filter=name+" IN ("+res.join(",")+")";
                
            }
            if (filter) {
                var table=$(this).attr('datatable');
                if (searchFilter[table]) searchFilter[table].push(filter);
                else{
                    searchFilter[table]=new Array();
                    searchFilter[table].push(filter);
                }
            }
		
        });	
	return searchFilter;
    }