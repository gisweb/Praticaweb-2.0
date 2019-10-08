<?php

/**
 * Description of pratica
 *
 * @author marco carbone
 */

use Doctrine\Common\ClassLoader;
require_once APPS_DIR.'plugins/Doctrine/Common/ClassLoader.php';
class generalPratica {
    var $pratica;
    var $tipopratica=null;
    var $titolo="";
    var $info=Array();
    var $allegati;
    var $url_allegati;
    var $documenti;
    var $url_documenti;
    var $user;
    var $cm_mq=37.7; //Valore Corrispettivo monetario €/mq
    var $next;
    var $prev;
    var $db;
    
    function __construct($id,$type=0){
		
        $this->pratica=$id;
        $db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
        if(!$db->db_connect_id)  die( "Impossibile connettersi al database ".DB_NAME);
        $this->db=$db;
        $this->db1=$this->setDB();
        switch($type){
            case 1:
                $this->initCdu();
                break;
            case 2:
                $this->initCE();
                break;
            case 3:
                $this->initVigi();
                break;
            default:
                $this->initPE();
                break;
        }
		
    }
    function __destruct(){
        $this->db1->close();
    }
    
    private function _setInfoUsers(){
        $conn = utils::getDb();
        $sql="SELECT userid as dirigente FROM admin.users WHERE attivato=1 and '13' = ANY(string_to_array(coalesce(gruppi,''),','));";
        $stmt = $conn->prepare($sql);
        $stmt->execute(Array($this->pratica));
        $this->info['dirigente']=$stmt->fetchColumn();
        //ESTRAGGO INFORMAZIONI SUL RESPONSABILE DEL SERVIZIO
        $sql="SELECT userid as rds FROM admin.users WHERE attivato=1 and '15' = ANY(string_to_array(coalesce(gruppi,''),','));";
        $stmt = $conn->prepare($sql);
        $stmt->execute(Array($this->pratica));
        $this->info['rds']=$stmt->fetchColumn();
        //INFO UTENTE (ID-GRUPPI-NOME)
        $this->userid=$_SESSION['USER_ID'];
        $this->usergroups=$_SESSION['GROUPS'];
        $sql="SELECT username FROM admin.users WHERE userid=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute(Array($this->userid));
        $this->user=$stmt->fetchColumn();
    }
    
    private function initPE(){
        $conn = utils::getDb();
        if ($this->pratica && is_numeric($this->pratica)){
            //INFORMAZIONI SULLA PRATICA
            $sql="SELECT numero,tipo,resp_proc,resp_it,resp_ia,date_part('year',data_presentazione) as anno,data_presentazione,data_prot,B.nome as tipo_pratica,B.tipologia,trim(format('%s%s%s',fascicolo,'.'||sub_fascicolo,'.'||sub_sub_fascicolo)) as fascicolo,anno_fascicolo,coalesce(online,0) as online FROM pe.avvioproc A LEFT JOIN pe.e_tipopratica B ON(A.tipo=B.id)  WHERE A.pratica=?";
            $stmt = $conn->prepare($sql);
            if (!$stmt->execute(Array($this->pratica))){
                return;
            }
            $r=$stmt->fetch(PDO::FETCH_ASSOC);
            $this->info=$r;
            $this->titolo=sprintf("%s n° %s del %s",$r["tipo_pratica"],$r["numero"],$r["data_presentazione"]);
            /*if($this->info['tipo'] < 10000 || in_array($this->info['tipo'],Array(14000,15000))){
                    $this->tipopratica='pratica';
            }
            elseif($this->info['tipo'] < 13000){
                    $this->tipopratica='dia';
            }
            else{
                    $this->tipopratica='ambientale';
            }*/
            $this->tipopratica=$info["tipologia"];
            $numero=appUtils::normalizeNumero($this->info['numero']);
            $tmp=explode('-',$numero);
            if (count($tmp)==2 && preg_match("|([A-z0-9]+)|",$tmp[0])){
                    //$tmp[0]=(preg_match("|^[89]|",$tmp[0]))?("19".$tmp[0]):($tmp[0]);
                    $numero=implode('-',$tmp);
            }
            $anno=($r['anno'])?($r['anno']):($tmp[0]);

            //Struttura delle directory
            //$arrDir=Array('/data','sanremo','pe','praticaweb','documenti','pe',$anno);
                $arrDir=Array(DATA_DIR,'praticaweb','documenti','pe',$anno);
            $this->annodir=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;
            $arrDir[]=$numero;
            $this->documenti=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;
            $arrDir[]="allegati";
            $this->allegati=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;
            $arrDir[]="tmb";
            $this->allegati_tmb=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;

            $this->url_documenti="/documenti/pe/$anno/$numero/";
            $this->url_allegati="/documenti/pe/$anno/$numero/allegati/";
            $this->smb_documenti=SMB_PATH."$anno/$numero/";


            //INFO PRATICA PREC E SUCC
            $sql="SELECT max(pratica) as pratica FROM pe.avvioproc WHERE pratica < ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute(Array($this->pratica));
            $this->prev=$stmt->fetchColumn();
            $sql="SELECT min(pratica) as pratica FROM pe.avvioproc WHERE pratica > ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute(Array($this->pratica));
            $this->prev=$stmt->fetchColumn();
        }

        //ESTRAGGO INFORMAZIONI SUL DIRIGENTE
        $this->_setInfoUsers();
    }
    
