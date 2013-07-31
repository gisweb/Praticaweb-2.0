<?
//IMPORATNTE : Nei documenti non si può utilizzare il tag <span> perchè è dedicato ai campi unione!!!!!!
class stampe {
	
/*CAMPI DELLA CLASSE*/
	var $idmodello;//Nome del modello del documento
	var $modello;//Nome del modello del documento
	var $documento;//Percorso+Nome del file di output
	var $nome_doc;//Nome del file di output in formato HTML
	var $nome_pdf;//Nome del file di output in formato PDF
	var $pratica;
	var $head;//Intestazione del documento
	var $body;// Testo del documento
	var $cicli;//Tabella con tutti le parti che devono essere ripetute (campi uno a molti)
	var $se;//Tabella con tutti le parti che devono essere scritte se è presente il campo obbligatorio
	var $valori;//Tabella con tutti tutti i valori (campi uno a unoi)
	var $errors;//Tabella con gli errori riscontrati
	var $path_in=MODELLI;//path
	var $path_out=STAMPE;
	var $ciclo;
	var $tag;
	var $finali; //Terminatore del campo da ripetere
	var $funzioni;
	var $viste;
	var $campi;
	var $campi_funz;
	var $obbligatori;//Campi obbligatori
	var $obbligatori_funz;
	var $dati;
	var $overwrite;
	var $db;
	//var $regexp_cicli='|<span style="font-size: 11px; font-family: Verdana, Geneva, Arial, sans-serif; background-color: yellow">IN_CICLO</span>(.+)<span style="font-size: 11px; font-family: Verdana, Geneva, Arial, sans-serif; background-color: yellow">FI_CICLO</span>|Umi';
	//var $regexp_if='|<span style="font-size: 11px; font-family: Verdana, Geneva, Arial, sans-serif; background-color: red">INIZIO_SE</span>(.+)<span style="font-size: 11px; font-family: Verdana, Geneva, Arial, sans-serif; background-color: red">FINE_SE</span>|Umi';
	//var $regexp_tag='|<span style="font-size: 11px; font-family: Verdana, Geneva, Arial, sans-serif; background-color: green">(.+)</span>|Umi';
	//var $regexp_tag_obbl='|<span style="font-size: 11px; font-family: Verdana, Geneva, Arial, sans-serif; background-color: red">(.+)</span>|Umi';
	var $regexp_cicli='|<span class="iniziocicli">IN_CICLO</span>(.+)<span class="finecicli">FI_CICLO</span>|Umi';
	var $regexp_if='|<span class="iniziose">INIZIO_SE</span>(.+)<span class="finese">FINE_SE</span>|Umi';
	//var $regexp_tag='|<span class="valore">(.+)</span>|Umi';
	var $regexp_tag_obbl='|<span class="obbligatori">(.+)</span>|Umi';
	
