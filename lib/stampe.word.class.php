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
        var $table;
	function __construct($modello,$pratica){
		$this->db=appUtils::getDb();
		$db=$this->db;
		$this->modello=$modello;
		$this->pratica=$pratica;
		$sql="SELECT * FROM stp.e_modelli WHERE id=?";
		$ris=$db->fetchAssoc($sql,Array($modello));
		$this->type=$this->getType($ris["form"]);
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
        private function getType($form){
            $frms=explode(".",$form);
            switch($frms[0]){
                case "cdu":
                    $type=1;
                    $this->table="cdu.richiesta";
                    break;
                case "ce":
                    $type=2;
                    $this->table="ce.commissione";
                    break;
                case "vigi":
                    $type=3;
                    $this->table="vigi.avvioproc";
                    break;
                default:
                    $type=0;
                    $this->table="pe.avvioproc";
                    break;
            }
            return $type;
        }
	private function getData(){
            $db=$this->db;
            foreach($this->query["single"] as $sql){
                $ris=$db->fetchAssoc($sql,Array($this->pratica));
                $this->data=(!$ris)?($this->data):(array_merge($this->data,$ris));
            }
            foreach($this->query["multiple"] as $key=>$sql){
                $ris=$db->fetchAll($sql,Array($this->pratica));
                $this->data[$key]=$ris;
            }
            
            //Recupero dati da file 
            $TBS = new clsTinyButStrong; // new instance of TBS
            $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin
            foreach($this->query["file_multi"] as $key=>$sql){
                $ris=$db->fetchAll($sql,Array($this->pratica));
                $libDoc = "";
                for($i=0;$i<count($ris);$i++){
                    if ($ris[$i]["file"]!= $libDoc){
                        
                        $TBS->LoadTemplate($this->modelliDir."documentLib".DIRECTORY_SEPARATOR.$ris[$i]["file"],OPENTBS_DEFAULT);
                    }
                    $ris[$i]["source"]=$TBS->GetBlockSource($ris[$i]["blockname"], FALSE, FALSE);
                    $libDoc=$ris[$i]["file"];
                }
                $this->data[$key]=$ris;
            }
                
                //print_debug($this->data,null,"STAMPE-PRE");
		$customData=$this->data;
                $pratica=$this->pratica;
		switch($this->type){
                    case 3:
                        if(file_exists(LOCAL_INCLUDE."vigi.stampe.php")){
                            include_once LOCAL_INCLUDE."vigi.stampe.php";
                         }
                        break;
                    case 2:
                        if(file_exists(LOCAL_INCLUDE."ce.stampe.php")){
                            include_once LOCAL_INCLUDE."ce.stampe.php";
                         }
                        break;
                    case 1:
                        if(file_exists(LOCAL_INCLUDE."cdu.php")){
                                include_once LOCAL_INCLUDE."cdu.php";
                        }
                        break;
                    default:
                        if(file_exists(LOCAL_INCLUDE."stampe.php")){
                            include_once LOCAL_INCLUDE."stampe.php";
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
                $TBS->LoadTemplate($this->modelliDir.$this->modello,OPENTBS_ALREADY_XML);

		$TBS->SetOption('noerr',true);
		
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
            $this->db=appUtils::getDb();
            $db=$this->db;
            $result=Array("single"=>Array("data_odierna"=>"SELECT CURRENT_DATE as oggi;"),"multiple"=>Array(),"fromfile"=>Array());
            $sql="SELECT table_name as name,array_to_string(array_agg('B.'||column_name::varchar),',') as field_list FROM information_schema.views INNER JOIN information_schema.columns USING(table_name,table_schema) WHERE table_schema='stp' AND table_name ILIKE 'single_%' AND column_name NOT IN ('pratica') GROUP BY table_name ORDER BY 1;";
            $ris=$db->fetchAll($sql);
            for($i=0;$i<count($ris);$i++){
                $view=$ris[$i]["name"];
                $fieldList=$ris[$i]["field_list"];
                $result["single"][$view]=sprintf("SELECT A.pratica,$fieldList FROM %s A LEFT JOIN stp.$view B USING(pratica) WHERE A.pratica=?;",$this->table);
            }
            $sql="SELECT table_name as name,array_to_string(array_agg('B.'||column_name::varchar),',') as field_list FROM information_schema.views INNER JOIN information_schema.columns USING(table_name,table_schema) WHERE table_schema='stp' AND table_name ILIKE 'multiple_%' AND column_name NOT IN ('pratica') GROUP BY table_name ORDER BY 1;";
            $ris=$db->fetchAll($sql);
            for($i=0;$i<count($ris);$i++){
                $view=$ris[$i]["name"];
                $fieldList=$ris[$i]["field_list"];
                $result["multiple"][str_replace('multiple_','',$view)]=sprintf("SELECT A.pratica,$fieldList FROM %s A LEFT JOIN stp.$view B USING(pratica) WHERE A.pratica=?;",$this->table);
            }
            
            $sql="SELECT table_name as name,array_to_string(array_agg('B.'||column_name::varchar),',') as field_list FROM information_schema.views INNER JOIN information_schema.columns USING(table_name,table_schema) WHERE table_schema='stp' AND table_name ILIKE 'fromfile_multiple_%' AND column_name NOT IN ('pratica') GROUP BY table_name ORDER BY 1;";
            $ris=$db->fetchAll($sql);
            for($i=0;$i<count($ris);$i++){
                $view=$ris[$i]["name"];
                $fieldList=$ris[$i]["field_list"];
                $result["file_multi"][str_replace('fromfile_multiple_','',$view)]=sprintf("SELECT A.pratica,$fieldList FROM %s A LEFT JOIN stp.$view B USING(pratica) WHERE A.pratica=?;",$this->table);
            }
            return $result;
    }
}

?>
