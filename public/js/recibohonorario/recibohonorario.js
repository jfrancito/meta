$(document).ready(function(){

    var carpeta = $("#carpeta").val();


   $(".recibohonorario").on('click','.buscarcompras', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var serie                   =   $('#serie').val();
        var documento               =   $('#documento').val();

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        data            =   {
                                _token                  : _token,
                                anio                    : anio,
                                periodo_id              : periodo_id,
                                serie                   : serie,
                                documento               : documento,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-listado-recibo-honorario");

    });

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
        var ruta                    =   '/ajax-modal-detalle-asiento-confirmar-rh';


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


    $(".recibohonorario").on('click','.btn-editar-asiento', function(e) {

        var _token                  =   $('#token').val();
        var cuenta_contable_id      =   $('#cuenta_contable_id').val();
        var monto                   =   $('#monto').val();
        monto                       =   monto.replace(",", "");
        var anio                    =   $('#anio_configuracion').val();
        var asiento_movimiento_id   =   $('#asiento_movimiento_id').val();
        var asiento_id_editar       =   $('#asiento_id_editar').val();
        var partida_id              =   $('#partida_id').val();
        var activo                  =   $('#activo').val();
        var ruta                    =   $('#ruta').val();
        var accion                  =   $('#accion').val();
        var idopcion                =   $('#opcion_configuracion').val();
        var periodo_id              =   $('#periodo_id_configuracion').val();
        var serie                   =   $('#serie_configuracion').val();
        var documento               =   $('#documento_configuracion').val();


        $('#modal-detalle-asiento-confirmar').niftyModal('hide');

        if(monto == '' || monto == '0.0000'){alerterrorajax("Ingrese un monto");return false;}
        if(cuenta_contable_id ==''){ alerterrorajax("Seleccione una cuenta contable."); return false;}

        data                            =   {
                                                _token                  : _token,
                                                cuenta_contable_id      : cuenta_contable_id,
                                                monto                   : monto,
                                                asiento_movimiento_id   : asiento_movimiento_id,
                                                asiento_id              : asiento_id_editar,
                                                partida_id              : partida_id,
                                                activo                  : activo,
                                                anio                    : anio,
                                                idopcion                : idopcion,
                                                accion                  : accion,
                                                periodo_id              : periodo_id,
                                                serie                   : serie,
                                                documento               : documento,
                                                ruta                    : ruta,
                                            };


        link                            =    "/ajax-editar-asiento-contable-movimiento-rh";

        ajax_modal(data,link,
                  "modal-detalle-asiento-confirmar","modal-detalle-asiento-confirmar-container");



    });



    $(".recibohonorario").on('click','.editar-cuenta', function(e) {

        var _token                  =   $('#token').val();

        data_id                     =   $(this).parents('.fila').attr('data_id');
        data_cuenta_id              =   $(this).parents('.fila').attr('data_cuenta_id');
        data_debe_mn                =   $(this).parents('.fila').attr('data_debe_mn');
        data_haber_mn               =   $(this).parents('.fila').attr('data_haber_mn');
        data_debe_me                =   $(this).parents('.fila').attr('data_debe_me');
        data_haber_me               =   $(this).parents('.fila').attr('data_haber_me');
        data_moneda                 =   $(this).parents('.fila').attr('data_moneda');
        data_registro               =   $(this).parents('.fila').attr('data_registro');

        debugger;

        if(data_registro=='editar'){
            partida                     =   'COP0000000000001';
            if(parseFloat(data_haber_mn)>0){
                partida                     =   'COP0000000000002';
            }            
        }else{
            partida                     =   '';
        }

        if(data_registro=='editar'){
            monto = parseFloat(data_debe_me)+parseFloat(data_haber_me);
            if(data_moneda == 'MON0000000000001'){
                monto = parseFloat(data_debe_mn)+parseFloat(data_haber_mn);
            }
        }else{
            monto                     =   0;
        }


        $('#cuenta_contable_id').val(data_cuenta_id.trim()).trigger('change');
        $('#partida_id').val(partida.trim()).trigger('change');

        $('#monto').val(monto);
        $('#asiento_movimiento_id').val(data_id);
        $('#partida_id').val(partida);
        $('#accion').val(data_registro);

        $('.tablageneral').toggle("slow");
        $('.editarcuentas').toggle("slow");


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
