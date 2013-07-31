var viewtype='pratica';
var searchTable;
var resultData={};
var colDefs=[
  {'mDataProp': 'pratica','bVisible':false},
  { 'mDataProp': 'numero', 'sTitle':'Numero','sWidth':'10%','sType':'numeric'},
  { 'mDataProp': 'data_presentazione' ,'sDefaultContent':' ','sTitle':'Data Presentazione','sWidth':'10%','sType':'uk_date' },
  { 'mDataProp': 'protocollo','sTitle':'Protocollo','sWidth':'10%'},
  { 'mDataProp': 'oggetto', 'sTitle':'Oggetto','sWidth':'30%'},
  { 'mDataProp': 'tipo_pratica', 'sTitle':'Tipo Pratica','sWidth':'10%'},
  { 'mDataProp': 'tipo_intervento', 'sTitle':'Tipo Intervento','sWidth':'15%'},
  { 'mDataProp': 'nome_responsabile', 'sTitle':'Nome Responsabile','sWidth':'15%'}
  
];


function startSearch(obj){
    var dataPost=new Object();
    var searchFilter=new Array();
    dataPost['action']='search';
    $(".search").each(function(index){
        var name=$(this).attr('name');
        var opValue=$(this).val();
        var filter;
        var t=($(this).hasClass('text'))?('text'):(($(this).hasClass('number'))?('number'):('date'));
        if (opValue == 'between'){
            if(t=='date'){
                filter=name+" >= '"+$('#1_'+name).val()+"'::date AND "+name +" <= '"+$('#2_'+name).val()+"'::date";
            }
            else{
                filter=name+" >= "+$('#1_'+name).val()+" AND "+name +" <= "+$('#2_'+name).val();
            }
        }
        else if(opValue == 'equal'){
             if(t=='date'){
                filter=name+" = '"+$('#1_'+name).val()+"'::date";
            }
            else if (t=='text'){
                filter=name+" ilike '"+$('#1_'+name).val()+"'";
            }
            else{
                filter=name+" = "+$('#1_'+name).val();
            }
        }
        else if(opValue == 'great'){
            if(t=='date'){
                filter=name+" > '"+$('#1_'+name).val()+"'::date";
            }
            else{
                filter=name+" > "+$('#1_'+name).val();
            }
        }
        else if(opValue == 'less'){
            if(t=='date'){
                filter=name+" < '"+$('#1_'+name).val()+"'::date";
            }
            else{
                filter=name+" < "+$('#1_'+name).val();
            }
        }
        else if(opValue == 'contains'){
            filter=name+" ilike '%"+$('#1_'+name).val()+"%'";
        }
        else if(opValue == 'startswith'){
             filter=name+" ilike '"+$('#1_'+name).val()+"%'";
        }
        else if(opValue == 'endswith'){
             filter=name+" ilike '%"+$('#1_'+name).val()+"'";
        }
        //console.log(filter);
        if (filter) searchFilter.push(filter);
    });
    dataPost['filter']=searchFilter.join(' AND ');
    dataPost['view']=viewtype;
    $(colDefs).each(function(k,v){
        dataPost['mDataProp_'+k]=v['mDataProp'];
    });
    if (searchTable) searchTable.fnDestroy();
	$.ajax( {
		'dataType': 'json', 
		'type': 'POST', 
		'url': searchUrl, 
		'data': dataPost, 
		'success': function(data, textStatus, jqXHR){
            //searchTable.fnClearTable();
            resultData=data['aaData'];
            //searchTable.fnDraw();
            searchTable=$('#resultTable').dataTable( {
                "bJQueryUI": true,
                "sPaginationType": "full_numbers",
                'sDom':'rt<\'bottom\'lip><\'clear\'>',
                'bDeferRender': true,
                'bPaginate': true,
                'bLengthChange': true,
                'bFilter': true,
                'iDisplayLength': 10,
                'aoColumns': colDefs,
                "aaSorting": [[5,'asc'], [1,'desc']],
                'oLanguage': { 
                    'sUrl': '/js/dataTable.lang.it'
                },
                'aLengthMenu': [[5,10, 25, 50,100 -1], [5,10, 25, 50,100, 'All']],
                "aaData":resultData
                //"bServerSide": true,
                //'sAjaxSource': '/services/xSearch.php',
                //'fnServerData': function ( sSource, aoData, fnCallback ) {
                //  aoData.push({'name':'view','value':viewtype});
                //  $.ajax( {
                //          'dataType': 'json', 
                //          'type': 'POST', 
                //          'url': sSource, 
                //          'data': aoData, 
                //          'success': fnCallback
                //  } );
                //}
            });
            $('#searchTab').hide();
			$('#resultTab').show();
            
		}
	});
}
$(document).ready( function () {
    $("#tabs").tabs();
    //searchTable=$('#resultTable').dataTable( {
    //    "bJQueryUI": true,
    //    "sPaginationType": "full_numbers",
    //    'sDom':'rt<\'bottom\'lip><\'clear\'>',
    //    'bDeferRender': true,
    //    'bPaginate': true,
    //    'bLengthChange': true,
    //    'bFilter': true,
    //    'iDisplayLength': 10,
    //    'aoColumns': colDefs,
    //    "aaSorting": [[5,'asc'], [1,'desc']],
    //    'oLanguage': { 
    //        'sUrl': '/js/dataTable.lang.it'
    //    },
    //    'aLengthMenu': [[5,10, 25, 50,100 -1], [5,10, 25, 50,100, 'All']],
    //    "aaData":resultData
        //"bServerSide": true,
        //'sAjaxSource': '/services/xSearch.php',
        //'fnServerData': function ( sSource, aoData, fnCallback ) {
        //  aoData.push({'name':'view','value':viewtype});
        //  $.ajax( {
        //          'dataType': 'json', 
        //          'type': 'POST', 
        //          'url': sSource, 
        //          'data': aoData, 
        //          'success': fnCallback
        //  } );
        //}
    //});
    
  });