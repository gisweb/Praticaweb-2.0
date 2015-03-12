<?php
// Require the class

//require_once dirname(__FILE__) . '/../HTML_ToPDF.php';
require_once "lib/HTML_ToPDF.php";
// Full path to the file to be converted
$htmlFile = STAMPE_DIR."$nome.html";
// The default domain for images that use a relative path
// (you'll need to change the paths in the test.html page 
// to an image on your server)
$defaultDomain = '/praticaweb_demo';
// Full path to the PDF we are creating
$pdfFile = STAMPE_DIR."$nome.pdf";
// Remove old one, just to make sure we are making it afresh
@unlink($pdfFile);

// Instnatiate the class with our variables
$pdf =& new HTML_ToPDF($htmlFile, $defaultDomain, $pdfFile);
//$pdf->debug=1;
$result = $pdf->convert();
// Check if the result was an error
if (PEAR::isError($result)) {
	echo "<p>ERRORE nella creazione del file $pdfFile</p>";
   $err=1;
}
?>