    private function initVigi(){
        $conn = utils::getDb();
        if ($this->pratica && is_numeric($this->pratica)){
            //INFORMAZIONI SULLA PRATICA
            $sql="SELECT numero,tipo,resp_proc,resp_it,resp_ia,date_part('year',data_presentazione) as anno,data_presentazione,data_prot,B.nome as tipo_pratica,B.tipologia FROM vigi.avvioproc A LEFT JOIN vigi.e_tipopratica B ON(A.tipo=B.id)  WHERE A.pratica=?";
            $stmt = $conn->prepare($sql);
            if (!$stmt->execute(Array($this->pratica))){
                return;
            }
            $r=$stmt->fetch(PDO::FETCH_ASSOC);
            $this->info=$r;
            $this->titolo=sprintf("%s n° %s del %s",$r["tipo_pratica"],$r["numero"],$r["data_presentazione"]);
            /*if($this->info['tipo'] < 10000 || in_array($this->info['tipo'],Array(14000,15000))){
                    $this->tipopratica='pratica';
            }
            elseif($this->info['tipo'] < 13000){
                    $this->tipopratica='dia';
            }
            else{
                    $this->tipopratica='ambientale';
            }*/
            $this->tipopratica=$info["tipologia"];
            $numero=appUtils::normalizeNumero($this->info['numero']);
            $tmp=explode('-',$numero);
            if (count($tmp)==2 && preg_match("|([A-z0-9]+)|",$tmp[0])){
                    //$tmp[0]=(preg_match("|^[89]|",$tmp[0]))?("19".$tmp[0]):($tmp[0]);
                    $numero=implode('-',$tmp);
            }
            $anno=($r['anno'])?($r['anno']):($tmp[0]);

            //Struttura delle directory
            //$arrDir=Array('/data','sanremo','vigi','praticaweb','documenti','vigi',$anno);
                $arrDir=Array(DATA_DIR,'praticaweb','documenti','vigi',$anno);
            $this->annodir=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;
            $arrDir[]=$numero;
            $this->documenti=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;
            $arrDir[]="allegati";
            $this->allegati=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;
            $arrDir[]="tmb";
            $this->allegati_tmb=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;

            $this->url_documenti="/documenti/vigi/$anno/$numero/";
            $this->url_allegati="/documenti/vigi/$anno/$numero/allegati/";
            $this->smb_documenti=SMB_PATH."$anno/$numero/";


            //INFO PRATICA PREC E SUCC
            $sql="SELECT max(pratica) as pratica FROM vigi.avvioproc WHERE pratica < ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute(Array($this->pratica));
            $this->prev=$stmt->fetchColumn();
            $sql="SELECT min(pratica) as pratica FROM vigi.avvioproc WHERE pratica > ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute(Array($this->pratica));
            $this->prev=$stmt->fetchColumn();
        }

        //ESTRAGGO INFORMAZIONI SUL DIRIGENTE

        $sql="SELECT userid as dirigente FROM admin.users WHERE attivato=1 and '13' = ANY(string_to_array(coalesce(gruppi,''),','));";
        $stmt = $conn->prepare($sql);
        $stmt->execute(Array($this->pratica));
        $this->info['dirigente']=$stmt->fetchColumn();
        //ESTRAGGO INFORMAZIONI SUL RESPONSABILE DEL SERVIZIO
        $sql="SELECT userid as rds FROM admin.users WHERE attivato=1 and '15' = ANY(string_to_array(coalesce(gruppi,''),','));";
        $stmt = $conn->prepare($sql);
        $stmt->execute(Array($this->pratica));
        $this->info['rds']=$stmt->fetchColumn();
        //INFO UTENTE (ID-GRUPPI-NOME)
        $this->userid=$_SESSION['USER_ID'];
        $this->usergroups=$_SESSION['GROUPS'];
        $sql="SELECT username FROM admin.users WHERE userid=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute(Array($this->userid));
        $this->user=$stmt->fetchColumn();

    }
    
