$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".archivople").on('change','#anio', function() {

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


    $(".archivople").on('change','#tipo_asiento_id', function() {
        var tipo_asiento_id        =   $(this).val();
        if(tipo_asiento_id =='TAS0000000000003'){ 
            $(".cajadocuento").css("display", "block");
        }else{
            $(".cajadocuento").css("display", "none");
        }         
    });


    $(".archivople").on('click','.descargararchivo', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var tipo_asiento_id         =   $('#tipo_asiento_id').val();
        var periodo_id              =   $('#periodo_id').val();
        var documento               =   $('#documento').val();
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

    $(".archivople").on('click','.descargararchivoexcel', function() {

        var anio                    =   $('#anio').val();
        var tipo_asiento_id         =   $('#tipo_asiento_id').val();
        var periodo_id              =   $('#periodo_id').val();
        var documento               =   $('#documento').val();

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        if(tipo_asiento_id ==''){ alerterrorajax("Seleccione un tipo de asiento."); return false;}

        href = $(this).attr('data-href')+'/'+anio+'/'+tipo_asiento_id+'/'+periodo_id+'/'+documento;
        $(this).prop('href', href);
        return true;


    });


    $(".archivople").on('click','.buscarple', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var tipo_asiento_id         =   $('#tipo_asiento_id').val();
        var periodo_id              =   $('#periodo_id').val();
        var documento               =   $('#documento').val();

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
                                documento               : documento,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-buscar-lista-ple");

    });





});
