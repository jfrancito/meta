
$(document).ready(function(){

	var carpeta = $("#carpeta").val();


    $(".notacredito").on('change','#motivo_id', function() {

        var motivo_id    =   $('#motivo_id').val();

        if(motivo_id == 'MEM0000000000004'){
            asignar_cantidades_original();
        }else{
            liberar_cantidades_original();
        }

    });



    $(".notacredito").on('click','#crearnotacredito_osiris', function() {

        var validacion_cantidad_productos    = $('#validacion_cantidad_productos').val();
        abrircargando();
        if(validacion_cantidad_productos=='1'){
            alerterrorajax("La generación de notas de creditos no se completo");
            cerrarcargando();
            return false;
        }else{
            return true;
        }

    });

    $(".notacredito").on('click','.filacondetalledocumento', function() {

        var _token                  = $('#token').val();
        var data_array_productos    = $(this).attr('data_array_productos');
        var data_serie_correlativo  = $(this).attr('data_serie_correlativo');
        var data_documento_id       = $(this).attr('data_documento_id');

        abrircargando();

        $.ajax({           
            type    :   "POST",
            url     :   carpeta+"/ajax-detalle-producto-boleta-nc",
            data    :   {
                            _token                      : _token,
                            data_array_productos        : data_array_productos,
                            data_serie_correlativo      : data_serie_correlativo,
                            data_documento_id           : data_documento_id,
                        },
            success: function (data) {
                cerrarcargando();
                $('.lista_nc_detalle_documento').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });

    });




    $(".notacredito").on('click','.generar_nota_credito', function() {

        var _token                  =   $('#token').val();

        var cantidades              =   validar_cantidades();
        var precios                 =   validar_precios();

        var flias                   =   validar_filas();
        var cuenta_id               =   $('#cuenta_id_m').val();
        var data_cod_orden_venta    =   $('#data_cod_orden_venta_m').val();
        var serie                   =   $('#serie').val();
        var motivo_id               =   $('#motivo_id').val();
        var informacionadicional    =   $('#informacionadicional').val();
        var tiene_asociadas         =   $('#tiene_asociadas').val();
        var idopcion                =   $('#opcion').val();

        if(serie.length<=0){alerterrorajax("Seleccione una serie para registrar");return false;}
        if(motivo_id.length<=0){alerterrorajax("Seleccione un motivo para registrar");return false;}


        if(precios==1 ){
            alerterrorajax("Hay precios con cantidades 0");
            return false;
        }

        if(cantidades==1 ){
            alerterrorajax("Hay productos con cantidades 0");
            return false;
        }

        if(flias==0){
            alerterrorajax("No existe productos para generar nota de creditos");
            return false;
        }

        if(motivo_id == 'MEM0000000000004' && tiene_asociadas==1){
            alerterrorajax("Motivo(Devolución Total) - Boletas ya tienen nota de credito asociada");
            return false;
        }


        var data                = dataenviar();
        var datasproductos      = JSON.stringify(data);
        abrircargando();


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





    $(".notacredito").on('click','.mdi-close-fila-tabla', function() {
        $(this).parents('.fila_producto').remove();
        calcular_totales();
    });

    
    $(".notacredito").on('click','.btn-detalle-producto', function() {

        var _token              = $('#token').val();
        var documento_id        = $(this).attr('data-documento-id');
        var cuenta_id           = $('#cuenta_id_m').val();

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-producto",
            data    :   {
                            _token                  : _token,
                            documento_id            : documento_id,
                            cuenta_id               : cuenta_id
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


    $(".notacredito").on('keyup','.updatecantidad', function(e) {

        calcular_sub_totales();
        calcular_totales();

    });


    $(".notacredito").on('keyup','.updateprecio', function(e) {
        calcular_sub_totales();
        calcular_totales();
    });



    $(".notacredito").on('click','#buscarordenventa', function() {

        var _token              = $('#token').val();
        var cuenta_id           = $('#cuenta_id').select2().val();

        /****** VALIDACIONES ********/
        if(cuenta_id.length<=0){
            alerterrorajax("Seleccione un cliente");
            return false;
        }

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-lista-orden-venta-nc",
            data    :   {
                            _token          : _token,
                            cuenta_id       : cuenta_id
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


    $(".notacredito").on('click','#buscarordenventa_modal', function() {

        var _token              = $('#token').val();
        var cuenta_id           = $('#cuenta_id_m_ov').val();
        var fechainicio         = $('#finicio').val();        
        var fechafin            = $('#ffin').val(); 

        if(fechainicio == ''){
            alerterrorajax("Seleccione una fecha de inicio");
            return false;
        }

        if(fechafin == ''){
            alerterrorajax("Seleccione una fecha de fin");
            return false;
        } 

        abrircargando();
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-lista-orden-venta-fechas",
            data    :   {
                            _token          : _token,
                            cuenta_id       : cuenta_id,
                            fechainicio     : fechainicio,
                            fechafin        : fechafin,
                        },
            success: function (data) {
                cerrarcargando();
                $('.ajax_lista_orden_venta').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });


    });


    $(".notacredito").on('click','.filaconordenventa', function() {

        var _token                  = $('#token').val();
        var cuenta_id               = $('#cuenta_id_m_ov').val();
        var data_cod_orden_venta    = $(this).attr('data_cod_orden_venta');

        abrircargando();
        $('#modal-detalledocumento').niftyModal('hide');

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-orden-venta-boletas",
            data    :   {
                            _token                      : _token,
                            cuenta_id                   : cuenta_id,
                            data_cod_orden_venta        : data_cod_orden_venta,
                        },
            success: function (data) {
                cerrarcargando();
                $('.listajax').html(data);
            },
            error: function (data) {
                error500(data);
            }
        });

    });

});

function liberar_cantidades_original(){
    var cantidad = 1.0000;
    $("#tabladetalleproductonc tbody tr").each(function(){

        //var cantidad_original = $(this).find('.columna-cantidad').attr('data_cantidad_original');
        $(this).find('.columna-cantidad').find('#cantidad').val(0);
        $(this).find('.columna-cantidad').find('#cantidad').attr("disabled", false);

    });
}


function asignar_cantidades_original(){
    $("#tabladetalleproductonc tbody tr").each(function(){
        var cantidad_original = $(this).find('.columna-cantidad').attr('data_cantidad_original');
        $(this).find('.columna-cantidad').find('#cantidad').val(cantidad_original);
        $(this).find('.columna-cantidad').find('#cantidad').attr("disabled", true);
    });
}

function dataenviar(){

    var data = [];
    $(".listatabladetalle tr").each(function(){

        producto_id     = $(this).attr('data_producto_id');
        cantidad        = $(this).find('.columna-cantidad').find('#cantidad').val();
        precio          = $(this).find('.columna-precio').find('#precio').val();

        data.push({
            producto_id     : producto_id,
            cantidad        : cantidad,
            precio          : precio,
        });   

    });
    return data;

}


function calcular_sub_totales(){

    $("#tabladetalleproductonc .listatabladetalle tr").each(function(){
        var cantidad    = parseFloat($(this).find('.columna-cantidad').find('#cantidad').val().trim());
        var precio      = parseFloat($(this).find('.columna-precio').find('#precio').val().trim());

        if(isNaN(cantidad)){cantidad = 0;}
        if(isNaN(precio)){precio = 0;}

        var importe     = (cantidad*precio).toFixed(4);
        $(this).find('.columna-importe').html(importe);
    });

}




function validar_cantidades(){
    var sw = 0;
    $("#tabladetalleproductonc .listatabladetalle tr").each(function(){
        var cantidad    = parseFloat($(this).find('.columna-cantidad').find('#cantidad').val());
        if( cantidad <= 0 || isNaN(cantidad)) {
            sw = 1;
        }
    });
    return sw;
}

function validar_precios(){
    var sw = 0;
    $("#tabladetalleproductonc .listatabladetalle tr").each(function(){
        var precio    = parseFloat($(this).find('.columna-precio').find('#precio').val());
        if( precio <= 0 || isNaN(precio)) {
            sw = 1;
        }
    });
    return sw;
}



function validar_filas(){
    var sw = 0;
    $("#tabladetalleproductonc tbody tr").each(function(){
            sw = 1;
    });
    return sw;
}


function calcular_totales(){
        var importet = 0.0000;
        $("#tabladetalleproductonc tbody tr").each(function(){
            if($(this).find('.columna-importe').length) {
                importe    =  $(this).find('.columna-importe').html();
                importet   =  importet + parseFloat(importe.trim());
            }
        });
        $('.total_nota_credito').html(importet.toFixed(4))
}









