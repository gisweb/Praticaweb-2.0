<?php
    include_once("login.php");
?>

<html>
<head>
    <title>Invia e firma</title>
</head>
<body>
<h2>Conferma invio e firma pratica</h2>
<?php

    try
    {
        $dsn_firma = "pgsql:host=10.95.10.27 port=5433 dbname=gw_spezia user=postgres password=postgres";
        $pdo_firma = new PDO($dsn_firma);
        $pdo_firma->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e)
    {
        echo "Errore accesso al database";
    }
/**
 * ACCODA A FIRMA - INIZIO
 */
    if(isset($_REQUEST['accoda_firma']) and $_REQUEST['accoda_firma']=="true"){

       $next_mail = true;
       $pec_mail_list = "";
        $nominativi_list = "";
       $pec_index = 1;

        while($next_mail){
            if(isset($_REQUEST["mail_soggetto_".$pec_index]) and $_REQUEST["mail_soggetto_".$pec_index] != ""){
                $temp_array = explode(';', $_REQUEST["mail_soggetto_".$pec_index]);
                if($pec_index > 1){
                    $pec_mail_list = $pec_mail_list.", ";
                    $nominativi_list = $nominativi_list.", ";
                }
                $pec_mail_list = $pec_mail_list.$temp_array[0];
                $nominativi_list = $nominativi_list.$temp_array[1];
                $pec_index = $pec_index +1;
            }else{
                $next_mail = false;
            }
        }

        if(isset($_REQUEST["altri_soggetti_list"]) and $_REQUEST["altri_soggetti_list"] != ""){
            if($nominativi_list !=""){
                $nominativi_list = $nominativi_list.", ".$_REQUEST["altri_soggetti_list"];
            }else{
                $nominativi_list = $_REQUEST["altri_soggetti_list"];
            }
            /*if($nominativi_list ==""){
                $nominativi_list = $_REQUEST["richiedenti"];
            }
            if($nominativi_list ==""){
                $nominativi_list = $_REQUEST["proprietari"];
            }*/

        }


        if(isset($_REQUEST["altre_pec_list"]) and $_REQUEST["altre_pec_list"] != ""){
            if($pec_mail_list !=""){
                $pec_mail_list = $pec_mail_list.", ".$_REQUEST["altre_pec_list"];
            }else{
                $pec_mail_list = $_REQUEST["altre_pec_list"];
            }
            /*if($nominativi_list ==""){
                $nominativi_list = $_REQUEST["richiedenti"];
            }
            if($nominativi_list ==""){
                $nominativi_list = $_REQUEST["proprietari"];
            }*/

        }

        $sql_documento = "SELECT * from stp.stampe where id = :id";

        try{
            $s = $pdo_firma->prepare($sql_documento);
            $s->bindValue(':id', $_REQUEST['id_doc']);
            $s->execute();
            $documentoDati = $s->fetchAll();

        }catch(Exception $ex){
            echo "Errore accesso al database";
        }

        $sql_nomeutente = "SELECT username from admin.users where userid = :userid";

        try{
            $s = $pdo_firma->prepare($sql_nomeutente);
            $s->bindValue(':userid', $_SESSION["USER_ID"]);
            $s->execute();
            $username_sessione = $s->fetchColumn();

        }catch(Exception $ex){
            echo "Errore accesso al database";
        }

        $sql_pratica = "SELECT * from pe.avvioproc where pratica = :pratica";
        try{
            $s = $pdo_firma->prepare($sql_pratica);
            $s->bindValue(':pratica', $_REQUEST['id_pratica']);
            $s->execute();
            $praticaDati = $s->fetchAll();
        }catch(Exception $ex){
            echo "Errore accesso al database";
        }



        try{
            $urlsoap = "http://10.95.10.42/wsFirmaDigitale.asmx?WSDL";

            $client = new SoapClient($urlsoap, array('exceptions' => 0, 'cache_wsdl' => 0));

            $param->mail_list = $pec_mail_list;

            $param->file_doc = $documentoDati[0]["file_doc"];
            $param->anno = $praticaDati[0]["anno"];
            $param->numero = $praticaDati[0]["numero"];
            $param->userid = $_SESSION["USER_ID"];
            $param->username = $username_sessione;
            $param->nominativi_list = $nominativi_list;
            $param->idpratica = $_REQUEST['id_pratica'];
            $param->oggetto = $_REQUEST['oggetto_invio'];

            $result = $client->accodaFirma($param)->accodaFirmaResult;


            if(isset($result) and $result == "OK"){
                echo '<h4>Documento correttamente accodato per la firma</h4>';
            }else{
                echo '<h4>Errore durante la trasmissione con il server di firma</h4>';
            }

        }catch(Exception $ex){
            echo $ex;
        }

        /**
         * ACCODA A FIRMA - FINE
         */
    }else{
        /**
         * DEFAULT
         */
        echo '<form action="invia_firma.php" id="invia_firma" name="invia_firma">
                <input type="hidden" id="accoda_firma" name="accoda_firma" value="true">
                <input type="hidden" id="id_doc" name="id_doc" value="'.$_REQUEST['iddoc'].'">
                <input type="hidden" id="id_pratica" name="id_pratica" value="'.$_REQUEST['idpratica'].'">';


        $sql_soggetti = "SELECT * from pe.soggetti where pratica = :pratica  and pec <> ''";

        try{
            $s = $pdo_firma->prepare($sql_soggetti);
            $s->bindValue(':pratica', $_REQUEST['idpratica']);
            $s->execute();
            $soggettiDati = $s->fetchAll();

        }catch(Exception $ex){
            echo "Errore accesso al database";
        }

        $n_chk = 1;
        $proprietari = "";
        $richiedenti = "";
        foreach($soggettiDati as $soggetto){
            if($soggetto['proprietario'] == 1){
                $nominativo_temp = ((isset($soggetto["ragsoc"]) and $soggetto["ragsoc"] != "") ? $soggetto["ragsoc"] : $soggetto["cognome"].' '.$soggetto["nome"]);
                if($proprietari != ""){
                    $proprietari = $proprietari." - ";
                }
                $proprietari = $proprietari.$nominativo_temp;
            }
            if($soggetto['richiedente'] == 1){
                $nominativo_temp = ((isset($soggetto["ragsoc"]) and $soggetto["ragsoc"] != "") ? $soggetto["ragsoc"] : $soggetto["cognome"].' '.$soggetto["nome"]);
                if($richiedenti != ""){
                    $richiedenti = $richiedenti." - ";
                }
                $richiedenti = $richiedenti.$nominativo_temp;
            }
            $ruolo_tmp = "(";
            $ruolo_tmp = $ruolo_tmp.($soggetto['proprietario'] == 1 ? 'proprietario, ' : '');
            $ruolo_tmp = $ruolo_tmp.($soggetto['richiedente'] == 1 ? 'richiedente, ' : '');
            $ruolo_tmp = $ruolo_tmp.($soggetto['concessionario'] == 1 ? 'concessionario, ' : '');
            $ruolo_tmp = $ruolo_tmp.($soggetto['progettista'] == 1 ? 'progettista, ' : '');
            $ruolo_tmp = $ruolo_tmp.($soggetto['esecutore'] == 1 ? 'esecutore, ' : '');
            $ruolo_tmp = $ruolo_tmp.($soggetto['sicurezza'] == 1 ? 'sicurezza, ' : '');
            $ruolo_tmp = $ruolo_tmp.($soggetto['collaudatore'] == 1 ? 'collaudatore' : '');
            $ruolo_tmp = trim(strrev(implode(strrev(""), explode(strrev(","), strrev($ruolo_tmp), 2)))).")";
            $ruolo_tmp = ($ruolo_tmp != "()" ? $ruolo_tmp : '');
            $nominativo = ((isset($soggetto["ragsoc"]) and $soggetto["ragsoc"] != "") ? $soggetto["ragsoc"] : $soggetto["cognome"].' '.$soggetto["nome"]);
            echo'<input type="checkbox" name="mail_soggetto_'.$n_chk.'" value="'.$soggetto["pec"].";".$nominativo.'">'.$nominativo.' '.$ruolo_tmp.'<br>';
            $n_chk = $n_chk +1;
        }
        //echo'<input type="hidden" name="proprietari" value="'.$proprietari.'">';
        //echo'<input type="hidden" name="richiedenti" value="'.$richiedenti.'">';
        if($n_chk == 1){
            echo '<h4 style="margin-right: 3em">Non sono presenti soggetti con indirizzo pec a cui inviare il file firmato, utilizzare i campi sottostanti</h4>';
        }else{
            echo '<br>';
        }
        echo'<label for="oggetto_invio">Oggetto</label>';
        echo'<textarea id = "oggetto_invio" name = "oggetto_invio" rows="3" cols="65" required></textarea><br>';
        echo'<label for="altri_soggetti_list">Inserire i nominativi dei soggetti aggiuntivi a cui inviare il file firmato (separati di virgole)</label>';
        echo'<textarea id = "altri_soggetti_list" name = "altri_soggetti_list" rows="6" cols="65"></textarea><br>';
        echo'<label for="altre_pec_list">Inserire gli indirizzi pec aggiuntivi a cui inviare il file firmato (separati di virgole)</label>';
        echo'<textarea id = "altre_pec_list" name = "altre_pec_list" rows="6" cols="65"></textarea><br>';
        echo'<br><input style="float: right; margin-right: 3em" type="submit" value="Invia">';
        echo'</form>';
        /**
         * DEFAULT - FINE
         */

    }
?>


</body>
</html>
