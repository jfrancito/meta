$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".diariomayor").on('click','.descargararchivo', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var libro_id                =   $('#libro_id').val();
        var periodo_id              =   $('#periodo_id').val();
        var data_archivo            =   $(this).attr('data_archivo');

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        if(libro_id ==''){ alerterrorajax("Seleccione un libro."); return false;}
        $('#data_archivo').val(data_archivo);

        $('#formdescargar').submit();

    });


    $(".diariomayor").on('click','.buscarple', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var libro_id                =   $('#libro_id').val();
        var periodo_id              =   $('#periodo_id').val();

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        if(libro_id ==''){ alerterrorajax("Seleccione un libro."); return false;}

        data            =   {
                                _token                  : _token,
                                anio                    : anio,
                                libro_id                : libro_id,
                                periodo_id              : periodo_id,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-buscar-lista-ple-diario");

    });





});
