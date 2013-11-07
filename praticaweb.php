<?
include_once ("login.php");
//include ("./lib/menu.class.php");
//include ("./lib/stati.class.php");
unset($is_commissione);
unset($is_commissione_paesaggio);
unset($is_cdu);
//unset($is_condono);

//print_r($_REQUEST);

//$is_commissione=isset($_REQUEST["comm"])?($_REQUEST["comm"]):(0);
//$is_commissione_paesaggio=isset($_REQUEST["comm_paesaggio"])?($_REQUEST["comm_paesaggio"]):(0);
$is_cdu=isset($_REQUEST["cdu"])?($_REQUEST["cdu"]):(0);

/*
if($is_commissione==1){
	$tipomenu="commissione";
	$path="ce";
}
elseif($is_commissione_paesaggio==1){
	$tipomenu="commissione_paesaggio";
	$path="clp";
}

else*/
if($is_cdu==1){
 	$tipomenu="cdu";
	$path="cdu";
}
else{
	$tipomenu="pratica";
	$path="pe";
}

$menu=new Menu($tipomenu,$path);
$active_form=(isset($_REQUEST["active_form"]))?($_REQUEST["active_form"]):('');
$idpratica=(isset($_REQUEST["pratica"]))?($_REQUEST["pratica"]):('');
$id=(isset($_REQUEST["id"]))?($_REQUEST["id"]):('');
if (isset($_REQUEST["active_form_param"])){
	$active_form=$_REQUEST["active_form"].".php?pratica=$idpratica&".@implode("&",$_REQUEST["active_form_param"]);
}
//if ($is_condono) include_once("admin/check_owner.php");

//if ($_REQUEST["stato"]) $_SESSION["stato"]=$_REQUEST["stato"];

if(isset($_POST["stampe"])){
	include "./db/db.stp.stampe.php";
		/*if($is_commissione) 
			$active_form="ce.commissione.php?pratica=$idpratica&comm=1";
		elseif($is_commissione_paesaggio) 
			$active_form="ce.commissione_paesaggio.php?pratica=$idpratica&comm_paesaggio=1";
		else*/
		if($is_cdu) 
			$active_form="cdu.richiesta.php?pratica=$idpratica";
		else {
			$active_form.="?pratica=$idpratica";
		}
}
elseif (isset($active_form) && $active_form){
	//per la gestione dei salvataggi
    
	if (!isset($_REQUEST["ext"])){
		if (file_exists(DATA_DIR."db".DIRECTORY_SEPARATOR."db.$active_form")){
			include_once DATA_DIR."db".DIRECTORY_SEPARATOR."db.$active_form";
		}
		else
			include (APPS_DIR."db/db.$active_form");
		$titolo=$_SESSION["TITOLO_$idpratica"];
	}
	else
		$active_form.="?pratica=$idpratica";
}
else{
	if($is_commissione) 
		$active_form="ce.commissione.php?pratica=$idpratica&comm=1";
       elseif($is_commissione_paesaggio) 
			$active_form="ce.commissione_paesaggio.php?pratica=$idpratica&comm_paesaggio=1";
	elseif($is_cdu) 
		$active_form="cdu.richiesta.php?pratica=$idpratica";
	else {
		$active_form="pe.iter.php?pratica=$idpratica";
		include "./db/db.pe.recenti.php";
	}
}
$pr=new pratica($idpratica,$is_cdu);
$_SESSION["TITOLO_".$idpratica]=$pr->titolo;
?>
<HTML>
	<HEAD>
		<TITLE><?=$_SESSION["TITOLO_".$idpratica]?></TITLE>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<SCRIPT language="javascript" src="js/LoadLibs.js" type="text/javascript"></SCRIPT>

		<SCRIPT language=javascript src="src/iframe.js" type="text/javascript"></SCRIPT>
              <SCRIPT language="javascript" src="/gisclient-2.1/public/jslib/GisClient.js" type="text/javascript"></SCRIPT>		

</HEAD>
<BODY >
<script language="javascript">
	window.name='praticaweb';

</script>


<!-- ### STANDARD  PAGE HEADER  ################### -->
<?include "./inc/inc.page_header.php";?>
<!-- ### STANDARD  PAGE HEADER  ################### -->

