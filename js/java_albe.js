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





	


////////////////////
//// CALCOLI///////
///////////////////









	
	
	
	

				 
				 
				 
