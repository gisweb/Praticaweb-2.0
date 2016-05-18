<?php
require_once "../../login.php";

$Errors=null;


$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$id=(isset($_REQUEST["id"]))?($_REQUEST["id"]):('');
$tabpath="pe";
$formaction="pe.e_tipopratica_scadenze.php";
$codice=$_REQUEST["codice"];
$JOIN = ($modo=='list')?('INNER'):('LEFT');
include "db/db.pe.e_tipopratica_scadenze.php";
$sql="SELECT 
A.id as id_scadenza ,B.id ,B.nome,A.tabella,A.campo,coalesce(A.codice,'$codice') as codice,A.scadenza,C.nome as nome_scadenza,tipologia
FROM pe.e_tipopratica B LEFT JOIN 
(SELECT * FROM pe.e_tipopratica_scadenze WHERE codice='$codice') A ON(A.tipo_pratica=B.id) 
$JOIN JOIN pe.e_scadenze C USING(codice)    
WHERE B.enabled=1 ORDER BY tipologia,nome";
$dbconn->sql_query($sql);
$res=$dbconn->sql_fetchrowset();

$sql="SELECT nome FROM pe.e_scadenze WHERE codice='$codice'";
$dbconn->sql_query($sql);
$r=$dbconn->sql_fetchfield('nome');
$nomeScadenza=$r;
$file_config="e_tipopratica_scadenze.tab";
$tit="$nomeScadenza - Dati sui Tipi di Pratica ";

?>
<html>
<head>
    <title>Elenco Scadenze</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <?php
	utils::loadJS('form/e_tipopratica_scadenze');
	utils::loadCss();
?>
    <SCRIPT language="javascript" type="text/javascript">
        $(document).ready(function(){
            $('#azione-chiudi').bind('click',function(event){
               event.preventDefault();
               linkToList('<?php echo $formaction;?>',{'codice':'<?php echo $codice?>'});
            });
        });
    </SCRIPT>

    </head>
    <body>
<?php 
include "./inc/inc.page_header.php";
?>

<?php
	if (($modo=="edit") or ($modo=="new")){
		$tabella=new tabella_h("$tabpath/$file_config",$modo);
		unset($_SESSION["ADD_NEW"]);
		?>	
		<FORM id="stati" name="utenti" method="post" action="<?php echo $formaction; ?>">
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="75%">		
				  
		<tr> 
			<td> 
				<!-- contenuto-->
				<?php
                  
				  
                     //print_array($Errors);
                                if ($Errors)
                                       $tabella->set_errors($Errors);
                                if (!count($Errors)){
                                       $tabella->array_dati=$res;
                                       $tabella->num_record=count($res);
                                }
                                else
                                       $tabella->set_dati($_POST);
                                   
				$tabella->elenco();?>
				<!-- fine contenuto-->
			</td>
		  </tr> 
		</TABLE>
		<input name="active_form" type="hidden" value="pe.e_tipopratica_scadenze.php">
        <input name="mode" type="hidden" value="<?=$_POST["mode"]?>">
        <input name="codice" type="hidden" value="<?=$codice?>">
		</FORM>		

		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA   >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
<?php
}
else {
	$tabella=new Tabella_h("$tabpath/$file_config",'list');
	$tabella->set_titolo("Elenco delle Scadenze - $nomeScadenza","modifica",Array("codice"=>$codice));
	$tabella->array_dati=$res;
	$tabella->num_record=count($res);
        //print_array($tabella);
	?>
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		<TR> 
			<TD> 
				
			<?php
                                $tabella->get_titolo();
				if ($tabella->num_record==0){
                                    echo "<p><b>Nessuna Scadenza impostata</b></p>";
                                }
                                else
                                    $tabella->elenco();
                        ?>
			</TD>
		</TR>
	</TABLE>
   <button id="btn_close" />
   <script>
	  $('#btn_close').button({
		 icons:{
			primary:'ui-icon-circle-close '
		 },
		 label:'Chiudi'
	  }).click(function(){
		 window.location.href='/pe.e_scadenze?mode=list';
	  });
   </script>
	<?php
	}?>
	
</body>
</html>
