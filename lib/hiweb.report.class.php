<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of hiweb
 *
 * @author marco
 */

use Doctrine\Common\ClassLoader;
require_once APPS_DIR.'plugins/Doctrine/Common/ClassLoader.php';

class gitReport {
	const sep = '|';
	const codBelf = "A192";
	const tipo_rec_h ="0";
	const tipo_rec_e ="9";
	const descComune = "Comune di Alghero";
	const versione = "3";
	const provenienza = "CONCESSIONI";
	
	var $recA = Array("chiave","concessione_numero","progressivo_numero","progressivo_anno","protocollo_data","protocollo_numero","tipo_intervento","oggetto","procedimento","codice_via","indirizzo","civico","barrato","zona","data_rilascio","data_inizio_lavori","data_fine_lavori","data_proroga_lavori","posizione_codice","posizione_descrizione","posizione_data");
	var $recB = Array("chiave","chiave_relazione","tipo_soggetto","tipo_persona","codice_fiscale","cognome","nome","denominazione_ragsoc","titolo","data_nascita","comune_nascita","provincia_nascita","indirizzo_residenza","civico_residenza","cap_residenza","comune_residenza","data_inizio_residenza","tel","fax","email","piva","indirizzo_studio","cap_studio","provincia_studio","albo","rag_soc_ditta","cf_ditta","pi_ditta","indirizzo_ditta","comune_ditta","provincia_ditta","qualita");
	var $recC = Array("chiave","chiave_relazione","dest_uso","zone_omogenee","zone_funzionali","vincoli","sup_eff_lotto","sup_edificabile","sup_lorda_pav","sup_occupata","sup_coperta","sup_filtrante","vol_virtuale","vol_fisico_istat","vol_totale","parcheggio_n_posti","parcheggio_sup","vani","num_abitazioni","data_agibilita","data_abitabilita");
	var $recD = Array("chiave","chiave_relazione","foglio","particella","subalterno","tipo","sezione","codice_fabbricato","chiave_relazione_e");
	var $recE = Array("chiave","chiave_relazione","filler","prefisso_via","indirizzo","codice_via","civico","civico2","civico3","descrizione");
	
	
	static function prepareData($res,$template){
		$result=Array();
		for($i=0;$i<count($res);$i++){
			foreach($template as $key){
				$result[$i][$key]=(in_array($key,array_keys($res[$i])))?($res[$i][$key]):("");
			}
		}
		return $result;
	}
	//RECORD DATI DELLA PRATICA
	static function recordA($pr){
		//$db=self::setDB();
		//Estrazione dati pratica
		$datiPratica=self::getDatiPratica($pr);
		//Estrazione Stato pratica
		$stato=self::getStatoPratica($pr);
		//Recupero primo indirizzo della lista
		$indirizzi=self::getIndirizzi($pr);
		$indirizzi=$indirizzi[0];
		//Unione dei dati
		$res=array_merge($datiPratica,$stato,$indirizzi);
		//Preparazione del dato x essere scritto
		$result=self::prepareData(Array($res),$this->recA);
		return $result;
	}
	//RECORD DEI SOGGETTI	
	static function recordB($pr){
		//Estrazione dati delle Persone
		$res=self::getSoggetti($pr);
		//Preparazione del dato x essere scritto
		$result=self::prepareData($res,$this->recB);
		return $result;
	}
	
	//RECORD DEI PARAMETRI DI PROGETTO
	static function recordC($pr){
		//Estrazione dati di Progetto
		$res=self::getDatiProgetto($pr);
		//Preparazione del dato x essere scritto
		$result=self::prepareData(Array($res),$this->recC);
		return $result;
	}
	//RECORD DEI DATI CATSTALI
	static function recordD($pr){
		//Estrazione Dati Catastali
		$res=self::getDatiCatastali($pr);
		//Preparazione del dato x essere scritto
		$result=self::prepareData($res,$this->recD);
		return $result;
	}
	//RECORD DEGLI INDIRIZZI
	static function recordE($pr){
		//Estrazione Dati Indirizzi
		$res=self::getIndirizzi($pr);
		//Preparazione del dato x essere scritto
		$result=self::prepareData($res,$this->recE);
		return $result;
	}
	
/*----------------------------------------------------------------------------------------------*/	
	static function getStatoPratica($pr){
		$db=self::setDB();
		$sql="SELECT codice,data,descrizione FROM pe.elenco_transizioni_pratiche WHERE pratica=? order by data DESC,tmsins DESC LIMIT 1;";
		$ris=$db->fetchAssoc($sql,Array($pr));
		return $ris;
	}
	
	static function getDatiPratica($pr){
		$db=self::setDB();
		$sql="select 
A.pratica as chiave,C.titolo as concessione_numero,regexp_replace(split_part(A.numero,'/',2),'([A-z]+)','')::integer as progressivo_numero,date_part('year',coalesce(data_prot,data_presentazione)) as progressivo_anno,coalesce(data_prot,data_presentazione) as protocollo_data,A.protocollo as protocollo_numero,B.descrizione as tipo_intervento,A.oggetto,C.data_rilascio,D.il as data_inizio_lavori,D.fl as data_fine_lavori,E.nome as procedimento
from
pe.avvioproc A left join 
pe.e_intervento B on(A.intervento=B.id) left join
pe.titolo C using(pratica) left join
pe.lavori D using(pratica) left join 
pe.e_tipopratica E on(A.tipo=E.id)
WHERE pratica=?";
		$ris=$db->fetchAssoc($sql,Array($pr));
		return Array($ris);
	}
	
