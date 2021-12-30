
$(document).ready(function(){

	var carpeta = $("#carpeta").val();

    $(".notacredito").on('click','.btn-detalle-regla-agregar', function() {

        var _token                  = $('#token').val();
        var fechainicio             = $('#fechainicio').val();        
        var fechafin                = $('#fechafin').val();
        var buscar                  = $('#buscar').val();
        var contrato_id             = $(this).attr('data-contrato');
        var ordencen_id             = $(this).attr('data_oredencen');
        var producto_id             = $(this).attr('data-producto');
        var array_reglas            = $(this).attr('data_reglas');
        var documento_id            = $(this).attr('data_documento');
        var referencia_id           = $(this).attr('data_referencia');

        var iddocumentonotacredito  = $('#iddocumentonotacredito ').val();        
        var opcion                  = $('#opcion').val();


        abrircargando();


        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-agregar-regla-orden-cen",
            data    :   {
                            _token          : _token,
                            cuenta_id       : contrato_id,
                            fechainicio     : fechainicio,
                            fechafin        : fechafin,
                            ordencen_id     : ordencen_id,
                            producto_id     : producto_id,
                            array_reglas    : array_reglas                                                   
                        },
            success: function (data) {

                JSONdata     = JSON.parse(data);
                error        = JSONdata[0].error;
                mensaje      = JSONdata[0].mensaje;
                if(error==false){
                    ajax_modal_agregar_detalle_notacredito(_token,carpeta,contrato_id,documento_id,referencia_id,ordencen_id,array_reglas);
                    

                    if(buscar==0){
                        ajax_lista_ordencen_notacredito(_token,carpeta,contrato_id,fechainicio,fechafin,array_reglas,opcion,iddocumentonotacredito,mensaje);
                    }else{
                        ajax_lista_ordencen(_token,carpeta,contrato_id,fechainicio,fechafin,array_reglas,opcion,iddocumentonotacredito,mensaje);
                    }


                }else{
                    alerterrorajax(mensaje); 
                }

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


    $(".notacredito").on('change','.pickerfecha', function() {
        var _token              =   $('#token').val();
        var cuenta_id           =   $('#cuenta_id').select2().val();
        var fechainicio         =   $('#fechainicio').val();        
        var fechafin            =   $('#fechafin').val(); 

        $.ajax({
              type    :     "POST",
              url     :     carpeta+"/ajax-reglas-cliente-fechas",
              data    :     {
                                _token              : _token,
                                cuenta_id           : cuenta_id,
                                fechainicio         : fechainicio,
                                fechafin            : fechafin,
                            },
                success: function (data) {
                    $('.ajax_regla').html(data);
                },
                error: function (data) {
                    error500(data);
                }
        });
    });



    $(".notacredito").on('change','#cuenta_id', function() {
        var _token              =   $('#token').val();
        var cuenta_id           =   $('#cuenta_id').select2().val();
        var fechainicio         =   $('#fechainicio').val();        
        var fechafin            =   $('#fechafin').val(); 

        $.ajax({
              type    :     "POST",
              url     :     carpeta+"/ajax-reglas-cliente-fechas",
              data    :     {
                                _token              : _token,
                                cuenta_id           : cuenta_id,
                                fechainicio         : fechainicio,
                                fechafin            : fechafin,
                            },
                success: function (data) {
                    $('.ajax_regla').html(data);
                },
                error: function (data) {
                    error500(data);
                }
        });
    });





    $(".notacredito").on('click','#buscarfacturas', function() {

        var _token              = $('#token').val();
        var cuenta_id           = $('#cuenta_id').select2().val();
        var fechainicio         = $('#fechainicio').val();        
        var fechafin            = $('#fechafin').val(); 

        /****** VALIDACIONES ********/
        if(cuenta_id.length<=0){
            alerterrorajax("Seleccione un cliente para el reporte");
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
        var textoajax   = $('.listafacturas').html(); 
        $(".listafacturas").html("");


        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-lista-facturas-nota-credito",
            data    :   {
                            _token          : _token,
                            cuenta_id       : cuenta_id,
                            fechainicio     : fechainicio,
                            fechafin        : fechafin                            
                        },
            success: function (data) {
                cerrarcargando();
                $(".listafacturas").html(data);                
            },
            error: function (data) {
                cerrarcargando();
                if(data.status = 500){
                    var contenido = $(data.responseText);
                    alerterror505ajax($(contenido).find('.trace-message').html()); 
                    $(".listafacturas").html(textoajax);  
                    console.log($(contenido).find('.trace-message').html());     
                }
            }
        });

    });




    $(".notacredito").on('click','#buscarordencen', function() {

        var _token              = $('#token').val();
        var cuenta_id           = $('#cuenta_id').select2().val();
        var fechainicio         = $('#fechainicio').val();        
        var fechafin            = $('#fechafin').val(); 
        var regla_id            = $('#regla_id').select2().val();
        var opcion              = $('#opcion').val();

        /****** VALIDACIONES ********/
        if(cuenta_id.length<=0){
            alerterrorajax("Seleccione un cliente para el reporte");
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
        if(regla_id == null){
            alerterrorajax("Seleccione por lo menos una regla");
            return false;
        }



        abrircargando();
        var textoajax   = $('.listafacturas').html(); 
        $(".listafacturas").html("");


        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-lista-oredencen-nota-credito",
            data    :   {
                            _token          : _token,
                            cuenta_id       : cuenta_id,
                            fechainicio     : fechainicio,
                            fechafin        : fechafin,
                            regla_id        : regla_id,
                            opcion          : opcion,                      
                        },
            success: function (data) {
                cerrarcargando();
                $(".listafacturas").html(data);                
            },
            error: function (data) {
                cerrarcargando();
                if(data.status = 500){
                    var contenido = $(data.responseText);
                    alerterror505ajax($(contenido).find('.trace-message').html()); 
                    $(".listafacturas").html(textoajax);  
                    console.log($(contenido).find('.trace-message').html());     
                }
            }
        });



    });

    $(".notacredito").on('click','#buscaragregarordencen', function() {

        var _token              = $('#token').val();
        var cuenta_id           = $('#cuenta_id').select2().val();
        var fechainicio         = $('#fechainicio').val();        
        var fechafin            = $('#fechafin').val(); 
        var regla_id            = $('#regla_id').select2().val();
        var opcion              = $('#opcion').val();
        var iddocumentonotacredito              = $('#iddocumentonotacredito').val();


        /****** VALIDACIONES ********/
        if(cuenta_id.length<=0){
            alerterrorajax("Seleccione un cliente para el reporte");
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
        if(regla_id == null){
            alerterrorajax("Seleccione por lo menos una regla");
            return false;
        }



        abrircargando();
        var textoajax   = $('.listafacturas').html(); 
        $(".listafacturas").html("");


        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-lista-agregar-oredencen-nota-credito",
            data    :   {
                            _token          : _token,
                            cuenta_id       : cuenta_id,
                            fechainicio     : fechainicio,
                            fechafin        : fechafin,
                            regla_id        : regla_id,
                            opcion          : opcion,
                            iddocumentonotacredito          : iddocumentonotacredito,                     
                        },
            success: function (data) {
                cerrarcargando();
                $(".listafacturas").html(data);                
            },
            error: function (data) {
                cerrarcargando();
                if(data.status = 500){
                    var contenido = $(data.responseText);
                    alerterror505ajax($(contenido).find('.trace-message').html()); 
                    $(".listafacturas").html(textoajax);  
                    console.log($(contenido).find('.trace-message').html());     
                }
            }
        });



    });





    $(".notacredito").on('click','.badgenotacredito', function(e) {

        var _token          = $('#token').val();
        var contrato_id     = $(this).parents('.fila_regla').attr('data_contrato');
        var documento_id    = $(this).parents('.fila_regla').attr('data_documento');
        var referencia_id   = $(this).parents('.fila_regla').attr('data_referencia');
        var ordencen_id     = $(this).parents('.fila_regla').attr('data_oredencen');
        var reglas_id       = $(this).parents('.fila_regla').attr('data_reglas');


        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-documento",
            data    :   {
                            _token          : _token,
                            contrato_id     : contrato_id,
                            documento_id    : documento_id,
                            referencia_id   : referencia_id,
                            ordencen_id     : ordencen_id,
                            reglas_id       : reglas_id
                        },
            success: function (data) {
                $('.modal-detalledocumento-container').html(data);
                $('#modal-detalledocumento').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });

    });




    $(".notacredito").on('click','.badgeelimnar', function(e) {

        var _token          = $('#token').val();
        var contrato_id     = $(this).parents('.fila_regla').attr('data_contrato');
        var documento_id    = $(this).parents('.fila_regla').attr('data_documento');
        var referencia_id   = $(this).parents('.fila_regla').attr('data_referencia');
        var ordencen_id     = $(this).parents('.fila_regla').attr('data_oredencen');
        var reglas_id       = $(this).parents('.fila_regla').attr('data_reglas');
        var documento_nota_credito_id    = $(this).parents('.fila_regla').attr('data_documento_nota_credito');

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-eliminar-orden-cen",
            data    :   {
                            _token          : _token,
                            contrato_id     : contrato_id,
                            documento_id    : documento_id,
                            referencia_id   : referencia_id,
                            ordencen_id     : ordencen_id,
                            documento_nota_credito_id     : documento_nota_credito_id,
                            reglas_id       : reglas_id
                        },
            success: function (data) {
                setTimeout(alertajax('Eliminación exitosa'), 5000);
                location.reload();

            },
            error: function (data) {
                error500(data);
            }
        });

    });




    $(".notacredito").on('click','#btnguardarasociacion', function() {

        $('#facturasnotacredito').val('');
        $('#idopcion').val($('#opcion').val());


        $('input[type=search]').val('').change();
        $("#tablenotacredito").DataTable().search("").draw();

        data = dataenviar();
        if(data.length<=0){alerterrorajax('Seleccione por lo menos una fila para relacionar las facturas'); return false;}
        var datastring = JSON.stringify(data);
        $('#facturasnotacredito').val(datastring);

        abrircargando();
        return true;

    });


    $(".notacredito").on('click','#btnguardar', function() {


        $('#facturasrelacionada').val('');
        var serie       =   $('#serie').val();
        var motivo_id      =   $('#motivo_id').val();




        dataradio = dataenviarradio();
        /* validaciones */
        if(serie.length<=0){alerterrorajax("Seleccione una serie para registrar");return false;}
        if(motivo_id.length<=0){alerterrorajax("Seleccione un motivo para registrar");return false;}
        if(dataradio.length<=0){alerterrorajax('Seleccione por lo menos una fila para relacionar la factura'); return false;}

        var total_factura       =   parseFloat($("input[name=factura]:checked").parents('.fila_regla').attr('data_tf'));
        var total_reglas        =   parseFloat($("#total_reglas").val());

        if(total_factura<total_reglas){alerterrorajax('Total recibido debe ser mayor e igual que total descuento'); return false;}

        var datastringradio = JSON.stringify(dataradio);
        $('#facturasrelacionada').val(datastringradio);
        abrircargando();
        return true;
        //return false;

    });


    $(".notacredito").on('change','#serie', function() {

        var serie      =   $('#serie').val();
        var _token     =   $('#token').val();


        $.ajax({
              type    :     "POST",
              url     :     carpeta+"/ajax-nro-documento",
              data    :     {
                                _token              : _token,
                                serie               : serie
                            },
                success: function (data) {
                    $('.nro-documento').html(data);
                },
                error: function (data) {
                    error500(data);
                }
        });


    });


    $(".notacredito").on('click','.checkbox_asignar', function() {

        var input       = $(this).siblings('.input_asignar');
        var accion      = $(this).attr('data-atr');
        var name        = $(this).attr('name');
        var check       = -1;
        var estado      = -1;

        if(name == 'todo_asignar'){
            var tfactura        = 0;
            var tnotacredito    = 0; 
        }else{
            var tfactura        = parseFloat($(this).parents('.fila_regla').attr('data_tf'));
            var tnotacredito    = parseFloat($(this).parents('.fila_regla').attr('data_tnc'));   
        }


        if($(input).is(':checked')){

            check   = 0;
            estado  = false;
            tfactura = -tfactura;
            tnotacredito = -tnotacredito;

        }else{

            check   = 1;
            estado  = true;

        }

        $('input[type=search]').val('').change();
        $("#tablenotacredito").DataTable().search("").draw();

        validarrelleno(accion,name,estado,check);
        totalfactura(tfactura,tnotacredito);
    });



    $(".notacredito").on('click','.input_asignar_radio', function() {

        var documento_id        =   $(this).parents('.fila_regla').attr('data_documento');
        var _token              =   $('#token').val();
        $.ajax({
              type    :     "POST",
              url     :     carpeta+"/ajax-glosa-documento",
              data    :     {
                                _token              : _token,
                                documento_id        : documento_id
                            },
                success: function (data) {
                    $('#glosa').val(data);
                },
                error: function (data) {
                    error500(data);
                }
        });



    });




});

