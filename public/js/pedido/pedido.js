$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".listapedidoosiris").on('click','#rechazarpedidosmeses', function() {

        var _token   = $('#token').val();

        $('#motivo_id_n').val($('#motivo_id').val());
        $('#observacion_n').val($('#observacion').val());
        data = dataenviar_anulacion();
        var data_string         = JSON.stringify(data);
        $('#pedido').val(data_string);
        abrircargando();
        return true;

    });


    $(".listapedidoosiris").on('click','.btn-detalle-pedido-rechazar', function() {


        var _token                      = $('#token').val();
        var detalle_pedido_id           = $(this).attr('data-id');
        var puntero                     = $(this);
        var data_ind_producto_obsequio  = $(this).attr('data_ind_producto_obsequio');
        var data_ind_obsequio           = $(this).attr('data_ind_obsequio');



        if(data_ind_obsequio == '0'){
            sw=0;
            $(".lista_detalle_pedido .fila_producto").each(function(){
                var ind_producto_obsequio = $(this).attr('data_ind_producto_obsequio');
                var ind_obsequio = $(this).attr('data_ind_obsequio');

                var estado_detalle_pedido       = $(this).find('.estado_detalle_pedido').find('.badge').html().trim();

                if(ind_obsequio == "1" && estado_detalle_pedido!='RECHAZADO'){
                    if(data_ind_producto_obsequio == ind_producto_obsequio){
                        sw       = 1;
                    }
                }
            });
            if(sw ==1){ alerterrorajax("Tiene un obsequio asociado."); return false;}

        }

        abrircargando();

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-pedido-rechazar",
            data    :   {
                            _token                          : _token,
                            detalle_pedido_id               : detalle_pedido_id
                        },    
            success: function (data) {

                JSONdata     = JSON.parse(data);
                error        = JSONdata[0].error;
                mensaje      = JSONdata[0].mensaje;
                if(error==false){
                    cambiar_estado_rechazado(puntero);
                    $(puntero).parents('.fila_producto').find('#cantidad').prop( "disabled", true );
                    $(puntero).parents('.fila_producto').find('#precio').prop( "disabled", true );
                    alertajax(mensaje);
                }else{
                    alerterrorajax(mensaje); 
                }
                cerrarcargando();

            },
            error: function (data) {
                error500(data);
            }
        });

        
    });



    $(".listapedidoosiris").on('keypress','.updateprice', function(e) {


        var _token                  =   $('#token').val();
        var detallepedido_id        =   $(this).parents('.fila_producto').attr('data_detallepedido');
        var pedido_id               =   $(this).parents('.fila_producto').attr('data_pedido');
        var ind_obsequio            =   $(this).parents('.fila_producto').attr('data_ind_obsequio');        var ind_obsequio            =   $(this).parents('.fila_producto').attr('data_ind_obsequio');


        var cantidad                =   $(this).parents('.fila_producto').find('.columna-cantidad').find('#cantidad').val();
        var precio                  =   $(this).val();
        precio                      =   precio.replace(",", "");
        cantidad                    =   cantidad.replace(",", "");
        
        var puntero                 =   $(this).parents('.fila_producto');
        var importe                 =   parseFloat(precio) * parseFloat(cantidad);

        if(ind_obsequio == '1'){
            importe = 0;
        }


        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){

            abrircargando();
            $.ajax({
                
                type    :   "POST",
                async   :   false,
                url     :   carpeta+"/ajax-guardar-precio-producto-pedido",
                data    :   {
                                _token                  : _token,
                                precio                  : precio,
                                detallepedido_id        : detallepedido_id,
                                pedido_id               : pedido_id
                            },
                success: function (data) {

                    JSONdata     = JSON.parse(data);
                    error        = JSONdata[0].error;
                    mensaje      = JSONdata[0].mensaje;
                    if(error==false){
                        puntero.val("");
                        $(puntero).find('.columna-importe').html(importe.toFixed(4));     
                        actualizartotalesproducto(pedido_id);
                        alertajax(mensaje);
                    }else{
                        alerterrorajax(mensaje); 
                    }
                    cerrarcargando();
                },
                error: function (data) {

                    if(data.status = 500){
                        var contenido = $(data.responseText);
                        alerterror505ajax($(contenido).find('.trace-message').html()); 
                        console.log($(contenido).find('.trace-message').html());     
                    }

                }
            });



        }
    });



    $(".listapedidoosiris").on('keypress','.updatecantidad', function(e) {


        var _token                  =   $('#token').val();
        var detallepedido_id        =   $(this).parents('.fila_producto').attr('data_detallepedido');
        var pedido_id               =   $(this).parents('.fila_producto').attr('data_pedido');
        var ind_obsequio            =   $(this).parents('.fila_producto').attr('data_ind_obsequio');

        var precio                  =   $(this).parents('.fila_producto').find('.columna-precio').find('#precio').val();
        var cantidad                =   $(this).val();
        precio                      =   precio.replace(",", "");
        cantidad                    =   cantidad.replace(",", "");
        var puntero                 =   $(this).parents('.fila_producto');
        var importe                 =   parseFloat(precio) * parseFloat(cantidad);

        if(ind_obsequio == '1'){
            importe = 0;
        }

        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){

            abrircargando();
            $.ajax({
                
                type    :   "POST",
                async   :   false,
                url     :   carpeta+"/ajax-guardar-cantidad-producto-pedido",
                data    :   {
                                _token                  : _token,
                                cantidad                : cantidad,
                                detallepedido_id        : detallepedido_id,
                                pedido_id               : pedido_id
                            },
                success: function (data) {

                    JSONdata     = JSON.parse(data);
                    error        = JSONdata[0].error;
                    mensaje      = JSONdata[0].mensaje;
                    if(error==false){
                        puntero.val("");
                        $(puntero).find('.columna-importe').html(importe.toFixed(4));     
                        actualizartotalesproducto(pedido_id);
                        alertajax(mensaje);
                    }else{
                        alerterrorajax(mensaje); 
                    }
                    cerrarcargando();
                },
                error: function (data) {

                    if(data.status = 500){
                        var contenido = $(data.responseText);
                        alerterror505ajax($(contenido).find('.trace-message').html()); 
                        console.log($(contenido).find('.trace-message').html());     
                    }

                }
            });

        }
    });

    

    $('#buscarpedidovendedor').on('click', function(event){

        event.preventDefault();
        var finicio     = $('#finicio').val();
        var ffin        = $('#ffin').val();
        var idopcion    = $('#idopcion').val();


        var _token      = $('#token').val();
        $(".listatablapedido").html("");
        abrircargando();

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-listado-de-toma-pedidos-vendedor",
            data    :   {
                            _token  : _token,
                            finicio : finicio,
                            ffin    : ffin,
                            idopcion    : idopcion,
                        },
            success: function (data) {
                cerrarcargando();
                $(".listatablapedido").html(data);

            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });

    });

    $(".listapedidoosiris").on('click','.btn-detalle-pedido-mobil', function() {


        var _token              = $('#token').val();
        var pedido_id           = $(this).attr('data-id');


        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-pedido-mobil",
            data    :   {
                            _token                  : _token,
                            pedido_id               : pedido_id
                        },    
            success: function (data) {
                $('.modal-detalle-pedido').html(data);
                $('#detalle-producto').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });

        
    });


    /*************** DETALLE DEL PEDIDO MODAL**************/


    $(".listapedidoosiris").on('click','.btn_guardar_detalle', function() {

        var mensaje                     = validardatadetallepedido();
        if(mensaje != ''){
            alerterrorajax(mensaje); 
            return false;
        }else{

            var data_detalle                = datadetallepedido();
            var pedido_id                   = $("#id_pedido_modal").val();
            var mensaje                     = "Datos guardados correctamente";
            var data_detalle_string         = JSON.stringify(data_detalle);

            $('.check'+pedido_id).prop("checked", true);
            console.log(data_detalle_string);
            console.log(pedido_id);

            //agregar el json
            $("#"+pedido_id+"pedido").attr('data-json-detalle',data_detalle_string);
            alertajax(mensaje);
            $("#detalle-producto .close").click();
            $('#enviarpedido').click();
        }



    });



    /*************** DETALLE DEL PEDIDO **************/


    $(".listapedidoosiris").on('click','.btn-detalle-pedido', function() {


        var _token              = $('#token').val();
        var pedido_id           = $(this).attr('data-id');
        var data_json_detalle   = $(this).attr('data-json-detalle');

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-pedido",
            data    :   {
                            _token                  : _token,
                            pedido_id               : pedido_id,
                            data_json_detalle       : data_json_detalle
                        },    
            success: function (data) {
                $('.modal-detalle-pedido').html(data);
                $('#detalle-producto').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });        
    });


    $(".listapedidoosiris").on('click','.btn-detalle-pedido-anulacion', function() {


        var _token              = $('#token').val();
        var pedido_id           = $(this).attr('data-id');
        var data_json_detalle   = $(this).attr('data-json-detalle');

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-pedido-anulacion",
            data    :   {
                            _token                  : _token,
                            pedido_id               : pedido_id,
                            data_json_detalle       : data_json_detalle
                        },    
            success: function (data) {
                $('.modal-detalle-pedido').html(data);
                $('#detalle-producto').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });        
    });

    $(".listapedidoosiris").on('click','.btn-detalle-pedido-transportista', function() {

        var _token              = $('#token').val();
        var pedido_id           = $(this).attr('data-id');

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-pedido-transportista",
            data    :   {
                            _token                  : _token,
                            pedido_id               : pedido_id
                        },    
            success: function (data) {
                $('.modal-detalle-pedido-transportista').html(data);
                $('#detalle-producto-transportista').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });        
    });



    $(".listapedidoosiris").on('click','.transportitas_reporte', function() {

        var data_producto           = datatransportista();

        //console.log(data_producto);
        data_producto = JSON.stringify(data_producto);
        data_producto = btoa(data_producto);
        //console.log(data_producto);

        $('#array_detalle_producto_transportista').val(data_producto)
        data_href   = $(this).attr('data_href');
        $(this).attr('href',data_href+data_producto);
        //console.log(data_href+data_producto);
        return true;
       
    });


    function datatransportista(){
        var data = [];
        $(".listatabledetalletransportista tbody tr").each(function(){

            check                   = $(this).find('.input_check');
            data_cantidad           = $(this).find('#cantidad').val();
            data_precio             = $(this).attr('data_precio');
            data_nombre_producto    = $(this).attr('data_nombre_producto');
            data_obsequio           = $(this).attr('data_obsequio');

            if($(check).is(':checked')){
                data.push({
                    data_cantidad     : data_cantidad,
                    data_precio     : data_precio,
                    data_nombre_producto     : data_nombre_producto,
                    data_obsequio     : data_obsequio
                });
            }               

        });
        return data;
    }



    $(".listapedidoosiris").on('click','.btn-detalle-pedido-autorizacion', function() {


        var _token              = $('#token').val();
        var pedido_id           = $(this).attr('data-id');

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-pedido-autorizacion",
            data    :   {
                            _token                  : _token,
                            pedido_id               : pedido_id
                        },    
            success: function (data) {
                $('.modal-detalle-pedido').html(data);
                $('#detalle-producto').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });

        
    });


    $(".listapedidoosiris").on('click','.btn-deuda-cliente', function() {


        var _token              = $('#token').val();
        var pedido_id           = $(this).attr('data-id');

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-deuda-cliente",
            data    :   {
                            _token                  : _token,
                            pedido_id               : pedido_id
                        },    
            success: function (data) {
                $('.modal-detalle-deuda-pedido').html(data);
                $('#detalle-deuda-pedido').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });

        
    });


    $(".listapedidoosiris").on('click','.btn-limite-credito', function() {


        var _token              = $('#token').val();
        var pedido_id           = $(this).attr('data-id');

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-limite-credito",
            data    :   {
                            _token                  : _token,
                            pedido_id               : pedido_id
                        },    
            success: function (data) {
                $('.modal-detalle-deuda-pedido').html(data);
                $('#detalle-deuda-pedido').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });

        
    });


    /*************** TOMA PEDIDO **************/

    $(".listatablapedido").on('click','label', function() {

        var input   = $(this).siblings('input');
        var accion  = $(this).attr('data-atr');
        var name    = $(this).attr('name');
        var check   = -1;
        var estado  = -1;
        
        if($(input).is(':checked')){
            check   = 0;
            estado  = false;
        }else{
            check   = 1;
            estado  = true;
        }

        validarrelleno(accion,name,estado,check);
        
    });


    $('#buscarpedidoautorizacion').on('click', function(event){

        event.preventDefault();
        var finicio     = $('#finicio').val();
        var ffin        = $('#ffin').val();
        var _token      = $('#token').val();
        $(".listatablapedido").html("");
        abrircargando();

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-listado-de-toma-pedidos-autorizacion",
            data    :   {
                            _token  : _token,
                            finicio : finicio,
                            ffin    : ffin
                        },
            success: function (data) {
                cerrarcargando();
                $(".listatablapedido").html(data);

            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });

    });




    $('#buscarpedido').on('click', function(event){

        event.preventDefault();
        var finicio     = $('#finicio').val();
        var ffin        = $('#ffin').val();
        var estado_id   = $('#estado_id').val();

        var _token      = $('#token').val();
        $(".listatablapedido").html("");
        abrircargando();

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-listado-de-toma-pedidos",
            data    :   {
                            _token  : _token,
                            finicio : finicio,
                            estado_id : estado_id,
                            ffin    : ffin
                        },
            success: function (data) {
                cerrarcargando();
                $(".listatablapedido").html(data);

            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });

    }); 



    $('#enviarpedidoautorizacion').on('click', function(event){

        event.preventDefault();
        $('#fechainicio').val($('#finicio').val());
        $('#fechafin').val($('#ffin').val());

        $('input[type=search]').val('').change();
        $("#tablatomapedido").DataTable().search("").draw();

        data = dataenviarautorizacion();
        if(data.length<=0){alerterrorajax("Seleccione por lo menos un pedido");return false;}
        var datastring = JSON.stringify(data);
        $('#pedido').val(datastring);

        console.log(data);
        abrircargando();
        $( "#formpedido" ).submit();
        
    });


    $('#enviarpedido_no_autorizacion').on('click', function(event){

        event.preventDefault();
        $('#fechainiciorechazar').val($('#finicio').val());
        $('#fechafinrechazar').val($('#ffin').val());

        $('input[type=search]').val('').change();
        $("#tablatomapedido").DataTable().search("").draw();

        data = dataenviarautorizacion();
        if(data.length<=0){alerterrorajax("Seleccione por lo menos un pedido");return false;}
        var datastring = JSON.stringify(data);
        $('#pedidorechazar').val(datastring);

        console.log(data);
        abrircargando();
        $( "#formpedidorechazar" ).submit();
        
    });



    $('#enviarpedido').on('click', function(event){
        event.preventDefault();

        //var mensaje                     = validarobsequios();
        var mensaje                     = '';
        //return false;

        if(mensaje != ''){
            alerterrorajax(mensaje); 
            return false;
        }else{

            $('#fechainicio').val($('#finicio').val());
            $('#fechafin').val($('#ffin').val());
            $('input[type=search]').val('').change();
            $("#tablatomapedido").DataTable().search("").draw();
            data = dataenviar();
            if(data.length<=0){alerterrorajax("Seleccione por lo menos un pedido");return false;}
            var datastring = JSON.stringify(data);
            $('#pedido').val(datastring);
            abrircargando();
            $( "#formpedido" ).submit();
        }
        
    });



    $('#enviarpedidorechazar').on('click', function(event){
        event.preventDefault();

        $('#fechainiciorechazar').val($('#finicio').val());
        $('#fechafinrechazar').val($('#ffin').val());

        $('input[type=search]').val('').change();
        $("#tablatomapedido").DataTable().search("").draw();

        data = dataenviar();
        if(data.length<=0){alerterrorajax("Seleccione por lo menos un pedido");return false;}
        var datastring = JSON.stringify(data);
        $('#pedidorechazar').val(datastring);

        abrircargando();
        $( "#formpedidorechazar" ).submit();
    });



    $('#enviarpedidorechazaranulacion').on('click', function(event){
        event.preventDefault();


        /*data = dataenviar();
        if(data.length<=0){alerterrorajax("Seleccione por lo menos un pedido");return false;}
        var datastring = JSON.stringify(data);
        $('#pedidorechazar').val(datastring);

        abrircargando();
        $( "#formpedidorechazar" ).submit();*/
    });


    $('#enviarpedidorechazar_modal').on('click', function(event){
        event.preventDefault();
        var _token      = $('#token').val();
        data = dataenviar_anulacion();
        if(data.length<=0){alerterrorajax("Seleccione por lo menos un pedido");return false;}
        var idopcion    = $(this).attr('data-opcion');

        /*var datastring = JSON.stringify(data);
        $('#pedidorechazar').val(datastring);*/

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-pedido-anulacion-observacion",
            data    :   {
                            _token                  : _token,
                            idopcion                : idopcion,
                        },    
            success: function (data) {
                $('.modal-detalledocumento-container-rechazo').html(data);
                $('#modal-detalledocumento-rechazo').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        }); 


    });




    /**********************************************************/

    //guardar pedido

    $(".crearpedido").on('click','.btn-guardar', function(e) {

        var cliente             =   $('#cliente').val();
        var obs             =   $('#iobs').val();
        var recibo             =   $('#irecibo').val();
        console.log(obs+' '+recibo);
        agregar_obs_hidden(obs,recibo);
        // validacion cliente
        if(cliente==''){ alertdangermobil("Seleccione un cliente"); return false;}
        // validacion productos
        data = agregar_producto_hidden();
        if(data.length<=0){alertdangermobil("Seleccione por lo menos un producto"); return false;}
        var datastring = JSON.stringify(data);
        $('#productos').val(datastring);
        abrircargando();
        return true;
    });


    $(".crearpedido").on('click','.btn-guardar-obsequio', function(e) {
        // validacion productos
        data = agregar_producto_hidden_obsequio();
        if(data.length<=0){alertdangermobil("Seleccione por lo menos un producto (OBSEQUIO)"); return false;}
        var datastring = JSON.stringify(data);
        $('#productos').val(datastring);
        abrircargando();
        return true;
    });



    $(".crearpedido").on('click','.filapedido', function(e) {

        var data_icl        =   $(this).attr('data_icl');
        var data_pcl        =   $(this).attr('data_pcl');
        var data_icu        =   $(this).attr('data_icu');
        var data_pcu        =   $(this).attr('data_pcu');
        var data_ncl        =   $(this).attr('data_ncl');
        var data_dcl        =   $(this).attr('data_dcl');
        var data_ccl        =   $(this).attr('data_ccl');
        var _token          =   $('#token').val();


        $.ajax({
              type    :   "POST",
              url     :   carpeta+"/ajax-direcion-cliente",
              data    :   {
                            _token                : _token,
                            data_icl              : data_icl,
                            data_pcl              : data_pcl,
                            data_icu              : data_icu,
                            data_pcu              : data_pcu,
                            data_ncl              : data_ncl,
                            data_dcl              : data_dcl,
                            data_ccl              : data_ccl,
                          },
            beforeSend: function() {
                $('.ajaxdirecciones').html("<div class='row text-center'><div class='lds-roller'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>");
            },
            success: function (data) {
                $('.ajaxdirecciones').html(data);
            },
            error: function (data) {
                error500(data);
                setTimeout(function(){ $('.listaclientes').toggle("slow");  $('.direccioncliente').toggle("slow");}, 2000);
            }
        });

        $('.listaclientes').toggle("slow");
        $('.direccioncliente').toggle("slow");

    });


    $(".crearpedido").on('click','.mdi-close-cliente', function(e) {

        $('.listaclientes').toggle("slow");
        $('.direccioncliente').toggle("slow");

    });
    $(".crearpedido").on('click','.mdi-check-cliente', function(e) {

        var direccion_select            =   $('#direccion_select').val();
        var tipopago_select             =   $('#tipopago_select').val();
        var fechadespacho               =   $('#fechades').val();
        var nombre_direccion_select     =   $('#direccion_select :selected').text();
        var nombre_tipopago_select     =   $('#tipopago_select :selected').text();
        var data_icl                    =   $(this).attr('data_icl');
        var data_pcl                    =   $(this).attr('data_pcl');
        var data_icu                    =   $(this).attr('data_icu');
        var data_pcu                    =   $(this).attr('data_pcu');
        var data_ncl                    =   $(this).attr('data_ncl');
        var data_dcl                    =   $(this).attr('data_dcl');
        var data_ccl                    =   $(this).attr('data_ccl');

        // validacion dirección

        if(direccion_select ==''){ alertdangermobil("Seleccione una dirección."); return false;}
        if(tipopago_select ==''){ alertdangermobil("Seleccione tipo de pago."); return false;}
        if(validarfechadespacho(fechadespacho)){ alertdangermobil("Fecha de despacho inválida."); return false;}
       
        $('.listaclientes').toggle("slow");
        $('.direccioncliente').toggle("slow");
        activaTab('productotp');
        agregar_cliente(data_ncl,data_dcl,data_ccl,nombre_direccion_select,nombre_tipopago_select,fechadespacho);
        agregar_cliente_hidden(data_pcl,data_icl,data_pcu,data_icu,direccion_select,tipopago_select,fechadespacho);  
        alertmobil("Cliente "+data_ncl+" seleccionado");
     
        return true;
    });




    /**************** PRODCUTO *************/

    /**************** PRODCUTO *************/

    $(".crearpedido").on('click','.filaproducto', function(e) {

        var data_ipr        =   $(this).attr('data_ipr');
        var data_ppr        =   $(this).attr('data_ppr');
        var data_npr        =   $(this).attr('data_npr');
        var data_upr        =   $(this).attr('data_upr');
        var _token          =   $('#token').val();

        var cli_id          =   $('#cliente').val();
        var cuenta_id       = $('#cuenta').val();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-regla-producto",
            data    :   {
                          _token                : _token,
                          data_ipr              : data_ipr,
                          data_ppr              : data_ppr,
                          data_npr              : data_npr,
                          data_upr              : data_upr,
                          cli_id                : cli_id,
                          cuenta_id             : cuenta_id
                            },

            success: function (data) {
                $('.ajaxreglaproducto').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });

        $('.listaproductos').toggle("slow");
        $('.precioproducto').toggle("slow");
        tituloprecioproducto(data_npr,data_upr,data_ipr,data_ppr);

    });


    $(".crearpedido").on('click','.filaproductoobsequio', function(e) {

        var data_ipr        =   $(this).attr('data_ipr');
        var data_ppr        =   $(this).attr('data_ppr');
        var data_npr        =   $(this).attr('data_npr');
        var data_upr        =   $(this).attr('data_upr');
        var _token          =   $('#token').val();
        var cli_id          =   $('#cliente').val();
        var cuenta_id       = $('#cuenta').val();
        var pedido_id       = $('#pedido_id').val();
        var relacionadas       = $('#relacionadas').val();

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-regla-producto-obsequio",
            data    :   {
                          _token                : _token,
                          data_ipr              : data_ipr,
                          data_ppr              : data_ppr,
                          data_npr              : data_npr,
                          data_upr              : data_upr,
                          cli_id                : cli_id,
                          pedido_id             : pedido_id,
                          relacionadas          : relacionadas,
                          cuenta_id             : cuenta_id
                        },

            success: function (data) {
                $('.ajaxreglaproducto').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });

        $('.listaproductos').toggle("slow");
        $('.precioproducto').toggle("slow");
        tituloprecioproducto(data_npr,data_upr,data_ipr,data_ppr);

    });





    $(".crearpedido").on('click','.mdi-close-precio', function(e) {

        $('.listaproductos').toggle("slow");
        $('.precioproducto').toggle("slow");

    });

    // atras en los tabs
    $(".crearpedido #pedidotp").on('click','.col-atras', function(e) {
        activaTab('productotp');
    });
    $(".crearpedido #productotp").on('click','.col-atras', function(e) {
        activaTab('clientetp');
    });

    // borrando un producto
    $(".crearpedido").on('click','.mdi-close-pedido', function(e) {

        relacion = $(this).parents('.productoseleccion').attr('data_ipo');
        obsequio = $(this).parents('.productoseleccion').attr('data_obq');        
        sw       = 0;
        if(obsequio == '0'){
            $(".detalleproducto .productoseleccion").each(function(){
                var data_obq = $(this).attr('data_obq');
                var data_ipo = $(this).attr('data_ipo');
                if(data_obq == "1"){
                    if(relacion == data_ipo){
                        sw       = 1;
                    }
                }
            });
            if(sw ==1){ alertdangermobil("Tiene un obsequio asociado."); return false;}
        }

        $(this).parents('.productoseleccion').remove();
        //agregar_producto_hidden();
        calcular_total(); 
        quitar_etiqueta_obsequio();


    });

    $(".crearpedido").on('click','#obsequio', function() {

        var _token          =   $('#token').val();
        if($("#obsequio").is(':checked')){
            data                =   agregar_producto_hidden();
            var datastring      =   JSON.stringify(data);
            $('#productos').val(datastring);
            $.ajax({
                type    :   "POST",
                url     :   carpeta+"/ajax-relacion-producto-obsequio",
                data    :   {
                              _token                : _token,
                              datastring            : datastring
                            },

                success: function (data) {
                    $('.ajax-obsequio-relacion').html(data);
                    $('.ajax-obsequio-relacion').css('display', 'block');
                },
                error: function (data) {
                    error500(data);
                }
            });
            //$( "#precio" ).prop( "disabled", true );
            //$( "#precio" ).val('0.0000');
        }else{
             $('.ajax-obsequio-relacion').css('display', 'none');
            //$( "#precio" ).prop( "disabled", false );
        }

    });


    // agregando producto
    $(".crearpedido").on('click','.mdi-check-precio', function(e) {

        var cantidad                =   $('#cantidad').val();
        var precio                  =   $('#precio').val();
        precio                      =   precio.replace(",", "");
        cantidad                    =   cantidad.replace(",", "");
        var txt_producto_obsequio   =   $('#txt_producto_obsequio').val();


        if($("#obsequio").is(':checked')){
            obsequio = 1;
            ind_producto_obsequio    =   $('#ind_producto_obsequio').val();
            if(ind_producto_obsequio =='' || ind_producto_obsequio === null){ alertdangermobil("Seleccione relacion obsequio"); return false;}
            //precio = '0.0000';
            txt_producto_obsequio    =   ind_producto_obsequio;
        }else{
            obsequio = 0;
            contador_ind_producto_obsequio(txt_producto_obsequio);
        }

        var data_ipr            =   $(this).attr('data_ipr');
        var data_ppr            =   $(this).attr('data_ppr');
        var data_npr            =   $(this).attr('data_npr');
        var data_upr            =   $(this).attr('data_upr');

        // validacion cantidad
        if(cantidad =='0.0000' || cantidad==''){ alertdangermobil("Ingrese cantidad"); return false;}
        if(precio =='0.0000' || precio==''){ alertdangermobil("Ingrese precio"); return false;}
        /*if(obsequio == 0){
           if(precio =='0.0000' || precio==''){ alertdangermobil("Ingrese precio"); return false;}   
        }*/

        if(existe_producto(data_ppr,data_ipr,obsequio) == '0'){ alertdangermobil("El producto ya existe en el pedido"); return false;}

        agregar_producto(data_npr,data_upr,cantidad,precio,data_ipr,data_ppr,obsequio,txt_producto_obsequio);
        quitar_etiqueta_obsequio();
        relacionadas();

        //agregar_producto_hidden();
        calcular_total();
        alertmobil("Producto "+data_npr+" agregado");
        limpiar_input_producto();
        $('.listaproductos').toggle("slow");
        $('.precioproducto').toggle("slow");
        return true;

    });
});

