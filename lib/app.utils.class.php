<?php
use Doctrine\Common\ClassLoader;
require_once APPS_DIR.'plugins/Doctrine/Common/ClassLoader.php';
require_once LIB."utils.class.php";
require_once APPS_DIR.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'app.utils.class.php';
class generalAppUtils {
   static function getDB(){
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
    static function getLastId($db,$tab,$sk=null,$tb=null){
		if(!$sk || !$tb) list($sk,$tb)=explode('.',$tab);
		//$db=self::getDB();
		$sql="select array_to_string(regexp_matches(column_default, 'nextval[(][''](.+)['']::regclass[)]'),'') as sequence from information_schema.columns where table_schema=? and table_name=? and column_default ilike 'nextval%'";
		$sequence=$db->fetchColumn($sql,Array($sk,$tb));
		return $db->fetchColumn("select currval('$sequence')");
	}
    
    static function isNumeric($v){
        try{
            $value=self::toNumber($v);
            return (int)(is_numeric($value));
        }
        catch(Exception $e){
            return 0;
        }
    }
    static function toNumber($v){
        return str_replace(",",".",$v);
    }
    
    static function getUserId(){
        return $_SESSION["USER_ID"];
    }
    
    static function getUserName(){
        return $_SESSION["USER_NAME"];
    }
/*-------------------------------------------------------------------------------*/    
    static function normalizeNumero($numero){
        return preg_replace("|([^A-z0-9\-]+)|",'',str_replace('/','-',str_replace('\\','-',$numero)));
    }
    static function getAnno($numero){
        $numero=self::normalizeNumero($numero);
    }

    
    
/*-------------------------------------------------------------------------------------------*/
    static function getInfoPratica($pratica){
        $db=self::getDb();
        $sql="SELECT numero,tipo,resp_proc,resp_it,resp_ia,date_part('year',data_presentazione) as anno,data_presentazione,data_prot FROM pe.avvioproc  WHERE pratica=?";
		$r=$db->fetchAssoc($sql, Array($pratica));
        //ESTRAGGO INFORMAZIONI SUL DIRIGENTE
		$sql="SELECT userid as dirigente FROM admin.users WHERE attivato=1 and '13' = ANY(string_to_array(coalesce(gruppi,''),','));";
		$dirig=$db->fetchColumn($sql);
		$r['dirigente']=$dirig;
		//ESTRAGGO INFORMAZIONI SUL RESPONSABILE DEL SERVIZIO
		$sql="SELECT userid as rds FROM admin.users WHERE attivato=1 and '15' = ANY(string_to_array(coalesce(gruppi,''),','));";
		$rds=$db->fetchColumn($sql);
		$r['rds']=$rds;
		return $r;
    }
    
    static function getStato($id){
		$db=pratica::setDB();
		$sql="SELECT codice,data,descrizione FROM pe.elenco_transizioni_pratiche WHERE pratica=? order by data DESC,tmsins DESC LIMIT 1;";
		$ris=$db->fetchAssoc($sql,Array($id));
		return $ris;
	}
    
    static function getIdTrans($m){
        $db=self::getDb();
        $id=$db->fetchColumn("SELECT id FROM pe.e_transizioni WHERE codice=?",Array($m),0);
        return $id;
    }
	
   static function getCodBelfiore($pratica){
	  if (defined('COD_BELFIORE') && COD_BELFIORE){
		 return COD_BELFIORE;   
	  }
	  elseif(in_array("cod_belfiore",$_REQUEST) && $_REQUEST["cod_belfiore"]){
		 return $_REQUEST["cod_belfiore"];
	  }
	  else{
		 $db = utils::getDb();
		 $sql="SELECT cod_belfiore FROM pe.avvioproc WHERE pratica=?;";
		 $stmt=$db->prepare($sql);
		 $cod = '';
		 if($stmt->execute(Array($pratica))){
			$cod = $stmt->fetchColumn();
		 }
		 return $cod;
	  }
	}
/*--------------------------------------------------------------------------------------------*/  
    static function getPraticaRole($cfg,$pratica){
        $db=self::getDB();
		//Recupero il responsabile del procedimento
		$rdp=$db->fetchColumn("SELECT resp_proc FROM pe.avvioproc WHERE pratica=?",Array($pratica),0);
		
		//Verifico il dirigente
		$idDiri=$db->fetchColumn("SELECT userid FROM admin.users WHERE (SELECT DISTINCT id::varchar FROM admin.groups WHERE nome='dirigenza')=ANY(string_to_array(coalesce(gruppi,''),','))",Array(),0);
		/*$db->sql_query($sql);
		$idDiri=$db->sql_fetchfield('userid');*/
		
		//Verifico il responsabile del Servizio
		$idRds=$db->fetchColumn("SELECT userid FROM admin.users WHERE (SELECT DISTINCT id::varchar FROM admin.groups WHERE nome='rds')=ANY(string_to_array(coalesce(gruppi,''),','))",Array(),0);
		/*$db->sql_query($sql);
		$idRds=$db->sql_fetchfield('userid');*/
        
        //Verifico gli archivisti
		$sql="SELECT userid FROM admin.users WHERE (SELECT DISTINCT id::varchar FROM admin.groups WHERE nome='archivio')=ANY(string_to_array(coalesce(gruppi,''),','));";
		$r=$db->fetchAll($sql);
        for($i=0;$i<count($r);$i++){
            $idArch[]=$r[$i];
            $roles[$r[$i]]="archivio";
            $ris[]=$r[$i];
        }
		//Array con tutti i ruoli
        $supRoles=Array($rdp,$idRds,$idDiri);
		$ris=Array($rdp,$idRds,$idDiri);
		
		$sql="SELECT role,utente FROM pe.wf_roles WHERE pratica=?";
        $res=$db->fetchAll($sql,Array($pratica));
        $roles[$idDiri]=Array('dir');
		$roles[$idRds]=Array('rds');
        
        for($i=0;$i<count($res);$i++){
				$r=$res[$i];
				$roles[$r['utente']][]=$r['role'];
				$ris[]=$r['utente'];
			}
		if(count($res)){
			if (in_array($_SESSION["USER_ID"],$ris) or $_SESSION["PERMESSI"]<2)
				$owner=1;
			else
				$owner=2;
		}
		else
			$owner=3;
        
        return Array("roles"=>$roles,"owner"=>$owner,"ris"=>$ris,"editor"=>$supRoles);
    }
/*---------------------------------------------------------------------------------------------*/    
    static function addRole($pratica,$role,$usr,$d){
		$t=time();
		$db=self::getDB();
		$data=($d)?(($d=='CURRENT_DATE')?('now'):($d)):('now');
		$arrDati=Array(
			'pratica'=>$pratica,
			'role'=>$role,
			'utente'=>$usr,
			'data'=>$data,
			'tmsins'=>$t,
			'uidins'=>self::getUserId()
		);
		$db->insert('pe.wf_roles', $arrDati);
	}
	static function delRole($pratica,$role){
		$db=self::getDB();
		$db->delete('pe.wf_roles',Array('pratica'=>$pratica,'role'=>$role));
	}
    
    
    static function addTransition($pratica,$prms){
        $db=self::getDb();
        $userid=appUtils::getUserId();
		$initVal=Array("pratica"=>$pratica,'codice'=>null,'utente_in'=>$userid,'utente_fi'=>null,'data'=>"now",'stato_in'=>null,'stato_fi'=>null,'note'=>null,'tmsins'=>time(),'uidins'=>$userid);
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
					self::addRole($pratica,substr($cod,1),$params['utente_fi'],$params['data']);
					break;
				case "rardp":
				case "raitec":
				case "raiamm":
					self::delRole($pratica,substr($cod,2));
					self::addRole($pratica,substr($cod,2),$params['utente_fi'],$params['data']);
					break;
				default:
					break;
			}
		}
		
	}
	static function delTransition($pratica,$id=null){
		$db=self::getDb();
        $isCodice=(is_numeric($id))?(0):(1);
		$filter=($isCodice)?(Array('pratica'=>$pratica,'id'=>$id)):(Array('pratica'=>$pratica,'codice'=>$id));
		$db->delete('pe.wf_transizioni',$filter);
	}
    
    static function addIter($pratica,$prms){
        $db=self::getDb();
        $usr=self::getUserName();
        $initVal=Array("pratica"=>$pratica,'data'=>'now()','utente'=>$usr,'nota'=>null,'nota_edit'=>null,'stampe'=>null,'immagine'=>'laserjet.gif','tmsins'=>time(),'uidins'=>$userid);
        foreach($initVal as $key=>$val) $params[$key]=(in_array($key,array_keys($prms)) && $prms[$key])?($prms[$key]):($val);
		//$params['nota']=($params['nota'])?($db->quote($params['nota'])):($params['nota']);
        //$params['nota_edit']=($params['nota_edit'])?($db->quote($params['nota_edit'])):($params['nota_edit']);
		$db->insert("pe.iter",$params);
    }
    
    
    static function setPrmProgCalcolati($pratica,$data){
        $db=self::getDB();
        $table="pe.parametri_prog";
        $sql="select distinct id,codice from pe.e_parametri order by 2;";
        $res=$db->fetchAll($sql);
        for($i=0;$i<count($res);$i++) $e_prm[$res[$i]["codice"]]=$res[$i]["id"];
        $sql="select distinct B.id,A.codice from pe.e_parametri A inner join pe.parametri_prog B on(A.id=B.parametro) order by 2;";
        $res=$db->fetchAll($sql);
        for($i=0;$i<count($res);$i++) $prms[$res[$i]["codice"]]=$res[$i]["id"];
        $params=array_keys($data);
        
        //Volume Totale
        if (self::isNumeric($data[$prms["ve"]]) && self::isNumeric($data[$prms["vp"]]) && self::isNumeric($data[$prms["vd"]])){
            $v=(double)self::toNumber($data[$prms["ve"]])+(double)self::toNumber($data[$prms["vp"]])-(double)self::toNumber($data[$prms["vd"]]);
            try{
                $db->insert($table,Array("pratica"=>$pratica,"parametro"=>$e_prm["v"],"valore"=>$v));
                $lastid=self::getLastId($db,$table);
                $prms["v"]=$lastid;
                $data[$lastid]=$v;
            }
            catch(Exception $e){}
        }
        //Indice di Fabbricabilità
        if (self::isNumeric($data[$prms["v"]]) && self::isNumeric($data[$prms["slot"]])){
            $v=(double)self::toNumber($data[$prms["v"]])/(double)self::toNumber($data[$prms["slot"]]);
            try{
                $db->insert($table,Array("pratica"=>$pratica,"parametro"=>$e_prm["iif"],"valore"=>$v));
                $lastid=self::getLastId($db,$table);
                $prms["iif"]=$lastid;
                $data[$lastid]=$v;
            }
            catch(Exception $e){}
        }
        
        //Indice di copertura
        if (self::isNumeric($data[$prms["sc"]]) && self::isNumeric($data[$prms["slot"]])){
            $v=((double)self::toNumber($data[$prms["sc"]])/(double)self::toNumber($data[$prms["slot"]]))*100;
            try{
                $db->insert($table,Array("pratica"=>$pratica,"parametro"=>$e_prm["ic"],"valore"=>$v));
                $lastid=self::getLastId($db,$table);
                $prms["ic"]=$lastid;
                $data[$lastid]=$v;
            }
            catch(Exception $e){}
        }
        //Superficie Coperta Totale
        if (self::isNumeric($data[$prms["sce"]]) && self::isNumeric($data[$prms["scp"]]) && self::isNumeric($data[$prms["scd"]])){
            $v=(double)self::toNumber($data[$prms["sce"]])+(double)self::toNumber($data[$prms["scp"]])-(double)self::toNumber($data[$prms["scd"]]);
            try{
                $db->insert($table,Array("pratica"=>$pratica,"parametro"=>$e_prm["sc"],"valore"=>$v));
                $lastid=self::getLastId($db,$table);
                $prms["sc"]=$lastid;
                $data[$lastid]=$v;
            }
            catch(Exception $e){}
        }
        //Superficie Utile Totale
        if (self::isNumeric($data[$prms["sue"]]) && self::isNumeric($data[$prms["sup"]]) && self::isNumeric($data[$prms["sud"]])){
            $v=(double)self::toNumber($data[$prms["sue"]])+(double)self::toNumber($data[$prms["sup"]])-(double)self::toNumber($data[$prms["sud"]]);
            try{
                $db->insert($table,Array("pratica"=>$pratica,"parametro"=>$e_prm["su"],"valore"=>$v));
                $lastid=self::getLastId($db,$table);
                $prms["su"]=$lastid;
                $data[$lastid]=$v;
            }
            catch(Exception $e){}
        }
        
        //Indice di utilizzo fondiario
        if (self::isNumeric($data[$prms["su"]]) && self::isNumeric($data[$prms["sf"]])){
            $v=(double)self::toNumber($data[$prms["su"]])/(double)self::toNumber($data[$prms["sf"]]);
            try{
                $db->insert($table,Array("pratica"=>$pratica,"parametro"=>$e_prm["uf"],"valore"=>$v));
                $lastid=self::getLastId($db,$table);
                $prms["uf"]=$lastid;
                $data[$lastid]=$v;
            }
            catch(Exception $e){}
        }
        //Indice di copertura esistente
        if (self::isNumeric($data[$prms["sce"]]) && self::isNumeric($data[$prms["slot"]])){
            $v=(double)self::toNumber($data[$prms["sce"]])/(double)self::toNumber($data[$prms["slot"]]);
            try{
                $db->insert($table,Array("pratica"=>$pratica,"parametro"=>$e_prm["ice"],"valore"=>$v));
                $lastid=self::getLastId($db,$table);
                $prms["ice"]=$lastid;
                $data[$lastid]=$v;
            }
            catch(Exception $e){}
        }
        //Volume con indice 3/1
        if (self::isNumeric($data[$prms["slot"]])){
            $v=(double)3*(double)self::toNumber($data[$prms["slot"]]);
            try{
                $db->insert($table,Array("pratica"=>$pratica,"parametro"=>$e_prm["v3_1"],"valore"=>$v));
                $lastid=self::getLastId($db,$table);
                $prms["v3_1"]=$lastid;
                $data[$lastid]=$v;
            }
            catch(Exception $e){}
        }
        //Volume Esistente - Volume da demolire
        if (self::isNumeric($data[$prms["ve"]]) && self::isNumeric($data[$prms["vd"]])){
            $v=(double)self::toNumber($data[$prms["ve"]])-(double)self::toNumber($data[$prms["vd"]]);
            try{
                $db->insert($table,Array("pratica"=>$pratica,"parametro"=>$e_prm["ve_vd"],"valore"=>$v));
                $lastid=self::getLastId($db,$table);
                $prms["ve_vd"]=$lastid;
                $data[$lastid]=$v;
            }
            catch(Exception $e){}
        }
        //Volume Progetto - Volume da demolire
        if (self::isNumeric($data[$prms["vp"]]) && self::isNumeric($data[$prms["vd"]])){
            $v=(double)self::toNumber($data[$prms["vp"]])-(double)self::toNumber($data[$prms["vd"]]);
            try{
                $db->insert($table,Array("pratica"=>$pratica,"parametro"=>$e_prm["vp_vd"],"valore"=>$v));
                $lastid=self::getLastId($db,$table);
                $prms["vp_vd"]=$lastid;
                $data[$lastid]=$v;
            }
            catch(Exception $e){}
        }
    }
    static function titoloPratica($req){

        if (!$_REQUEST["pratica"]) return "";
        $pr=$_REQUEST["pratica"];
        $filename=basename(__FILE__, '.php');

        if ($_REQUEST["cdu"] || strpos($filename,'cdu.')!==FALSE){
            $sql="SELECT 'Certificato di Destinazione Urbanitica Prot n° '||protocollo as titolo FROM cdu.richiesta WHERE pratica=?";
        }
        elseif ($_REQUEST["comm"] || strpos($filename,'ce.')!==FALSE){
            $sql="SELECT B.nome|| ' del '|| to_char(data_convocazione,'DD/MM/YYYY') as titolo FROM ce.commissione A INNER JOIN ce.e_tipopratica B ON(A.tipo=B.id)  WHERE pratica=?;";
        }
        elseif ($_REQUEST["vigi"] || strpos($filename,'vigi.')!==FALSE){
            $sql="SELECT B.nome|| ' n° '||A.numero as titolo FROM vigi.avvioproc A INNER JOIN vigi.e_tipopratica B ON(A.tipo_comm=B.id)  WHERE pratica=?;";
        }
        elseif($_REQUEST["agi"] || strpos($filename,'agi.')!==FALSE){
            $sql="SELECT B.nome|| coalesce(' - '||C.nome,'') ||' n° '||A.numero as titolo FROM agi.avvioproc A INNER JOIN agi.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN agi.e_categoriapratica C ON (coalesce(A.categoria,0)=C.id)  WHERE pratica=?;";
        }
		elseif($_REQUEST["storage"] || strpos($filename,'storage.')!==FALSE){
            $sql="SELECT 'Documentazione inviata il '||data_invio|| ' da '||cognome||' '||nome as titolo FROM storage.invio  WHERE pratica=?;";
        }
        else{
            $sql="SELECT B.nome|| coalesce(' - '||C.nome,'') ||' n° '||A.numero as titolo FROM pe.avvioproc A INNER JOIN pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN pe.e_categoriapratica C ON (coalesce(A.categoria,0)=C.id)  WHERE pratica=?;";
        }
        //echo $pr;
        $db=self::getDb();
        $result=$db->fetchAll($sql,Array($pr));
        return $result[0]["titolo"];
    }
    static function getScadenze($userId){
            $conn=utils::getDb();
            //DETTAGLI DELLE SCADENZE
            $lLimit=(defined('LOWER_LIMIT'))?(LOWER_LIMIT):(5);
            $uLimit=(defined('UPPER_LIMIT'))?(UPPER_LIMIT):(3);
            $sql="select * from pe.vista_scadenze_utenti where $userId = ANY(interessati) and scadenza <= CURRENT_DATE +$lLimit  and scadenza >= CURRENT_DATE -$uLimit and completata=0 order by scadenza";
            
            $stmt=$conn->prepare($sql);
            if(!$stmt->execute()){
                return Array("errore"=>1,"query"=>$sql);
            }
            else{
                $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
                return Array("totali"=>count($res),"data"=>$res);
            }
    }
    static function getVerifiche($userId){
            $conn=utils::getDb();

            $sql="select * from pe.vista_verifiche_utenti where $userId = ANY(interessati);";
            
            $stmt=$conn->prepare($sql);
            if(!$stmt->execute()){
                return Array("errore"=>1,"query"=>$sql);
            }
            else{
                $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
                return Array("totali"=>count($res),"data"=>$res);
            }
    }
    static function getAnnotazioni($userId){
            $conn=utils::getDb();
            //DETTAGLI DELLE SCADENZE
            $lLimit=(defined('LOWER_LIMIT'))?(LOWER_LIMIT):(5);
            $uLimit=(defined('UPPER_LIMIT'))?(UPPER_LIMIT):(3);
            $sql="select * from pe.vista_pratiche_online_daassegnare where $userId = ANY(interessati);";
            
            $stmt=$conn->prepare($sql);
            if(!$stmt->execute()){
                return Array("errore"=>1,"query"=>$sql);
            }
            else{
                $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
                return Array("totali"=>count($res),"data"=>$res,"sql"=>$sql);
            }
    }
    
    static function chooseRespVerifiche($tipo){
        $sql = "SELECT resp_proc FROM pe.e_verifiche WHERE id=?";
        $conn=utils::getDb();
        $stmt=$conn->prepare($sql);
        if(!$stmt->execute(Array($tipo))){
            return -1;
        }
        else{
            $res=$stmt->fetchColumn();
            return $res;
        }

    }
    
    static function getAnnoOneri($id,$data){
        $conn=utils::getDb();
        $sql = "SELECT DISTINCT anno FROM oneri.e_tariffe WHERE valido_da <= ? AND valido_a >= ?;";
        $stmt=$conn->prepare($sql);
        if(!$stmt->execute(Array($data,$data))){
            return -1;
        }
        else{
            $res=$stmt->fetchColumn();
            return $res;
        }
    }
    static function setVisitata($id,$frm,$user){
        $sql="INSERT INTO pe.pratiche_visitate(pratica,form,userid) VALUES(?,?,?)";
        $conn=utils::getDb();
        $stmt=$conn->prepare($sql);
        $stmt->execute(Array($id,$frm,$user));
    }
    static function getNotifiche($userId){
            $conn=utils::getDb();
            //DETTAGLI DELLE SCADENZE
            $lLimit=(defined('LOWER_LIMIT'))?(LOWER_LIMIT):(5);
            $uLimit=(defined('UPPER_LIMIT'))?(UPPER_LIMIT):(3);
            $sql="select A.id,A.pratica,B.numero,B.data_prot,testo as oggetto,ARRAY[soggetto_notificato] as interessati from pe.notifiche A inner join pe.avvioproc B using(pratica) where soggetto_notificato=$userId and visionato=0;";
            
            $stmt=$conn->prepare($sql);
            if(!$stmt->execute()){
                return Array("errore"=>1,"query"=>$sql);
            }
            else{
                $res=$stmt->fetchAll(PDO::FETCH_ASSOC);
                return Array("totali"=>count($res),"data"=>$res);
            }
    }
    static function getInterventiOneri(){
    	$db = self::getDB();
    	$sql="SELECT * FROM oneri.e_interventi order by tabella,descrizione";
    	$res=$db->fetchAll($sql);
    	foreach($res as $val){
    		$result[$val["tabella"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
    	}
    	return $result;
    }
    static function getTariffeOneri(){
    	$db = self::getDB();
	    $sql="SELECT * FROM oneri.e_tariffe order by anno,tabella,descrizione";
	    $res=$db->fetchAll($sql);
	    foreach($res as $val){
	    	$result[$val["anno"]][]=Array("id"=>$val["tabella"],"opzione"=>$val["descrizione"]);
	    }
	    return $result;
    }
    static function getC1Oneri(){
    	$db = self::getDB();
    	$sql="SELECT * FROM oneri.e_c1 order by tabella,descrizione";
    	$res=$db->fetchAll($sql);
    	foreach($res as $val){
    		$result[$val["tabella"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
    	}
    	return $result;
    }
    static function getC2Oneri(){
    	$db = self::getDB();
    	$sql="SELECT * FROM oneri.e_c2 order by tabella,descrizione";
    	$res=$db->fetchAll($sql);
    	foreach($res as $val){
    		$result[$val["tabella"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
    	}
    	return $result;
    }
    static function getC3Oneri(){
    	$db = self::getDB();
    	$sql="SELECT * FROM oneri.e_c3 order by tabella,descrizione";
    	$res=$db->fetchAll($sql);
    	foreach($res as $val){
    		$result[$val["tabella"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
    	}
    	return $result;
    }
    static function getC4Oneri(){
    	$db = self::getDB();
    	$sql="SELECT * FROM oneri.e_c4 order by tabella,descrizione";
    	$res=$db->fetchAll($sql);
    	foreach($res as $val){
    		$result[$val["tabella"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
    	}
    	return $result;
    }
    static function getC5Oneri(){
    	$db = self::getDB();
    	$sql="SELECT * FROM oneri.e_c5 order by tabella,descrizione";
    	$res=$db->fetchAll($sql);
    	foreach($res as $val){
    		$result[$val["tabella"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
    	}
    	return $result;
    }
    static function getD1Oneri(){
    	$db = self::getDB();
    	$sql="SELECT * FROM oneri.e_d1 order by tabella,descrizione";
    	$res=$db->fetchAll($sql);
    	foreach($res as $val){
    		$result[$val["tabella"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
    	}
    	return $result;
    }
    static function getD2Oneri(){
    	$db = self::getDB();
    	$sql="SELECT * FROM oneri.e_d2 order by tabella,descrizione";
    	$res=$db->fetchAll($sql);
    	foreach($res as $val){
    		$result[$val["tabella"]][]=Array("id"=>$val["valore"],"opzione"=>$val["descrizione"]);
    	}
    	return $result;
    }
}

?>
