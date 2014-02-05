<?php
//Nota conservo il tipo per poter verificere se Ãš cambiato
include_once("login.php");
$tabpath="pe";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$idpratica=isset($_REQUEST["pratica"])?($_REQUEST["pratica"]):('');
$pr=new pratica($idpratica);
$pr->createStructure();
$file_config="$tabpath/avvio_procedimento";
$intestazione='Avvio del procedimento e comunicazione responsabile';
include "./lib/tabella_v.class.php";
$db=appUtils::getDB();
$sql="SELECT A.id,B.id as tipo,A.nome FROM pe.e_categoriapratica A inner join pe.e_tipopratica B ON(tipo=tipologia) WHERE A.enabled=1 AND B.enabled=1;";
$res=$db->fetchAll($sql);
foreach($res as $val){
    $categoria[$val["tipo"]][]=Array("id"=>$val["id"],"opzione"=>$val["nome"]);
}

?>
<html>
<head>
    <title>Avvio Procedimento - <?=$_SESSION["TITOLO_".$idpratica]?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
    utils::writeJS();
    utils::writeCSS();

?>

    <script>
    var selectdb = new Object;
    selectdb['categoria'] = <?php print json_encode($categoria)?>;
    $(document).ready(function(){
        if ($('#mode').val()=='new') $('#tipo').trigger('change');
        
    });
    </script>
</head>

<body>
<?php
 if (($modo=="edit") or ($modo=="new")) {

	$tabella=new Tabella_v($file_config,$modo);					
	unset($_SESSION["ADD_NEW"]);	
	include "./inc/inc.page_header.php";
?>
	

		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM id="" name="avvioproc" method="post" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
				<H2 class="blueBanner"><?=$intestazione?></H2>
				<?php
				if(isset($Errors) && $Errors){
					$tabella->set_errors($Errors);
					$tabella->set_dati($_POST);
				}
				elseif ($modo=="edit"){	
					$tabella->set_dati("pratica=$idpratica");
				}
				$tabella->edita();?>			  
			</td>
		  </tr>

		</TABLE>
<input name="active_form" type="hidden" value="pe.avvioproc.php">				
<input name="refpratica" type="hidden" value="<?=$_POST["refpratica"]?>">
<input name="riferimento" type="hidden" value="<?=$_POST["riferimento"]?>">				
<input name="oldtipo" type="hidden" value="<?=$tabella->get_campo("tipo")?>">

<input name="mode" type="hidden" id="mode" value="<?=$modo?>">
</FORM>
<div id="result" style="width:800px;height:600px;display:none;"></div>
<div id="waiting"></div>
<?//include "./inc/inc.window.php"; // contiene la gesione della finestra popup
}else{
?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->

		<H2 class="blueBanner">Avvio del procedimento e comunicazione responsabile</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?
                $pr=new pratica($idpratica);
                $tabella=new tabella_v($file_config,"view");
				$tabella->set_titolo("Dati della pratica","modifica");
				$nrec=$tabella->set_dati("pratica=$idpratica");
				$tabella->elenco();
				$tabella->close_db();?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
<?php
}
?>
</body>
</html>
