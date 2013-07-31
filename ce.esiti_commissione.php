<?
include_once("login.php");
include "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";

$active_form="ce.esiti_commissione.php";
$tabpath="ce";
$file_config="$tabpath/esiti_commissione";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('list');
$idcomm=$_REQUEST["pratica"];


$tornaacasa="
	<script language=javascript>
		parent.location='index.php';
	</script>";

	if ($modo=="edit" || $modo=="view"){
		$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
		if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
		
		$sql="SELECT numero FROM pe.pareri left join pe.avvioproc on pareri.pratica=avvioproc.pratica WHERE pareri.id=".$_REQUEST["id"];
		$db->sql_query($sql);
		print_debug($sql);
		$num=$db->sql_fetchfield("numero");
		
	}
	
	
	?>


<html>
<head>
<title>Pareri della commissione - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
<SCRIPT>
	function link(id,prat){
		loc="ce.esiti_commissione.php?mode=view&pratica=<?=$idcomm?>&id_pratica="+prat+"&id_parere="+id;
		//alert(loc);
		window.location=loc;
	}
</SCRIPT>
</head>
<body>
<?

	if ($modo=="edit" or $modo=="new"){
		
		include "./inc/inc.page_header.php";
		print("<H2 class=\"blueBanner\">Parere</H2>");
		$idpratica=$_REQUEST["id_pratica"];
		$id_parere=$_REQUEST["id"];
		$tabella=new Tabella_v($file_config,$modo);
		$tabella->set_errors($Errors);
		if ($modo=="edit") {
			$tabella->set_dati("id=$id_parere");
			$tabella->set_titolo("Modifica parere della pratica n° $num");
		}
		else{
			$_SESSION["ADD_NEW"]=0;
			$tabella->set_titolo("Nuovo parere della pratica n° ".$_REQUEST["numero"]);
		}
		$tabella->get_titolo();?>
	<form name="pareri" method="post" action="praticaweb.php">
		<?$tabella->edita();?>
		<input name="active_form" type="hidden" value="ce.esiti_commissione.php">
		<input name="mode" type="hidden" value="<?=$modo?>">
		<input name="id_pratica" type="hidden" value="<?=$idpratica?>">
		<input name="comm" type="hidden" value=1>
		<input name="id_parere" type="hidden" value="<?=$id_parere?>">
		<input name="pratica" type="hidden" value="<?=$idcomm?>">
              <input name="tipo" type="hidden" value="<?=$tipo?>">
		<input name="id" type="hidden" value="<?=$id_parere?>">
		<input name="numero" type="hidden" value=<?=$_POST["numero"]?>>
		
	</form>
		
		<?php
		include "./inc/inc.window.php";
	}
	elseif($modo=="view"){
		$idpratica=$_REQUEST["id_pratica"];
		$id_parere=$_REQUEST["id"];
		$tabella=new Tabella_v($file_config,$modo);
		$tabella->set_dati("id=$id_parere");
		$tabella->set_titolo("Parere della pratica n° $num",'Modifica',Array("id_pratica"=>$idpratica,"id"=>$id_parere));
		$tabella->get_titolo();
		$tabella->tabella();
	}
	else {
		print("<H2 class=\"blueBanner\">Elenco Pareri Espressi</H2>");
			$tabella=new Tabella_h($file_config,'list');
			//$tabella->set_tag($idpr);
			$tabella->set_dati("ente =(SELECT tipo_comm FROM ce.commissione WHERE id=$idcomm) AND data_rich = (SELECT data_convocazione FROM ce.commissione WHERE id=$idcomm);");
			
			$mod=(($parere==0)?("new"):("edit"));
			//$butt_mod=(($parere==0)?("nuovo"):("modifica"));
			//$tabella->set_titolo($titolo,"",array("id_pratica"=>$idpr,"numero"=>$num,"mode"=>"$mod","id_parere"=>$parere));
			//$tabella->get_titolo();
			$tabella->elenco();
		//}
	}
?>


</body>
</html>
