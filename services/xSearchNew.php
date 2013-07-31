<?php
	//require_once "../login.php";
    /*
     * Script:    DataTables server-side script for PHP and PostgreSQL
     * Copyright: 2010 - Allan Jardine
     * License:   GPL v2 or BSD (3-point)
     */
     
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Easy set variables
     */
     
    /* Array of database columns which should be read and sent back to DataTables. Use a space where
     * you want to insert a non-database field (for example a counter or static image)
     */
    $aColumns = array( 'A.pratica', 'A.numero', 'A.tipo', 'B.nome as tipopratica', 'A.protocollo','coalesce(A.data_prot,A.data_presentazione) as data_protocollo','A.oggetto','C.richiedente','D.progettista','E.data_rilascio','E.titolo');
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "data_protocollo";
     
    /* DB table to use */
    //$sTable = "ajax";
     
    $sTable = <<<EOT
pe.avvioproc A left join 
pe.e_tipopratica B on(A.tipo=B.id) left join
(SELECT pratica,array_to_string(array_agg(coalesce(cognome,'')||' '||coalesce(nome,'')||coalesce(' ('||ragsoc||')','')) ,', ') as richiedente FROM pe.soggetti WHERE richiedente=1 group by pratica) C using(pratica) left join
(SELECT pratica,array_to_string(array_agg(coalesce(cognome,'')||' '||coalesce(nome,'')||coalesce(' ('||ragsoc||')','')) ,', ') as progettisti FROM pe.soggetti WHERE progettista=1 group by pratica) D using(pratica) left join
pe.titolo E using(pratica)
EOT;
     
     
     
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */
     
    /*
     * DB connection
     */
    $gaSql['link'] = pg_connect(
        " host=127.0.0.1".
        " dbname=gw_sanremo".
        " user=postgres".
        " password=postgres"
    ) or die('Could not connect: ' . pg_last_error());
     
     
    /*
     * Paging
     */
    $sLimit = "";
    if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
    {
        $sLimit = "LIMIT ".intval( $_REQUEST['iDisplayStart'] )." OFFSET ".
            intval( $_REQUEST['iDisplayLength'] );
    }
     
     
    /*
     * Ordering
     */
    if ( isset( $_REQUEST['iSortCol_0'] ) )
    {
        $sOrder = "ORDER BY  ";
        for ( $i=0 ; $i<intval( $_REQUEST['iSortingCols'] ) ; $i++ )
        {
            if ( $_REQUEST[ 'bSortable_'.intval($_REQUEST['iSortCol_'.$i]) ] == "true" )
            {
                $sOrder .= $aColumns[ intval( $_REQUEST['iSortCol_'.$i] ) ]."
                    ".($_REQUEST['sSortDir_'.$i]==='asc' ? 'asc' : 'desc').", ";
            }
        }
         
        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" )
        {
            $sOrder = "";
        }
    }
     
     
    /*
     * Filtering
     * NOTE This assumes that the field that is being searched on is a string typed field (ie. one
     * on which ILIKE can be used). Boolean fields etc will need a modification here.
     */
    $sWhere = "";
    if ( $_REQUEST['sSearch'] != "" )
    {
        $sWhere = "WHERE (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $_REQUEST['bSearchable_'.$i] == "true" )
            {
                $sWhere .= $aColumns[$i]." ILIKE '%".pg_escape_string( $_REQUEST['sSearch'] )."%' OR ";
            }
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ")";
    }
     
    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if ( $_REQUEST['bSearchable_'.$i] == "true" && $_REQUEST['sSearch_'.$i] != '' )
        {
            if ( $sWhere == "" )
            {
                $sWhere = "WHERE ";
            }
            else
            {
                $sWhere .= " AND ";
            }
            $sWhere .= $aColumns[$i]." ILIKE '%".pg_escape_string($_REQUEST['sSearch_'.$i])."%' ";
        }
    }
     
     
    $sQuery = "
        SELECT ".implode(", ", $aColumns)."
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
    ";
    $rResult = pg_query( $gaSql['link'], $sQuery ) or die(pg_last_error());
     
    $sQuery = "
        SELECT $sIndexColumn
        FROM   $sTable
    ";
    $rResultTotal = pg_query( $gaSql['link'], $sQuery ) or die(pg_last_error());
    $iTotal = pg_num_rows($rResultTotal);
    pg_free_result( $rResultTotal );
     
    if ( $sWhere != "" )
    {
        $sQuery = "
            SELECT $sIndexColumn
            FROM   $sTable
            $sWhere
        ";
        $rResultFilterTotal = pg_query( $gaSql['link'], $sQuery ) or die(pg_last_error());
        $iFilteredTotal = pg_num_rows($rResultFilterTotal);
        pg_free_result( $rResultFilterTotal );
    }
    else
    {
        $iFilteredTotal = $iTotal;
    }
     
     
     
    /*
     * Output
     */
    $output = array(
        "sEcho" => intval($_REQUEST['sEcho']),
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );
     
    while ( $aRow = pg_fetch_array($rResult, null, PGSQL_ASSOC) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $aColumns[$i] == "version" )
            {
                /* Special output formatting for 'version' column */
                $row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] != ' ' )
            {
                /* General output */
                $row[] = $aRow[ $aColumns[$i] ];
            }
        }
        $output['aaData'][] = $row;
    }
     
    echo json_encode( $output );
     
    // Free resultset
    pg_free_result( $rResult );
     
    // Closing connection
    pg_close( $gaSql['link'] );
?>	