function relacionadas(){

    cadena = '';
    $(".detalleproducto .productoseleccion").each(function(){

        var data_ipo = $(this).attr('data_ipo');
        var data_upd = $(this).attr('data_upd');
        if(data_upd == '1'){
            cadena = cadena+data_ipo+',';
        }
    });

    $('#relacionadas').val(cadena);

}
function quitar_etiqueta_obsequio(){

    $(".detalleproducto .productoseleccion").each(function(){

        var data_ipo = $(this).attr('data_ipo');
        var sw       = 0;

        $(".detalleproducto .productoseleccion").each(function(){
            if(data_ipo == $(this).attr('data_ipo')){
                sw=sw+1;
            }
        });

        if(sw==1){
            $(this).find('.txtrelacion').html('');
        }else{
            $(this).find('.txtrelacion').html('('+data_ipo+')');
        }

    });

}

function contador_ind_producto_obsequio(txt_producto_obsequio){
        $('#txt_producto_obsequio').val(parseInt(txt_producto_obsequio)+1);
}

function cambiar_estado_rechazado(puntero){

    var cadena = '';            
    cadena += "<span class='badge badge-danger'>RECHAZADO</span>";
    puntero.parents('.rechazar').siblings('.estado_detalle_pedido').html(cadena);

}

function validardatadetallepedido(){

    var txtmensaje = '';
    var sw_ob = 0;
    var cont_ob = 0;
    var sw_no_ob = 0;

    var sw_dif_ob = 0;
    var sw = 0;
    var count = 0;
    var empresa_primero_id =   '';
    var empresa_id =   '';
    var obsequio_si =   0;
    var obsequio_no =   0;

    $(".listatabledetalle .detalle_pedido").each(function(){


        var empresa_id                      =   $(this).find('.empresa_id');
        var check                           =   $(this).find('.input_check_pe');
        var data_obsequio                   =   $(this).attr("data_obsequio");
        var orden_detalle_pedido_id         =   $(this).find('.select_orden_detalle_pedido_id').find('#orden_detalle_pedido_id').val();
        var orden_detalle_pedido_id_inicio  =   '';
        var data_cantidad                   =   $(this).attr("data_cantidad");
        var data_atendido                   =   $(this).attr("data_atendido").trim();
        var atender                         =   $(this).find('#atender').val();
        data_cantidad                       =   data_cantidad.replace(",", "");
        data_atendido                       =   data_atendido.replace(",", "");
        atender                             =   atender.replace(",", "");
        var data_nombre                     =   $(this).attr("data_nombre");



        if($(check).is(':checked')){

            if(count==0){
                empresa_primero_id                      =   $(this).find('#empresa_id').val();
            }
            empresa_id                             =   $(this).find('#empresa_id').val();
            
            if(empresa_primero_id != empresa_id){
                txtmensaje = 'Solo se puede redireccionar a una sola empresa';
            }
            if(data_obsequio==0){
                obsequio_no = 1;
            }
            if(data_obsequio==1){
               obsequio_si  = 1;
            }


            if(parseFloat(atender) <=0){
                txtmensaje = 'Cantidad atender del producto ('+data_nombre+') atendido debe ser mayor a cero';
            }else{
                if(parseFloat(atender) > (data_cantidad-data_atendido)){
                    txtmensaje = 'Cantidad atender del producto ('+data_nombre+') excede a lo solicitado';
                }
            }
            sw = 1;
            count = count + 1;


            //validar si esta que se va con su venta
            var data_ind_producto_obsequio      =   $(this).attr("data_ind_producto_obsequio");

            var sw_c = 0;
            $(".listatabledetalle .detalle_pedido").each(function(){
                var check_c                     =   $(this).find('.input_check_pe');
                var data_estado_id    =   $(this).attr("data_estado_id");


                if(data_estado_id!='EPP0000000000004'){
                    // no esta con check
                    if(!($(check_c).is(':checked'))){
                        var ind_producto_obsequio    =   $(this).attr("data_ind_producto_obsequio");

                        data_estado_id
                        if(ind_producto_obsequio == data_ind_producto_obsequio){
                            sw_c = 1;
                        }
                    }          
                }

            });
            if(sw_c == 1 ){
                txtmensaje = 'No se puede realizar la venta porque hay productos asociados';
            }

        }
        
    });

    if(sw == 0){
        txtmensaje = 'Es necesario seleccionar un producto';
    }

    if(obsequio_si == 1){
        if(obsequio_no == 0){
            txtmensaje = 'El obsequio no puede atenderse solo';
        }
    }

    return txtmensaje;
}


