<?php



function loadLibs(){
    $libs=Array("pratica.class.php","app.utils.class.php","utils.class.php","menu.class.php","mail.class.php");
    foreach($libs as $lib){
    
        if (file_exists(LOCAL_LIB.$lib)){
            require_once LOCAL_LIB.$lib;
        }
        elseif(file_exists(APPS_DIR."lib".DIRECTORY_SEPARATOR.$lib)) {
            require_once LIB.$lib;
        }
        else die("impossibile caricare la libreria $lib");
    }
};
error_reporting(E_ERROR);

if (!session_id()) session_start();
    
$_SESSION["USER_ID"]=1;    
define('DATA_DIR','/data/imperia/pe/');
define('APPS_DIR','/apps/praticaweb-2.1/');
define('PRINT_VERSION',1);


if (!file_exists(DATA_DIR.'config.php')) die("Nessun file di configurazione trovato!");
require_once DATA_DIR.'config.php';
loadLibs();

if (!defined('PRINT_VERSION')) define('PRINT_VERSION',2);
             
$sql = "SELECT DISTINCT pratica from stp.stampe where pratica not in (17944,17788,17764,17706)  order by 1 DESC OFFSET 320;";
$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database ".DB_NAME);
$db->sql_query($sql);

$res = $db->sql_fetchrowset();

//require_once LOCAL_LIB."dompdf/autoload.inc.php";
//use Dompdf\Dompdf;
//use Dompdf\Options;
require_once(APPS_DIR."plugins/mpdf/mpdf.php");
$tot = count($res);
$j=1;
foreach($res as $r){
    $pratica = $r["pratica"];
    $pr = new pratica($pratica);
    $pr->createStructure();
    $sql = "SELECT A.testohtml,A.file_pdf,C.script,C.definizione FROM stp.stampe A inner join stp.e_modelli B on(A.modello=B.id) inner join stp.css C on (C.id=B.css_id) where (B.form ilike 'pe.%' or B.form ilike 'oneri.%')  and coalesce(A.testohtml,'')<>'' and A.pratica=$pratica and not file_pdf ilike '%/%';";
    echo "Considero pratica $j di $tot\n";
    echo "$sql\n";
    $db->sql_query($sql);
    $docs = $db->sql_fetchrowset();
    for($i=0;$i<count($docs);$i++){
        $doc =$docs[$i];
        $definizione = $doc["definizione"];
        $script = $doc["script"];
        $testo = $doc["testohtml"];
        $html=<<<EOT
<html>
<head>
    <style>$definizione</style>
</head>
<body>
    $script
    $testo
</body>
</html>
EOT;
        
        $pdfFile = $pr->documenti.$doc["file_pdf"];
        @unlink($pdfFile);
        $mpdf=new mPDF();
        try{
            $mpdf->WriteHTML($html);
            $mpdf->Output($pdfFile);
            echo "\tCreo documento $pdfFile\n";

        }
        catch(Exception $e){
            echo "\nErrore nella conversione del documento $pdfFile\n";
        }
//	if (!fwrite($handle,$p)) echo "\tErrore nella generazione del documento $pdfFile\n"; 
          
//	fclose($handle);
    }
    $j++;
}
?>
