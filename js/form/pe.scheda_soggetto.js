$(document).ready(function(){
    verificaRuoloSoggetti();
	var label;
	$('input[type=checkbox]').each(function () {
		label = $(this).data('label');
		if (label){
			 $(this).prev().text(label);
		}
        console.log($(this).attr('id')+ " : " + $(this).prev().text()); 
    });
    $('form#scheda_soggetto').bind('submit',function(event){
    
        var ischeck=($('[data-plugins=tipo_soggetto]:checked').length > 0);
	if (!ischeck)
		alert ('Attenzione occorre assegnare un ruolo al soggetto in fase di inserimento');
	return ischeck;
    });
});