	var $regexp_tag='|\$[{](.+)[}]|Umi';
	var $schema;
	var $debug;
	var $debug_file;
/*METODI DELLA CLASSE*/	
	/*Inizializzatore della Classe*/
	function stampe($pr,$idmodello,$out,$schema="stp",$d=0,$mod_write=0){
		/*INIZIALIZZAZIONI DEI CAMPI DELLA CLASSE*/
		$this->pratica=$pr;
		$this->get_db();
		if (!is_numeric($idmodello)){
			$in=$idmodello;
			$sql="SELECT testohtml FROM stp.e_modelli where nome='$in'";
		}	
		else{
			$sql="SELECT nome,testohtml FROM stp.e_modelli where id=$idmodello";
			$this->idmodello=$idmodello;
		}
		// QUERY PER SELEZIONARE IL E IL NOME TESTO DEL MODELLO IN BASE ALL'ID
		
		// QUERY PER SELEZIONARE IL TESTO DEL MODELLO IN BASE AL NOME
		
		print_debug($sql);
		if(!$this->db->sql_query($sql))
			print_debug("ERRORE $sql");
		$tmp=$this->db->sql_fetchfield('testohtml');
		$in=($in)?($in):($this->db->sql_fetchfield('nome'));
		$objPratica=new pratica($pr);
        $this->path_out=$objPratica->documenti;
        //print_array($objPratica);
		$this->modello=DATA_DIR."praticaweb/modelli/".$in;
		echo "<p>$this->modello</p>";
		$this->documento=$this->path_out."/"."$pr-$out.html";
		$this->nome_doc="$pr-$out.html";
		$this->nome_pdf="$pr-$out.doc";
		$this->overwrite=$mod_write;
		
		$this->schema=$schema;
		$this->debug=$d;
		
		if ($this->debug){		//APRO IL FILE DOVE ANDRO' A SCRIVERE I DEBUG
			$this->debug_file=fopen(DEBUG_DIR."stampe_debug_".$_SESSION["USER_ID"].".sql","w+");
		}
		//Controllo esistenza del modello
		//if (file_exists(LIB."HTML_ToPDF.conf")){
		//	$handle=fopen(LIB."HTML_ToPDF.conf","r");
		//	$this->style= fread($handle, filesize(LIB."HTML_ToPDF.conf"));
		//fclose($handle);
		//}
		//ACQUISISCO IL TESTO DEL MODELLO DA FILE
		$handle = fopen($this->modello, "r");
		$tmp= fread($handle, filesize($this->modello));
		fclose($handle);
		$this->head="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
			<HTML>
<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"UTF-8\">
<HEAD>
	<STYLE>
		".$this->style."
	</STYLE>
	<TITLE>".$this->nome_doc."</TITLE>
</HEAD>";
			
		$this->body=str_replace(chr(10),"",$tmp);
		//$this->body=str_replace(chr(13),"",$this->body);
		//DA FARE Acquisizione delle viste del DB
		$this->get_all_tags($this->body);					//Acquisizione dei TAGS del modello	
			
			
	}
	/*Funzione che acquisisce i tag (non i cicli) da un testo passato come stringa*/
	function get_tag($str){
		preg_match_all($this->regexp_tag,$str,$out,PREG_SET_ORDER);
		print_debug($this->regexp_tag."\n\n".$str,null,"regexp");
		print_debug($out,null,"regexp");
		
		for($j=0;$j<count($out);$j++){
			list($html,$tmp)=$out[$j];
			list($tipo,$vista,$campo)=explode(".",$tmp);
			$index=($campo)?($vista.".".$campo):($vista);
			$this->tag[$index]=$html;
			switch ($tipo) {
				case "V":
					//list($vista,$campo)=explode(".",$tab);
					$this->viste[]=$vista;
					$this->campi[$vista][]=$campo;
					break;
				case "O":
					//list($vista,$campo)=explode(".",$nome);
					$this->viste[]=$vista;
					$this->obbligatori[$vista][]=$campo;
					break;
				case "F":
					$this->funzioni[]=$vista;
					$this->campi_funz[$vista][]=$campo;
					break;
				case "T":
					$this->finali[]=$vista;
					break;
				case "D":$this->data=$vista;
			}
		}
	}
	function get_tag_obbl($str,$i){
		preg_match_all($this->regexp_tag_obbl,$str,$out_obbl,PREG_SET_ORDER);
		for($j=0;$j<count($out_obbl);$j++){
			
			list($html,$tmp)=$out_obbl[$j];
			list($tipo,$vista,$campo)=explode(".",$tmp);
			$index=($campo)?($vista.".".$campo):($vista);
			$this->tag_obbl[$index]=$html;
			switch ($tipo) {
				case "V":
					//list($vista,$campo)=explode(".",$tab);
					$this->viste[]=$vista;
					$this->obbligatori[$vista][]=$campo;
					break;
				case "F":
					$this->funzioni[]=$vista;
					$this->obbligatori_funz[$vista][]=$campo;
					break;
			}
			
			/*if(($vista) and ($campo)){
				
				$this->viste[]=$vista;
				$this->obbligatori[$i][]=$vista.".".$campo;
			}*/
		}
	}
	
	/*Funzione che acquisisce tutti i tag compresi i cicli e li inserisce nei campi della classe (richiama get_tag)*/
	
