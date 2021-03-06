<?php
include_once("login.php");
include_once "./lib/tabella_v.class.php";
$tabpath="vigi";
$self=$_SERVER["PHP_SELF"];
$ruolo=(isset($_REQUEST["ruolo"]))?($_REQUEST["ruolo"]):(null);
$idsoggetto=(isset($_REQUEST["id"]))?($_REQUEST["id"]):(null);
$modo=isset($_REQUEST["mode"])?($_REQUEST["mode"]):('view');
$errors=null;

$idpratica=$_REQUEST["pratica"];
$schema = ($_REQUEST["schema"])?($_REQUEST["schema"]):("vigi");
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

if(substr($ruolo,0,1)=='v'){
	$ruolo=substr($ruolo,1);
	$voltura=1;
}
else{
	$voltura=0;
}
if(($ruolo=="proprietario") || ($ruolo=="richiedente") || ($ruolo=="concessionario")){
	$config_file="richiedente";
}
elseif(($ruolo=="progettista") || ($ruolo=="direttore") || ($ruolo=="esecutore") || ($ruolo=="sicurezza") || ($ruolo=="collaudatore") || ($ruolo=="collaudatore_ca") || ($ruolo=="geologo") || ($ruolo=="progettista_ca")){
	$config_file="tecnico";
}
elseif($ruolo=="esecutore"){
	$config_file="esecutore";
}
else{
	$config_file="soggetto";
	$ruolo="nuovo soggetto";
}
$config_file="soggetto_completo";
$config_file=$tabpath."/".$config_file;
//$titolo=$_SESSION["TITOLO_$idpratica"];


?>

<html>
<head>
<title><?=$ruolo?> - <?=$_SESSION["TITOLO_".$idpratica]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php
	utils::loadJS(Array('form/vigi.scheda_soggetto'));
	utils::loadCss();
?>

</head>
<body  background="">
<?php
switch ($modo) { 

// <<<<<<<<<<<<<<<<<<<<<   MODALITA' NUOVO INSERIMENTO  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	case "addnew":
		$_SESSION["ADD_NEW"]=0;
		include "./inc/inc.page_header.php";
		$tabella=new tabella_v("$tabpath/soggetto",'search');	
?>
	<FORM id="" name="nuovo" method="post" action="vigi.scheda_soggetto.php">	
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
			  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Inserimento di un nuovo soggetto</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?php
                                    $tabella->edita();
                                ?>
				<!-- fine contenuto-->
			</td>
		  </tr>
		  
		  <tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   </tr>  
		</TABLE>
	<input name="active_form" type="hidden" value="vigi.scheda_soggetto.php">
	<input name="mode" type="hidden" value="find">
        <input name="vigi" type="hidden" value="1">
	</FORM>
<?php	break;

// <<<<<<<<<<<<<<<<<<<<<   VEDO SE IL NOME GIA ARCHIVIATO  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->
	case "find":
		$idpratica=$_POST["pratica"];//non posso usare quello della tabella
		$campi=array_keys($_POST);
		$valori=array_values($_POST);
		//$modo="new"; //imposto a new
		for($i=0;$i<5;$i++){
		//crea la stringa sql per i campi passati
		if ($valori[$i])
			$sql.="$campi[$i] ilike '".$valori[$i]."%' and ";
		}
		//se almeno un campo è stato compilato faccio la ricerca altrimenti passo al form di inserimento vuoto
		if (strlen($sql)>5){
			$sql=substr($sql,0,strlen($sql)-4);  // taglio l'ultimo "and "
			$tabella=new tabella_v("$tabpath/soggetto",'search');	
			if ($tabella->set_elenco_trovati($sql,'vigi')){ //se trovo un nome corrispondente ai criteri imposto la pagina con i risultati
			include "./inc/inc.page_header.php";?>	
	<FORM id="" name="find" method="post" action="vigi.scheda_soggetto.php">		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
				  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Elenco dei nominativi già  presenti in archivio</H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?php
				// riempo i campi della tabella con i dati immessi per la ricerca e visualizzo l'elenco dei risultati della ricerca
				//$trovati=1;
				$tabella->set_dati($_POST);
				$tabella->edita();
				$tabella->elenco_trovati($idpratica,"vigi");?>
				<!-- fine contenuto-->
			</td>
		  </tr>
		  <tr> 
				<!-- riga finale -->
				<td align="left"><img src="images/gray_light.gif" height="2" width="90%"></td>
		   </tr>  
		</TABLE>
	<input name="mode" type="hidden" value="new">
	<input name="active_form" type="hidden" value="vigi.scheda_soggetto.php">
	<input name="pratica" type="hidden" value="<?php print $idpratica;?>">
	<input name="vigi" type="hidden" value="1">
				
	</FORM>	
		<?php
		break;//mi fermo solo se ho trovato qualche nome altrimenti carico la pagina di editing per il nuovo nome con giÃ  inseriti i valori che ho usato per la ricerca
		} //end if ricerca nominativi giÃ  presenti in elenco.
	
	} // end if
	//se arrivo qui devo settare il modo a new
	$modo="new";

// <<<<<<<<<<<<<<<<<<<<<   MODALITA' EDITA NOMINATIVO SIA NUOVO CHE ESISTENTE  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->		
	case "new":
	case "edit":

		include "./inc/inc.page_header.php";	
		if ($voltura==1) $config_file.="_voltura";
		$tabella=new tabella_v($config_file,$modo);

		$tabella->set_errors($errors);
		(($ruolo=="proprietario") || ($ruolo=="richiedente") || ($ruolo=="concessionario"))?($tit="Sposta in Volture"):($tit="Sposta in Variazioni");
		//se edito un soggetto esitente passo l'id altrimenti completo il form con i dati passati per la ricerca 
		if($idsoggetto){
			if (isset($errors))
				$dataset=$_POST;
			else if ($modo=="edit")
				$dataset="id = $idsoggetto";
			else
				$dataset="id = $idsoggetto AND schema='$schema'";
		}
		else{
			$dataset=$_POST;
		}
		?>
	<FORM id="scheda_soggetto" name="scheda" method="post" action="praticaweb.php">	
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
			  
		  <tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner">Scheda anagrafica <?=$ruolo?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
				<?php
					$tabella->set_dati($dataset);
					$tabella->edita();
				?>
				<!-- fine contenuto-->
			</td>
		  </tr>
		</TABLE>
	<input name="active_form" type="hidden" value="vigi.scheda_soggetto.php">	
	<input name="mode" type="hidden" value="<?=$modo?>">
	<input name="ruolo" type="hidden" value="<?=$_REQUEST["ruolo"];?>">
        <input name="vigi" type="hidden" value="1">


	</FORM>		
		<?php include "./inc/inc.window.php"; // contiene la gesione della finestra popup
	break;

//<<<<<<<<<<<<<<<<<<<<<   MODALITA' VISTA DATI  >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--->	
	default:		

		$tabella=new Tabella_v($config_file,"view");
		$titolo=($voltura==1)?("ex ".$ruolo):($ruolo);
		?>
		<H2 class="blueBanner">Soggetti interessati</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
		  <TR> 
			<TD> 
			<!-- contenuto-->
				<?php
				$tabella->set_titolo("Scheda anagrafica $titolo","modifica",array("id"=>$idsoggetto,"ruolo"=>$_REQUEST["ruolo"]));
				$nrec=$tabella->set_dati("id = $idsoggetto");
				
				$tabella->elenco();?>
			<!-- fine contenuto-->
			 </TD>
	      </TR>

		</TABLE>	
		
<?php
} //end switch
?>

</body>
</html>