    private function initCdu(){
        $db=$this->db1;
        $this->tipopratica='cdu';
        if($this->pratica){
            $sql="select protocollo,date_part('year',data) as anno FROM cdu.richiesta WHERE pratica=?";
            $r=$db->fetchAssoc($sql,Array($this->pratica));
            $this->info=$r;
            extract($r);
            $arrDir=Array(DATA_DIR,'praticaweb','documenti','cdu',$anno);
            $this->annodir=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;
            $arrDir[]=$protocollo;
            $this->documenti=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;
            $this->url_documenti="/documenti/cdu/$anno/$protocollo/";
            $this->smb_documenti=SMB_PATH."$anno/$protocollo/";
        }
    }
	
    private function initCE(){
        $conn = utils::getDb();
        if ($this->pratica && is_numeric($this->pratica)){
            //INFORMAZIONI SULLA PRATICA
            $sql="SELECT A.pratica,C.nome,A.numero,A.data_convocazione,A.ora_convocazione,date_part('year',data_convocazione) as anno,A.sede1 as sede,C.tipologia FROM ce.commissione A inner join pe.e_enti B ON(A.tipo_comm=B.id) inner join ce.e_tipopratica C ON(B.codice=C.tipologia)  WHERE A.pratica=?";
            $stmt = $conn->prepare($sql);
            if (!$stmt->execute(Array($this->pratica))){
                return;
            }
            $r=$stmt->fetch(PDO::FETCH_ASSOC);
            $this->info=$r;
            $this->titolo=sprintf("%s n° %s del %s",$r["tipo_pratica"],$r["numero"],$r["data_convocazione"]);
            /*if($this->info['tipo'] < 10000 || in_array($this->info['tipo'],Array(14000,15000))){
                    $this->tipopratica='pratica';
            }
            elseif($this->info['tipo'] < 13000){
                    $this->tipopratica='dia';
            }
            else{
                    $this->tipopratica='ambientale';
            }*/
            $this->tipopratica=$info["tipologia"];
            $numero=appUtils::normalizeNumero($this->info['numero']);
            $tmp=explode('-',$numero);
            if (count($tmp)==2 && preg_match("|([A-z0-9]+)|",$tmp[0])){
                    $tmp[0]=(preg_match("|^[89]|",$tmp[0]))?("19".$tmp[0]):($tmp[0]);
                    $numero=implode('-',$tmp);
            }
            $anno=($r['anno'])?($r['anno']):($tmp[0]);

            //Struttura delle directory
            //$arrDir=Array('/data','sanremo','pe','praticaweb','documenti','pe',$anno);
            $arrDir=Array(DATA_DIR,'praticaweb','documenti','ce',$anno);
            $this->annodir=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;
            $arrDir[]=$numero;
            $this->documenti=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;
            $arrDir[]="allegati";
            $this->allegati=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;
            $arrDir[]="tmb";
            $this->allegati_tmb=implode(DIRECTORY_SEPARATOR,$arrDir).DIRECTORY_SEPARATOR;

            $this->url_documenti="/documenti/ce/$anno/$numero/";
            $this->url_allegati="/documenti/ce/$anno/$numero/allegati/";
            $this->smb_documenti=SMB_PATH."$anno/$numero/";


            //INFO PRATICA PREC E SUCC
            $sql="SELECT max(pratica) as pratica FROM pe.avvioproc WHERE pratica < ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute(Array($this->pratica));
            $this->prev=$stmt->fetchColumn();
            $sql="SELECT min(pratica) as pratica FROM pe.avvioproc WHERE pratica > ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute(Array($this->pratica));
            $this->prev=$stmt->fetchColumn();
        }
        
    }
    
    
    function createStructure(){
        if($this->pratica){

            if(!file_exists($this->annodir)) {
                    mkdir($this->annodir);
                    chmod($this->annodir,0777);
                    print (!file_exists($this->annodir))?("Errore nella creazione della cartella $this->annodir\n"):("");
            }
            if(!file_exists($this->documenti)) {
                    mkdir($this->documenti);
                    chmod($this->documenti,0777);
                    print (!file_exists($this->documenti))?("Errore nella creazione della cartella $this->documenti\n"):("Cartella $this->documenti creata con successo\n");
            }
            if($this->allegati && !file_exists($this->allegati)) {
                    mkdir($this->allegati);
                    chmod($this->allegati,0777);
                    //print (!file_exists($this->allegati))?("Errore nella creazione della cartella $this->allegati\n"):("Cartella $this->allegati creata con successo\n");
            }
            if($this->allegati_tmb && !file_exists($this->allegati_tmb)){
                    mkdir($this->allegati_tmb);
                    chmod($this->allegati_tmb,0777);
                    //print (!file_exists($this->allegati_tmb))?("Errore nella creazione della cartella $this->allegati_tmb\n"):("Cartella $this->allegati_tmb creata con successo\n");

            }
        }
    }
	