<!-- === MAIN PAGE LAYOUT TABLE ======================================================== -->
<TABLE id=main_layout cellSpacing=0 cellPadding=0 width="100%" border="0">
<!--prima cella contiene la colonna dei menÃ¹ e grafica, seconda cella di separazione tra frame e contenuto, terza cella divisa in 2 righe sopra l'iframe sotto i contenuti fissi-->
  <TBODY> 
	<TR vAlign=top align=left> 
     <!-- *** colonna menu  *** -->
		<TD id=scan_column vAlign=top align=left width="160px" rowspan="3">
			<IMG height=31 alt="" src="images/header_spacer.gif" width=160 border=0>
		   <!--<img src="images/pixel.gif" alt="" width="1" height="4" border="0">
		   <IMG height=1 alt="" src="images/white.gif" width=160 align=bottom border=0>-->
		   <!--*** elenco menu *** -->
			<?
			/*if ($is_commissione) 
				$menu->get_list($idpratica);
			else*/
				$menu->get_list($idpratica);
			?>
		</TD>
	<!-- *** colonna di separazione *** -->
		<TD  rowspan="2">
			<IMG height=8 alt="" Src="images/pixel.gif" width="10">
		</TD>		
 	<!-- *** colonna contenuti *** -->
  	<!-- *** cella contenuti dinamici caricati nell'iframe *** -->
		<TD  height="100" width="97%" valign="top">
			<!-- *** MY IFRAME ********************************************* -->
		<IFRAME id=myframe style="DISPLAY: none; OVERFLOW: visible; WIDTH: 97%" marginWidth=0  marginHeight=0 src="<?=$active_form?>" frameBorder=0 scrolling=no ></IFRAME>	
		</TD>
	</TR>
	<!--<TR vAlign=top align=left> 
		<TD valign="top" colspan=3>
			<table cellSpacing=0 cellPadding=0 width="100%" border="0">
				<tr>
					<td align="left"><P style="MARGIN-TOP: 0.3em; FONT-SIZE: 11px; MARGIN-BOTTOM: 0.8em; COLOR: black; LINE-HEIGHT: 1.4em; FONT-FAMILY: Verdana, Geneva, Arial, sans-serif;paddding-bottom: .1em;color:red"><b>
					</b></P></td>
					<td align="right">
						<P class=footerlinks>		
							<A  href="javascript:goToPratica('praticaweb.php',{'pratica':<?php echo $pr->prev?>});">Pratica Precedente</A> &nbsp; &nbsp;
							<A href="javascript:goToPratica('praticaweb.php',{'pratica':<?php echo $pr->next?>});">Pratica Successiva</A> &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</P>
					</td>
				</tr>
			</table>
		</TD>
    </TR>-->
	<TR vAlign=top align=left> 
    <!-- *** cella piè di pagina *** -->
		<TD height="200" valign="top" colspan=3>
			<table cellSpacing=0 cellPadding=0 width="100%" border="0">
				<tr>
					<td align="left"><P style="MARGIN-TOP: 0.3em; FONT-SIZE: 11px; MARGIN-BOTTOM: 0.8em; COLOR: black; LINE-HEIGHT: 1.4em; FONT-FAMILY: Verdana, Geneva, Arial, sans-serif;paddding-bottom: .1em;color:red"><b>
					</b></P></td>
					<td align="right">
						<P class=footerlinks>
							 &nbsp; &nbsp;			
							<A  href="#">Norme di Attuazione</A> &nbsp; &nbsp;
							<A href="#">Leggi e riferimenti</A> &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</P>
					</td>
				</tr>
			</table>
			<SPAN class="footerlinks" style="margin-rigth:50px;margin-top:30px"></SPAN>
			
			<IMG height="2" width="95%" src="images/gray_light.gif"  vspace=1><BR>	
			<?//include "./inc/messaggi.inc.php";
				if(isset($ERRMSG) && $ERRMSG) print ("<p>$ERRMSG</p>");
			?>
		
		</TD>
    </TR>
  </TBODY> 
</TABLE>

<!-- *** fine pagina principale *** -->
</BODY>
</HTML>
