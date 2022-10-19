$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".compras").on('click','.generarasiento', function() {
        var fechaemision                =   $('#fechaemision').val();
        if(fechaemision ==''){ alerterrorajax("Seleccione una fecha de emision."); return false;}
        var asiento_id                  =   $('#asiento_id_configuracion').val();
        var idopcion                    =   $('#opcion_configuracion').val();
        var anio                        =   $('#anio_configuracion').val();
        var periodo_id                  =   $('#periodo_id_configuracion').val();
        var serie                       =   $('#serie_configuracion').val();
        var documento                   =   $('#documento_configuracion').val();
        var _token                      =   $('#token').val();
        $('#modal-detalle-asiento-confirmar').niftyModal('hide');

        data                            =   {
                                                _token                  : _token,
                                                asiento_id              : asiento_id,
                                                idopcion                : idopcion,
                                                fechaemision            : fechaemision,
                                                anio                    : anio,
                                                periodo_id              : periodo_id,
                                                serie                   : serie,
                                                documento               : documento,
                                            };

        ajax_modal(data,"/ajax-modal-cambiar-asiento-fechaemision",
                  "modal-detalle-asiento-confirmar","modal-detalle-asiento-confirmar-container");

    });



    $(".compras").on('click','.guardarcuentadetraccion', function() {

        var empresa_id              =   $('#empresa_select').val();
        var sw_acccion              =   $('#sw_acccion').val();
        if(sw_acccion =='1'){
            if(empresa_id ==''){ alerterrorajax("Seleccione una empresa."); return false;}
        }
        $('#DOCUMENTO').val(empresa_id);
        return true;

    });


    $(".compras").on('click','.generararchivo', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var data_archivo            =   $(this).attr('data_archivo');
        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        $('#data_archivo').val(data_archivo);

        $('#formdescargar').submit();

    });

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

    $(".compras").on('click','.buscardetracciones', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        data            =   {
                                _token                  : _token,
                                anio                    : anio,
                                periodo_id              : periodo_id,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-listado-deposito-masivo-detraccion");

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

    $(".compras").on('change','#anio_asiento', function() {

        event.preventDefault();
        var anio        =   $('#anio_asiento').val();
        var _token      =   $('#token').val();
        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un anio."); return false;}
        data            =   {
                                _token      : _token,
                                anio        : anio
                            };

        ajax_normal_combo(data,"/ajax-combo-periodo-xanio-xempresa","ajax_anio_asiento")                    

    });

    $(".compras").on('change','#tipo_descuento', function() {

        event.preventDefault();
        var tipo_descuento   =   $(this).val();
                    
        if(tipo_descuento==''){
            $('#porcentaje_detraccion').val("0.00");
            $('#total_detraccion').val("0.00");
        }else{
            $('#porcentaje_detraccion').val($('#porcentaje_detraccion').attr('data_valor'));
            $('#total_detraccion').val($('#total_detraccion').attr('data_valor'));
        }

    });


    $(".compras").on('keypress keyup keydown','#porcentaje_detraccion', function(e) {

        var total_documento             =   $('#total_documento').val();
        var total                       =    parseFloat(total_documento) * $(this).val() / 100;
        $('#total_detraccion').val(total);

    });

    $(".compras").on('click','.btn-modificar-asiento', function(e){

        var anio_asiento                =   $('#anio_asiento').val();
        var periodo_asiento_id          =   $('#periodo_asiento_id').val();

        var tipo_descuento              =   $('#tipo_descuento').val();
        var porcentaje_detraccion       =   $('#porcentaje_detraccion').val();
        var total_detraccion            =   $('#total_detraccion').val();


        if(anio_asiento ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_asiento_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        if(tipo_descuento =='DCT0000000000002'){ 
                if(parseFloat(porcentaje_detraccion) <= 0){ alerterrorajax("Porcentaje de la detraccion debe ser mayor a 0."); return false;}
                if(parseFloat(total_detraccion) <= 0){ alerterrorajax("Total de la detraccion debe ser mayor a 0."); return false;}
        }

        abrircargando();

        return true;

    });


    $(".compras").on('click','.btn-guardar-configuracion-reversion', function(e){

        var anio_asiento                =   $('#anio_asiento').val();
        var periodo_asiento_id          =   $('#periodo_asiento_id').val();

        if(anio_asiento ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_asiento_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}

        abrircargando();

        return true;

    });




    $(".compras").on('click','.clickasientodiariocompra', function(e) {

        var _token                  =   $('#token').val();
        var asiento_id              =   $(this).attr('data_asiento_id');
        var idopcion                =   $('#idopcion').val();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var serie                   =   $('#serie').val();
        var documento               =   $('#documento').val();


        data                        =   {
                                            _token                  : _token,
                                            asiento_id              : asiento_id,
                                            idopcion                : idopcion,
                                            anio                    : anio,
                                            periodo_id              : periodo_id,
                                            serie                   : serie,
                                            documento               : documento,
                                        };
        ajax_modal(data,"/ajax-modal-detalle-asiento-diario-compra",
                  "modal-detalle-asiento-confirmar","modal-detalle-asiento-confirmar-container");

    });


    $(".compras").on('click','.clickasientodiario', function(e) {

        var _token                  =   $('#token').val();
        var asiento_id              =   $(this).attr('data_asiento_id');
        var idopcion                =   $('#idopcion').val();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var serie                   =   $('#serie').val();
        var documento               =   $('#documento').val();

        data                        =   {
                                            _token                  : _token,
                                            asiento_id              : asiento_id,
                                            idopcion                : idopcion,
                                            anio                    : anio,
                                            periodo_id              : periodo_id,
                                            serie                   : serie,
                                            documento               : documento,
                                        };
        ajax_modal(data,"/ajax-modal-crear-detalle-asiento-diario",
                  "modal-detalle-asiento-confirmar","modal-detalle-asiento-confirmar-container");

    });



    $(".compras").on('click','.clickcrearasiento', function(e) {

        var _token                  =   $('#token').val();
        var asiento_id              =   $(this).attr('data_asiento_id');
        var idopcion                =   $('#idopcion').val();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var serie                   =   $('#serie').val();
        var documento               =   $('#documento').val();


        data                        =   {
                                            _token                  : _token,
                                            asiento_id              : asiento_id,
                                            idopcion                : idopcion,
                                            anio                    : anio,
                                            periodo_id              : periodo_id,
                                            serie                   : serie,
                                            documento               : documento,
                                        };
        ajax_modal(data,"/ajax-modal-detalle-asiento-diario-reversion",
                  "modal-detalle-asiento-confirmar","modal-detalle-asiento-confirmar-container");

    });


    $(".compras").on('click','.clickasientocompra', function(e) {

        var _token                  =   $('#token').val();
        var asiento_id              =   $(this).attr('data_asiento_id');
        var idopcion                =   $('#idopcion').val();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var serie                   =   $('#serie').val();
        var documento               =   $('#documento').val();

        data                        =   {
                                            _token                  : _token,
                                            asiento_id              : asiento_id,
                                            idopcion                : idopcion,
                                            anio                    : anio,
                                            periodo_id              : periodo_id,
                                            serie                   : serie,
                                            documento               : documento,
                                        };
        ajax_modal(data,"/ajax-modal-detalle-asiento-confirmar",
                  "modal-detalle-asiento-confirmar","modal-detalle-asiento-confirmar-container");

    });


    $(".compras").on('dblclick','.dobleclickpcreversion', function(e) {

        var _token                  =   $('#token').val();
        var asiento_id              =   $(this).attr('data_asiento_id');
        var idopcion                =   $('#idopcion').val();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var serie                   =   $('#serie').val();
        var documento               =   $('#documento').val();

        data                        =   {
                                            _token                  : _token,
                                            asiento_id              : asiento_id,
                                            idopcion                : idopcion,
                                            anio                    : anio,
                                            periodo_id              : periodo_id,
                                            serie                   : serie,
                                            documento               : documento,
                                        };
        ajax_modal(data,"/ajax-modal-detalle-asiento-transicion",
                  "modal-detalle-asiento-confirmar","modal-detalle-asiento-confirmar-container");

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
