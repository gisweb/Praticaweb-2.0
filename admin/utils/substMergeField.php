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

$localDir=Array("praticaweb","modelli");
$directory = DATA_DIR.implode(DIRECTORY_SEPARATOR,$localDir).DIRECTORY_SEPARATOR;
echo "<p>Scanning Directory $directory</p>";
//get all text files with a .txt extension.
$files = glob($directory . "*.docx");

$substitutions=Array(
"«NUMERO_PRATICA»"=>"[numero]",
"«RICHIEDENTI»"=>"[elenco_richiedenti]",
"«OGGETTO»"=>"[oggetto]",
"«UBICAZIONE»"=>"[ubicazione]",
"«DATA_PROT»"=>"[data_protocollo]",
"«NUMERO_PROT»"=>"[protocollo]",
"«PROGETTISTI»"=>"[elenco_progettisti]",
"«RIF_CATASTO»"=>"[elenco_ct]",
"«RIF_CATASTO_URB»"=>"[elenco_cu]",
"«DATA_CIE»"=>"[data_ce]",
//"«PARERI»"=>"",
"«ELENCO_DOCUMENTI»"=>"[documenti_mancanti.documento;block=tbs:listitem]",
//"«ONERI_DETTAGLIO»"=>"",
"«ONERI_CC»"=>"[oneri_cc]",
"«ONERI_B1_90P»"=>"[oneri_b1_90p]",
"«ONERI_B2_93P»"=>"[oneri_b2_90p]",
"«ONERI_B2_7P»"=>"[oneri_b2_7p]",
"«ONERI_B1B2_10P»"=>"[oneri_b1b2_10p]",
"«ONERI_TOTALE»"=>"[oneri_totale]",
"«RICHIED1_NOME»"=>"[elenco_richiedenti]",
"«INTERVENTO»"=>"[intervento]",
"AB_AG_RICPROT"=>"[abitabilita_protocollo_richiesta]",
"AB_AG_RICDATAPROT"=>"[abitabilita_data_richiesta]",
"INIZIO_LAVORI"=>"[lavori_inizio]",
"FINE_LAVORI"=>"[lavori_fine]",
"NOMI_PROGETT"=>"[elenco_progettisti]",
"RICHIEDENTI_DATI"=>"[richiedenti.nominativo;block=tbs:listitem]",
"PROGETTISTI_DATI"=>"[progettisti.nominativo;block=tbs:listitem]",
"DIRETTORI_DATI"=>"[direttori.nominativo;block=tbs:listitem]",
"ESECUTORI_DATI"=>"[esecutori.nominativo;block=tbs:listitem]",
"NOMI_RICHIED"=>"[elenco_richiedenti]",
"DATARIL_CONC"=>"[data_rilascio_titolo]"
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
