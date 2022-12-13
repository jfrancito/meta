$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".asiento").on('change','#tipo_documento', function() {

        event.preventDefault();
        var tipo_documento        =   $('#tipo_documento').val();
        var _token      =   $('#token').val();

        data            =   {
                                _token                : _token,
                                tipo_documento        : tipo_documento
                            };

        ajax_normal_combo(data,"/ajax-combo-tipo-documento-referencia-xtipodocumento","ajax_tiporeferencia")                    

    });


    $(".asiento").on('change','#anio', function() {

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
    
    $(".asiento").on('change','#tipo_asiento_id', function() {

        event.preventDefault();
        var tipo_asiento_id      =   $('#tipo_asiento_id').val();
        var anio                    =   $('#anio').val();
        var _token              =   $('#token').val();
        //validacioones
        if(tipo_asiento_id ==''){ alerterrorajax("Seleccione un tipo asiento."); return false;}
        if(anio ==''){ alerterrorajax("Seleccione un anio."); return false;}

        data            =   {
                                _token                  : _token,
                                tipo_asiento_id        : tipo_asiento_id,
                                anio                    : anio,
                            };

        ajax_normal_combo(data,"/ajax-combo-cuentapagocobro-xtipoasiento","ajax_cuentapagocuenta")                    

    });

    $(".asiento").on('click','.tabpaso1', function() {

        $(".btn_guardar_asiento_cobro_pago").addClass("ocultar");
        return true;
    });


    $(".asiento").on('click','.tabpaso2', function() {
        event.preventDefault();
        var sw   = 0;
        var fechadocumento      =   $('#fechadocumento').val();
        var moneda_id           =   $('#moneda_id').val();
        var tipocambio           =   $('#tipocambio').val();



        var cuenta_referencia   =   $('#cuenta_referencia').val();
        var tipo_documento      =   $('#tipo_documento').val();
        var serie               =   $('#serie').val();
        var nrocomprobante      =   $('#nrocomprobante').val();
        var glosa               =   '';


        $(".table-detalle-asientos-p-c .fila_asiento_p_c").each(function(){
            sw=1;         
            glosa = $(this).attr('data_txt_documento').trim() +' '+$(this).attr('data_moneda')+' '+$(this).attr('data_serie')+' '+$(this).attr('data_nro')+' // '+$(this).attr('data_cliente');

        });

        if(fechadocumento ==''){ alerterrorajax("Seleccione un fecha documento."); return false;}
        if(moneda_id ==''){ alerterrorajax("Seleccione una moneda."); return false;}
        if(tipocambio == ''){alerterrorajax("Ingrese un tipo cambio");return false;}
        if(cuenta_referencia ==''){ alerterrorajax("Seleccione un cuenta referencia."); return false;}
        if(tipo_documento ==''){ alerterrorajax("Seleccione un tipo documento."); return false;}
        if(serie == ''){alerterrorajax("Ingrese una serie");return false;}
        if(nrocomprobante == ''){alerterrorajax("Ingrese una nro comprobante");return false;}


        if(sw==0){alerterrorajax("No existe detalle de asientos");return false;}

        $("#glosa").val(glosa);
        $(".btn_guardar_asiento_cobro_pago").removeClass("ocultar");

        return true;



    });


    $(".asiento").on('click','.btn_guardar_asiento_cobro_pago', function() {

        var sw   = 0;


        var anio                 =   $('#anio').val();
        var tipo_asiento_id      =   $('#tipo_asiento_id').val();
        var cuenta_id            =   $('#cuenta_id').val();
        var documento            =   $('#documento').val();
        var glosa                =   $('#glosa').val();

        if(anio ==''){ alerterrorajax("Seleccione un anio."); return false;}
        if(tipo_asiento_id ==''){ alerterrorajax("Seleccione una cancelar documento."); return false;}
        if(cuenta_id == ''){alerterrorajax("Ingrese un cuenta");return false;}

        if(documento == ''){alerterrorajax("Ingrese una documento");return false;}
        if(glosa == ''){alerterrorajax("Ingrese una glosa");return false;}

        return true;



    });



    $(".asiento").on('click','.agregardetalleasiento', function() {
        event.preventDefault();
        var _token                  =   $('#token').val();

        data                        =   {
                                            _token                  : _token,
                                        };
                              
        ajax_modal(data,"/ajax-modal-detalle-asiento-configuracion",
                  "modal-asiento","modal-asiento-container");

    });



    $(".asiento").on('change','#nivel', function() {

        event.preventDefault();
        var nivel       =   $('#nivel').val();
        var _token      =   $('#token').val();
        //validacioones
        if(nivel ==''){ alerterrorajax("Seleccione un nivel."); return false;}
        data            =   {
                                _token      : _token,
                                nivel       : nivel
                            };

        ajax_normal_combo(data,"/ajax-combo-cuentacontable-xnivel","ajax_nivel");                   

    });


    $(".asiento").on('click','.btn-guardar-detalle-asiento', function() {
        event.preventDefault();
        var _token                  =   $('#token').val();

        var nivel                   = $('#nivel').val();
        var partida_id              = $('#partida_id').val();
        var monto                   = $('#monto').val();
        monto                       = monto.replace(",", "");
        var cuenta_contable_id      = $('#cuenta_contable_id').val();
        var array_detalle_asiento   = $('#array_detalle_asiento').val();
        var ultimalinea             = parseInt($('#ultimalinea').val()) + 1;


        if(nivel == ''){alerterrorajax("Seleccione un nivel");return false;}
        if(partida_id == ''){alerterrorajax("Seleccione una partida");return false;}
        if(cuenta_contable_id == ''){alerterrorajax("Seleccione una cuenta contable");return false;}
        if(monto == ''){alerterrorajax("Ingrese un monto");return false;}
        $('#ultimalinea').val(ultimalinea);
        data            =   {
                                _token                  : _token,
                                nivel                   : nivel,
                                partida_id              : partida_id,
                                cuenta_contable_id      : cuenta_contable_id,
                                monto                   : monto,
                                array_detalle_asiento   : array_detalle_asiento,
                                ultimalinea             : ultimalinea,
                            };
        ajax_normal(data,"/ajax-agregar-detalle-asiento");

        $('#modal-asiento').niftyModal('hide');

    });



    $(".asiento").on('click','.eliminar-detalle-asiento', function() {

        event.preventDefault();
        var _token                  = $('#token').val();
        var fila                    = $(this).parents('.fila_pedido').attr('data_linea');
        var array_detalle_asiento   = $('#array_detalle_asiento').val();

        data            =   {
                                _token                  : _token,
                                fila                    : fila,
                                array_detalle_asiento   : array_detalle_asiento,
                            };

        ajax_normal(data,"/ajax-eliminar-detalle-asiento");


    });



    $(".asiento").on('click','.btn-confirmar-guardar-asiento', function() {
        var input        =   $('#cobranza');
        if($(input).is(':checked')){
            $('#pagocobro').val("1");
        }else{
            $('#pagocobro').val("0");
        }
        $( "#formasiento" ).submit();
    });

    $(".asiento").on('click','.btn_guardar_asiento', function() {
        var data = [];
        var sw   = 0;
        var msj  = '';

        var _token            =   $('#token').val();

        var anio              =   $('#anio').val();
        var periodo_id        =   $('#periodo_id').val();
        var tipo_asiento_id   =   $('#tipo_asiento_id').val();
        var moneda_id         =   $('#moneda_id').val(); 
        var tipocambio        =   $('#tipocambio').val();
        var glosa             =   $('#glosa').val();
        var fechadocumento    =   $('#fechadocumento').val();


        $(".table-detalle-asientos .fila_pedido").each(function(){
            sw=1;         
        });

        if(anio ==''){ alerterrorajax("Seleccione un anio."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        if(tipo_asiento_id ==''){ alerterrorajax("Seleccione un tipo asiento."); return false;}
        if(moneda_id ==''){ alerterrorajax("Seleccione una moneda."); return false;}
        if(tipocambio == ''){alerterrorajax("Ingrese un tipo cambio");return false;}
        if(glosa == ''){alerterrorajax("Ingrese una glosa");return false;}
        if(fechadocumento == ''){alerterrorajax("Ingrese una fecha documento");return false;}

        if(sw==0){alerterrorajax("No existe detalle de asientos");return false;}

        data                        =   {
                                            _token                  : _token,
                                        };
                              
        ajax_modal(data,"/ajax-modal-confirmacion-guardar",
                  "modal-asiento","modal-asiento-container");


        return true;

    });


    $(".asiento").on('click','.buscarasiento', function() {

        var data = [];
        var sw   = 0;
        var msj  = '';

        var _token              =   $('#token').val();
        var tipo_documento      =   $('#tipo_documento').val();
        var serie               =   $('#serie').val();
        var nrocomprobante      =   $('#nrocomprobante').val();
        var asiento_id          =   $('#cod_asiento').val();        


        if(tipo_documento ==''){ alerterrorajax("Seleccione un tipo documento."); return false;}
        if(serie == ''){alerterrorajax("Ingrese un serie");return false;}
        if(nrocomprobante == ''){alerterrorajax("Ingrese una nrocomprobante");return false;}


        data            =   {
                                _token                  : _token,
                                tipo_documento          : tipo_documento,
                                serie                   : serie,
                                asiento_id              : asiento_id,
                                nrocomprobante          : nrocomprobante
                            };
        ajax_normal(data,"/ajax-buscar-asiento-pago-cobro");


    });


});

