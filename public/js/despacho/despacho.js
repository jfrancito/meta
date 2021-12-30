
$(document).ready(function(){

	var carpeta = $("#carpeta").val();



    $(".despacho").on('click','.rechazarproductogestion', function() {

        var _token                      = $('#token').val();
        var ordendespacho_id            = $('#ordendespacho_id').val();
        var data                        = [];
        var sw                          = 0;
        var msj                         = '';

        $(".table-pedidos-despachos tbody tr").each(function(){
            check                               =   $(this).find('.input_asignar_lp');

            if($(check).is(':checked')){

                var cabecera_tabla_tr               =   $(this);
                var mobil_grupo                     =   $(cabecera_tabla_tr).attr('mobil_grupo');
                var guia_remision_id                =   $(cabecera_tabla_tr).attr('guia_remision_id');
                var nro_serie                       =   $(cabecera_tabla_tr).attr('nro_serie');
                var nro_documento                   =   $(cabecera_tabla_tr).attr('nro_documento');
                var orden_transferencia_id          =   $(cabecera_tabla_tr).attr('orden_transferencia_id');
                var nombre_producto                 =   $(cabecera_tabla_tr).attr('nombre_producto');
                var data_detalle_orden_despacho     =   $(cabecera_tabla_tr).attr('data_detalle_orden_despacho');

                if(guia_remision_id != ''){
                    msj = nombre_producto+' tiene guia de remision asociada';
                    sw=1;
                }
                if(nro_serie != ''){
                    msj = nombre_producto+' tiene una serie asignada';
                    sw=1;
                }
                if(nro_documento != ''){
                    msj = nombre_producto+' tiene un numero de documento asignada';
                    sw=1;
                }
                if(orden_transferencia_id != ''){
                    msj = nombre_producto+' tiene una Transferencia PT asignada';
                    sw=1;
                }

                data.push({
                    data_detalle_orden_despacho  : data_detalle_orden_despacho,
                    mobil_grupo                  : mobil_grupo,
                });
            }               
        });

        if(sw==1){alerterrorajax(msj); return false;}
        if(data.length<=0){alerterrorajax('Seleccione por lo menos un producto'); return false;}

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-rechazar-producto-gestion",
            data    :   {
                            _token                      : _token,
                            ordendespacho_id            : ordendespacho_id,
                            data_productos_rechazar     : data,
                        },
            success: function (data) {
                cerrarcargando();
                alertajax("Producto agregado exitosa");
                $('.lista_orden_gestion').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });

    });



    $(".despacho").on('change','#grupo_mobil_modal,#cuenta_id_modal', function() {

        var _token                      =   $('#token').val();
        var grupo_mobil_modal           =   $('#grupo_mobil_modal').val();
        var cuenta_id_modal             =   $('#cuenta_id_modal').val();        
        var ordendespacho_id            =   $('#ordendespacho_id').val();

        cargar_combo_orden_cen(ordendespacho_id,grupo_mobil_modal,cuenta_id_modal,_token)


    });


    function cargar_combo_orden_cen(ordendespacho_id,grupo_mobil_modal,cuenta_id_modal,_token){


        abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-orden-cen-mobil-modal",
            data    :   {
                            _token                  : _token,
                            grupo_mobil_modal       : grupo_mobil_modal,
                            ordendespacho_id        : ordendespacho_id,
                            cuenta_id_modal         : cuenta_id_modal,
                        },
            success: function (data) {
                $(".ajax_ordencen_modal").html(data);
                cerrarcargando();
            },
            error: function (data) {
                error500(data);
            }
        });

    }


    $(".despacho").on('click','#agregarproductosatender', function() {

        event.preventDefault();

        var _token                  = $('#token').val();
        var tabestado               = $('#tabestado').val();
        var grupo_mobil_modal       = $('#grupo_mobil_modal').val();
        var cuenta_id_modal         = $('#cuenta_id_modal').val();
        var orden_cen_modal         = $('#orden_cen_modal').val();


        if(tabestado == 'prod'){

            $('input[type=search]').val('').change();
            $("#despacholopatender").DataTable().search("").draw();
            var data_producto           = dataenviarproducto();
            var ordendespacho_id        = $('#ordendespacho_id').val();
            if(data_producto.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}
            $('#modal-detalledocumento-atender').niftyModal('hide');
            $('.modal-detalledocumento-atender-container').html('');

            $.ajax({

                type    :   "POST",
                url     :   carpeta+"/ajax-modal-agregar-producto-pedido-gestion",
                data    :   {
                                _token                  : _token,
                                data_producto           : data_producto,
                                ordendespacho_id        : ordendespacho_id,
                                grupo_mobil_modal       : grupo_mobil_modal,
                                cuenta_id_modal         : cuenta_id_modal,
                                orden_cen_modal         : orden_cen_modal,
                            },    
                success: function (data) {
                    cerrarcargando();
                    alertajax("Producto agregado exitosa");
                    $('.lista_orden_gestion').html(data);
                },
                error: function (data) {
                    error500(data);
                }
            });


        }
    });




    $(".despacho").on('click','.agregarproductogestion', function() {

        var _token                      = $('#token').val();
        var ordendespacho_id            = $('#ordendespacho_id').val();

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-lista-orden-gestion-producto",
            data    :   {
                            _token              : _token,
                            ordendespacho_id    : ordendespacho_id,
                        },
            success: function (data) {
                cerrarcargando();
                $('.modal-detalledocumento-atender-container').html(data);
                $('#modal-detalledocumento-atender').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });

    });


    $(".despacho").on('click','.checkbox_asignar_lp', function() {

        var input   = $(this).siblings('.input_asignar_lp');
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


    $(".despacho").on('click','.input_asignar_gop', function() {

        var data_check_oc     = $(this).attr('data_check_oc');
        var check             = -1;
        var estado            = -1;
        var accion            = 'ver';

        if($(this).is(':checked')){
            check = true;
            estado  = true;
        }else{
            check = false;
            estado  = false;            
        }  
        $(".table-pedidos-despachos tbody tr").each(function(){
            data_check_sel  = $(this).find('.input_asignar_lp').attr('data_check_sel');
            if(data_check_oc == data_check_sel){
                $(this).find('.input_asignar_lp').prop('checked', check);
            }               
        });

        validarrelleno(accion,name,estado,check);
        //check_todosrellenar
        var sw = 0;
        $(".table-pedidos-despachos tbody tr").each(function(){
            nombre = $(this).find('.input_asignar_lp').attr('id');
            if(nombre != 'todo_asignar'){
                if(!($(this).find('.input_asignar_lp').is(':checked'))){
                    sw = sw + 1;
                }
            }             
        });
        if(sw==0){
            $(".todo_asignar").prop('checked', true);
        }else{
            $(".todo_asignar").prop('checked', false);
        }

    });



    $(".despacho").on('click','.guardarcambios', function() {

        event.preventDefault();

        var _token                  = $('#token').val();
        var array_detalle_producto  = $('#array_detalle_producto').val();
        var grupo                   = $('#grupo').val();
        var numero_mobil            = $('#numero_mobil').val();
        var correlativo             = $('#correlativo').val(); 
        var opcion_id               = $('#opcion').val();

        var data_producto_despacho  = dataproductoatender_edit();
        if(data_producto_despacho.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}


        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-pedido-crear-update-pedido-despacho-centro",
            data    :   {
                            _token                      : _token,
                            array_detalle_producto      : array_detalle_producto,
                            grupo                       : grupo,
                            numero_mobil                : numero_mobil,
                            correlativo                 : correlativo,
                            opcion_id                   : opcion_id,
                            data_producto_despacho      : data_producto_despacho,
                        },
            success: function (data) {
                cerrarcargando();
                alertajax("Modificación exitosa");
                $('.lista_pedidos_despacho').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });

    });


    function dataproductoatender_edit(){

        var data = [];
        var centro_atender_id = ''

        $(".table-pedidos-despachos tbody tr").each(function(){

                data_correlativo                = $(this).attr('data_correlativo');
                var muestra                     = $(this).find('#muestra').val();
                var centro_atender_val          = $(this).find('#centro_atender_id').val();

                var precio                      = $(this).find('#precio').val();
                muestra                         = muestra.replace(",", "");
                precio                          = precio.replace(",", "");
                producto_id                     = $(this).attr('data_producto');


                if (centro_atender_val !== undefined) {
                    centro_atender_id  = centro_atender_val;
                }

                data.push({
                    data_correlativo        : data_correlativo,
                    muestra                 : muestra,
                    precio                  : precio,
                    centro_atender_id       : centro_atender_id,
                    producto_id             : producto_id,
                });

        });
        return data;
    }


    $(".despacho").on('click','.btn-guardar-pedido', function() {

        var data = [];
        var sw   = 0;
        var msj  = '';

        $(".table-pedidos-despachos tbody tr").each(function(){

            //debugger;
            correlativo             = $(this).attr('data_correlativo');
            check                   = $(this).find('.input_asignar_lp');
            fecha_entrega           = $(this).attr('fecha_entrega');
            mobil                   = $(this).attr('data_mobil');
            cantidad                = $(this).attr('data_cantidad');
            nombre_producto         = $(this).attr('nombre_producto');
            centro_origen           = $(this).attr('centro_origen');


            if(parseFloat(cantidad)<=0){
                msj = nombre_producto+' la cantidad debe ser mayor a 0';
                sw=1;
            }

            if(mobil=='0'){
                msj = nombre_producto+' no tiene asignado una mobil';
                sw=1;
            }

            if(fecha_entrega==''){
                msj = nombre_producto+' no tiene fecha de entrega';
                sw=1;
            }

            if(centro_origen==''){
                msj = nombre_producto+' no tiene centro de origen';
                sw=1;
            }


            if($(check).is(':checked')){
                data.push({
                    correlativo     : correlativo
                });
            }          

        });
        if(sw==1){alerterrorajax(msj); return false;}
        $('#ind_plantilla').val($('#plantilla_valor').val());

        return true;

    });


    $(".listadespacho").on('click','#buscarpedidoatender', function() {

        var _token              = $('#token').val();
        var fechainicio         = $('#fechainicio').val();        
        var fechafin            = $('#fechafin').val();
        var opcion_id           = $('#opcion_id').val();
        /****** VALIDACIONES ********/

        if(fechainicio == ''){
            alerterrorajax("Seleccione una fecha de inicio");
            return false;
        }

        if(fechafin == ''){
            alerterrorajax("Seleccione una fecha de fin");
            return false;
        } 

        abrircargando(); 
        $(".listatablapedidosatender").html("");

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-lista-atender-pedidos-despacho",
            data    :   {
                            _token          : _token,
                            fechainicio     : fechainicio,
                            fechafin        : fechafin,
                            opcion_id       : opcion_id,                      
                        },
            success: function (data) {
                cerrarcargando();
                $(".listatablapedidosatender").html(data);                
            },
            error: function (data) {
                cerrarcargando();
                if(data.status = 500){
                    var contenido = $(data.responseText);
                    alerterror505ajax($(contenido).find('.trace-message').html());  
                    console.log($(contenido).find('.trace-message').html());     
                }
            }
        });

    });



    $(".listadespacho").on('click','#buscarpedidodespacho', function() {

        var _token              = $('#token').val();
        var fechainicio         = $('#fechainicio').val();        
        var fechafin            = $('#fechafin').val();

        /****** VALIDACIONES ********/

        if(fechainicio == ''){
            alerterrorajax("Seleccione una fecha de inicio");
            return false;
        }

        if(fechafin == ''){
            alerterrorajax("Seleccione una fecha de fin");
            return false;
        } 

        abrircargando(); 
        $(".listatablapedidosdespachos").html("");

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-lista-pedidos-despacho",
            data    :   {
                            _token          : _token,
                            fechainicio     : fechainicio,
                            fechafin        : fechafin                            
                        },
            success: function (data) {
                cerrarcargando();
                $(".listatablapedidosdespachos").html(data);                
            },
            error: function (data) {
                cerrarcargando();
                if(data.status = 500){
                    var contenido = $(data.responseText);
                    alerterror505ajax($(contenido).find('.trace-message').html());  
                    console.log($(contenido).find('.trace-message').html());     
                }
            }
        });

    });


    $(".despacho").on('click','#modificarconfiguracionprouducto', function() {

        event.preventDefault();
        var _token                  = $('#token').val();
        var array_detalle_producto  = $('#array_detalle_producto').val();
        var grupo                   = $('#grupo').val();
        var numero_mobil            = $('#numero_mobil').val();

        var correlativo             = $('#correlativo').val();
        var cantidad_bolsa_saco     = $('#cantidad_bolsa_saco').val(); 
        var cantidad_saco_palet     = $('#cantidad_saco_palet').val();
        var producto_id             = $('#producto_configuracion_id').val();
        var opcion_id               = $('#opcion').val();

        if(cantidad_bolsa_saco == ''){alerterrorajax("Ingrese cantidad de bolsas que contiene un SACO");return false;}
        if(cantidad_saco_palet == ''){alerterrorajax("Ingrese cantidad de sacos que contiene un PALET");return false;}


        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modificar-configuracion-del-producto",
            data    :   {
                            _token                      : _token,
                            array_detalle_producto      : array_detalle_producto,
                            grupo                       : grupo,
                            numero_mobil                : numero_mobil,
                            correlativo                 : correlativo,
                            cantidad_bolsa_saco         : cantidad_bolsa_saco,
                            cantidad_saco_palet         : cantidad_saco_palet,
                            producto_id                 : producto_id,
                            opcion_id                   : opcion_id,
                        },
            success: function (data) {
                alertajax("Modificación exitosa");
                $('.lista_pedidos_despacho').html(data);
                $('#modal-cofiguracion-cantidad').niftyModal('hide');
            },
            error: function (data) {
                error500(data);
            }
        });


    });


    $(".despacho").on('click','.configuracion-despacho-cantidad', function() {

        var _token              = $('#token').val();
        var producto_id         = $(this).parents('.fila_pedido').attr('data_producto'); 

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-configuracion-producto-cantidad",
            data    :   {
                            _token          : _token,
                            producto_id     : producto_id
                        },
            success: function (data) {
                cerrarcargando();
                $('.modal-configuracion-container').html(data);
                $('#modal-cofiguracion-cantidad').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });
    });


    $(".despacho").on('click','.cambiarfechaentrega', function() {

        event.preventDefault();
        var radiomobil                  =   $("input[name='rmobil']:checked").val();
        if(typeof(radiomobil)  === "undefined"){alerterrorajax('Seleccione por lo menos un mobil'); return false;}
        $('#fecha_i_t').val("i");
        $('#modal-entrega').niftyModal();

    });


    $(".despacho").on('click','.cambiarfechaentregatotal', function() {
        event.preventDefault();
        $('#fecha_i_t').val("t");
        $('#modal-entrega').niftyModal();
    });


    $(".despacho").on('click','#modificarfechadeentrega', function() {

        event.preventDefault();
        var _token                  = $('#token').val();
        var array_detalle_producto  = $('#array_detalle_producto').val();
        var grupo                   = $('#grupo').val();
        var numero_mobil            = $('#numero_mobil').val();
        var correlativo             = $('#correlativo').val();
        var fechadeentrega          = $('#fechadeentrega').val(); 
        var opcion_id               = $('#opcion').val();
        var radiomobil              = $("input[name='rmobil']:checked").val();
        var fecha_i_t               = $('#fecha_i_t').val();


        if(fechadeentrega == ''){
            alerterrorajax("Seleccione una fecha de entrega");
            return false;
        }

        var data_producto_pedido = [];
        $(".table-pedidos-despachos tbody tr").each(function(){
            nombre                  = $(this).find('.input_asignar_lp').attr('id');
            if(nombre != 'todo_asignar'){

                var cabecera_tabla_tr               =   $(this);
                correlativo                         =   $(this).attr('data_correlativo');
                var mobil_grupo                     =   $(cabecera_tabla_tr).attr('mobil_grupo');
                if(mobil_grupo==radiomobil){
                    data_producto_pedido.push({
                        correlativo     : correlativo
                    });
                }
            }
        });

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-pedido-modificar-fecha-de-entrega",
            data    :   {
                            _token                      : _token,
                            data_producto_pedido        : data_producto_pedido,
                            array_detalle_producto      : array_detalle_producto,
                            grupo                       : grupo,
                            numero_mobil                : numero_mobil,
                            correlativo                 : correlativo,
                            fechadeentrega              : fechadeentrega,
                            opcion_id                   : opcion_id,
                            fecha_i_t                   : fecha_i_t,
                        },
            success: function (data) {
                alertajax("Modificación exitosa");
                $('.lista_pedidos_despacho').html(data);
                $('#modal-entrega').niftyModal('hide');

            },
            error: function (data) {
                error500(data);
            }
        });
    });


    $(".despacho").on('click','.crearmobil33palets', function() {

        event.preventDefault();
        var _token                  = $('#token').val();
        var array_detalle_producto  = $('#array_detalle_producto').val();
        var grupo                   = $('#grupo').val();
        var numero_mobil            = $('#numero_mobil').val();
        var correlativo             = $('#correlativo').val();
        var opcion_id               = $('#opcion').val();

        var radiomobil              = $("input[name='rmobil']:checked").val();


        if(typeof(radiomobil)  === "undefined"){alerterrorajax('Seleccione por lo menos un mobil'); return false;}

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-pedido-crear-mobil-33-palets",
            data    :   {
                            _token                      : _token,
                            array_detalle_producto      : array_detalle_producto,
                            grupo                       : grupo,
                            numero_mobil                : radiomobil,
                            correlativo                 : correlativo,
                            opcion_id                   : opcion_id,
                        },
            success: function (data) {
                alertajax("Modificación exitosa");
                $('.lista_pedidos_despacho').html(data);
                $('#modal-entrega').niftyModal('hide');

            },
            error: function (data) {
                error500(data);
            }
        });


    });



    $(".despacho").on('keypress','.updatepriced', function(e) {

        event.preventDefault();
        var _token                  = $('#token').val();
        var cantidad                = $(this).val();
        cantidad                    = cantidad.replace(",", "");
        var fila                    = $(this).parents('.fila_pedido').attr('data_correlativo');
        var producto_id             = $(this).parents('.fila_pedido').attr('data_producto');  
        var array_detalle_producto  = $('#array_detalle_producto').val();
        var correlativo             = $('#correlativo').val();
        var grupo                   = $('#grupo').val();
        var opcion_id               = $('#opcion').val();
        var numero_mobil            = $('#numero_mobil').val();

        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){


            $.ajax({
                
                type    :   "POST",
                url     :   carpeta+"/ajax-modificar-cantidad-producto-fila",
                data    :   {
                                _token                      : _token,
                                array_detalle_producto      : array_detalle_producto,
                                cantidad                    : cantidad,
                                producto_id                 : producto_id,
                                correlativo                 : correlativo,
                                grupo                       : grupo,
                                numero_mobil                : numero_mobil,
                                opcion_id                   : opcion_id,
                                fila                        : fila
                            },
                success: function (data) {

                    alertajax("Modificación exitosa");
                    $('.lista_pedidos_despacho').html(data);

                },
                error: function (data) {
                    error500(data);
                }
            });

        }
    });


    $(".despacho").on('keypress','.updatepricemuestradseparado', function(e) {

        event.preventDefault();
        var _token                  = $('#token').val();
        var muestra                 = $(this).val();
        muestra                     = muestra.replace(",", "");
        var fila                    = $(this).parents('.fila_pedido_muestras').attr('data_correlativo');
        var producto_id             = $(this).parents('.fila_pedido_muestras').attr('data_producto'); 
        var array_detalle_producto_muestra  = $('#array_detalle_producto_muestra').val();
        var correlativo             = $('#correlativo').val();
        var grupo                   = $('#grupo').val();
        var opcion_id               = $('#opcion').val();
        var numero_mobil            = $('#numero_mobil').val();
        var array_detalle_producto  = $('#array_detalle_producto').val();

        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){

            $.ajax({
                
                type    :   "POST",
                url     :   carpeta+"/ajax-modificar-muestra-producto-fila-separado",
                data    :   {
                                _token                      : _token,
                                array_detalle_producto      : array_detalle_producto,
                                array_detalle_producto_muestra      : array_detalle_producto_muestra,
                                muestra                     : muestra,
                                producto_id                 : producto_id,
                                correlativo                 : correlativo,
                                grupo                       : grupo,
                                numero_mobil                : numero_mobil,
                                opcion_id                   : opcion_id,
                                fila                        : fila
                            },
                success: function (data) {

                    alertajax("Modificación exitosa");
                    $('.lista_pedidos_despacho').html(data);

                },
                error: function (data) {
                    error500(data);
                }
            });

        }
    });




    $(".despacho").on('keypress','.updatepricemuestrad', function(e) {

        event.preventDefault();
        var _token                  = $('#token').val();
        var muestra                 = $(this).val();
        muestra                     = muestra.replace(",", "");
        var fila                    = $(this).parents('.fila_pedido').attr('data_correlativo');
        var producto_id             = $(this).parents('.fila_pedido').attr('data_producto');  
        var array_detalle_producto  = $('#array_detalle_producto').val();
        var correlativo             = $('#correlativo').val();
        var grupo                   = $('#grupo').val();
        var opcion_id               = $('#opcion').val();
        var numero_mobil            = $('#numero_mobil').val();


        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){


            $.ajax({
                
                type    :   "POST",
                url     :   carpeta+"/ajax-modificar-muestra-producto-fila",
                data    :   {
                                _token                      : _token,
                                array_detalle_producto      : array_detalle_producto,
                                muestra                     : muestra,
                                producto_id                 : producto_id,
                                correlativo                 : correlativo,
                                grupo                       : grupo,
                                numero_mobil                : numero_mobil,
                                opcion_id                   : opcion_id,
                                fila                        : fila
                            },
                success: function (data) {

                    alertajax("Modificación exitosa");
                    $('.lista_pedidos_despacho').html(data);

                },
                error: function (data) {
                    error500(data);
                }
            });

        }
    });




    $(".despacho").on('change','#plantilla_valor', function() {
        var valor    = $(this).val();
        
        if(valor == '0'){
            $('.panel-muestra-pedido').css('display', 'block');
        }else{
            $('.panel-muestra-pedido').css('display', 'none');
        }
  
    });

    $(".despacho").on('click','.seltab', function() {
        var seltab   = $(this).attr('data_tab');
        $('#tabestado').val(seltab);
    });


    $(".despacho").on('click','.eliminar-producto-despacho', function() {

        event.preventDefault();
        var _token                  = $('#token').val();
        var array_detalle_producto  = $('#array_detalle_producto').val();
        var grupo                   = $('#grupo').val();
        var numero_mobil            = $('#numero_mobil').val();        
        var correlativo             = $('#correlativo').val();
        var fila                    = $(this).parents('.fila_pedido').attr('data_correlativo');
        var opcion_id               = $('#opcion').val();


        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-pedido-eliminar-fila",
            data    :   {
                            _token                      : _token,
                            array_detalle_producto      : array_detalle_producto,
                            grupo                       : grupo,
                            numero_mobil                : numero_mobil,
                            correlativo                 : correlativo,
                            fila                        : fila,
                            opcion_id                   : opcion_id,
                        },
            success: function (data) {
                alertajax("Eliminación exitosa");
                $('.lista_pedidos_despacho').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });
    });



    $(".despacho").on('click','.crearmobilindividuales', function() {

        event.preventDefault();
        var _token                  = $('#token').val();
        var array_detalle_producto  = $('#array_detalle_producto').val();
        var grupo                   = $('#grupo').val();
        var numero_mobil            = $('#numero_mobil').val();
        var correlativo             = $('#correlativo').val();
        var opcion_id               = $('#opcion').val();

        data_producto_pedido        = dataproductopedidosindivuduales();
        if(data_producto_pedido.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}


        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-pedido-crear-movil-individuales",
            data    :   {
                            _token                      : _token,
                            data_producto_pedido        : data_producto_pedido,
                            array_detalle_producto      : array_detalle_producto,
                            grupo                       : grupo,
                            numero_mobil                : numero_mobil,          
                            correlativo                 : correlativo,
                            opcion_id                   : opcion_id,
                        },
            success: function (data) {
                $('.lista_pedidos_despacho').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });

    });



    $(".despacho").on('click','.crearmobil', function() {

        event.preventDefault();
        var _token                  = $('#token').val();
        var array_detalle_producto  = $('#array_detalle_producto').val();
        var grupo                   = $('#grupo').val();
        var numero_mobil            = $('#numero_mobil').val();

        var correlativo             = $('#correlativo').val();
        var opcion_id               = $('#opcion').val();

        data_producto_pedido        = dataproductopedidos();
        if(data_producto_pedido.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-pedido-crear-movil",
            data    :   {
                            _token                      : _token,
                            data_producto_pedido        : data_producto_pedido,
                            array_detalle_producto      : array_detalle_producto,
                            grupo                       : grupo,
                            numero_mobil                : numero_mobil,
                            correlativo                 : correlativo,
                            opcion_id                   : opcion_id,
                        },
            success: function (data) {
                $('.lista_pedidos_despacho').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });

    });




    $(".despacho").on('click','#buscarordenpedidodespacho', function() {

        var _token              = $('#token').val();
        var cuenta_id           = $('#cuenta_id').select2().val();
        var opcion_id           = $('#opcion').val();

        /****** VALIDACIONES ********/
        /*if(cuenta_id.length<=0){
            alerterrorajax("Seleccione un cliente");
            return false;
        }*/

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-lista-orden-cen-producto",
            data    :   {
                            _token          : _token,
                            cuenta_id       : cuenta_id,
                            opcion_id       : opcion_id
                        },
            success: function (data) {
                cerrarcargando();
                $('.modal-detalledocumento-container').html(data);
                $('#modal-detalledocumento').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });

    });



    $(".despacho").on('click','.despacholocen', function() {

 
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-generar-nota-credito",
            data    :   {
                            _token                  : _token,
                            cuenta_id               : cuenta_id,
                            datasproductos          : datasproductos,
                            data_cod_orden_venta    : data_cod_orden_venta,
                            serie                   : serie,
                            motivo_id               : motivo_id,
                            informacionadicional    : informacionadicional,
                            idopcion                : idopcion,
                        },    
            success: function (data) {
                cerrarcargando();
                $('.modal-nota_credito_generada').html(data);
                $('#nota_credito_generada').niftyModal();

            },
            error: function (data) {
                error500(data);
            }
        });

    });


    $(".despacho").on('click','#agregarproductos', function() {

        event.preventDefault();

        var _token                  = $('#token').val();
        var grupo                   = $('#grupo').val();
        var numero_mobil            = $('#numero_mobil').val();
        var array_detalle_producto  = $('#array_detalle_producto').val();
        var correlativo             = $('#correlativo').val();
        var tabestado               = $('#tabestado').val();
        var opcion_id               = $('#opcion').val();

        if(tabestado == 'prod'){

            var cuenta_id_m             = $('#cuenta_id_m').val();
            $('input[type=search]').val('').change();
            $("#despacholop").DataTable().search("").draw();
            data_producto = dataenviarproducto();
            if(data_producto.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}
            $('#modal-detalledocumento').niftyModal('hide');

            $.ajax({

                type    :   "POST",
                url     :   carpeta+"/ajax-modal-agregar-producto-pedido",
                data    :   {
                                _token                  : _token,
                                data_producto           : data_producto,
                                grupo                   : grupo,
                                numero_mobil            : numero_mobil,
                                correlativo             : correlativo,
                                array_detalle_producto  : array_detalle_producto,
                                cuenta_id_m             : cuenta_id_m,
                                opcion_id               : opcion_id,
                            },    
                success: function (data) {
                    cerrarcargando();
                    $('.lista_pedidos_despacho').html(data);
                },
                error: function (data) {
                    error500(data);
                }
            });


        }else{

            var tipo_grupo          = $('#tipo_grupo').val();

            $('input[type=search]').val('').change();
            $("#despacholocen").DataTable().search("").draw();
            data_orden_cen = dataenviar();
            if(data_orden_cen.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}
            $('#modal-detalledocumento').niftyModal('hide');


            $.ajax({

                type    :   "POST",
                url     :   carpeta+"/ajax-modal-agregar-orden-cen-pedido",
                data    :   {
                                _token                  : _token,
                                data_orden_cen          : data_orden_cen,
                                grupo                   : grupo,
                                numero_mobil            : numero_mobil,   
                                correlativo             : correlativo,
                                tipo_grupo              : tipo_grupo,
                                array_detalle_producto  : array_detalle_producto,
                                opcion_id               : opcion_id,
                            },    
                success: function (data) {
                    cerrarcargando();
                    $('.lista_pedidos_despacho').html(data);
                },
                error: function (data) {
                    error500(data);
                }
            });
        }
    });
});

