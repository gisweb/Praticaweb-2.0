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
<script language="javascript">
    $(document).ready(function(){
        $(".textbox").bind("keyup",function(event){
            if(event.keyCode == 13){
                $("#avvia-ricerca").click();
            }
        });
    });
    function getSearchFilter(){
	var searchFilter=new Object();
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
                filter=name+"::varchar ilike '"+$('#1_'+name).val()+"'";
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
                            
                            $tabella=new tabella_v("pe/ricerca2.tab",'standard');
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
                   <tr> 
			<td align="left">
                            <button id="avvia-ricerca">Avvia Ricerca</button>
                            <script>
                                $('#avvia-ricerca').button({
                                    icons:{primary:'ui-icon-search'}
                                }).bind('click',function(event){
                                    var dataSend={};
                                    event.preventDefault();
                                   dataSend=getSearchFilter();
                                   console.log(dataSend);
                                });
                            </script>
                        </td>            
		   </tr>  
		</TABLE>
		
        </FORM>
    
</body>
</html>
