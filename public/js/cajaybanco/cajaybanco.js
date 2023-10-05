$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".cajaybanco").on('click','.buscarmovimientofectivo', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var caja_id                 =   $('#caja_id').val();

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        if(caja_id ==''){ alerterrorajax("Seleccione una caja."); return false;}

        data            =   {
                                _token                  : _token,
                                anio                    : anio,
                                periodo_id              : periodo_id,
                                caja_id                 : caja_id,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-registro-movimiento-efectivo");

    });


    $(".cajaybanco").on('dblclick','.dobleclickmc', function(e) {

        var _token                  =   $('#token').val();
        var cod_operacion_caja      =   $(this).attr('data_cod_operacion_caja');
        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            cod_operacion_caja      : cod_operacion_caja,
                                            idopcion                : idopcion,
                                        };

        debugger;

        ajax_modal(data,"/ajax-modal-configuracion-movimiento-efectivo",
          "modal-lista-movimiento-caja-banco","modal-lista-movimiento-caja-banco-container");

    });



    $(".cajaybanco").on('click','.buscarmovimiento', function() {

        var _token                  =   $('#token').val();
        var cuenta_referencia       =   $('#cuenta_referencia').val();
        var nrooperacion            =   $('#nrooperacion').val();
        var idopcion                =   $('#idopcion').val();

        if(cuenta_referencia ==''){ alerterrorajax("Selecciona una cuenta de referencia."); return false;}
        if(nrooperacion ==''){ alerterrorajax("Ingrese numero de operción."); return false;}

        data                        =   {
                                            _token                  : _token,
                                            cuenta_referencia     	: cuenta_referencia,
                                            nrooperacion     		: nrooperacion,
                                            idopcion                : idopcion
                                        };

        ajax_modal(data,"/ajax-modal-lista-movimiento-caja-banco",
                  "modal-lista-movimiento-caja-banco","modal-lista-movimiento-caja-banco-container");

    });


    $(".cajaybanco").on('click','.btn-modificar-asiento', function() {

        abrircargando();
        return true;

    });









    $(".cajaybanco").on('dblclick','.dobleclickpc', function(e) {

        var _token                  =   $('#token').val();
		var idopcion                =   $('#idopcion').val();
        var nro_operacion           =   $(this).attr('data_nro_operacion');
        var cod_caja_banco          =   $(this).attr('data_cod_caja_banco');

        var nro_cuenta_bancaria     =   $(this).attr('data_nro_cuenta_bancaria');
        var txt_categoria_operacion_caja          =   $(this).attr('data_txt_categoria_operacion_caja');
        var txt_categoria_moneda    =   $(this).attr('data_txt_categoria_moneda');
        var can_tipo_cambio         =   $(this).attr('data_can_tipo_cambio');
        var fec_movimiento_caja     =   $(this).attr('data_fec_movimiento_caja');

        var nro_referencia          =   $(this).attr('data_nro_referencia');


        data                        =   {
                                            _token                  : _token,
                                            nro_operacion           : nro_operacion,
                                            cod_caja_banco          : cod_caja_banco,

                                            nro_cuenta_bancaria     : nro_cuenta_bancaria,
                                            txt_categoria_operacion_caja          : txt_categoria_operacion_caja,
                                            txt_categoria_moneda    : txt_categoria_moneda,
                                            can_tipo_cambio         : can_tipo_cambio,
                                            fec_movimiento_caja     : fec_movimiento_caja,

                                            nro_referencia          : nro_referencia,
                                            idopcion          		: idopcion,
                                        };
		ajax_normal(data,"/ajax-lista-movimiento-caja-banco");
		$("#modal-lista-movimiento-caja-banco").niftyModal('hide');


    });
    $(".cajaybanco").on('dblclick','.dobleclickcb', function(e) {

        var _token                  =   $('#token').val();
        var idopcion                =   $('#idopcion').val();
        var banco_caja_id           =   $(this).attr('data_banco_caja_id');

        data                        =   {
                                            _token                  : _token,
                                            banco_caja_id           : banco_caja_id,
                                            idopcion                : idopcion,
                                        };


        ajax_modal(data,"/ajax-modal-asociar-banco-caja",
          "modal-asociar-banco-caja","modal-asociar-banco-caja-container");

    });


});
