<?php
require_once "../../login.php";


$tabpath="storage";
$idpratica=$_REQUEST["pratica"];
$user=$_SESSION["USER_ID"];
$data=date("d/m/Y");
$filetab="$tabpath/documentazione_inviata";

?>
<html>
<head>
<title>Documentazione Inviata</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS();
	utils::loadCss();
?>

</head>
<body  background="">
<?php

$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$form="documentazione_inviata";
$titolo = "Documento Inviato";
if (($modo=="edit") or ($modo=="new")){
    include "./inc/inc.page_header.php";
    unset($_SESSION["ADD_NEW"]);
    $tabella=new tabella_v($filetab,$modo);
    if($modo=="edit"){
        $id=$_POST["id"];
        $filtro="id=$id";
        $tabella->set_dati($filtro);
    }
    else{

        $tabella->set_dati($_REQUEST);
    }

    //aggiungendo un nuovo parere uso pareri_edit che contiene anche l'elenco degli ENTI
    
                ?>	
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<FORM height=0 method="post" action="/praticaweb.php" enctype="multipart/form-data">
				<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
						<TR> <!-- intestazione-->
								<TD><H2 class="blueBanner"><?=$titolo?></H2></TD>
						</TR> 
						<TR>
								<td>
						<!-- contenuto-->
		<?php
		
		$tabella->edita();
		?>
		<!-- fine contenuto-->
								</TD>
						</TR>

				</TABLE>
			<input name="active_form" type="hidden" value="storage.documentazione_inviata.php">
            <input name="storage" type="hidden" value="1">
			
			<input name="mode" type="hidden" value="<?=$modo?>">	
		</FORM>
		
	<?php
        include "./inc/inc.window.php";
		
	}else{
		$tabella=new tabella_h($filetab,"view");
        $numrec=$tabella->set_dati("pratica=$idpratica");
		$conn= utils::getDB();
		$sql= "SELECT A.id, format('%s - %s',B.nome,A.nome) as testo FROM pe.e_documenti A INNER JOIN pe.e_iter B ON(A.iter=B.id) ORDER BY B.ordine,A.nome";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$res = $stmt->fetchAll();
		$options=<<<EOT
		<option value="">Seleziona una tipologia di documento</option>	
EOT;
		for($i=0;$i<count($res);$i++){
			list($id,$nome)=$res[$i];
			$options.=<<<EOT
		<option value="$id">$nome</option>		
EOT;
		}
		?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
		<H2 class="blueBanner">Documenti Inviati</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
                $tabella->set_titolo($titolo,"nuovo",array("id"=>""));
                $tabella->get_titolo();
                $tabella->elenco();

				?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>  
		</TABLE>
		<div id = "associa-pratica" style="display:none; width: 600px;height: 400px;">
			<table class="stiletabella" width="100%" cellpadding="2" border="0">
				<tr>
					<td class="label" width="200">Numero Pratica</td>
					<td valign="middle"><input type="text" value="" id="numero" name="numero" size="20" maxlength="15" class="textbox">
					</td>
				</tr>
				<tr>
					<td class="label" width="200">Tipo di Documento</td>
					<td valign="middle">
						<select id="documento" class="textbox">
							<?php
								print $options;
							?>
						</select>
					</td>
				</tr>
			</table>
			<input type="hidden" name="storage-documento" id="storage-documento" value=""/>
			<div id="message-dialog"></div>
			<div class="button_line"></div>
	
			<div>	
				<span id="associa"> </span>
				<span id="close"> </span>
			</div>
		</div>
<?php
}
?>

</body>
</html>
