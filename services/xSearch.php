<?php
include_once "../login.php";
error_reporting(E_ERROR);
$action=(isset($_REQUEST['action']))?($_REQUEST['action']):('search');
$searchtype=$_REQUEST['searchType'];
$value=addslashes($_REQUEST['term']);
$usr=$_SESSION['USER_ID'];
$result=Array();
switch($action){
    case "list":
        switch($searchtype) {
            case 'recenti':
                $sql="SELECT A.data,B.* FROM pratiche.recenti A INNER JOIN (select X.pratica as id,split_part(X.numero,'/',1) as numero,coalesce(X.data_incarico::varchar,'') as data_incarico,Y.nome as tipo,Z.opzione as perito,W.opzione as assicurazione from ((pratiche.pratica X inner join pratiche.e_tipopratica Y on(X.tipo=Y.id)) left join admin.elenco_periti Z on(resp_pratica=Z.id::varchar)) left join pratiche.elenco_assicurazioni W on(compagnia_assic=W.id)) B ON (A.pratica=B.id) WHERE utente = $usr ORDER BY A.data DESC LIMIT ".RECENTI;
                if($dbconn->sql_query($sql)){
                    $res=$dbconn->sql_fetchrowset();//print_array($res);
                    for($i=0;$i<count($res);$i++){
                        $r=Array();
                        foreach($child[$field] as $k=>$v) $r[$k]=$res[$i][$v];
                        $result['aaData'][]=$res[$i];
                        
                    }
                    $exec=1;
                }
                else
                    $result[]=Array( "id"=>'',"value"=>'',"label"=>"Si è verificato un errore nell' esecuzione dell'interrogazione : \n $sql");
            break;
            default:
                $sql="select X.pratica as id,split_part(X.numero,'/',1) as numero,coalesce(X.data_incarico::varchar,'') as data_incarico,Y.nome as tipo,Z.opzione as perito,W.opzione as assicurazione from ((pratiche.pratica X inner join pratiche.e_tipopratica Y on(X.tipo=Y.id)) left join admin.elenco_periti Z on(resp_pratica=Z.id::varchar)) left join pratiche.elenco_assicurazioni W on(compagnia_assic=W.id) ";
                if($dbconn->sql_query($sql)){
                    $res=$dbconn->sql_fetchrowset();//print_array($res);
                    for($i=0;$i<count($res);$i++){
                        $r=Array();
                        foreach($child[$field] as $k=>$v) $r[$k]=$res[$i][$v];
                        $result['aaData'][]=$res[$i];
                        
                    }
                    $exec=1;
                }
                else{
                    $result[]=Array( "id"=>'',"value"=>'',"label"=>"Si è verificato un errore nell' esecuzione dell'interrogazione : \n $sql");
                    
                }
            break;
        }        
        break;
    case "search":
        foreach($_REQUEST as $key=>$val){
            if(strpos($key,'mDataProp_')!==FALSE) {
                $idx=str_replace('mDataProp_','',$key);
                $aColumns[$idx]=$val;
            }
        }
        
        if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' ){
            $sLimit = "LIMIT ".pg_escape_string( $_REQUEST['iDisplayLength'] )." OFFSET ".pg_escape_string( $_REQUEST['iDisplayStart'] );
        }
        if ( isset( $_REQUEST['iSortCol_0'] ) ){
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_REQUEST['iSortingCols'] ) ; $i++ ){
                if ( $_REQUEST[ 'bSortable_'.intval($_REQUEST['iSortCol_'.$i]) ] == "true" ){
                    $sOrder .= $aColumns[ intval( $_REQUEST['iSortCol_'.$i] ) ]." ".pg_escape_string( $_REQUEST['sSortDir_'.$i] ) .", ";
                }
            }
             
            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                $sOrder = "";
            }
        }
        
        $flds=implode(",",$aColumns);
        switch($_REQUEST['view']){
            case 'pratica':
                $sTable="pe.avvio_procedimento";
				$sWhere="WHERE ".stripslashes($_REQUEST['filter']);
                break;
        }
        $sql="SELECT $flds FROM $sTable;";
        $dbconn->sql_query($sql);
        $iTotal=$dbconn->sql_numrows();
        //$sql=trim("SELECT 'gradeA' as \"DT_RowClass\",$flds FROM $sTable $sWhere $sOrder $sLimit");
        $sql=trim("SELECT $flds FROM $sTable $sWhere $sOrder $sLimit");
        $dbconn->sql_query($sql);
        $ris=$dbconn->sql_fetchrowset();
        
        $iFilteredTotal=count($ris);
        $result = array(
            "sEcho" => intval($_REQUEST['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => $ris,
            "query"=>$sql
        );        
        break;
}




print json_encode($result);
return;

//
//
//require_once '../login.php'	;
//

//echo json_encode( $result );
?>