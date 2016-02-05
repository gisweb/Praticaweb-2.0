<?php


/*
Descrizione della classe e dei metodi


fatta da roberto starnini per praticaweb........

*/
require_once DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."pratica.class.php";
require_once DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."app.utils.class.php";
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
        var $table_list=0;    //TABELLA DI ELENCO o NO
	function Tabella($config_file,$mode='view',$pratica=null,$id=null){
	// ******LETTURA FILE DI CONFIGURAZIONE e impostazione layout della tabella
		$campi=null;
		if (!strpos($config_file,'.tab'))
			$config_file.='.tab';
        $fName=TAB.$config_file;
		if(!file_exists(TAB.$config_file)) echo "<p class='ui-error'>file $fName non esistente</p>";
        $cfg=parse_ini_file(TAB.$config_file,true);
		
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

            $ncol=count($lay['data']);
            $this->mode=$mode;
            $this->debug=null;
            $this->tabelladb=$lay['table'];
            $this->tabella_ref_db=(isset($lay['ref_table']))?($lay['ref_table']):($lay['table']);
            $this->function_prms=(isset($lay['function_prms']) && $lay['function_prms'])?($lay['function_prms']):(null);
            $this->campi_obbl=(isset($lay['campi_obbligatori']) && $lay['campi_obbligatori'])?(explode(';',$lay['campi_obbligatori'])):(null);
            $this->campi_ord=(isset($lay['campi_ordinamento']) && $lay['campi_ordinamento'])?(explode(';',$lay['campi_ordinamento'])):(null);
            $this->num_col=$ncol;

            //$lay=file(TAB.$config_file);
            //$datidb=explode(',',$lay[0]);//prima_riga[0] contiene le info per il db: nome tabella e campi obbligatori 
            //$ncol=count($lay)-1;
            for ($i=0;$i<count($lay['data']);$i++)//comincio da 1 perchÃ¨ sulla prima riga ho il nome della tabella e i campi obbligatori
                    $row[]=explode('|',$lay['data'][$i]);//array di configurazione delle tabelle
            //
            ////estraggo l'elenco dei campi
            for ($i=0;$i<$ncol;$i++){
                    for ($j=0;$j<count($row[$i]);$j++){ //ogni elemento puÃ² avere un numero di elementi arbitrario
                            list($label,$campo,$prms,$tipo)=explode(';',$row[$i][$j]);
                            $tipo=trim($tipo);
                            
                            if (!in_array($tipo,Array("id","pratica","submit","ui-button","button","upload","stampa"))){
                                $campi[]=$campo;
                            }
                    }
            }
            $campi=implode(',',$campi);
            if (isset($lay['button']) && $lay['button']){
                    $btn=explode('|',$lay['button']);
                    for($i=0;$i<count($btn);$i++){
                            @list($button['text'],$button['name'],$prms,$button['type'])=explode(';',$btn[$i]);
                            @list($button['size'],$button['onclick'])=explode('#',$prms);
            $name=strtolower($button['text']);
            $button['width']='80px';
    switch(strtolower($button['text'])){
        case "aggiungi":
         $button['icon']='ui-icon-plus';
         $button['value']='Salva';
         break;
        case "salva":
         $button['icon']='ui-icon-disk';
         $button['value']='Salva';
         break;
        case "avanti":
         $button['icon']='ui-icon-circle-triangle-e';
         $button['value']='Avanti';
         break;
        case "elimina":
         $button['icon']='ui-icon-trash';
         $button['value']='Elimina';
         $button['onclick']='confirmDelete';
         break;
        case "annulla":
         $button['icon']='ui-icon-circle-triangle-w';
         $button['value']='Annulla';
         break;
        case "indietro":
         $button['icon']='ui-icon-circle-triangle-w';
         $button['value']='Indietro';
         break;
        case "chiudi":
         $button['icon']='ui-icon-circle-triangle-w';
         $button['value']='Chiudi';
         break;
	case "cerca":
         $button['icon']='ui-icon-search';
         $button['value']='Cerca';
         break;
       case "voltura":
           $button['icon']='ui-icon-shuffle';
           $button['text']='Sposta in Variazioni';
           $button['onclick']='confirmSpostaVariazioni';
           $button['width']='160px';
           break;
       case "preview":
         $button['icon']='ui-icon-print';
         $button['value']='Stampa';
        default:
         $button['value']=$button['text'];
         break;
				}
				$this->button[$name]=$button;
			}
		}
		
		$this->elenco_campi=$campi;
		$this->tab_config=$row;
		$this->config_file=$config_file;
                $this->idtabella=$id;
		$this->idpratica=($pratica)?($pratica):((isset($_REQUEST["pratica"]))?($_REQUEST["pratica"]):(null));
		$this->current_user=$_SESSION["USERNAME"];
		$this->current_groups=$_SESSION["GROUPS"];
		$this->checkPermission($cfg['general']);
		//echo "<pre>";print_r($this);echo "</pre>";
	}
	
	function get_idpratica(){
		return $this->idpratica;
	}
	
	function set_titolo($titolo,$menu=0,$hidden=0){
		$this->titolo=$titolo;
		if ($menu) $this->button_menu=$menu;
		if ($hidden) $this->array_hidden=$hidden;
	}
	
	function get_titolo($self=SELF,$forceEditBtn=false){
		$hidden=null;
		$mode=null;
		//$self=$_SERVER["PHP_SELF"];
		$pr=$this->idpratica;
		//testo titolo
                
		$titolo=(isset($this->array_dati[$this->curr_record][$this->titolo]))?($this->array_dati[$this->curr_record][$this->titolo]):($this->titolo);//se il titolo Ã¨ dato dal campo 
		//if(!isset($titolo)) $titolo=$this->titolo;//altrimenti il titolo Ã¨ la stringa passata
		//pulsante di menÃ¹
		
		if ($this->editable || $forceEditBtn){
			if (strtolower($this->button_menu)=="modifica"){
				if ($_SESSION["PERMESSI"]<=3 ){
					$mode="edit";		
					$butt=$this->button_modifica;
					$im='ui-icon-pencil';
					$label='Modifica';
				}
			}
			elseif (strtolower($this->button_menu)=="nuovo"){
				if ($_SESSION["PERMESSI"]<=3){
					$mode="new";
					$butt=$this->button_nuovo;
					$im='ui-icon-plusthick';
					$label='Nuovo';
				}
			}
		}
		//$tit=str_replace('_','',$titolo);

		//$riga_titolo="<td width=\"90%\" bgColor=\"".$this->sfondo_titolo."\"><font face=\"Verdana\" color=\"".$this->testo_titolo."\" size=\"2\"><b>".ucfirst(strtolower($titolo))."</b></font></td>";
		$riga_titolo="<td class=\"titolo\">".ucfirst($titolo)."</td>";
		if (isset($butt)){
			//$riga_titolo.="<td><input type=\"image\" src=\"images/$butt\"></td>";
            //$idobj="btn_".$tit."_".$this->idtabella;
            $idobj="btn_".rand();
			$riga_titolo.=<<<EOT
<td>
	<button id='$idobj' class="button_titolo"></button>
	<script>
		jQuery('#$idobj').button({
				icons:{
					primary:'$im'
				},
				label:'$label'
			}).click(function(){
				$('#$idobj').parents('form:first').submit();
			});
		
		
	</script>
</td>
EOT;
		}
		else{
			
		}
	
		//campi nascosti del form
		if (isset($this->array_hidden)){
			//echo "<br>nascosti:";print_r($this->array_hidden);
			foreach ($this->array_hidden as $key=>$value){
				$nome=$key;
				if($value=='')	$value=$this->array_dati[$this->curr_record][$nome];//se non ho passato un valore vado a prenderlo nel record
				$hidden.="<input type=\"hidden\" name=\"$nome\" value=\"$value\">\n\t";
			}
		}
	
		if($this->idpratica) // se ho giÃ  l'id pratica lo passo
			$hidden.="<input type=\"hidden\" name=\"pratica\" value=\"".$this->idpratica."\">";
	

		$tabella=<<<EOT
<table  class=\"printhide\" width=100% >		
	<input type="hidden" name="mode" value="$mode">
	$hidden
	<tr>
		$riga_titolo
	</tr>
</table>
EOT
;	
		//if (isset($mode))
		$tabella_titolo=<<<EOT
<form method="post" target="_parent" action="$self">
	$tabella
</form>
EOT;

		print $tabella_titolo;
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
					$sql=($this->table_list)?("select $this->elenco_campi,id from $this->tabelladb $data $ord"):("select $this->elenco_campi,id,pratica,chk from $this->tabelladb $data $ord");	//aggiungo sempre il campo chk per il controllo della concorrenza
			//echo("<p>$sql</p>");
			//print_debug($this->config_file."\n".$sql,NULL,"tabella");
                    utils::debug(DEBUG_DIR.$_SESSION["USER_ID"]."_".'tabella.debug', $sql);
			if ($this->db->sql_query(trim($sql))){
				$this->array_dati=$this->db->sql_fetchrowset();
				$this->num_record=$this->db->sql_numrows();
			}
			else{
				$this->num_record=0;
				if ($_SESSION["USER_ID"]==1){
					echo "<p>$sql</p>";
				}
			}
			$this->curr_record=0;	
			return  $this->num_record;	
		}
	}
	
	function date_format($stringa_data){
	//formatta la data in giorno-mese-anno
		//if (($stringa_data) && (!$this->error_flag)){	
		if ($stringa_data){
			//$ar=explode("-",$stringa_data);
			$ar= split('[/.-]', $stringa_data);
			$stringa_data=$ar[0]."-".$ar[1]."-".$ar[2];
		}
		return $stringa_data; 
	}
	
	function set_db($db){
		$this->db=$db;
	}
	
	function get_db(){
		if(!isset($this->db)) $this->connettidb();
		return $this->db;
	}
	
	function connettidb(){
		$this->db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
		if(!$this->db->db_connect_id)  die( "Impossibile connettersi al database");
	}
	
	function close_db(){
		if(isset($this->db)) $this->db->sql_close();
	}
	
	function set_tag($mytag){
		$this->tag=$mytag;
	}

