$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".reporteregistroventa").on('click','.buscarrv', function() {

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
                                anio                    : anio,
                                tipo_asiento_id         : tipo_asiento_id,
                                periodo_id              : periodo_id,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-buscar-reporte-registro-venta");

    });




    $(".reporteregistroventa").on('click','.descargararchivoexcel', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var tipo_asiento_id         =   $('#tipo_asiento_id').val();
        var periodo_id              =   $('#periodo_id').val();

        var data_archivo            =   $(this).attr('data_archivo');

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        if(tipo_asiento_id ==''){ alerterrorajax("Seleccione un tipo de asiento."); return false;}
        $('#data_archivo').val(data_archivo);

        $('#formdescargar').submit();

    });



});
