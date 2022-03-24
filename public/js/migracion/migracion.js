$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".migracion").on('change','#anio', function() {

        event.preventDefault();
        var anio        =   $('#anio').val();
        var _token      =   $('#token').val();
        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un anio."); return false;}
        data            =   {
                                _token      : _token,
                                anio        : anio
                            };

        ajax_normal_combo(data,"/ajax-combo-periodo-xanio-xempresa","ajax_anio")                    

    });


    $(".migracion").on('click','.buscarregistrodiario', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var tipo_asiento_id         =   $('#tipo_asiento_id').val();
        var periodo_id              =   $('#periodo_id').val();
        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        if(tipo_asiento_id ==''){ alerterrorajax("Seleccione un tipo de asiento."); return false;}
        data            =   {
                                _token                  : _token,
                                tipo_asiento_id         : tipo_asiento_id,
                                anio                    : anio,
                                periodo_id              : periodo_id,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-observacion-documentos-ventas");

    });


    $(".migracion").on('dblclick','.dobleclickpc', function(e) {

        var _token                  =   $('#token').val();
        var cod_documento_id        =   $(this).attr('data_cod_documento');
        var cod_tipo_asiento        =   $(this).attr('data_cod_tipo_asiento');
        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            cod_documento_id        : cod_documento_id,
                                            cod_tipo_asiento        : cod_tipo_asiento,
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