	//Cancellazione della Pratica
    static function delete($id){
        $db=pratica::setDB();
        //$sql="DELETE FROM pe.avvioproc WHERE pratica=$id;";
        if($db->delete('pe.avvioproc',array($id))){
            system("rm -rf ".$this->documenti); 
        }
    }
	
	function removeStructure(){
		rmdir($this->allegati_tmb);
		rmdir($this->allegati);
		rmdir($this->documenti);
	}
	
	function nuovaPratica($arrInfo){
            //Creazione Struttura nuova Pratica
            $this->createStructure();	
	}
	private function setAllegati($list=Array()){
            if(!$list){
                    $db=$this->db1;
                    $ris=$db->fetchAll("select $this->pratica as pratica,id as documento,1 as allegato,$this->userid as uidins,".time()." as tmsins from pe.e_documenti where default_ins=1");
                    for ($i=0;$i<count($ris);$i++) $db->insert("pe.allegati",$ris[$i]);
            }
	}
	function addRecenti(){
            if (!is_numeric($this->pratica)) return;
            $db=$this->db1;
            $pr=$db->fetchColumn("select coalesce(pratica,0) from pe.recenti where utente=? and pratica=?",Array($this->userid,$this->pratica));
            if($pr){
                $db->update("pe.recenti",Array("data"=>time()),Array("utente"=>$this->userid,"pratica"=>$this->pratica));
            }
            else{
                $tot=$db->fetchColumn("select count(*) from pe.recenti where utente=?",Array($this->userid));
                if((int)$tot > 10){
                        $d=$db->fetchColumn("SELECT min(data) FROM pe.recenti WHERE utente=?",Array($this->userid));
                        $db->delete("pe.recenti",Array("utente"=>$this->userid,"data"=>$d));
                }
                $db->insert("pe.recenti",Array("pratica"=>$this->pratica,"data"=>time(),"utente"=>$this->userid));
            }
	}
	
	//Aggiunge Un record all'iter
    function addIter($testoview,$testoedit){
        $db=$this->db;
        $usr=$_SESSION['USER_NAME'];
        
        $today=date('j-m-y'); 
        $sql="INSERT INTO pe.iter(pratica,data,utente,nota,nota_edit,uidins,tmsins,stampe,immagine) VALUES($this->pratica,'$today','$usr','$testoview','$testoedit',$this->userid,".time().",null,'laserjet.gif');";
        $db->sql_query($sql);
    }
	
