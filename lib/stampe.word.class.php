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

require_once APPS_DIR."plugins/openTbs/tbs_class_php5.php";
require_once APPS_DIR."plugins/openTbs/tbs_plugin_opentbs.php";
require_once APPS_DIR."/lib/php-sql-parser.php";
function decode(&$item, &$key){
	$item=(mb_detect_encoding($item)=='UTF-8' && FALSE)?(utf8_decode($item)):($item);
}



function parse_query($sql,$title=""){
    $parser = new PHPSQLParser($sql, true);
    foreach ($parser->parsed["SELECT"] as $v){
        $key=($v["alias"])?($v["alias"]["name"]):($v["base_expr"]);
	$res[$key]=Array("title"=>$key);
    }
    asort($res);
    return ($title)?Array("title"=>$title,"isFolder"=>"true","key"=>$title,"children"=>array_values($res)):($res);
}


class wordDoc {
	var $db;
	var $modello;
	var $pratica;
	var $viste;
	var $funzioni;
	var $data;
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
		$this->docName=$this->pratica."-".$this->modello;
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
				array_walk_recursive($ris, 'decode');
				$this->data[$vista]=$ris;
					
				
			}
			
		}
		for($i=0;$i<count($this->funzioni);$i++){
			$funzione=$this->funzioni[$i];
			if ($funzione){
				$sql="SELECT * FROM ".$this->schema.".$funzione(?);";
				$ris=$db->fetchAll($sql,Array($this->pratica));
				array_walk_recursive($ris, 'decode');
				$this->data[$funzione]=$ris;
			}
		}
                foreach($this->query["single"] as $sql){
                    $ris=$db->fetchAssoc($sql,Array($this->pratica));
                    array_merge($ris,$this->data);
                }
                foreach($this->query["multiple"] as $key=>$sql){
                    $ris=$db->fetchAll($sql,Array($this->pratica));
                    $this->data[$key]=$ris;
                }
		$customData=$this->data;
                
		switch($this->type){
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
		$this->data=$customData;
	}
        private function getFields(){
		$db=$this->db;
                $result=Array();
		foreach($this->query['single'] as $key=>$sql){
                    $res=parse_query($sql);
                    $result=array_merge($res,$result);
                }
                foreach($this->query['multiple'] as $key=>$sql){
                        $result[$key]=parse_query($sql,$key);
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
		$TBS->LoadTemplate($this->modelliDir.$this->modello, OPENTBS_ALREADY_XML);	
		$TBS->SetOption('noerr',true);
		//$template = $PHPWord->loadTemplate($this->modelliDir.$this->modello);
		foreach($this->data as $tb=>$data){
			if (is_array($data))
				$TBS->MergeBlock($tb, $data);
			else
				$TBS->MergeField($tb,$data);

		}
		$TBS->MergeField("data", date("d/m/Y"));
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
            return json_encode($data);
        }
        function setQuery(){
            return Array(
        "single"=>Array(
            "pratica"=>         "SELECT  numero, B.nome as tipo, C.descrizione as intervento, anno, 
                                    data_presentazione, protocollo, data_prot as data_protocollo, protocollo_int, data_prot_int,  
                                    D.nome as responsabile_procedimento, data_resp as data_responsabile, com_resp, data_com_resp as data_comunicazione_responsabile,
                                    rif_aut_amb as numero_autorizzazione_amb,  oggetto, note, rif_pratica as numero_pratica_precedente,  
                                    diritti_segreteria, riduzione_diritti, pagamento_diritti
                                FROM 
                                    pe.avvioproc A LEFT JOIN pe.e_tipopratica B ON (A.tipo=B.id) 
                                    LEFT JOIN pe.e_intervento C ON (A.intervento=C.id) 
                                    LEFT JOIN admin.users D ON (A.resp_proc=D.userid)  
                                    LEFT JOIN admin.users E ON (A.resp_it=E.userid)  
                                    LEFT JOIN admin.users F ON (A.resp_ia=F.userid)
                                WHERE 
                                    pratica=?",
            "elenco_ct"=>       "SELECT 
                                    trim(coalesce('Sezione: '||sezione,'')||coalesce(' Foglio: '||foglio,'')||coalesce(' Mappali: '||mappali,'')) as elenco_ct
                                FROM 
                                    (select B.nome as sezione,coalesce(foglio,'')as foglio,array_to_string(array_agg(coalesce(mappale,'')),',') as mappali from pe.cterreni A left join nct.sezioni B using(sezione) WHERE pratica = ? GROUP BY 1,2) AS FOO",
            "elenco_cu"=>      "SELECT 
                                    trim(coalesce('Sezione: '||sezione,'')||coalesce(' Foglio: '||foglio,'')||coalesce(' Mappali: '||mappali,'')) as elenco_cu
                                FROM 
                                    (select B.nome as sezione,coalesce(foglio,'')as foglio,array_to_string(array_agg(coalesce(mappale,'')),',') as mappali from pe.curbano A left join nct.sezioni B using(sezione) WHERE pratica = ? GROUP BY 1,2) AS FOO",
            "oneri"=>           "SELECT 
                                    totali.cc + totali.b1 + totali.b2 - totali.scb1 - totali.scb2 AS totale, 
                                    totali.quietanza AS quietanza, totali.data as data_quietanza, oblazione, q_oblazione as quietanza_, data_oblazione, indennita, totali.q_indennita as quietanza_indennita, data_indennita
                                FROM 
                                    oneri.totali
                                WHERE 
                                    pratica=?;",
            "agibilita"=>       "SELECT 
                                    numero_rich as numero_richiesta_agi,prot_rich as prot_richiesta_agi,data_rich as data_richiesta_agi,numero_doc as numero_agi,prot_doc as protocollo_agi,data_ril as data_agi
                                FROM 
                                    pe.abitabi
                                WHERE 
                                    pratica=?"            
        ),
        "multiple"=>Array(
            "soggetti"=>        "SELECT DISTINCT coalesce(app,'') as app, coalesce(cognome,'') as cognome, coalesce(nome,'') as nome,coalesce(app||' ','')||coalesce(cognome||' ','')||coalesce(nome,'') as nominativo, 
                                    coalesce(indirizzo,'') as indirizzo, coalesce(comune,'') as comune, coalesce(prov,'') as prov, coalesce(cap,'') as cap, 
                                    comunato, provnato, datanato, sesso, codfis,titolo,
                                    telefono, email, pec, 
                                    titolod, ragsoc, 
                                    sede, comuned, provd, capd, 
                                    piva, ccia, cciaprov, inail, inailprov, inps, inpsprov, cedile, cedileprov, 
                                    albo, albonumero, alboprov,
                                    coalesce(voltura,0) as voltura, comunicazioni, note, 
                                    proprietario,richiedente, concessionario, progettista, direttore, esecutore, 
                                    sicurezza, collaudatore,geologo, collaudatore_ca, progettista_ca, economia_diretta 
                                FROM 
                                    pe.soggetti 
                                WHERE 
                                    pratica=? and comunicazioni = 1",
            "indirizzi"=>       "SELECT  
                                    via, civico, interno, scala, piano
                                FROM 
                                    pe.indirizzi 
                                WHERE
                                    pratica=?;",
            "particelle_ct"=>  "SELECT DISTINCT 
                                    coalesce(B.nome,'') as sezione,foglio,mappale 
                                FROM 
                                    pe.cterreni A 
                                    LEFT JOIN nct.sezioni B USING(sezione) 
                                WHERE 
                                    pratica=?",

            "particelle_cu"=>   "SELECT DISTINCT 
                                    coalesce(B.nome,'') as sezione,foglio,mappale 
                                FROM 
                                    pe.curbano A 
                                    LEFT JOIN nct.sezioni B USING(sezione) 
                                WHERE 
                                    pratica=?",
            "pareri"=>          "SELECT 
                                    prot_rich as protocollo_richiesta, data_rich as data_richiesta, prot_soll as protocollo_sollecito, data_soll as data_sollecito, prot_ril as protocollo_rilascio, data_ril as data_rilascio, prot_rice as protocollo_ricezione, data_rice as data_ricezione, 
                                    C.nome as parere,testo,prescrizioni, note,numero_doc as numero_parere,B.nome as ente,B.codice as codice_ente
                                FROM 
                                    (SELECT AA.* FROM pe.pareri AA INNER JOIN (SELECT ente,max(data_rich) as data_rich FROM pe.pareri GROUP BY ente ) BB USING(ente,data_rich)) A 
                                    INNER JOIN (SELECT * FROM pe.e_enti WHERE enabled=1) B ON (A.ente=B.id) 
                                    LEFT JOIN pe.e_pareri C ON (A.parere=C.id)
                                WHERE 
                                    pratica=? 
                                ORDER BY data_rich DESC",
            
            "oneri_calcoli"=>   "SELECT 
                                    calcolati.anno as anno, e_tariffe.funzione::text || ' mq '::text || calcolati.sup::text AS calcolo, e_tariffe.funzione AS destuso, e_tariffe.descrizione AS descrizione, e_interventi.descrizione AS intervento,
                                    calcolati.perc as percentuale, calcolati.degradato as degradato, calcolati.sup as superficie, 
                                    calcolati.cc as cc, calcolati.b1 as b1, calcolati.b2 as b2, calcolati.e1 as e1, calcolati.e2 as e2, calcolati.note as note,
                                    e_c1.descrizione AS c1, e_c2.descrizione AS c2, e_c3.descrizione AS c3, e_c4.descrizione AS c4, e_d1.descrizione AS d1, e_d2.descrizione AS d2, calcolati.chk, calcolati.b1 + calcolati.b2 + calcolati.cc AS totale
                                    
                                FROM 
                                    oneri.calcolati
                                    LEFT JOIN oneri.e_c1 ON calcolati.tabella::text = e_c1.tabella::text AND calcolati.c1 = e_c1.valore
                                    LEFT JOIN oneri.e_c2 ON calcolati.tabella::text = e_c2.tabella::text AND calcolati.c2 = e_c2.valore
                                    LEFT JOIN oneri.e_c3 ON calcolati.tabella::text = e_c3.tabella::text AND calcolati.c3 = e_c3.valore
                                    LEFT JOIN oneri.e_c4 ON calcolati.tabella::text = e_c4.tabella::text AND calcolati.c4 = e_c4.valore
                                    LEFT JOIN oneri.e_d1 ON calcolati.tabella::text = e_d1.tabella::text AND calcolati.d1 = e_d1.valore
                                    LEFT JOIN oneri.e_d2 ON calcolati.tabella::text = e_d2.tabella::text AND calcolati.d2 = e_d2.valore
                                    JOIN oneri.e_interventi ON calcolati.tabella::text = e_interventi.tabella::text AND calcolati.intervento = e_interventi.valore
                                    JOIN oneri.e_tariffe ON calcolati.tabella::text = e_tariffe.tabella::text AND calcolati.anno = e_tariffe.anno
                                WHERE
                                    pratica=? 
                                ORDER BY calcolati.id DESC;",
            "allegati"=>        "SELECT 
                                    coalesce(B.descrizione,B.nome) as documento,allegato,mancante,integrato,sostituito
                                FROM 
                                    pe.allegati A 
                                    INNER JOIN pe.e_documenti B ON(A.documento=B.id) 
                                WHERE 
                                    pratica=?"
    )
);
        }
}

?>