/*--------------------------- Funzione che costruisce i bottoni di salvataggio,annulla,elimina --------------------------------*/    
    function set_buttons(){
        if (count($this->button)==0) return '';
        $buttons="<div class=\"button_line\"></div>\n";
        $buttons.='<input id="btn_azione" name="azione" value="" type="hidden"/>';
        if ($this->mode=='new'){
            unset($this->button['elimina']);
            unset($this->button['voltura']);
        }
        
    
        if (in_array($this->mode,Array('edit','new','addnew','search'))){
            foreach($this->button as $key=>$v){
                extract($v);
                $idbtn="azione-".strtolower($key);
                if ($type=='submit'){
                    $check=($onclick)?("$onclick(this)"):('true');
                    $buttons.= <<<EOT
            
    <span id="$idbtn" style=""></span>
    <script>
        $("#$idbtn").button({
            icons: {
                primary: "$icon"
            },
            label:"$text"
        }).click(function(){
            if ($check){
                $(this).parents('form:first').append('<input type="hidden" name="azione" value="$text"/>');
                $(this).parents('form:first').submit();
            }
        });
    </script>
EOT;
                }
                else
                    $buttons.= <<<EOT
    
    <button id="$idbtn"></button>
    <script>
        $("#$idbtn").button({
            icons: {
                primary: "$icon"
            },
            label:"$text"
        }).click(function(){
            $onclick(this);
            $(this).parents('form:first').append('<input type="hidden" name="azione" value="$text"/>');
            $(this).parents('form:first').submit();
        });
    </script>
EOT;
            }
        }
        elseif($this->mode=='view'){
            foreach($this->button as $key=>$v){
                extract($v);
                $idbtn="btn_".strtolower($key);
                switch($key){
                    case "preview":
                        $buttons.=<<<EOT
        <button id="$idbtn"></button>
        <script>
            $(document).ready(function(){
                $('#$idbtn').button({
                    icons:{
                        primary:'$icon'	
                    },
                    label:'$text'
                }).click(function(){
                    $('#divPreview').dialog({
                        title:'Stampa di un documento di prova',
                        show: "blind",
                        hide: "explode",
                        height:400,
                        width:600,
                        modal:true
                    });
                });
            });
        </script>
EOT;
                        break;
                    case "chiudi":
                    case "annulla":
                    case "indietro":
                        $pratica=$this->idpratica?$this->idpratica:'null';
                        $buttons.=<<<EOT
        <button id="$idbtn"></button>
        <script>
            $('#$idbtn').button({
                icons:{
                    primary:'$icon'	
                },
                label:'$text'
            }).click(function(){
                 linkToList('$onclick',{'pratica':$pratica});
            });
        </script>
EOT;
                    break;
                default:
                    $buttons.=<<<EOT
        <button id="$idbtn"></button>
EOT;
                    break;
                    
                }
            }
            
        }
        
        return $buttons;
        
    }	
