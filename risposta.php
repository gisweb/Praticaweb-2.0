<?
//Parte da sostituire con include del Login
include "login.php";


if ($_GET["table_name"]){
	$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
	list($type,$table)=explode(".",$_GET['table_name']);
	echo "<ul>";
	if ($type=="FUNCTION") echo "<li><a href=\"javascript:insert_campo('$type','$table','')\">$table</a></li></ul>";
	elseif ($type=="VIEW"){
		$sql="SELECT colonna,alias_colonna,descrizione FROM stp.colonne WHERE nome='$table' and char_length(alias_colonna)>0;";
		$db->sql_query($sql);
		$colonne=$db->sql_fetchlist("colonna");
		$desc=$db->sql_fetchlist("descrizione");
		$alias=$db->sql_fetchlist("alias_colonna");
		if ($colonne) for($i=0;$i<count($colonne);$i++){
			echo "<li><div title='".$desc[$i]."'><a href=\"javascript:insert_campo('$type','$table','".$colonne[$i]."')\">".utf8_decode($alias[$i])."</a></div></li>";
		}
		echo "</ul>";
	}
}
elseif ($_GET["margin"]){		// INDENTAZIONE DI UNA PARTE DI DOCUMENTO
	echo "<table border=\"0\">
	<tr>
		<td><input type=\"text\" size=\"5\" MAXLENGTH=\"5\" id=\"margine_sx\"></td>
		<td class=\"stiletabella\">Margine Sinistro</td>
	</tr>
	<tr>
		<td><input type=\"text\" size=\"5\" MAXLENGTH=\"5\" id=\"margine_dx\"></td>
		<td class=\"stiletabella\">Margine Destro</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td colspan=\"2\"><input type=\"button\" class=\"hexfield\" value=\"Allinea\" onclick=\"javascript:indenta();\"></td>
	</tr>
</table>";
}
elseif ($_GET["termina"]){		// SELEZIONE DEL TERMINATORE DEL CICLO RIPETUTO
	echo "<table border=\"0\">
	<tr>
		<td><input type=\"radio\" name=\"radio\" value=\"PV\"></td>
		<td class=\"stiletabella\">Punto e Virgola</td>
	</tr>
	<tr>
		<td><input type=\"radio\" name=\"radio\" value=\"V\"></td>
		<td class=\"stiletabella\">Virgola</td>
	</tr>
	<tr>
		<td><input type=\"radio\" name=\"radio\" value=\"C\"></td>
		<td class=\"stiletabella\">A Capo</td>
	</tr>
	<tr>
		<td><input type=\"button\" class=\"hexfield\" value=\"Ripeti\" onclick=\"javascript:insert_ciclo();\"></td>
		<td><input type=\"button\" class=\"hexfield\" value=\"Annulla\" onclick=\"javascript:chiudi();\"></td>
	</tr>
</table>";
}elseif($_GET["stampa"]){// STAMPE IN HTML
	include "lib/stampe.class.php";
	$idpratica="7829";	
	$n=$_GET["file"];	
	$m=new stampe($idpratica,$n,$n,"stp",1);
	$m->sostituisci_valori();
	$m->crea_documento();
	if ($m->errors) $m->stampa_tag("errori");
	else
		echo "Documento creato nella cartella documenti";

}
elseif ($_POST){		// SALVATAGGIO DEL FILE
	$file=$_POST["file"];
	$dir="modelli";
	if (file_exists($dir."/".$file) and !$_POST["sovrascrivi"]){ 
		
		echo "<table border=\"0\">
	<tr>
		<td class=\"stiletabella\" colspan=\"2\">Il file $file Ãš giÃ  presente.Vuoi sovrascriverlo?</td>
	</tr>
	<tr>
		<td><input type=\"button\" class=\"hexfield\" value=\"Si\" onclick=\"sovrascrivi('$file');\"></td>
		<td><input type=\"button\" class=\"hexfield\" value=\"NO\" onclick=\"chiudi();\"></td>
	</tr>
</table>";
		
	}
	else{
		$handle =fopen($dir."/".$file,"w+");
		preg_match_all("|<tbody[^>]*>(.*?)</tbody>|si",$_POST["testo"],$out,PREG_SET_ORDER);
		$testo=$out[0][1];
		$testo="<html>
<head>
	<LINK media=\"screen\" href=\"./src/modelli.css\" type=\"text/css\" rel=\"stylesheet\"><LINK media=\"print\" href=\"src/styles_print.css\" type=\"text/css\" rel=\"stylesheet\">
</head>
<body>
	<table style=\"width:19cm;\">\n\t\t".stripslashes($testo)."\n\t</table>\n</body>";
		str_replace(chr(10),"",$testo);
		str_replace(chr(13),"",$testo);
		if (fwrite($handle,str_replace("</LABEL>","</LABEL>\r\n",$testo)))	{
			fclose($handle);
			echo "Salvataggio riuscito";
		}
		else
			echo "Salvataggio NON riuscito";
	}
}
?>