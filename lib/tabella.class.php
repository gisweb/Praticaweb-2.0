<?php
/*
Descrizione della classe e dei metodi


fatta da roberto starnini per praticaweb........

*/
class Tabella{

	// costanti che definiscono i file immagine
    var $button_nuovo="nuovo_btn2.gif";
    var $button_modifica="modifica_btn2.gif";
    var $testo_titolo="#FFFFFF";
    var $sfondo_titolo="#728bb8";
    var $stile="stiletabella";

    var $idpratica;
    var $titolo; //stringa del titolo puo essere il titolo esplicito o il nome del campo che contiene il titolo
    var $button_menu;//pulsante da inserire nella riga di intestazione della tabella "nuovo" o "modifica"
    var $array_hidden;//array con l'elenco dei campi nascosti

    var $array_dati;//array associativo campo=>dato con i dati da visualizzare
    var $num_record;//numero di record presenti in array_dati
    var $curr_record;//bookmark al record corrente di array_dati

    var $config_file;//file di configurazione del form
    var $tabelladb; //nome della tabella o vista sul db dalla quale estraggo i dati
    //var $campi_obb; // array con l'elenco dei campi obbligatori (non serve qui)
    var $tab_config; //vettore che definisce la configurazione della tabella. La dimensione corrisponde al numero di righe per le tabelle H o al numero di colonne per le tabelle V
                                     //ogni elemento Ã¨ un vettore con un elemento per la tabella V e un numero di elementi pari al numero di campi sulla stessa riga per le tabelle H 
    var $num_col; // numero di colonne di tab_config
    var $elenco_campi;//elenco dei campi per la select 

    var $elenco_modelli;//elenco dei modelli di stampa da proporre nel form separati da virgola(posso non mettere nulla e lasciare all'utente ogni volta libera scelta)

    var $db;//puntatore a connessione a db da vedere se usare classe di interfaccia.....
    var $current_user;	//Utente attualmente connesso
    var $current_groups;	//Gruppi ai quali appartiene l'utente corrente
    var $button;
    var $table_list=0;	//TABELLA DI ELENCO o NO
    function Tabella($config_file,$mode='view',$pratica=null,$id=null){
    // ******LETTURA FILE DI CONFIGURAZIONE e impostazione layout della tabella
        $campi=null;
        if (!strpos($config_file,'.json')) $config_file.='.json';
        $fName=TAB.$config_file;
        if(!file_exists(TAB.$config_file)) echo "<div class='alert alert-danger'>".message::getMessage("file-not-found",TAB.$config_file)."</div>";
        $f=fopen(TAB.$config_file,'r');
        $cfgFile=fread($f,filesize(TAB.$config_file));
        $cfg=json_decode($cfgFile,true);
        if (!in_array($mode,array_keys($cfg))){
                if ($mode=='new' && in_array('edit',array_keys($cfg)))
                        $lay=$cfg['edit'];
                else
                        $lay=$cfg['standard'];
        }
        else
            $lay=$cfg[$mode];
        $this->table_list=(isset($cfg['general']['table_list']) && $cfg['general']['table_list'])?(1):(0);
        $this->printForm = (isset($cfg['general']['print_form']) && $cfg['general']['print_form'])?($cfg['general']['print_form']):(null);

        $ncol=count($lay['rows']);
        $this->mode=$mode;
        $this->debug=null;
        $this->tabelladb=(isset($lay["table"]))?($lay["table"]) :($cfg['general']['table']);
        //$this->function_prms=(isset($lay['function_prms']) && $lay['function_prms'])?($lay['function_prms']):(null);
        $this->campi_obbl=(isset($lay['mandatory-field']) && $lay['mandatory-field'])?(explode(';',$lay['mandatory-field'])):(null);
        $this->campi_ord=(isset($lay['order-field']) && $lay['order-field'])?(explode(';',$lay['order-field'])):(null);
        $this->num_col=$ncol;
        /*$this->viewable=(isset($lay['viewable']))?($lay["viewable"]) :($cfg['general']['viewable']);
        $this->editable=(isset($lay["editable"]))?($lay["editable"]):($cfg['general']['editable']);*/
        $this->viewable=1;
        $this->editable=1;
        //$lay=file(TAB.$config_file);
        //$datidb=explode(',',$lay[0]);//prima_riga[0] contiene le info per il db: nome tabella e campi obbligatori 
        //$ncol=count($lay)-1;
        //for ($i=0;$i<count($lay['rows']);$i++)//comincio da 1 perchÃ¨ sulla prima riga ho il nome della tabella e i campi obbligatori
                        //$row[]=explode('|',$lay['data'][$i]);//array di configurazione delle tabelle
        //
        $row=$lay["rows"];
                    ////estraggo l'elenco dei campi
                    /*for ($i=0;$i<$ncol;$i++){
                                    for ($j=0;$j<count($row[$i]);$j++){ //ogni elemento puÃ² avere un numero di elementi arbitrario
                                                    list($label,$campo,$prms,$tipo)=explode(';',$row[$i][$j]);
                                                    $tipo=trim($tipo);
                                                    if (($campo!="id") and ($campo!="pratica") and ($tipo!="submit") and ($tipo!="button") and ($tipo!="upload"))
                                                                    ($campi)?(($campo)?($campi.=",".$campo):($campi)):($campi=$campo);

                                    }
                    }
                    

            */
            $this->tab_config=$row;
            $this->config_file=$config_file;
            $this->idtabella=$id;
            $this->idpratica=($pratica)?($pratica):((isset($_REQUEST["pratica"]))?($_REQUEST["pratica"]):(null));

            //echo "<pre>";print_r($this);echo "</pre>";
    }

    function get_idpratica(){
            return $this->idpratica;
    }

    
    // >>>>>>>>>>>>>>>>>>>>>>>>>ATTENZIONE OGNI TABELLA DEVE AVERE I CAMPI ID PRATICA E CHK<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    function set_dati($data=0,$mode=null){
        //se passo un array questo Ã¨ l'array di POST altrimenti Ã¨ il filtro - per default filtra su idpratica se settato
        if (is_array($data)){		
            $this->array_dati=array(0=>$data);
            $this->num_record=1;
            $this->curr_record=0;
        }
        else{
            $data=($data)?("where $data"):("");
            $ord='';
            if (isset($this->campi_ord) && $this->campi_ord) $ord= " ORDER BY ".implode(',',$this->campi_ord);
            if (!isset($this->db)) $this->connettidb();
            $tb=$this->tabelladb;
            if (strpos($tb,"()") > 0) {
                $tb=str_replace("()","",$tb);
                $sql="select * from $tb($this->idpratica) $data $ord";
            }
            else
                $sql="select * from $this->tabelladb $data $ord";
            //echo("<p>$sql</p>");
            utils::debug("tabella",$this->config_file."\n".$sql);
            $stat=$this->db->prepare(trim($sql));
            if ($stat){
                $stat->execute();
                $this->array_dati=$stat->fetchAll();
                $this->num_record=count($this->array_dati);
            }
            else
                    $this->num_record=0;
            $this->curr_record=0;	
            return  $this->num_record;	
        }
    }

    function getHTML5Attr($cfg){
        return "";
    }

    function set_db($db){
            $this->db=$db;
    }

    function get_db(){
            if(!isset($this->db)) $this->connettidb();
            return $this->db;
    }

    function connettidb(){
        $this->db=utils::getDoctrineDB();
    }
}//end class

?>	
