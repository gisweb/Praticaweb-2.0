<?php
require_once 'login.php';
require_once APPS_DIR.'lib/tabella_v.class.php';
?>
<html>
<head>

<title>Ricerca pratica</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>
<link rel="stylesheet" type="text/css" href="/css/default/easyui.css">
<link rel="stylesheet" type="text/css" href="/css/icon.css">
<script type="text/javascript" src="/js/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/js/locale/easyui-lang-it.js"></script>
<script type="text/javascript" src="/js/datagrid-detailview.js"></script>

<script language="javascript">
    
    $(document).ready(function(){
        $( "#result-container" ).hide();
        $(".textbox").bind("keyup",function(event){
            if(event.keyCode == 13){
                $("#avvia-ricerca").click();
            }
        });
        
        $('#btn-close').button({
            icons:{primary:'ui-icon-circle-close'}
        }).bind('click',function(event){
            event.preventDefault();
            closeWindow();
        });
        $( "input[name='groupby']").bind('change',function(event){
            event.preventDefault();
            var v=$( "input[name='groupby']:checked" ).val();
            if (v=='civico'){
                $('#ricerca_indirizzi-0 tr:eq(1)').hide();
                $('#ricerca_indirizzi-0 tr:eq(1) input[type="text"]').val('');
                $('#ricerca_indirizzi-0 tr:eq(2)').hide();
                $('#ricerca_indirizzi-0 tr:eq(2) input[type="text"]').val('');
                $('#ricerca_indirizzi-0 tr:eq(0)').show();
            }
            else if(v=='particella-urbano'){
                $('#ricerca_indirizzi-0 tr:eq(0)').hide();
                $('#ricerca_indirizzi-0 tr:eq(0) input[type="text"]').val('');
                $('#ricerca_indirizzi-0 tr:eq(1)').hide();
                $('#ricerca_indirizzi-0 tr:eq(1) input[type="text"]').val('');
                $('#ricerca_indirizzi-0 tr:eq(2)').show();
            }
            else{
                $('#ricerca_indirizzi-0 tr:eq(0)').hide();
                $('#ricerca_indirizzi-0 tr:eq(0) input[type="text"]').val('');
                $('#ricerca_indirizzi-0 tr:eq(2)').hide();
                $('#ricerca_indirizzi-0 tr:eq(2) input[type="text"]').val('');
                $('#ricerca_indirizzi-0 tr:eq(1)').show();
            }
        });
        $( "input[name='groupby']").trigger('change');
        $('#avvia-ricerca').button({
            icons:{primary:'ui-icon-search'}
        }).bind('click',function(event){

            event.preventDefault();
            var fld=$( "input[name='groupby']:checked" ).val();
            dataPost=getSearchFilter();
            $('#ricerca').hide('slide',500)
            $('#result-container').show('slide',500);
            if (fld=='civico'){
                var id='#result-container #result-table-civici';
                $('#result-container').html($('#container-civici').clone());
                
            }
            else{
                var id='#result-container #result-table-catasto';
                $('#result-container').html($('#container-catasto').clone());
            }
            $('#result-container #btn-back').button({
                icons:{primary:'ui-icon-arrowreturnthick-1-w'}
            }).bind('click',function(event){
                event.preventDefault();
                $( "#result-container" ).hide('slide',500);
                $( "#result-container" ).html('');
                $( "#ricerca" ).show('slide',500);
            });
            $(id).treegrid({
                title:'Risultato della ricerca',
                url:searchUrl,
                method:'post',
                idField:'id',
                treeField:'name',
                nowrap:false,
                rownumbers: true,
                queryParams:{data:dataPost,action:'group',field:fld}
            });
           
        });
    });
    var result={};
    var dataPost={};
    function formatLink(value,rowData,rowIndex){
        var text=value;
        if(rowData['pratica']){
            text='<a target="praticaweb" href="praticaweb.php?pratica= ' + rowData["pratica"] + '&active_form=pe.ubicazione?pratica=' + rowData["pratica"] + '">' + value +'</a>';
        }
        return text;
    }
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
                            
                            $tabella=new tabella_v("pe/ricerca_indirizzi.tab",'standard');
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
                <fieldset style="display:inline;border:0px;">
                    <legend class="stiletabella" style="font-weight:bold;">Raggruppa per</legend>
                    <input type="radio" class="textbox" style="border:0px;" name="groupby" value="civico" checked><span class="stiletabella" style="font-weight:bold;">Indirizzo</span>
                    <input type="radio" class="textbox" style="border:0px;" name="groupby" value="particella-terreni"><span class="stiletabella" style="font-weight:bold;">Particella C.T.</span>
                    <input type="radio" class="textbox" style="border:0px;" name="groupby" value="particella-urbano"><span class="stiletabella" style="font-weight:bold;">Particella C.U.</span>                
                </fieldset>
                <button id="btn-close" style="margin-left:20px;">Chiudi</button>
                <button style="margin-left:20px;" id="avvia-ricerca">Avvia Ricerca</button>
            </div>
    </FORM>
<div id="result-container" >
    
    
</div>   
 
    <div id="res" style="display:none;">
        <div id="container-civici">
            <table id="result-table-civici" width="100%">
                <thead>
                   <tr>
                   <th data-options="field:'name',formatter:formatLink" width="300px">Indirizzo</th>
                   <th data-options="field:'pratica',hidden:true"></th>
                   <th data-options="field:'oggetto'" width="300px">Oggetto</th>
                   <th data-options="field:'ct'" width="300px" >Catasto Terreni</th>
                   <th data-options="field:'cu'" width="300px">Catasto Urbano</th>
                   </tr>
               </thead>
           </table>
            <div style="margin-top:20px;">
               <button id="btn-back">Torna alla Ricerca</button>
               <input type="hidden" id="elenco" value=""/>
           </div>  
        </div>  
       <div id="container-catasto">
            <table id="result-table-catasto" width="100%">
                 <thead>
                    <tr>
                    <th data-options="field:'name',formatter:formatLink" width="300px">Particella</th>
                    <th data-options="field:'pratica',hidden:true"></th>
                    <th data-options="field:'oggetto'" width="300px">Oggetto</th>
                    <th data-options="field:'ubicazione'" width="300px" >Indirizzo</th>
                    <th data-options="field:'cu'" width="300px">Catasto</th>
                    </tr>
                </thead>
            </table>
            <div style="margin-top:20px;">
                <button id="btn-back">Torna alla Ricerca</button>
                <input type="hidden" id="elenco" value=""/>
            </div>  
       </div>
         
    </div>    
</body>
</html>
