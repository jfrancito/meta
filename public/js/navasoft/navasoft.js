$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".navasoft").on('click','.buscardet', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var tipo_asiento_id         =   $('#tipo_asiento_id').val();
        var periodo_id              =   $('#periodo_id').val();
        var estado_migracion_id     =   $('#estado_migracion_id').val();

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();
        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        if(tipo_asiento_id ==''){ alerterrorajax("Seleccione un tipo de asiento."); return false;}
        if(estado_migracion_id ==''){ alerterrorajax("Seleccione un estado migracion."); return false;}


        data            =   {
                                _token                  : _token,
                                anio                    : anio,
                                tipo_asiento_id         : tipo_asiento_id,
                                periodo_id              : periodo_id,
                                estado_migracion_id     : estado_migracion_id,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-buscar-lista-navasotf");

    });


    $(".navasoft").on('click','.descargararchivo', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var tipo_asiento_id         =   $('#tipo_asiento_id').val();
        var periodo_id              =   $('#periodo_id').val();
        var estado_migracion_id     =   $('#estado_migracion_id').val();


        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        if(tipo_asiento_id ==''){ alerterrorajax("Seleccione un tipo de asiento."); return false;}
        if(estado_migracion_id ==''){ alerterrorajax("Seleccione un estado migracion."); return false;}


        $('#formdescargar').submit();

    });

    $(".navasoft").on('click','.descargararchivomigrado', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var tipo_asiento_id         =   $('#tipo_asiento_id').val();
        var periodo_id              =   $('#periodo_id').val();
        var estado_migracion_id     =   $('#estado_migracion_id').val();

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        $('#migrado').val('1');

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        if(tipo_asiento_id ==''){ alerterrorajax("Seleccione un tipo de asiento."); return false;}
        if(estado_migracion_id ==''){ alerterrorajax("Seleccione un estado migracion."); return false;}


        $('#formdescargar').submit();

    });





});