function datadetallepedido(){

        var data = [];


        $(".listatabledetalle .detalle_pedido").each(function(){

            var check                       =   $(this).find('.input_check_pe');
            var detalle                     =   $(this).attr("data_detalle_pedido_id");
            var empresa_id                  =   $(this).find('.select_empresa').find('#empresa_id').val();
            var atender                     =   $(this).find('#atender').val();
            atender                         =   atender.replace(",", "");
            var orden_detalle_pedido_id     =   $(this).find('.select_orden_detalle_pedido_id').find('#orden_detalle_pedido_id').val();
 
            if (typeof orden_detalle_pedido_id === "undefined") {
                orden_detalle_pedido_id = '';
            }

            var detalle_pedido_id           =   $(this).attr("data_detalle_pedido_id");
            var estado_id                   =   $(this).attr("data_estado_id");
            var data_obsequio               =   $(this).attr("data_obsequio");
            var checked                     =   "";

            if($(check).is(':checked')){
                checked = "checked";
            }

            data.push({
                detalle_pedido_id       : detalle_pedido_id,
                empresa_id              : empresa_id,
                orden_detalle_pedido_id : orden_detalle_pedido_id,  
                ind_obsequio            : data_obsequio,
                checked                 : checked,
                estado_id               : estado_id,
                atender                 : atender,
            });

        });
        return data;
}

