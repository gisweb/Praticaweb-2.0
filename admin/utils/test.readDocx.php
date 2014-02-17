<?php
require_once "../../login.php";
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
$ext=($_REQUEST["ext"])?($_REQUEST["ext"]):('docx');
$localDir=Array("praticaweb","modelli");
$directory = DATA_DIR.implode(DIRECTORY_SEPARATOR,$localDir).DIRECTORY_SEPARATOR;
echo "<p>Scanning Directory $directory for $ext extension</p>";
//get all text files with a .docx extension.
$files = glob($directory . "*.$ext");

$debug=1;
$i=0;
$tot=count($files);
echo "Found $tot files";
$result=Array();
$regexpOldField="|«([\w]+)»|U";
$regexpNewField="|\[([\w]+)\]|";
echo "<ol>";
$contentFile=($ext=='docx')?('word/document.xml'):('content.xml');
for($j=0;$j<$tot;$j++){
	$fileName=$files[$j];
    $i++;
    $zip = new ZipArchive;
    if ($zip->open($fileName) === TRUE) {
        $info=pathinfo($fileName);
        $fName=$info["basename"];
        echo "<li>Considering File $fName $i di $tot:<li>";
        $xmlString = $zip->getFromName($contentFile);
        preg_match_all($regexpNewField,$xmlString,$out, PREG_SET_ORDER);

		for($k=0;$k<count($out);$k++){
			$result[]=$out[$k][1];
			if ($debug==1){
				echo "<li>Found MergeField \"".$out[$k][1]."\" in file $fName</li>";
			}
		}
        

        $zip->close();
    } else {
        echo "<li>Failed Opening $directory$fName</li>";
    }
}
echo "</ol>";
$res = array_unique($result);
print "Array(<br>";
foreach($res as $r) print "\"$r\"=>\"\",<br>";
print ")";
?>
