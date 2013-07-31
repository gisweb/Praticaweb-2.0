var bHtmlMode = false;
var str_iFrameDoc = (document.all)? "document.frames(\"Composition\").document\;": "document.getElementById(\"Composition\").contentDocument\;";
//var oFCKeditor = FCKeditorAPI.GetInstance('testo') ;
//var oFCKeditor = window.parent.InnerDialogLoaded().FCK ;

// Inizializzazione

	function setFocus() {
		if (document.all)
			document.frames("Composition").focus();
		else
			document.getElementById('Composition').contentWindow.focus()
		return;
	}
	//Funzione che richiede il salvataggio del file
	function salva(){
		iFrameDoc = eval(str_iFrameDoc);
		riquadro = iFrameDoc.body;
		txt = escape(riquadro.innerHTML);
		var getstr='azione=salva&file='+dati.nomefile.value+'&testo='+txt;
		if (dati.nomefile.value.length>0)
			makeRequest('test.risposta.php',getstr,'alertContents','POST');
		else
			alert("Inserire un nome per il file da salvare");
	}
	//Funzione che richiede il salvataggio del file con sovrascrittura del file precedente
	function sovrascrivi(file){
		iFrameDoc = eval(str_iFrameDoc);
		riquadro = iFrameDoc.body;
		txt = escape(riquadro.innerHTML);
		var getstr='azione=salva&sovrascrivi=1&file='+file+'&testo='+txt;
		makeRequest('risposta.php',getstr,'alertContents','POST');
	}

	function chiudi(){
		document.getElementById('rif').style.visibility = "hidden";
	}
	
	// AJAX FUNZIONE 
    var http_request = false;			
			
	function makeRequest(url,parameters,funct,method) {
				http_request = false;
			    if (window.XMLHttpRequest) { // Mozilla, Safari,...
					http_request = new XMLHttpRequest();
			        if (http_request.overrideMimeType) {
			            http_request.overrideMimeType('text/xml');
			        }
			    } else if (window.ActiveXObject) { // IE
			        try {
			            http_request = new ActiveXObject("Msxml2.XMLHTTP");
			        } catch (e) {
			            try {
			               http_request = new ActiveXObject("Microsoft.XMLHTTP");
			            } catch (e) {}
			        }
			    }
			    if (!http_request) {
			        alert('Cannot create XMLHTTP instance');
			        return false;
			    }
				if (method=='POST'){
					http_request.open('POST', url, true);
					http_request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				}
				else
					http_request.open('GET', url +'?'+parameters, true);
					
			    http_request.onreadystatechange = function(){
					if (http_request.readyState == 4) {
						strResponse = http_request.responseText;
						switch (http_request.status) {
			                   // Page-not-found error
			                case 404:
			                    alert('Error: Not Found. The requested URL ' + url + ' could not be found.');
			                    break;
			                   // Display results in a full window for server-side errors
			                case 500:
								handleErrFullPage(strResponse);
								break;
			                default:
			                           // Call JS alert for custom error or debug messages
			                    if (strResponse.indexOf('Error:') > -1 || strResponse.indexOf('Debug:') > -1) {
			                        alert(strResponse);
			                    }
			                           // Call the desired result function
			                    else {
									eval(funct + '(strResponse);');
			                    }
			                    break;
						}
					}
				}
				
				//METODO GET
			    if (method=='POST')
					http_request.send(parameters);
				else
					http_request.send(null);
			}

	function handleErrFullPage(strIn) {

		        var errorWin;

		        // Create new window and display error
		        try {
		                errorWin = window.open('', 'errorWin');
		                errorWin.document.body.innerHTML = strIn;
		        }
		        // If pop-up gets blocked, inform user
		        catch(e) {
		                alert('An error occurred, but the error message cannot be' +
		                        ' displayed because of your browser\'s pop-up blocker.\n' +
		                        'Please allow pop-ups from this Web site.');
		        }
			}			
			// Funzione che permette la visualizzazione delle finestre di dialogo
    function alertContents(txt) {
                d=document.getElementById('rif');
				d.innerHTML=http_request.responseText;
				d.style.visibility="";

            }
			//Funzione che costruisce la stringa dei paramentri da inviare
	function get_data(){
				var getstr = "";
				l=dati.elements.length;
				for (i=0;i<l;i++){
					elem=dati.elements[i];
					if (elem.type=="radio"){
						if (elem.checked) getstr+=elem.name+'='+elem.value+'&';
					} else if (elem.type=="select-one"){
						if (elem.selectedIndex > 0) getstr+=elem.name+'='+elem.options[elem.selectedIndex].value+'&';
					} else if (elem.type=="text"){
						if (elem.value.length>0) getstr+=elem.name+'='+elem.value+'&';
					} else if (elem.type=="hidden"){
						if (elem.value.length>0) getstr+=elem.name+'='+elem.value+'&';
					} else if (elem.type=="checkbox"){
						if (elem.checked) {
			                  getstr += elem.name+'='+elem.value+'&';
							} 
					} else if (elem.type==""){
					
					}
					
				}
				makeRequest('test.risposta.php', getstr,'alertContents','GET');
				frames.Composition.focus();
			}
	function richiesta_indentazione(){
		makeRequest('test.risposta.php','margin=1','alertContents','GET');
		document.getElementById('table_name').selectedIndex=0;
	}
	function richiesta_ciclo(){
		makeRequest('test.risposta.php','termina=1','alertContents','GET');
		document.getElementById('table_name').selectedIndex=0;
	}	