function dataenviarautorizacion(){
        var data = [];
        $(".listatabla tr").each(function(){
            check   = $(this).find('input');
            nombre  = $(this).find('input').attr('id');
            if(nombre != 'todo'){
                if($(check).is(':checked')){
                    data.push({id: $(check).attr("id")});
                }               
            }
        });
        return data;
}

function actualizartotalesproducto(pedido_id){
        var importet = 0.0000;
        $(".lista_detalle_pedido tbody tr").each(function(){
            if($(this).find('.columna-importe').length) {
                importe    =  $(this).find('.columna-importe').html();
                importet   =  importet + parseFloat(importe.trim());
            }
        });
        $('.p'+pedido_id).html(importet.toFixed(4))
}


function dataenviar(){
        var data = [];
        $(".listatabla tr").each(function(){
            check   = $(this).find('input');
            nombre  = $(this).find('input').attr('id');

            if(nombre != 'todo'){

                //json detalle
                var datajsondetalle  = $(this).find('.btn-detalle-pedido').attr('data-json-detalle');
                 

                if($(check).is(':checked')){
                    data.push({
                        id      : $(check).attr("id"),
                        detalle : datajsondetalle
                    });                    
                }               
            }

        });
        return data;
}

function dataenviar_anulacion(){
        var data = [];
        $(".listatabla tr").each(function(){
            check   = $(this).find('input');
            nombre  = $(this).find('input').attr('id');

            if(nombre != 'todo'){

                if($(check).is(':checked')){
                    data.push({
                        id      : $(check).attr("id"),
                    });                    
                }               
            }

        });
        return data;
}