	function get_all_tags(){

		preg_match_all($this->regexp_cicli,$this->body,$out_cicli,PREG_SET_ORDER);
		preg_match_all($this->regexp_if,$this->body,$out_if,PREG_SET_ORDER);
		
		$k=0;
		for($i=0;$i<count($out_cicli);$i++){
			for($j=0;$j<count($out_if);$j++){	//Verifico se all'interno del ciclo esiste un paragrafo condizionale
				if (strpos($out_cicli[$i][1],$out_if[$j][0])) {		//paragrafo condizionale interno a ciclo
					$this->se[$k]=$out_if[$j][0];					//inserisco il paragrafo condizionale nel str. dati
					$this->get_tag($out_if[$j][1]);					//ricerco tag all'interno del paragrafo condizionale
					$this->get_tag_obbl($out_if[$j][1],$k);
					$this->body=str_replace($out_if[$j][0],"#se-$k#",$this->body);
					$k++;
				}
			}
			$this->get_tag($out_cicli[$i][1]);						//ricerco tag all'interno del ciclo
			$this->ciclo[]=$out_cicli[$i][0];						//inserisco il ciclo nel str. dati
			$this->body=str_replace($out_cicli[$i][0],"#ciclo-$i#",$this->body);
		}
		for($i=0;$i<count($out_if);$i++){	
			
			if(((count($this->se)) and (!in_array($out_if[$i][0],$this->se))) or (!$this->se)){
				$this->se[$k]=$out_if[$i][0];					//inserisco il paragrafo condizionale nel str. dati
				$this->get_tag($out_if[$i][1]);					//ricerco tag all'interno del paragrafo condizionale
				$this->get_tag_obbl($out_if[$i][1],$k);
				$this->body=str_replace($out_if[$i][0],"#se-$k#",$this->body);
				$k++;
			}
		}

		$this->get_tag($this->body);								//ricerco tag all'interno del modello
		for($i=0;$i<count($out_cicli);$i++){
			$this->body=str_replace("#ciclo-$i#",$this->ciclo[$i],$this->body);	//Sostituisco ciclo i-esimo con il suo codice HTML
			for($j=0;$j<count($out_if);$j++) $this->body=str_replace("#se-$j#",$this->se[$j],$this->body);	//Sostituisco il paragrafo condizionale j-esimo con il suo codice HTML
			$this->body=str_replace("#ciclo-$i#",$this->ciclo[$i],$this->body);	//Sostituisco i cicli he erano all'interno dei paragrafi con il loro codice HTML
		}
		if ($this->ciclo) $this->ciclo=array_unique($this->ciclo);
		if ($this->tag) $this->tag=array_unique($this->tag);
		if ($this->viste) $this->viste=array_unique($this->viste);
		foreach($this->campi as $key=>$val) if ($this->campi[$key]) $this->campi[$key]=array_unique($val);
		if ($this->funzioni) $this->funzioni=array_unique($this->funzioni);
		if ($this->finali) $this->finali=array_unique($this->finali);
		
		
	}
	
	/*Funzione che acquisisce i dati dal database (deve essere chiamata dopo aver acquisito i campi unione) */
	
