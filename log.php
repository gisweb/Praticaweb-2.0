<?php
require_once "login.php";
if($_SESSION["USER_ID"]<10){
    $ORDINAMENTO=Array(
        "data"=>"data_enter desc",
        "user"=>"nome"
    );
    $db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
    if(!$_REQUEST["order"]) $order="data_enter desc,nome";
    else{
        $order=$ORDINAMENTO[$_REQUEST["order"]];
    }
    $sql="select Y.nome,Y.username,X.data_enter,X.ipaddr  from admin.accessi_log X inner join admin.users Y using(username) order by $order";
    $db->sql_query($sql);
    $ris=$db->sql_fetchrowset();
    $tr[]="
        <tr>
            <th width=\"40%\">Utente</th>
            <th width=\"35%\">Data</th>
            <th width=\"35%\">Indirizzo I.P.</th>
        </tr>
    ";
    if(count($ris))
        for($i=0;$i<count($ris);$i++){
            $r=$ris[$i];
            $tr[]="
            <tr>
                <td>$r[nome]</td>
                <td>$r[data_enter]</td>
                <td>$r[ipaddr]</td>
            </tr>";
        }
    else
        $tr[]="
        <tr>
            <td colspan=\"3\">Nessun accesso registrato</td>
        </tr>";
}
else{
    include_once "enter.php";
exit;
}

?>
<html>
    <head>
        <title></title>
    </head>
    <body>
        <table border=1 width="60%">
            <?php echo implode("",$tr);?>
        </table>
    </body>
</html>