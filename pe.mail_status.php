<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once("login.php");
include "./lib/tabella_h.class.php";

$tabpath="pe";
//print_array($_REQUEST);
$idpratica=$_REQUEST["pratica"];
$modo='view';
$today=date('j-m-y'); 
$titolo="Stato delle Comunicazioni";
$pr=new pratica($idpratica);


//Imposto i permessi di default per il modulo
$_SESSION["PERMESSI"]=$_SESSION["PERMESSI_$idpratica"];

/**/
$banner = "Stato delle Comunicazioni in Uscita";
$formaction = "pe.firma_digitale.php";
?>

<html>
<head>
<title>Mail Status - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php
	utils::loadJS();
	utils::loadCss();
?>
</head>
<body>
<?php
    //-<<<<<<<<<<<<<<<<<<<<<< VISUALIZZA DOCUMENTI DA FIRMARE >>>>>>>>>>>>>>>>>>>>>>>>>>>----------------------->	
        $tabella=new tabella_h("$tabpath/mail_status",$modo);
        $sql =<<<EOT
(select distinct pratica, format('%s|iol_praticaweb',foreign_id) as key, data_protocollo as data from pe.istanze where coalesce(pratica,0)<>0 and pratica = ?)
UNION ALL
(select distinct idpratica as pratica,format('%s_%s',idpratica,raggruppamentoprotocollo) as key,firma as data from firma_digitale.documenti where idpratica=? and coalesce(raggruppamentoprotocollo,'') <> '')

order by pratica,data        
EOT;
        
        $stmt = $tabella->dbh->prepare($sql);
        $stmt->execute(Array($idpratica,$idpratica));
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once LOCAL_LIB."wsclient.mail.class.php";
        for($i=0;$i<count($res);$i++){
            //DA FARE CHIAMATA A WS SIMONE PER ACCETTAZIONE E CONSEGNA PEC IN BASE A PRATICA_RAGGRUPPAMENTOPROTOCOLLO
            $objId = $res[$i]["key"];
            $rr = wsClientMail::getInfoPEC($objId);
            if (count($rr)){
                $acc = ($rr[0]["Accettazione"])?($rr[0]["Accettazione"]):(" --- ");
                $cons = ($rr[0]["Consegna"])?($rr[0]["Consegna"]):(" --- ");
            }
            else{
                $acc = " --- ";
                $cons = " --- ";
            }    
            $regexp = "/([0-9\-]+)T([0-9:]+)\./";
            preg_match($regexp,$rr[0]["DataOra"],$result);
            //print_array(count($result));
            $res[$i]["data"] = (count($result)==0)?(substr(str_replace('T',' ',$rr[0]["DataOra"]),0,-3)):($result[1]." ".$result[2]);
            $res[$i]["oggetto"] = $rr[0]["OggettoFiltrato"];
            $res[$i]["destinatari"] = str_replace(';','<br>',$rr[0]["Destinatario"]);
            $res[$i]["consegna"] = $cons;
            $res[$i]["accettazione"] = $acc;
            //$dd = json_decode($res[$i]["pathdocumento"],TRUE);
            //$res[$i]["nomedocumento"] = $dd["nomedocumento"];
            
        }
        
        $tabella->set_dati($res,'list');	
        $nrec = count($res);
        $tabella->set_titolo($titolo,"");
        echo "\t\t<H2 class='blueBanner'>$banner</H2>";

?>			

        <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
            <TR> 
                <TD> 
                <!-- contenuto-->
<?php

        $tabella->get_titolo();
        if ($nrec)	
            $tabella->elenco();
        else
            print ("<p><b>Nessun Comunicazione Inviata</b></p>");			
?>
                <!-- fine contenuto-->
                 </TD>
            </TR>
        </TABLE>	
</body>
</html>