function ajax_lista_ordencen(_token,carpeta,contrato_id,fechainicio,fechafin,array_reglas,opcion,iddocumentonotacredito,mensaje){


        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-lista-oredencen-nota-credito",
            data    :   {
                            _token          : _token,
                            cuenta_id       : contrato_id,
                            fechainicio     : fechainicio,
                            fechafin        : fechafin,
                            regla_id        : array_reglas,
                            opcion          : opcion,                      
                        },
            success: function (data) {
                cerrarcargando();
                $(".listafacturas").html(data);
                alertajax(mensaje);               
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
}


function ajax_lista_ordencen_notacredito(_token,carpeta,cuenta_id,fechainicio,fechafin,regla_id,opcion,iddocumentonotacredito,mensaje){

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-lista-agregar-oredencen-nota-credito",
            data    :   {
                            _token                          : _token,
                            cuenta_id                       : cuenta_id,
                            fechainicio                     : fechainicio,
                            fechafin                        : fechafin,
                            regla_id                        : regla_id,
                            opcion                          : opcion,
                            iddocumentonotacredito          : iddocumentonotacredito,                     
                        },
            success: function (data) {
                $(".listafacturas").html(data);
                cerrarcargando(); 
                alertajax(mensaje);             
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

}

function ajax_modal_agregar_detalle_notacredito(_token,carpeta,cuenta_id,documento_id,referencia_id,ordencen_id,reglas_id){

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-documento",
            data    :   {
                            _token          : _token,
                            contrato_id     : cuenta_id,
                            documento_id    : documento_id,
                            referencia_id   : referencia_id,
                            ordencen_id     : ordencen_id,
                            reglas_id       : reglas_id
                        },
            success: function (data) {
                $('.modal-detalledocumento-container').html(data);
                $('#modal-detalledocumento').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });

}




function totalfactura(tfactura,tnotacredito){

    var totalf = tfactura;
    var totalnc = tnotacredito;

    $(".listatabla tr").each(function(){
        nombre          =   $(this).find('.input_asignar').attr('id');

        if(nombre != 'todo_asignar'){
            check           = $(this).find('.input_asignar');
            if($(check).is(':checked')){
                totalnc      =   totalnc + parseFloat($(this).attr('data_tnc'));
                totalf       =   totalf + parseFloat($(this).attr('data_tf'));
            }               
        }
    });


    totalf          =  totalf + parseFloat($('#totalfacturas').val());
    totalnc         =  totalnc + parseFloat($('#totalreglas').val());

    $('.totalfactura').html(totalf.toFixed(4));
    $('.totalnotacredito').html(totalnc.toFixed(4));
}


function dataenviarradio(){
    var data = [];
    $(".listatabla tr").each(function(){

        nombre          = $(this).find('.input_asignar_radio').attr('id');

        if(nombre != 'todo_asignar'){

            check           = $(this).find('.input_asignar_radio');
            documento_id    = $(this).attr('data_documento');

            if($(check).is(':checked')){

                data.push({
                    documento_id     : documento_id
                });

            }               
        }
    });
    return data;
}

function dataenviar(){
    var data = [];
    $(".listatabla tr").each(function(){

        nombre          = $(this).find('.input_asignar').attr('id');

        if(nombre != 'todo_asignar'){

            check           = $(this).find('.input_asignar');
            documento_id    = $(this).attr('data_documento');
            orden_id        = $(this).attr('data_oredencen');


            if($(check).is(':checked')){

                data.push({
                    documento_id      : documento_id,
                    orden_id          : orden_id
                });

            }               
        }
    });
    return data;
}

function actualizar_reglas(){

    var _token              =   $('#token').val();
    var cuenta_id           =   $('#cuenta_id').select2().val();
    var fechainicio         =   $('#fechainicio').val();        
    var fechafin            =   $('#fechafin').val(); 

    $.ajax({
          type    :     "POST",
          url     :     carpeta+"/ajax-reglas-cliente-fechas",
          data    :     {
                            _token              : _token,
                            cuenta_id           : cuenta_id,
                            fechainicio         : fechainicio,
                            fechafin            : fechafin,
                        },
            success: function (data) {
                $('.ajax_regla').html(data);
            },
            error: function (data) {
                error500(data);
            }
    });

}

function validarrelleno(accion,name,estado,check,token){


        if (accion=='todas_asignar') {

            var table = $('#tablenotacredito').DataTable();
            $(".listatabla tr").each(function(){
                nombre = $(this).find('.input_asignar').attr('id');
                if(nombre != 'todo_asignar'){
                    $(this).find('.input_asignar').prop("checked", estado);
                }
            });
        }else{

            sw = 0;
            if(estado){
                $(".listatabla tr").each(function(){
                    nombre = $(this).find('.input_asignar').attr('id');

                    if(nombre != 'todo_asignar' && $(this).find('.input_asignar').length > 0){
                        if(!($(this).find('.input_asignar').is(':checked'))){
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
