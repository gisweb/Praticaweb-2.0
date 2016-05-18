<?php
//Stesso codice che utilizzo in ubicazione.php, progetto.php, asservimento.php
require_once "../../login.php";
$tabpath="pe";
include_once "./lib/tabella_h.class.php";
include_once "./lib/tabella_v.class.php";

$idpratica=$_REQUEST["pratica"];
$modo=(isset($_REQUEST["mode"]) && $_REQUEST["mode"])?($_REQUEST["mode"]):('view');
$titolo=$_SESSION["TITOLO_$idpratica"];
$azione=(isset($_POST["azione"]) && $_POST['azione'])?($_POST['azione']):(null);
$tabpath="pe";
appUtils::setVisitata($idpratica,basename(__FILE__, '.php'),$_SESSION["USER_ID"]);

?>
<html>
<head>
<title>Ubicazione - <?=$_SESSION[$idpratica]["TITOLO"]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php
    utils::loadJS(Array('init'));
    utils::loadCss();

?>
</head>

<body>

<?php
if (($modo=="edit") or ($modo=="new")){
    unset($_SESSION["ADD_NEW"]);
    $id=$_REQUEST["id"];
    
    //$tab_edit=$_POST["tab_edit"].".tab";
    $titolo=($_REQUEST["tab"]=='indirizzi')?("Indirizzi"):(($tab=='catasto_terreni')?('Catasto Terreni'):('Catasto Urbano'));	
	$tab=$tabpath."/".$_REQUEST["tab"].".tab";
    include "./inc/inc.page_header.php";
    $tabellav=new tabella_v($tab,$modo);
		//$tabellah=new tabella_h($tab_edit,'edit');
?>
		
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="99%" align="center">		
		<tr> 
			<td> 
			<!-- intestazione-->
				<H2 class="blueBanner"><?="Ubicazione dell'intervento - $titolo"?></H2>
			<!-- fine intestazione-->
			</td>
		  </tr>
		  <tr> 
			<td> 
				<!-- contenuto-->
                <form method=post id="ubicazione" action="praticaweb.php">
				<input type="hidden" name="id" id="id" value="<?php echo $id;?>">
				<input type="hidden" name="mode" value="<?php echo $modo;?>">
				<input name="active_form" type="hidden" value="pe.ubicazione.php">
				<input type="hidden" name="tab" value=<?=$_REQUEST["tab"]?>>
				<input type="hidden" name="titolo" value=<?=$_REQUEST["titolo"]?>>
			<?php
				
				
				if($Errors){
					$tabellav->set_errors($Errors);
					$tabellav->set_dati($_POST);
				}
				
                elseif($id){
                    $numrows=$tabellav->set_dati("id=$id");
				}
				//print_array($tabellav);
				$tabellav->edita();
                            ?>
			</form>
				<!-- fine contenuto-->
			</td>
		  </tr> 
		</TABLE>
			
<?php 
                include "./inc/inc.window.php"; // contiene la gesione della finestra popup

}else{// modalità vedi
?>


		<H2 class="blueBanner">Ubicazione dell'intervento</H2>
		<TABLE cellPadding=0  cellspacing=0 border=0 class="stiletabella" width="100%">		
<?php
$array_file_tab=array("indirizzi","catasto_terreni","catasto_urbano");
$array_titolo=array("Indirizzi","Catasto Terreni","Catasto Urbano");
for($i=0;$i<3;$i++){

    $file_tab=$array_file_tab[$i];
    $titolo=$array_titolo[$i];
    print "<tr><td>";
    $tabella=new Tabella_h("$tabpath/$file_tab");

    $tabella->set_titolo($titolo,"nuovo",array("titolo"=>$titolo,"tab"=>$file_tab));

    $numrows=$tabella->set_dati("pratica=$idpratica;");
    $tabella->get_titolo();

    if ($numrows)	
        $tabella->elenco();
    else{
        
        print ("<p><b>Elenco vuoto</b></p>");
    }
        print "<br/></td></tr>";
}
print "</table>";
$sql="SELECT * FROM pe.conteggio_ubicazioni WHERE pratica=?;";
$conn = utils::getDb();
$stmt=$conn->prepare($sql);
$stmt->execute(Array($idpratica));
$res=$stmt->fetch(PDO::FETCH_ASSOC);
$ind=($res["indirizzi"])?(""):("un indirizzo,");
$part=($res["particelle"])?(""):(" una particella catastale (terreni o urbano)");


$msg=<<<EOT
<div class="avviso" style="margin-top:10px;">
    Per la compilazione dell'anagrafe tributaria sono necessari almeno $ind $part 
</div>
EOT;
if ($ind || $part){
    echo $msg;
}

}
?>

</body>
</html>
