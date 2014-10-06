<?php

require_once("login.php");
//print_r($_POST);
include "./lib/tabella_h.class.php";
$tabpath="stp";
$tipo=$_REQUEST["tipo"];
$mod=($tipo=='html')?('nuovo'):('');

$conn=utils::getDb();

if ($_POST["azione"]){ 
	$idrow=$_POST["idriga"];
	$sql="SELECT nome FROM stp.e_modelli WHERE id=?"; 
	$sth=$conn->prepare($sql);
        $sth->execute(Array($idrow));
	$nome=$sth->fetchAll(PDO::FETCH_COLUMN); 
	$file=MODELLI_DIR.$nome; 
	@unlink($file); 
	$sql="delete from stp.e_modelli where id=$idrow";
	$conn->exec($sql);
}

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
<script language="javascript">
<?php
    if ($tipo=="html"){?>
function link(id,pratica){
	window.location="stp.editor.php?id_modelli="+id;
}
<?php }else{?>
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
<?php }?>
</script>
</head>
<body  background="" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
<?php
$tabella_modelli=new Tabella_h("$tabpath/modelli_$tipo",'list');

$sql="select distinct opzione,form,stampa from stp.e_form order by stampa;";
$sth=$conn->prepare ($sql);
if(!$sth->execute()) print_debug($sql,NULL,"tabella");
$elenco_modelli = $sth->fetchAll(PDO::FETCH_ASSOC);
?>

<H2 class="blueBanner">Elenco dei modelli</H2>
<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
       <form method="post" name="modelli" action="">
	<input type="hidden" name="azione" id="azione" value="">
	<input type="hidden" name="idriga" id="idriga" value="0">
       </form>
<?php
        foreach ($elenco_modelli as $row){
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
				<?php
                                $tabella_modelli->get_titolo("stp.editor.php?tipo=modelli");
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
<?php	}// end for?>
		<tr>
			<td><input class="hexfield1" style="width:100px;margin-top:10px" type="button" value="chiudi" onClick="javascript:window.opener.focus();window.close();"></td>
		</tr>

</TABLE>
</body>
</html>
