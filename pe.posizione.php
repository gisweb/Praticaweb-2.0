<?php

include_once("login.php");
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];

$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$file_config="$tabpath/posizione";
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);
$dbh = utils::getDB();
$sql = "SELECT * FROM pe.posizione WHERE pratica=?";
$stmt = $dbh->prepare($sql);
if( $stmt->execute(Array($idpratica))){
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else{
    $data = Array();
}
$geoms = json_encode($data);
?>
<html>
<head>
    <title>Posizione - <?php echo $_SESSION["TITOLO_".$idpratica];?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <?php
    utils::loadCss();
    utils::loadJS();
    $jsrand = sprintf("js/map.js?random=%d",rand(100000,999999));
    ?>

    
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBjBtRCk34lZRaccgkAoUvV2JpiZhAnflk&libraries=drawing"></script>
    <script>
       var map;
       var bounds;
    </script>
    <script type="text/javascript" src="js/proj4js.js"></script>
    <script type="text/javascript" src="js/local/layersData.js"></script>
    <script type="text/javascript" src="<?php echo $jsrand;?>"></script>
    <link rel="stylesheet" type="text/css" media="all" href="css/map.css" />
    <script>
        $(document).ready(function(){
//            map.fitBounds(bounds);       // auto-zoom
//            map.panToBounds(bounds);     // auto-center
        });
    </script>
</head>
<body  background="">
<?php

$id=$_REQUEST["id"];

if (($modo=="edit") or ($modo=="new")) {
    include "./inc/inc.page_header.php";
    unset($_SESSION["ADD_NEW"]);

    if(!$id) $modo ="new";
    $tabella=new Tabella_v($file_config,$modo);
    //aggiungendo un nuovo parere uso pareri_edit che contiene anche l'elenco degli ENTI
    ?>
    <!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <FORM height=0  method="post" action="praticaweb.php">
        <TABLE cellPadding=0 cellspacing=0 border=0 class="stiletabella" width="99%" align="center">
            <TR> <!-- intestazione-->
                <TD><H2 class="blueBanner">Posizione in mappa</H2></TD>
            </TR>
            <TR>
                <td>
<!--
                    <div id="map"></div>
                    <div id="coords"></div>
-->
                    <!-- contenuto-->
                    <?php
                    if($Errors){
                        $tabella->set_errors($Errors);
                        $tabella->set_dati($_POST);
                    }
                    elseif ($modo=="edit"){
                        $tabella->set_dati("pratica=$idpratica and id = $id");
                    }

                    //for($i=0;$i<count($tabella->array_dati);$i++){
                    //    $points[]=$tabella->array_dati[$i];
                    //}
                    //$geoms = json_encode($points);
                    echo <<<EOT
            <div id="map"></div>
            <div id="coords"></div>
            <input type="hidden" name="points" id="points" value='$geoms'>
            <input type="hidden" id="id" value="$id">
            <div id="coords"></div>
EOT;
			$tabella->edita();

                    ?>
                    <!-- fine contenuto-->
                </TD>
            </TR>

        </TABLE>
        <input name="active_form" type="hidden" value="pe.posizione.php">
        <input id="mode" name="mode" type="hidden" value="<?php echo $modo; ?>">

    </FORM>
    <?php
    include "./inc/inc.window.php";
}
else {
    ?>
    <!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <H2 class="blueBanner">Posizione</H2>
    <TABLE cellPadding=0 cellspacing=0 border=0 class="stiletabella" width="100%">
        <TR>
            <TD>

                <?php
                $modo="list";
                $btn="nuovo";
                $tabella=new Tabella_h("pe/posizione",$modo);
                $tabella->set_dati("pratica=$idpratica");
                for($i=0;$i<count($tabella->array_dati);$i++){
                    $points[]=$tabella->array_dati[$i];
                }
                $geoms = json_encode($points);
                /*if($tabella->num_record){
                    
                    $geom = $tabella->array_dati[0]["geometry"];
                    $x = $tabella->array_dati[0]["coordx"];
                    $y = $tabella->array_dati[0]["coordy"];
                }
                else{
                    $geom = "";
                    $x = "";
                    $y = "";
                    $btn="nuovo";
                }*/
                $tabella->set_titolo("Posizione in mappa",$btn,array("tabella"=>"posizione"));
                echo <<<EOT
                <div id="map"></div>
                <div id="coords"></div>
                <input type="hidden" name="geometry" id="geometry" value="$geom">
                <input type="hidden" name="coordx" id="coordx" value="$x">
                <input type="hidden" name="coordy" id="coordy" value="$y">
                <input type="hidden" name="points" id="points" value='$geoms'/>   
                <input type="hidden" id="mode" value="$modo">   
EOT;
                $tabella->get_titolo();
                $tabella->elenco();

                ?>

            </TD>
        </TR>
    </TABLE>
    <?php

}
?>
</body>
</html>