	function setDateLavori($data){
            $db=$this->db;	
            $sql="select id from pe.lavori where pratica=$this->pratica";
            $db->sql_query($sql);
            $res=$db->sql_fetchrow();
            // se ho giÃƒÂ  il record esco
		
            if(!$res){
                $sql="SELECT tipo FROM pe.avvioproc WHERE pratica=$this->pratica;";
                $db->sql_query($sql);
                $tipo=$db->sql_fetchfield('tipo');
                switch($tipo){
                    case "2000":
                    case "2050":
                    case "2070":
                    case "2100":
                    case "2150":
                    case "2170":
                    case "2180":
                    case "2190":
                        $sql="insert into pe.lavori (pratica,scade_il,scade_fl,uidins,tmsins) values ($this->pratica,'$data'::date + INTERVAL '1 year', '$data'::date + INTERVAL '3 year',".$_SESSION["USER_ID"].",".time().");";

                        $db->sql_query($sql);
                        //INSERIMENTO SCADENZE RATE ONERI URBANIZZAZIONE E CORRISPETTIVO MONETARIO
                        //$db->sql_query($sql);
                        break;
                    case "10000":
                    case "10100":
                        $sql="insert into pe.lavori (pratica,scade_il,scade_fl,uidins,tmsins) values ($this->pratica,('$data'::date + INTERVAL '1 year 30 day')::date, ('$data'::date + INTERVAL '3 year 30 day')::date,".$_SESSION["USER_ID"].",".time().");";
                        $db->sql_query($sql);
                        //INSERIMENTO SCADENZE RATE ONERI URBANIZZAZIONE E CORRISPETTIVO MONETARIO
                        //$this->setDateRateCM($data);
                        //$this->setDateRateOC($data);
                        break;
                    default:
                        break;
                }
                    //INSERIMENTO SCADENZE DATE INIZIO E FINE LAVORI
            }
	}
        function addScadenze($form,$tipo=null){
            $tipo=($tipo)?($tipo):($this->info['tipo']);
            $db=$this->db1;
            $ris=$db->fetchAll("SELECT DISTINCT scadenza,testo FROM pe.tipopratica_scadenze WHERE tipo=? AND form=?",Array($tipo,$form));
        }
/*********************************************************************************************************/	
/*------------------------------------     TITOLO         -----------------------------------------------*/
/*********************************************************************************************************/
	

	function removeTitolo(){
		$db=$this->db1;
		$db->delete("pe.lavori",Array("pratica"=>$this->pratica));
		$db->update("oneri.rate",Array("data_scadenza"=>null),Array("pratica"=>$this->pratica));
	}
	
/*********************************************************************************************************/	
/*------------------------------------     ONERI          -----------------------------------------------*/
/*********************************************************************************************************/
	
	//Calcolo Corrispettivo Monetario
	function setCM(){
		$db=$this->db;
		$sql="UPDATE oneri.c_monetario SET totale_noscomputo = round(coalesce(sup_cessione*$this->cm_mq,0),2),totale = round(coalesce(sup_cessione*$this->cm_mq,0),2)-coalesce(scomputo,0) WHERE pratica=$this->pratica;";

		$db->sql_query($sql);
	}
	
