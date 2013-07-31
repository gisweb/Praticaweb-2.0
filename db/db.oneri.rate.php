<?
$azione=$_POST["azione"];
if (($azione=="Salva") || ($azione=="Elimina") ){
	include_once "./db/db.savedata.php";
	if ($azione=='Salva'){
		$pr=new pratica($idpratica);
		$pr->setFidiCM();
		$pr->setFidiOC();
	}
}
elseif ($azione=="Calcola") 
	include"./db/db.oneri.calcolarate.php";

$active_form=$_POST["active_form"]."?pratica=$idpratica";

?>



