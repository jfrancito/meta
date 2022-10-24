$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".recibohonorario").on('change','#anio', function() {

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


    $(".recibohonorario").on('click','.clickasientorecibohonorario', function(e) {

        var _token                  =   $('#token').val();
        var asiento_id              =   $(this).attr('data_asiento_id');
        var idopcion                =   $('#idopcion').val();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var serie                   =   $('#serie').val();
        var documento               =   $('#documento').val();

        data                        =   {
                                            _token                  : _token,
                                            asiento_id              : asiento_id,
                                            idopcion                : idopcion,
                                            anio                    : anio,
                                            periodo_id              : periodo_id,
                                            serie                   : serie,
                                            documento               : documento,
                                        };
        ajax_modal(data,"/ajax-modal-detalle-asiento-rh-confirmar",
                  "modal-detalle-asiento-confirmar","modal-detalle-asiento-confirmar-container");

    });


    $(".recibohonorario").on('dblclick','.dobleclickpcreversion', function(e) {

        var _token                  =   $('#token').val();
        var asiento_id              =   $(this).attr('data_asiento_id');
        var idopcion                =   $('#idopcion').val();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var serie                   =   $('#serie').val();
        var documento               =   $('#documento').val();

        data                        =   {
                                            _token                  : _token,
                                            asiento_id              : asiento_id,
                                            idopcion                : idopcion,
                                            anio                    : anio,
                                            periodo_id              : periodo_id,
                                            serie                   : serie,
                                            documento               : documento,
                                        };
        ajax_modal(data,"/ajax-modal-detalle-rh-asiento-transicion",
                  "modal-detalle-asiento-confirmar","modal-detalle-asiento-confirmar-container");

    });


});

function dataenviar(){
    var data = [];
    $(".listatabla tr").each(function(){
        nombre          = $(this).find('.input_asignar').attr('id');
        if(nombre != 'todo_asignar'){

            check           = $(this).find('.input_asignar');
            asiento_id     = $(this).attr('data_asiento_id');

            if($(check).is(':checked')){
                data.push({
                    asiento_id        : asiento_id
                });
            }               
        }
    });
    return data;
}
