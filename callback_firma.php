<?php
try
    {
    $dsn_callback = "pgsql:host=10.95.10.27 port=5433 dbname=gw_spezia user=postgres password=postgres";
    $pdo_callback = new PDO($dsn_callback);
    $pdo_callback->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
catch (PDOException $e)
{
    echo "Errore accesso al database";
}

if(isset($_REQUEST["esito"]) and $_REQUEST["esito"] = "ok"){
    $file_doc_signed = $_REQUEST["file"];


    $sql_stampa = "INSERT INTO stp.stampe (pratica, file_doc, file_pdf, utente_doc, data_creazione_doc, utente_pdf, data_creazione_pdf, data_invio, prot_out) VALUES (:pratica, :file_doc, :file_pdf, :utente_doc, :data_creazione_doc, :utente_pdf, :data_creazione_pdf, :data_invio, :prot_out)";


    try{
        $s = $pdo_callback->prepare($sql_stampa);
        $s->bindValue(':pratica', $_REQUEST['idpratica']);
        $s->bindValue(':file_doc', $_REQUEST['file']);
        $s->bindValue(':file_pdf', $_REQUEST['file']);
        $s->bindValue(':utente_doc', $_REQUEST['utente']);
        $s->bindValue(':data_creazione_doc', date("Y-m-d"));
        $s->bindValue(':utente_pdf', $_REQUEST['utente']);
        $s->bindValue(':data_creazione_pdf', date("Y-m-d"));
        $s->bindValue(':data_invio', date("Y-m-d"));
        $s->bindValue(':prot_out', $_REQUEST['protocollo']);

        $s->execute();

    }catch(Exception $ex){
        echo $ex;
    }
    $sql_id_stampa = "SELECT id from stp.stampe where pratica = :pratica and file_doc = :file_doc and data_creazione_doc = :data_creazione_doc";
    try{
        $s = $pdo_callback->prepare($sql_id_stampa);
        $s->bindValue(':pratica', $_REQUEST['idpratica']);
        $s->bindValue(':file_doc', $_REQUEST['file']);
        $s->bindValue(':data_creazione_doc', date("Y-m-d"));
        $s->execute();
        $objectid = $s->fetchColumn();

    }catch(Exception $ex){
        echo $ex;
    }
    $nota_edit = '<img src="images/word.gif" border=0 >&nbsp;&nbsp;<a target="documenti" href="./openDocument.php?id='.$objectid.'&pratica='.$_REQUEST['id_pratica'].'" >'.$file_doc_signed.'</a>';
    $sql_iter = "INSERT INTO pe.iter (pratica, data, nota, utente, nota_edit, stampe, immagine) VALUES (:pratica, :data, :nota, :utente, :nota_edit, :stampe, :immagine )";
    try{
        $s = $pdo_callback->prepare($sql_iter);
        $s->bindValue(':pratica', $_REQUEST['idpratica']);
        $s->bindValue(':data', date("Y-m-d"));
        $s->bindValue(':nota', "Creato il documento firmato");
        $s->bindValue(':utente', $_REQUEST['utente']);
        $s->bindValue(':nota_edit', $nota_edit);
        $s->bindValue(':stampe', $objectid);
        $s->bindValue(':immagine', "word.png");
        $s->execute();

    }catch(Exception $ex){
        echo $ex;
    }
    echo "ok";
}





?>