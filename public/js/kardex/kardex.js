$(document).ready(function(){

    var carpeta = $("#carpeta").val();

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
