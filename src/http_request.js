	// AJAX FUNZIONE 
    var http_request = false;			
			
	function makeRequest(url,parameters,funct,method) {
		
		//alert(parameters);
		
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
			http_request.send(parameters);
		}
		else{
			http_request.open('GET', url +'?'+parameters, true);
			http_request.send(null);
		}	
		
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
							//alert(strResponse);
							//var risposta=eval(strResponse);
							//alert(risposta);
							eval(funct + '('+strResponse+');');
			            }
			            break;
				}
			}
		}
				
			//METODO GET

			
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