	function query($f=""){
		if ($f){
			$filtri=explode("AND",$f);
			foreach($filtri as $flt) {
				$tmp=explode("=",trim($flt));
				$campi_filtro[]=$tmp[0];
				$val_filtro[]=$tmp[1];
			}
		}
		
		if ($this->campi) foreach($this->campi as $vista=>$arr_campi){
			$campi=implode(",",$arr_campi);
			if (!$f)  $sql="SELECT DISTINCT $campi FROM ".$this->schema.".$vista WHERE pratica=".$this->pratica;
			else{
				$sql="SELECT * FROM ".$this->schema.".$vista";
				$this->db->sql_query($sql);
				fwrite($this->debug_file,$sql."\n");
				for($i=0;$i<$this->db->sql_numfields();$i++) $cols[$vista][$i]=$this->db->sql_fieldname($i);
				//echo "<p>$vista</p>";print_array($cols);echo "<p>CAMPI DEI FILTRI</p>";print_array($campi_filtro);echo "<p>FINE</p>";
				$flag=1;
				for ($i=0;$i<count($campi_filtro);$i++){
					if (!in_array($campi_filtro[$i],$cols[$vista])) $flag=0;
				}
				
				$sql=($flag)?("SELECT DISTINCT id,$campi FROM ".$this->schema.".$vista WHERE $f and pratica=".$this->pratica):("SELECT DISTINCT id,$campi FROM ".$this->schema.".$vista WHERE pratica=".$this->pratica);
			}
			if ($this->db->sql_query($sql)){
				if ($this->debug) fwrite($this->debug_file,$sql."\n");
				foreach($arr_campi as $val) $this->dati[$vista.".".$val]=$this->db->sql_fetchlist($val);
			}
			else
				$this->errors["QUERY"][]="<p>Errore nella query $sql</p>";
		}
		if ($this->campi_funz) foreach($this->campi_funz as $funz=>$arr_campi){
			$campi=implode(",",$arr_campi);
			if (!$f) $sql="SELECT $campi FROM ".$this->schema.".$funz(".$this->pratica.");";
			else{
				$sql="SELECT * FROM ".$this->schema.".$funz(".$this->pratica.");";
				$this->db->sql_query($sql);
				fwrite($this->debug_file,$sql."\n");
				for($i=0;$i<$this->db->sql_numfields();$i++) $cols_funz[$funz][$i]=$this->db->sql_fieldname($i);					
				
				$flag=1;
				for ($i=0;$i<count($campi_filtro);$i++){
					if (!in_array($campi_filtro[$i],$cols_funz[$funz])) $flag=0;
				}
				
				$sql=($flag)?("SELECT $campi FROM ".$this->schema.".$funz(".$this->pratica.") WHERE $f;"):("SELECT $campi FROM ".$this->schema.".$funz(".$this->pratica.");");
			}
			if ($this->debug) fwrite($this->debug_file,$sql."\n");
			if ($this->db->sql_query($sql)){
				//$this->dati[$funz]=$this->db->sql_fetchlist($funz);
				foreach($arr_campi as $val) $this->dati[$funz.".".$val]=$this->db->sql_fetchlist($val);
			}
			else
				$this->errors["QUERY"][]="<p>Errore nella query $sql</p>";
		}
		if ($this->obbligatori) foreach($this->obbligatori as $vista=>$campi){
			//list($vista,$campo)=explode(".",$this->obbligatori[$i][0]);
			$campo=$campi[0];
			if (!$f) $sql="SELECT DISTINCT $campo FROM ".$this->schema.".$vista WHERE pratica=".$this->pratica;
			else
				$sql="SELECT DISTINCT $campo FROM ".$this->schema.".$vista WHERE $f and pratica=".$this->pratica;
			if ($this->debug) fwrite($this->debug_file,$sql."\n");
			if ($this->db->sql_query($sql)){
				$this->dati_obbl[$vista.".".$campo]=$this->db->sql_fetchlist($campo);
			}
			else
				$this->errors["QUERY"][]="<p>Errore nella query $sql</p>";
		}
		$cols_funz=ARRAY();
		$campi_filtro=ARRAY();
		if ($this->obbligatori_funz) foreach($this->obbligatori_funz as $funz=>$arr_campi){
			$campi=implode(",",$arr_campi);
			if (!$f) $sql="SELECT $campi FROM ".$this->schema.".$funz(".$this->pratica.");";
			else{
				$sql="SELECT * FROM ".$this->schema.".$funz(".$this->pratica.");";
				$this->db->sql_query($sql);
				fwrite($this->debug_file,$sql."\n");
				for($i=0;$i<$this->db->sql_numfields();$i++) $cols_funz[$funz][$i]=$this->db->sql_fieldname($i);					
				
				$flag=1;
				for ($i=0;$i<count($campi_filtro);$i++){
					if (!in_array($campi_filtro[$i],$cols_funz[$funz])) $flag=0;
				}
				
				$sql=($flag)?("SELECT $campi FROM ".$this->schema.".$funz(".$this->pratica.") WHERE $f;"):("SELECT $campi FROM ".$this->schema.".$funz(".$this->pratica.");");
			}
			if ($this->debug) fwrite($this->debug_file,$sql."\n");
			if ($this->db->sql_query($sql)){
				//$this->dati[$funz]=$this->db->sql_fetchlist($funz);
				foreach($arr_campi as $val) $this->dati_obbl[$funz.".".$val]=$this->db->sql_fetchlist($val);
			}
			else
				$this->errors["QUERY"][]="<p>Errore nella query $sql</p>";
		}
	}
	//Metodo che sostistuisce ai tag i corrispondenti valori
	//DA FINIRE idea : sostituire prima i cicli con tag speciale poi sostituire i campi uno a uno ed infine sostituire i campi uno a molti nei cicli!!!! Dovrei evitare un'altro ciclo sui dati
	function sostituisci_valori($filtro=""){
		print_debug($filtro);
		$this->query($filtro);
		for($i=0;$i<count($this->se);$i++) {						//SOSTITUZIONE DEI PARAGRAFI CONDIZIONALI
			preg_match_all($this->regexp_tag_obbl,$this->se[$i],$out,PREG_SET_ORDER);
			print_debug($out,null,'se');
			list($tipo,$vista,$campo)=explode(".",$out[0][1]);
			
			if((!$this->dati_obbl[$vista.".".$campo][0])) $this->body=str_replace($this->se[$i],"",$this->body);
			else{
				$replaced_text=str_replace('<span class="iniziose">INIZIO_SE</span>',"",$this->se[$i]);
				$replaced_text=str_replace('<span class="finese">FINE_SE</span>',"",$replaced_text);
				$this->body=str_replace($this->se[$i],$replaced_text,$this->body);
			}
			
		}
		for($i=0;$i<count($this->ciclo);$i++){
			$n_giri=$this->get_size($this->ciclo[$i]);
			for($j=0;$j<$n_giri;$j++){			//SOSTITUZIONE DEI CICLI
				//$ciclo.=$this->ciclo[$i];
				$val_ciclo=$this->ciclo[$i];
				unset($match);
				foreach($this->tag as $key=>$val){
					list($vista,$campo)=explode(".",$key);
					if ((@array_key_exists($key,$this->dati) or @array_key_exists($key,$this->funzioni))){
						//$val_ciclo=str_replace($val,"<span>".((count($this->dati[$key])==$n_giri)?($this->dati[$key][$j]):($this->dati[$key][0]))."</span>",$val_ciclo);
						//if (!$this->dati[$key][0]) $this->dati[$key][0]="Dato non presente";
						$val_ciclo=str_replace($val,((count($this->dati[$key])==$n_giri)?($this->dati[$key][$j]):($this->dati[$key][0])),$val_ciclo);
					}
					elseif (@in_array($key,$this->finali)){ 
						$val_ciclo=str_replace($val,(($j<($n_giri-1))?($this->terminatore($key)):("")),$val_ciclo);
					}
					elseif(@in_array($campo,$this->obbligatori[$vista])){
						// Da fare la gestione dei campi obbligatori
						$match=(strlen($this->dati_obbl[$key][$j])>0)?("TROVATO"):("NON TROVATO");
						/*echo "$key Valore : ".$this->dati_obbl[$key][$j];
						echo "$match<br>";*/
						$val_ciclo=str_replace($val,((count($this->dati[$key])==$n_giri)?($this->dati_obbl[$key][$j]):($this->dati_obbl[$key][0])),$val_ciclo);
					}
					elseif(@in_array($campo,$this->obbligatori_funz[$vista])){
						// Da fare la gestione dei campi obbligatori
						$match=(strlen($this->dati_obbl[$key][$j])>0)?("TROVATO"):("NON TROVATO");
						/*echo "$key Valore : ".$this->dati_obbl[$key][$j];
						echo "$match<br>";*/
						$val_ciclo=str_replace($val,((count($this->dati[$key])==$n_giri)?($this->dati_obbl[$key][$j]):($this->dati_obbl[$key][0])),$val_ciclo);
					}	
				}
				if ($this->tag_obbl)
				foreach($this->tag_obbl as $key=>$val){
					if(@array_key_exists($key,$this->dati_obbl)) $this->body=str_replace($val,$this->dati_obbl[$key][$i],$this->body);
				}
				if (!isset($match) or $match=="TROVATO") $ciclo.=$val_ciclo;
				$val_ciclo="";	
			}
			$this->body=str_replace($this->ciclo[$i],$ciclo,$this->body);
			$ciclo="";
			
		}
		foreach($this->tag as $key=>$val){		//SOSTITUZIONE DEI CAMPI UNO A UNO
			
			if (array_key_exists($key,$this->dati) or @array_key_exists($key,$this->funzioni)){ 
				if (!$this->dati[$key][0]) $this->dati[$key][0]="";//dato non presente
				$this->body=str_replace($val,$this->dati[$key][0],$this->body);
			}
			elseif($key=="data.data")
				$this->body=str_replace($val,date('d-m-Y'),$this->body);
		}
		if ($this->tag_obbl)
		foreach($this->tag_obbl as $key=>$val){
			if(@array_key_exists($key,$this->dati_obbl)) $this->body=str_replace($val,htmlentities($this->dati_obbl[$key][0],ENT_NOQUOTES,'ISO8859-15'),$this->body);
		}
		$this->body=preg_replace('|<span class="iniziocicli">(.*)</span>|Umi',"",$this->body);
		$this->body=preg_replace('|<span class="finecicli">(.*)</span>|Umi',"",$this->body);
		//$this->body=str_replace(html_entity_decode("<span style=\"font-size: 11px; font-family: Verdana, Geneva, Arial, sans-serif; background-color: yellow\">FI_CICLO</span>"),"",$this->body);
	}
	/*Funzione che determina quante volte deve essere effettuato un ciclo*/
	function get_size($ciclo){
		$max=1;
		foreach($this->tag as $key=>$val) if (strpos($ciclo,$val)) $max=(($max>count($this->dati[$key])?($max):(count($this->dati[$key]))));
		return $max;
	}
	/*Funzione che inserisce il terminatore del ciclo alla fine di esso*/
	function terminatore($ch){
		switch ($ch){
			case "P":
				return "<P></P>";
				break;
			case "V":
				return ", ";
				break;
			case "PV":
				return "; ";
				break;
			case "C":
			default:
				return "<BR>";
				break;
		}
	}
	/*Funzione che scrive il documento creato nel file di output*/
	
