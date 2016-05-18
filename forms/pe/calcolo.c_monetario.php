<?php

//if(!defined("comune")) return;
require_once "login.php";
$db = new sql_db(DB_HOST,DB_USER,DB_PWD,DB_NAME, false);
if(!$db->db_connect_id)  die( "Impossibile connettersi al database");

$query="SELECT * FROM oneri.parametri";
$result=$db->sql_query($query);
//if(!$result){echo "SQL Error - ".mysql_error()."<br>".$query;return;}
$row = $db->sql_fetchrow($result);
$costo_base=$row['costo_base'];
$qbase  = $row['quota_base'];
$classe = $row['classe_comune'];
$quota= $row['corrispettivo'];
$delibera=$row['delibera'];

$oggi=date("d-m-Y");	
$pratica=$_REQUEST['pratica'];
$sql="SELECT numero from pe.avvioproc where pratica=$pratica";
$db->sql_query($sql);
$numero=$db->sql_fetchfield('numero');
$sql="SELECT case when (not coalesce(piva,'')='') then coalesce(ragsoc,'') else coalesce(cognome,'')||' '||coalesce(nome,'') end as nominativo FROM pe.soggetti WHERE richiedente=1 and pratica=$pratica;";
$db->sql_query($sql);
$ris=$db->sql_fetchlist('nominativo');
$nominativi=implode('; ',$ris);
$sql="SELECT coalesce(via,'')||' '||coalesce(civico,'') as indirizzi FROM pe.indirizzi WHERE pratica=$pratica;";
$db->sql_query($sql);
$ris=$db->sql_fetchlist('indirizzi');
$indirizzi=implode('; ',$ris);
$sql="SELECT valore,codice FROM pe.parametri_prog X inner join pe.e_parametri Y on(X.parametro=Y.id) WHERE pratica=$pratica and codice in ('slot','ve','vp')";
//echo $sql;
$db->sql_query($sql);
$ris=$db->sql_fetchrowset();
for($i=0;$i<count($ris);$i++) $prm[$ris[$i]['codice']]=$ris[$i]['valore'];
?>
<html>
	<head>
		<script src="../js/LoadLibs.js"></script>
<!--        <script src="../js/java_albe.js"></script>-->

<link rel="stylesheet" type="text/css" href="../css/simple.css" />
<link rel="stylesheet" type="text/css" href="../css/theme.css" />

<script language="javascript" type="text/javascript">

function crea() {
		
	var sendData={};
	sendData['m1']=$('#pe').val();
	sendData['m2']=$('#da_od').val();
	sendData['m3']=$('#int').val();
	sendData['m4']=$('#ind').val();
	sendData['m5']=$('#per_3').val();
	sendData['m6']=$('#per_2').val();
	sendData['m7']=$('#moneta').val();
	sendData['m8']=$('#sup_l').val();
	sendData['m9']=$('#vol_es').val();
	sendData['m10']=$('#vol_pr').val();
	sendData['m11']=$('#per_cess').val() ;
	sendData['m12']=$('#con_1').val()  ;
	sendData['m13']=$('#cess2').val() ;
	sendData['m14']=$('#corr').val() ;
	sendData['m15']=$('#rata').val() ;
	sendData['m16']=$('#vol_3_1').val() ;
	sendData['m17']=$('#vol_calc').val() ;
	sendData['m18']=$('#cess').val() ;
	sendData['m19']=$('#sup_30').val()  ;
	sendData['m20']=$('#cess1').val() ;
	sendData['pratica']=<?php echo $pratica;?>;
	
	
	$.ajax({
		url:'create_l.php',
		type:'POST',
		dataType:'JSON',
		data: sendData,
		success:function(data, textStatus, jqXHR){
			$('#praticaFrm').submit();
			return;
		}
	});

//
//
//s = "m1="+m1 + "&m2="+m2 + "&m3="+m3 + "&m4="+m4 + "&m5="+m5 + "&m6="+m6 + "&m7="+m7 + "&m8="+m8 + "&m9="+m9;
//s = s + "&m10="+m10 + "&m11="+m11 + "&m12="+m12 + "&m13="+m13 + "&m14="+m14 + "&m15="+m15 + "&m16="+m16 + "&m17="+m17 + "&m18="+m18 + "&m19="+m19 + "&m20="+m20;
////confirm(s);	
//		
//
//					
//					
//strURL='create_l.php?'+s;
//
//
//
//var xmlH = false;
//var self = this;
//
//if (window.XMLHttpRequest) {self.xmlH = new XMLHttpRequest();}
//else if (window.ActiveXObject) {self.xmlH = new ActiveXObject("Microsoft.XMLHTTP");}
//
//self.xmlH.open('GET', strURL, true);
//self.xmlH.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
//self.xmlH.send();
//
//
//
//
//
//
//
//
//
//
//
//self.xmlH.onreadystatechange = function() {		/*Gli stai di una richiesta possono essere 5
//												* 0 - UNINITIALIZED
//												* 1 - LOADING
//												* 2 - LOADED
//												* 3 - INTERACTIVE
//												* 4 - COMPLETE*/
//
//												//Se lo stato � completo 
//												if (self.xmlH.readyState == 4) {//alert(self.xmlHttpReq.responseText);
//																					aggiornaPagina_2(self.xmlH.responseText);}
//												/* Aggiorno la pagina con la risposta ritornata dalla precendete richiesta dal web server.Quando la richiesta � terminata il responso della richiesta � disponibie come responseText.*/
//
//
//												} 
//
//
//
//
//
//
////confirm(strURL);
}

