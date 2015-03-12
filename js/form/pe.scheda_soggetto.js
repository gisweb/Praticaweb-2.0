$(document).ready(function(){
    verificaRuoloSoggetti();
    $('form#scheda_soggetto').bind('submit',function(event){
        var ischeck=($('[data-plugins=tipo_soggetto]:checked').length > 0);
	if (!ischeck)
		alert ('Attenzione occorre assegnare un ruolo al soggetto in fase di inserimento');
	return ischeck;
    });
});