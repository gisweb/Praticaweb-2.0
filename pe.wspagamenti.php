<?php
include_once("login.php");
$tabpath="pe";
include_once "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";
$includeFile = APPS_DIR."include".DIRECTORY_SEPARATOR."init.pe.wspagamenti.php";
$includeLocalFile = DATA_DIR."praticaweb".DIRECTORY_SEPARATOR."include".DIRECTORY_SEPARATOR."init.pe.wspagamenti.php";
$idpratica=$_REQUEST["pratica"];
$modo="list";
$titolo=$_SESSION["TITOLO_$idpratica"];
$confRichiesti = "importi_dovuti";
$confPagopa = "pagopa";
$confPagameti = "pagamenti";

$wsData = Array();

$tabpath="pe";
$dbh = utils::getDb();

$pratica = new pratica($idpratica);
$online = $pratica->info["online"];

if (file_exists($includeLocalFile)) require_once $includeLocalFile;
elseif (file_exists($includeFile)) require_once $includeFile;

$message =<<<EOT
<div style="color:red;font-weight:bold;font-size:13px;">E' possibile creare richieste di pagamento e stampare i relativi ordini di incasso per tutte le pratiche, ma è possibile pubblicare le richieste sul portale delle istanze online solo per quelle presentate online.<br> Per le pratiche cartacee dovranno essere fatte delle integrazioni con pagamento tramite lo stesso portale</div>
EOT;

//require_once LOCAL_LIB."message.class.php";
//$message = message::getMessage("ISTRUZIONI_PAGOPA_VIEW");




?>
<html>
<head>
<title>Pagamenti  - <?=$_SESSION[$idpratica]["TITOLO"]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php
    utils::loadJS(Array('init'));
    utils::loadCss();

?>
<style>
tr.pari{
    background-color:#CEE3F6;
}
tr.dispari{
    background-color:#ECF6CE;
}
</style>
<script>

</script>
</head>
<body>
<?php
    include "./inc/inc.window.php";
    $tabellaRichiesti=new tabella_h("$tabpath/importi_dovuti","list");
    $tabellaRichiesti->dati_aggiuntivi = Array("online"=>$online);
    $tabellaPagoPA=new tabella_h("$tabpath/pagopa","list");
    $tabellaVersati=new tabella_h("$tabpath/pagamenti","list");

    $tabellaRichiesti->set_dati("pratica=$idpratica");
    $tabellaPagoPA->array_dati = $wsData;
    $tabellaVersati->set_dati("pratica=$idpratica and coalesce(modalita,0)<>4");
    
    $tabellaPagoPA->num_record = count($wsData);
    
?>
		<!-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
    <H2 class="blueBanner">Pagina riassuntiva degli importi della pratica</H2>
    <?php echo $message;?>
    <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
      <TR> 
            <TD> 
			<!-- tabella nuovo inserimento-->
<?php
    if(($online==1 || !$online)){
        $tabellaRichiesti->set_titolo("Elenco degli importi Richiesti",'nuovo');
        $tabellaRichiesti->get_titolo('pe.importi_dovuti.php');
        if ($tabellaRichiesti->num_record) 
            $tabellaRichiesti->elenco();
        else
            print ("<p><b>Nessuna richiesta di pagamento effettuata</b></p>");
        print "<BR>";
    }    
        $tabellaPagoPA->set_titolo("Importi versati tramite PagoPA");
        $tabellaPagoPA->get_titolo();
        if ($tabellaPagoPA->num_record) 
            $tabellaPagoPA->elenco();
        else
            print ("<p><b>Nessuna pagamento effettuato tramite PagoPA</b></p>");
        print "<BR>";
        
        $tabellaVersati->set_titolo("Elenco dei Pagamenti",'nuovo');
        $tabellaVersati->get_titolo('pe.pagamenti.php');
        if ($tabellaVersati->num_record) 
            $tabellaVersati->elenco();
        else
            print ("<p><b>Nessuna pagamento effettuato</b></p>");
        print "<BR>";
	//print_array($tabella);
?>
<!-- fine tabella nuovo inserimento-->
			</TD>
		  </TR>			  
		</TABLE>

<div id="page-dialog-message" title="Elaborazione">
  <p>
    Attenzione Elaborazione in corso.....
  </p>
</div>    
<script>
    $("[data-color]").each(function(){
        var d = $(this).data();
        $(this).closest("tr").addClass(d['color']);
    });
    $( "#page-dialog-message" ).dialog({
      modal: true,
      autoOpen: false
    });
$("[data-plugins='stampa_documento']").bind("click",function(){
            event.preventDefault();
            var d = $(this).data();
            if (!confirm('Sei sicuro di voler stampare il documento?')) return;
            $("#page-dialog-message").dialog( "open" );
            $.ajax({
                url:serverUrl,
                dataType:'json',
                type:'POST',
                data:d,
                success: function(data, textStatus, jqXHR){
                    $("#page-dialog-message").dialog( "close" );
                    if (data["success"]==1){
                        window.location.reload(true);
                    }
                    else{
                        alert(data["message"]);
                    }
                }
            });
        });

</script>		
</body>

