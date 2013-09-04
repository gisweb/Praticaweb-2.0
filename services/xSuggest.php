<?php
include_once "../login.php";
error_reporting(E_ERROR);
$db=$dbconn;
//$db=new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
//if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
$result=Array();
$field=$_REQUEST['field'];
$value=addslashes($_REQUEST['term']);
switch($field) {
	case 'tavola':
		$sql="SELECT nome_tavola as id,coalesce(descrizione,nome_tavola) as opzione FROM vincoli.tavola WHERE nome_vincolo='$value' or nome_vincolo is null order by ordine,2;";
		if($db->sql_query($sql)){
			$result=Array(Array("id"=>null,"opzione"=>'Seleziona =====>'));
			$res=$db->sql_fetchrowset();//print_array($res);
            for($i=0;$i<count($res);$i++){
				$result[]=Array("id"=>$res[$i]["id"],"opzione"=>$res[$i]["opzione"]);
			}
			$exec=1;
		}
		else
            $result[]=Array(
                "id"=>'',
                "value"=>'',
                "label"=>"Si è verificato un errore nell' esecuzione dell'interrogazione $sql"
                
            );
		break;
	case 'zona':
		$vincolo=addslashes($_REQUEST['vincolo']);

		$sql="SELECT nome_zona as id,coalesce(descrizione,nome_zona) as opzione FROM vincoli.zona WHERE nome_tavola='$value' and nome_vincolo='$vincolo' order by ordine,2;";
		if($db->sql_query($sql)){
			$res=$db->sql_fetchrowset();//print_array($res);
			$result=Array(Array("id"=>null,"opzione"=>'Seleziona =====>'));
            for($i=0;$i<count($res);$i++){
				$result[]=Array("id"=>$res[$i]["id"],"opzione"=>$res[$i]["opzione"]);
			}
			$exec=1;
		}
		else
            $result[]=Array(
                "id"=>'',
                "value"=>'',
                "label"=>"Si è verificato un errore nell' esecuzione dell'interrogazione $sql"
                
            );
		break;
	case 'motivo':
		$motivo=addslashes($_REQUEST['motivo']);
		switch($motivo){
			case "2":
				$sql="select * from admin.elenco_istruttori_tecnici where id=(SELECT resp_it FROM pe.avvioproc where pratica=); ";
				break;
			default:
				$sql="SELECT * FROM admin.elenco_istruttori;";
				
				break;
		}
		if($db->sql_query($sql)){
			$res=$db->sql_fetchrowset();//print_array($res);
			$result=Array(Array("id"=>null,"opzione"=>'Seleziona =====>'));
            for($i=0;$i<count($res);$i++){
				$result[]=Array("id"=>$res[$i]["id"],"opzione"=>$res[$i]["opzione"]);
			}
			$exec=1;
		}
		else
            $result[]=Array(
                "id"=>'',
                "value"=>'',
                "label"=>"Si è verificato un errore nell' esecuzione dell'interrogazione $sql"
                
            );
		break;
    case 'codfis':
        require_once("../calcolacodicefiscale.php");
		$cognome=$_REQUEST['cognome'];
		$nome=$_REQUEST['nome'];
		$sesso=$_REQUEST['sesso'];
		$comune=addslashes($_REQUEST['comunato']);
		$datanascita=$_REQUEST['datanato'];
        $sql="SELECT codice FROM pe.e_comuni WHERE nome ilike '$comune' order by nome";
        if($db->sql_query($sql)){
            //$comune=$db->sql_fetchfield('codice');
            $codfis='';
            $r=new risultato;
            $r=calcolacodicefiscale($cognome,$nome,$sesso,$comune,$datanascita);
            if (sizeof($r->errori)){
                $errors= "Si sono verificati i seguenti errori:";
                reset ($r->errori);
                while (list ($key, $val) = each ($r->errori)) {
                    $errors.= ($key+1)." - $val;\n";
                }
            } 
            else{
                $codfis= $r->codicefiscale;
            }
            $result=Array('value'=>$codfis,'error'=>$errors);
        }
        break;
    case 'comune':
    case 'comuned':
    case 'citta':
    case 'comunato':
        $sql="SELECT * FROM pe.e_comuni WHERE nome ilike '$value%' order by nome";
        $child=Array(
            'comune'=>Array('cap'=>'cap','prov'=>'sigla_prov'),
            'comunato'=>Array('provnato'=>'sigla_prov'),
            'comuned'=>Array('capd'=>'cap','provd'=>'sigla_prov'),
            'citta'=>Array('cap'=>'cap','sigla_prov'=>'prov')
        );
        if($db->sql_query($sql)){
            
            $res=$db->sql_fetchrowset();//print_array($res);
            for($i=0;$i<count($res);$i++){
                $r=Array();
                foreach($child[$field] as $k=>$v) $r[$k]=$res[$i][$v];
                $result[]=Array(
                    "id"=>$res[$i]["codice"],
                    "value"=>$res[$i]["nome"],
                    "label"=>$res[$i]["nome"]." (".$res[$i]["sigla_prov"].") - ".$res[$i]["cap"],
                    "child"=>$r
                );
                
            }
            $exec=1;
        }
        else
            $result[]=Array(
                "id"=>'',
                "value"=>'',
                "label"=>"Si è verificato un errore nell' esecuzione dell'interrogazione"
                
            );
        break;
    case "foglio":
        $sezione=($_REQUEST["sezione"])?($_REQUEST["sezione"]):('%');
        $sql="SELECT DISTINCT foglio as valore,length(foglio) FROM nct.particelle WHERE foglio ilike '%$value%' and (sezione ilike '$sezione' or sezione is null) order by 2,1";
        if($db->sql_query($sql)){
            $res=$db->sql_fetchrowset();
            for($i=0;$i<count($res);$i++){
                $result[]=Array(
                    "id"=>$res[$i]["valore"],
                    "value"=>$res[$i]["valore"],
                    "label"=>$res[$i]["valore"]
                );
                
            }
            $exec=1;
        }
		else
        $result[]=Array(
            "id"=>'',
            "value"=>'',
            "label"=>"Si è verificato un errore nell' esecuzione dell'interrogazione;",
			"query"=>$sql,
			"field"=>$field
        );
        break;
    case 'mappale':
        $fg=(isset($_REQUEST['foglio']))?(addslashes($_REQUEST['foglio'])):('%');
        $sezione=($_REQUEST["sezione"])?($_REQUEST["sezione"]):('%');
        $sql="SELECT DISTINCT mappale as valore,CASE WHEN (regexp_replace(mappale,'([^0-9]+)','','g'))='' THEN 0 ELSE regexp_replace(mappale,'([^0-9]+)','','g')::integer end FROM nct.particelle WHERE mappale ilike '$value%' and foglio ilike '$fg' and (sezione ilike '$sezione' or sezione is null) order by 2";

        if($db->sql_query($sql)){
            $res=$db->sql_fetchrowset();
            for($i=0;$i<count($res);$i++){
                $result[]=Array(
                    "id"=>$res[$i]["valore"],
                    "value"=>$res[$i]["valore"],
                    "label"=>$res[$i]["valore"]
                );
            }
            $exec=1;
        }
        else
            $result[]=Array(
                "id"=>'',
                "value"=>'',
                "label"=>"Si è verificato un errore nell' esecuzione dell'interrogazione $sql;"
                
            );
        break;
    case 'via':
        $sql="SELECT DISTINCT nome as valore FROM civici.vie WHERE nome ilike '%$value%' order by 1";
        if($db->sql_query($sql)){
            $res=$db->sql_fetchrowset();
            for($i=0;$i<count($res);$i++){
                $result[]=Array(
                    "id"=>$res[$i]["valore"],
                    "value"=>$res[$i]["valore"],
                    "label"=>$res[$i]["valore"]
                );
                
            }
            $exec=1;
        }
        else
            $result[]=Array(
                "id"=>'',
                "value"=>'',
                "label"=>"Si è verificato un errore nell' esecuzione dell'interrogazione $sql"
                
            );
        break;
    case 'civico':
        $strada=(isset($_REQUEST['via']))?(addslashes($_REQUEST['via'])):('%');
        $sql="SELECT DISTINCT label as valore,length(label) FROM civici.civici inner join civici.vie on(vie.id=civici.strada) WHERE label ilike '$value%' and nome ilike '$strada' order by 2,1";
        if($db->sql_query($sql)){
            $res=$db->sql_fetchrowset();
            for($i=0;$i<count($res);$i++){
                $result[]=Array(
                    "id"=>$res[$i]["valore"],
                    "value"=>$res[$i]["valore"],
                    "label"=>$res[$i]["valore"]
                );
            }
            $exec=1;
        }
        else
            $result[]=Array(
                "id"=>'',
                "value"=>'',
                "label"=>"Si è verificato un errore nell' esecuzione dell'interrogazione $sql;"
                
            );
        break;
    case "titolo":
    case "titolod":
        $tabella='pe.soggetti';
        break;
    case 'parere':
            $tabella="pe.pareri";
            break;
    case 'notaio':
            $tabella="pe.asservimenti";
            break;
    case 'destuso1':
    case 'destuso2':
            $field="destuso";
            $tabella='pe.e_destuso';
            break;
    case 'motivo':
            $tabella="pe.sopralluoghi";
            break;
    case 'motivo_v':
            $tabella="vigi.sopralluoghi";
            break;
    case 'origine':
            $tabella="vigi.esposti";
            break;
    case 'intervento':
            $tabella="pe.e_intervento";
            break;	
    case 'nota':
            $tabella="pe.e_voci_iter";
            break;			
    case 'sede1':
            $tabella="ce.commissione";
            break;
    case 'numero-pratica':
        $sql="SELECT numero as valore, 'Pratica n° '|| numero || ' del ' || to_char(data_prot,'DD/MM/YYYY') as label,B.nome as categoria,coalesce(data_prot,data_presentazione) as data_prot,pratica FROM pe.avvioproc A left join pe.e_tipopratica B on (A.tipo=B.id) WHERE numero ilike '$value%' order by 3,4;";
        if($db->sql_query($sql)){
            $res=$db->sql_fetchrowset();
            for($i=0;$i<count($res);$i++){
                $result[]=Array(
                    "id"=>$res[$i]["pratica"],
                    "value"=>$res[$i]["valore"],
                    "label"=>$res[$i]["label"],
                    "category"=>$res[$i]["categoria"]
                );

            }
        }
        break;
}
if (!$result){
    $sql="select distinct $field as valore from $tabella where $field ilike '$value%' order by 1;";
    if($db->sql_query($sql)){
        $res=$db->sql_fetchrowset();
        for($i=0;$i<count($res);$i++){
            $result[]=Array(
                "id"=>$res[$i]["valore"],
                "value"=>$res[$i]["valore"],
                "label"=>$res[$i]["valore"]
            );
            
        }
    }
    elseif(isset($exec))
        passthru;
    else
        $result[]=Array(
            "id"=>'',
            "value"=>'',
            "label"=>"Si è verificato un errore nell' esecuzione dell'interrogazione;",
			"query"=>$sql,
			"field"=>$field
        );
}

print json_encode($result);
return;
?>