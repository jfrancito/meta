$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".tipocambio").on('click','.buscartipocambio', function() {

        event.preventDefault();
        var anio        =   $('#anio').val();
        var mes         =   $('#mes').val();

        var idopcion    =   $('#idopcion').val();
        var _token      =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(mes ==''){ alerterrorajax("Seleccione un mes."); return false;}
        data            =   {
                                _token      : _token,
                                anio        : anio,
                                mes        : mes,
                                idopcion    : idopcion,
                            };
        ajax_normal(data,"/ajax-tipo-cambio");

    });

    $(".tipocambio").on('keypress keyup keydown','.tipocambio_edit', function(e) {
        var cabecera_tabla_tr           =   $(this).parents('.fila_tipo_cambio');
        var nombre                      =   $(cabecera_tabla_tr).attr('data_edit_tipo_cambio','1');
    });


    $(".tipocambio").on('click','.guardarcambios', function() {

        event.preventDefault();
        var _token                  = $('#token').val();
        var anio                    = $('#anio').val();
        var mes                     = $('#mes').val();

        $('input[type=search]').val('').change();
        $("#nso").DataTable().search("").draw();
        var data_tipo_cambio           = datatipocambio_edit();
        if(data_tipo_cambio.length<=0){ alerterrorajax("Por lo menos edite un tipo de cambio."); return false;}

        data            =   {
                                _token                  : _token,
                                data_tipo_cambio        : data_tipo_cambio,
                                anio                    : anio,
                                mes                     : mes,
                            };

        ajax_normal(data,"/ajax-guardar-configuracion-tipo-cambio");

    });


    function datatipocambio_edit(){

        var data = [];

        $(".tablatipocambio tbody tr").each(function(){

                var data_fecha_tipo_cambio          = $(this).attr('data_fecha_tipo_cambio');
                var data_edit_tipo_cambio           = $(this).attr('data_edit_tipo_cambio');
                var can_compra_sbs                  = $(this).find('#CAN_COMPRA_SBS').val();                
                var can_venta_sbs                   = $(this).find('#CAN_VENTA_SBS').val();


                if(data_edit_tipo_cambio == '1'){
                    data.push({
                        fecha_tipo_cambio       : data_fecha_tipo_cambio,
                        can_compra_sbs          : can_compra_sbs,
                        can_venta_sbs           : can_venta_sbs,
                    });         
                }

        });
        return data;
    }


 

});
