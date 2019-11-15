<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$azione = strtolower($_POST["azione"]);
$dbh = utils::getDb();
//print_r($_REQUEST);
if ($azione == ACTION_SAVE){
    $pr = new pratica($idpratica);
    $elencoDocumenti = $pr->getDocumenti();
	$elencoAllegati = $pr->getAllegati();
    $numero = $pr->info["numero"];
    $documenti = $_REQUEST["oggetto"];
	//$allegatidafirmare = $_REQUEST["oggetto_1"];
    $allegati = $_REQUEST["allegati"];
    $richiestaProt = ($_REQUEST["protocolla"])?('1'):('0');
    $invioComunicazione = ($_REQUEST["invio"])?('1'):('0');
	$object = ($_REQUEST["object"])?($_REQUEST["object"]):("");
	$body = ($_REQUEST["body"])?($_REQUEST["body"]):("");
    if ($richiestaProt == '0') 
        $invioComunicazione = '0';
    else
        $invioComunicazione = '1';
    $destinatari = $_REQUEST["destinatari"];
    $idsDest = implode("','",$destinatari);
    if ($invioComunicazione === '1'){
        if(!$_REQUEST["destinatari"]){
            $array_dati["errors"]["destinatari"] = "Attenzione selezionare almeno un destinatario";
            echo "<p><b style='color:red'>".$array_dati["errors"]["destinatari"]."</b></p>";
        }
        if(!$_REQUEST["allegati"] && !$_REQUEST["oggetto"]){
            $array_dati["errors"]["allegati"] = "Attenzione selezionare almeno un allegato da firmare";
            echo "<p><b style='color:red'>".$array_dati["errors"]["allegati"]."</b></p>";
        }
/*
        if(!$_REQUEST["object"]){
            $array_dati["errors"]["object"] = "Attenzione il campo Oggetto Comunicazione è obbligatorio";
            //echo "<p><b style='color:red'>".$array_dati["errors"]["destinatari"]."</b></p>";
        }
        if(!$_REQUEST["body"]){
            $array_dati["errors"]["body"] = "Attenzione il campo Testo Comunicazione è obbligatorio";
            echo "<p><b style='color:red'>".$array_dati["errors"]["body"]."</b></p>";
        }
*/
        if ($array_dati["errors"]){
            include_once  $_REQUEST["active_form"];
            exit;
        }
    }
    $sql = "SELECT DISTINCT nominativo,pec FROM pe.elenco_destinatari_comunicazione WHERE id IN ('$idsDest')";
//    echo "<p>$sql</p>";
    $stmt = $dbh->prepare($sql);
    if($stmt->execute()){
       $soggetti = $stmt->fetchAll(PDO::FETCH_ASSOC);
       for($i=0;$i<count($soggetti);$i++){
           $nominativi[] = $soggetti[$i]["nominativo"];
           $pec[] = $soggetti[$i]["pec"];
       }
    }

    $data = Array(
        ":datainvio" => date("d/m/Y"),
        ":idutentesrc" => $_SESSION["USER_ID"],
        ":idutentedst" => $_REQUEST["idutentedst"],
        ":oggetto" => "",
        ":pathdocumento" => "",
        ":stato" => FIRMA_STATO_DEFAULT,
        ":numeropratica" => $numero,
        ":destinatarimail" => implode(";",$pec),
        ":nominativilist" => implode(";",$nominativi),
        ":pratica" => $idpratica,
        ":raggruppramentoprotocollo" => ($richiestaProt)?(time()):("")
    );
//print_array($pec);
//print_array($data);
    $sql =<<<EOT
INSERT INTO firma_digitale.documenti
    (datainvio, idutentesrc, idutentedst, oggetto, pathdocumento, stato, numeropratica,destinatarimail,nominativilist, idpratica,raggruppamentoprotocollo)
    VALUES (?, ?, ?, ?, ?, ?,?,?, ?, ?,?);            
EOT;
    // :datainvio, :idutentesrc, :idutentedst, :oggetto, :stato, :numero, :pratica
    $stmt = $dbh->prepare($sql);
    foreach($documenti as $doc){
        $data[":oggetto"] = $elencoDocumenti[$doc];
        $pathDoc = Array(
            "idpratica" => $idpratica,
            "iddocumento" => $doc,
            "tipodocumento" => "1",
            "richiestaprotocollo" => $richiestaProt,
            "inviocomunicazione" => $invioComunicazione,
            "nomedocumento" => $elencoDocumenti[$doc],
            "template" => "invia_pratica_web",
			"object" => htmlentities($object,ENT_COMPAT, 'UTF-8'),
			"body" => htmlentities($body,ENT_COMPAT, 'UTF-8')
        );
		$data[":pathdocumento"] = html_entity_decode(json_encode($pathDoc),ENT_COMPAT, 'UTF-8');
		$text = sprintf("<p>%s</p>",$pathDoc["object"]);
		echo $text;
        if(!$stmt->execute(array_values($data))){
            echo "<p>$sql</p>";
            echo "<pre>";print_r($data);print_r($stmt->errorInfo());echo "</pre>";
        }
    }
    foreach($allegati as $doc){
        $data[":oggetto"] = $object;
		$pathDoc = Array(
            "idpratica" => $idpratica,
            "iddocumento" => $doc,
            "tipodocumento" => "2",
            "richiestaprotocollo" => $richiestaProt,
            "inviocomunicazione" => $invioComunicazione,
            "nomedocumento" => $elencoAllegati[$doc],
            "template" => "invia_pratica_web",
			"object" => htmlentities($object,ENT_COMPAT, 'UTF-8'),
			"body" => htmlentities($body,ENT_COMPAT, 'UTF-8')
        );
		$data[":pathdocumento"] = html_entity_decode(json_encode($pathDoc),ENT_COMPAT, 'UTF-8');
        if(!$stmt->execute(array_values($data))){
            echo "<p>$sql</p>";
            echo "<pre>";print_r($data);print_r($stmt->errorInfo());echo "</pre>";
        }
        //echo "<pre>";print_r($data);echo "</pre>";
    }
    
}
elseif ($azione == ACTION_DELETE) {
    $sql = "DELETE FROM firma_digitale.documenti WHERE iddocumento = ?;";
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute(Array($_REQUEST["id"]))){
        echo "<pre>";print_r($stmt->errorInfo());echo "</pre>";
    }
    
}

$active_form="pe.firma_digitale.php?pratica=$idpratica";
?>
