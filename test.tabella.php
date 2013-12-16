<?php
require_once "login.php";
require_once APPS_DIR."lib/tabella_b.class.php";
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <?php
        appUtils::writeJS();
        appUtils::writeCSS();
        ?>
    </head>
    <body>
        <?php
        $table= new Tabella_b('pe/avvio_procedimento','view');
        $table->set_dati("pratica=9256");
        $table->viewTable();
        ?>
    </body>
</html>