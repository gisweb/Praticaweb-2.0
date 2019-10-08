<?php

include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];

$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$file_config="$tabpath/posizione";
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

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
    ?>

    <script LANGUAGE="JavaScript">
        function confirmSubmit()
        {
            var msg='Sicuro di voler eliminare definitivamente il parere corrente?';
            var agree=confirm(msg);
            if (agree)
                return true ;
            else
                return false ;
        }

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBjBtRCk34lZRaccgkAoUvV2JpiZhAnflk&libraries=drawing"></script>

    <script type="text/javascript" src="js/proj4js.js"></script>
    <script type="text/javascript" src="js/local/layersData.js"></script>
    <script type="text/javascript" src="js/map.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="css/map.css" />
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
    <FORM height=0 method="post" action="praticaweb.php">
        <TABLE cellPadding=0 cellspacing=0 border=0 class="stiletabella" width="99%" align="center">
            <TR> <!-- intestazione-->
                <TD><H2 class="blueBanner">Posizione in mappa</H2></TD>
            </TR>
            <TR>
                <td>
                    <div id="map"></div>
                    <div id="coords"></div>
                    <!-- contenuto-->
                    <?php
                    if($Errors){
                        $tabella->set_errors($Errors);
                        $tabella->set_dati($_POST);
                    }
                    elseif ($modo=="edit"){
                        $tabella->set_dati("pratica=$idpratica");
                    }
                    $tabella->edita();
                    ?>
                    <!-- fine contenuto-->
                </TD>
            </TR>

        </TABLE>
        <input name="active_form" type="hidden" value="pe.posizione.php">
        <input name="mode" type="hidden" value="<?php echo $modo; ?>">

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
                $tabella=new Tabella_v($file_config,$modo);
                $tabella->set_dati("pratica=$idpratica");
                if($tabella->num_record){
                    $btn="modifica";
                    $geom = $tabella->array_dati[0]["geometry"];
                    $x = $tabella->array_dati[0]["coordx"];
                    $y = $tabella->array_dati[0]["coordy"];
                }
                else{
                    $geom = "";
                    $x = "";
                    $y = "";
                    $btn="nuovo";
                }
                $tabella->set_titolo("Posizione in mappa",$btn,array("tabella"=>"posizione","id"=>""));

                $tabella->get_titolo();
	        echo <<<EOT
                <div id="map"></div>
                <div id="coords"></div>
                <input type="hidden" name="geometry" id="geometry" value="$geom">
                <input type="hidden" name="coordx" id="coordx" value="$x">
                <input type="hidden" name="coordy" id="coordy" value="$y">
EOT;


                $tabella->tabella();

                ?>

            </TD>
        </TR>
    </TABLE>
    <?php

}
?>
</body>
</html>