//function aggiornaPagina_2(stringa){
//document.getElementById("link_down_1").innerHTML = stringa;
//}



</script>


<script language="javascript" type="text/javascript">

//function startCalc_li(){
//	interval = setInterval("calc_li()",1);
//}
//function stopCalc_li(){
//	clearInterval(interval);
//}
function round(numero){
	arrotondato=numero.toFixed(2);
	return arrotondato;
}



function calc(){
/////////////////////////////
//calcolo la somma della SU//
/////////////////////////////
	var rata;
	var volume_calc;
	var controllo1;
	var rule;
	var cess2;
	var corrispettivo;
	
	var superficie_l = $('#sup_l').val();
	var volume_es = $('#vol_es').val(); 
	var volume_pr = $('#vol_pr').val() 
	var percentuale_cess = $('#per_cess').val();
	var moneta_unit = $('#moneta').val();
	
	
	if ((volume_es * 1)==0) {volume_es=0;}

	var volume_3_1 = superficie_l * 3;

	if (volume_3_1 < volume_es ) {
		volume_calc=(volume_pr * 1 ) ;
	}
	else {
		volume_calc=(volume_pr * 1) + (volume_es *1)- (volume_3_1 *1);
	}
	var cessione = round((volume_calc * 18) / 100);
	var superficie_30 = round(superficie_l * 0.3);


	$('#vol_3_1').val(volume_3_1);
	$('#vol_calc').val(volume_calc);
	$('#cess').val(cessione);
	$('#sup_30').val(superficie_30);
	$('#cess1').val(cessione);

	



/////////////////////
//Norme applicabili//
/////////////////////
	if (superficie_l < 700) {
		rule="Cessioni compensabili in corrispettivo monetario al 100%.";
		if (percentuale_cess <= 100) {
			controllo1="Monetizz. corretta";
			cess2 = round (((100 - (percentuale_cess * 1))/100) * cessione);
			corrispettivo = round (((cessione * 1) - (cess2 * 1))* moneta_unit);
			rata = round (corrispettivo/2);
		}
		else {
            controllo1="Monetiz. non corretta" 
            cess_2 = "------";
            corrispettivo = "------";
            rata = "------";//round (corrispettivo/2);
        }
	}			

	if ((superficie_l >= 700) && (superficie_l < 1000)) {
		rule="Cessioni compensabili in corrispettivo monetario al 50%.";
		if (percentuale_cess <= 50) {
			controllo1="Monetizz. corretta";
			cess2 = round(((100 -(percentuale_cess * 1))/100)*cessione);
			corrispettivo = round(((cessione * 1) - (cess2 * 1))* moneta_unit);
			rata = round (corrispettivo/2);
		}
		else {
			controllo1="Monetiz. non corretta" ;
			cess2 = "------";
			corrispettivo = "------";
			rata = "------";//round (corrispettivo/2);
		}
		
	}

	if (superficie_l >= 1000) {
		rule="Cessioni non compensabili in corrispettivo monetario.";
		if (percentuale_cess == 0) {
			controllo1="Monetizz. corretta";
			cess2 = round(0);
			corrispettivo = cessione * moneta_unit;
			rata = round (corrispettivo/2);
		}
		else {
			 controllo1="Monetiz. non corretta";
		};
		cess2 = "------";
		corrispettivo = "------";
		rata = "------";//round (corrispettivo/2);
	
	}			

	$('#norme').val(rule);
	$('#con_1').val(controllo1) ;
	$('#cess2').val(cess2);
	$('#corr').val(corrispettivo);
	
	$('#rata').val(rata) ;
}



