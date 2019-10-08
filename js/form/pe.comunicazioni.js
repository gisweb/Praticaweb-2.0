/**
 * Created by mamo on 05/07/17.
 */
$(document).ready(function(){
    $("#tipo_comunicazione").change(function () {
        var v = $(this).val();
        if (!v) $("#destinatari").children('option').show();
        else {
            $("#destinatari").children('option').hide();
            $("#destinatari").children("option[data-metodo='" + v + "']").show()
        }

    });
    $("#tipo_comunicazione").trigger("change");

    var idCom = $("#id_comunicazione").val();
    if (idCom){
        $('#azione-mail').hide();
        $('#azione-elimina').hide();
        $('#azione-salva').hide();
    }
});
