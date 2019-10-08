<?php
require_once "../login.php";
require_once LIB."anagr_tributaria.php";

function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
}

$limit=$_REQUEST["limit"];
$offset=$_REQUEST["offset"];
$tipo=$_REQUEST["tipo_richiesta"];
$anno=$_REQUEST["anno_riferimento"];
$mode=$_REQUEST["mode"];
$fileName=$_REQUEST["filename"];
$processed=$_REQUEST["processed"];
$rejected = $_REQUEST["discarded"];
$filter["anno"]=($anno>0)?("anno_riferimento=$anno"):("true");
$filter["tipo"]=($tipo>0)?("tipo_richiesta=".($tipo-1)):("true");
$num_err=$_REQUEST["error"];
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
        $result=Array("success"=>1,"message"=>"");
        header('Content-Type: application/json; charset=utf-8');
        print json_encode($result);
        return;
        break;
    case "coda":
        $sql="SELECT * FROM anagrafe_tributaria.find_coda($anno);";
        $stmt=$conn->prepare($sql);
        if($stmt->execute()){
            $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);	
            $testo=implode("",$rows[0]);
            $handle=fopen(STAMPE.$fileName,'a+');
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
            $result=Array("success"=>1,"message"=>"");
            header('Content-Type: application/json; charset=utf-8');
            print json_encode($result);
            return;
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
            $result=Array("success"=>0,"message"=>"Errore nell'esecuzione della query $sql","errori"=>$num_err,"processed"=>$processed);
            header('Content-Type: application/json; charset=utf-8');
            print json_encode($result);
            return;
        } 

        /*Elenco delle pratiche*/

        $sql="SELECT pratica,protocollo as numero,data_presentazione,nome_tipo FROM anagrafe_tributaria.pratiche WHERE ".implode(" AND ",$filter)." OFFSET $offset LIMIT $limit;";
        $stmt=$conn->prepare($sql);
        if ($stmt->execute()){
               $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        else{
            $result=Array("success"=>0,"message"=>"Errore nell'esecuzione della query $sql","errori"=>$num_err,"processed"=>$processed);
            header('Content-Type: application/json; charset=utf-8');
            print json_encode($result);
            return;
        }

        for($i=0;$i<count($res);$i++){		//CICLO SU TUUTE LE PRATICHE TROVATE
           $discarded=0;
           list($pratica,$num_pr,$data_pres,$tipo)=array_values($res[$i]);
           /*Per ogni tipo di record*/
            foreach($rec as $v){    
                $t1=  getmicrotime();
                $sql="SELECT * FROM anagrafe_tributaria.e_tipi_record WHERE record='".$v["tipo"]."' order by ordine;";
                $stmt=$conn->prepare($sql);
                if ($stmt->execute()){
                    $fld_int=$stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach($fld_int as $el){
                        $intestazioni[$v["nome"]][$el["nome"]]=Array("visibile"=>$el["visibile"],"label"=>$el["label"],"tipo_dato"=>"","validazione"=>$el["tipo_validazione"],"active_form"=>$el["active_form"]);  //Da sostituire $el["nome"] con $el["label"] quando le avrÃ² messe
                    }
                    $fld=implode(",",array_keys($intestazioni[$v["nome"]]));
                    $arr_sql[$v["nome"]]="SELECT $fld FROM anagrafe_tributaria.".$v["funzione"]."($pratica);";
                    //foreach($arr_sql as $key=>$sql){
                    //$t2=  getmicrotime();
                    $stmt=$conn->prepare($arr_sql[$v["nome"]]);
                    if ($stmt->execute()){
                        $r[$v["nome"]]=$stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    else {
                        utils::debug(DEBUG_DIR."anagrafe.debug",$sql);
                        $discarded=1;
                        $message[]="Errore nell'esecuzione della query \"".$arr_sql[$v["nome"]]."\"";
                    }
                    /*$str=sprintf("%d) Query \"%s\" : %d ms",$i,$sql,(getmicrotime()-$t2)*1000);
                    utils::debug(DEBUG_DIR."time-".(string)$offset.".debug",$str);*/
                    //}
                    
                    
                }
                else {
                    utils::debug(DEBUG_DIR."anagrafe.debug",$sql);
                    $discarded=1;
                    $message[]="Errore nell'esecuzione della query \"$sql\"";
                    //header('Content-Type: application/json; charset=utf-8');
                    //print json_encode($result);
                } 
                /*$str=sprintf("%d) Ciclo sulle Pratiche - Record %s : %d ms",$i,$v["tipo"],(getmicrotime()-$t)*1000);
                utils::debug(DEBUG_DIR."time-".(string)$offset.".debug",$str);*/
            }
            
            $p=valida_recordset($r,$intestazioni,$pratica);
            
            list($html_code,$errore)=array_values($p);
            if($discarded==1){
                $rejected++;
            }
            else{
                if($errore){
                    $num_err++;
                    $riga[]="<tr><td class=\"pratica\"><a class=\"pratica\" href=\"#\" onclick=\"javascript:NewWindow('praticaweb.php?pratica=$pratica','Praticaweb',0,0,'yes')\">$num_err) $tipo protocollo n° $num_pr del $data_pres</a></td></tr><tr><td width=\"100%\">$html_code</td></tr>";
                    scrivi_file($r,$fileName);
                }
                else
                    scrivi_file($r,$fileName);
                $r=Array();
                /*$str=sprintf("%d) Ciclo sulle Pratiche : %d ms",$i,(getmicrotime()-$t)*1000);
                utils::debug(DEBUG_DIR."time-".(string)$offset.".debug",$str);*/
                $processed++;
                }
         }
        
        $result=Array("success"=>1,"html"=>implode("",$riga),"message"=>$message,"errori"=>$num_err,"processed"=>$processed,"discarded"=>$rejected);

                    
        
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