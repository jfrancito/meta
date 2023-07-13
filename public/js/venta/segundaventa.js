$(document).ready(function(){

    var carpeta = $("#carpeta").val();




    $(".segundaventa").on('click','.selectcheck', function() {

        var data_cantidad       =   $(this).attr('data_cantidad');
        var check               =   $(this).val();
        var contitem            =   $('.contitem').html();

        var total               =   contadortotal();

        $('.contitem').html(total);

    });


    $(".segundaventa").on('click','.buscarinventario', function() {

        event.preventDefault();
        var anio        =   $('#anio').val();
        var idopcion    =   $('#idopcion').val();
        var _token      =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}

        data            =   {
                                _token      : _token,
                                anio        : anio,
                                idopcion    : idopcion,
                            };
        ajax_normal(data,"/ajax-segunda-ventas");

    });


    $(".segundaventa").on('dblclick','.dobleclickpc', function(e) {

        var _token                  =   $('#token').val();
        var data_producto      		=   $(this).attr('data_producto');
        var data_periodo      		=   $(this).attr('data_periodo');
        var data_filtro      		=   $(this).attr('data_filtro'); 
        var data_anio      			=   $(this).attr('data_anio');        
        var data_monto      		=   $(this).attr('data_monto');
        var data_asociada      		=   $(this).attr('data_asociada');

        var idopcion                =   $('#idopcion').val();
        var anio                    =   $('#anio').val();

        data                        =   {
                                            _token                  : _token,
                                            data_producto      : data_producto,
                                            data_periodo      : data_periodo,
                                            data_filtro      : data_filtro,
                                            data_anio      : data_anio,
                                            data_monto      : data_monto,
                                            data_asociada      : data_asociada,
                                            idopcion                : idopcion,
                                            anio                    : anio,
                                        };
        ajax_modal(data,"/ajax-modal-configuracion-segunda-venta",
                  "modal-configuracion-segunda-venta","modal-configuracion-segunda-venta-container");

    });
 

    $(".segundaventa").on('click','.guardarasociar', function() {

        event.preventDefault();
        var cantidad_descuento 	=   $('#cantidad_descuento').val();
        var suma_seleccionado 	=   0;
        var suma_seleccionado   =   suma_seleccionado_asi();
        $('#cantidad_documento').val(suma_seleccionado);

        if(cantidad_descuento != 0){
            if(cantidad_descuento <= suma_seleccionado){alerterrorajax('Las filas seleccionadas superan la cantidad asociar'); return false;}	
        }
        var array_item      =   dataenviar();
        datastring = JSON.stringify(array_item);
        $('#data_archivo').val(datastring);
        $('#formguardar').submit();

    });

});

function dataenviar(){
    var data = [];
    $(".listatabla tr").each(function(){

        	asiento_id          = $(this).find('.input_asignar').attr('id');
        	cantidad          	= parseFloat($(this).find('.input_asignar').attr('data_cantidad'));
            check            	= $(this).find('.input_asignar');

            if($(check).is(':checked')){
                data.push({
                    asiento_id  : asiento_id,
                    cantidad  	: cantidad,
                });
            }


    });
    return data;
}

function contadortotal(){
    var total = 0;
    $(".listatabla tr").each(function(){
            cantidad            = parseFloat($(this).find('.input_asignar').attr('data_cantidad'));
            check               = $(this).find('.input_asignar');
            if($(check).is(':checked')){
                total = total + cantidad;
            }
    });
    return total;
}


function suma_seleccionado_asi(){
    var suma = 0;
        		        debugger;
    $(".listatabla tr").each(function(){

        	cantidad          	= parseFloat($(this).find('.input_asignar').attr('data_cantidad'));

            check            	= $(this).find('.input_asignar');
            if($(check).is(':checked')){
            	suma = suma + cantidad;
            }	


    });
    return suma;
}