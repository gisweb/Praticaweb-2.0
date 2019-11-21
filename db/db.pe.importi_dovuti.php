<?php

$pratica = $_REQUEST["pratica"];
$activeForm = $_REQUEST["active_form"];
if($_POST["azione"]=="Salva" && $_REQUEST["mode"]=="new"){
    $raggruppamento = utils::rand_str(16);
    $data = $_REQUEST["data_scadenza"];
    if (!$data) $Errors["data_scadenza"] = "Campo Obbligatorio";
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

        $imp1 = str_replace(',','.',$_REQUEST["importo_1"]);
        $valori[] = Array($pratica,$data,$_REQUEST["tipo_1"],$imp1,$_REQUEST["causale_1"],$raggruppamento,$_SESSION["USER_ID"],time());
        
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
        $imp2 = str_replace(',','.',$_REQUEST["importo_2"]);
        $valori[] = Array($pratica,$data,$_REQUEST["tipo_2"],$imp2,$_REQUEST["causale_2"],$raggruppamento,$_SESSION["USER_ID"],time());

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

        $imp3 = str_replace(',','.',$_REQUEST["importo_3"]);
        $valori[] = Array($pratica,$data,$_REQUEST["tipo_3"],$imp3,$_REQUEST["causale_3"],$raggruppamento,$_SESSION["USER_ID"],time());

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

        $imp4 = str_replace(',','.',$_REQUEST["importo_4"]);
        $valori[] = Array($pratica,$data,$_REQUEST["tipo_4"],$imp4,$_REQUEST["causale_4"],$raggruppamento,$_SESSION["USER_ID"],time());

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
        
        $imp5 = str_replace(',','.',$_REQUEST["importo_5"]);
        $valori[] = Array($pratica,$data,$_REQUEST["tipo_5"],$imp5,$_REQUEST["causale_5"],$raggruppamento,$_SESSION["USER_ID"],time());

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
        $dbh=utils::getDb();
        $sqlQuery= "INSERT INTO ragioneria.importi_dovuti(pratica,data_scadenza,tipo,importo,causale,codice_richiesta,uidins,tmsins) VALUES(?,?,?,?,?,?,?,?);";
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
        $sql = "SELECT lpad(array_to_string(array_agg(cont),''),10,'0') as codtrans FROM (SELECT contatore::varchar as cont FROM ragioneria.importi_dovuti WHERE pratica=? AND codice_richiesta=? ORDER BY cont) A";
        
        $stmt = $dbh->prepare($sql);
        if($stmt->execute(Array($pratica,$raggruppamento))){
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $codTrans = $res[0]["codtrans"];
            $sql = "UPDATE ragioneria.importi_dovuti SET codice_richiesta = '$codTrans' WHERE codice_richiesta = '$raggruppamento' and pratica=$pratica;";
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
}
elseif (($_POST["azione"]=="Salva" && $_REQUEST["mode"]=="edit") || ($_POST["azione"]=="Elimina") ){
	include_once "./db/db.savedata.php";
}
	
$active_form="pe.wspagamenti.php?pratica=$idpratica";
?>
