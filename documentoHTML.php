<?php

	include_once ("login.php");
	$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
	$documento=$_REQUEST["documento"];
	$tipo=$_REQUEST["tipo"];
	
	if($tipo=='doc'){
	header ("Content-Type: application/vnd.ms-word; Charset=UTF-8");
	header ("Content-Disposition: inline; filename=file.doc");
	}
	
	$sql="SELECT testohtml,file_doc,modello FROM stp.stampe WHERE id='$documento';";
	if($db->sql_query($sql)){
		$nome=$db->sql_fetchfield('file_doc');
		$testo=$db->sql_fetchfield('testohtml');
		$modello=$db->sql_fetchfield('modello');
	}
	else
		echo "$sql";
		
	$sql="SELECT definizione,script FROM stp.e_modelli,stp.css WHERE e_modelli.id='$modello' and e_modelli.css_id=css.id;";
	if($db->sql_query($sql)){
		$css_def=$db->sql_fetchfield('definizione');
		$css_script=$db->sql_fetchfield('script');
	}
	else
		echo "$sql";
		

?>

<html>
<head>
	<style type="text/css">	
			<?php
			echo"$css_def";
			?>	
		TABLE{
			page-break-inside: avoid;
		}
		P{
			page-break-inside: avoid;
		}
	</style>

</head>
<body class="cdu">


<?php 
echo"$css_script";
echo"$testo";
?>


</body>
</html>