	//Calcolo rate Corrispettivo Monetario
	function setRateCM(){
		$db=$this->db;
        $t=time();
		$sql="DELETE FROM oneri.rate WHERE pratica=$this->pratica and rata in (5,6);
INSERT INTO oneri.rate(pratica,rata,totale,uidins,tmsins) (
(SELECT $this->pratica as pratica,5 as rata,(totale*0.5),$this->userid,$t FROM oneri.c_monetario WHERE pratica=$this->pratica)
UNION
(SELECT $this->pratica as pratica,6 as rata,(totale*0.5),$this->userid,$t FROM oneri.c_monetario WHERE pratica=$this->pratica));";
		$db->sql_query($sql);
		
		
		
		$menu=new Menu('pratica','pe');
		$menu->add_menu($this->pratica,'120');
        $menu->add_menu($this->pratica,'130');
		
	}
	//Calcolo date scadenza rate CM
	function setDateRateCM($data){
		if($data){
			$db=$this->db;
			$sql="UPDATE oneri.rate SET data_scadenza='$data'::date WHERE pratica=$this->pratica  and rata=5;";
			$sql.="UPDATE oneri.rate SET data_scadenza='$data'::date + INTERVAL '1 year' WHERE pratica=$this->pratica  and rata=6;";
			$db->sql_query($sql);
		}
	}
	//Calcolo della Fideiussione CM
	function setFidiCM(){
		$db=$this->db;
		$sql="UPDATE oneri.c_monetario SET fideiussione=(SELECT totale-coalesce(versato,0) from oneri.rate where pratica=$this->pratica and rata=6) WHERE pratica=$this->pratica;";
		//echo $sql;        
		$db->sql_query($sql);
	}
	//Calcolo Totale Oneri Costruzione
	function setOC(){
		$db=$this->db;
		$sql="UPDATE oneri.oneri_concessori SET totale = coalesce(oneri_urbanizzazione,0) + coalesce(oneri_costruzione,0)-(coalesce(scomputo_urb,0)+coalesce(scomputo_costr,0)) WHERE pratica=$this->pratica;";
		$db->sql_query($sql);
	}
	
	//Calcolo Rate Oneri Costruzione
    function setRateOC($rateizzato=1){
		//$this->setOC();
        $db=$this->db;
        $t=time();
		if($rateizzato==1)	// <---- MODIFICA DEL 21/06/2012
			$sql="DELETE FROM oneri.rate WHERE pratica=$this->pratica and rata in (1,2,3,4);
INSERT INTO oneri.rate(pratica,rata,totale,uidins,tmsins) (
(SELECT $this->pratica as pratica,1 as rata,totale/3,$this->userid,$t FROM oneri.vista_totali WHERE pratica=$this->pratica)
UNION
(SELECT $this->pratica as pratica,2 as rata,totale/3,$this->userid,$t FROM oneri.vista_totali WHERE pratica=$this->pratica)
UNION
(SELECT $this->pratica as pratica,3 as rata,totale/3,$this->userid,$t FROM oneri.vista_totali WHERE pratica=$this->pratica)
);";
		else
			$sql="DELETE FROM oneri.rate WHERE pratica=$this->pratica and rata in (1,2,3,4);
INSERT INTO oneri.rate(pratica,rata,totale,uidins,tmsins) (SELECT $this->pratica as pratica,4 as rata,((coalesce(oneri_urbanizzazione,0)-coalesce(scomputo_urb,0))+(coalesce(oneri_costruzione,0)-coalesce(scomputo_costr,0))),$this->userid,$t FROM oneri.oneri_concessori WHERE pratica=$this->pratica);";
        $db->sql_query($sql);
		
		$menu=new Menu('pratica','pe');
		$menu->add_menu($this->pratica,'120');
        $menu->add_menu($this->pratica,'130');
    }
	//Calcolo date scadenza rate OC
	function setDateRateOC($data){
		$db=$this->db;
		if($data){
			$sql="UPDATE oneri.rate SET data_scadenza='$data'::date WHERE pratica=$this->pratica and rata=1;";
			$sql.="UPDATE oneri.rate SET data_scadenza='$data'::date + INTERVAL '1 year' WHERE pratica=$this->pratica and rata=2;";
			$sql.="UPDATE oneri.rate SET data_scadenza='$data'::date + INTERVAL '3 year' WHERE pratica=$this->pratica and rata=3;";
			$db->sql_query($sql);
		}
	}
	//Calcolo della Fideiussione OC
	function setFidiOC(){
		$db=$this->db;
		$sql="UPDATE oneri.oneri_concessori SET fideiussione=coalesce((SELECT sum(totale-coalesce(versato,0)) FROM oneri.rate WHERE rata in (2,3) and pratica=$this->pratica),0) WHERE pratica=$this->pratica;";
        $db->sql_query($sql);
	}
	
/*********************************************************************************************************/	
/*------------------------------------     WORKFLOW       -----------------------------------------------*/
/*********************************************************************************************************/	
	
