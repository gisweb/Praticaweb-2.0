<?php
//VERIFICARE IN BASE AL TIPO DI UTENTE I SERVIZI DISPONIBILI
//se passo un idpratica punto alla pratica


require_once ("login.php");

$file = TAB_ELENCO."elenco_index.tab";
$menu=0;
$last_change=shell_exec("git log -1 --pretty=format:'%ci'");
?>

<html>
<head>
<SCRIPT LANGUAGE="JavaScript1.2">



</SCRIPT>
<title>PraticaWeb: Servizi</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php
	utils::loadJS();
	utils::loadCss();
?>
<SCRIPT language="javascript" >
    window.name='indexPraticaweb';
    var firstWin = window;
    var windows={};

</SCRIPT>


</head>
<body>

<?php
include "./inc/inc.page_header.php";
?>
<TABLE id=main_layout cellSpacing=0 cellPadding=0 width="102%" border=0>
  <TBODY> 
  <TR vAlign=top align=left> 
    <TD width=15 rowspan="3"><IMG height=8 alt="" Src="images/pixel.gif" width=5></TD>
    <td width="768" height="30" valign="top">
    <H2 class=blueBanner>Sportello unico dell'Edilizia</H2>
	
<?php
/*INIZIO MODIFICHE*/

	if (file_exists($file)){
		$rows=file($file);
		for($i=1;$i<count($rows);$i++){	//Escludo la prima riga dedicata ai commenti del file di configurazione
			$fields=explode("@",$rows[$i]);
			if (count($fields)==2){
				list($grp,$permessi)=explode("-",$fields[0]);
				if($_SESSION["PERMESSI"]==1 || ($_SESSION["PERMESSI"]<=$permessi && (trim($grp)=='' || count(array_intersect(explode(',',$grp),$_SESSION['GROUPS']))))){
					if ($i!=1) print("\n\t</UL>");
	?>
	 
	<table width="95%" border="0">
	 	<tr  bgColor=#728bb8>
			<td   bgColor=#728bb8><font face="Verdana" color="#ffffff" size="1"><b><?=$fields[1]?></b></font></td>
		</tr>
	</table>
	<UL>
	<?php			}
			}
		else{
			list($permessi,$js,$param,$link,$text)=explode("@",$rows[$i]);
			list($grp,$permessi)=explode("-",$permessi);
			if($_SESSION["PERMESSI"]==1 || ($_SESSION["PERMESSI"]<=$permessi && (trim($grp)=='' || count(array_intersect(explode(',',$grp),$_SESSION['GROUPS']))))){
				if ($js!="#"){
	?>
		<LI>		
			<a href="javascript:<?=$js?>(<?=$param?>)"><?=$link?></A> - <?=utf8_decode($text)?>
		</LI>
	<?php
				}
				else{
	?>
		<LI>		
			<a href="#"><?=$link?></A> - <?=$text?>
		</LI>
	<?php
				}
			}
		}
		}
	}
	else 
		print("	\n\tNessun file di configurazione $file\n");
	
	/*FINE MODIFICHE*/
	?>

      <IMG height=1 src="images/gray_light.gif" width="100%"  vspace=1><BR>

      <P class=footer><IMG height=1 alt="" src="images/pixel.gif"  space=4><BR>
        <table class="footer" cellspacing="10"><tr><td>Ultima modifica: <a href="#"><?php echo $last_change?></a></td><td>Telefono : 010-2474491</td><td>email : <a href="mailto:assistenza@gisweb.it">gisweb</a></td></tr></table><BR>
      </P>
 </TR>
  </TBODY> 
</TABLE>


</BODY>
</HTML>
