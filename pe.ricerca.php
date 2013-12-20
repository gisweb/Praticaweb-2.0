<?php
require_once 'login.php';
require_once APPS_DIR.'lib/tabella_v.class.php';
?>
<html>
<head>

<title>Ricerca pratica</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<link rel="stylesheet" type="text/css" href="/css/default/easyui.css">
<link rel="stylesheet" type="text/css" href="/css/icon.css">
<script type="text/javascript" src="/js/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/js/locale/easyui-lang-it.js"></script>
<script type="text/javascript" src="/js/datagrid-detailview.js"></script>

<script language="javascript">
    $(document).ready(function(){
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
            $('body').append('<form id="frm-report" action="./services/xReport.php" method="POST" target="reportPraticaweb"><input type="hidden" value="" name="elenco" id="elencopratiche"/></form>')
            $('#elencopratiche').val($('#elenco').val())
            $('#frm-report').submit();
        });
        $('#btn-back').button({
            icons:{primary:'ui-icon-arrowreturnthick-1-w'}
        }).bind('click',function(event){
            event.preventDefault();
            $( "#result-container" ).hide( 'slide', 500 );
            $( "#ricerca" ).show( 'slide', 500 );
        });
        
        $('#avvia-ricerca').button({
            icons:{primary:'ui-icon-search'}
        }).bind('click',function(event){

            event.preventDefault();
            var oper=$('#op').val();
            dataPost=getSearchFilter();
            $('#ricerca').hide('slide',500)
            $('#result-container').show('slide',500)
            $('#result-table').datagrid({
                title:'Risultato della ricerca',
                url:searchUrl,
                method:'post',
                nowrap:false,
                columns:colsDef['pratica'],
                fitColumns:false,
                pagination:true,
                autoRowHeight:true,

                queryParams:{data:dataPost,action:'search',op:oper},
                view: detailview,
                detailFormatter:function(index,row){
                    return '<div class="ddv" style="padding:5px 0;background-color:#EEF7FF"></div>';
                },
                onLoadSuccess:function(data){
                    $('#elenco').val(data['elenco_id']);
                },
                onExpandRow: function(index,row){
                    var ddv = $(this).datagrid('getRowDetail',index).find('div.ddv');
                    var text = '\n\
<table width="100%">\n\
<tr>\n\
<td width="10%"><div class="datagrid-cell"><b>Catasto Terreni :</b></div></td>\n\
<td width="40%"><div class="datagrid-cell">' + row.elenco_ct + '</div></td>\n\
<td width="10%" rowspan="2"><div class="datagrid-cell"><b>Ubicazione :</b></div></td>\n\
<td width="40%"rowspan="2"><div class="datagrid-cell">' + row.ubicazione + '</div></td>\n\
</tr>\n\
<tr>\n\
<td width="10%"><div class="datagrid-cell"><b>Catasto Urbano :</b></div></td>\n\
<td><div class="datagrid-cell">' + row.elenco_cu + '</div></td>\n\
</tr>\n\
<tr>\n\
<td width="10%"><div class="datagrid-cell"><b>Richiedenti :</b></div></td>\n\
<td colspan="3"><div class="datagrid-cell">' + row.richiedente + '</div></td>\n\
</tr>\n\
<tr>\n\
<td width="10%"><div class="datagrid-cell"><b>Progettisti :</b></div></td>\n\
<td colspan="3"><div class="datagrid-cell">' + row.progettista + '</div></td>\n\
</tr>\n\
</table>';
                    $(ddv).html(text);
                    $('#result-table').datagrid('fixDetailRowHeight',index);
                }
            });
        });
    });
    var result={};
    /*function getSearchFilter(){
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
    }*/
var colsDef={
    civici:[[
        {title:'Indirizzo',field:'indirizzo',sortable:true,width:1000},
        //{title:'Via',field:'via',sortable:true,width:500},
        //{title:'Civico',field:'civico',sortable:true,width:100}
    ]],
    pratica:[[
        {title:'',field:'pratica',sortable:false,width:20,formatter: function(value,row,index){return '<a target="new" href="praticaweb.php?pratica=' + value + '"><div class="ui-icon ui-icon-search"/></a>'}},
        {title:'Tipo Pratica',field:'tipo_pratica',sortable:true,width:150},
        {title:'Numero',field:'numero',sortable:true,width:100},
        {title:'Protocollo',sortable:true,field:'protocollo',width:100},
        {title:'Data Prot.',sortable:true,field:'data_prot',width:100},
        
        {title:'Intervento',sortable:true,field:'tipo_intervento',width:150},
        {title:'Oggetto',sortable:true,field:'oggetto',width:350}
    ]],
    default_cols:[[
        {title:'',sortable:true,field:'',width:100},
    ]]

}
var dataPost={};
</script>
</head>
<body>
<?php include "./inc/inc.page_header.php";?>
    <FORM id="ricerca" name="ricerca" method="post" action="pe.ricerca.php">
 	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Ricerca pratiche</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 			
				<!-- ricerca base pratica -->
                            <?php
                            
                            $tabella=new tabella_v("pe/ricerca.tab",'standard');
                            //$tabella->set_db($db);	
                            //$tabella_avanzata=new tabella_v("$tabpath/ricerca_avanzata.tab",'standard');
                            //in avanzata devo settare il db perchÃš c'Ãš un elenco

                            $tabella->edita();?>
				<!-- ricerca avanzata pratica -->


			</td>
		  </tr>
		  <tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   </tr>  
        </table>
            <div style="margin-top:20px;">
                <select id="op" class="textbox">
                    <option value="AND">Tutte le opzioni devono essere verificate</option>
                    <option value="OR">Almeno una opzione deve essere verificata</option>
                </select>
                <button style=";margin-left:20px;" id="avvia-ricerca">Avvia Ricerca</button>
            </div>                    
    </FORM>
<div id="result-container" >
    <table id="result-table" width="100%">
    </table>
    <div style="margin-top:20px;">
        <button id="btn-back">Torna alla Ricerca</button>
        <button id="btn-report">Crea Report</button>
        
        <input type="hidden" id="elenco" value=""/>
    </div>
</div>   
    
</body>
</html>
