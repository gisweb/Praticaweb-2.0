<?php
//print_r($_REQUEST);
include_once("login.php");
include "./lib/tabella_h.class.php";
include "./lib/tabella_v.class.php";
$tabpath="stp";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$id=(isset($_REQUEST["id"]))?($_REQUEST["id"]):(null);
$file_config="$tabpath/modelli";

if (in_array(strtolower($_REQUEST["azione"]),Array('elimina','salva','annulla'))){
    require_once "./db/db.stp.modelli.php";
} 

$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

?>
<html>
<head>
    <title>ELENCO MODELLI DI STAMPA</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?php
	utils::loadJS();
	utils::loadCss();
?>
	<script>
    		$(document).ready(function(){
			
			$('#btn-print-fields').button({
				icons:{primary:'ui-icon-info'},
				label:'Visualizza campi'
			}).bind('click',function(event){
				event.preventDefault();
				/*$.ajax({
					url:'./services/xServer.php',
					dataType:'JSON',
					type:'POST',
					data:{action:'printFieldsList'},
				});*/
                                window.open('/stp.fieldsList.php');
			});
		});
	</script>
</head>
<body>
<?php
 if (in_array($modo,Array("edit","new"))) {
    
	$tabella=new Tabella_v($file_config,$modo);
    //print_array($tabella);
    if(isset($Errors) && $Errors){
        $tabella->set_errors($Errors);
        $tabella->set_dati($_POST);
        $intestazione="Modello ".$_REQUEST["nome"];
    }
    elseif ($modo=="edit"){	
        $tabella->set_dati("id=$id");
        $intestazione="Modello ".$tabella->array_dati[0]["nome"];
        
    }
    else{
        $tabella->set_dati($_POST);
        $intestazione="Nuovo Modello di stampa";
    }
	unset($_SESSION["ADD_NEW"]);	
	include "./inc/inc.page_header.php";
?>
<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	<FORM id="" ENCTYPE="multipart/form-data" name="modelli" method="post" action="stp.modelli.php" >
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
				<H2 class="blueBanner"><?=$intestazione?></H2>
				<?php
				$tabella->edita();?>			  
			</td>
		  </tr>

		</TABLE>
        <input name="active_form" type="hidden" value="stp.modelli.php">				
        <input name="mode" type="hidden" value="<?=$modo?>">
        <input name="id" type="hidden" value="<?=$id?>">
    </FORM>
<?php
}
elseif($modo=="view"){
?>
<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->

    <H2 class="blueBanner">Avvio del procedimento e comunicazione responsabile</H2>
    <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
      <TR> 
        <TD> 
        <!-- contenuto-->
<?php
        $pr=new pratica($idpratica);
        $tabella=new tabella_v($file_config,"view");
        $nrec=$tabella->set_dati("id=$id");
        $tabella->set_titolo("Modello ".$tabella->array_dati[0]["nome"],"modifica",Array("id"=>""));
        $tabella->get_titolo();
        $tabella->tabella();
?>
			<span id="btn-print-fields"/>
        <!-- fine contenuto-->
         </TD>
      </TR>
    </TABLE>
	

    <div id="divPreview" style="display:none;">
        <fieldset>
            <legend>Numero Pratica</legend>
            <input type="text" id="numero" style="width:200px;" value=""/>
            <input type="hidden" id="n-pratica" value="">
            <input type="hidden" id="modello" value="<?php echo $id;?>">
        </fieldset>
        <hr>
        <div id="message" style="display:none;margin: 10px;"></div>
        <div id="btn-preview"></div>
                
        
    </div>
	
    <script>	
        $('#btn-preview').button({
            icons:{primary:'ui-icon-print'},
            label:'Apri documento'
        }).bind('click',function(event){
            event.preventDefault();
            $("#message").html("").hide();
            $('#n-pratica').val('');
            var num = $('#numero').val();
            $.ajax({
                url:'./services/xSuggest.php',
                dataType:'JSON',
                type:'POST',
                async:false,
                data:{field:'numero-pratica','term':num},
                success:function (data,textStatus,jqXHR) {
                    if (data.length==0 || !data[0]['id']){
                        var message=sprintf("<b>La pratica n° %s non esiste<b/>",num);
                        
                        $("#message").html(message).show();
                        
                    }
                    else if(data && data.length==1){
                        $('#n-pratica').val(data[0]['id']);
                    }
                    else{
                        var message=sprintf("<b>Molteplici pratiche relative al n° %s<b/>",num);
                        $("#message").html(message).show();
                    }
                    
                }
            });
            
            var pratica = $('#n-pratica').val();
            var modello = $('#modello').val();
            
            if (pratica){
                $('body').append('<form id="frm-preview" action="./stp.preview.php" method="POST" target="_new"><input name="pratica" value="' + pratica + '"/><input name="modello" value="' + modello + '"/></form>');
                $('#frm-preview').submit();
                $('#frm-preview').remove();
            }   
            
        });
    </script>
<?php
}
else{
	$file_tab = ($_REQUEST["tipo"]=='html')?('modelli_html'):('modelli');
    $tabella_modelli=new Tabella_h("$tabpath/$file_tab",'list');
    
    $sql="select distinct opzione,form,stampa from stp.e_form order by stampa;";
    $db->sql_query ($sql);
    $elenco_form = $db->sql_fetchrowset();
?>
    <form method="post" name="modelli" action="">
        <input type="hidden" name="azione" id="azione" value="">
        <input type="hidden" name="idriga" id="idriga" value="0">
    </form>
    <H2 class="blueBanner">Elenco dei modelli</H2><form method="post" name="modelli" action="">
        <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
        
<?php	
    foreach ($elenco_form as $row){
		$form=$row["form"];
		$desc=$row["opzione"];

		//Visualizzare solo quelli inerenti il form e opzioni 
		$num_modelli=$tabella_modelli->set_dati("form='$form'");
        $tabella_modelli->set_titolo($desc,"nuovo",array("form"=>$form));
		$upload_butt="";

		$tabella_modelli->set_tag($idpratica);
		
		?>
                <tr> 
                  <td> 
                  <!--  intestazione-->
                      <?php
			$tabella_modelli->get_titolo();
                          if ($num_modelli) 
                              $tabella_modelli->elenco();
                          else
                              print ("<p><b>Nessun Modello per questo Form</b></p>");

                      ?>
                  <!-- fine intestazione-->
                  <br>
                  </td>
                </tr>
<?php
    }
?>
                <tr>
                    <td>
                        <span id="btn_close"></span><span id="btn_check"></span>
                        <script>
                            $('#btn_close').button({
                                'icons':{'primary':'ui-icon-close'},
                                'label':'Chiudi'
                            }).click(function(){
                                window.opener.focus();
                                window.close();
                            });
                            /*$('#btn_check').button({
                                'icons':{'primary':'ui-icon-gear'},
                                'label':'Verifica Modelli di Stampa'
                            }).click(function(){
                                alert('TODO');
                            });*/
                        </script>
                        
                    </td>
                </tr>
        </TABLE>
<?php
}
?>
</body>
</html>

