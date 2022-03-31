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


    $(".compras").on('dblclick','.dobleclickpc', function(e) {

        var _token                  =   $('#token').val();
        var asiento_id              =   $(this).attr('data_asiento_id');
        var idopcion                =   $('#idopcion').val();

        data                        =   {
                                            _token                  : _token,
                                            asiento_id              : asiento_id,
                                            idopcion                : idopcion,
                                        };
        ajax_modal(data,"/ajax-modal-detalle-asiento",
                  "modal-detalle-asiento","modal-detalle-asiento-container");

    });


    $(".compras").on('click','#confirmarasientos', function(e) {
        e.preventDefault();
        data = dataenviar();
        if(data.length<=0){alerterrorajax('Seleccione por lo menos un asiento'); return false;}
        var datastring = JSON.stringify(data);
        $('#documentos').val(datastring);

        $('#anio_confirmar').val($('#anio').val());
        $('#periodo_id_confirmar').val($('#periodo_id').val());
        $('#nro_serie_confirmar').val($('#serie').val());
        $('#nro_doc_confirmar').val($('#documento').val());


        abrircargando();
        $( "#formgenerarasiento" ).submit();
    });

    /*
    function activaTab(tab){
        $('.nav-tabs a[href="#' + tab + '"]').tab('show');
    }
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
    });*/

});

function dataenviar(){
    var data = [];
    $(".listatabla tr").each(function(){
        nombre          = $(this).find('.input_asignar').attr('id');
        if(nombre != 'todo_asignar'){

            check           = $(this).find('.input_asignar');
            asiento_id     = $(this).attr('data_asiento_id');

            if($(check).is(':checked')){
                data.push({
                    asiento_id        : asiento_id
                });
            }               
        }
    });
    return data;
}
