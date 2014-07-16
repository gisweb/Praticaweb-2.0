<?php
require_once "../login.php";

$limit=$_REQUEST["limit"];
$offset=$_REQUEST["offset"];
$tipo=$_REQUEST["tipo_richiesta"];
$anno=$_REQUEST["anno_riferimento"];
$filter["anno"]=($anno>0)?("anno_riferimento=$anno"):("true");
$filter["tipo"]=($tipo>0)?("tipo_richiesta=".($tipo-1)):("true");
$sql="(SELECT pratica,protocollo as numero,data_presentazione FROM anagrafe_tributaria.pratiche WHERE ".implode(" AND ",$filter)." OFFSET $offset LIMIT $limit;";
$conn=utils::getDb();
$stmt=$conn->prepare($sql);
$stmt->execute();
$res=$stmt->fetchAll(PDO::FETCH_ASSOC);

$sql="SELECT * FROM anagrafe_tributaria.find_testa($anno);";
$stmt=$conn->prepare($sql);
if($stmt->execute())
    $testa=$stmt->fetchAll(PDO::FETCH_ASSOC);	
if (!$db->sql_query($sql)) print_debug($sql);
$testa=$db->sql_fetchrowset();
$testa=implode("",$testa[0]);
// SCRITTURA DEL RECORD DI TESTA
$handle=fopen(STAMPE_DIR."anagrafe_tributaria.txt",'a+');
if(!$handle) echo "Impossibile aprire il file ".$dir."anagrafe_tributaria";
fwrite($handle,$testa);
fclose($handle);

for($i=0;$i<count($ris);$i++){		//CICLO SU TUUTE LE PRATICHE TROVATE
        list($pratica,$num_pr,$data_pres)=array_values($ris[$i]);
        $sql="SELECT * FROM anagrafe_tributaria.e_record order by ordine;";
        if (!$db->sql_query($sql)) print_debug($sql);
        $rec=$db->sql_fetchrowset();
        foreach($rec as $v){
                $sql="SELECT * FROM anagrafe_tributaria.e_tipi_record WHERE record='".$v["tipo"]."' order by ordine;";

                if (!$db->sql_query($sql)) print_debug($sql);
                $fld_int=$db->sql_fetchrowset();
                foreach($fld_int as $el){
                        $intestazioni[$v["nome"]][$el["nome"]]=Array("visibile"=>$el["visibile"],"label"=>$el["label"],"tipo_dato"=>"","validazione"=>$el["tipo_validazione"],"active_form"=>$el["active_form"]);  //Da sostituire $el["nome"] con $el["label"] quando le avrÃ² messe
                }
                $fld=implode(",",array_keys($intestazioni[$v["nome"]]));
                $arr_sql[$v["nome"]]="SELECT $fld FROM anagrafe_tributaria.".$v["funzione"]."($pratica);";

        }		
        foreach($arr_sql as $key=>$sql){
                if (!$db->sql_query($sql)) utils::debug(DEBUG_DIR."anagrafe.debug",$key."  ===> \n\t\t\t".$sql,'w+');
                //if($_SESSION["USER_ID"]<5) echo "<p>$sql</p>";
                $r[$key]=$db->sql_fetchrowset();
        }
        $p=valida_recordset($r,$intestazioni,$pratica);
        list($html_code,$errore)=array_values($p);
        if($errore){
                $result[]="<tr><td class=\"pratica\"><a class=\"pratica\" href=\"#\" onclick=\"javascript:NewWindow('praticaweb.php?pratica=$pratica','Praticaweb',0,0,'yes')\">".(($limit*$offset)+($i+1)).") Pratica n° $num_pr del $data_pres</a></td></tr><tr><td width=\"100%\">$html_code</td></tr>";
                $num_err++;
                scrivi_file($r);
        }
        else
                scrivi_file($r);
        $r=Array();
}
$sql="SELECT * FROM anagrafe_tributaria.find_coda($anno);";	// TROVO INFO SUL RECORD DI CODA
if (!$db->sql_query($sql)) print_debug($sql);
$coda=$db->sql_fetchrowset();
$coda=implode("",$coda[0]);
// SCRITTURA DEL RECORD DI CODA
$handle=fopen(STAMPE_DIR."anagrafe_tributaria.txt",'a+');
if(!$handle) echo "Impossibile aprire il file ".$dir."ana_trib";
fwrite($handle,$coda);
fclose($handle);
	

header('Content-Type: application/json; charset=utf-8');

print json_encode($result);
return;
?>