$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".migracion").on('dblclick','.dobleclickpc', function(e) {

        var _token                  =   $('#token').val();
        var cod_documento_id        =   $(this).attr('data_cod_documento');
        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            cod_documento_id        : cod_documento_id,
                                            idopcion                : idopcion
                                        };

        ajax_modal(data,"/ajax-modal-detalle-producto-migracion-ventas",
                  "modal-detalle-producto-xdocumento","modal-detalle-producto-xdocumento-container");

    });

 
    $(".migracion").on('click','#generarasientos', function(e) {
        e.preventDefault();
        data = dataenviar();
        if(data.length<=0){alerterrorajax('Seleccione por lo menos un documento'); return false;}
        var datastring = JSON.stringify(data);
        $('#documentos').val(datastring);
        abrircargando();
        $( "#formgenerarasiento" ).submit();
    });

});

function dataenviar(){
    var data = [];
    $(".listatabla tr").each(function(){

        nombre          = $(this).find('.input_asignar').attr('id');

        if(nombre != 'todo_asignar'){

            check           = $(this).find('.input_asignar');
            cod_documento     = $(this).attr('data_cod_documento');
            cod_tipo_asiento     = $(this).attr('data_cod_tipo_asiento');


            if($(check).is(':checked')){
                data.push({
                    cod_documento     : cod_documento,
                    cod_tipo_asiento     : cod_tipo_asiento
                });
            }               
        }
    });
    return data;
}