function validarobsequios(){

    var mensaje = '';
        $(".listatabla tr").each(function(){
            check   = $(this).find('.input_check_pe_ln');
            nombre  = $(this).find('.input_check_pe_ln').attr('id');

            if(nombre != 'todo'){

                //json detalle
                var datajsondetalle  = $(this).find('.btn-detalle-pedido').attr('data-json-detalle');
                //debugger;
                if($(check).is(':checked')){

                    var datajsondetalle_array = JSON.parse(datajsondetalle);
                    datajsondetalle_array.forEach(function(item, index) {


                        if(item.ind_obsequio==1 && item.orden_detalle_pedido_id=='' && item.checked != ''){
                            mensaje = 'Existe obsequios sin venta asociada';
                        }
                    });
                                       
                }               
            }

        });
    return mensaje;
}



function validarfechadespacho(fecha){

    var fecha1= moment().format("YYYY-MM-DD");
    var fecha2= moment(fecha).format("YYYY-MM-DD");

   
    if ( moment(fecha2).isBefore(fecha1) || fecha==''  ) {
        console.log(fecha1+' '+fecha2 +' '+ fecha);
            return true;
    }
    else {
      return false;
    }
}

function validarrelleno(accion,name,estado,check,token){

    if (accion=='todas') {

        var table = $('#tfactura').DataTable();
        $(".listatabla tr").each(function(){
            nombre = $(this).find('input').attr('id');

            if(nombre != 'todo' && !$(this).find('input').is(':disabled')){
                $(this).find('input').prop("checked", estado);
            }
        });
    }else{
        sw = 0;
        if(estado){
            $(".listatabla tr").each(function(){

                nombre = $(this).find('input').attr('id');
                if(nombre != 'todo'){
                    if(!($(this).find('input').is(':checked'))){
                        sw = sw + 1;
                    }
                }

            });
            if(sw==1){
                $("#todo").prop("checked", estado);
            }
        }else{
            $("#todo").prop("checked", estado);
        }           
    }
}