/*----------------------------------------------------------------------------------------*/
/*-------------------------------Verifica dei permessi della pratica----------------------*/
	function checkPermission($cfg){
		/*TODO   AUTORIZZAZIONE NON SUI GRUPPI MA SUI RUOLI*/
		
		if($_SESSION["PERMESSI"]<2 ) {
			$this->editable = true;
			$this->viewable = true;
			return;
		}
		
		$db = $this->get_db();
		$dsn = sprintf('pgsql:dbname=%s;host=%s;port=%s',DB_NAME,DB_HOST,DB_PORT);
        $conn = new PDO($dsn, DB_USER, DB_PWD);
		list($schema,$table)=explode('.',$this->tabelladb);
		switch($schema){
			case "oneri":
			case "ragioneria":
			case "stp":
				$sql = "SELECT role FROM pe.ruoli_pratica WHERE pratica=? and userid=?;";
				break;
			default:		//Caso delle pratiche Edilizie
                $sql = "SELECT role FROM $schema.ruoli_pratica WHERE pratica=? and userid=?;";

			break;
		}
        
        $stmt = $conn->prepare($sql);
		if(!$stmt->execute(Array($this->idpratica,$_SESSION["USER_ID"]))){
			$err = $conn->errorInfo();
		}
		$roles = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        //print_array($roles);
		
		/*Verifica della visibilità delle pratica*/
		if (!$cfg['viewable'] || $_SESSION["PERMESSI"]< 4 || ALWAYS_VIEWABLE==1){
			$this->viewable=true;
		}
		else{
			$vroles=explode(';',$cfg['viewable']);
			if (count(array_intersect($vroles,$roles)) > 0){
				$this->viewable=true;
			}
			else{
				$this->viewable=false;
			}
		}
        if ($_SESSION["PERMESSI"]>= 4){
			$this->editable=false;
		}        
		elseif ((!$cfg['editable']) || ALWAYS_EDITABLE==1){
			$this->editable=true;
		}
		else{
			$stdRoles=Array("dirigente","rds","ruo","rdp");
			$groles=array_merge(explode(';',$cfg['editable']),$stdRoles);
			print_array($groles);
			print_array($roles);
			if (((count(array_intersect($groles,$roles))>0)) ){
				$this->editable=true;
			}
			else{
                $this->editable=false;
			}
		}
		//print_array(Array("VIEWABLE"=>(string)$this->viewable,"EDITABLE"=>(string)$this->editable));
	}
	function print_titolo(){
		print "<div class=\"titolo\" style=\"width:90%\">".ucfirst(strtolower($this->titolo))."</div>";
	}
    
    function getParams($row,$w){
        $params=Array();
        $params['id']=$this->array_dati[$row]["id"];
        $params['pratica']=$this->idpratica;
        $prms=explode('#',$w);
        $size=array_shift($prms);
        $form=array_shift($prms);
        for($i=0;$i<count($prms);$i++){
            $params[$prms[$i]]=$this->array_dati[$row][$prms[$i]];
        }
        if (isset($this->params))
            foreach($this->params as $k=>$v){
                $params[$k]=$v;
            }
        
        return Array("size"=>$size,"form"=>$form,"params"=>$params);
    }
	
	function getHTML5Attr($html5Data){
        $d=explode('#',$html5Data);
        for($k=0;$k<count($d);$k++){
			list($key,$v)=explode('=',$d[$k]);
			if(strpos($v, '@')===0){
				$html5Attr[]=sprintf('%s="%s"',$key,$this->array_dati[$nriga][str_replace('@', '', $v)]);
			}
			else{
				$html5Attr[]=sprintf('%s="%s"',$key,$v);
			}
		}
		$html5Attr=implode(" ",$html5Attr);
        return $html5Attr;
	}
	
