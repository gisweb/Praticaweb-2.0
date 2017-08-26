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

//Imposto i permessi di default per il modulo
$_SESSION["PERMESSI"]= min($_SESSION["PERMESSI_$idpratica"],$_SESSION["PERMESSI_A_$idpratica"]);


include "./lib/tabella_v.class.php";
$db=appUtils::getDB();
$sql="SELECT A.id,B.id as tipo,A.nome,B.nome as tipopratica FROM pe.e_categoriapratica A inner join pe.e_tipopratica B ON(tipo=tipologia) WHERE A.enabled=1 AND B.enabled=1;";
$res=$db->fetchAll($sql);
foreach($res as $val){
    $categoria[$val["tipo"]][]=Array("id"=>$val["id"],"opzione"=>$val["nome"]);
    $tipopratica[$val["tipo"]]=$val["tipopratica"];
}
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

?>
<html>
<head>
    <title>Avvio Procedimento - <?=$_SESSION["TITOLO_".$idpratica]?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <script>
        var selectdb = new Object;
        selectdb['categoria'] = <?php print json_encode($categoria)?>;
        selectdb['tipo'] = <?php print json_encode($tipopratica)?>;
    </script>
<?php
    utils::loadJS(Array('form/pe.avvioproc'));
    utils::loadCss();

?>

    
</head>

<body>
<?php
 if (($modo=="edit") or ($modo=="new")) {
        if ($_REQUEST["dati_chiusura"]){
            $file_config="$tabpath/chiusura";
            $intestazione='Dati di chiusura del procedimento - Fine lavori agibilità';            
        }
        else if ($_REQUEST["dati_chiusura_pa"]){
            $file_config="$tabpath/chiusura_pa";
            $intestazione='Dati di chiusura del procedimento amministrativo - Avvenuta Verifica Atti';            
        }
        else{
            if ($_SESSION["USER_ID"]==52) {
                $file_config="$tabpath/avvio_procedimento_admin";
            }
            else  {
                $file_config="$tabpath/avvio_procedimento";
            }
            $intestazione='Avvio del procedimento e comunicazione responsabile';
        }

	$tabella=new Tabella_v($file_config,$modo);					
	unset($_SESSION["ADD_NEW"]);	
	include "./inc/inc.page_header.php";
?>
	

		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM id="form-avvioproc" name="avvioproc" method="post" action="praticaweb.php">
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
				<H2 class="blueBanner"><?=$intestazione?></H2>
                                
				<?php
					if (file_exists(LOCAL_INCLUDE."pe.avvioproc.edit.before.php")){
						$html="";
						include_once LOCAL_INCLUDE."pe.avvioproc.edit.before.php";
						print $html;
					}
				if(isset($Errors) && $Errors){
					$tabella->set_errors($Errors);
					$tabella->set_dati($_POST);
				}
				elseif ($modo=="edit"){	
					$tabella->set_dati("pratica=$idpratica");
				}
                $tabella->edita();
                ?>
			</td>
		  </tr>

		</TABLE>
<input name="active_form" type="hidden" value="pe.avvioproc.php">				
<input name="oldtipo" type="hidden" value="<?=$tabella->get_campo("tipo")?>">

<input name="mode" type="hidden" id="mode" value="<?=$modo?>">
</FORM>
<div id="result" style="width:800px;height:600px;display:none;"></div>
<div id="waiting"></div>
<?php
//include "./inc/inc.window.php"; // contiene la gesione della finestra popup
}else{
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
                $tabella->set_titolo("Dati della pratica","modifica");
                $nrec=$tabella->set_dati("pratica=$idpratica");
                $tabella->elenco();
                $tabella->close_db();
                if (file_exists(TAB."$tabpath/chiusura_pa.tab")){
                    $tabella=new tabella_v("$tabpath/chiusura_pa","view");
                    $tabella->set_titolo("Dati di chiusura della procedimento amministrativo - Avvenuta Verifica Atti","modifica",Array("dati_chiusura_pa"=>1));
                    $nrec=$tabella->set_dati("pratica=$idpratica");
                    $tabella->elenco();
                }
                if (file_exists(TAB."$tabpath/chiusura.tab")){
                    $tabella=new tabella_v("$tabpath/chiusura","view");
                    $tabella->set_titolo("Dati di chiusura della pratica - Fine lavori agibilità","modifica",Array("dati_chiusura"=>1));
                    $nrec=$tabella->set_dati("pratica=$idpratica");
                    $tabella->elenco();
                }
                ?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>
		</TABLE>
                <input name="mode" type="hidden" id="mode" value="<?=$modo?>">
<?php
}
?>
</body>
</html>