//function stopCalc_li(){
//	clearInterval(interval);
//}

</script>
<body>
	<h2 class="blueBanner">Calcolo Corrispettivo Monetario Lotti Interclusi Zone B</h2>
<form name="autoForm" >
	<table width="625" >
	
	
	
	
	
	<tr><td colspan="7"><br></td></tr>
<tr>
	<td class="testost">P.E.</td>
	<td align="center" class="testost_odd_calc"><input size="10" class="testimp_odd_calc" id="pe" value="<?php echo $numero;?>" type="text" readonly="readonly"></td>
	<td colspan="2" align="right" class="testost">Intestatario</td>
	<td colspan="3" align="center" class="testost_odd_calc"><input size="45" class="testimp_odd_calc" id="int" value="<?php echo $nominativi;?>" readonly="readonly" type="text"></td>
	</tr>
	<tr>
	<td class="testost">Data</td>
		<td align="center" class="testost_odd_calc">
            <input size="10" value="<?php echo $oggi;?>" class="testimp_odd_calc" id="da_od" id="da_od" type="text">
            <script>
                $('#da_od').datepicker({
                    dateFormat:'dd-mm-yy',
                    changeMonth: true,
                    changeYear: true
                });
            </script>
    </td>
	<td colspan="2" align="right" class="testost">Indirizzo</td>
	<td colspan="3" align="center" class="testost_odd_calc"><input size="45" class="testimp_odd_calc" value="<?php echo $indirizzi;?>" id="ind" readonly="readonly" type="text"></td>
	</tr>

	<tr><td colspan="7"><br></td><tr>
	
	<tr><td colspan="7" class="testost" align="center">Parametri Lotti Interclusi</td></tr>

	<tr>
		<td colspan="4" align="center" class="testost_odd">Tipologia Intervento</td>
		<td colspan="3" align="right" class="testost_odd_calc">
											<select class="testimp_odd_calc" id="per_3" name="per_3" onFocus="startCalc_li();" onBlur="stopCalc_li()";>
													<option value="1" >Nuova costruzione</option>>
													<option value="2" >Ampliamento o sopra-elevazione</option>
													
											</select>
	</tr>
	<tr>
		<td colspan="4" align="center" class="testost_even">Zona di intervento:</td>
		<td colspan="3" align="right" class="testost_even_calc"><select class="testimp_even_calc" id="per_2" name="per_2" onKeyUp="calc()";>
													<option value="B1" >B1</option>>
													<option value="B2" >B2</option>
													
											</select>
	</tr>
	<tr>
		<td colspan="4" align="center" class="testost_odd">Corrispettivo monetario unitario Euro/mq:</td>
		<td colspan="3" align="right" class="testost_odd"><input size="10" value="<?php echo $quota; ?>" class="testimp_odd" id="moneta" name="moneta" type="text" onKeyUp="calc()"></td>
													
											
	</tr>
	<tr><td colspan="7"><br></td></tr>
