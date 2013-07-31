//Funzioni comuni a tutti i file PHP


var now_field=""
function check_pe(name_field,val) {
	if (now_field!='' && now_field!=name_field) return;
	now_field=name_field;	
	if (name_field=="pe")
		{
		//confirm ('ciao');
		//alert (field);
		if ((val *1)+(1*1)==1) {	confirm('Campo pratica edilizia obbligatorio!!!');
									setTimeout(function() {document.getElementById("pe").focus()}, 0);
									//document.getElementBYId('pe').select();
									return false;	}
		
		$splitted=val.split('/');
		if ($splitted.length==1) {		
									confirm('Il numero di pratica deve avere il formato "anno/numero" esempio 2011/0678');
									document.getElementById("pe").value="";
									setTimeout(function() {document.getElementById("pe").focus()}, 0);
									//document.autoForm.getElementById('pra').focus()
									return false;
								
								}
		else 	{	$k=($splitted[0]*1)+(1*1);
					if ($k=($splitted[0]*1)+(1*1))
								{		
								  if ( $splitted[0].length != 4)
										{		
											confirm('Inserire l\'anno in formato 4 cifre!!!');
											document.getElementById("pe").value="";
											setTimeout(function() {document.getElementById("pe").focus()}, 0);
											return false;
										}
							if ($splitted[0] < 1980)
										{		
											confirm('Solo pratiche dal 1980 in poi!!!');
											document.getElementById("pe").value="";
											setTimeout(function() {document.getElementById("pe").focus()}, 0);
											return false;
										}
								}
					else {		
							confirm('Sono ammessi solo formati numerici come 2011/0456');
							document.getElementById("pe").value="";
							setTimeout(function() {document.getElementById("pe").focus()}, 0);
							return false;	
						 }
					if ($k=($splitted[1]*1)+(1*1))
								{		
									
									if ( $splitted[1].length != 4)
										{		
											confirm('Inserire il numero di pratica in formato 4 cifre (es. 0876)!!!');
											document.getElementById("pe").value="";
											setTimeout(function() {document.getElementById("pe").focus()}, 0);
											return false;
										}
							
								}
					else {		
							confirm('Sono ammessi solo formati numerici come 2011/0456');
							document.getElementById("pe").value="";	
							setTimeout(function() {document.getElementById("pe").focus()}, 0);
							return false;
						 }
					
				}					
		
	
	}
now_field="";
return true
	
}
	
var now_field=""
function check_int(name_field,val){	
			
	if (now_field!='' && now_field!=name_field) return;
	now_field=name_field;	
	if (name_field=="int")
		{
		
		if ((val *1)+(1*1)==1) {	confirm('Campo Intestatario obbligatorio!!!');
									setTimeout(function(){document.getElementById("int").focus()}, 0);
									return false;	}
				
		$splitted=val.split(' ');
		
		
		
		if ($splitted.length==1) {		
									confirm('Inserire nome e cognome!!!');
									document.getElementById("int").value="";
									setTimeout(function() {document.getElementById("int").focus()}, 0);
									//document.autoForm.getElementById('pra').focus()
									return false;
								
								}
					
		
	
	}
now_field="";
return true
}
	var source;
	var tipo;
	var campi = new Array();

	function cal(val, e, t) {
		
		
		
		var dt = new Array();
		xPos = (document.layers) ? e.pageX : ((document.all) ? event.x : e.clientX);
		yPos = (document.layers) ? e.pageY : ((document.all) ? event.y : e.clientY);
		
		tipo = t;
		source = val;
		if (t == 0) {
			dt[0] = val[0].value;
			dt[1] = val[1].value;
			dt[2] = val[2].value;
		}
		else
			dt = val.value.split('-');
	
		showCalendar(new Date(dt[2], dt[1] - 1, dt[0]), xPos, yPos);
	}


	
	
	
	
	
	
	
	
	
	
	
	
	
	
	


