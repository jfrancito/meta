$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".asientomodelo").on('click','.buscarasientomodelo', function() {

        event.preventDefault();
        var tipo_asiento_id         =   $('#tipo_asiento_id').val();
        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();
        var anio                    =   $('#anio').val();

        //validacioones
        if(tipo_asiento_id ==''){ alerterrorajax("Seleccione un tipo de asiento."); return false;}
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}

        data            =   {
                                _token                  : _token,
                                tipo_asiento_id         : tipo_asiento_id,
                                anio                    : anio,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-asiento-modelo");

    });



    $(".asientomodelo").on('click','.agregacuentacontable', function() {

        var _token                  =   $('#token').val();
        var asiento_contable_id     =   $(this).attr('data_asiento_contable');
        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            asiento_contable_id     : asiento_contable_id,
                                            idopcion                : idopcion
                                        };

        ajax_modal(data,"/ajax-modal-configuracion-asiento-modelo-detalle",
                  "modal-configuracion-asiento-modelo-detalle","modal-configuracion-asiento-modelo-detalle-container");

    });



    $(".asientomodelo").on('change','#nivel', function() {

        event.preventDefault();
        var nivel       =   $('#nivel').val();
        var _token      =   $('#token').val();
        //validacioones
        if(nivel ==''){ alerterrorajax("Seleccione un nivel."); return false;}
        data            =   {
                                _token      : _token,
                                nivel       : nivel
                            };

        ajax_normal_combo(data,"/ajax-combo-cuentacontable-xnivel","ajax_nivel")                    

    });

    $(".asientomodelo").on('click','.btn-guardar-configuracion', function() {

        var partida_id                =   $('#partida_id').val();
        var orden                     =   $('#orden').val();
        var cuenta_contable_id        =   $('#cuenta_contable_id').val();
        //validacioones
        if(cuenta_contable_id ==''){ alerterrorajax("Seleccione una cuenta contable."); return false;}
        if(partida_id ==''){ alerterrorajax("Seleccione una partida."); return false;}
        if(orden ==''){ alerterrorajax("Ingrese un orden."); return false;}
        return true;

    });



    $(".asientomodelo").on('click','.modificarcuentacontable', function() {

        var _token                      =   $('#token').val();
        var asiento_modelo_id           =   $(this).attr('data_asiento_modelo_id_id');
        var asiento_modelo_detalle_id   =   $(this).attr('data_detalle_asiento_modelo_id');
        var idopcion                    =   $('#idopcion').val();

        data                            =   {
                                                _token                      : _token,
                                                asiento_modelo_id           : asiento_modelo_id,
                                                asiento_modelo_detalle_id   : asiento_modelo_detalle_id,
                                                idopcion                    : idopcion
                                            };

        ajax_modal(data,"/ajax-modal-modificar-configuracion-asiento-modelo-detalle",
                  "modal-configuracion-asiento-modelo-detalle","modal-configuracion-asiento-modelo-detalle-container");

    });





 

});
