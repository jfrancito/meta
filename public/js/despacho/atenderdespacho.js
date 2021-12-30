
$(document).ready(function(){
	var carpeta = $("#carpeta").val();



    $(".despacho").on('click','.agregarmuestras', function() {

        event.preventDefault();
        var _token                      =   $('#token').val();

        var data                        =   [];
        var sw                          =   0;
        var msj                         =   '';
        var radiomobil                  =   $("input[name='rmobil']:checked").val();
        var ordendespacho_id            =   $('#ordendespacho_id').val();

        //no se puede asignar muestras
        var sw_mobil = 0;
        var mobil = '';
        $(".tabla-producto-muestra tbody tr").each(function(){
            mobil          = $(this).attr('data_mobil');
            if(mobil != ''){
                sw_mobil = 1;
            }
        });

        if(sw_mobil == 1){alerterrorajax('Las muestras ya fueron asignadas'); return false;}
        if(typeof(radiomobil)  === "undefined"){alerterrorajax('Seleccione por lo menos un mobil'); return false;}

        abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-asignar-muestras-mobil",
            data    :   {
                            _token     : _token,
                            radiomobil : radiomobil,
                            ordendespacho_id : ordendespacho_id,
                        },
            success: function (data) {
                
                $('.lista_orden_atender').html(data);
                actualizar_lista_muestra(ordendespacho_id,_token);
            },
            error: function (data) {
                error500(data);
            }
        });

    });

    function actualizar_lista_muestra(ordendespacho_id,_token){

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-actualizar-lista-muestra-mobil",
            data    :   {
                            _token                      : _token,
                            ordendespacho_id       : ordendespacho_id
                        },
            success: function (data) {
                cerrarcargando();
                alertajax("Se asigno las muestras exitosamente");
                $('.lista_muestra_ajax').html(data);

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
                url     :   carpeta+"/ajax-modal-agregar-producto-pedido-atender",
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
                    $('.lista_orden_atender').html(data);
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
        var data_detalle_orden_id   = $(this).parents('.fila_pedido_muestras').attr('data_detalle_orden_id');


        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            $('.lista_muestra_ajax').html("");
            $.ajax({
                
                type    :   "POST",
                url     :   carpeta+"/ajax-modificar-muestra-pedido-creado-fila-separado",
                data    :   {
                                _token                      : _token,
                                muestra                     : muestra,
                                data_detalle_orden_id       : data_detalle_orden_id
                            },
                success: function (data) {

                    alertajax("Modificación exitosa");
                    $('.lista_muestra_ajax').html(data);

                },
                error: function (data) {
                    error500(data);
                }
            });

        }
    });



    $(".despacho").on('change','#grupo_mobil_modal,#cuenta_id_modal', function() {

        var _token                      =   $('#token').val();
        var grupo_mobil_modal           =   $('#grupo_mobil_modal').val();
        var cuenta_id_modal             =   $('#cuenta_id_modal').val();        
        var ordendespacho_id            =   $('#ordendespacho_id').val();

        cargar_combo_orden_cen(ordendespacho_id,grupo_mobil_modal,cuenta_id_modal,_token);


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


    $(".despacho").on('click','.agregar_servicio', function() {

        var _token                      =   $('#token').val();
        var count_servicio              =   $('#count_servicio').val();
        var calcula_cantidad_peso       =   $('#calcula_cantidad_peso').val();

        abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-agregar-servicio",
            data    :   {
                            _token                  : _token,
                            count_servicio          : count_servicio,
                            calcula_cantidad_peso   : calcula_cantidad_peso, 
                        },
            success: function (data) {
                $(".ajax_lista_servicio").html(data);
                cerrarcargando();
            },
            error: function (data) {
                error500(data);
            }
        });


    });


    $(".despacho").on('click','.eliminar-servicio-despacho', function() {
        event.preventDefault();
        $(this).parents('.fila_servicio').remove();
    });


    $(".despacho").on('change','#cuenta_servicio', function() {

        var cabecera_tabla_tr           =   $(this).parents('.fila_servicio');
        var cuenta_id                   =   $(this).val();
        $(cabecera_tabla_tr).find('.contrato_cuenta').html(cuenta_id);

    });


    $(".despacho").on('change','.empresa_servicio_select', function() {

        var cabecera_tabla_tr           =   $(this).parents('.fila_servicio');
        var _token                      =   $('#token').val();
        var empresa_servicio_id         =   $(this).val();

        abrircargando(); 
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-combo-cuenta-servicio",
            data    :   {
                            _token                  : _token,
                            empresa_servicio_id     : empresa_servicio_id,
                        },
            success: function (data) {
                $(cabecera_tabla_tr).find('.ajax_cuenta_servicio').html(data);
                cerrarcargando();
            },
            error: function (data) {
                error500(data);
            }
        });
    });


    $(".despacho").on('click','.checkbox_asignar_ser', function() {

        var cabecera_tabla_tr           =   $(this).parents('.fila_servicio');
        var input                       =   $(this).siblings('.input_asignar_ser');
        var cantidad_servicio           =   $(cabecera_tabla_tr).find('.update_price_cantidad_servicio').val();
        var precio_servicio             =   $(cabecera_tabla_tr).find('.update_price_precio_servicio').val();
        var total_servicio              =   $(cabecera_tabla_tr).find('.update_price_total_servicio').val();
        var cantidad_servicio           =   parseFloat(cantidad_servicio.replace(",", ""));
        var precio_servicio             =   parseFloat(precio_servicio.replace(",", ""));
        var total_servicio              =   parseFloat(total_servicio.replace(",", ""));
        var click_check                 =   1;

        var check                       =   -1;
        var estado                      =   -1;
        if($(input).is(':checked')){
            check   = 0;
            estado  = false;
        }else{
            check   = 1;
            estado  = true;
        }

        calcular_totales_servicio(cabecera_tabla_tr,'cantidad',cantidad_servicio,precio_servicio,total_servicio,input,check,click_check);
    });



    $(".despacho").on('keypress keyup keydown','.update_price_total_servicio', function(e) {

        var cabecera_tabla_tr           =   $(this).parents('.fila_servicio');
        var _token                      =   $('#token').val();
        var evtType                     =   e.type;

        var cantidad_servicio           =   $(cabecera_tabla_tr).find('.update_price_cantidad_servicio').val();
        var precio_servicio             =   $(cabecera_tabla_tr).find('.update_price_precio_servicio').val();
        var total_servicio              =   $(cabecera_tabla_tr).find('.update_price_total_servicio').val();
        var cantidad_servicio           =   parseFloat(cantidad_servicio.replace(",", ""));
        var precio_servicio             =   parseFloat(precio_servicio.replace(",", ""));
        var total_servicio              =   parseFloat(total_servicio.replace(",", ""));
        var check                       =   $(cabecera_tabla_tr).find('.input_asignar_ser');
        var click_check                 =   0;

        if(cantidad_servicio>0){
            switch (evtType) {
                case 'keypress':
                    calcular_totales_servicio(cabecera_tabla_tr,'precio',cantidad_servicio,precio_servicio,total_servicio,check,0,click_check);
                    break;
                case 'keyup':
                    calcular_totales_servicio(cabecera_tabla_tr,'precio',cantidad_servicio,precio_servicio,total_servicio,check,0,click_check);
                    break;
                case 'keydown':
                    calcular_totales_servicio(cabecera_tabla_tr,'precio',cantidad_servicio,precio_servicio,total_servicio,check,0,click_check);
                    break;
                default:
                    break;
            }
        }else{
            $(cabecera_tabla_tr).find('.update_price_total_servicio').val('0.0000')
            alerterrorajax("Ingrese Cantidad");
            return false;
        }
    });


    $(".despacho").on('keypress keyup keydown','.update_price_cantidad_servicio,.update_price_precio_servicio', function(e) {

        var cabecera_tabla_tr           =   $(this).parents('.fila_servicio');
        var _token                      =   $('#token').val();
        var evtType                     =   e.type;
        var cantidad_servicio           =   $(cabecera_tabla_tr).find('.update_price_cantidad_servicio').val();
        var precio_servicio             =   $(cabecera_tabla_tr).find('.update_price_precio_servicio').val();
        var total_servicio              =   $(cabecera_tabla_tr).find('.update_price_total_servicio').val();
        var cantidad_servicio           =   parseFloat(cantidad_servicio.replace(",", ""));
        var precio_servicio             =   parseFloat(precio_servicio.replace(",", ""));
        var total_servicio              =   parseFloat(total_servicio.replace(",", ""));
        var check                       =   $(cabecera_tabla_tr).find('.input_asignar_ser');
        var click_check                 =   0;


        switch (evtType) {
            case 'keypress':
                calcular_totales_servicio(cabecera_tabla_tr,'total',cantidad_servicio,precio_servicio,total_servicio,check,0,click_check);
                break;
            case 'keyup':
                calcular_totales_servicio(cabecera_tabla_tr,'total',cantidad_servicio,precio_servicio,total_servicio,check,0,click_check);
                break;
            case 'keydown':
                calcular_totales_servicio(cabecera_tabla_tr,'total',cantidad_servicio,precio_servicio,total_servicio,check,0,click_check);
                break;
            default:
                break;
        }
    });


    function calcular_totales_servicio(cabecera_tabla_tr,atributo,cantidad_servicio,precio_servicio,total_servicio,check,ind_ckeck,click_check){

        if(atributo == 'total'){
            var calculo = cantidad_servicio*precio_servicio;
                $(cabecera_tabla_tr).find('.update_price_total_servicio').val(calculo);   
            total_servicio =  calculo;          
        }

        if(atributo == 'precio'){
            var calculo = total_servicio/cantidad_servicio;
            $(cabecera_tabla_tr).find('.update_price_precio_servicio').val(calculo);   
        }


        if(click_check == 0){
            if($(check).is(':checked')){
                costo           =   precio_servicio/1.18;
                subtotal        =   total_servicio/1.18;
                igv             =   total_servicio-subtotal;
            }else{

                costo           =   precio_servicio;
                subtotal        =   total_servicio;
                igv             =   0.00;
            }
        }else{
            if(ind_ckeck==1){
                costo           =   precio_servicio/1.18;
                subtotal        =   total_servicio/1.18;
                igv             =   total_servicio-subtotal;
            }else{

                costo           =   precio_servicio;
                subtotal        =   total_servicio;
                igv             =   0.00;
            }
        }



        $(cabecera_tabla_tr).find('.costo').html(costo.toFixed(4));
        $(cabecera_tabla_tr).find('.subtotal').html(subtotal.toFixed(4));
        $(cabecera_tabla_tr).find('.igv').html(igv.toFixed(4)); 

    }





    $(".despacho").on('click','.btn_guardar_transferencia_pt', function() {

        var glosa                                           =   $('#glosa').val();
        var origen_propietario                              =   $('#origen_propietario').val();
        var origen_servicio                                 =   $('#origen_servicio').val();
        var destino_propietario                             =   $('#destino_propietario').val();
        var destino_servicio                                =   $('#destino_servicio').val();
        var destino_centro                                  =   $('#destino_centro').val();
        var destino_almacen                                 =   $('#destino_almacen').val();        
        var origen_almacen                                  =   $('#origen_almacen').val();
        var h_array_productos_transferencia_pt              =   $('#h_array_productos_transferencia_pt').val();

        $('#h_glosa').val(glosa);
        $('#h_origen_propietario').val(origen_propietario);
        $('#h_origen_servicio').val(origen_servicio);
        $('#h_destino_propietario').val(destino_propietario);
        $('#h_destino_servicio').val(destino_servicio);
        $('#h_destino_centro').val(destino_centro);
        $('#h_destino_almacen').val(destino_almacen);
        $('#h_origen_almacen').val(origen_almacen); 
        $('#array_productos_transferencia_pt_h').val(h_array_productos_transferencia_pt); 

        var h_glosa                     =   $('#h_glosa').val();
        var h_origen_propietario        =   $('#h_origen_propietario').val();
        var h_origen_servicio           =   $('#h_origen_servicio').val();
        var h_destino_propietario       =   $('#h_destino_propietario').val();
        var h_destino_servicio          =   $('#h_destino_servicio').val();
        var h_destino_centro            =   $('#h_destino_centro').val();
        var h_destino_almacen           =   $('#h_destino_almacen').val();


        if(h_origen_propietario.length<=0){alerterrorajax("Seleccione un propietario origen");return false;}
        if(h_origen_servicio.length<=0){alerterrorajax("Seleccione un servicio origen");return false;}
        if(h_destino_propietario.length<=0){alerterrorajax("Seleccione un propietario destino");return false;}
        if(h_destino_servicio.length<=0){alerterrorajax("Seleccione un servicio destino");return false;}
        if(h_destino_centro.length<=0){alerterrorajax("Seleccione un centro destino");return false;}
        if(h_destino_almacen.length<=0){alerterrorajax("Seleccione un almacen destino");return false;}

        var h_array_productos_transferencia_pt  = $('#h_array_productos_transferencia_pt').val();
        if(h_array_productos_transferencia_pt.length<=2){alerterrorajax('No existe ningun material en la lista'); return false;}

        
        //servicio 
        var data_servicio_despacho = [];
        var sw   = 0;
        var msj  = '';

        $(".listaservicios tbody tr").each(function(){

            var cabecera_tabla_tr               =   $(this);
            var nombre_estado                   =   $(cabecera_tabla_tr).find('.nombre_estado').html();
            var producto_id                     =   $(cabecera_tabla_tr).find('.producto_id').html();
            var nombre_producto                 =   $(cabecera_tabla_tr).find('.nombre_porducto_id').html();
            var costo                           =   $(cabecera_tabla_tr).find('.costo').html();

            var catidad_servicio                =   $(cabecera_tabla_tr).find('.update_price_cantidad_servicio').val();
            var precio_servicio                 =   $(cabecera_tabla_tr).find('.update_price_precio_servicio').val();
            catidad_servicio                    =   parseFloat(catidad_servicio.replace(",", ""));
            precio_servicio                     =   parseFloat(precio_servicio.replace(",", ""));
            var input                           =   $(cabecera_tabla_tr).find('.input_asignar_ser');

            if($(input).is(':checked')){
                ind_igv   = 1;
            }else{
                ind_igv   = 0;
            }

            var subtotal                        =   $(cabecera_tabla_tr).find('.subtotal').html();
            var igv                             =   $(cabecera_tabla_tr).find('.igv').html();

            var total_servicio                  =   $(cabecera_tabla_tr).find('.update_price_total_servicio').val();
            total_servicio                      =   parseFloat(total_servicio.replace(",", ""));

            var empresa_servicio                =   $(cabecera_tabla_tr).find('.empresa_servicio_select').val();
            var cuenta_servicio                 =   $(cabecera_tabla_tr).find('#cuenta_servicio').val();
            var contrato_cuenta                 =   $(cabecera_tabla_tr).find('.contrato_cuenta').html();
            var tipo_documento_id               =   $(cabecera_tabla_tr).find('.tipo_documento_id').html();




            if(catidad_servicio<=0){
                msj = 'Servicio ('+nombre_producto+') ingrese cantidad';
                sw=1;
            }
            if(precio_servicio<=0){
                msj = 'Servicio ('+nombre_producto+') ingrese precio';
                sw=1;
            }
            if(total_servicio <=0){
                msj = 'Servicio ('+nombre_producto+') ingrese total';
                sw=1;
            }

            if(empresa_servicio == 'VACIO'){
                msj = 'Servicio ('+nombre_producto+') seleccione empresa servicio';
                sw=1;
            }

            if(cuenta_servicio == ''  || cuenta_servicio === null){
                msj = 'Servicio ('+nombre_producto+') seleccione cuenta';
                sw=1;
            }

            data_servicio_despacho.push({
                nombre_estado                   : nombre_estado,
                producto_id                     : producto_id,
                nombre_producto                 : nombre_producto,
                costo                           : costo,
                catidad_servicio                : catidad_servicio,
                precio_servicio                 : precio_servicio,
                ind_igv                         : ind_igv,
                subtotal                        : subtotal,
                igv                             : igv,
                total_servicio                  : total_servicio,
                empresa_servicio                : empresa_servicio,
                cuenta_servicio                 : cuenta_servicio,
                contrato_cuenta                 : contrato_cuenta,
                tipo_documento_id               : tipo_documento_id
            });
           
        });
        if(sw==1){alerterrorajax(msj); return false;}

        $('#array_servicio_transferencia_pt_h').val(JSON.stringify(data_servicio_despacho)); 
        /*
        console.log(data_servicio_despacho);
        console.log($('#array_servicio_transferencia_pt_h').val());
        */
        abrircargando();
        return true;

    });



    $(".despacho").on('click','.transferenciapt', function() {

        event.preventDefault();
        var _token                      =   $('#token').val();



        var data                        =   [];
        var sw                          =   0;
        var msj                         =   '';
        var radiomobil                  =   $("input[name='rmobil']:checked").val();

        if(typeof(radiomobil)  === "undefined"){alerterrorajax('Seleccione por lo menos un mobil'); return false;}


        $(".table-pedidos-despachos tbody .fila_pedido").each(function(){

            nombre                  = $(this).find('.input_asignar_lp').attr('id');
            almacen_combo_id        = '';

            if(nombre != 'todo_asignar'){

                var cabecera_tabla_tr               =   $(this);
                var data_detalle_orden_despacho     =   $(cabecera_tabla_tr).attr('data_detalle_orden_despacho');
                var mobil_grupo                     =   $(cabecera_tabla_tr).attr('mobil_grupo');

                var data_producto                   =   $(cabecera_tabla_tr).attr('data_producto');
                var nombre_producto                 =   $(cabecera_tabla_tr).attr('nombre_producto');
                var unidad_medida                   =   $(cabecera_tabla_tr).attr('unidad_medida');
                var stock_neto                      =   $(cabecera_tabla_tr).find('.stock_neto').html();
                var almacen_id                      =   $(cabecera_tabla_tr).find('#almacen_id').val();
                var almacen_nombre                  =   $(cabecera_tabla_tr).find('#almacen_id option:selected').text();
                var array_lote_id                   =   $(cabecera_tabla_tr).find('#lote_id').val();
                var costo                           =   $(cabecera_tabla_tr).find('.costo').html();            
                var cantidad_atender                =   $(cabecera_tabla_tr).find('.updatepriceatender').val();
                var stock_neto                      =   parseFloat(stock_neto.replace(",", ""));
                var costo                           =   parseFloat(costo.replace(",", ""));
                var cantidad_atender                =   parseFloat(cantidad_atender.replace(",", ""));
                var check                           =   $(this).find('.input_asignar_lp');
                var total                           =   cantidad_atender * costo;
                almacen_combo_id                    =   almacen_id;

                if(mobil_grupo==radiomobil){

                    if(cantidad_atender<=0){
                        msj = 'Cantidad Atender del '+nombre_producto+' debe ser mayor a cero'; 
                        sw=1;
                    }
                    if(stock_neto<=0){
                        msj = 'Stock Neto del '+nombre_producto+' debe ser mayor a cero'; 
                        sw=1;
                    }
                    if(stock_neto < cantidad_atender){
                        msj = nombre_producto+' Cantidad Atender debe ser menor al Stock Neto'; 
                        sw=1;
                    }

                    data.push({
                        data_detalle_orden_despacho     : data_detalle_orden_despacho,
                        data_producto                   : data_producto,
                        nombre_producto                 : nombre_producto,
                        unidad_medida                   : unidad_medida,
                        almacen_id                      : almacen_id,
                        almacen_nombre                  : almacen_nombre,
                        array_lote_id                   : array_lote_id,
                        cantidad_atender                : cantidad_atender,
                        costo                           : costo,
                        total                           : total
                    });
                }
            }

        });


        if(sw==1){alerterrorajax(msj); return false;}
        abrircargando();

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-lista-productos-transferencia-pt",
            data    :   {
                            _token                                  : _token,
                            data                                    : data,
                        },
            success: function (data) {

                activaTab('tranferenciapt');
                $('.ajax_lista_producto_tp').html(data);
                actualizar_combo_almacen_origen(_token,carpeta,almacen_combo_id);
                cerrarcargando();
            },
            error: function (data) {
                error500(data);
            }
        });

    });


    $(".despacho").on('change','#destino_centro', function() {

        var _token                      =   $('#token').val();
        var destino_centro_id           =   $(this).val();

        abrircargando(); 
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-combo-almacen-destino",
            data    :   {
                            _token                  : _token,
                            destino_centro_id       : destino_centro_id,
                        },
            success: function (data) {
                $('.ajax_almacen_destino').html(data);
                cerrarcargando();
            },
            error: function (data) {
                error500(data);
            }
        });
    });


    $(".despacho").on('keypress keyup keydown','.updatepriceatender', function(e) {

        var cabecera_tabla_tr           =  $(this).parents('.fila_pedido');
        var _token                      =   $('#token').val();
        var evtType = e.type;
        var cantidad_atender  =    $(cabecera_tabla_tr).find('.updatepriceatender').val();
        $(cabecera_tabla_tr).find('.td_cantidad_atender').html(cantidad_atender);

        switch (evtType) {
            case 'keypress':
                limpiar_lote_stock(cabecera_tabla_tr,_token,carpeta);
                pintar_input_cantidad_atender(cabecera_tabla_tr);

                /*var _token                          = $('#token').val();
                var catidad_atender                 = $(this).val();
                catidad_atender                     =   parseFloat(catidad_atender.replace(",", ""));
                var detalle_orden_despacho_id       = $(this).parents('.fila_pedido').attr('data_detalle_orden_despacho');
                var code = (e.keyCode ? e.keyCode : e.which);
                
                if(code==13){

                    $.ajax({

                        type    :   "POST",
                        url     :   carpeta+"/ajax-modificar-cantidad-atender-producto-id",
                        data    :   {
                                        _token                      : _token,
                                        catidad_atender             : catidad_atender,
                                        detalle_orden_despacho_id   : detalle_orden_despacho_id
                                    },
                        success: function (data) {

                            alertajax("Modificación exitosa");
                            $(cabecera_tabla_tr).find('.td_cantidad_atender').html(data);

                        },
                        error: function (data) {
                            error500(data);
                        }
                    });

                }*/

                break;
            case 'keyup':
                limpiar_lote_stock(cabecera_tabla_tr,_token,carpeta);
                pintar_input_cantidad_atender(cabecera_tabla_tr);
                break;
            case 'keydown':
                limpiar_lote_stock(cabecera_tabla_tr,_token,carpeta);
                pintar_input_cantidad_atender(cabecera_tabla_tr);
                break;
            default:
                break;
        }
    });


    $(".despacho").on('change','.select_tabla_almacen_id', function() {

        var cabecera_tabla_tr           =  $(this).parents('.fila_pedido');
        var almacen_id                  =  $(this).val();
        var producto_id                 =  $(this).parents('.fila_pedido').attr('data_producto');
        var cantidad_atender            =  $(this).parents('.fila_pedido').find('.td_cantidad_atender').html();
        var cantidad_atender            =    cantidad_atender.replace(",", "");

        var _token                      = $('#token').val();

        abrircargando(); 
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-combo-lote-almacen",
            data    :   {
                            _token                  : _token,
                            almacen_id              : almacen_id,
                            producto_id             : producto_id,
                            cantidad_atender        : cantidad_atender,
                        },
            success: function (data) {

                $(cabecera_tabla_tr).find('.ajax_combo_lote').html(data);
                var lote_id     =       $(cabecera_tabla_tr).find('.ajax_combo_lote').find('#lote_id').val();
                actualizar_stock(lote_id,almacen_id,producto_id,_token,carpeta,cabecera_tabla_tr);
                
            },
            error: function (data) {
                error500(data);
            }
        });
    });





    $(".despacho").on('change','.select_tabla_lote_id', function() {

        var cabecera_tabla_tr           =  $(this).parents('.fila_pedido');
        var array_lote_id               =  $(this).val();

        var almacen_id                  =  $(cabecera_tabla_tr).find('#almacen_id').val();
        var producto_id                 =  $(this).parents('.fila_pedido').attr('data_producto');
        var _token                      = $('#token').val();

        abrircargando();

        actualizar_stock(array_lote_id,almacen_id,producto_id,_token,carpeta,cabecera_tabla_tr);

    });




    $(".despacho").on('click','#modificarfechadeentrega', function() {

        event.preventDefault();
        var _token                  = $('#token').val();
        //var data_producto_despacho      = dataproductoatender_fecha_entrega();
        var fechadeentrega          = $('#fechadecarga').val(); 
        var ordendespacho_id        = $('#ordendespacho_id').val();
        var radiomobil              = $("input[name='rmobil']:checked").val();

        if(fechadeentrega == ''){
            alerterrorajax("Seleccione una fecha de entrega");
            return false;
        }

        var data_producto_despacho = [];
        $(".table-pedidos-despachos tbody tr").each(function(){
            //debugger;
                nombre                  = $(this).find('.input_asignar_lp').attr('id');
                if(nombre != 'todo_asignar'){

                    var cabecera_tabla_tr               =   $(this);
                    data_detalle_orden_despacho         =   $(this).attr('data_detalle_orden_despacho');
                    var mobil_grupo                     =   $(cabecera_tabla_tr).attr('mobil_grupo');
                    if(mobil_grupo==radiomobil){
                        data_producto_despacho.push({
                            data_detalle_orden_despacho     : data_detalle_orden_despacho
                        });
                    }
                }            
        });


        abrircargando();

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-pedido-atender-modificar-fecha-de-entrega",
            data    :   {
                            _token                      : _token,
                            data_producto_despacho      : data_producto_despacho,
                            fechadeentrega              : fechadeentrega,
                            ordendespacho_id            : ordendespacho_id
                        },
            success: function (data) {
                cerrarcargando();
                alertajax("Modificación exitosa");
                $('.lista_orden_atender').html(data);
                $('#modal-carga').niftyModal('hide');

            },
            error: function (data) {
                error500(data);
            }
        });


    });

    $(".despacho").on('click','#modificarorigen', function() {

        event.preventDefault();
        var _token                      =   $('#token').val();
        var data_producto_despacho      =   [];
        var sw   = 0;
        var msj                         =   '';
        var centro_origen_id            =   '';
        var radiomobil                  =   $("input[name='rmobil']:checked").val();

        $(".table-pedidos-despachos tbody .fila_pedido").each(function(){

            nombre                  = $(this).find('.input_asignar_lp').attr('id');
            almacen_combo_id        = '';
            if(nombre != 'todo_asignar'){

                var cabecera_tabla_tr               =   $(this);
                var data_detalle_orden_despacho     =   $(cabecera_tabla_tr).attr('data_detalle_orden_despacho');
                var mobil_grupo                     =   $(cabecera_tabla_tr).attr('mobil_grupo');
                var guia_remision_id                =   $(cabecera_tabla_tr).attr('guia_remision_id');
                centro_origen_id                   =   $(cabecera_tabla_tr).attr('centro_origen_id');

                if(mobil_grupo==radiomobil){
                    data_producto_despacho.push({
                        data_detalle_orden_despacho     : data_detalle_orden_despacho
                    });
                }
            }
        });

        var centro_origen_id            = $('#centro_origen_id').val(); 
        var ordendespacho_id            = $('#ordendespacho_id').val();

        if(centro_origen_id == ''){
            alerterrorajax("Seleccione un Origen");
            return false;
        }

        abrircargando();

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-pedido-atender-modificar-origen",
            data    :   {
                            _token                      : _token,
                            data_producto_despacho      : data_producto_despacho,
                            centro_origen_id            : centro_origen_id,
                            ordendespacho_id            : ordendespacho_id
                        },
            success: function (data) {
                cerrarcargando();
                alertajax("Modificación exitosa");
                $('.lista_orden_atender').html(data);
                $('#modal-cambiar-origen').niftyModal('hide');
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

        $('#modal-carga').niftyModal();

    });


    $(".despacho").on('click','.cambiarorigen', function() {

        event.preventDefault();
        var radiomobil                  =   $("input[name='rmobil']:checked").val();
        if(typeof(radiomobil)  === "undefined"){alerterrorajax('Seleccione por lo menos un mobil'); return false;}

        //servicio 
        var data = [];
        var sw   = 0;
        var msj  = '';
        var centro_origen_id = '';

        $(".table-pedidos-despachos tbody .fila_pedido").each(function(){

            nombre                  = $(this).find('.input_asignar_lp').attr('id');
            almacen_combo_id        = '';
            if(nombre != 'todo_asignar'){

                var cabecera_tabla_tr               =   $(this);
                var data_detalle_orden_despacho     =   $(cabecera_tabla_tr).attr('data_detalle_orden_despacho');
                var mobil_grupo                     =   $(cabecera_tabla_tr).attr('mobil_grupo');
                var guia_remision_id                =   $(cabecera_tabla_tr).attr('guia_remision_id');


                if(mobil_grupo==radiomobil){

                    if(guia_remision_id != ''){
                        msj = 'Mobil ('+mobil_grupo+') ya tienen una guia de remisión'; 
                        sw=1;
                    }

                    centro_origen_id                   =   $(cabecera_tabla_tr).attr('centro_origen_id');
                    data.push({
                        data_detalle_orden_despacho     : data_detalle_orden_despacho
                    });
                }
            }
        });

        if(sw==1){alerterrorajax(msj); return false;}

        //$('#centro_origen_id').val(centro_origen_id).trigger('change');
        $('#centro_origen_id').val(centro_origen_id).change();
        $('#modal-cambiar-origen').niftyModal();

    });


    $(".despacho").on('click','.guardarcambios', function() {

        event.preventDefault();
        data_producto_despacho          = dataproductoatender_edit();
        var ordendespacho_id            = $('#ordendespacho_id').val();        
        var _token                      = $('#token').val();
        if(data_producto_despacho.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}


        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-pedido-atender-modificar-cantidad-atender",
            data    :   {
                            _token                      : _token,
                            data_producto_despacho      : data_producto_despacho,
                            ordendespacho_id            : ordendespacho_id
                        },
            success: function (data) {
                cerrarcargando();
                alertajax("Modificación exitosa");
                $('.lista_orden_atender').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });

    });








    $(".despacho").on('click','.agregarproductoatender', function() {

        var _token                      = $('#token').val();
        var ordendespacho_id            = $('#ordendespacho_id').val();

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-lista-orden-atender-producto",
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


    $(".despacho").on('click','.rechazarproductoatender', function() {

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
            url     :   carpeta+"/ajax-rechazar-producto",
            data    :   {
                            _token                      : _token,
                            ordendespacho_id            : ordendespacho_id,
                            data_productos_rechazar    : data,
                        },
            success: function (data) {
                cerrarcargando();
                alertajax("Producto agregado exitosa");
                $('.lista_orden_atender').html(data);
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



});

function limpiar_lote_stock(cabecera_tabla_tr,_token,carpeta){
    $(cabecera_tabla_tr).find("#lote_id").multiselect("deselectAll", false).multiselect("refresh");

    $(cabecera_tabla_tr).find(".stock_neto").html('0.00');
    $(cabecera_tabla_tr).find(".stock_fisico").html('0.00');
    $(cabecera_tabla_tr).find(".costo").html('0.00');


}


function validarrelleno(accion,name,estado,check,token){


    if (accion=='todas_asignar') {
        $(".table-pedidos-despachos tr").each(function(){
            nombre              =    $(this).find('.input_asignar_lp').attr('id');
            check_disableb      =    $(this).find('.check_disableb').length;
            if(nombre != 'todo_asignar' && check_disableb==0){
                $(this).find('.input_asignar_lp').prop("checked", estado);
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



function dataproductoatender_fecha_entrega(){
    var data = [];
    $(".table-pedidos-despachos tbody tr").each(function(){
        //debugger;
        data_detalle_orden_despacho     = $(this).attr('data_detalle_orden_despacho');
        check                           = $(this).find('.input_asignar_lp');
        if($(check).is(':checked')){
            data.push({
                data_detalle_orden_despacho     : data_detalle_orden_despacho
            });
        }              
    });
    return data;
}

function dataproductoatender_edit(){
    var data = [];
    var serie_registro = '';
    var nro_registro = '';


    $(".table-pedidos-despachos tbody tr").each(function(){
        //debugger;
        nombre              =    $(this).find('.input_asignar_lp').attr('id');

        if(nombre != 'todo_asignar'){

            data_detalle_orden_despacho     = $(this).attr('data_detalle_orden_despacho');
            check                           = $(this).find('.input_asignar_lp');
            var cantidad_atender            = $(this).find('.updatepriceatender').val();
            var serie                       = $(this).find('#serie_guia').val();
            var nro_documento               = $(this).find('.nro_documento').val();
            var producto_id                 = $(this).attr('data_producto');

            if (serie !== undefined) {
                serie_registro  = serie;
                nro_registro    = nro_documento;
            }

            if (cantidad_atender !== undefined) {

                cantidad_atender                = cantidad_atender.replace(",", "");
                data.push({
                    data_detalle_orden_despacho     : data_detalle_orden_despacho,
                    cantidad_atender                : cantidad_atender,
                    serie                           : serie_registro,
                    nro_documento                   : nro_registro,
                    producto_id                     : producto_id,
                });

            }



        }    
    });
    return data;
}




function dataenviarproducto(){
    var data = [];
    $(".lista_tabla_prod tbody tr").each(function(){

        check                = $(this).find('.input_asignar_prod');
        producto_id          = $(this).attr('data_producto_id');
        cantidad_atender     = $(this).find('.precio_modal').val();

        if($(check).is(':checked')){
            data.push({
                producto_id         : producto_id,
                cantidad_atender    : cantidad_atender
            });
        }               

    });
    return data;
}

function actualizar_combo_almacen_origen(_token,carpeta,almacen_combo_id){


    $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-combo-almacen-origen",
            data    :   {
                            _token                  : _token,
                            almacen_combo_id        : almacen_combo_id
                        },
            success: function (data) {
                $('.ajax_almacen_origen').html(data);
        },
        error: function (data) {
            error500(data);
        }
    });

}


function actualizar_stock(array_lote_id,almacen_id,producto_id,_token,carpeta,cabecera_tabla_tr){

    $.ajax({
        type    :   "POST",
        url     :   carpeta+"/ajax-calcular-stock-almacen-lote",
        data    :   {
                        _token                  : _token,
                        array_lote_id           : array_lote_id,
                        almacen_id              : almacen_id,
                        producto_id             : producto_id
                    },
        success: function (data) {
            $(cabecera_tabla_tr).find('.ajax_stock_almacen_lote').html(data);
            pintar_input_cantidad_atender(cabecera_tabla_tr);
            cerrarcargando();
        },
        error: function (data) {
            error500(data);
        }
    });

}

function pintar_input_cantidad_atender(cabecera_tabla_tr){


    var stock_neto        =    $(cabecera_tabla_tr).find('.stock_neto').html();
    var cantidad_atender  =    $(cabecera_tabla_tr).find('.updatepriceatender').val();
    var stock_neto        =    stock_neto.replace(",", "");
    var cantidad_atender  =    cantidad_atender.replace(",", "");

    $(cabecera_tabla_tr).find('.td_cantidad_atender').removeClass("background_rojo");
    $(cabecera_tabla_tr).find('.updatepriceatender').removeClass("color_rojo");
    if(parseFloat(stock_neto) < parseFloat(cantidad_atender)){
        $(cabecera_tabla_tr).find('.updatepriceatender').addClass("color_rojo");
        $(cabecera_tabla_tr).find('.td_cantidad_atender').addClass("background_rojo");
    }
}



function activaTab(tab){
    $('.nav-tabs a[href="#' + tab + '"]').tab('show');
}


