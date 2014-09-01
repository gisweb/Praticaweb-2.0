<?php
include_once "../login.php";
error_reporting(E_ERROR);
$db=  appUtils::getDB();

$result=Array();
$action=(isset($_REQUEST["action"]) && $_REQUEST["action"])?($_REQUEST["action"]):("");
switch($action) {
        case "list-pratiche-folder":
            $sql="SELECT pratica,B.nome as tipo,A.numero,coalesce(data_presentazione,data_prot) as data FROM pe.avvioproc A INNER JOIN pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN pe.e_categoriapratica C ON (coalesce(A.categoria,0)=C.id)  WHERE cartella=? AND pratica <> ? ORDER BY data_presentazione DESC;";
            $result=$db->fetchAll($sql,Array($_REQUEST["value"],$_REQUEST["pratica"]));
            
            break;
        case "check-draw":
            $dataSorteggio=($_REQUEST["data_sorteggio"])?("'".$_REQUEST["data_sorteggio"]."'::date"):('CURRENT_DATE');
            $tipo=$_REQUEST["tipo"];
            $sql="SELECT id,tipi_pratica,tipo_sorteggio,totale_sorteggi,coalesce(filter,'true') as filter FROM pe.e_verifiche WHERE codice=?";
            $idTipo=$db->fetchColumn($sql,Array($tipo),0);
            $listTipi=$db->fetchColumn($sql,Array($tipo),1);
            $tipoSorteggio=$db->fetchColumn($sql,Array($tipo),2);
            $totSorteggi=$db->fetchColumn($sql,Array($tipo),3);
            $filter=$db->fetchColumn($sql,Array($tipo),4);
            /*Pratiche Sorteggiate*/
            switch($tipo){
                case "agibi":
                    $sql=sprintf("SELECT count(*) as sorteggiato FROM pe.verifiche WHERE tipo IN (%s) and date_part('month',data_sorteggio)=date_part('month',$dataSorteggio) AND date_part('year',data_sorteggio)=date_part('year',$dataSorteggio);",$idTipo);
                    break;
                case "dia":
                case "scia":    
                case "pratica":
                    $sql=sprintf("SELECT count(*) as sorteggiato FROM pe.verifiche WHERE tipo IN (%s) and date_part('month',data_sorteggio)=date_part('month',$dataSorteggio) AND date_part('year',data_sorteggio)=date_part('year',$dataSorteggio);",$idTipo);
                    break;
                case "durc":
                    $sql=sprintf("SELECT count(*) as sorteggiato FROM pe.verifiche WHERE tipo IN (%s) and date_part('week',data_sorteggio)=date_part('week',$dataSorteggio) AND date_part('year',data_sorteggio)=date_part('year',$dataSorteggio);",$idTipo);
                    break;
            }
            $sorteggiato=(int)(bool)$db->fetchColumn($sql,Array(),0);
            
            /*Pratiche Sorteggiabili*/
            switch($tipo){
                case "agibi":
                    $sql=sprintf("SELECT count(*)  FROM pe.elenco_pratiche_sorteggi WHERE  date_part('month',data_agibilita)=date_part('month',$dataSorteggio)-1 AND date_part('year',data_agibilita)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s');",$tipo);
                    break;
                case "com":
                    $sql=sprintf("SELECT count(*)  FROM pe.elenco_pratiche_sorteggi WHERE tipologia ilike 'com%' AND  date_part('month',il)=date_part('month',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s');",$tipo);
                    break;
                case "dia":
                    $sql=sprintf("SELECT count(*)  FROM pe.elenco_pratiche_sorteggi WHERE tipologia='dia' AND  date_part('month',il)=date_part('month',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s');",$tipo);
                    break;
                case "scia":
                    $sql=sprintf("SELECT count(*)  FROM pe.elenco_pratiche_sorteggi WHERE tipologia='scia' AND  date_part('month',il)=date_part('month',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s');",$tipo);
                    break;
                case "pratica":
                    $sql=sprintf("SELECT count(*)  FROM pe.elenco_pratiche_sorteggi WHERE tipologia='permessi' AND  date_part('month',il)=date_part('month',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s');",$tipo);
                    break;
                case "durc":
                    $sql=sprintf("SELECT count(*) FROM pe.elenco_pratiche_sorteggi WHERE  date_part('week',il)=date_part('week',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s');",$tipo);
                    break;
            }
            $sorteggiabili=$db->fetchColumn($sql,Array(),0);
            
            
            $result=Array("sorteggiato"=>$sorteggiato,"sorteggiabili"=>$sorteggiabili,"sql"=>$sql);
            break;
        case "draw":
            $tipo=$_REQUEST["tipo"];
            $dataSorteggio=($_REQUEST["data_sorteggio"])?("'".$_REQUEST["data_sorteggio"]."'::date"):('CURRENT_DATE');
            $sql="SELECT id,tipi_pratica,tipo_sorteggio,totale_sorteggi,coalesce(filter,'true') as filter FROM pe.e_verifiche WHERE codice=?";
            $idTipo=$db->fetchColumn($sql,Array($tipo),0);
            $listTipi=$db->fetchColumn($sql,Array($tipo),1);
            $tipoSorteggio=$db->fetchColumn($sql,Array($tipo),2);
            $totSorteggi=$db->fetchColumn($sql,Array($tipo),3);
            $filter=$db->fetchColumn($sql,Array($tipo),4);
            $filterTipi=($listTipi)?("tipo IN ($listTipi)"):('true');
            $conn=utils::getDb();
            switch($tipo){
                case "agibi":
                    $sql=sprintf("SELECT pratica FROM pe.abitabi WHERE date_part('month',data_agibilita)=date_part('month',$dataSorteggio)-1 AND date_part('year',data_agibilita)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND %s;",$tipo);
                    $perc=0.1;
                    break;
                case "dia":
                    $sql=sprintf("SELECT pratica FROM pe.elenco_pratiche_sorteggi WHERE pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND tipologia='dia' AND  date_part('month',il)=date_part('month',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio)  AND %s;",$tipo,$filterTipi);
                    break;
                case "scia":
                    $sql=sprintf("SELECT pratica FROM pe.elenco_pratiche_sorteggi WHERE pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND tipologia='scia' AND  date_part('month',il)=date_part('month',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio)  AND %s;",$tipo,$filterTipi);
                    break;
                case "pratica":
                    $sql=sprintf("SELECT pratica FROM pe.elenco_pratiche_sorteggi WHERE pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND tipologia='permessi' AND  date_part('month',il)=date_part('month',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio)  AND %s;",$tipo,$filterTipi);         
                    break;
                case "durc":
                    $sql=sprintf("SELECT pratica FROM pe.elenco_pratiche_sorteggi WHERE pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND date_part('week',il)=date_part('week',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio) AND  %s;",$tipo,$filterTipi);                  
                    break;
            }
            utils::debug(DEBUG_DIR.$_SESSION["USER_ID"]."_draw.debug",$sql);
            $stmt=$conn->prepare($sql);
            if(!$stmt->execute()){
                $result=Array("success"=>0,"message"=>"draw_select_error","query"=>$sql);
                break;
            }
            $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
            $tot=($tipoSorteggio=='Percentuale')?(ceil(count($res)*$totSorteggi)):($totSorteggi);
            shuffle($res);
            $result=array_slice($res,0,$tot);
            $success=1;
            $conn->beginTransaction();
            for($i=0;$i<count($result);$i++){
                $sql=sprintf("INSERT INTO pe.verifiche(pratica, tipo, uidins, tmsins, data_sorteggio) VALUES (%s, %s, %s, %s, %s);",$result[$i]["pratica"],$idTipo,$_SESSION["USER_ID"],time(),$dataSorteggio);
                utils::debug(DEBUG_DIR.'draw.debug', $sql);
                $stmt=$conn->prepare($sql);
                if(!$stmt->execute()) {
                    $success=0;
                    $message="no_draw_insert";
                }
            }
            if($success==1){
                $conn->commit();
                $message="Sono state inserite ".count($result)." verifiche";
            }
            else{
                $conn->rollBack();
            }
            $result=Array("success"=>$success,"message"=>$message);
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
			//error_reporting(E_ALL);
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
        case "delete-pratica":
            if($_REQUEST["pratica"])
                $db->delete('pe.avvioproc',Array("pratica"=>$_REQUEST["pratica"]));
            break;
        case "notify":
            $userId = $_SESSION["USER_ID"];
            $userId = 17;
            $result["msg-scadenze"]=appUtils::getScadenze($userId);
            $result["msg-verifiche"]=appUtils::getVerifiche($userId);
            //DETTAGLI SULLE VERIFICHE
            //$result["query"]=$sql;
            break;
	default:
		break;
}
header('Content-Type: application/json; charset=utf-8');
print json_encode($result);
return;
?>