	function setMansione($m,$usr,$d,$fr = NULL,$note = ''){
		$from=($fr)?($fr):($_SESSION['USER_ID']);
		$d=($d)?(($d=='CURRENT_DATE')?($d):("'$d'::date")):("null");
		$db=$this->db;	
		$sql="INSERT INTO pe.movimenti_pratiche(pratica,da_utente,a_utente,data,motivo,note,uidins,tmsins) VALUES($this->pratica,$from,$usr,$d,(select id from pe.e_statipratica where codice='$m'),'$note',$from,".time().");";
		$db->sql_query($sql);
	}
	function removeMansione($m){
		if($m){
			$db=$this->db;	
			$sql="DELETE FROM pe.movimenti_pratiche WHERE pratica=$this->pratica and motivo = (select id from pe.e_statipratica where codice='$m')";
			$db->sql_query($sql);
		}
	}
	
	/* WORKFLOW Da Mettere*/
	
	function addRole($role,$usr,$d){
		$t=time();
		$db=$this->db1;
		$data=($d)?(($d=='CURRENT_DATE')?('now'):($d)):('now');
		$arrDati=Array(
			'pratica'=>$this->pratica,
			'role'=>$role,
			'utente'=>$usr,
			'data'=>$data,
			'tmsins'=>$t,
			'uidins'=>$this->userid
		);
		$db->insert('pe.wf_roles', $arrDati);
	}
	function delRole($role){
		$db=$this->db1;
		$db->delete('pe.wf_roles',Array('pratica'=>$this->pratica,'role'=>$role));
	}
	function addTransition($prms){
		$db=$this->db1;
		$initVal=Array("pratica"=>$this->pratica,'codice'=>null,'utente_in'=>$this->userid,'utente_fi'=>null,'data'=>"now",'stato_in'=>null,'stato_fi'=>null,'note'=>null,'tmsins'=>time(),'uidins'=>$this->userid);
		foreach($initVal as $key=>$val) $params[$key]=(in_array($key,array_keys($prms)) && $prms[$key])?($prms[$key]):($val);
		$params['note']=($params['note'])?($db->quote($params['note'])):($params['note']);
		$cod=$params['codice'];
		
		if($db->insert("pe.wf_transizioni",$params)){
			switch($cod){
				case "ardp":
				case "aitec":
				case "aiamm":
				case "aipre":
				case "aiagi":
				case "ailav":
					$this->addRole(substr($cod,1),$params['utente_fi'],$params['data']);
					break;
				case "rardp":
				case "raitec":
				case "raiamm":
					$this->delRole(substr($cod,2));
					$this->addRole(substr($cod,2),$params['utente_fi'],$params['data']);
					break;
				default:
					break;
			}
		}
		
	}
	function delTransition($id=null,$cod=null){
		$db=$this->db1;
		$filter=($id)?(Array('pratica'=>$this->pratica,'id'=>$id)):(Array('pratica'=>$this->pratica,'cod_transizione'=>$cod));
		$db->delete('pe.wf_transizioni',$filter);
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
	
	
	
	
	
	function getLastId($tab){
		list($sk,$tb)=explode('.',$tab);
		$db=$this->db1;
		$sql="select array_to_string(regexp_matches(column_default, 'nextval[(][''](.+)['']::regclass[)]'),'') as sequence from information_schema.columns where table_schema=? and table_name=? and column_default ilike 'nextval%'";
		$sequence=$db->fetchColumn($sql,Array($sk,$tb));
		return $db->fetchColumn("select currval('$sequence')");
	}
	
	/*-----------------------------------------------------------------------------------------*/
	
	static function getStato($id){
		$db=pratica::setDB();
		$sql="SELECT codice,data,descrizione FROM pe.elenco_transizioni_pratiche WHERE pratica=? order by data DESC,tmsins DESC LIMIT 1;";
		$ris=$db->fetchAssoc($sql,Array($id));
		return $ris;
	}
	
        
}



?>