<tr>
		
		<td width="10" align="center" class="testost">Superficie Lotto (mq)</td>
		<td width="10" align="center" class="testost">Volume Esistente (mc)</td>
		<td width="10" align="center" class="testost">Volume in progetto (mc)</td>
		<td width="10" align="center" class="testost">Volume 3/1 (mc)</td>
		<td width="5" align="center" class="testost">Volume di calcolo (mc)</td>
		<td width="10" align="center" class="testost">Cessione (mq)</td>
		<td width="10" align="center" class="testost">30% lotto (comma F) (mq)</td>
	</tr>
	<tr>
		<td align="center" class="testost_odd_calc"><input size="10" class="testimp_odd_calc" id="sup_l" name="sup_l" value="<?php echo $prm['slot']?>" type="text" onKeyUp="calc()"></td>
		<td align="center" class="testost_odd_calc"><input size="10" class="testimp_odd_calc" id="vol_es" name="vol_es" type="text" value="<?php echo $prm['ve']?>" onKeyUp="calc()"></td>
		<td align="center" class="testost_odd_calc"><input size="10" class="testimp_odd_calc" id="vol_pr" name="vol_pr" type="text" value="<?php echo $prm['vp']?>" onKeyUp="calc()"></td>
		<td align="center" class="testost_odd"><input size="10" readonly="readonly" class="testimp_odd" id="vol_3_1" name="vol_3_1"> </td>
		<td align="center" class="testost_odd"><input size="10" readonly="readonly" class="testimp_odd" id="vol_calc" name="vol_calc"> </td>
		<td align="center" class="testost_odd"><input size="10" readonly="readonly" class="testimp_odd" id="cess" name="cess"> </td>
		<td align="center" class="testost_odd"><input size="10" readonly="readonly" class="testimp_odd" id="sup_30" name="sup_30"> </td>
	</tr>
	
	<tr><td colspan="7"><br></td></tr>
	 	 	
	<tr>
		<td colspan="7" align="center" class="testost">Norme di monetizzazione</td>
	</tr>
	<tr>
		<td colspan="7" align="center" class="testost_even"><input size="120" readonly="readonly" class="testimp_even" id="norme" name="norme"></td>
	</tr>
	<tr><td colspan="7"><br></td></tr>
	
	<tr>
		<td colspan="7" align="center" class="testost">Calcolo senza comma F art. 6.4 NDA Piano B1 B2</td>
	</tr>
	<tr>
		<td colspan="2" align="right" class="testost_odd">Cessione calcolata:</td>
		<td align="center" class="testost_odd"><input size="10" readonly="readonly" class="testimp_odd" id="cess1" name="cess1" type="text" ></td>
		<td colspan="1" align="right" class="testost_odd">% monetiz.:</td>
		<td align="left" class="testost_odd_calc"><input size="10" class="testimp_odd_calc" id="per_cess" name="per_cess" type="text" onKeyUp="calc()" ></td>
		<td colspan="2"align="center" class="testost_odd"><input size="20" readonly="readonly" class="testimp_odd" id="con_1" name="con_1" type="text" ></td>

	</tr>
	<tr>
		<td colspan="1" align="right" class="testost_even">Cess. prop.:</td>
		<td align="center" class="testost_even"><input readonly="readonly" size="10" class="testimp_even" id="cess2" name="cess2" type="text" ></td>
		<td colspan="1" align="right" class="testost_even">Corrispettivo:</td>
		<td colspan="1"align="center" class="testost_even"><input readonly="readonly" size="10" class="testimp_even" id="corr"  name="corr" type="text" ></td>
		<td colspan="1" align="right" class="testost_even">2 Rate:</td>
		<td colspan="2"align="center" class="testost_even"><input readonly="readonly" size="10" class="testimp_even" id="rata" name="rata" type="text" ></td>
	</tr>
	<tr><td colspan="7"><br><br></td></tr>
	
	<!--<tr><td colspan="2"><input class="blueButton" type="button" value="Crea File" onClick="crea_1();" onFocus="startCalc_li();" onBlur="stopCalc_li();"></td><td colspan="6"><div id="link_down_1">Qui apparira' il link</div></td></tr>-->
	<tr>
		<td colspan="2">
			<span id="back_btn"></span>
			<span id="print_btn"></span>
			<script>
				$('#back_btn').button({
					'label':'Indietro',
					'icons':{
						'primary':'ui-icon-circle-triangle-w'
					}
				}).click(function(){
					$('#praticaFrm').submit();
				});
				$('#print_btn').button({
					'label':'Salva',
					'icons':{
						'primary':'ui-icon-disk'
					}
				}).click(function(){
					crea();
				});
			</script>
		</td>
	</tr>
	</table>
	</form>
	<form id="praticaFrm" action="/praticaweb.php" method="POST">
		<input id="pratica" name="pratica" type="hidden" value="<?php echo $pratica?>"/>
		<input id="mode" name="mode" type="hidden" value="view"/>
		<input id="azione" name="azione" type="hidden" value="Annulla"/>
		<input id="active_form" name="active_form" type="hidden" value="oneri.importi.php"/>
	</form>
	
</body>
</html>