function activaTab(tab){
    $('.nav-tabs a[href="#' + tab + '"]').tab('show');
}

function tituloprecioproducto(data_npr,data_upr,data_ipr,data_ppr){

    // limpiar
    $('.p_nombre_producto').html('');
    $('.p_unidad_medida').html('');

    $(".mdi-check-precio").attr("data_ipr",'');
    $(".mdi-check-precio").attr("data_ppr",'');
    $(".mdi-check-precio").attr("data_npr",'');
    $(".mdi-check-precio").attr("data_upr",'');

    // AGREGAR 

    $('.p_nombre_producto').html(data_npr);
    $('.p_unidad_medida').html(data_upr);

    // agregar todos los valores del producto al check 
    $(".mdi-check-precio").attr("data_ipr",data_ipr);
    $(".mdi-check-precio").attr("data_ppr",data_ppr);
    $(".mdi-check-precio").attr("data_npr",data_npr);
    $(".mdi-check-precio").attr("data_upr",data_upr);
  

}





function limpiar_input_producto(){
    $('#cantidad').val('');
    $('#precio').val('');
    $( "#precio" ).prop( "disabled", false );
    $("#obsequio").removeAttr('checked');
}


function agregar_cliente(nombrecliente,ruc,cuenta,nombre_direccion_select,nombre_tipopago_select,fechadespacho){

    var cadena = '';            
    cadena += " <div class='col-sm-12 col-mobil-top'>";
    cadena += "     <div class='panel panel-full'>";
    cadena += "         <div class='panel-heading cell-detail'>";
    cadena +=               nombrecliente;
    cadena += "             <span class='panel-subtitle cell-detail-description-producto'>"+ruc+"</span>";
    cadena += "             <span class='panel-subtitle cell-detail-description-contrato'>"+cuenta+"</span>";
    cadena += "             <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Fecha de entrega:</strong> <small>"+fechadespacho+"</small></span>";
    cadena += "             <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Dirección de entrega:</strong> <small>"+nombre_direccion_select+"</small></span>";
    cadena += "             <span class='panel-subtitle cell-detail-direccion-entrega'><strong>Condición de pago :</strong> <small>"+nombre_tipopago_select+"</small></span>";
    cadena += "         </div>";
    cadena += "     </div>";
    cadena += " </div>";

    $(".detallecliente").html(cadena);


}
function agregar_cliente_hidden(data_pcl,data_icl,data_pcu,data_icu,direccion_select,tipopago_select,fechadespacho){
    $('#cliente').val(data_pcl+'-'+data_icl);   
    $('#direccion_entrega').val(direccion_select);
    $('#cuenta').val(data_pcu+'-'+data_icu);
    $('#condicion_pago').val(tipopago_select);
    $('#fecha_entrega').val(fechadespacho);

}
function agregar_obs_hidden(obs, recibo){
    $('#obs').val(obs);   
    $('#recibo').val(recibo);


}


