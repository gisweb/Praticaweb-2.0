<?php
/*
form per il calcolo automatico oneri di urbanizzazione e costo di costruzione Regione Liguria
in modalità  edit permette il calcolo automatico o la modifica di un calcolo esistente 
in modalità  view elenca in ordine cronologico inverso i calcoli fatti


*/
include_once("login.php");
include "./lib/tabella_v.class.php";
$self=$_SERVER["PHP_SELF"];
$step=$_POST["step"];
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');$calcolo=$_POST["calcolo"];
$id=$_REQUEST["id"];
unset($_SESSION["ADD_NEW"]);
$titolo=$_SESSION["TITOLO_$idpratica"];
$tabpath="oneri";

$db=appUtils::getDB();
$sql="SELECT * FROM oneri.e_interventi order by tabella,descrizione";
$res=$db->fetchAll($sql);
foreach($res as $val){
    $interventi[$val["tabella"]][$val["anno"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
}
$sql="SELECT * FROM oneri.e_tariffe order by anno,tabella,descrizione";
$res=$db->fetchAll($sql);
foreach($res as $val){
    $tariffe[$val["anno"]][]=Array("id"=>$val["tabella"],"opzione"=>$val["descrizione"]);
}

$sql="SELECT * FROM oneri.e_c1 order by tabella,descrizione";
$res=$db->fetchAll($sql);
foreach($res as $val){
    $c1[$val["tabella"]][$val["anno"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
}
$sql="SELECT * FROM oneri.e_c2 order by tabella,descrizione";
$res=$db->fetchAll($sql);
foreach($res as $val){
    $c2[$val["tabella"]][$val["anno"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
}
$sql="SELECT * FROM oneri.e_c3 order by tabella,descrizione";
$res=$db->fetchAll($sql);
foreach($res as $val){
    $c3[$val["tabella"]][$val["anno"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
}
$sql="SELECT * FROM oneri.e_c4 order by tabella,descrizione";
$res=$db->fetchAll($sql);
foreach($res as $val){
    $c4[$val["tabella"]][$val["anno"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
}
$sql="SELECT * FROM oneri.e_c5 order by tabella,descrizione";
$res=$db->fetchAll($sql);
foreach($res as $val){
    $c5[$val["tabella"]][$val["anno"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
}

$sql="SELECT * FROM oneri.e_d1 order by tabella,descrizione";
$res=$db->fetchAll($sql);

foreach($res as $val){
    $d1[$val["tabella"]][$val["anno"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
}
$sql="SELECT * FROM oneri.e_d2 order by tabella,descrizione";
$res=$db->fetchAll($sql);
foreach($res as $val){
    $d2[$val["tabella"]][$val["anno"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
}
//print_array($el);
?>

<html>
<head>
<title>Calcolo Oneri - <?php echo $titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>
<!--<SCRIPT language="javascript" src="src/rpc.oneri_calcolati.js" type="text/javascript"></SCRIPT>
<SCRIPT language="javascript" src="src/http_request.js" type="text/javascript"></SCRIPT>-->

<script language=javascript>
    var selectdb = new Object;
    selectdb['intervento'] = <?php print json_encode($interventi)?>;
    selectdb['tabella'] = <?php print json_encode($tariffe)?>;
    selectdb['c1'] = <?php print json_encode($c1)?>;
    selectdb['c2'] = <?php print json_encode($c2)?>;
    selectdb['c3'] = <?php print json_encode($c3)?>;
    selectdb['c4'] = <?php print json_encode($c4)?>;
    selectdb['c5'] = <?php print json_encode($c5)?>;
    selectdb['d1'] = <?php print json_encode($d1)?>;
    selectdb['d2'] = <?php print json_encode($d2)?>;
    
    function set_perc(){
        if (($('#intervento').val()>0) || $('#intervento').val()=='')
            $('#perc').hide();
        else
            $('#perc').show();

    }

    function confirmSubmit()
    {	
        return confirm('Sicuro di voler eliminare definitivamente il calcolo?');
    }
    function checkdati(){
        for(i=0;i<document.oneri.elements.length;i++){
            var obj=document.oneri.elements[i];
            if ((obj.type=='select-one') && (obj.value=='')){
                alert('Per effettuare il calcolo automatico è necessario selezionare il campo '+obj.name);
                return false;
            }
        }
        return true;
    }
    $(document).ready(function(){
        if ($('#mode').val()=='new') $('#anno').trigger('change');
        set_perc();
    });
    
</script>

</head>
<!--<body  background="" onload="//javascript:init(<?//"$idpratica,'$modo'"?>);">-->
<body  background="">
<?php

if (($modo=="new") or ($modo=="edit")){
//########### MODALITA NUOVO CALCOLO O EDITA CALCOLO ESISTENTE ################
	$tabella=new tabella_v("$tabpath/calcolati",$modo);
	$tabella->set_tabella_elenco("e_oneri");	
	if ($modo=="edit"){//sto editando un calcolo esistente sul db
		if ($calcolo)
			$dati=$_POST;
		else
            $dati="id=$id";
	}
       else if ($modo=="new") $dati="pratica=$idpratica";
?>

<?php include "./inc/inc.page_header.php";	?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <FORM id="oneri" name="oneri" method="post" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Calcolo Oneri</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
			<?php
                
                if(isset($Errors) && $Errors){
                    $tabella->set_errors($Errors);
                    $tabella->set_dati($_POST);
		}
                elseif ($modo=="edit"){	
                    $tabella->set_dati("id=$id");
                }
                else{
                    $pr=new pratica($idpratica);
                    $request["anno"]=appUtils::getAnnoOneri(idPratica,$pr->info['data_presentazione']);
                    $tabella->set_dati($request);
                }
		$tabella->edita();?>
				<!-- fine contenuto-->
			</td>
		  </tr>
		</TABLE>
        <input name="active_form" type="hidden" value="oneri.calcolati.php">
        <input name="pratica" id="pratica" type="hidden" value="<?=$idpratica?>">
        <input name="calcolo" type="hidden" value="1">
        <input name="id" type="hidden" value="<?=$id?>">
        <input id="mode" name="mode" type="hidden" value="<?=$modo?>">	
    </FORM>		

	</body>
</html>
<?php }
else{
?>

		
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Calcolo Oneri in dettaglio</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		<TR>
			<TD>
				<?php
				$tabella=new tabella_v("$tabpath/calcolati");
				$nrec=$tabella->set_dati("pratica=$idpratica");
				if($nrec>0){
					$tabella->set_tabella_elenco("oneri.e_tariffe");	
					$tabella->set_titolo("calcolo","modifica",array("id"=>"","pratica"=>""));
					$tabella->elenco();
				}
				else{
					echo "<p><b>Nessun calcolo</b></p>";
				}				
					
					?>
			</TD>
		</TR>
<?php if ($tabella->editable){ ?>		
		<tr>
		  <td>
				&nbsp;<input  name=""  id="" class="hexfield1" style="width:70px" type="button" value="chiudi" onClick="javascript:window.location='oneri.importi.php?pratica=<?=$_REQUEST["pratica"]?>'" >
		  </td>
		</tr>
<?php } ?>		
</body>

<?php }?>
</html>