function elenco_stampe ($form){
//elenco degli elaborati in modo vista: solo i pdf
	if ($_SESSION["PERMESSI"]>3) return;
	$icona_pdf="images/acrobat.gif";
	$icona_rtf="images/word.gif";
	$procedimento=$this->array_dati[$this->curr_record]["id"];		
	$sql="select id,file_doc,file_pdf,utente_pdf from stp.stampe where (pratica=$this->idpratica) and (form='$form') and ((char_length(file_doc)>0 or (char_length(file_pdf)>0)));";
	if ($this->debug) echo ("<p>$sql</p>");
	
	if (!$this->db) $this->connettidb();
	$this->db->sql_query($sql);
	$elenco = $this->db->sql_fetchrowset();
	$nrighe=$this->db->sql_numrows();
	//$hostname=$_SERVER["HTTP_HOST"];
       $sql="select e_tipopratica.nome as tipo from pe.avvioproc left join pe.e_tipopratica on (avvioproc.tipo=e_tipopratica.id) where pratica=$this->idpratica";
       $this->db->sql_query($sql);
       $tipo_pratica=$this->db->sql_fetchfield("tipo");
	$form=($form)?($form):($this->printForm);
	list($schema,$f)=explode(".",$form);
		$tabella="
			<hr>
			<form method=\"post\" target=\"_parent\" action=\"stp.stampe.php\">
				<input type=\"hidden\" name=\"form\" value=\"$form\">
				<input type=\"hidden\" name=\"procedimento\" value=\"$procedimento\">
				<input type=\"hidden\" name=\"pratica\" value=\"$this->idpratica\">
                            <input type=\"hidden\" name=\"tipo_pratica\" value=\"$tipo_pratica\">
                           

				<table class=\"stiletabella\" width=\"90%\" border=0>
					<tr>
						<td align=\"right\" valign=\"bottom\">
							<input type=\"image\" src=\"images/printer_edit.png\" alt=\"Modifica elaborati\">
						</td>
					</tr>
				</table>
			</form>";  

		return $tabella;
	}

	
	
}//end class

?>	
