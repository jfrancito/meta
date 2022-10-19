$(document).ready(function(){

    var carpeta = $("#carpeta").val();


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


    $(".asiento").on('click','.btn_guardar_asiento', function() {
        var data = [];
        var sw   = 0;
        var msj  = '';
        $(".table-detalle-asientos .fila_pedido").each(function(){
            sw=1;         
        });

        if(sw==0){alerterrorajax("No existe detalle de asientos");return false;}
        return true;

    });

});
