<?php
include_once "../login.php";
error_reporting(E_ERROR);
$db=  appUtils::getDB();
$dbh = utils::getDb();
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
            $sql="SELECT id,tipi_pratica,tipo_sorteggio,totale_sorteggi,coalesce(filter,'true') as filter FROM pe.e_verifiche WHERE sorteggio=1 AND codice=?";
            $stmt = $dbh->prepare($sql);
            if ($stmt->execute(Array($tipo))){
                $row = $stmt->fetch();
                $idTipo = $row["id"];
                $listTipi = $row["tipi_pratica"];
                $tipoSorteggio = $row["tipo_sorteggio"];
                $totSorteggi = $row["totale_sorteggi"];
                $filter = $row["filter"];
            }
            /*$idTipo=$db->fetchColumn($sql,Array($tipo),0);
            $listTipi=$db->fetchColumn($sql,Array($tipo),1);
            $tipoSorteggio=$db->fetchColumn($sql,Array($tipo),2);
            $totSorteggi=$db->fetchColumn($sql,Array($tipo),3);
            $filter=$db->fetchColumn($sql,Array($tipo),4);*/
            $filterTipi=($listTipi)?("tipo IN ($listTipi)"):('true');
            /*Pratiche Sorteggiate*/
            switch($tipo){
				case "sca":
					$sql = <<<EOT
SELECT 
	count(*) as sorteggiato 
FROM 
	pe.verifiche 
WHERE 
	tipo=999 and date_part('year',data_sorteggio)=date_part('year',$dataSorteggio);				
EOT;
					$sql = sprintf($sql,$filterTipi,$tipo);
					break;
                case "agibi":
                    $sql=sprintf("SELECT count(*) as sorteggiato FROM pe.verifiche WHERE tipo IN (%s) and date_part('month',data_sorteggio)=date_part('month',$dataSorteggio) AND date_part('year',data_sorteggio)=date_part('year',$dataSorteggio);",$idTipo);
                    break;
		        case "agibi-sanremo":
                    $sql=sprintf("SELECT count(*) as sorteggiato FROM pe.verifiche WHERE tipo IN (%s) AND date_part('year',data_sorteggio)=date_part('year',$dataSorteggio)-1;",$idTipo);
                    break;
                case "dia":
                case "scia":    
                case "pratica":
                case "cila":
                    $sql=sprintf("SELECT count(*) as sorteggiato FROM pe.verifiche WHERE tipo IN (%s) and date_part('month',data_sorteggio)=date_part('month',$dataSorteggio) AND date_part('year',data_sorteggio)=date_part('year',$dataSorteggio);",$idTipo);
                    break;

                case "durc":
                    $sql=sprintf("SELECT count(*) as sorteggiato FROM pe.verifiche WHERE tipo IN (%s) and date_part('week',data_sorteggio)=date_part('week',$dataSorteggio) AND date_part('year',data_sorteggio)=date_part('year',$dataSorteggio);",$idTipo);
                    break;
            }
            $sorteggiato=(int)(bool)$db->fetchColumn($sql,Array(),0);
            
            /*Pratiche Sorteggiabili*/
            switch($tipo){
				case "sca":
					$sql = <<<EOT
SELECT 
	count(*) as sorteggiabili 
FROM 
	pe.avvioproc 
WHERE 
	tipo=70000 AND 
	date_part('year',coalesce(data_prot,data_presentazione))=date_part('year',$dataSorteggio) AND
	pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s');
EOT;
					$sql = sprintf($sql,$filterTipi,$tipo);
					break;
                case "agibi":
                    $sql=sprintf("SELECT count(*)  FROM pe.elenco_pratiche_sorteggi WHERE  date_part('month',data_agibilita)=date_part('month',$dataSorteggio)-1 AND date_part('year',data_agibilita)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s');",$tipo);
                    break;
		        case "agibi-sanremo":
		            $sql=sprintf("SELECT count(*)  FROM pe.elenco_pratiche_sorteggi WHERE date_part('year',data_agibilita)=date_part('year',$dataSorteggio)-1 AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s');",$tipo);
		            break;
                case "com":
                    $sql=sprintf("SELECT count(*)  FROM pe.elenco_pratiche_sorteggi WHERE tipologia ilike 'com%' AND  date_part('month',il)=date_part('month',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s');",$tipo);
                    break;
                case "cila":
                    $sql=sprintf("SELECT count(*)  FROM pe.elenco_pratiche_sorteggi WHERE tipologia='cila'  AND  date_part('week',data_presentazione)=date_part('week',$dataSorteggio)-1 AND date_part('year',data_presentazione)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s');",$tipo);
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
//                case "durc":
//                    $sql=sprintf("SELECT count(*) FROM pe.elenco_pratiche_sorteggi WHERE  date_part('week',il)=date_part('week',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s');",$tipo);
                    break;
                case "durc":
                    $sql = <<<EOT
WITH pratiche_sorteggiabili AS (
SELECT DISTINCT pratica FROM pe.elenco_pratiche_sorteggi WHERE  date_part('week',il)=date_part('week',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='$tipo')
),
esecutori AS (
    SELECT id,pratica,codfis,piva,format('%s %s',coalesce(cognome,''),coalesce(nome,'')) as nominativo,coalesce(piva,'') as piva FROM pe.soggetti WHERE esecutore=1
)
SELECT count(*) FROM pratiche_sorteggiabili INNER JOIN esecutori USING(pratica);
EOT;
                    //$sql=sprintf($sqlA,$tipo);
			//echo $sql;
                    break;
            }
            $sorteggiabili=$db->fetchColumn($sql,Array(),0);
            
            
            $result=Array("sorteggiato"=>$sorteggiato,"sorteggiabili"=>$sorteggiabili,"sql"=>$sql);
            break;
        case "draw":
            $tipo=$_REQUEST["tipo"];
            $dataSorteggio=($_REQUEST["data_sorteggio"])?("'".$_REQUEST["data_sorteggio"]."'::date"):('CURRENT_DATE');
            $sql="SELECT id,tipi_pratica,tipo_sorteggio,totale_sorteggi,coalesce(filter,'true') as filter FROM pe.e_verifiche WHERE sorteggio=1 AND codice=?";
            $stmt = $dbh->prepare($sql);
            if ($stmt->execute(Array($tipo))){
                $row = $stmt->fetch();
                $idTipo = $row["id"];
                $listTipi = $row["tipi_pratica"];
                $tipoSorteggio = $row["tipo_sorteggio"];
                $totSorteggi = $row["totale_sorteggi"];
                $filter = $row["filter"];
            }
            /*$idTipo=$db->fetchColumn($sql,Array($tipo),0);
            $listTipi=$db->fetchColumn($sql,Array($tipo),1);
            $tipoSorteggio=$db->fetchColumn($sql,Array($tipo),2);
            $totSorteggi=$db->fetchColumn($sql,Array($tipo),3);
            $filter=$db->fetchColumn($sql,Array($tipo),4);*/
            $filterTipi=($listTipi)?("tipo IN ($listTipi)"):('true');
            $filterTipi=($listTipi)?("tipo IN ($listTipi)"):('true');
            $conn=utils::getDb();
            switch($tipo){
                case "sca":
                    $sql = <<<EOT
SELECT 
    pratica 
FROM 
    pe.avvioproc 
 WHERE 
    date_part('year',coalesce(data_prot,data_presentazione))=date_part('year',%s) AND 
    pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND 
    tipo in (70000);                       
EOT;
                    $sql=sprintf($sql,$dataSorteggio,$tipoSorteggio,$idTipo);
                    break;
                case "agibi":
                    $sql=sprintf("SELECT pratica FROM pe.abitabi WHERE date_part('month',data_agibilita)=date_part('month',$dataSorteggio)-1 AND date_part('year',data_agibilita)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND %s;",$tipo);
                    $perc=0.1;
                    break;
		        case "agibi-sanremo":
		            $sql=sprintf("SELECT pratica FROM pe.elenco_pratiche_sorteggi WHERE date_part('year',data_agibilita)=date_part('year',$dataSorteggio)-1 AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND %s;",$tipo,$filterTipi);
                    break;
                case "dia":
                    $sql=sprintf("SELECT pratica FROM pe.elenco_pratiche_sorteggi WHERE pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND tipologia='dia' AND  date_part('month',il)=date_part('month',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio)  AND %s;",$tipo,$filterTipi);
                    break;
                case "cila":
                    $sql=sprintf("SELECT pratica FROM pe.elenco_pratiche_sorteggi WHERE pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND tipologia='cila' AND  date_part('week',data_presentazione)=date_part('week',$dataSorteggio)-1 AND date_part('year',data_presentazione)=date_part('year',$dataSorteggio)  AND %s;",$tipo,$filterTipi);
                    break;
                case "scia":
                    $sql=sprintf("SELECT pratica FROM pe.elenco_pratiche_sorteggi WHERE pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND tipologia='scia' AND  date_part('month',il)=date_part('month',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio)  AND %s;",$tipo,$filterTipi);
                    break;
                case "pratica":
                    $sql=sprintf("SELECT pratica FROM pe.elenco_pratiche_sorteggi WHERE pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND tipologia='permessi' AND  date_part('month',il)=date_part('month',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio)  AND %s;",$tipo,$filterTipi);         
                    break;
/*                case "durc":
                    $sql=sprintf("SELECT pratica FROM pe.elenco_pratiche_sorteggi WHERE pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='%s') AND date_part('week',il)=date_part('week',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio) AND  %s;",$tipo,$filterTipi);                  
                    break;*/
                case "durc":
                    $sql = <<<EOT
WITH pratiche_sorteggiabili AS (
SELECT DISTINCT pratica FROM pe.elenco_pratiche_sorteggi WHERE  date_part('week',il)=date_part('week',$dataSorteggio)-1 AND date_part('year',il)=date_part('year',$dataSorteggio) AND pratica NOT IN (SELECT DISTINCT pratica FROM pe.verifiche INNER JOIN pe.e_verifiche ON(verifiche.tipo=e_verifiche.id) WHERE codice='$tipo')
),
esecutori AS (
SELECT id,pratica,codfis,piva,ragsoc,format('%s %s',coalesce(cognome,''),coalesce(nome,'')) as nominativo,coalesce(piva,'') as piva FROM pe.soggetti WHERE esecutore=1
)
SELECT * FROM pratiche_sorteggiabili INNER JOIN esecutori USING(pratica);
EOT;
                    break;
            }
            utils::debug(DEBUG_DIR.$_SESSION["USER_ID"]."_draw.debug",$sql);
            $stmt=$conn->prepare($sql);
            if(!$stmt->execute()){
                $error = $stmt->errorInfo();
		        $result=Array("success"=>0,"message"=>"draw_select_error","query"=>$sql,"error"=>$error[2]);
                break;
            }
            $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
            $tot=($tipoSorteggio=='Percentuale')?(ceil(count($res)*$totSorteggi)):($totSorteggi);
            shuffle($res);
            $result=array_slice($res,0,$tot);
            $success=1;
            utils::debug(DEBUG_DIR.'draw-data.debug', $result);
            $conn->beginTransaction();
            for($i=0;$i<count($result);$i++){
                $istr = appUtils::chooseRespVerifiche($idTipo);
                if ($tipo=="durc"){
                    $sql=sprintf("INSERT INTO pe.verifiche(pratica, tipo, uidins, tmsins, data_sorteggio, resp_proc_verifica, note, id_rif) VALUES (%s,%s,%s, %s, %s, %s, %s,%s );",$result[$i]["pratica"],$idTipo,$_SESSION["USER_ID"],time(),$dataSorteggio,$istr,"'Sorteggio DURC della ditta : ".$result[$i]["ragsoc"]."'","'".$result[$i]["piva"]."'");
                }
                else{
                    $sql=sprintf("INSERT INTO pe.verifiche(pratica, tipo, uidins, tmsins, data_sorteggio, resp_proc_verifica) VALUES (%s, %s, %s, %s, %s,%s );",$result[$i]["pratica"],$idTipo,$_SESSION["USER_ID"],time(),$dataSorteggio,$istr);
                }  
  //              $sql=sprintf("INSERT INTO pe.verifiche(pratica, tipo, uidins, tmsins, data_sorteggio, resp_proc_verifica) VALUES (%s, %s, %s, %s, %s,%s );",$result[$i]["pratica"],$idTipo,$_SESSION["USER_ID"],time(),$dataSorteggio,$istr);
                utils::debug(DEBUG_DIR.'draw.debug', $sql);
                $stmt=$conn->prepare($sql);
                if(!$stmt->execute()) {
                    $success=0;
                    $error = $stmt->errorInfo();
                    $message="no_draw_insert - ".$error[2];
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
		    $schema = $_REQUEST["schema"];
			$pr = $_REQUEST["pratica"];
			if($pr){
				switch($schema){
				    case "vigi":
						$db->delete('vigi.avvioproc',Array("pratica"=>$_REQUEST["pratica"]));
						break;
				    default:
						$db->delete('pe.avvioproc',Array("pratica"=>$_REQUEST["pratica"]));
						break;
				}
			}    
            break;
        case "notify":
            $userId = $_SESSION["USER_ID"];
            $result["msg-scadenze"]=appUtils::getScadenze($userId);
            $result["msg-verifiche"]=appUtils::getVerifiche($userId);
            //DETTAGLI SULLE VERIFICHE
            //$result["query"]=$sql;
            break;
	case "invia_documento":
		$conn = utils::getDB();
		$numero = $_REQUEST["numero_pratica"];
		$idDoc = $_REQUEST["id"];
		$idAllegato = $_REQUEST['documento'];
		$table = $_REQUEST["assoc_table"];
		$schema = $_REQUEST["assoc_schema"];
		$sql = "SELECT pratica FROM pe.avvioproc WHERE numero=?";
		$stmt = $conn->prepare($sql);
		if($stmt->execute(Array($numero))){
		    $pratica = $stmt->fetchColumn();
		    if ($pratica){
				$sql="INSERT INTO storage.associazioni(documento,pratica,id_allegato,assoc_schema,assoc_table) VALUES(?,?,?,?,?)";
				$stmt = $conn->prepare($sql);
				if($stmt->execute(Array($idDoc,$pratica,$idAllegato,$schema,$table))){
				    $sql = "SELECT filedata,filename FROM storage.documentazione_inviata WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(Array($idDoc));
					list($ff,$filename) = $stmt->fetch();
                    $file = base64_decode($ff);
                    
                    $pr = new pratica($res);
					$fname = sprintf("%s%s",$pr->allegati,$filename);
                    $f = fopen($fname,'w');
                    if ($f){
						if (fwrite($f,$file))
						    $result = Array("success"=>1,"message"=>"Documento inviato alla pratica con successo e scritto in $fname");
						else
						    $result = Array("success"=>-1,"message"=>"Impossibile scrivere il documento $fname");
						fclose($f);
					}
					else{
						$result = Array("success"=>-1,"message"=>"Impossibile aprire il documento $fname");
					}
				    
				}
				else{
				    
				    $result=Array("success"=>-1,"query"=>$sql,"message"=>$stmt->errorInfo());
				}
		    }
		    else{
				$result = Array("success"=>-1,"message"=>"Nessuna pratica trovata");
		    }
		}
		else{
		    $result=Array("success"=>-1,"message"=>$stmt->errorInfo());
		}
		break;
	case "save-data":
		$conn = utils::getDB();
		$table=$_POST["table"];
		$field=$_POST["field"];
		$id=$_POST["id"];
		$value=$_POST["value"];
		$user=$_SESSION["USER_ID"];
                $tms= time();
		$sql = "UPDATE $table SET $field=?, uidupd=?, tmsupd=? WHERE id=?;";
		
		$stmt = $conn->prepare($sql);

		if($stmt->execute(Array($value,$user,$tms,$id))){
			$result = Array("success"=>1,"message"=>"");
		}
		else{
			$result = Array("success"=>-1,"message"=>$sql);
		}

//		$result = Array("success"=>1,"message"=>$sql);		
		break;
	case "invia-pec":
		$pratica=$_REQUEST["pratica"];
		$id=$_REQUEST["id"];
		$r = appUtils::getComunicazione($id);
		if($r["success"]==1){
			require_once DATA_DIR."config.mail.php";
			require_once LIB."mail.class.php";
			$rr = inviaPec("",$r["comunicazione"]["to"],$r["comunicazione"]["subject"],$r["comunicazione"]["text"],$r["comunicazione"]["attachments"]);
			if($rr["success"]==1){
				$dbh = utils::getDb();
				$sql = "UPDATE pe.comunicazioni SET data_invio=?, id_comunicazione=? WHERE pratica=? AND id=?;";
				$stmt = $dbh->prepare->sql($sql);
				if(!$stmt->execute(Array(date('d/m/Y'),$rr["uuid"],$pratica,$id))){
					$err = $stmt->errorInfo();
					$result = Array("success"=>-1,"message"=>$err[2]);
				}
				else{
					$result = Array("success"=>1,"message"=>"");
				}
			}
			else{
				$message="Si sono verificati degli errori durante l'invio della comunicazione";
				$result = Array("success"=>-1,"message"=>$message);
			}
		}
		break;
	default:
		break;
}
header('Content-Type: application/json; charset=utf-8');
print json_encode($result);
return;
?>
