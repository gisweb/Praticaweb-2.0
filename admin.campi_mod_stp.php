
<?
include_once("./login.php");

if ($_SESSION["PERMESSI"] > 2){ 
   include_once HOME;
   exit;
}
$errors=null;
include "./lib/tabella_v.class.php";
include "./lib/tabella_h.class.php";
$modo=(isset($_REQUEST["mode"]))?($_REQUEST["mode"]):('view');
$id=isset($_REQUEST["id"])?($_REQUEST["id"]):('');
$tabpath="admin";

//include "db/db.admin.utenti.php";
$file_config="campi";
switch ($modo) {
	case "new" :
		$tit="Inserimento Nuovo Utente";
		break;
	case "edit" :
		$tit="Modifica Dati di Registrazione";
		break;
	case "view" :
		$tit="Visualizzazione dei Dati di Registrazione";
		break;
	default :
		$tit="Gestione degli Utenti";
		break;
}
?>
<html>
<head>
<title>Gestione Utenti di PraticaWeb</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>

<SCRIPT language=javascript>
function link(i){
	document.invia.id.value=i;
	document.invia.submit();
}
</SCRIPT>

</head>
<body>

<?include "./inc/inc.page_header.php";?>
<H2 class="blueBanner"><?=$tit?></H2>
<?
   
	if (($modo=="edit") or ($modo=="new") or $modo=="view"){
	$tabella=new tabella_v("$tabpath/$file_config.tab");
?>
   
	  <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="75%">				  
		 <tr> 
			<td> 
				<!-- contenuto-->
			<?					
				$tabella->set_titolo("Campo modello di stampa");
				$tabella->get_titolo();
				$tabella->set_dati("nome='$_REQUEST[nome]'");
				$tabella->tabella();
			?>
				<!-- fine contenuto-->
			</td>
		  </tr>
		</TABLE>
   <!--<form name="invia" method="POST">
	  <input type="hidden" name="mode" value="list">
   <input type="submit" id="btn_close" style="margin-left:20px;" value="Chiudi"></div>-->
   


<?}else{		//-- <<<<<<<<<<<<<<<<<<<<<   MODALITA' FORM IN VISTA TUTTI DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
?>
   <TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">
	  <TR> 
			<TD> 
	  <!-- contenuto-->
		  <?
			//$sql="SELECT * FROM gd_vocglo order by gruppo,mnemonico";
			//$dbconn->sql_query($sql);
			//$ris=$dbconn->sql_fetchrowset();
			$tabella=new tabella_h("$tabpath/$file_config",'list');
			$tabella->set_titolo("Elenco degli Campi");
			$tabella->get_titolo();
			$tabella->set_dati("true");
			//print_array($tabella);
			$tabella->elenco();
	  ?>
	  <!-- fine contenuto-->
		 
		 </TD>
	  </TR>
  </TABLE>

   <button id="btn_close" />
   <script>
	  $('#btn_close').button({
		 icons:{
			primary:'ui-icon-circle-close '
		 },
		 label:'Chiudi'
	  }).click(function(){
		 window.opener.focus();
		 window.close();
	  });
   </script>
<?}?>
</body>
</html>
