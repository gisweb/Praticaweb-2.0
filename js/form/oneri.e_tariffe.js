$(document).ready(function(){
    
     if ($('#mode').val()!='new'){
        $('#btn-elimina').button({
            icons:{
                primary:'ui-icon-circle-close'
            },
            label:'Elimina'
        }).click(function(){
            if(!($('#anno').val())){
                alert('Selezionare un anno da eliminare');
                return;
            }
            if (confirm('Sei sicuro di voler eliminare il record?')){
                $('#azione').val('Elimina');   
                $('#mode').val('list');
                $('#data-form').submit();
            }
            
            
        });
    }
    
   $('#btn-nuovo').button({
        icons:{
            primary:'ui-icon-plusthick'
        },
        label:'Nuovo'
    }).click(function(){
            $('#new_form').submit();
    }); 
    $('#btn-annulla').button({
        icons:{
            primary:'ui-icon-circle-triangle-w'
        },
        label:'Annulla'
    }).click(function(){
        $('#azione').val('Annulla');
        $('#mode').val('list');
        $('#data-form').submit();
    });
   
    $('#btn-salva').button({
        icons:{
            primary:'ui-icon-disk'
        },
        label:'Salva'
    }).click(function(){
        
        $('#azione').val('Salva');
        $('#mode').val('list');
        $('#data-form').submit();
    });
    $('.textbox-data').datepicker({
        dateFormat:'dd-mm-yy',
        changeMonth: true,
	changeYear: true
    });
});

