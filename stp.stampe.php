<?php
//Nota conservo il tipo per poter verificere se Ãš cambiato
include_once("login.php");
include_once "./lib/tabella_h.class.php";
$tabpath="stp";
$usr=$_SESSION['USER_NAME'];
$idpratica=$_REQUEST["pratica"];
$tipopratica=$_POST["tipo_pratica"];
$form=$_POST["form"];
$tipo=$_POST["tipo"];
$modello=$_POST["modello"];
$file=$_POST["file"];
$azione=$_POST["azione"];
$procedimento=$_POST["procedimento"];
list($tipo,$pag)=explode(".",$form);
//print_r($_POST);
$condono=($tipo=="cn")?(1):(0);
$ce=($tipo=="ce")?(1):(0);
$cdu=($tipo=="cdu")?(1):(0);
$vigi=($tipo=="vigi")?(1):(0);
$active_form=$form.".php";
$tab_err=array();
$hidden="hidden";
if($_POST["azione"])
	include("./db/db.stp.stampe.php");
?>
<html>
<head>
<title>Gestione modelli e stampe - <?=$_SESSION["TITOLO_$idpratica"]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS(Array('jquery.easyui.min','locale/easyui-lang-it','datagrid-detailview'));
	utils::loadCss(Array('default/easyui','icon'));
?>
<SCRIPT language="javascript">
    $(document).ready(function(){
        $('#result-table-modelli').tree({
                title:'Elenco dei modelli di stampa',
                data:modelli,
                formatter:function(node){
                    if (node.children)
                        return sprintf('<b>%(text)s</b>',node);
                        
                    else
                        return sprintf('<input type="radio" value="%(id)s" name="id" class="stiletabella">%(text)s</input>',node);
                }
                
            });
        });
function home(){
	document.main.azione.value="Annulla";
	document.main.submit();
	
}
function check_radio(nome,act){
	var id=$("input[name='id']:checked").val();
	id=(!id>0)?(-1):(id);
	if(id==-1){
		alert("Seleziona un elemento.");
		return 0;
	}
	else{
		$("#id_mod").val(id);
		if (act=="elimina") return confirm('Sei sicuro di voler eliminare il modello ?');
		else
			return 1;
	}
}
function submit_form(nome,act){
	if (check_radio('id',act)==1) {
		//document.getElementById('azione').value=act;
		return true;
	}
	else {
		return false;
	}
}
function formatLink(value,rowData,rowIndex){
        var text=value;
        if(rowData['id']){
            text='<input type="radio" name="id" class="stiletabella">' + text + '</input>';
        }
        
        return text;
    }
</SCRIPT>
</head>	

<body>

<?php
	include "./inc/inc.page_header.php";
    $pr=new pratica($idpratica);
    $arrFiltri=Array();
    if ($cdu) $arrFiltri["form"]="form='$form'";
    $arrFiltri["utente"]="(coalesce(proprietario,'pubblico')='pubblico' or proprietario='$usr')";
    //$arrFiltri["tipopratica"]="(coalesce(tipo_pratica,'0')='0' or '".floor((double)$pr->info['tipo']/100)."'=ANY(string_to_array(coalesce(tipo_pratica,''),',')) or '".$pr->info['tipo']."'=ANY(string_to_array(coalesce(tipo_pratica,''),',')))";
    $arrFiltri["disponibili"]="NOT A.id IN (SELECT DISTINCT modello FROM stp.stampe A INNER JOIN stp.e_modelli B ON (B.id=A.modello) WHERE A.pratica=$idpratica and multiple=0) AND form ILIKE '$tipo%'";
    /*if ($_SESSION["PERMESSI"]<=3) $array_file_tab=(!$condono)?(array("$tabpath/stampe_docx","$tabpath/stampe_rtf","$tabpath/stampe_pdf")):(array("$tabpath/modelli_condono","$tabpath/stampehtml_condono","$tabpath/stampepdf_condono"));
	else
		$array_file_tab=(!$condono)?(array("$tabpath/modelli_usr","$tabpath/stampe_rtf","$tabpath/stampe_pdf")):(array("$tabpath/modelli_condono","$tabpath/stampehtml_condono","$tabpath/stampepdf_condono"));
	*/
    $file_tab="$tabpath/stampe";
    $titolo="Elenco Modelli";
    $filtro=implode(" AND ",$arrFiltri);
    $sql="select coalesce(B.id::varchar,'')||'#'||coalesce(A.id::varchar,'') as codice,A.id,coalesce(B.id::varchar,'tutti') as idtipo,A.nome as modello,coalesce(B.nome,'Tutti i tipi di pratica') as tipo_pratica,form from stp.e_modelli A left join pe.e_tipopratica B on (B.id::varchar =ANY(string_to_array(tipo_pratica,','))) WHERE $filtro order by tipo_pratica,modello;";
    $db=appUtils::getDb();
    $res=$db->fetchAll($sql);
    //print_array($res);
    $modelli=  json_encode(appUtils::groupData("modelli",$res));
   // print_array($modelli);
    echo <<<EOT
    <script>
        var modelli=$modelli;
    </script>
EOT;
?>	
<H2 class="blueBanner">Gestione Stampe e Modelli</H2>

<!------------------------------------------------------------------------   MODELLI   ------------------------------------------------------------------------------------------------------------------------------------->


            <ul id="result-table-modelli">

           </ul>
              
        </div> 
<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="800">
	<TR>
		<TD colspan="4">
		
		</TD>
	</TR>
	<TR>
		<TD><hr>
			
		</TD>
	</TR>
</table>
<form method="POST" action="praticaweb.php" name="modelli" id="praticaFrm">
	<input type="hidden" name="pratica" value="<?php echo $idpratica;?>">
	<input type="hidden" name="form" value="<?php echo $form;?>">
	<input type="hidden" name="condono" value="<?=$condono?>">
	<input type="hidden" name="cdu" value="<?=$cdu?>">
	<input type="hidden" name="comm" value="<?=$ce?>">
	<input type="hidden" name="vigi" value="<?=$vigi?>">
	<input type="hidden" name="active_form" value="<?php echo $active_form?>">
	<input type="hidden" name="stampe" value="1">
	<input type="hidden" name="id" id="id_mod" value="">
	<input type="hidden" name="comm_paesaggio" value="<?=$comm_paesaggio?>">
	<input type="hidden" name="azione" id="azione" value="">
	<span id="back_btn"></span>
	<span id="print_btn"></span>
	<script>
		$('#back_btn').button({
			'label':'Indietro',
			'icons':{
				'primary':'ui-icon-circle-triangle-w'
			}
		}).click(function(){
			$("#azione").val('Annulla');
			$('#praticaFrm').submit();
		});
		$('#print_btn').button({
			'label':'Crea Documento',
			'icons':{
				'primary':'ui-icon-disk'
			}
		}).click(function(){
			$("#azione").val('Crea Documento');
			if (submit_form('id','crea')){
                            $('#print_btn').unbind('click');
                            $('#praticaFrm').submit();
                        }
			else
                            return false;
		});
	</script>
    </form>



</body>
</html>