	static function getDatiProgetto($pr){
		$db=self::setDB();
		$sql="SELECT 
A.id as chiave,A.pratica as chiave_relazionale,destuso1 as dest_uso,B.valore as sup_eff_lotto,C.valore as sup_edificabile,D.valore as sup_lorda_pav, E.valore as sup_coperta,F.valore as vol_totale,G.valore as parcheggio_n_posti,H.valore as parcheggio_sup,I.valore as sup_filtrante
FROM 
pe.progetto A left join 
pe.parametri_prog B using(pratica) left join
pe.parametri_prog C using(pratica) left join
pe.parametri_prog D using(pratica) left join
pe.parametri_prog E using(pratica) left join
pe.parametri_prog F using(pratica) left join
pe.parametri_prog G using(pratica) left join
pe.parametri_prog H using(pratica) left join
pe.parametri_prog I using(pratica)
WHERE 
A.pratica=? and 
B.parametro=1 and 
C.parametro=71 and
D.parametro=70 and
E.parametro=5 and
F.parametro=11 and
G.parametro=80 and
H.parametro=81 and 
I.parametro=72;";
		$ris=$db->fetchAssoc($sql,Array($pr));
		return Array($ris);
	}
	static function getIndirizzi($pr){
		$db=self::setDB();
		$sql="select id as chiave,pratica as chiave_relazionale,via as indirizzo,civico from pe.indirizzi where pratica=?";
		$ris=$db->fetchAll($sql,Array($pr));
		return $ris;
	}
	
	static function getDatiCatastali($pr){
		$db=self::setDB();
		$sql="SELECT id as chiave,pratica as chiave_relazione,sezione,foglio,mappale as particella,sub as subalterno,'TERRENI' as tipo FROM pe.cterreni WHERE pratica=?
UNION
(SELECT id as chiave,pratica as chiave_relazione,sezione,foglio,mappale as particella,sub as subalterno,'URBANO' as tipo FROM pe.curbano WHERE pratica=?)
";
		$res=$db->fetchAll($sql,Array($pr,$pr));
		return $res;
	}
	
	static function getSoggetti($pr){
		$tipi=Array("RI","PR","PG","DL","IM");
		$res=Array();
		$result=Array();
		foreach($tipi as $key){
			$sog=self::getSoggetto($pr, $key);
			$res=array_merge($result,$sog);
		}
		return $res;
	}
	
	static function getSoggetto($pr,$tipo){
		$db=self::setDB();
		switch($tipo){
			case "IM":
				$tiposoggetto="esecutore";
				break;
			case "PR":
				$tiposoggetto="proprietario";
				break;
			case "PG":
				$tiposoggetto="progettista";
				break;
			case "DL":
				$tiposoggetto="direttore";
				break;
			default:
				$tiposoggetto="richiedente";
				break;
		}
		$sql="SELECT 
id as chiave,pratica as chiave_relazionale,'P' as tipo_soggetto, case when(coalesce(piva,'')<>'') then 'G' else 'F' end as tipo_persona,
		codfis as codice_fiscale,cognome,nome,ragsoc as denominazione_ragsoc,'$tipo' as titolo,datanato as data_nascita,comunato as comune_nascita,provnato as provincia_nascita,
		indirizzo as indirizzo_residenza,comune as comune_residenza,cap as cap_residenza,telefono as tel,email, 
		piva,sede as indirizzo_studio,capd as cap_studio,comuned as comune_studio,provd as provincia_studio,albo,ragsoc as rag_soc_ditta,
		titolo
FROM pe.soggetti 
where pratica=? and $tiposoggetto=1 and voltura=0";
		$res=$db->fetchAll($sql,Array($pr));
		return $res;
		
	}
	static function setDB(){
		
		$classLoader = new ClassLoader('Doctrine', APPS_DIR.'plugins/');
		$classLoader->register();
		$config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array(
			'dbname' => DB_NAME,
			'user' => DB_USER,
			'password' => DB_PWD,
			'host' => DB_HOST,
			'port' => DB_PORT,
			'driver' => DB_DRIVER,
		);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
		
		return $conn;
	}	
	static function writeInfo($file,$data){
		$f=fopen($file,'w+');
		for($i=0;$i<count($data);$i++){
			$str=implode(self::sep,$data[$i])."\n";
			fwrite($f,$str);
		}
		fclose($f);
	}
	static function getHeadRecord(){
		return Array(
			self::tipo_rec_h,
			self::codBelf,
			self::descComune,
			date("%d/%m/%Y"),
			self::versione,
			self::provenienza
		);
	}
	static function getTailRecord(){
		return Array(
			self::tipo_rec_e,
			self::codBelf,
			self::descComune,
			date("%d/%m/%Y")
		);
	}
	
}

?>
