<?php
require(APPS_DIR.'plugins/fpdf17/fpdf.php');

class PDF extends FPDF{
		// Load data
    function LoadData($file){
        // Read file lines
        $lines = file($file);
        $data = array();
        foreach($lines as $line)
            $data[] = explode(';',trim($line));
        return $data;
    }

    // Simple table
    function BasicTable($header, $data){
        // Header
        foreach($header as $col)
            $this->Cell(40,7,$col,1);
        $this->Ln();
        // Data
        /*foreach($data as $row)
        //{
            foreach($row as $col)
                $this->Cell(40,6,$col,1);
            $this->Ln();
        }*/
    }
}

function correggi($stringa){
    $correzioni = array("À" => "A", "�?" => "A", "Â" => "A", "Ä" => "A", "Å" => "A", "Æ" => "AE", "Ç" => "C", "È" => "E", "É" => "E", "Ê" => "E", "Ë" => "E", "Ì" => "I", "�?" => "I", "Î" => "I", "�?" => "I", "Ñ" => "N", "Ò" => "O", "Ó" => "O", "Ô" => "O", "Ö" => "O", "Ù" => "U", "Ú" => "U", "Û" => "U", "Ü" => "U", "ß" => "ss", "à" => "a", "á" => "a", "â" => "a", "ä" => "a", "æ" => "ae", "ç" => "c", "è" => "e", "é" => "e", "ê" => "e", "ë" => "e", "ì" => "i", "í" => "i", "î" => "i", "ï" => "i", "ñ" => "n", "ò" => "o", "ó" => "o", "ô" => "o", "ö" => "o", "ù" => "u", "ú" => "u", "û" => "u", "ü" => "u", "O" => "O", "o" => "o", "Œ" => "OE", "œ" => "oe");
    foreach($correzioni as $chiave => $valore){
        $stringa = str_replace($chiave, $valore, $stringa);
    }
    $stringa=str_replace("?","_",$stringa);
    $stringa=str_replace("/","_",$stringa);

    return $stringa;
}  

function zeri ($stringa){
    $find=".";
    $pos=strpos($stringa,$find);
    if ($stringa=="")	{
        $stringa=$stringa."0";
    }  
    if ($pos===false) {
        if ($stringa=="0") {$stringa="0.00";	}
        else {
            $stringa=$stringa.".00";			}  
        } 

    else {
        $p=substr($stringa,$pos);
        if (strlen($p)==2) {$stringa=$stringa."0";	}  
    }  
    return $stringa;
}
function check_value($k_k)
{
    if ($k_k=="") {$k_k="0.00";}  
    return $k_k;

}  
?>
