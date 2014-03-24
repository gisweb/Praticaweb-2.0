<?php
require_once 'login.php';
require_once APPS_DIR.'lib/tabella_v.class.php';
$currentDate  = mktime(0, 0, 0, date("m")  , date("d"), date("Y")); 
$today = date ("d/m/Y",$currentDate);
$nextMonth = date("d/m/Y", $currentDate+30 * 24 * 3600);
?>
<html>
<head>
    <title>Scadenze</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::loadCss();
    utils::loadJS();
?>
    <link rel="stylesheet" type="text/css" href="/css/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="/css/icon.css">
    <script type="text/javascript" src="/js/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="/js/locale/easyui-lang-it.js"></script>
    <script type="text/javascript" src="/js/datagrid-filter.js"></script>
    
    <script type="text/javascript" src="/js/init.search.js"></script>
    <script>
        $(document).ready(function(){
            <?php
                print "var data_start='".$today."';\n\t\t";
                print "var data_end='".$nextMonth."';\n\t\t";
                printf("var tipo_scadenze=%s;\n\t\t",utils::getDbDataToJson("SELECT value,text FROM ((SELECT '' as value,'Tutte' as text,-1 as ordine) UNION ALL (SELECT codice as value,nome as text,ordine FROM pe.e_scadenze WHERE enabled=1) order by 3,2) A", $conn));
            ?>
            $('#op_pe-scadenze-scadenza').val('between');
            $('#1_pe-scadenze-scadenza').val(data_start);
            $('#2_pe-scadenze-scadenza').val(data_end);
            $('#op_pe-scadenze-scadenza').trigger('change');
            
            $(".textbox").bind("keyup",function(event){
                if(event.keyCode == 13){
                    $("#avvia-ricerca").click();
                }
            });
            $( "#result-container" ).hide();
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
            $("#avvia-ricerca").button({
                icons:{primary:'ui-icon-search'}
            }).bind('click',function(event){
                event.preventDefault();
                dataPost=getSearchFilter();
                $('#ricerca').hide('slide',500);
                $('#result-container').show('slide',500);
                var dg=$('#result-table').datagrid({
                    title:'Risultato della ricerca',
                    url:searchUrl,
                    method:'post',
                    nowrap:false,
                    columns:colsDef['scadenze'],
                    fitColumns:false,
                    pagination:true,
                    autoRowHeight:true,

                    queryParams:{data:dataPost,action:'scadenze'},
                    onLoadSuccess:function(data){
                    $('#elenco').val(data['elenco_id']);
                    /*$.each($('tr.datagrid-filter-row td'),function(k,v){
                        if($(v).attr('field')!='cod_scadenza') $(v).hide();
                        //console.log(v);
                    });*/
                }
            });
            
            //dg.datagrid('removeFilterRule');
            /*dg.datagrid('enableFilter',[{
                field:'cod_scadenza',
                type:'combobox',
                options:{
                        panelHeight:'auto',
                        data:tipo_scadenze,
                        onChange:function(value){
                                if (value == ''){
                                        dg.datagrid('removeFilterRule', 'cod_scadenze');
                                } else {
                                        dg.datagrid('removeFilterRule', 'cod_scadenze');
                                        dg.datagrid('addFilterRule', {
                                                field: 'cod_scadenza',
                                                op: 'equal',
                                                value: value
                                        });
                                }
                                dg.datagrid('doFilter');
                        }
                }
            
            }]);*/
        });
    });
    </script>
</head>
<body>
    <?php include "./inc/inc.page_header.php";?>
    <FORM id="ricerca" name="ricerca" method="post" action="pe.ricerca.php">
 	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
            <tr> 
                  <td> 
                  <!-- intestazione-->
                          <H2 class="blueBanner">Elenco Scadenze delle Pratiche</H2>
                  <!-- fine intestazione-->
                  </td>
            </tr>
            <tr> 
                  <td> 			
                          <!-- ricerca base pratica -->
                      <?php

                      $tabella=new tabella_v("pe/report_scadenze.tab",'standard');	
                      $tabella->edita();
                      ?>
                          <!-- ricerca avanzata pratica -->


                  </td>
            </tr>
            <tr> 
                          <!-- riga finale -->
                          <td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
             </tr>  
        </table>
        <div style="margin-top:20px;">
            <button style=";margin-left:20px;" id="btn-close">Chiudi</button>
            <button style=";margin-left:20px;" id="avvia-ricerca">Avvia Ricerca</button>
        </div> 
    </form>    
    <div id="result-container" >
        <div style="display:inline;width:300px;margin-right:10px;"></div>
        <table id="result-table" width="100%">
        </table>
        <div style="margin-top:20px;">
            <button id="btn-back">Torna alla Ricerca</button>
            <!--<button id="btn-report">Crea Report</button>-->

            <input type="hidden" id="elenco" value=""/>
        </div>
    </div>      
</body>
</html>