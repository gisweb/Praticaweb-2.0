var result={};
var dataPost={};
$(document).ready(function(){
	
	showprov.value="0";
	
    $(".textbox").bind("keyup",function(event){
        if(event.keyCode == 13){
            $("#avvia-ricerca").click();
        }
    });
    $( "#result-container" ).hide();
    $('#btn-report').button({
        icons:{primary:'ui-icon-document'}
    }).bind('click',function(event){
        event.preventDefault();
        $('#frm-report').remove();
        $('body').append('<form id="frm-report" action="./services/xEstratto.php" method="POST" target="reportPraticaweb"><input type="hidden" value="" name="elenco" id="elencocapitoli"/></form>')
        $('#elencocapitoli').val($('#elenco').val())
        $('#frm-report').submit();
    });
    $('#btn-back').button({
        icons:{primary:'ui-icon-arrowreturnthick-1-w'}
    }).bind('click',function(event){
        event.preventDefault();
        $( "#result-container" ).hide( 'slide', 500 );
        $( "#estrattoconto" ).show( 'slide', 500 );
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
	    //alert(JSON.stringify(dataPost));
		var searchUrlEstratti='/services/xSearchEstratti.php';
		var actionsel = 'search';
		var colsDefEst;
		
		if (groupprat.value=="1") actionsel='grouped';
		
		if (showprov.value=="1") {
			actionsel='provvisori'; 
			groupprat.value="1";
			}
		
		if (groupprat.value=="1")
		colsDefEst={
		estratto:[[
                    {title:'Operatore',field:'operatore',sortable:true,width:100},    
					{title:'Data Pagamento',field:'data_pagamento',sortable:true,width:100},    
					{title:'Modalita',field:'modalita',sortable:true,width:100},    
					{title:'Tipo',field:'tipo',sortable:true,width:100},    
					{title:'Descrizione',field:'descrizione',sortable:true,width:100},  
					{title:'Nr. Scontrino',field:'codice_pagamento',sortable:true,width:100},  					
					{title:'Importo',field:'sum',sortable:true,width:100, editor:{type:'numberbox'},formatter:function(val, row, idx){
																													return  '<span style="float:right;">'+val.replace(".",",")+'</span>';
																												}}, 
					{title:'Rif. Pratica',field:'numero',sortable:true,width:100}, 
					{title:'Data',field:'data_prot',sortable:true,width:100},  
					{title:'Pratica',field:'pratica',sortable:true,width:100, editor:{type:'numberbox'},formatter:function(val, row, idx){
																													if(val) return  '<span style="float:right;"><a href=javascript:NewWindow("praticaweb.php?pratica='+val+'","Praticaweb",0,0,"yes")>'+ 'Vai a pratica' +'</a></span>';
																												}},																																																							
                ]]
		};
		
		if (showprov.value=="1")
		colsDefEst={
		estratto:[[
                    {title:'Operatore',field:'operatore',sortable:true,width:100},    
					{title:'Data Pagamento',field:'data_pagamento',sortable:true,width:100},    
					{title:'Modalita',field:'modalita',sortable:true,width:100},    
					{title:'Tipo',field:'tipo',sortable:true,width:100},    
					{title:'Descrizione',field:'descrizione',sortable:true,width:100},  
					{title:'Nr. Scontrino',field:'codice_pagamento',sortable:true,width:100},  					
					{title:'Importo',field:'sum',sortable:true,width:100, editor:{type:'numberbox'},formatter:function(val, row, idx){
																													return  '<span style="float:right;">'+val.replace(".",",")+'</span>';
																												}}, 
					{title:'Rif. Pratica',field:'numero',sortable:true,width:100}, 
					{title:'Data',field:'data_prot',sortable:true,width:100},  
					{title:'Pratica',field:'pratica',sortable:true,width:100, editor:{type:'numberbox'},formatter:function(val, row, idx){
																													if(val) return  '<span style="float:center;"><a href=javascript:NewWindow("praticaweb.php?pratica='+val+'","Praticaweb",0,0,"yes")>'+ 'Vai a pratica' +'</a></span>';
																												}},																												
					//{title:'Pagam.',field:'id',sortable:true,width:80, editor:{type:'numberbox'},formatter:function(val, row, idx){
					//																								if(val) return  '<span style="float:center;"><a href=javascript:NewWindow("pe.pagamenti.php?mode=edit&id='+val+'","Praticaweb",0,0,"yes")>'+ 'Modifica' +'</a></span>';
					//																							}},
                ]]
		};
		
		if (groupprat.value=="0")
		colsDefEst={
		estratto:[[
					{title:'Modalita',field:'modalita',sortable:true,width:100},    
					{title:'Tipo',field:'tipo',sortable:true,width:100},    
					{title:'Descrizione',field:'descrizione',sortable:true,width:100},    
					{title:'Importo',field:'sum',sortable:true,width:100, editor:{type:'numberbox'},formatter:function(val, row, idx){
																													return  '<span style="float:right;">'+val.replace(".",",")+'</span>';
																												}}, 
                ]]
		};
		$('#estrattoconto').hide('slide',500);
        $('#result-container').show('slide',500);
        $('#result-table').datagrid({
            title:'Risultato della ricerca',
            url:searchUrlEstratti,
            method:'post',
            nowrap:false,
            columns:colsDefEst['estratto'],
            fitColumns:false,
            pagination:true,
            autoRowHeight:true,
			showFooter:true,
			rownumbers:true,
			singleSelect:true,
            queryParams:{data:dataPost,action:actionsel,op:oper},
            // view: myview,
            // detailFormatter:function(index,row){
                // return '<div class="ddv" style="padding:5px 0;background-color:#EEF7FF"></div>';
            // },
            onLoadSuccess:function(data){
				//alert(JSON.stringify(data['filter']));
                $('#elenco').val(data['elenco_id']);
            }

        });
    });
});
 