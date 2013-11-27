<?php
require_once "../login.php";
function odt2text($filename) {
    return readZippedXML($filename, "content.xml");
}

function docx2text($filename) {
    return readZippedXML($filename, "word/document.xml");
}

function readZippedXML($archiveFile, $dataFile) {
    // Create new ZIP archive
    $zip = new ZipArchive;

    // Open received archive file
    if (true === $zip->open($archiveFile)) {
        // If done, search for the data file in the archive
        if (($index = $zip->locateName($dataFile)) !== false) {
            // If found, read it to the string
            $data = $zip->getFromIndex($index);
            // Close archive file
            $zip->close();
            // Load XML from a string
            // Skip errors and warnings
            $xml = DOMDocument::loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            // Return data without XML formatting tags
            return $xml->saveXML();
        }
        $zip->close();
    }

    // In case of failure return empty string
    return "";
}

$directory = DATA_DIR."..\\modelli\\";
//get all text files with a .txt extension.
$files = glob($directory . "*.docx");

$substitutions=Array(
    "pratica.prot"=>"protocollo",
    "pratica.d_prot"=>"data_protocollo",
    "pratica.numero"=>"numero",
    "pratica.d_ce"=>"data_rilascio_ce",
    "pratica.oggetto"=>"oggetto",
    "pratica.ubicazione"=>"ubicazione",
    "pratica.el_rich"=>"elenco_richiedenti",
    "pratica.el_prog"=>"elenco_progettisti",
    "pratica.dirigente"=>"dirigente",
    "pratica.el_urbano"=>"elenco_cu",
    "pratica.el_terreni"=>"elenco_ct",
	"oneri.cc"=>"oneri_cc",
	"oneri.totale"=>"oneri_totale",
	"oneri.ou_pr90p"=>"",
	"oneri.ou_sec93p"=>"",
	"oneri.ou_sec7p"=>""
	"oneri.ou_pr10p"=>"oneri_urb_a15_lr15_1989"
);
$i=0;
$tot=count($files);
echo "<ol>";
foreach($files as $fileName){
    $i++;
    $zip = new ZipArchive;
    if ($zip->open($fileName) === TRUE) {
        $info=pathinfo($fileName);
        $fName=$info["basename"];
        echo "<li>Considering File $fName $i di $tot:<ul>";
        $xmlString = $zip->getFromName('word/document.xml');
        foreach($substitutions as $from=>$to){
            $occurrencies=substr_count($xmlString,$from);
            $message=($occurrencies)?("<li>Found $occurrencies occurrencies of $from</li>"):("<li>No match found for $from</li>");
            echo $message;
            $xmlString = str_replace($from, $to, $xmlString);
        }
        sleep(1);
        $zip->addFromString('word/document.xml', $xmlString);
        echo "</ul></li>";

        $zip->close();
    } else {
        echo "<li>Failed Opening $fName</li>";
    }
}
echo "</ol>";
?>
