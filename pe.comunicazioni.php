<?php
include_once("login.php");
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$idpratica=$_REQUEST["pratica"];
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

$tabpath="pe";
$config_file="$tabpath/comunicazioni";
include_once "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";
?>
<html>
<head>
<title>Provvedimenti - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS(Array("jquery.multi-select",'form/pe.comunicazioni'));
	utils::loadCss(Array("multi-select"));
?>
<style>
.ms-container .ms-selectable li.ms-elem-selectable,
.ms-container .ms-selection li.ms-elem-selection{
  border-bottom: 1px #eee solid;
  padding: 2px 10px;
  color: rgb(65, 85, 120);
  font-family:Verdana, Geneva, Arial, sans-serif; 
  font-size: 10px;
}div.ms-container {width:800px;}
</style>
<script>
function verificapec(pratica,uuid){
//    alert(uuid);
$.ajax({
                url:serverUrl,
                dataType:'json',
                type:'POST',
                data:{action:'verifica-pec',uuid:uuid,pratica:pratica},
                success: function(data, textStatus, jqXHR){
                    if (data["success"]==1){
//console.log(data);
//                        if ($("div#verifica-pec")){
                            $("div#verifica-pec").html('');
//                        }
//                        else{
//                            $(document).append('<div id="verifica-pec"></div>');
//                        }
                        var html = '<ol>';
                        var tmp = '';
                       for (var i = 0; i < data["result"].length; i++) {
                            tmp='<li>'+ data["result"][i] +'</li>';
                            html += tmp;
                        }    
                        html += '</ol>';
                        $("#verifica-pec").html(html);
                        var dialog = $("verifica-pec").dialog({
                            autoOpen: false,
                            height: 400,
                            width: 350,
                            modal: true,
                            buttons: {
                                Cancel: function() {
                                    dialog.dialog( "close" );
                                }
                            }
                        });
                        dialog.dialog("open");

                    }
                    else{
                        alert(data["message"]);
                    }
                }
            });


}
</script>
</head>
<body>
<?php
if (($modo=="edit") or ($modo=="new") ){
	//---<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  EDITA ELENCO ITER >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------------------------------>
		unset($_SESSION["ADD_NEW"]);
		$id = $_POST["id"];
		$tabella=new tabella_v("$tabpath/comunicazioni",$modo);
		include "./inc/inc.page_header.php";?>
		<form method="post" name="comunicazioni" action="praticaweb.php">		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">					  
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?php
					if($Errors){
						$tabella->set_errors($Errors);
						$tabella->set_dati($_POST);
					}
					else{
						$numrows=$tabella->set_dati("id=$id AND pratica=$idpratica");
					}
                  print $tabella->edita();
				?>	
				<input type="hidden" name="mode" value="<?php echo $modo;?>">
				<INPUT type="hidden" name="pratica" value="<?php echo $idpratica;?>">
				<INPUT type="hidden" name="id" value="<?php echo $id;?>">
				<input name="active_form" type="hidden" value="pe.comunicazioni.php">
				<br><br><br>
				<!-- fine contenuto-->			
			</td>
		  </tr>
		</TABLE>
		</form>		
<script>
//console.log($("data-plugins='multi-select'"));
$("[data-plugins='multi-select']").multiSelect({});
	$("[data-locked='protocollo']").each(function($k,v){
        if ("#protocollo").val() $(v).prop('readonly', true);
    });
</script>		
	<?php
}	
else{
	//-<<<<<<<<<<<<<<<<<<<<<< VISUALIZZA ITER >>>>>>>>>>>>>>>>>>>>>>>>>>>----------------------->	
		$tabella=new tabella_h("$tabpath/comunicazioni","list");
		$titolo = "Comunicazioni Inviate/Ricevute";
		$nrec=$tabella->set_dati("pratica = $idpratica");	?>			
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
					$tabella->set_titolo($titolo,"nuovo");
					$tabella->get_titolo();
					if ($nrec)	
						$tabella->elenco();
					else
						print ("<p><b>Nessuna Comunicazione</b></p>");			
					?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
<div id="verifica-pec"></div>
<?php
}
?>		
<script>
/*
$("[data-plugins='verifica-pec']").each(function(k,v){
        $(v).bind('click',functions(event){
            event.preventDefault();
            var d = $(v).data();
            $.ajax({
                url:serverUrl,
                dataType:'json',
                type:'POST',
                data:d,
                success: function(data, textStatus, jqXHR){
                    if (data["success"]==1){
                        if ($("div#verifica-pec")){
                            $("div#verifica-pec").html('');
                        }
                        else{
                            $(document).append('<div id="verifica-pec"></div>');
                        }
                        var html = '';
                        var tmp = ''
                        $(data["result"]).each(function(v){
                            tmp='<li>'+ v +'</li>';
                            
                        });
                        html = '<ol>' + tmp + '</ol>';
                        $("div#verifica-pec").html(html);
                        $("div#verifica-pec").dialog({
                            autoOpen: true,
                            height: 400,
                            width: 350,
                            modal: true,
                            buttons: {
                                Chiudi: function() {
                                    dialog.dialog( "close" );
                                }
                            }
                        });
                    }
                    else{
                        alert(data["message"]);
                    }
                }
            });
        });
    });
*/
</body>
</html>
