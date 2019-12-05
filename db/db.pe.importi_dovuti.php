<?php

$pratica = $_REQUEST["pratica"];
$activeForm = $_REQUEST["active_form"];
if($_POST["azione"]=="Salva" && $_REQUEST["mode"]=="new"){
    
    /*
    $tipoScadenza = $_REQUEST["tipo_scadenza"];
    if ($tipoScadenza=='DATA'){
        $campo = "data_scadenza";
        $data = $_REQUEST["data_scadenza"];
        if (!$data) $Errors["data_scadenza"] = "Campo Obbligatorio";
    }
    else{
        $campo = "note_scadenza";
        $data = $_REQUEST["note_scadenza"];
        if (!$data) $Errors["note_scadenza"] = "Campo Obbligatorio";
    }
    */
    $dbh=utils::getDb();
    $data = $_REQUEST["scadenza"];
    if ($data){
        
        $sql = "SELECT * FROM ragioneria.importi_dovuti WHERE pratica=? AND scadenza = ?;";
        $stmt = $dbh->prepare($sql);
        if($stmt->execute(Array($pratica,$data))){
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($res)>=5){
                $Errors["scadenza"] = "Impossibile inserire pi&ugrave; di 5 pagamenti nella stessa scadenza";
            }
            elseif(count($res)==0){
                $raggruppamento = utils::rand_str(10);
            }
            else{
                $raggruppamento = $res[0]["codice_richiesta"];
            }
        }
    }
    if (!$data) $Errors["scadenza"] = "Campo Obbligatorio";
    if(utils::is_zero($_REQUEST["importo_1"])){
        $Errors["importo_1"] = "Campo Obbligatorio";
        if(!trim($_REQUEST["causale_1"])){
            $Errors["causale_1"] = "Campo obbligatorio";
        }
        if(!trim($_REQUEST["tipo_1"])){
            $Errors["tipo_1"] = "Campo obbligatorio";
        }
    }
    elseif(utils::validation('valuta', $_REQUEST["importo_1"])){
        if(!trim($_REQUEST["causale_1"])){
            $Errors["causale_1"] = "Campo obbligatorio";
        }
        if(!trim($_REQUEST["tipo_1"])){
            $Errors["tipo_1"] = "Campo obbligatorio";
        }
        $num = ($_REQUEST["quantita_1"])?($_REQUEST["quantita_1"]):(1);
        if (!utils::validation('intero', $num)){
            $Errors["quantita_1"] = "Il valore $num non &egrave; di tipo numerico";
        }
        
        $imp1 = str_replace(',','.',$_REQUEST["importo_1"]);
        $valori[] = Array($pratica,$data,$_REQUEST["tipo_1"],$imp1,$_REQUEST["causale_1"],$raggruppamento,$num,$_SESSION["USER_ID"],time());
        
    }
    else{
        $Errors["importo_1"] = sprintf("Il valore dell\'importo %s non è nel formato corretto (Es. 100,00)",$_REQUEST["importo_1"]);
        if(!trim($_REQUEST["causale_1"])){
            $Errors["causale_1"] = "Campo obbligatorio";
        }
        if(!trim($_REQUEST["tipo_1"])){
            $Errors["tipo_1"] = "Campo obbligatorio";
        }
    }
    
    if(utils::is_zero($_REQUEST["importo_2"])){
        
    }
    elseif(utils::validation('valuta', $_REQUEST["importo_2"])){
        if(!trim($_REQUEST["causale_2"])){
            $Errors["causale_2"] = "Campo obbligatorio";
        }
        if(!trim($_REQUEST["tipo_2"])){
            $Errors["tipo_2"] = "Campo obbligatorio";
        }
        
        $num = ($_REQUEST["quantita_2"])?($_REQUEST["quantita_2"]):(1);
        if (!utils::validation('intero', $num)){
            $Errors["quantita_2"] = "Il valore $num non &egrave; di tipo numerico";
        }
        
        $imp2 = str_replace(',','.',$_REQUEST["importo_2"]);
        $valori[] = Array($pratica,$data,$_REQUEST["tipo_2"],$imp2,$_REQUEST["causale_2"],$raggruppamento,$num,$_SESSION["USER_ID"],time());

    }
    else{
        $Errors["importo_2"] = sprintf("Il valore dell\'importo %s non è nel formato corretto (Es. 100,00)",$_REQUEST["importo_2"]);
        if(!trim($_REQUEST["causale_2"])){
            $Errors["causale_2"] = "Campo obbligatorio";
        }
        if(!trim($_REQUEST["tipo_2"])){
            $Errors["tipo_2"] = "Campo obbligatorio";
        }
    }
    
    if(utils::is_zero($_REQUEST["importo_3"])){
        
    }
    elseif(utils::validation('valuta', $_REQUEST["importo_3"])){
        if(!trim($_REQUEST["causale_3"])){
            $Errors["causale_3"] = "Campo obbligatorio";
        }
        if(!trim($_REQUEST["tipo_3"])){
            $Errors["tipo_3"] = "Campo obbligatorio";
        }

        $num = ($_REQUEST["quantita_3"])?($_REQUEST["quantita_3"]):(1);
        if (!utils::validation('intero', $num)){
            $Errors["quantita_3"] = "Il valore $num non &egrave; di tipo numerico";
        }
        
        $imp3 = str_replace(',','.',$_REQUEST["importo_3"]);
        $valori[] = Array($pratica,$data,$_REQUEST["tipo_3"],$imp3,$_REQUEST["causale_3"],$raggruppamento,$num,$_SESSION["USER_ID"],time());

    }
    else{
        $Errors["importo_3"] = sprintf("Il valore dell\'importo %s non è nel formato corretto (Es. 100,00)",$_REQUEST["importo_3"]);
        if(!trim($_REQUEST["causale_3"])){
            $Errors["causale_3"] = "Campo obbligatorio";
        }
        if(!trim($_REQUEST["tipo_3"])){
            $Errors["tipo_3"] = "Campo obbligatorio";
        }
    }
    
    if(utils::is_zero($_REQUEST["importo_4"])){
        
    }
    elseif(utils::validation('valuta', $_REQUEST["importo_4"])){
        if(!trim($_REQUEST["causale_4"])){
            $Errors["causale_4"] = "Campo obbligatorio";
        }
        if(!trim($_REQUEST["tipo_4"])){
            $Errors["tipo_4"] = "Campo obbligatorio";
        }

        $num = ($_REQUEST["quantita_4"])?($_REQUEST["quantita_4"]):(1);
        if (!utils::validation('intero', $num)){
            $Errors["quantita_4"] = "Il valore $num non &egrave; di tipo numerico";
        }
        
        $imp4 = str_replace(',','.',$_REQUEST["importo_4"]);
        $valori[] = Array($pratica,$data,$_REQUEST["tipo_4"],$imp4,$_REQUEST["causale_4"],$raggruppamento,$num,$_SESSION["USER_ID"],time());

    }
    else{
        $Errors["importo_4"] = sprintf("Il valore dell\'importo %s non è nel formato corretto (Es. 100,00)",$_REQUEST["importo_4"]);
        if(!trim($_REQUEST["causale_4"])){
            $Errors["causale_4"] = "Campo obbligatorio";
        }
    }
    
    if(utils::is_zero($_REQUEST["importo_5"])){
        
    }
    elseif(utils::validation('valuta', $_REQUEST["importo_5"])){
        if(!trim($_REQUEST["causale_5"])){
            $Errors["causale_5"] = "Campo obbligatorio";
        }
        if(!trim($_REQUEST["tipo_5"])){
            $Errors["tipo_5"] = "Campo obbligatorio";
        }
        
        $num = ($_REQUEST["quantita_5"])?($_REQUEST["quantita_5"]):(1);
        if (!utils::validation('intero', $num)){
            $Errors["quantita_5"] = "Il valore $num non &egrave; di tipo numerico";
        }
        
        $imp5 = str_replace(',','.',$_REQUEST["importo_5"]);
        $valori[] = Array($pratica,$data,$_REQUEST["tipo_5"],$imp5,$_REQUEST["causale_5"],$raggruppamento,$num,$_SESSION["USER_ID"],time());

    }
    else{
        $Errors["importo_5"] = sprintf("Il valore dell\'importo %s non è nel formato corretto (Es. 100,00)",$_REQUEST["importo_5"]);
        if(!trim($_REQUEST["causale_5"])){
            $Errors["causale_5"] = "Campo obbligatorio";
        }
    }
    
    if ($Errors){
        require_once $activeForm;
        exit;
    }
    else{
        
        $sqlQuery= "INSERT INTO ragioneria.importi_dovuti(pratica,scadenza,tipo,importo,causale,codice_richiesta,quantita,uidins,tmsins) VALUES(?,?,?,?,?,?,?,?,?);";
        $stmt = $dbh->prepare($sqlQuery);
        
        for($i=0;$i<count($valori);$i++){
            if(!$stmt->execute($valori[$i])){
                $err = $stmt->errorInfo();
                $Errors["generic"] = $err[2];
                print "<p><b style=\"color:red;\">".$err[2]."</b></p>";
                $query = "DELETE FROM ragioneria.importi_dovuti WHERE pratica=$pratica AND codice_richiesta='$raggruppamento';";
                $dbh->exec($query);
                
                require_once $activeForm;
                exit;
            }
            else{
                
            }
        }
        /* ELIMINO IL RELATIVO RECORD DELLE STAMPE */
        //$sql = "DELETE FROM stp.stampe WHERE pratica=? AND riferimento_record=?";
        $riferimento = sprintf("ragioneria.importi_dovuti.%s",$raggruppamento);
        $sql = "DELETE FROM stp.stampe WHERE pratica=$pratica AND riferimento_record='$riferimento'";
        $stmt=$dbh->prepare($sql);
        //$stmt->execute(Array($pratica,$riferimento));
        $stmt->execute();
        
        $sql = "SELECT lpad(array_to_string(array_agg(cont),''),10,'0') as codtrans FROM (SELECT contatore::varchar as cont FROM ragioneria.importi_dovuti WHERE pratica=? AND codice_richiesta=? ORDER BY cont) A";
        $stmt = $dbh->prepare($sql);
        if($stmt->execute(Array($pratica,$raggruppamento))){
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $codTrans = $res[0]["codtrans"];
            if (utils::validation('data', $_REQUEST["scadenza"])){
                $sql = sprintf("UPDATE ragioneria.importi_dovuti SET codice_richiesta = '$codTrans', data_scadenza='%s'::date, printed=0 WHERE codice_richiesta = '$raggruppamento' and pratica=$pratica;",$_REQUEST["scadenza"]);
            }
            else{
                $sql = "UPDATE ragioneria.importi_dovuti SET codice_richiesta = '$codTrans', printed=0 WHERE codice_richiesta = '$raggruppamento' and pratica=$pratica;";
            }
            if ($codTrans){
                $stmt = $dbh->prepare($sql);
                if(!$stmt->execute()){
                    echo "<p><b>Errore nell'aggiornamento del codTrans $codTrans</b><p>";
                }
            }
            else{
                
            }
        }
        else{
            $err = $stmt->errorInfo();
            print "<p><b>".$err[2]."</b></p>";
        }
        
    }
    if (file_exists(LOCAL_DB."db.pe.importi_dovuti.php")){
        require_once LOCAL_DB."db.pe.importi_dovuti.php";
    }
}
elseif (($_POST["azione"]=="Salva" && $_REQUEST["mode"]=="edit") || ($_POST["azione"]=="Elimina") ){
    $codTrans = $_REQUEST["codice_richiesta"];
    /* ELIMINO IL RELATIVO RECORD DELLE STAMPE */
    $dbh=utils::getDb();
    //$sql = "DELETE FROM stp.stampe WHERE pratica=? AND riferimento_record=?";
    $riferimento = sprintf("ragioneria.importi_dovuti.%s",$codTrans);
    $sql = "DELETE FROM stp.stampe WHERE pratica=$pratica AND riferimento_record='$riferimento'";
    //echo "<p>$sql</p>";
    $stmt=$dbh->prepare($sql);
    //$stmt->execute(Array($pratica,$riferimento));
    $stmt->execute();
    $sql = "UPDATE ragioneria.importi_dovuti SET printed=0 WHERE pratica=$pratica AND codice_richiesta = '$codTrans';";
    //$stmt=$dbh->prepare($sql);
    if(!$dbh->exec($sql)){
        utils::debugAdmin($dbh->errorInfo());
        
    }
    else{
        utils::debugAdmin($sql);
    }
	include_once "./db/db.savedata.php";
    
    
    if($_POST["azione"]=="elimina"){
        $sql = "SELECT lpad(array_to_string(array_agg(cont),''),10,'0') as codtrans FROM (SELECT contatore::varchar as cont FROM ragioneria.importi_dovuti WHERE pratica=? AND codice_richiesta=? ORDER BY cont) A";
        $stmt = $dbh->prepare($sql);
        if($stmt->execute(Array($pratica,$codTrans))){
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $codTrans = $res[0]["codtrans"];
            $sql = "UPDATE ragioneria.importi_dovuti SET codice_richiesta = '$codTrans' WHERE codice_richiesta = '$raggruppamento' and pratica=$pratica;";
            //echo "<p>$sql</p>";
            if ($codTrans){
                $stmt = $dbh->prepare($sql);
                if(!$stmt->execute()){
                    echo "<p><b>Errore nell'aggiornamento del codTrans $codTrans</b><p>";
                }
            }
            else{
                
            }
        }
    }
    
    
    if (file_exists(LOCAL_DB."db.pe.importi_dovuti.php")){
        require_once LOCAL_DB."db.pe.importi_dovuti.php";
    }
    
}
	
$active_form="pe.wspagamenti.php?pratica=$idpratica";
?>
