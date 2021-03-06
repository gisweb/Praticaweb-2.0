<?php
require_once "../login.php";
$dbh = utils::getDB();
$pratica = $_REQUEST["pratica"];
$pr = new pratica($pratica);

$allegatiDir = $pr->allegati.$fName;
if ($_REQUEST["stato_allegato"] && $_REQUEST["stato_allegato"]!="all"){
    $sql = "SELECT id,nome_file,prot_allegato,data_prot_allegato FROM pe.file_allegati WHERE pratica = ? AND stato_allegato = ? and coalesce(nome_file,'')<>'';";
    $data = Array($pratica,$_REQUEST["stato_allegato"]);
}
else{
    $sql = "SELECT id,nome_file,prot_allegato,data_prot_allegato FROM pe.file_allegati WHERE pratica = ? and coalesce(nome_file,'')<>'';";
    $data = Array($pratica);
}
$stmt = $dbh->prepare($sql);

if($stmt->execute($data)){
    $res = $stmt->fetchAll();
    if (count($res)==0) die("<p>No data found!</p>");
    $zip = new ZipArchive;
    chdir(DATA_DIR."tmp");
    $archive_file_name = $pratica.".zip";
//error_reporting(E_ALL);    
    if ($zip->open($archive_file_name,  ZipArchive::CREATE)) {
        for($i=0;$i<count($res);$i++){
            $r = $res[$i];
	    $fileName = sprintf("%s%s",$allegatiDir,$r["nome_file"]);
//	    utils::debug(DEBUG_DIR."TEST.debug",$fileName,'a+');
            if (file_exists($fileName)){
	                if(! $zip->addFile($fileName, $r["nome_file"])){
			    utils::debug(DEBUG_DIR."DOWNLOAD.debug",$fileName,'a+');
                        }
			else{
			    utils::debug(DEBUG_DIR."DOWNLOAD-ERROR.debug",$fileName,'a+');
			}
            }
            else{
                utils::debug(DEBUG_DIR."DOWNLOAD-ERROR-NotFound.debug",$fileName,'a+');
            }
            
            
        }
		
        $zip->close();
    }
    else{
       die("Failed!");
    }
	$size = filesize($archive_file_name);
    $f = fopen($archive_file_name,'r');
    $zipFile = fread($f,$size);
    fclose($f);
    
    unlink(DATA_DIR."tmp".DIRECTORY_SEPARATOR.$archive_file_name);
    header("Content-type: application/zip"); 
    header("Content-Disposition: attachment; filename=$archive_file_name");
    header("Content-length: " . $size);
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
    print $zipFile;
    
}
else{
    echo "Errore";
    print_r($stmt->errorInfo());
}

?>
