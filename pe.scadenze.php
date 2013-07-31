<?
include_once("login.php");
include "./lib/tabella_h.class.php";
$tabpath="pe";

$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$titolo=$_SESSION["TITOLO_$idpratica"];
$today=date('j-m-y'); 
?>

<html>
<head>
<title>Scadenze - <?=$titolo?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>
</head>
<body>
<?php
//-<<<<<<<<<<<<<<<<<<<<<< VISUALIZZA ITER >>>>>>>>>>>>>>>>>>>>>>>>>>>----------------------->	
	$tabella=new tabella_h("$tabpath/scadenze");?>
	<H2 class="blueBanner">Scadenze della pratica</H2>
	<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
	  <TR> 
		<TD> 
		<!-- contenuto-->
<?
	$sql="SELECT id,nome from pe.e_iter order by ordine";	
	$tabella->connettidb();
	$tabella->db->sql_query($sql);	
	$iter=$tabella->db->sql_fetchrowset();
	for($i=0;$i<count($iter);$i++){
		$tabella->set_titolo($iter[$i]["nome"]);
		$tabella->get_titolo();
		$table="<table class=\"stiletabella\" width=\"90%\"><tr><td><img src=\"get_scadenza.php?pratica=$idpratica&iter=".$iter[$i]["id"]."\" border=\"0\"></td></tr></table>"; 
		echo $table;
	}
				?>
		<!-- fine contenuto-->
		 </TD>
	     </TR>
	</TABLE>	
</body>
</html>