	function crea_documento(){
		/*if (!file_exists($this->documento) or $this->overwrite){			//Controllo esistenza del documento
			$handle = fopen($this->documento, "w+");
			$this->body="<BODY>\n\t".$this->body."\n</BODY>\n</HTML>";
			fwrite($handle,$this->head."\n".$this->body);
			fclose($handle);
		}
		else
			$this->errors["FILE"][]="Documento $out già presente!";
			
		if ($this->debug) $this->print_debug();*/
		//$this->body=$this->head."\n<BODY>\n\t".$this->body."\n</BODY>\n</HTML>";
	}
	
    function crea_word(){
        $standardTime=ini_get('max_execution_time');
		$standardMem=ini_get('memory_limit');
		ini_set('max_execution_time',600);
    }
    
	function crea_doc(){
		$standardTime=ini_get('max_execution_time');
		$standardMem=ini_get('memory_limit');
		ini_set('max_execution_time',600);
		$htmlFile = $this->documento;
		$pdfFile = $this->path_out."/".$this->nome_pdf;
        //echo "$pdfFile;";
		system("rm $pdfFile");
		//$html=$this->head."<body>$this->body</body></html>";
		$html=$this->body;
		$handle=fopen($pdfFile,'w');
		fwrite($handle,$html);
		fclose($handle);
		chmod($pdfFile,0777);
		chgrp($pdfFile, 'nogroup');
		
			
		ini_set('max_execution_time',$standardTime);
		ini_set('memory_limit',$standardMem);
	}
	