//FUNZIONI PER L'INSERIMENTO DEI TAG
	
	function stampa(){
		getstr="stampa=1&file="+dati.nomefile.value;
		makeRequest('test.risposta.php', getstr,'alertContents','GET');
	}
	function set_col(tab,num_tab){
		var tmp;
		tmp=tab.split(".");
		var sel=document.dati.column_name;
		sel.length=1;
		//for(i=1;i<sel.options.length;i++) sel.options[i]=Null;
		if (num_tab>0) {
			
			for(i=0;i<colonne[num_tab-1].length;i++){
				sel.options[i+1]=new Option(colonne[num_tab-1][i][1],colonne[num_tab-1][i][1]);
			}			
		}
	}
	function show_desc(i,j){
		
		document.getElementById("desc").innerText=colonne[i][j][2];
		window.focus();
		
	}
	function inizio_ciclo(fck){
		var oFCKeditor = FCKeditorAPI.GetInstance(fck);
		oFCKeditor.InsertHtml( '<span class="iniziocicli">IN_CICLO</span>');
	}
	function fine_ciclo(fck){
	var oFCKeditor = FCKeditorAPI.GetInstance(fck);
		oFCKeditor.InsertHtml( '<span class="finecicli">FI_CICLO</span>');
	}
	function inizio_if(fck){
	var oFCKeditor = FCKeditorAPI.GetInstance(fck);
		oFCKeditor.InsertHtml( '<span class="iniziose">INIZIO_SE</span>');
	}
	function fine_if(fck){
	var oFCKeditor = FCKeditorAPI.GetInstance(fck);
		oFCKeditor.InsertHtml( '<span class="finese">FINE_SE</span>');
	}
	function insert_tag(fck,tab,col,i,j){
		var tmp;
		var oFCKeditor = FCKeditorAPI.GetInstance(fck);
		tmp=tab.split(".");
		tag=(tmp[0]=='FUNCTION')?('F.'+tmp[1]+'.'+col):('V.'+tmp[1]+'.'+col);
		tag=' <span class="valore">'+tag+'</span> ';
		if ((i>=0) && (j>=0)) oFCKeditor.InsertHtml(tag);
		else
			alert('Attenzione!!!\nSelezionare una tabella e un campo.')
	}
	function insert_obbl_tag(fck,tab,col,i,j){
		var tmp;
		var oFCKeditor = FCKeditorAPI.GetInstance(fck);
		tmp=tab.split(".");
		var tag=(tmp[0]=='FUNCTION')?('F.'+tmp[1]+'.'+col):('V.'+tmp[1]+'.'+col);
		tag=' <span class="obbligatori">'+tag+'</span> ';
		oFCKeditor.InsertHtml(tag);
	}	
	function inserisci_data(fck){
		var oFCKeditor = FCKeditorAPI.GetInstance(fck);
		tag=' <span class="valore">D.data.data</span> ';
		oFCKeditor.InsertHtml(tag);
	}
	function insert_bp(fck){
		var tag='<!--NewPage-->';
		var oFCKeditor = FCKeditorAPI.GetInstance(fck);
		oFCKeditor.InsertHtml(tag);
	}
	