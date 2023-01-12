$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".situacionfinanciera").on('click','.descargararchivo', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_inicio_id       =   $('#periodo_inicio_id').val();
        var periodo_fin_id          =   $('#periodo_fin_id').val();
        var reporte                 =   $('#reporte').val();

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_inicio_id ==''){ alerterrorajax("Seleccione un periodo inicio."); return false;}
        if(periodo_fin_id ==''){ alerterrorajax("Seleccione un periodo fin."); return false;}


        $('#formdescargar').submit();

    });


    $(".situacionfinanciera").on('click','.buscarsf', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_inicio_id       =   $('#periodo_inicio_id').val();
        var periodo_fin_id          =   $('#periodo_fin_id').val();
        var reporte                 =   $('#reporte').val();


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
                                reporte                 : reporte,
                            };
        ajax_normal(data,"/ajax-buscar-situacion-financiera");

    });


    $(".situacionfinanciera").on('change','#anio', function() {

        event.preventDefault();
        var anio        =   $('#anio').val();
        var _token      =   $('#token').val();
        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un anio."); return false;}
        data            =   {
                                _token      : _token,
                                anio        : anio
                            };

        ajax_normal_combo(data,"/ajax-combo-periodo-xanio-titulo","ajax_anio")                    

    });


});