function agregar_producto_hidden(){

    var data = [];
    $(".detalleproducto .productoseleccion").each(function(){

        var data_ppr_for = $(this).attr('data_ppr');
        var data_ipr_for = $(this).attr('data_ipr');
        var data_prpr_for = $(this).attr('data_prpr');
        var data_ctpr_for = $(this).attr('data_ctpr');
        var data_obq = $(this).attr('data_obq');
        var data_ipo = $(this).attr('data_ipo');

        data.push({
            prefijo_producto    : data_ppr_for,
            id_producto         : data_ipr_for,
            precio_producto     : data_prpr_for,
            cantidad_producto   : data_ctpr_for,
            obsequio   : data_obq,
            ind_producto_obsequio   : data_ipo,
        });

    });
    return data;
}


function agregar_producto_hidden_obsequio(){

    var data = [];
    $(".detalleproducto .productoseleccion").each(function(){

        var data_ppr_for = $(this).attr('data_ppr');
        var data_ipr_for = $(this).attr('data_ipr');
        var data_prpr_for = $(this).attr('data_prpr');
        var data_ctpr_for = $(this).attr('data_ctpr');
        var data_obq = $(this).attr('data_obq');
        var data_upd = $(this).attr('data_upd');
        var data_ipo = $(this).attr('data_ipo');

        if(data_upd != '0'){
            data.push({
                prefijo_producto    : data_ppr_for,
                id_producto         : data_ipr_for,
                precio_producto     : data_prpr_for,
                cantidad_producto   : data_ctpr_for,
                obsequio   : data_obq,
                ind_producto_obsequio   : data_ipo,
            });
        }

    });
    return data;
}