	function crea_pdf(){
		$standardTime=ini_get('max_execution_time');
		$standardMem=ini_get('memory_limit');
		ini_set('max_execution_time',600);
		ini_set('memory_limit','512M');
		require_once LIB."HTML_ToPDF.php";
		// Full path to the file to be converted
		$htmlFile = $this->documento;
		$defaultDomain = '';
		$pdfFile = $this->path_out.$this->nome_pdf;
		system("rm $pdfFile");
		/*MODIFICHE */
		$handle=fopen(LIB."HTML_ToPDF.conf","r");
		$style= fread($handle, filesize(LIB."HTML_ToPDF.conf"));
		fclose($handle);
		$html="<html><head><style>$style</style></head><body>$this->body</body></html>";
		$pdf =& new HTML_ToPDF($html, $defaultDomain, $pdfFile);
		/*FINE MODIFICHE*/
		//$pdf =& new HTML_ToPDF($this->body, $defaultDomain, $pdfFile);
		if($_SESSION["USER_ID"]==32) $pdf->debug=false;
		else
			$pdf->debug=false;
		$this->errors["PDF"][]="Creato il documento $pdfFile dal documento $htmlFile";
		$result = $pdf->convert();

		ini_set('max_execution_time',$standardTime);
		ini_set('memory_limit',$standardMem);	
		// Check if the result was an error
		/*if (PEAR::isError($result)) {
			$this->errors["PDF"][]="Errore nella creazione del File ".$this->nome_pdf;
		   $err=1;
		}*/
		//$result='';
	}
	function crea_pdf_1(){ 
		$standardTime=ini_get('max_execution_time');
		$standardMem=ini_get('memory_limit');
		ini_set('max_execution_time',600);
		ini_set('memory_limit','512M');
		require_once LIB."dompdf_config.inc.php"; 
		$sql="SELECT script,definizione,dimensione,orientamento FROM stp.e_modelli inner join stp.css on(css_id=css.id) WHERE e_modelli.id=$this->idmodello";
		print_debug($sql,null,"STP");
		$this->db->sql_query($sql);
		$definizione=$this->db->sql_fetchfield('definizione');
		$script=$this->db->sql_fetchfield('script');
		$size=$this->db->sql_fetchfield('dimensione');
		$orient=$this->db->sql_fetchfield('orientamento');
		$pdfFile = $this->path_out.$this->nome_pdf; 
		@unlink($pdfFile);
		$html="<html>
	<head>
		<style>$definizione</style>
	</head>
	<body>
		$script
		$this->body
	</body>
</html>";
		print_debug($html,null,"STP");
		/*MODIFICHE */
		$dompdf = new DOMPDF(); 
		$dompdf->set_paper($size,$orient);
		$dompdf->load_html($html);
		$dompdf->render();
		$handle=fopen($pdfFile,'w+');
		$p=$dompdf->output(); 
		fwrite($handle,$p);
		fclose($handle);
		ini_set('max_execution_time',$standardTime);
		ini_set('memory_limit',$standardMem);	
	}
	/*Stampa a schermo i tag corrispondenti*/
	function stampa_tag($tipo){
		//echo "<hr><H3>".strtoupper($tipo)." del modello ".str_replace($this->path_in,"",$this->modello)." pratica n° ".$this->pratica."</H3><hr><pre>";
		switch ($tipo){
			case "cicli":
				if(count($this->ciclo)) print_r($this->ciclo);
				else
					echo "Nessun ciclo trovato";
				break;
			case "viste":
				if(count($this->viste)) print_r($this->viste);
				else
					echo "Nessuna vista trovata";
				break;
			case "campi":
				if(count($this->campi)) print_r($this->campi);
				else
					echo "Nessun campo trovato";
				break;
			case "tag":
				if(count($this->tag)) print_r($this->tag);
				else
					echo "Nessun tag trovato";
				break;
			case "funzioni":
				if(count($this->funzioni)) print_r($this->funzioni);
				else
					echo "Nessuna funzione trovata";
				break;
			case "finali":
				if(count($this->finali)) print_r($this->finali);
				else
					echo "Nessun terminatore di ciclo trovato";
				break;
			case "dati":
				if(count($this->dati)) print_r($this->dati);
				else
					echo "Nessun dato trovato";
				break;
			case "dati obbligatori":
				if(count($this->dati_obbl)) print_r($this->dati_obbl);
				else
					echo "Nessun dato trovato";
				break;
			case "errori":
				if(count($this->errors)) print_r($this->errors);
				else
					echo "Nessun errore trovato";
				break;
			case "obbligatori":
				if(count($this->obbligatori)) print_r($this->obbligatori);
				else
					echo "Nessun campo obbligatorio trovato";
				break;
			case "se":
				if(count($this->se)) print_r($this->se);
				else
					echo "Nessun paragrafo condizionale trovato";
				break;
		}
		echo "</pre><hr>";
	}
	
