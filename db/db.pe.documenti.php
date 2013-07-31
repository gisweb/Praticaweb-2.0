<?
if (isset($_POST["azione"]) && (($_POST["azione"]=="Salva") || ($_POST["azione"]=="Elimina")) ){
	$azione=$_POST["azione"];
    $active_form="pe.documenti.php";
	include_once "./db/db.savedata.php";
	$modo=($_POST["azione"]=="Salva")?('view'):(($_POST["azione"]=="Elimina")?('all'):(''));
	if ($_POST["mode"]=='new') $id=$_SESSION['ADD_NEW'];
}
if (isset($_POST["azione"]) && (($_POST["azione"]=="Annulla")))
	$modo=($_POST["mode"]=='new')?('list'):('view');

?>