function validarrelleno(accion,name,estado,check,token){

    if (accion=='todas_asignar') {
        $(".table-pedidos-despachos tr").each(function(){
            nombre              =    $(this).find('.input_asignar_lp').attr('id');
            check_disableb      =    $(this).find('.check_disableb').length;
            if(nombre != 'todo_asignar' && check_disableb==0){
                $(this).find('.input_asignar_lp').prop("checked", estado);
                $(this).find('.input_asignar_gop').prop("checked", estado);
            }
        });
    }else{

        sw = 0;
        if(estado){
            $(".table-pedidos-despachos tr").each(function(){
                nombre = $(this).find('.input_asignar_lp').attr('id');
                check_disableb      =    $(this).find('.check_disableb').length;

                if(nombre != 'todo_asignar' && $(this).find('.input_asignar_lp').length > 0 && check_disableb==0){
                    if(!($(this).find('.input_asignar_lp').is(':checked'))){
                        sw = sw + 1;
                    }
                }
            });
            if(sw==1){
                $("#todo_asignar").prop("checked", estado);
            }
        }else{
            $("#todo_asignar").prop("checked", estado);
        }           
    }
}


function dataenviarproducto(){
    var data = [];
    $(".lista_tabla_prod tbody tr").each(function(){
        check                = $(this).find('.input_asignar_prod');
        producto_id          = $(this).attr('data_producto_id');
        cantidad_atender     = $(this).find('.precio_modal').val();
        

        if($(check).is(':checked')){
            data.push({
                producto_id     : producto_id,
                cantidad_atender    : cantidad_atender
            });
        }               

    });
    return data;
}


function dataenviar(){
    var data = [];
    $(".lista_tabla_oc tbody tr").each(function(){

        check           = $(this).find('.input_asignar_oc');
        ordencen_id     = $(this).attr('data_orden_id');

        if($(check).is(':checked')){
            data.push({
                ordencen_id     : ordencen_id
            });
        }               

    });
    return data;
}


function dataproductopedidos(){
    var data = [];
    $(".table-pedidos-despachos tbody tr").each(function(){

        //debugger;
        correlativo     = $(this).attr('data_correlativo');
        check           = $(this).find('.input_asignar_lp');

        if($(check).is(':checked')){
            data.push({
                correlativo     : correlativo
            });
        }               

    });
    return data;
}

function dataproductopedidosindivuduales(){
    var data = [];
    $(".table-pedidos-despachos tbody tr").each(function(){

        //debugger;
        correlativo     = $(this).attr('data_correlativo');
        check           = $(this).find('.input_asignar_lp');
        existe_grupales = $(this).find('.input_asignar_lp').hasClass('grupales');

        if($(check).is(':checked') && existe_grupales == false){
            data.push({
                correlativo     : correlativo
            });
        }               

    });
    return data;
}








