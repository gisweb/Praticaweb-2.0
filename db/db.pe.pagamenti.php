<?php

if (($_REQUEST["azione"]=="Salva") || ($_REQUEST["azione"]=="Elimina") ){
	include_once "./db/db.savedata.php";

    $db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);


    foreach ($_POST as $key => $value) {
        if (substr($key,0,3)=="id_") {
            $idpag = str_replace("id_","",$key);

            $codpag="";
            $datapag="";
            $modalita="";

            $codpag = $_POST['codice_pagamento'];
            $datapag = $_POST['data_pagamento'];
            $modalita = $_POST['modalita'];

            if ($codpag=="") $codpag="null";
            if ($datapag=="") $datapag="null";

            $sql = "UPDATE ragioneria.pagamenti set modalita=$modalita, codice_pagamento=$codpag, data_pagamento='$datapag'::date WHERE id=$idpag;";
            $db->sql_query($sql);

            //$file = 'debug.txt';
            //$current = file_get_contents($file);
            //file_put_contents($file, $sql);
        }

    }



    //$ris = $db->sql_fetchrowset();
    //if(count($ris)>0) $ret="<BR><BR>Imposta data e codice pagamento per altri pagamenti:<BR><BR>";
    //for($i=0;$i<count($ris);$i++) {


    //}

    //$file = 'debug.txt';
    //$current = file_get_contents($file);
    //file_put_contents($file, print_r($_POST, true));
    //file_put_contents($file, $_POST['myidpag']." ".$_POST['id_6'].$_POST['data_pagamento'].$_POST['codice_pagamento']);
}

$active_form="pe.wspagamenti.php?pratica=$idpratica";