function start(s){
				  	//alert(s);		 
					switch (s) {

						  case 1:
							interval = setInterval("somma()",1);
						  break;

						  case 2:
								
							interval = setInterval("calc()",1);
						  break;

						  case 3:
							interval = setInterval("calc_li()",1);
						  break;

						 
					} 

 }

 
// l 
//Funzioni lotto intercluso 
 
 
 
 
 
 
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

} 
 
 
 function aggiornaPagina_2(stringa){
document.getElementById("link_down_1").innerHTML = stringa;
}
 
 
//funzioni per il cambio di destinazione d'uso 
 
 
 
 
function coefficiente(z) {
					splitted=z.split(";");
					zzz=splitted[2];
					strURL="get.php?zona="+zzz;
//alert(strURL);
					//Inizializzo l'oggetto xmlHttpReq
var xmlHttpReq = false;
var self = this;
// qui valutiamo la tipologia di browser utilizzato per selezionare la tipologia di oggetto da creare.
// Se sono in un browser Mozilla/Safari, utilizzo l'oggetto XMLHttpRequest per lo scambio di dati tra browser e server.
if (window.XMLHttpRequest) {self.xmlHttpReq = new XMLHttpRequest();}
// Se sono in un Browser di Microsoft (IE), utilizzo Microsoft.XMLHTTP 
//che rappresenta la classe di riferimento per questo browser
else if (window.ActiveXObject) {self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");}
//Apro il canale di connessione per regolare il tipo di richiesta.
//Passo come parametri il tipo di richiesta, url e se è o meno un operazione asincrona (isAsync)
self.xmlHttpReq.open('GET', strURL, true);

//setto l'header dell'oggetto
self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

/* Passo alla richiesta i valori del form in modo da generare l'output desiderato*/
self.xmlHttpReq.send();

/* Valuto lo stato della richiesta */
self.xmlHttpReq.onreadystatechange = function() {

												/*Gli stai di una richiesta possono essere 5
												* 0 - UNINITIALIZED
												* 1 - LOADING
												* 2 - LOADED
												* 3 - INTERACTIVE
												* 4 - COMPLETE*/

												//Se lo stato è completo 
												if (self.xmlHttpReq.readyState == 4) {//alert(self.xmlHttpReq.responseText);
																					aggiornaPagina(self.xmlHttpReq.responseText);}
												/* Aggiorno la pagina con la risposta ritornata dalla precendete richiesta dal web server.Quando la richiesta è terminata il responso della richiesta è disponibie come responseText.*/


												} 
}	
	
	
/*Questa funzione viene richiamata dall'oggetto xmlHttpReq per l'aggiornamento asincrono dell'elemento risultato*/
function aggiornaPagina(stringa){
document.getElementById("info").innerHTML = stringa;
} 
 
 
 
 
 
 
 function somma(){
					
					calc_primaria = document.formagella.primaria.value;
					//alert(calc_primaria);
					calc_secondaria = document.formagella.secondaria.value;
					u1_u2=document.formagella.zona.value;
					splitted=u1_u2.split(";");
					u1 = splitted [0];
					u2=splitted[1];
					zona=splitted[2];
					//alert(zona);
					v1=document.formagella.vol1.value;
					if (calc_primaria==1) {ur1=round(u1*v1);}
					else {ur1=roundi(0);}
					if (calc_secondaria==1) {ur2=round(u2*v1);}
					else {ur2=0;}
					if (u1.slice(0,1)==0) {u1=u1.substr(1,4);}
					if (u2.slice(0,1)==0) {u2=u2.substr(1,4);}
					document.formagella.u1.value=u1;
					document.formagella.u2.value=u2;
					document.formagella.urb1.value=ur1;
					document.formagella.urb2.value=ur2;	
					document.formagella.urb_tot.value=round((ur1 * 1) + (ur2 * 1));
					$dovuto=(document.getElementById("info").innerHTML) * round((ur1 * 1) + (ur2 * 1));
					//document.write(document.getElementById("info").innerHTML);
					document.formagella.dovuto.value=round($dovuto);
						
				}

				
				
///////////////////////////////				
//FUNZIONI nuove costruzioni
//////////////////////////////				
				
function crea() {

m1=document.autoSumForm.pe.value;
m2=document.autoSumForm.da_od.value;
m3=document.autoSumForm.int.value;
m4=document.autoSumForm.ind.value;
m5=document.autoSumForm.n_alloggi1.value;
m6=document.autoSumForm.n_alloggi2.value;
m7=document.autoSumForm.n_alloggi3.value;
m8=document.autoSumForm.n_alloggi4.value;
m9=document.autoSumForm.n_alloggi5.value;
m10=document.autoSumForm.su_1.value;
m11=document.autoSumForm.su_2.value;
m12=document.autoSumForm.su_3.value;
m13=document.autoSumForm.su_4.value;
m14=document.autoSumForm.su_5.value;
m27=document.autoSumForm.snr_1.value;
m28=document.autoSumForm.snr_2.value;
m29=document.autoSumForm.snr_3.value;
m30=document.autoSumForm.snr_4.value;
m42=document.autoSumForm.zona.value;
m43=document.autoSumForm.vol1.value;
m46=document.autoSumForm.car1.value;
m47=document.autoSumForm.car2.value;
m48=document.autoSumForm.car3.value;
m49=document.autoSumForm.car4.value;
m50=document.autoSumForm.car5.value;
m67=document.autoSumForm.sk_n_tab.value;
m68=document.autoSumForm.sk_a_tab.value;
m72=document.autoSumForm.c_base.value;
m75=document.autoSumForm.costo_doc_1.value;
m77=document.autoSumForm.costo_doc_2.value;
m79=document.autoSumForm.costo_doc_3.value;
m80=document.autoSumForm.pe.value;
m81=document.autoSumForm.per_1.value;
m82=document.autoSumForm.per_2.value;
m83=document.autoSumForm.per_3.value;
m84=document.autoSumForm.per_4.value;
m85=document.autoSumForm.per_5.value;
m88=document.autoSumForm.primaria.value;
m89=document.autoSumForm.secondaria.value;



s = "m1="+m1 + "&m2="+m2 + "&m3="+m3 + "&m4="+m4 + "&m5="+m5 + "&m6="+m6 + "&m7="+m7 + "&m8="+m8 + "&m9="+m9;



s = s + "&m10="+m10 + "&m11="+m11 + "&m12="+m12 + "&m13="+m13 + "&m14="+m14 + "&m27="+m27 + "&m28="+m28 + "&m29="+m29 + "&m30="+m30 + "&m42="+m42;					
s = s + "&m43="+m43 + "&m46="+m46 + "&m47="+m47 + "&m48="+m48 + "&m49="+m49 + "&m50="+m50 + "&m67="+m67 + "&m68="+m68 + "&m72="+m72 + "&m75="+m75 + "&m77="+m77;					
s = s + "&m79="+m79 + "&m80="+m80 + "&m81="+m81 + "&m82="+m82 + "&m83="+m83 + "&m84="+m84 + "&m85="+m85 + "&m88="+m88 + "&m89="+m89;										
confirm(s);				

					
					
strURL='create_f.php?'+s;



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
												
												//Se lo stato è completo 
												if (self.xmlH.readyState == 4) {//alert(self.xmlHttpReq.responseText);
																					aggiornaPagina_1(self.xmlH.responseText);}
												/* Aggiorno la pagina con la risposta ritornata dalla precendete richiesta dal web server.Quando la richiesta è terminata il responso della richiesta è disponibie come responseText.*/


												} 






//confirm(strURL);
}


