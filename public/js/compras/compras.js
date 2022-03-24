$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".compras").on('click','.buscarcompras', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var serie                   =   $('#serie').val();
        var documento               =   $('#documento').val();

        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        data            =   {
                                _token                  : _token,
                                anio                    : anio,
                                periodo_id              : periodo_id,
                                serie                   : serie,
                                documento               : documento,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-listado-compras");

    });
    $(".compras").on('click','.clistacompras', function() {
        activaTab('listacompras');
    });




    $(".compras").on('change','#anio', function() {

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

    $(".compras").on('dblclick','.dobleclickac', function(e) {

        var _token                  =   $('#token').val();
        var documento_ctble_id      =   $(this).attr('data_documento_ctble_id');
        var idopcion                =   $('#idopcion').val();


        activaTab('asiento');
        data                        =   {
                                            _token                  : _token,
                                            documento_ctble_id      : documento_ctble_id,
                                            idopcion                : idopcion,
                                        };

        ajax_normal_seccion(data,"/ajax-buscar-compra-seleccionada","crearasientoajax");

    });


    function activaTab(tab){
        $('.nav-tabs a[href="#' + tab + '"]').tab('show');
    }


});
