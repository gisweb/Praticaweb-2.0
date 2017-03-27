<?php

include_once("login.php");
include "./lib/tabella_v.class.php";
$tabpath="pe";
$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$filetab="$tabpath/posizione";
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


$form="posizione";
if (($modo=="edit") or ($modo=="new")) {
    include "./inc/inc.page_header.php";
    unset($_SESSION["ADD_NEW"]);


    //aggiungendo un nuovo parere uso pareri_edit che contiene anche l'elenco degli ENTI
    ?>
    <!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN EDITING  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <FORM height=0 method="post" action="praticaweb.php">
        <TABLE cellPadding=0 cellspacing=0 border=0 class="stiletabella" width="99%" align="center">
            <TR> <!-- intestazione-->
                <TD><H2 class="blueBanner"><?= $titolo ?></H2></TD>
            </TR>
            <TR>
                <td>
                    <!-- contenuto-->
                    <?php

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
                <div id="map"></div>
                <div id="coords"></div>
                <form>
                    <p>
                        <label for="coordx">Coordinata X</label>
                        <input type="text" name="coordx" id="coordx"/>
                        <label for="coordy">Coordinata Y</label>
                        <input type="text" name="coordy" id="coordy"/>
                    </p>
                    <p>
                        <label for="note_geometry">Annotazioni</label>
                        <textarea name="note_geometry" id="note_geometry" rows="4" cols="50"></textarea>
                    </p>
                    <input type="text" name="geometry" id="geometry" value="9.151762 44.350338"/>
                </form>
            </TD>
        </TR>
    </TABLE>
    <?php

}
?>
</body>
</html>