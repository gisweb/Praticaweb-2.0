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

		
?>
<script src="../js/LoadLibs.js"></script>
<link rel="stylesheet" type="text/css" href="../css/simple.css" />
<link rel="stylesheet" type="text/css" href="../css/theme.css" />

<script language="JavaScript">
	
	function check_pe($field) {
		//confirm ('ciao');
		
		
		if (($field *1)+(1*1)==1) {	confirm('Campo pratica edilizia obbligatorio!!!');
									setTimeout(function() {document.autoForm.pe.focus()}, 0);
									return false;	}
		
		
		$splitted=$field.split('/');
		
		
		
		if ($splitted.length==1) {		
									confirm('Il numero di pratica deve avere il formato "anno/numero" esempio 2011/0678');
									
									
									document.autoForm.pe.value="";
									setTimeout(function() {document.autoForm.pe.focus()}, 0);
									//document.autoForm.getElementById('pra').focus()
									return false;
								
								}
		else 	{
					$k=($splitted[0]*1)+(1*1);
					
					if ($k=($splitted[0]*1)+(1*1))
								{		
									
									if ( $splitted[0].length != 4)
										{		
											confirm('Inserire l\'anno in formato 4 cifre!!!');
											document.autoForm.pe.value="";
											setTimeout(function() {document.autoForm.pe.focus()}, 0);
											return false;
										}
							if ($splitted[0] < 1980)
										{		
											confirm('Solo pratiche dal 1980 in poi!!!');
											document.autoForm.pe.value="";
											setTimeout(function() {document.autoForm.pe.focus()}, 0);
											return false;
										}
								}
					else {		
							confirm('Sono ammessi solo formati numerici come 2011/0456');
							document.autoForm.pe.value="";
							setTimeout(function() {document.autoForm.pe.focus()}, 0);
							return false;	
						 }
					
					
					if ($k=($splitted[1]*1)+(1*1))
								{		
									
									if ( $splitted[1].length != 4)
										{		
											confirm('Inserire il numero di pratica in formato 4 cifre (es. 0876)!!!');
											document.autoForm.pe.value="";
											setTimeout(function() {document.autoForm.pe.focus()}, 0);
											return false;
										}
							
								}
					else {		
							confirm('Sono ammessi solo formati numerici come 2011/0456');
							document.autoForm.pe.value="";	
							setTimeout(function() {document.autoForm.pe.focus()}, 0);
							return false;
						 }
					
				}					
		
	
	}

	</script>
<script language="JavaScript">
	
	function check_int($field) {
		//confirm ('ciao');
		
		
		if (($field *1)+(1*1)==1) {	confirm('Campo Intestatario obbligatorio!!!');
									setTimeout(function() {document.autoForm.int.focus()}, 0);
									return false;	}
		
		
		$splitted=$field.split(' ');
		
		
		
		if ($splitted.length==1) {		
									confirm('Inserire nome e cognome!!!');
									
									
									document.autoForm.pe.value="";
									setTimeout(function() {document.autoForm.int.focus()}, 0);
									//document.autoForm.getElementById('pra').focus()
									return false;
								
								}
					
		
	
	}

	</script>

<script language="javascript" type="text/javascript">

function crea_1() {
		

m1 =$('#pe').val();
m2 =$('#da_od').val();
m3 =$('#int').val();
m4 =$('#ind').val();
m5 =$('#per_3').val();
m6 =$('#per_2').val();
m7 =$('#moneta').val();
m8 =$('#sup_l').val();
m9 =$('#vol_es').val();
m10=$('#vol_pr').val();
m11=$('#per_cess').val() ;
m12=$('#con_1').val()  ;
m13=$('#cess2').val() ;
m14=$('#corr').val() ;
m15=$('#rata').val() ;
m16=$('#vol_3_1').val() ;
m17=$('#vol_calc').val() ;
m18=$('#cess').val() ;
m19=$('#sup_30').val()  ;
m20=$('#cess1').val() ;
//confirm('Ciao');



s = "m1="+m1 + "&m2="+m2 + "&m3="+m3 + "&m4="+m4 + "&m5="+m5 + "&m6="+m6 + "&m7="+m7 + "&m8="+m8 + "&m9="+m9;
s = s + "&m10="+m10 + "&m11="+m11 + "&m12="+m12 + "&m13="+m13 + "&m14="+m14 + "&m15="+m15 + "&m16="+m16 + "&m17="+m17 + "&m18="+m18 + "&m19="+m19 + "&m20="+m20;
//confirm(s);	
		

					
					
strURL='create_l.php?'+s;



var xmlH = false;
var self = this;

if (window.XMLHttpRequest) {self.xmlH = new XMLHttpRequest();}
else if (window.ActiveXObject) {self.xmlH = new ActiveXObject("Microsoft.XMLHTTP");}

self.xmlH.open('GET', strURL, true);
self.xmlH.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
self.xmlH.send();











self.xmlH.onreadystatechange = function() {		/*Gli stai di una richiesta possono essere 5
												* 0 - UNINITIALIZED
												* 1 - LOADING
												* 2 - LOADED
												* 3 - INTERACTIVE
												* 4 - COMPLETE*/

												//Se lo stato � completo 
												if (self.xmlH.readyState == 4) {//alert(self.xmlHttpReq.responseText);
																					aggiornaPagina_2(self.xmlH.responseText);}
												/* Aggiorno la pagina con la risposta ritornata dalla precendete richiesta dal web server.Quando la richiesta � terminata il responso della richiesta � disponibie come responseText.*/


												} 






//confirm(strURL);
}

function aggiornaPagina_2(stringa){
document.getElementById("link_down_1").innerHTML = stringa;
}



</script>


