<?php
require_once "../login.php";
require_once LIB."anagr_tributaria.php";

$limit=$_REQUEST["limit"];
$offset=$_REQUEST["offset"];
$tipo=$_REQUEST["tipo_richiesta"];
$anno=$_REQUEST["anno_riferimento"];
$mode=$_REQUEST["mode"];
$fileName=$_REQUEST["filename"];
$filter["anno"]=($anno>0)?("anno_riferimento=$anno"):("true");
$filter["tipo"]=($tipo>0)?("tipo_richiesta=".($tipo-1)):("true");

$conn=utils::getDb();
switch($mode){
    case "testa":
        $sql="SELECT * FROM anagrafe_tributaria.find_testa($anno);";
        $stmt=$conn->prepare($sql);
        if($stmt->execute()){
            $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);	
            $testo=implode("",$rows[0]);
            $handle=fopen(STAMPE.$fileName,'w');
            if(!$handle) {
                $result=Array("success"=>0,"message"=>"Impossibile aprire il file $dir$fileName");
                header('Content-Type: application/json; charset=utf-8');
                print json_encode($result);
                return;
            }
            else{
                if(!fwrite($handle,$testo)){
                    $result=Array("success"=>0,"message"=>"Impossibile scrivere il file $dir$fileName");
                    header('Content-Type: application/json; charset=utf-8');
                    print json_encode($result);
                    return;
                }
                
            }
            
            fclose($handle);
        }
        else {
            utils::debug(DEBUG_DIR."anagrafe.debug",$sql);
            $result=Array("success"=>0,"message"=>"Errore nell'esecuzione della query $sql");
            header('Content-Type: application/json; charset=utf-8');
            print json_encode($result);
            return;
        } 
        
        break;
    case "coda":
        $sql="SELECT * FROM anagrafe_tributaria.find_coda($anno);";
        $stmt=$conn->prepare($sql);
        if($stmt->execute()){
            $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);	
            $testo=implode("",$rows[0]);
            $handle=fopen(STAMPE.$fileName,'w');
            if(!$handle) {
                $result=Array("success"=>0,"message"=>"Impossibile aprire il file $dir$fileName");
                header('Content-Type: application/json; charset=utf-8');
                print json_encode($result);
                return;
            }
            else{
                if(!fwrite($handle,$testo)){
                    $result=Array("success"=>0,"message"=>"Impossibile scrivere il file $dir$fileName");
                    header('Content-Type: application/json; charset=utf-8');
                    print json_encode($result);
                    return;
                }
                
            }
            
            fclose($handle);
        }
        else {
            utils::debug(DEBUG_DIR."anagrafe.debug",$sql);
            $result=Array("success"=>0,"message"=>"Errore nell'esecuzione della query $sql");
            header('Content-Type: application/json; charset=utf-8');
            print json_encode($result);
            return;
        } 
        break;
    case "dati":
        
        $handle=fopen(STAMPE.$fileName,'a+');
        if(!$handle) {
            $result=Array("success"=>0,"message"=>"Impossibile aprire il file $dir$fileName");
            header('Content-Type: application/json; charset=utf-8');
            print json_encode($result);
            return;
        }
        
        /*ELENCO DEI record*/
        $sql="SELECT * FROM anagrafe_tributaria.e_record order by ordine;";
        $stmt=$conn->prepare($sql);
        if ($stmt->execute()){
            $rec=$stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        else {
            utils::debug(DEBUG_DIR."anagrafe.debug",$sql);
            $result=Array("success"=>0,"message"=>"Errore nell'esecuzione della query $sql");
            header('Content-Type: application/json; charset=utf-8');
            print json_encode($result);
        } 
        /*Elenco delle pratiche*/
        $sql="SELECT pratica,protocollo as numero,data_presentazione FROM anagrafe_tributaria.pratiche WHERE ".implode(" AND ",$filter)." OFFSET $offset LIMIT $limit;";
        $stmt=$conn->prepare($sql);
        if ($stmt->execute()){
               $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        else{
            $result=Array("success"=>0,"message"=>"Errore nell'esecuzione della query $sql");
            header('Content-Type: application/json; charset=utf-8');
            print json_encode($result);
        }
        for($i=0;$i<count($res);$i++){		//CICLO SU TUUTE LE PRATICHE TROVATE
            
           list($pratica,$num_pr,$data_pres)=array_values($res[$i]);
           
            foreach($rec as $v){
                $sql="SELECT * FROM anagrafe_tributaria.e_tipi_record WHERE record='".$v["tipo"]."' order by ordine;";
                $stmt=$conn->prepare($sql);
                if ($stmt->execute()){
                    $fld_int=$stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach($fld_int as $el){
                        $intestazioni[$v["nome"]][$el["nome"]]=Array("visibile"=>$el["visibile"],"label"=>$el["label"],"tipo_dato"=>"","validazione"=>$el["tipo_validazione"],"active_form"=>$el["active_form"]);  //Da sostituire $el["nome"] con $el["label"] quando le avrÃ² messe
                    }
                    $fld=implode(",",array_keys($intestazioni[$v["nome"]]));
                    $arr_sql[$v["nome"]]="SELECT $fld FROM anagrafe_tributaria.".$v["funzione"]."($pratica);";
                    foreach($arr_sql as $key=>$sql){
                        $stmt=$conn->prepare($sql);
                        if ($stmt->execute()){
                            $r[$key]=$stmt->fetchAll(PDO::FETCH_ASSOC);
                        }
                        else {
                            utils::debug(DEBUG_DIR."anagrafe.debug",$sql);
                            $result=Array("success"=>0,"message"=>"Errore nell'esecuzione della query $sql");
                            header('Content-Type: application/json; charset=utf-8');
                            print json_encode($result);
                        }
                    }
                    $p=valida_recordset($r,$intestazioni,$pratica);
                    list($html_code,$errore)=array_values($p);
                    if($errore){
                        $riga[]="<tr><td class=\"pratica\"><a class=\"pratica\" href=\"#\" onclick=\"javascript:NewWindow('praticaweb.php?pratica=$pratica','Praticaweb',0,0,'yes')\">".(($limit*$offset)+($i+1)).") Pratica n° $num_pr del $data_pres</a></td></tr><tr><td width=\"100%\">$html_code</td></tr>";
                        $num_err++;
                        scrivi_file($r);
                    }
                    else
                        scrivi_file($r);
                    $r=Array();
                    
                }
                else {
                    utils::debug(DEBUG_DIR."anagrafe.debug",$sql);
                    $result=Array("success"=>0,"message"=>"Errore nell'esecuzione della query $sql");
                    header('Content-Type: application/json; charset=utf-8');
                    print json_encode($result);
                } 

             }
         }
        
        $result=Array("success"=>1,"html"=>implode("",$riga),"errori"=>$num_err);

                    
        
        fwrite($handle,$testo);
        fclose($handle);
        header('Content-Type: application/json; charset=utf-8');
                    print json_encode($result);
                    return;
        break;
    default:
        $result=Array("success"=>0,"message"=>"Errore parametro mode settato a valore '$mode'");
        break;
}

	

header('Content-Type: application/json; charset=utf-8');
print json_encode($result);
return;
?>