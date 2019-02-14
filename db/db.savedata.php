<?PHP
// Modulo per la validazione del dato se fila tutto liscio salva e  restituisce is_save_ok=1
//altrimenti crea l'array degli errori
require_once './lib/tabella.class.php';
error_reporting(E_ERROR);
function valida_dati($array_config,$campi_obbligatori){
    //dall'array tratto dal file di configurazione crea l'array campi=>valori validati per il db
    $OK_Save=1;
    $db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
    if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
    //Controllo dei campi obbligatori
    if (isset($campi_obbligatori)){
        foreach($campi_obbligatori as $c){
            if (strlen(trim($_POST[trim($c)]))==0){
                $errors[trim($c)]="Campo Obbligatorio";
                $OK_Save=0;
            }
        }
    }
    for ($i=0;$i<count($array_config);$i++){
        $row_config=$array_config[$i];
        foreach($row_config as  $r)
            $array_def[]=explode(';',$r);
    }
    foreach($array_def as $def){
        $campo=$def[1];
        $tipo=trim($def[3]);
        $val=(!in_array($tipo,Array('select2-int','select2-str','multiselectdb','_multiselectdb','_checkbox')))?(trim($_POST[$campo])):($_REQUEST[$campo]);
        switch ($tipo) {
            case "idriga":
                $val=''; //inutile metterlo nella query
                break;
            case "pratica":
            case "numero_pratica":
                if (strlen(trim($val))>0){
                    $sql="SELECT pratica FROM pe.avvioproc WHERE numero='$val'";
                    if($db->sql_query($sql)){
                        $r=$db->sql_fetchrowset();
                        if (count($r)==0) {
                            $OK_Save=0;
                            $errors[$campo]="La pratica $val non esiste";
                        }
                        else
                            $val="'$val'";
                    }
                }
                else
                    $val="NULL";
                break;
            case "text":
            case "textarea":
            case "richtext":
            case "autosuggest":
                if (strlen($val)>0){
                    if (get_magic_quotes_runtime() or get_magic_quotes_gpc()) {
                        $val="'".addslashes($val)."'";
                    }
                    else{
                        $val="'".addslashes($val)."'";
                    }

                }
				elseif (strlen($val)===0) $val="NULL";
                break;
            case "_checkbox":
            case "_multiselectdb":
            case "select2-str":
                if (!is_array($val)) {
                    $val = "NULL";
                }
                else{
                    $val = sprintf("ARRAY['%s']",implode("','",$val));
                }
                break;
            case "select2-int":
                if (!is_array($val)) {
                    $val = "NULL";
                }
                else{
                    $val = sprintf("ARRAY[%s]",implode(",",$val));
                }
                break;
            case "data":
                $l=strlen($val);
                //primo controllo se i caratteri inseriti sono del tipo corretto
                if (strlen($val)>0 and !ereg ("([0123456789/.-]{".$l."})", $val)){
                    $OK_Save=0;
                    $errors[$campo]="Formato della data non valido $val";
                }
                else{
                    list($giorno,$mese,$anno) = split('[/.-]', $val);
                    //Da Verificare..... il 30 Febbraio 2005 lo prende se scritto come anno-mese-giorno con anno a 2 cifre!!!!! Errore
                    if (strlen($val)>0 and (checkdate((int) $mese,(int) $giorno,(int) $anno))){
                        $val="'".$giorno."/".$mese."/".$anno."'";
                    }
					elseif (strlen($val)>0 and strlen($giorno)>3 and (checkdate((int) $mese,(int) $anno,(int) $giorno))) {
                        $val="'".$anno."/".$mese."/".$giorno."'";
                    }
					elseif (strlen($val)>0 and strlen($giorno)<=2 and (checkdate((int) $mese,(int) $anno,(int) $giorno))) {
                        $OK_Save=0;
                        $errors[$campo]="Data ambigua $val";
                    }
					elseif (strlen($val)>0) {
                        $OK_Save=0;
                        $errors[$campo]="Data non valida $val";
                    }
					elseif (strlen($val)===0) $val="NULL";
                }
                break;
            case "select":
                if ($val) $val="'".addslashes($val)."'";
                break;

            case "multiselectdb":
                if (is_array($val) && count($val)){
                    $val="'".implode(',',$val)."'";
                }
                else
                    $val='';

                break;
            case "selectdb":
            case "selectRPC":
                if ($val==-1) {
                    $OK_Save=0;
                    $errors[$campo]=($campo=="tipo_allegati")?("Impossibile modificare il tipo per allegati. Prima di modicarlo rimuovere tutti gli allegati presenti"):("Errore generico");
                }
				elseif(strlen(trim($val))==0) $val='null';
				elseif(!is_numeric($val)) $val="'".addslashes($val)."'";

            case "elenco":
                break;
            case "valuta":
                //$val=str_replace("","",$val);
                $val=str_replace(".","",$val);
                $val=str_replace(",",".",$val);
                if (strlen($val) and !is_numeric($val)){
                    $OK_Save=0;
                    $errors[$campo]="Dato non numerico";
                }
                else if (strlen($val)==0) $val="0";
                break;
            case "ora":
                $val=str_replace(",",".",$val);
                $val=str_replace(":",".",$val);
                if (strlen($val) and !is_numeric($val)){
                    $OK_Save=0;
                    $errors[$campo]="Dato orario non valido";
                }

                break;
            case "superficie":
                $val=str_replace("mq","",$val);
                $val=(double)str_replace(",",".",$val);
                if (strlen($val) and !is_float($val)){
                    $OK_Save=0;
                    $errors[$campo]="Dato non numerico";
                }
                break;
            case "volume":
            case "numero":
                $val=str_replace(",",".",$val);
                if (strlen($val) and !is_numeric($val)){
                    $OK_Save=0;
                    $errors[$campo]="Dato non numerico";
                }
                //else if (strlen($val)==0) $val=0.00;
                break;
            case "intero":
                if (is_numeric($val)) $val=(int)$val;
                $val=str_replace(","," ",$val);
                if (strlen($val) and !is_numeric($val) and (!ereg("/^[0-9]{1,12}$/"))){
                    $OK_Save=0;
                    $errors[$campo]="Dato non numerico";
                }
                //else if (strlen($val)==0) $val=0.00;
                break;
            case "bool":
                ($val="SI")?($val="'t'"):($val="'f'");
                break;
            case "yesno":
                if ($val=='SI')
                    $val=1;
                else if ($val=='NO')
                    $val=0;
                break;
            case "checkbox":
            case "semaforo":
                if ($val=='on')
                    $val=1;
                else
                    $val=0;
                break;
            case "radio":
                $arvalue=$_POST[$campo];
                break;

        }
        if(($tipo!="button") and ($tipo!="submit"))
            $array_data[$campo]=$val;

    }

    return array("data"=>$array_data,"errors"=>$errors);

}
function valida_campi($arr){
    foreach ($arr as $key=>$value){
        switch($key){
            case "codfis":
                $val=str_replace(" ","",$value);
                break;
            default:
                $val=$value;
                break;
        }
        $ris[$key]=$val;
    }
    return $ris;
}