<script language="javascript" type="text/javascript">

function startCalc_li(){
		interval = setInterval("calc_li()",1);
}

function round(numero){
arrotondato=numero.toFixed(2);
return arrotondato;
}



function calc_li(){
/////////////////////////////
//calcolo la somma della SU//
/////////////////////////////



superficie_l = document.autoForm.sup_l.value;
volume_es = document.autoForm.vol_es.value; 
volume_pr = document.autoForm.vol_pr.value; 
percentuale_cess = document.autoForm.per_cess.value;
moneta_unit = document.autoForm.moneta.value;

if ((volume_es * 1)==0) {volume_es=0;}

volume_3_1 = superficie_l * 3;

if (volume_3_1 < volume_es ) {volume_calc=(volume_pr * 1 ) ;}
else {volume_calc=(volume_pr * 1) + (volume_es *1)- (volume_3_1 *1);}
cessione = round((volume_calc * 18) / 100);
superficie_30 = round(superficie_l * 0.3);


document.autoForm.vol_3_1.value = volume_3_1;
document.autoForm.vol_calc.value = volume_calc;
document.autoForm.cess.value = cessione;
document.autoForm.sup_30.value = superficie_30 ;
document.autoForm.cess1.value = cessione;

function stopCalc_li(){
				clearInterval(interval);
				}



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
								 controllo1="Monetiz. non corretta"   };
								 cess2 = "------";
 								 corrispettivo = "------";
								 rata = "------";//round (corrispettivo/2);

								 }			

								 
document.autoForm.norme.value = rule ;
document.autoForm.con_1.value = controllo1 ;
document.autoForm.cess2.value = cess2 ;
document.autoForm.corr.value = corrispettivo ;
console.log(rata);
$('#rata').val(rata) ;







}



function stopCalc_li(){
clearInterval(interval);
}

</script>

	<h4>Calcolo Corrispettivo Monetario Lotti Interclusi Zone B</h4>
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
	<tr><td colspan="7"><br><br> </td></tr>
	<tr><td colspan="7"><br><br> </td></tr>
	<tr><td colspan="7"><br></td><tr>
	
	<tr><td colspan="7" class="testost" align="center">Parametri Lotti Interclusi</td></tr>
	
	<tr><td colspan="7"><br></td></tr>
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
		<td colspan="3" align="right" class="testost_even_calc"><select class="testimp_even_calc" id="per_2" name="per_2" onFocus="startCalc_li();" onBlur="stopCalc_li()";>
													<option value="B1" >B1</option>>
													<option value="B2" >B2</option>
													
											</select>
	</tr>
	<tr>
		<td colspan="4" align="center" class="testost_odd">Corrispettivo monetario unitario Euro/mq:</td>
		<td colspan="3" align="right" class="testost_odd"><input size="10" value="<?php echo $quota; ?>" class="testimp_odd" id="moneta" name="moneta" type="text" onFocus="startCalc_li();" onBlur="stopCalc_li();"></td>
													
											
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
		<td align="center" class="testost_odd_calc"><input size="10" class="testimp_odd_calc" id="sup_l" name="sup_l" type="text" onFocus="startCalc_li();" onBlur="stopCalc_li();"></td>
		<td align="center" class="testost_odd_calc"><input size="10" class="testimp_odd_calc" id="vol_es" name="vol_es" type="text" onFocus="startCalc_li();" onBlur="stopCalc_li();"></td>
		<td align="center" class="testost_odd_calc"><input size="10" class="testimp_odd_calc" id="vol_pr" name="vol_pr" type="text" onFocus="startCalc_li();" onBlur="stopCalc_li();"></td>
		<td align="center" class="testost_odd"><input size="10" readonly="readonly" class="testimp_odd" id="vol_3_1" name="vol_3_1"> </td>
		<td align="center" class="testost_odd"><input size="10" readonly="readonly" class="testimp_odd" id="vol_calc" name="vol_calc"> </td>
		<td align="center" class="testost_odd"><input size="10" readonly="readonly" class="testimp_odd" id="cess" name="cess"> </td>
		<td align="center" class="testost_odd"><input size="10" readonly="readonly" class="testimp_odd" id="sup_30" name="sup_30"> </td>
	</tr>
	
	<tr><td colspan="7"><br><br> </td></tr>
	 	 	
	<tr>
		<td colspan="7" align="center" class="testost">Norme di monetizzazione</td>
	</tr>
	<tr>
		<td colspan="7" align="center" class="testost_even"><input size="120" readonly="readonly" class="testimp_even" id="norme" name="norme"></td>
	</tr>
	<tr><td colspan="7"><br><br> </td></tr>
	
	<tr>
		<td colspan="7" align="center" class="testost">Calcolo senza comma F art. 6.4 NDA Piano B1 B2</td>
	</tr>
	<tr>
		<td colspan="2" align="right" class="testost_odd">Cessione calcolata:</td>
		<td align="center" class="testost_odd"><input size="10" readonly="readonly" class="testimp_odd" id="cess1" name="cess1" type="text" ></td>
		<td colspan="1" align="right" class="testost_odd">% monetiz.:</td>
		<td align="left" class="testost_odd_calc"><input size="10" class="testimp_odd_calc" id="per_cess" name="per_cess" type="text" onFocus="startCalc_li();" onBlur="stopCalc_li();" ></td>
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
	
	<tr><td colspan="2"><input class="blueButton" type="button" value="Crea File" onClick="crea_1();" onFocus="startCalc_li();" onBlur="stopCalc_li();"></td><td colspan="6"><div id="link_down_1">Qui apparira' il link</div></td></tr>
	
	
	
	
	
	<?php
	echo"</table></form>";
	

	
	
	
	
?>
