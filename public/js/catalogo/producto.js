
$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".configuracionproducto").on('click','.input_check_pe_ln', function() {
        producto_id  = $(this).attr('data_producto');
        if($(this).is(':checked')){
            ajax_ind_mobil(1,producto_id);
        }else{
            ajax_ind_mobil(0,producto_id);
        } 
    });


    function ajax_ind_mobil(ind_mobil,producto_id){

        var _token                  = $('#token').val();

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-guardar-producto-indmobil",
            data    :   {
                            _token                      : _token,
                            ind_mobil                   : ind_mobil,
                            producto_id                 : producto_id,
                        },
            success: function (data) {
                cerrarcargando();
                alertajax("Producto modificado exitosa");
            },
            error: function (data) {
                error500(data);
            }
        });
    }



    $(".configuracionproducto").on('keypress keyup keydown','.producto_edit', function(e) {
        var cabecera_tabla_tr           =   $(this).parents('.fila_producto');
        var nombre                      =   $(cabecera_tabla_tr).attr('data_edit_producto','1');
    });



    $(".configuracionproducto").on('click','.guardarcambios', function() {

        event.preventDefault();
        var _token                  = $('#token').val();

        $('input[type=search]').val('').change();
        $("#table1").DataTable().search("").draw();
        var data_producto           = dataproducto_edit();

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-guardar-configuracion-producto",
            data    :   {
                            _token                      : _token,
                            data_producto               : data_producto,
                        },
            success: function (data) {
                cerrarcargando();
                alertajax("Modificación exitosa");
                $('.ajax_lista_configuracion_producto').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });

    });


    function dataproducto_edit(){

        var data = [];

        $(".tablaproducto tbody tr").each(function(){

                var data_producto_id            = $(this).attr('data_producto_id');
                var can_bolsa_saco              = $(this).find('#can_bolsa_saco').val();                
                var can_saco_palet              = $(this).find('#can_saco_palet').val();
                var data_edit_producto          = $(this).attr('data_edit_producto');

                if(data_edit_producto == '1'){
                    data.push({
                        data_producto_id        : data_producto_id,
                        can_bolsa_saco          : can_bolsa_saco,
                        can_saco_palet          : can_saco_palet
                    });         
                }

        });
        return data;
    }



    $(".listaregladescuento").on('click','#buscarreglas', function() {

        var _token              = $('#token').val();
        var estado_id           = $('#estado_id').select2().val();
        var fechainicio         = $('#fechainicio').val();        
        var fechafin            = $('#fechafin').val(); 
        var idopcion            = $('#idopcion').val(); 


        /****** VALIDACIONES ********/
        if(estado_id.length<=0){
            alerterrorajax("Seleccione un estado");
            return false;
        }
        if(fechainicio == ''){
            alerterrorajax("Seleccione una fecha de inicio");
            return false;
        }

        if(fechafin == ''){
            alerterrorajax("Seleccione una fecha de fin");
            return false;
        } 

        abrircargando(); 
        $(".listajax").html("");

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-lista-reglas-descuento",
            data    :   {
                            _token          : _token,
                            estado_id       : estado_id,
                            fechainicio     : fechainicio,
                            fechafin        : fechafin,
                            idopcion        : idopcion,                            
                        },
            success: function (data) {
                cerrarcargando();
                $(".listajax").html(data);                
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






    $(".precioproducto").on('keypress','.updateprice', function(e) {

        var check           = '<i class="mdi mdi-check-circle"></i>';
        var _token          = $('#token').val();
        var puntero         = $(this);
        var precio          = $(this).val();
        var producto_id     = $(this).parents('tr').attr('data-id');
        var producto_pre    = $(this).parents('tr').attr('data-pref');

        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){

            $.ajax({
                
                type    :   "POST",
                url     :   carpeta+"/ajax-guardar-precio-producto",
                data    :   {
                                _token          : _token,
                                precio          : precio,
                                producto_id     : producto_id,
                                producto_pre    : producto_pre
                            },
                success: function (data) {
                    alertajax(data);
                    puntero.val("");
                    puntero.parent('.columna-warning').siblings('.columna-precio').html(check+' '+precio);
                    puntero.parent('.columna-warning').siblings('.columna-precio').removeClass("columna-default");  
                    puntero.parent('.columna-warning').siblings('.columna-precio').addClass("columna-success");
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

    $(".crearcupon").on('click','.generarcupon', function(e) {

        var _token          = $('#token').val();

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-generarcupon",
            data    :   {
                            _token          : _token
                        },
            success: function (data) {
                $('#cupon').val(data);                   
            },
            error: function (data) {
                if(data.status = 500){
                    var contenido = $(data.responseText);
                    alerterror505ajax($(contenido).find('.trace-message').html()); 
                    console.log($(contenido).find('.trace-message').html());     
                }
            }
        });

    });


    $(".crearcupon").on('click','.tipodescuento', function(e) {

        var value = $(this).val();
        if(value == 'POR'){
            $(".ssoles").css("display", "none");
            $(".sporcentaje").css("display", "table-cell");
        }else{
            $(".ssoles").css("display", "table-cell");
            $(".sporcentaje").css("display", "none");
        }
    });


    $(".crearcupon").on('click','.documentorb', function(e) {

        var value = $(this).val();
        if(value == 'OV'){
            $("#porcentaje").prop('disabled', true);
            $("input[name='tipodescuento'][value='IMP']").prop('checked', 'checked');
            $(".ssoles").css("display", "table-cell");
            $(".sporcentaje").css("display", "none");

            $(".ind_departamento").css("display", "block");
            $(".ind_cantidad_minima").css("display", "block");

            /*$(".aumentorb").prop('disabled', false);
            $(".ind_departamentob").prop('disabled', false);*/
        }else{

            $("#porcentaje").prop('disabled', false);

            $("#departamento").val("").change();
            $(".ind_departamento").css("display", "none");
            $("#cantidadminima").val("0");
            $(".ind_cantidad_minima").css("display", "none");
            /*$(".aumentorb").prop('disabled', true);
            $(".ind_departamentob").prop('disabled', true);
            $("input[name='ind_departamento'][value='0']").prop('checked', 'checked');
            $("input[name='descuentoaumento'][value='DS']").prop('checked', 'checked');
            $(".departamento_select").css( "display", "none" );*/
        }

    });



    $(".crearcupon").on('click','.ind_departamento', function(e) {

        var value = $(this).val();
        if(value == '1'){
            $(".departamento_select").css( "display", "block" );
        }else{
            $(".departamento_select").css( "display", "none" );
        }

    });


});