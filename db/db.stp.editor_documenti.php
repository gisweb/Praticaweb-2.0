<?include "login.php";

if(preg_match_all("|<body>(.+)</body>|Umi",$testo,$ris,PREG_SET_ORDER)) $testo=$out[0][1];
$id=$_POST["id"];
$id_doc=$_POST["id_doc"];

if ($_POST["azione"]=="Salva"){ 
        $db->sql_query("SELECT pratica from stp.stampe where id=$id_doc");
        $pratica=$db->sql_fetchfield('pratica');
        $pr=new pratica($pratica);
		$testo=html_entity_decode($testo);
	    $infoFile=pathinfo($file);
	    $nome=$infoFile["filename"];
       	$ext=$infoFile["extension"];
		//list($nome,$ext)=explode(".",$file);
		$nome_pdf=$nome.".pdf";
		$sql="UPDATE stp.stampe SET testohtml='".addslashes($testo)."' WHERE id=$id_doc";
		if(!$db->sql_query($sql))
			echo "<p style=\"color:red;\">Errore nel Salvataggio del File</p>";
        
        
        system("rm $pr->documenti$nome.doc");
		$html="<html><head><style>$style</style></head><body>$testo</body></html>";
		$handle=fopen("$pr->documenti$nome.doc",'w');
		fwrite($handle,$html);
		fclose($handle);
		/*CREAZIONE DEL PDF*/
		if(!$modal){
			require_once LIB."HTML_ToPDF.php";
			//$htmlFile = STAMPE."/$file";
			$handle=fopen(LIB."HTML_ToPDF.conf","r");
			$style= fread($handle, filesize(LIB."HTML_ToPDF.conf"));
			fclose($handle);
			$html="<html><head><style>$style</style></head><body>$testo</body></html>";
			$defaultDomain = '';
			$pdfFile = STAMPE."/$nome_pdf";
			@unlink($pdfFile);
			$pdf =& new HTML_ToPDF($html, $defaultDomain, $pdfFile);
			$pdf->debug=false;
			$result = $pdf->convert();
		/*FINE PDF*/
		}
		else{ 
			require_once LIB."dompdf_config.inc.php";
			$sql="SELECT script,definizione,dimensione,orientamento FROM stp.stampe inner join stp.e_modelli on (stampe.modello=e_modelli.id) inner join stp.css on(css_id=css.id) WHERE stampe.id=$id_doc";
			$db->sql_query($sql);
			$definizione=$db->sql_fetchfield('definizione');
			$script=$db->sql_fetchfield('script');
			$size=$db->sql_fetchfield('dimensione');
			$orient=$db->sql_fetchfield('orientamento');
			$pdfFile = STAMPE."/$nome_pdf";
			//if(preg_match('|^<p>(.+)</p>$|Umi',$testo,$out)) $testo=$out[1]; 
			@unlink($pdfFile);
			$html="<html>
		<head>
			<style>$definizione</style>
		</head>
		<body>
			$script
			$testo
		</body>
</html>";

			/*MODIFICHE */
			$dompdf = new DOMPDF();
			$dompdf->set_paper($size,$orient);
			$dompdf->load_html($html);
			$dompdf->render();
			$p=$dompdf->output();
			$handle=fopen($pdfFile,'w+');
			fwrite($handle,$p);
			fclose($handle);
		}
		//$testo=$html;
		$body_onload="window.opener.location.reload();window.focus();";
		
	}
elseif($_POST["azione"]="Elimina"){
	$sql="DELETE FROM stp.stampe WHERE id=$id_doc;";
	//echo "<p>$sql</p>";
	$db->sql_query($sql);
	$body_onload="window.opener.location.reload();window.close();";
}
?>