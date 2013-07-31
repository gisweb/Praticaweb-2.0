<?

require_once("login.php");
//print_r($_POST);
include "./lib/tabella_h.class.php";
$tabpath="stp";
$tipo=$_REQUEST["tipo"];
$mod=($tipo=='html')?('nuovo'):('');

$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

if ($_POST["azione"]){ 
	$idrow=$_POST["idriga"];
	$sql="SELECT * FROM stp.e_modelli WHERE id=$idrow"; 
	$db->sql_query($sql);
	$nome=$db->sql_fetchfield("nome"); 
	$file=MODELLI_DIR.$nome; 
	@unlink($file); 
	$sql="delete from stp.e_modelli where id=$idrow";
	$db->sql_query($sql);
}

?>
<html>
<head>
<title>ELENCO MODELLI DI STAMPA</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<script language="javascript">
<?if ($tipo=="html"){?>
function link(id,pratica){
	window.location="stp.editor.php?id_modelli="+id;
}
<?}else{?>
function link(id,pratica){
	window.open("modelli/"+id);
}
function elimina(id){
	var agree=confirm("Sicuro di voler eliminare il modello selezionato?");
	if (agree){
		document.getElementById("azione").value="Elimina";
		document.getElementById("idriga").value=id;
		document.modelli.submit();
	}
}
<?}?>
</script>
</head>
<body  background="" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
<?
$tabella_modelli=new Tabella_h("$tabpath/modelli_$tipo",'list');
$db=$tabella_modelli->get_db();
$sql="select distinct opzione,form,stampa from stp.e_form order by stampa;";
$db->sql_query ($sql);
print_debug($sql,NULL,"tabella");
$elenco_modelli = $db->sql_fetchrowset();
?>

<H2 class="blueBanner">Elenco dei modelli</H2>
<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
       <form method="post" name="modelli" action="">
	<input type="hidden" name="azione" id="azione" value="">
	<input type="hidden" name="idriga" id="idriga" value="0">
       </form>
<?	foreach ($elenco_modelli as $row){
		$form=$row["form"];
		$desc=$row["opzione"];

		//Visualizzare solo quelli inerenti il form e opzioni 
		$num_modelli=$tabella_modelli->set_dati("form='$form' and nome ilike '%.$tipo%'");
		if ($tipo=='html'){
			$tabella_modelli->set_titolo($desc,"nuovo",array("form"=>$form));
			$upload_butt="";
		}
		else{
			$tabella_modelli->set_titolo($desc);
			$upload_butt="<table border=\"0\" width=\"90%\"><tr><td style=\"text-align:right\"><input  class=\"hexfield1\" style=\"width:130px;\" type=\"submit\" value=\"Carica Modello\" onclick=\"NewWindow('stp.carica_modello.php?form=$form','documento',600,350);\" ></td></tr></table>";
		}
		$tabella_modelli->set_tag($idpratica);
		
		?>
		  <tr> 
			<td> 
			<!--  intestazione-->
				<?$tabella_modelli->get_titolo("stp.editor.php?tipo=modelli");
					if ($num_modelli) 
						$tabella_modelli->elenco();
					else
						print ("<p><b>Nessun Modello per questo Form</b></p>");
					
					print $upload_butt;
				?>
			<!-- fine intestazione-->
			<br>
			</td>
		  </tr>
<?	}// end for?>
		<tr>
			<td><input class="hexfield1" style="width:100px;margin-top:10px" type="button" value="chiudi" onClick="javascript:window.opener.focus();window.close();"></td>
		</tr>

</TABLE>
</body>
</html>
