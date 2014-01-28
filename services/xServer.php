<?php
include_once "../login.php";
error_reporting(E_ERROR);
$db=  appUtils::getDB();

$result=Array();
$action=(isset($_REQUEST["action"]) && $_REQUEST["action"])?($_REQUEST["action"]):("");
switch($action) {
        case "check-draw":
            $tipo=$_REQUEST["tipo"];
            $sql="SELECT id FROM pe.e_verifiche WHERE codice=?";
            $idTipo=$db->fetchColumn($sql,Array($tipo),0);
            $sql="SELECT count(*) as sorteggiato FROM pe.verifiche WHERE tipo=? and date_part('month',data_sorteggio)=date_part('month',CURRENT_DATE) AND date_part('year',data_sorteggio)=date_part('year',CURRENT_DATE);";
            $sorteggiato=(int)(bool)$db->fetchColumn($sql,Array($idTipo),0);
            $result=Array("sorteggiato"=>$sorteggiato);
            break;
        case "draw":
            $tipo=$_REQUEST["tipo"];
            $sql="SELECT id FROM pe.e_verifiche WHERE codice=?";
            $idTipo=$db->fetchColumn($sql,Array($tipo),0);
            switch($tipo){
                case "agibi":
                    $sql="SELECT pratica FROM pe.abitabi WHERE autocertificata=1 AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche WHERE id = (SELECT id FROM pe.e_verifiche WHERE codice = 'agibi'));";
                    $perc=0.1;
                    $res=$db->fetchAll($sql);
                    $tot=(int)(count($res)*$perc);
                    shuffle($res);
                    $result=array_slice($res,0,$tot);
                    $success=1;
                    for($i=0;$i<count($result);$i++){
                        $sql=sprintf("INSERT INTO pe.verifiche(pratica, tipo, uidins, tmsins, data_sorteggio) VALUES (%s, %s, %s, %s, %s);",$result[$i]["pratica"],$idTipo,$_SESSION["USER_ID"],time(),CURRENT_DATE);
                        if(!$db->executeQuery($sql)) {
                            $success=0;
                            $message="Si è verificato un problema nell'estrazione dei certificati";
                        }
                    }
                    $result=Array("success"=>$success,"message"=>$message);
                    break;
                case "pratica":
                    $result=Array("message"=>"Not yet implemented");
            }
            
            break;
	case "nuovi_oneri":
		$data_inizio=$_REQUEST["inizio"];
		$sqlData="SELECT code,zona,c_1,c_2,c_3,c_4,c_5,c_6,c_7,c_8,c_9,c_10,c_11,c_12,c_13,c_14,c_15,c_16,c_17,c_18,c_19,c_20,'$data_inizio'::date as inizio_validita FROM oneri.tabella_b WHERE inizio_validita = (SELECT max(inizio_validita) from oneri.tabella_b)";
		$data=$db->fetchAll($sqlData);
		foreach($data as $v) $db->insert("oneri.tabella_b",$v);
		
		break;
	case "oneri":
		$pratica=$_REQUEST['pratica'];
		$field=$_REQUEST["field"];
		switch($field){
			case "anno":
				$anno=$_REQUEST['data']['anno'];
				$sql="SELECT * FROM oneri.e_tariffe WHERE anno = ?";
				$ris=$db->fetchAll($sql,Array($anno));
				break;
			case "tabella":
				$anno=$_REQUEST['data']['anno'];
				$tabella=$_REQUEST['data']['tabella'];
				break;
			default:
				break;
		}
		break;
	case "checkModelli":
		$value=(isset($_REQUEST["id"]) && $_REQUEST["id"])?($_REQUEST["id"]):("%");
		$sql="SELECT id,nome FROM stp.e_modelli WHERE id::varchar ilike ?";
		$res=$db->fetchAll($sql,Array($value),0);
		for($i=0;$i<count($res);$i++){
			
			$nome=$res[$i]["nome"];
			$text=appUtils::unzip(MODELLI, $nome);
			$err=appUtils::getDocxErrors($text);
			$result[$res[$i]["id"]]=$err;
		}
		
		break;
	case "printFieldsList":
		$customData=Array();
		if(file_exists(APPS_DIR."lib".DIRECTORY_SEPARATOR."print.fields.php")){
			error_reporting(E_ALL);
			include_once APPS_DIR."lib".DIRECTORY_SEPARATOR."print.fields.php";
		}
		$result=$customData;
		break;
        case "export-table":
            $tables=$_REQUEST['tabella'];
            $exportDir=DATA_DIR.implode(DIRECTORY_SEPARATOR,Array("db","export_file")).DIRECTORY_SEPARATOR;
            foreach($tables as $t){
                $sql="COPY $t TO '$exportDir$t.csv' CSV DELIMITER '|' HEADER";
                $sql=str_replace(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,$sql);
                $db->query($sql);
                $result[]=$sql;
            }
            break;
        case "import-table":
            $tables=$_REQUEST['tabella'];
            $importDir=DATA_DIR.implode(DIRECTORY_SEPARATOR,Array("db","import_file")).DIRECTORY_SEPARATOR;
            foreach($tables as $t){
                $sql="COPY $t FROM '$importDir$t.csv' CSV DELIMITER '|' HEADER";
                $sql=str_replace(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,$sql);
                $db->query($sql);
                $result[]=$sql;
            }
            break;
	default:
		break;
}
header('Content-Type: application/json');
print json_encode($result);
return;
?>