function calcular_total(){
    var total = 0.00;

    $(".detalleproducto .productoseleccion").each(function(){
        var subtotal     = 0;
        var data_ppr_for = $(this).attr('data_prpr');
        var data_ipr_for = $(this).attr('data_ctpr');
        var data_obq     = $(this).attr('data_obq');

        if(data_obq=='0'){
            var subtotal     = parseFloat(data_ppr_for)*parseFloat(data_ipr_for);
        }
    
        total = total + subtotal;
    });
    $('.total').html(total.toFixed(4));
}


function existe_producto(data_ppr,data_ipr,obsequio){

    var sw = '1';
    $(".detalleproducto .productoseleccion").each(function(){
        var data_ppr_for = $(this).attr('data_ppr');
        var data_ipr_for = $(this).attr('data_ipr');
        var data_obq = $(this).attr('data_obq');
        if(obsequio == 0){
            if(data_ppr_for == data_ppr && data_ipr_for == data_ipr && data_obq == 0){
                sw = '0';
            }        
        }
        if(obsequio == 1){
            if(data_ppr_for == data_ppr && data_ipr_for == data_ipr && data_obq == 1){
                sw = '0';
            }        
        }
    });
    return sw;

}



function agregar_producto(nombreproducto,unidadmedida,cantidad,precio,data_ipr,data_ppr,obsequio,ind_producto_obsequio){

    var txtobsequio = '';
    var importe = 0;
    var update  = 1;
    if(obsequio==1){
        txtobsequio = ' (Obsequio)';
    }else{
        importe = parseFloat(cantidad)*parseFloat(precio);
    }

    var cadena = '';  
    cadena += "<div class='col-sm-12 productoseleccion col-mobil-top'";
    cadena += "data_ipr='"+data_ipr+"' data_ppr= '"+data_ppr+"' data_prpr='"+precio+"' data_ctpr='"+cantidad+"' data_obq='"+obsequio+"' data_upd='"+update+"' data_ipo='"+ind_producto_obsequio+"'>" 
    cadena += "     <div class='panel panel-default panel-contrast'>";
    cadena += "         <div class='panel-heading cell-detail'>";
    cadena +=               nombreproducto+"<span class='txtobsequio'> "+txtobsequio+"</span>"+"<span class='txt-danger txtrelacion'"+ind_producto_obsequio+"> ("+ind_producto_obsequio+")</span>";
    cadena += "             <div class='tools'>";
    cadena += "                 <span class='icon mdi mdi-close mdi-close-pedido'></span>";
    cadena += "             </div>";
    cadena += "             <span class='panel-subtitle cell-detail-producto'>Cantidad : "+cantidad+" "+unidadmedida+"</span>";
    cadena += "             <span class='panel-subtitle cell-detail-producto'>Precio : S/."+precio+" <strong> Importe "+importe.toFixed(4)+" </strong></span>";
    cadena += "         </div>";
    cadena += "     </div>";
    cadena += "</div>";
    $(".detalleproducto").append(cadena);

}