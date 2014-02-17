<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of new
 *
 * @author marco
 */
//require_once APPS_DIR."plugins/phpWord.php";


require_once APPS_DIR."login.php";
require_once APPS_DIR."plugins/openTbs/tbs_class_php5.php";
require_once APPS_DIR."plugins/openTbs/tbs_plugin_opentbs.php";
require_once APPS_DIR."/lib/php-sql-parser.php";
function decode(&$item, &$key){
	$item=(mb_detect_encoding($item)=='UTF-8' && FALSE)?(utf8_decode($item)):($item);
        $item=str_replace('&','&amp;',$item);
}

class wordDoc {
	var $db;
	var $modello;
	var $pratica;
	var $viste;
	var $funzioni;
	var $data = Array();
	var $schema='stp';
	var $modelliDir;
        var $fields;
	var $query;
	function __construct($modello,$pratica){
		$this->db=appUtils::getDb();
		$db=$this->db;
		$this->modello=$modello;
		$this->pratica=$pratica;
		$sql="SELECT * FROM stp.e_modelli WHERE id=?";
		$ris=$db->fetchAssoc($sql,Array($modello));
		$this->type=(strpos($ris["form"],'cdu.')!==FALSE)?(1):(0);
		$this->modello=$ris["nome"];
		$this->viste=explode(',',$ris["views"]);
		$this->funzioni=explode(',',$ris["functions"]);
		$this->modelliDir=DATA_DIR.DIRECTORY_SEPARATOR."praticaweb".DIRECTORY_SEPARATOR."modelli".DIRECTORY_SEPARATOR;
		$info=pathinfo($this->modello);
		$this->basename=$info["filename"];
		$this->extension=$info["extension"];
		$this->docName=utils::rand_str()."-".$this->modello;
		$this->actions=$ris["action"];
                $this->query=$this->setQuery();
	}
	private function getData(){
		$db=$this->db;
		for($i=0;$i<count($this->viste);$i++){
			$vista=$this->viste[$i];
			if ($vista){
				$sql="SELECT * FROM ".$this->schema.".$vista WHERE pratica=?";
				
				$ris=$db->fetchAll($sql,Array($this->pratica));
				//array_walk_recursive($ris, 'decode');
				$this->data[$vista]=$ris;
					
				
			}
			
		}
		for($i=0;$i<count($this->funzioni);$i++){
			$funzione=$this->funzioni[$i];
			if ($funzione){
				$sql="SELECT * FROM ".$this->schema.".$funzione(?);";
				$ris=$db->fetchAll($sql,Array($this->pratica));
				//array_walk_recursive($ris, 'decode');
				$this->data[$funzione]=$ris;
			}
		}
                
                foreach($this->query["single"] as $sql){
                    $ris=$db->fetchAssoc($sql,Array($this->pratica));
                    $this->data=(!$ris)?($this->data):(array_merge($this->data,$ris));
                    
                    
                }
                foreach($this->query["multiple"] as $key=>$sql){
                    $ris=$db->fetchAll($sql,Array($this->pratica));
                    $this->data[$key]=$ris;
                }
                
                //print_debug($this->data,null,"STAMPE-PRE");
		$customData=$this->data;
                $pratica=$this->pratica;
		switch($this->type){
			case 1:
				if(file_exists(LOCAL_INCLUDE."cdu.php")){
					include_once LOCAL_INCLUDE."cdu.php";
				}
				
				break;
			default:
				if(file_exists(LOCAL_INCLUDE."stampe.php")){
					include_once LOCAL_INCLUDE."stampe.php";
                                        //print_debug($this->data,null,'STAMPE_LOCAL');

				}
				break;
		}
                array_walk_recursive($customData, 'decode');
		$this->data=$customData;
                print_debug($this->data,null,'STAMPE');
	}
        private function getFields(){
		$db=$this->db;
                $result=Array();
                $result=$db->fetchAll("SELECT colonna as id,colonna||coalesce(' : '||descrizione,'') as text,'open' as state FROM stp.descrizioni_colonne_di_stampa WHERE tabella='' AND colonna <> 'pratica' ORDER BY 1;",Array());
		
                $views=$db->fetchAll("SELECT DISTINCT tabella FROM stp.descrizioni_colonne_di_stampa WHERE length(tabella)>0 ORDER BY 1");
                foreach($views as $v){
                        $view=$v["tabella"];
                        $ris=$db->fetchAll("SELECT colonna as id,colonna||coalesce(' : '||descrizione,'') as text,'open' as state FROM stp.descrizioni_colonne_di_stampa WHERE tabella=? AND colonna <> 'pratica' ORDER BY 1;",Array($view));
                        $result[$view]=Array("id"=>$view,"text"=>$view,"state"=>"closed","key"=>$view,"children"=>$ris);
                }
                
		$customFields=$result;
                
                /*if(file_exists(LOCAL_INCLUDE."cdu.php")){
                        include_once LOCAL_INCLUDE."cdu.php";
                }
                
                if(file_exists(LOCAL_INCLUDE."stampe.php")){
                        include_once LOCAL_INCLUDE."stampe.php";
                }*/
		$this->fields=$customFields;
	}
	function createDoc($test=0){
		$TBS = new clsTinyButStrong; // new instance of TBS
		$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

		$this->getData();
		$TBS->LoadTemplate($this->modelliDir.$this->modello,OPENTBS_ALREADY_UTF8);	
		$TBS->SetOption('noerr',true);
		//$template = $PHPWord->loadTemplate($this->modelliDir.$this->modello);
		foreach($this->data as $tb=>$data){
			if (is_array($data))
				$TBS->MergeBlock($tb, $data);
			else
				$TBS->MergeField($tb,$data);

		}
		$TBS->MergeField("data", date("d/m/Y"));
                /*Check if exists header and footer */
                if($TBS->Plugin(OPENTBS_FILEEXISTS, "styles.xml")){
                    $TBS->LoadTemplate("#styles.xml");
                    foreach($this->data as $tb=>$data){
			if (is_array($data))
				$TBS->MergeBlock($tb, $data);
			else
				$TBS->MergeField($tb,$data);

                    }
                    $TBS->MergeField("data", date("d/m/Y"));
                }
		$pr=new pratica($this->pratica,$this->type);
		if ($test==1){
			$TBS->Show(TBS_OUTPUT);
		}
		else{
			$TBS->Show(OPENTBS_FILE,$pr->documenti.$this->docName);
			//echo $pr->documenti.$this->docName;
		}
		//print_array($this->data);
	} 
        function viewFieldList(){
            //return $query;exit;
            $this->getFields();
            $data=$this->fields;
            asort($data);
            $data=array_values($data);
            return $data;
        }
        function setQuery(){
            
	

            return Array(
            "single"=>Array(
                "data_odierna"=>    "SELECT CURRENT_DATE as oggi;",
                "single_dirigente"=>"SELECT * FROM stp.single_dirigente WHERE pratica=?;",
                "single_ubicazione"=>"SELECT * FROM stp.single_ubicazione WHERE pratica=?;",
                "single_lavori"=>"SELECT * FROM stp.single_lavori WHERE pratica=?;",
                "single_progetto"=>"SELECT * FROM stp.single_progetto WHERE pratica=?;",
                "single_pratica"=>"SELECT * FROM stp.single_pratica WHERE pratica=?;",
                "single_titolo"=>"SELECT * FROM stp.single_titolo WHERE pratica=?;",
                "single_fidi_oneri"=>"SELECT * FROM stp.single_fidi_oneri WHERE pratica=?;",
                "single_parere_vf"=>"SELECT * FROM stp.single_parere_vf WHERE pratica=?;",
                "single_parere_clp"=>"SELECT * FROM stp.single_parere_clp WHERE pratica=?;",
                "single_parere_ce"=>"SELECT * FROM stp.single_parere_ce WHERE pratica=?;",
                "single_parere_asl"=>"SELECT * FROM stp.single_parere_asl WHERE pratica=?;",
                "single_elenco_richiedenti"=>"SELECT * FROM stp.single_elenco_richiedenti WHERE pratica=?;",
                "single_elenco_concessionari"=>"SELECT * FROM stp.single_elenco_concessionari WHERE pratica=?;",
                "single_elenco_progettisti"=>"SELECT * FROM stp.single_elenco_progettisti WHERE pratica=?;",
                "single_elenco_cu"=>"SELECT * FROM stp.single_elenco_cu WHERE pratica=?;",
                "single_elenco_ct"=>"SELECT * FROM stp.single_elenco_ct WHERE pratica=?;",
                "single_parere_commissione"=>"SELECT * FROM stp.single_parere_commissione WHERE pratica=?;",
                "single_agibilita"=>"SELECT * FROM stp.single_agibilita WHERE pratica=?;",
                "single_oneri"=>"SELECT * FROM stp.single_oneri WHERE pratica=?;",
                "single_rate_oneri"=>"SELECT * FROM stp.single_rate_oneri WHERE pratica=?;",
                "single_rate_in_scadenza"=>"SELECT * FROM stp.single_rate_in_scadenza WHERE pratica=?;",
                "single_elenco_esecutori"=>"SELECT * FROM stp.single_elenco_esecutori WHERE pratica=?;",
                "single_elenco_direttori"=>"SELECT * FROM stp.single_elenco_direttori WHERE pratica=?;"
            ),
            "multiple"=>Array(
                "soggetti"=>"SELECT * FROM stp.multiple_soggetti WHERE pratica=?;",
                "richiedenti"=>"SELECT * FROM stp.multiple_richiedenti WHERE pratica=?;",
                "concessionari"=>"SELECT * FROM stp.multiple_concessionari WHERE pratica=?;",
                "progettisti"=>"SELECT * FROM stp.multiple_progettisti WHERE pratica=?;",
                "particelle_cu"=>"SELECT * FROM stp.multiple_particelle_cu WHERE pratica=?;",
                "particelle_ct"=>"SELECT * FROM stp.multiple_particelle_ct WHERE pratica=?;",
                "pareri"=>"SELECT * FROM stp.multiple_pareri WHERE pratica=?;",
                "oneri_calcolati"=>"SELECT * FROM stp.multiple_oneri_calcolati WHERE pratica=?;",
                "indirizzi"=>"SELECT * FROM stp.multiple_indirizzi WHERE pratica=?;",
                "documenti_mancanti"=>"SELECT * FROM stp.multiple_allegati_mancanti WHERE pratica=?;",
                "allegati"=>"SELECT * FROM stp.multiple_allegati WHERE pratica=?;",
                "oneri_dettaglio"=>"SELECT * FROM stp.multiple_oneri_dettaglio WHERE pratica=?;",
                "esecutore"=>"SELECT * FROM stp.multiple_esecutori WHERE pratica=?;",
                "direttore"=>"SELECT * FROM stp.multiple_direttori WHERE pratica=?;"
            )
        );
    }
}

?>