//MODULO COMUNE PER IL SAVATAGGIO DEI DATI
$azione=$_REQUEST["azione"];
if ($azione=="Annulla")	return;

$config_file=$_POST["config_file"];
$modo=$_REQUEST['mode'];
//**********ERRORE DA GESTIRE UN PO' MEGLIO*/
if (!isset($config_file)){
    echo ("Manca il file di definizione del form, non è possibile continuare");
    exit;
}

$tb=new Tabella($config_file,$modo);
$tabelladb=$tb->tabella_save_db;
$ref_table=$tb->tabella_ref_db;
$campi_obbl=$tb->campi_obbl;
$array_config=$tb->tab_config;

$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$idrow=$_POST["id"];
if (!$idrow) $idrow=$_POST["idriga"]; //utilizzato solo per eliminare dall'iter, da togliere dopo modifica a pe.iter


if ($azione=="Elimina"){
    $sql="delete from $tabelladb where id=$idrow;";
//		echo "<p>$sql</p>";
    print_debug($sql,null,'deletedata');
    $db->sql_query ($sql);
    return;
}

if (in_array($azione,Array("Salva",'Aggiungi'))){

    $array_dati=valida_dati($array_config,$campi_obbl);

    if($array_dati["errors"]){
        //if (DEBUG) echo "<br>".$active_form."<br>";
        $db->sql_close();
        $Errors=$array_dati["errors"];
        //print_array($Errors);
        include $active_form;
        exit;
    }
    //	Validazione Anagrafe Tributaria Soggetti
    if (defined('VALIDATE_ANAGR_TRIB') && VALIDATE_ANAGR_TRIB==1){
        $sql = "SELECT * FROM anagrafe_tributaria.pratiche WHERE pratica= ".$_POST["pratica"];
        $db->sql_query ($sql);
        $ris = $db->sql_fetchrowset();
        if (count($ris)>0){
            require_once LIB."anagr_tributaria.php";
            $Errors = valida_dati_praticaweb($array_dati["data"],$tabelladb);
            if ($Errors){
                include $active_form;
                exit;
            }
        }

    }

    $Dati=valida_campi($array_dati["data"]);



    //I dati sono stati validati costruisco le query di inserimento/aggiornamento
    if ($_POST["mode"]=="edit"){
        //controllo che un altro utente non abbia modificato il record
        //DA SOSTITUIRE CON UN TRIGGER???????
        if (!$tb->table_list){
            $chkret=0;
            $sql="select coalesce(chk,0) as chk from $tabelladb where id=$idrow;";

            $db->sql_query ($sql);
            $chkret = $db->sql_fetchfield("chk");
            if (!($chkret==$_POST["chk"])){
                $Errors["Multiutenza"]= "Un altro utente ha salvato il record, oppure è gia stato salvato.....aggiornare  il form";
                echo "<p style=\"color:red\">Un altro utente ha salvato il record, oppure è gia stato salvato.....aggiornare  il form</p>";
                $db->sql_close();
                include $active_form;
                exit;
            }
            $Dati["chk"]=++$chkret;
            $Dati["uidupd"]=$_SESSION["USER_ID"];
            $Dati["tmsupd"]=time();
        }
        foreach ($Dati as $campo=>$valore){
            if (strlen($valore)>0) $sqlupdate.="$campo=$valore,";
        }
        $sqlupdate=substr($sqlupdate,0,strlen($sqlupdate)-1);
        $sqlupdate="update $tabelladb set $sqlupdate where id=$idrow";
        $sql=$sqlupdate;
//        if($_SESSION["USER_ID"]==1) echo "<p>$sql</p>";
    }


	elseif ($_POST["mode"]=="new") {

        if ($_SESSION["ADD_NEW"]){
            echo "Il record è già stato inserito ".$_SESSION["ADD_NEW"];
            return;
        }
        if(!$tb->table_list){
            if(isset($idpratica))	$Dati["pratica"]=$idpratica;
            $Dati["chk"]=1;
            $Dati["uidins"]=$_SESSION["USER_ID"];
            $Dati["tmsins"]=time();
        }
        else
            $Dati['id']="(SELECT coalesce(max(id),0)+1 FROM $tabelladb)";
        foreach ($Dati as $campo=>$valore){
            if (strlen($valore)>0) {
                $sqlinsertfield.="$campo,";
                $sqlinsertvalues.="$valore,";
            }
        }
        $sqlinsertfield=substr($sqlinsertfield,0,strlen($sqlinsertfield)-1);
        $sqlinsertvalues=substr($sqlinsertvalues,0,strlen($sqlinsertvalues)-1);
        $sqlinsert="insert into $tabelladb ($sqlinsertfield) values ($sqlinsertvalues)";
        $sql=$sqlinsert;

    }
    //echo "<p>$sql</p>";
    print_debug($sql,null,'savedata');
    /*$result = $db->sql_query ($sql);
    $retval="";
    $elenco = $db->sql_fetchrowset();
    $nrighe=$db->sql_numrows();*/
    $dbh=utils::getDb();
    $sth = $dbh->prepare($sql);
    if(!$sth->execute()){

        $arr = $sth->errorInfo();
        if($arr[0]=="23505"){
            $Errors["protocollo"]=addslashes($arr[2]);
            include $active_form;
            exit;
        }
        echo ("ERRORE NEL SALVATAGGIO<p>$sql</p>");
        return;
    }
    //se ho inserito un nuovo valore ricavo l'ultimo id
    if ($_POST["mode"]=="new") {

        $sql=($tb->table_list)?("SELECT max(id) FROM $ref_table"):("select currval ('".trim($ref_table)."_id_seq')");
        //echo "<p>$sql</p>";
        $sth=$dbh->prepare ($sql);
        $sth->execute();
        $lastid=$sth->fetchColumn();
        $_SESSION["ADD_NEW"]=$lastid;
        //print_debug("sessione ho $lastid");
    }

}

?>