function aggiornaPagina_1(stringa){

document.getElementById("link_down").innerHTML = stringa;
}				
				
				
				
				
function calc(){
				/////////////////////////////
				//calcolo la somma della SU//
				/////////////////////////////
				one = document.autoSumForm.su_1.value;
				two = document.autoSumForm.su_2.value; 
				tree = document.autoSumForm.su_3.value; 
				four = document.autoSumForm.su_4.value; 
				five = document.autoSumForm.su_5.value; 
				sum_su = (one * 1) + (two * 1) + (tree * 1) +(four * 1) + (five * 1);
				sum_su=round(sum_su);
				document.autoSumForm.su_tot.value=sum_su;
				//////////////////////////////
				//calcolo i rapporti SU/STOT//
				//////////////////////////////
				r1= round(one / sum_su);
				r2= round(two / sum_su);
				r3= round(tree / sum_su);
				r4= round(four / sum_su);
				r5= round(five / sum_su);
				document.autoSumForm.r_1.value = r1;
				document.autoSumForm.r_2.value = r2;
				document.autoSumForm.r_3.value = r3;
				document.autoSumForm.r_4.value = r4;
				document.autoSumForm.r_5.value = r5;
				////////////////////////////////////////
				//calcolo % incremento sulle superfici//
				///////////////////////////////////////
				i1 = round (100 * 0 * r1);
				i2 = round (100 * 0.05 * r2);
				i3 = round (100 * 0.15 * r3);
				i4 = round (100 * 0.30 * r4);
				i5 = round (100 * 0.50 * r5);
				document.autoSumForm.i1.value = i1;
				document.autoSumForm.i2.value = i2;
				document.autoSumForm.i3.value = i3;
				document.autoSumForm.i4.value = i4;
				document.autoSumForm.i5.value = i5;
				////////////////////////////////////////
				//calcolo  incremento 1//
				///////////////////////////////////////
				if (sum_su==0)	{incremento1=round(0);}
				else {incremento1 = round((i1 * 1) + (i2 * 1) + (i3 * 1) + (i4 * 1) + (i5 * 1));}
				document.autoSumForm.incr1.value = incremento1;
				////////////////////////////////////////
				//calcolo totale snr 1//
				///////////////////////////////////////
				snr_one = document.autoSumForm.snr_1.value;
				snr_two = document.autoSumForm.snr_2.value; 
				snr_tree = document.autoSumForm.snr_3.value; 
				snr_four = document.autoSumForm.snr_4.value; 
				sum_snr = (snr_one * 1) + (snr_two * 1) + (snr_tree * 1) + (snr_four * 1);
				sum_snr=round(sum_snr);
				document.autoSumForm.snr_tot.value = sum_snr;
				////////////////////////////////////////
				//calcolo snr / su * 100//
				///////////////////////////////////////
				if (sum_su==0)	{snr_su=0;}
				else {snr_su=round(sum_snr/sum_su*100);}
				document.autoSumForm.snr_per.value = snr_su;
				////////////////////////////////////////
				//calcolo percentuale di incremento//
				///////////////////////////////////////
				if (snr_su<50) {incremento2=round(0);}
				else if (snr_su<75) {incremento2=round(10);}
				else if (snr_su<100) {incremento2=round(20);}
				else {incremento2=round(30);}
				document.autoSumForm.incr2.value = incremento2;
				//////////////////////////////////////////
				///incremento caratteristiche ricorrenti//
				//////////////////////////////////////////
				c_car1 = document.autoSumForm.car1.value;
				c_car2 = document.autoSumForm.car2.value;
				c_car3 = document.autoSumForm.car3.value;
				c_car4 = document.autoSumForm.car4.value;
				c_car5 = document.autoSumForm.car5.value;
				if (c_car1==1) {c_c1=10;}
				else {c_c1=0;}
				if (c_car2==1) {c_c2=10;}
				else {c_c2=0;}
				if (c_car3==1) {c_c3=10;}
				else {c_c3=0;}
				if (c_car4==1) {c_c4=10;}
				else {c_c4=0;}
				if (c_car5==1) {c_c5=10;}
				else {c_c5=0;}
				incremento3 = (c_c1 * 1) +  (c_c2 * 1) +(c_c3 * 1) +(c_c4 * 1) + (c_c5 * 1);
				document.autoSumForm.c1.value=c_c1;
				document.autoSumForm.c2.value=c_c2;
				document.autoSumForm.c3.value=c_c3;
				document.autoSumForm.c4.value=c_c4;
				document.autoSumForm.c5.value=c_c5;
				document.autoSumForm.incr3.value=incremento3;
				//////////////////////////////////////////////
				///incremento totale, classe e maggiorazione//
				//////////////////////////////////////////////
				incr_totale=(incremento1 *1) + (incremento2 * 1) +(incremento3 * 1);
				document.autoSumForm.incr_tot.value=incr_totale;

				if (incr_totale<5) {cla=1;mag=0;}
				if (incr_totale>=5 && incr_totale<10) {cla=2;mag=5;}
				if (incr_totale>=10 && incr_totale<15) {cla=3;mag=10;}
				if (incr_totale>=15 && incr_totale<20) {cla=4;mag=15;}
				if (incr_totale>=20 && incr_totale<25) {cla=5;mag=20;}
				if (incr_totale>=25 && incr_totale<30) {cla=6;mag=25;}
				if (incr_totale>=30 && incr_totale<35) {cla=7;mag=30;}
				if (incr_totale>=35 && incr_totale<40) {cla=8;mag=35;}
				if (incr_totale>=40 && incr_totale<45) {cla=9;mag=40;}
				if (incr_totale>=45 && incr_totale<50) {cla=10;mag=45;}
				if (incr_totale>=50) {cla=11;mag=50;}
				document.autoSumForm.classe.value=cla;
				document.autoSumForm.maggiorazione.value=mag;
				/////////////////////////////////////
				///calcolo superfici ragguagliate//
				////////////////////////////////////
				snr60=round(sum_snr*0.6);
				scomp=(sum_su*1)+(snr60*1);
				document.autoSumForm.s_tab.value=sum_su;
				document.autoSumForm.snr_tab.value=sum_snr;
				document.autoSumForm.snr60_1.value=snr60;
				scomp=round(scomp);
				document.autoSumForm.sc_1.value=scomp;
				///////////////////////////////////////
				///calcolo superfici non residenziali//
				//////////////////////////////////////
				sk_n = document.autoSumForm.sk_n_tab.value;
				sk_a = document.autoSumForm.sk_a_tab.value;
				sk_60 = (sk_a * 0.6);
				sk_60 = round(sk_60);
				sk_t=(sk_n * 1) + (sk_60 * 1);
				document.autoSumForm.sk_a60_1.value = sk_60;
				document.autoSumForm.sk_t_1.value = round(sk_t);
				//document.write(sum_su);
				///////////////
				///calcolo K//
				///////////////
				if (sum_su==0)	{c_k=0;}
				else {c_k = round( sk_t / sum_su *100);}
				document.autoSumForm.k_1.value = c_k;
				//////////////////////////////
				///calcolo costo costruzione//
				/////////////////////////////
				if (c_k <= 25) {
								costo_base = document.autoSumForm.c_base.value;
								costo_maggiorato =round ( costo_base * ( 1 + (mag/100)));
								costo=round((scomp + sk_t) * costo_maggiorato);
								document.autoSumForm.c_base_mag.value = costo_maggiorato;
								document.autoSumForm.costo_d.value = costo;
								avv="Poiche' k <= 25, il quadro sottostante non deve essere compilato."
								document.autoSumForm.avviso.value = avv;
								document.autoSumForm.costo_doc_1.value = "0.00";
								document.autoSumForm.costo_doc_2.value = "0.00";
								document.autoSumForm.contributo1.value = "0.00";
								document.autoSumForm.contributo2.value = "0.00";
								}
				else 			{
								//costo_base = 0;
								costo_maggiorato =round ( costo_base * ( 1 + (mag/100)));
								costo=round((scomp + sk_t) * costo_maggiorato);
								document.autoSumForm.c_base_mag.value = costo_maggiorato;
								document.autoSumForm.costo_d.value = costo;
								avv="N.B. - Poiche' k > 25 il costo di costruzione deve essere calcolato secondo lo schema che segue:"
								document.autoSumForm.avviso.value = avv;
								doc_1 = document.autoSumForm.costo_doc_1.value;
								doc_2 = document.autoSumForm.costo_doc_2.value
								contr_1=round(doc_1*0.07);
								contr_2=round(doc_2*0.04);
								document.autoSumForm.contributo1.value = contr_1;
								document.autoSumForm.contributo2.value = contr_2;
								//alarm(avv);
								}
				doc_3 = document.autoSumForm.costo_doc_3.value
				
				if (doc_3=="")
								{
								document.autoSumForm.contributo3.value = "0.00";
								}
				else 			{
								document.autoSumForm.contributo3.value = round(doc_3*0.05);
								}
								
				//////////////////////////////
				/// calcolo urbanizzazioni ///
				//////////////////////////////
				calc_primaria = document.autoSumForm.primaria.value;
				calc_secondaria = document.autoSumForm.secondaria.value;
				u1_u2=document.autoSumForm.zona.value;
				splitted=u1_u2.split(";");
				$zona_urb=splitted [0];
				u1 = splitted [1];
				u2=splitted[2];
				v1=document.autoSumForm.vol1.value;
				if (calc_primaria==1)   {ur1=round(u1*v1);}
				else {ur1=round(0);}
				if (calc_secondaria==1) {ur2=round(u2*v1);}
				else {ur2=0;}
				if (u1.slice(0,1)==0) {u1=u1.substr(1,4);}
				if (u2.slice(0,1)==0) {u2=u2.substr(1,4);}
				document.autoSumForm.u1.value=u1;
				document.autoSumForm.u2.value=u2;
				document.autoSumForm.urb1.value=ur1;
				document.autoSumForm.urb2.value=ur2;
				////////////////////////////////////////////
				///calcolo percentuale e costo costruzione//
				///////////////////////////////////////////
				p_1 = document.autoSumForm.per_1.value;
				p_2 = document.autoSumForm.per_2.value;				
				p_3 = document.autoSumForm.per_3.value;
				p_4 = document.autoSumForm.per_4.value;
				p_5 = document.autoSumForm.per_5.value;			
				p_t= (p_1 * 1) + (p_2 * 1) + (p_3 * 1) + (p_4 * 1) + (p_5 * 1)
				document.autoSumForm.per_tot.value=p_t;				
				costo_costruzione = (costo * p_t / 100);
				costo_costruzione = round (costo_costruzione);
				document.autoSumForm.oneri_costr.value = costo_costruzione;
				if (c_k <= 25)  {
								oneri_costruzione = costo_costruzione;
								}
				else 			{
								oneri_costruzione= round( (contr_1 * 1) + (contr_2 * 1) + (costo_costruzione *1));
								}
				document.autoSumForm.costruzione1.value = oneri_costruzione;
				urban_tot = round ((ur1 * 1) + (ur2 * 1));			
				document.autoSumForm.urbanizzazione1.value = urban_tot;
				document.autoSumForm.totale_oneri.value = round((urban_tot *1) + ( oneri_costruzione * 1));
				//////////////////
				///calcolo rate//
				//////////////////
				r1 = (urban_tot * 0.5)  + (oneri_costruzione * 0.3);
				r2 = (urban_tot * 0.25) + (oneri_costruzione * 0.3);	
				r3 = (urban_tot * 0.25) + (oneri_costruzione * 0.4);						
				fides = (urban_tot *1) +  (oneri_costruzione * 1) - (r1	* 1);
				document.autoSumForm.rata1.value = round(r1);	
				document.autoSumForm.rata2.value = round(r2);
				document.autoSumForm.rata3.value = round(r3);
				document.autoSumForm.fide.value  = round(fides);
				}	
	
	
	

				 
				 
				 
function stop_calc(){
				clearInterval(interval);
				}
		
function round(numero){
				arrotondato=numero.toFixed(2);
				return arrotondato;		
		  }