	function print_debug(){
		fwrite($this->debug_file,"\nELENCO DEI CICLI\n");
		if(count($this->ciclo))
			foreach ($this->ciclo as $key=>$val){
				fwrite($this->debug_file,"\t$key\n\t\t$val");
			}
		else
			fwrite($this->debug_file,"\t\tNessun ciclo trovato\n");

		fwrite($this->debug_file,"\nELENCO DELLE VISTE\n");
		if(count($this->viste))
			foreach ($this->viste as $key=>$val){
				fwrite($this->debug_file,"\t$key\n\t\t$val");
			}
		else
			fwrite($this->debug_file,"\t\tNessuna vista trovata\n");

		fwrite($this->debug_file,"\nELENCO DEI CAMPI\n");
		if(count($this->campi))
			foreach ($this->campi as $key=>$val){
				fwrite($this->debug_file,"\tTABELLA $key\n");
				foreach ($val as $k=>$v)
					fwrite($this->debug_file,"\t\t$v\n");
			}
		else
			fwrite($this->debug_file,"\t\tNessun campo trovato\n");

		fwrite($this->debug_file,"\nELENCO DEI TAG\n");
		if(count($this->tag))
			foreach ($this->tag as $key=>$val){
				fwrite($this->debug_file,"\t$key\n\t\t$val\n");
			}
		else
			fwrite($this->debug_file,"\t\tNessun tag trovato\n");

		fwrite($this->debug_file,"\nELENCO DELLE FUNZIONI\n");
		if(count($this->funzioni))
			foreach ($this->funzioni as $key=>$val){
				fwrite($this->debug_file,"\t$key\n\t\t$val");
			}
		else
			fwrite($this->debug_file,"\t\tNessuna funzione trovata\n");
					
		fwrite($this->debug_file,"\nELENCO DEI TERMINATORI DI CICLO\n");
		if(count($this->finali))
			foreach ($this->finali as $key=>$val){
				fwrite($this->debug_file,"\t$key\n\t\t$val");
			}
		else
			fwrite($this->debug_file,"\t\tNessun terminatore di ciclo trovato\n");

		fwrite($this->debug_file,"\nELENCO DEI DATI\n");
		if(count($this->dati))
			foreach ($this->dati as $key=>$val){
				list($tab,$campo)=explode(".",$key);
				fwrite($this->debug_file,"\tTABELLA $tab\n\t\tCAMPO $campo\n");
				foreach ($val as $k=>$v)
					fwrite($this->debug_file,"\t\t\t$v\n");
			}
		else
			fwrite($this->debug_file,"\t\tNessun dato trovato\n");
		
		fwrite($this->debug_file,"\nELENCO DEI DATI OBBLIGATORI\n");
		if(count($this->dati_obbl))
			foreach ($this->dati_obbl as $key=>$val){
				fwrite($this->debug_file,"\t$key\n\t\t$val");
			}
		else
			fwrite($this->debug_file,"\t\tNessun dato obbligatorio trovato\n");
			
		fwrite($this->debug_file,"\nELENCO DEGLI ERRORI\n");
		if(count($this->errors))
			foreach ($this->errors as $key=>$val){
				if (is_array($val)){
					fwrite($this->debug_file,"\t$key\n");
					foreach($val as $v) fwrite($this->debug_file,"\t\t$v\n");
				}
				else
					fwrite($this->debug_file,"\t$key\n\t\t$val");
			}
		else
			fwrite($this->debug_file,"\t\tNessun errore trovato\n");
	
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
		if(isset($this->db)) $this->db->sql_close;
	}
	function close(){
		$this->close_db();
		if ($this->debug){		//CHIUDO IL FILE DOVE ANDRO' A SCRIVERE I DEBUG
			fclose($this->debug_file);
		}
	}
}
?>
