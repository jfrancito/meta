$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".kardex").on('click','.descargararchivo', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var documento               =   $('#documento').val();
        
        var data_archivo            =   $(this).attr('data_archivo');
        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();
        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        $('#data_archivo').val(data_archivo);

        $('#formdescargar').submit();

    });


    $(".kardex").on('click','.buscarple', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}

        data            =   {
                                _token                  : _token,
                                anio                    : anio,
                                periodo_id              : periodo_id,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-buscar-lista-ple-kardex");

    });

    $(".kardex").on('change','#anio', function() {

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

    $(".kardex").on('click','.agregartranferenciaproducto', function() {

        var _token                  =   $('#token').val();
        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            idopcion                : idopcion
                                        };

        ajax_modal(data,"/ajax-modal-configuracion-transferencia-producto",
                  "modal-detalle-producto-kardex-configuracion","modal-detalle-producto-kardex-configuracion-container");

    });


    $(".kardex").on('click','.btn_calcular_cu', function() {

        var _token                  =   $('#token').val();
        var idopcion                =   $('#idopcion').val();
        var producto_salida_id      =   $('#producto_salida_id').val();
        var fecha                   =   $('#fecha').val();

        if(producto_salida_id ==''){ alerterrorajax("Seleccione producto de salida."); return false;}
        if(fecha ==''){ alerterrorajax("Seleccione una fecha."); return false;}

        data                        =   {
                                            _token                  : _token,
                                            idopcion                : idopcion,
                                            producto_salida_id      : producto_salida_id,
                                            fecha                   : fecha,
                                        };
        abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+'/ajax-calcular-ultimo-cu',
            data    :   data,
            success: function (data) {
                cerrarcargando();

                $('#cu').val(data);      
                var cantidad                =   $('#cantidad').val();
                var cantidad                =   cantidad.replace(",", "");
                var cu                      =   $('#cu').val();
                var cu                      =   cu.replace(",", "");
                $('#importe').val(cantidad*cu);


                //$('.cu').html(data);

            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });

    });

    $(".kardex").on('click','.btn-guardar-configuracion', function() {

        var producto_salida_id      =   $('#producto_salida_id').val();
        var fecha                   =   $('#fecha').val();
        var producto_ingreso_id     =   $('#producto_ingreso_id').val();
        var cantidad                =   $('#cantidad').val();
        var cu                      =   $('#cu').val();
        var importe                 =   $('#importe').val();

        if(producto_salida_id ==''){ alerterrorajax("Seleccione producto de salida."); return false;}
        if(fecha ==''){ alerterrorajax("Seleccione una fecha."); return false;}
        if(producto_salida_id ==''){ alerterrorajax("Seleccione producto de ingreso."); return false;}
        if(cantidad  <0){ alerterrorajax("Ingrese cantidad."); return false;}
        if(cu < 0){ alerterrorajax("Ingrese CU."); return false;}
        if(importe < 0){ alerterrorajax("Ingrese importe."); return false;}
        return true;

    });




    $(".kardex").on('click','.buscarsaldoinicial', function() {

        event.preventDefault();
        var tipo_producto_id         =   $('#tipo_producto_id').val();
        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(tipo_producto_id ==''){ alerterrorajax("Seleccione un tipo de producto."); return false;}


        data            =   {
                                _token                  : _token,
                                tipo_producto_id        : tipo_producto_id,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-saldo-inicial");

    });

    $(".kardex").on('click','.buscarmovimientokardex', function() {

        event.preventDefault();
        var anio                     =   $('#anio').val();
        //var tipo_movimiento_id       =   $('#tipo_movimiento_id').val();
        var tipo_movimiento_id       =   '';        
        var tipo_producto_id         =   $('#tipo_producto_id').val();
        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        //if(tipo_movimiento_id ==''){ alerterrorajax("Seleccione un tipo de movimiento."); return false;}
        if(tipo_producto_id ==''){ alerterrorajax("Seleccione un tipo de producto."); return false;}

        data            =   {
                                _token                  : _token,
                                tipo_movimiento_id      : tipo_movimiento_id,
                                tipo_producto_id        : tipo_producto_id,
                                anio                    : anio,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-movimiento-kardex");

    });


    $(".kardex").on('click','.descargarexcel', function() {

        event.preventDefault();
        var anio                     =   $('#anio').val();
        //var tipo_movimiento_id       =   $('#tipo_movimiento_id').val();
        var tipo_movimiento_id       =   '';        
        var tipo_producto_id         =   $('#tipo_producto_id').val();
        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        //if(tipo_movimiento_id ==''){ alerterrorajax("Seleccione un tipo de movimiento."); return false;}
        if(tipo_producto_id ==''){ alerterrorajax("Seleccione un tipo de producto."); return false;}

        $('#formdescargar').submit();

    });


    $(".kardex").on('dblclick','.dobleclickac', function(e) {

        var _token                  =   $('#token').val();
        var data_tipo_producto_id   =   $(this).attr('data_tipo_producto_id');
        var monto_total             =   $(this).attr('monto_total');
        var periodo                 =   $(this).attr('periodo');
        var data_anio               =   $(this).attr('data_anio');
        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            data_tipo_producto_id   : data_tipo_producto_id,
                                            monto_total             : monto_total,
                                            periodo                 : periodo,
                                            data_anio               : data_anio,
                                            idopcion                : idopcion
                                        };

        ajax_modal(data,"/ajax-modal-asiento-contable-kardex",
                  "modal-detalle-producto-kardex","modal-detalle-producto-kardex-container");

    });


    $(".kardex").on('dblclick','.dobleclickpc', function(e) {

        var _token                  =   $('#token').val();
        var data_producto_id        =   $(this).attr('data_producto_id');
        var data_periodo_id         =   $(this).attr('data_periodo_id');
        var data_anio               =   $(this).attr('data_anio');
        var data_tipo_asiento_id    =   $(this).attr('data_tipo_asiento_id');
        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            data_producto_id        : data_producto_id,
                                            data_periodo_id         : data_periodo_id,
                                            data_anio               : data_anio,
                                            data_tipo_asiento_id    : data_tipo_asiento_id,
                                            idopcion                : idopcion
                                        };

        ajax_modal(data,"/ajax-modal-detalle-producto-kardex",
                  "modal-detalle-producto-kardex","modal-detalle-producto-kardex-container");

    });


    $(".kardex").on('dblclick','.dobleclickto', function(e) {

        var _token                  =   $('#token').val();
        var data_producto_id        =   $(this).attr('data_producto_id');
        var data_periodo_id         =   $(this).attr('data_periodo_id');
        var data_anio               =   $(this).attr('data_anio');
        var data_tipo_asiento_id    =   '';
        var idopcion                =   $('#idopcion').val();
        var tipo_producto_id        =   $(this).attr('data_tipo_producto_id');


        data                        =   {
                                            _token                  : _token,
                                            data_producto_id        : data_producto_id,
                                            data_periodo_id         : data_periodo_id,
                                            data_tipo_asiento_id    : data_tipo_asiento_id,
                                            data_anio               : data_anio,
                                            tipo_producto_id        : tipo_producto_id,
                                            idopcion                : idopcion
                                        };

        ajax_modal(data,"/ajax-modal-detalle-producto-total-kardex",
                  "modal-detalle-producto-kardex","modal-detalle-producto-kardex-container");

    });


});
