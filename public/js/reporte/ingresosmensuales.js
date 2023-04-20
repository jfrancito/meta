$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".ingresosmensuales").on('click','.descargararchivo', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_inicio_id       =   $('#periodo_inicio_id').val();
        var periodo_fin_id          =   $('#periodo_fin_id').val();
        var moneda_id               =   $('#moneda_id').val();
        var cuenta_inicio_id        =   $('#cuenta_inicio_id').val();
        var cuenta_fin_id           =   $('#cuenta_fin_id').val();

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_inicio_id ==''){ alerterrorajax("Seleccione un periodo inicio."); return false;}
        if(periodo_fin_id ==''){ alerterrorajax("Seleccione un periodo fin."); return false;}
        if(moneda_id ==''){ alerterrorajax("Seleccione una moneda."); return false;}
        if(cuenta_inicio_id ==''){ alerterrorajax("Seleccione una cuenta inicio."); return false;}
        if(cuenta_fin_id ==''){ alerterrorajax("Seleccione una cuenta fin."); return false;}


        $('#formdescargar').submit();

    });


    $(".ingresosmensuales").on('click','.buscarim', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_inicio_id       =   $('#periodo_inicio_id').val();
        var periodo_fin_id          =   $('#periodo_fin_id').val();
        var moneda_id               =   $('#moneda_id').val();
        var cuenta_inicio_id        =   $('#cuenta_inicio_id').val();
        var cuenta_fin_id           =   $('#cuenta_fin_id').val();


        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_inicio_id ==''){ alerterrorajax("Seleccione un periodo inicio."); return false;}
        if(periodo_fin_id ==''){ alerterrorajax("Seleccione un periodo fin."); return false;}
        if(moneda_id ==''){ alerterrorajax("Seleccione una moneda."); return false;}
        if(cuenta_inicio_id ==''){ alerterrorajax("Seleccione una cuenta inicio."); return false;}
        if(cuenta_fin_id ==''){ alerterrorajax("Seleccione una cuenta fin."); return false;}
        
        data            =   {
                                _token                  : _token,
                                anio                    : anio,
                                periodo_inicio_id       : periodo_inicio_id,
                                periodo_fin_id          : periodo_fin_id,
                                moneda_id               : moneda_id,
                                cuenta_inicio_id        : cuenta_inicio_id,
                                cuenta_fin_id           : cuenta_fin_id,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-buscar-ingresos-mensuales");

    });


    $(".ingresosmensuales").on('change','#anio', function() {

        event.preventDefault();
        var anio        =   $('#anio').val();
        var _token      =   $('#token').val();


        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un anio."); return false;}
        data            =   {
                                _token      : _token,
                                anio        : anio
                            };
        ajax_normal_combo(data,"/ajax-combo-periodo-xanio-titulo","ajax_anio");
        ajax_normal_combo(data,"/ajax-combo-cuentas-xanio-titulo","ajax_cuentas"); 

    });


});
