<?php
require_once "login.php";
require_once APPS_DIR."lib/tabella_b.class.php";
$mode=($_REQUEST["mode"])?($_REQUEST["mode"]):("view");
$idPratica=($_REQUEST["pratica"])?($_REQUEST["pratica"]):("12569");
$id=($_REQUEST["id"])?($_REQUEST["id"]):("");

$cfg="pe/avvio_procedimento";

switch($mode){
    case "view":
        $table= new Tabella_b($cfg,$mode);
        $table->set_dati("pratica=$idPratica");
        $table->title="Dati di avvio procedimento";
        break;
    case "edit":
        $table= new Tabella_b($cfg,$mode);
        $table->set_dati("pratica=$idPratica");
        $table->title="Modifica dati di avvio procedimento";
        break;
    case "new":
        $table= new Tabella_b($cfg,$mode);
        $table->title="Nuova Pratica";
        break;
    case "list":
        $table= new Tabella_b($cfg,$mode);
        break;
}


?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <?php
        appUtils::writeJS();
        appUtils::writeCSS();
        ?>
        <script>
            var tableData=<?php echo $table->toJson()?>;
            $(document).ready(function(){
                $('#pw-data').form('load',tableData);
            });
        </script>
    </head>
    <body>
        <form id="pw-data" role="form" method="post">
        <?php
            $table->viewTable();
        ?>
        </form>
    </body>
</html>