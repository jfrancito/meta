$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".balancecomprobacion").on('click','.descargararchivo', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_inicio_id       =   $('#periodo_inicio_id').val();
        var periodo_fin_id          =   $('#periodo_fin_id').val();

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_inicio_id ==''){ alerterrorajax("Seleccione un periodo inicio."); return false;}
        if(periodo_fin_id ==''){ alerterrorajax("Seleccione un periodo fin."); return false;}


        $('#formdescargar').submit();

    });


    $(".balancecomprobacion").on('click','.buscarbalancecomprobacion', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_inicio_id       =   $('#periodo_inicio_id').val();
        var periodo_fin_id          =   $('#periodo_fin_id').val();

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_inicio_id ==''){ alerterrorajax("Seleccione un periodo inicio."); return false;}
        if(periodo_fin_id ==''){ alerterrorajax("Seleccione un periodo fin."); return false;}

        data            =   {
                                _token                  : _token,
                                anio                    : anio,
                                periodo_inicio_id       : periodo_inicio_id,
                                periodo_fin_id          : periodo_fin_id,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-buscar-balance-comprobacion");

    });





});
