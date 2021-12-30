
$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    //18-10-2019
    $(".gestionregla").on('click','#asignarpreciomasivo', function() {

        event.preventDefault();

        $('input[type=search]').val('').change();
        $("#tablereglamasivo").DataTable().search("").draw();
        data = dataenviar();

        precio_total    =   $('#precio_total').val();

        if(data.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}
        /****** VALIDACIONES ********/
        if(precio_total==""){
            alerterrorajax("Ingrese un precio total");
            return false;
        }



        var datastring = JSON.stringify(data);
        actualizar_producto_masivas(datastring);
        actualizar_lista_porducto_masivas();


    });



    $(".gestionregla").on('keypress','.updateprice', function(e) {


        var _token          =   $('#token').val();
        var producto_id     =   $(this).parents('.fila_regla').attr('data_producto');
        var cliente_id      =   $(this).parents('.fila_regla').attr('data_cliente');
        var contrato_id     =   $(this).parents('.fila_regla').attr('data_contrato');
        var precio          =   $(this).val();
        var puntero         =   $(this);
        var check           = '<i class="mdi mdi-check-circle"></i>';    

        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){

            $.ajax({
                
                type    :   "POST",
                async   :   false,
                url     :   carpeta+"/ajax-guardar-precio-producto-contrato",
                data    :   {
                                _token          : _token,
                                precio          : precio,
                                producto_id     : producto_id,
                                cliente_id      : cliente_id,
                                contrato_id     : contrato_id
                            },
                success: function (data) {
                    alertajax(data);
                    puntero.val("");
                    puntero.parent('.columna-warning').siblings('.columna-precio').html(check+' '+precio);
                    puntero.parent('.columna-warning').siblings('.columna-precio').removeClass("columna-default");  
                    puntero.parent('.columna-warning').siblings('.columna-precio').addClass("columna-primary");

                },
                error: function (data) {
                    if(data.status = 500){
                        var contenido = $(data.responseText);
                        alerterror505ajax($(contenido).find('.trace-message').html()); 
                        console.log($(contenido).find('.trace-message').html());     
                    }
                }
            });

            //actualizar_lista_porducto_masivas();

        }
    });




    $(".gestionregla").on('change','#responsable_id', function() {

        var responsable_id      =   $('#responsable_id').val();
        var _token              =   $('#token').val();

        $.ajax({
              type    :     "POST",
              url     :     carpeta+"/ajax-canal-responsable",
              data    :     {
                                _token              : _token,
                                responsable_id      : responsable_id
                            },
                success: function (data) {
                    $('.ajax_canal').html(data);
                },
                error: function (data) {
                    error500(data);
                }
        });

        $.ajax({
              type    :     "POST",
              url     :     carpeta+"/ajax-cliente-responsable",
              data    :     {
                                _token              : _token,
                                responsable_id      : responsable_id
                            },
                success: function (data) {
                    $('.ajax_cliente').html(data);
                },
                error: function (data) {
                    error500(data);
                }
        });

    });


    $(".gestionregla").on('change','#canal_id', function() {

        var responsable_id      =   $('#responsable_id').val();
        var canal_id            =   $('#canal_id').val();
        var _token              =   $('#token').val();


        $.ajax({
              type    :     "POST",
              url     :     carpeta+"/ajax-subcanal_canal-responsable",
              data    :     {
                                _token              : _token,
                                responsable_id      : responsable_id,
                                canal_id            : canal_id,
                            },
                success: function (data) {
                    $('.ajax_sub_canal').html(data);
                },
                error: function (data) {
                    error500(data);
                }
        });
    });


    $(".gestionregla").on('click','#buscarasignarreglamasiva', function() {
        actualizar_lista_contratos_masivas();
    });

    $(".gestionregla").on('click','#buscarasignarpreciomasiva', function() {
        actualizar_lista_porducto_masivas();
    });




    $(".gestionregla").on('click','#asignarreglas', function(event) {
        
        event.preventDefault();
        $('input[type=search]').val('').change();
        $("#tablereglamasivo").DataTable().search("").draw();

        data = dataenviar();
        if(data.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}
        var datastring = JSON.stringify(data);

        actualizar_reglas_masivas(datastring);
        actualizar_lista_contratos_masivas();

    });


    $(".gestionregla").on('click','#eliminarreglas', function(event) {
        
        event.preventDefault();
        $('input[type=search]').val('').change();
        $("#tablereglamasivo").DataTable().search("").draw();
        
        data = dataenviareliminar();
        if(data.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}
        var datastring = JSON.stringify(data);

        eliminar_reglas_masivas(datastring);
        actualizar_lista_contratos_masivas();

    });


    $(".listacontratomasiva").on('click','.checkbox_asignar', function() {

        var input   = $(this).siblings('.input_asignar');
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


    $(".listacontratomasiva").on('click','.checkbox_eliminar', function() {

        var input   = $(this).siblings('.input_eliminar');
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
        validarrellenoeliminar(accion,name,estado,check);
    });






    function dataenviareliminar(){
        var data = [];
        $(".listatabla tr").each(function(){

            nombre          = $(this).find('.input_eliminar').attr('id');
            if(nombre != 'todo_eliminar'){

                check           = $(this).find('.input_eliminar');
                producto_id     = $(this).attr('data_producto');
                cliente_id      = $(this).attr('data_cliente');
                contrato_id     = $(this).attr('data_contrato');
                regla_id        = $("#regla_id").val();

                if($(check).is(':checked')){

                    data.push({
                        producto_id     : producto_id,
                        cliente_id      : cliente_id,
                        contrato_id     : contrato_id,
                        regla_id        : regla_id
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
                producto_id     = $(this).attr('data_producto');
                cliente_id      = $(this).attr('data_cliente');
                contrato_id     = $(this).attr('data_contrato');
                empresa_id      = $(this).attr('data_empresa');

                regla_id        = $("#regla_id").val();

                if($(check).is(':checked')){

                    data.push({
                        producto_id     : producto_id,
                        cliente_id      : cliente_id,
                        contrato_id     : contrato_id,
                        regla_id        : regla_id,
                        empresa_id      : empresa_id
                    });

                }               
            }
        });
        return data;
    }




    function actualizar_lista_contratos_masivas(){

        var _token              = $('#token').val();
        var responsable_id      = $('#responsable_id').select2().val();
        var canal_id            = $('#canal_id').select2().val();
        var cliente_id          = $('#cliente_id').select2().val();
        var subcanal_id         = $('#subcanal_id').select2().val();
        var producto_id         = $('#producto_id').select2().val();
        var regla_id            = $('#regla_id').val();
        var empresa_id         = $('#empresa_id').select2().val();

        /****** VALIDACIONES ********/
        if(responsable_id.length<=0){
            alerterrorajax("Seleccione un responsable para el reporte");
            return false;
        }
        if(producto_id.length<=0){
            alerterrorajax("Seleccione un producto para el reporte");
            return false;
        }

        if(empresa_id.length<=0){
            alerterrorajax("Seleccione una empresa para la asignacion");
            return false;
        }



        abrircargando();
        var textoajax   = $('.listacontratomasiva').html(); 
        $(".listacontratomasiva").html("");
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-lista-contrato-producto_masivo",
            data    :   {
                            _token          : _token,
                            responsable_id  : responsable_id,
                            canal_id        : canal_id,
                            cliente_id      : cliente_id,
                            subcanal_id     : subcanal_id,
                            producto_id     : producto_id,
                            regla_id        : regla_id,
                            empresa_id      : empresa_id,                            
                        },
            success: function (data) {
                cerrarcargando();
                $(".listacontratomasiva").html(data);                
            },
            error: function (data) {

                cerrarcargando();
                if(data.status = 500){

                    var contenido = $(data.responseText);
                    alerterror505ajax($(contenido).find('.trace-message').html()); 
                    $(".listacontratomasiva").html(textoajax);  
                    console.log($(contenido).find('.trace-message').html());     
                }
                
            }
        });
    }


    //18-10-2019
    function actualizar_lista_porducto_masivas(){

        var _token              = $('#token').val();
        var responsable_id      = $('#responsable_id').select2().val();
        var canal_id            = $('#canal_id').select2().val();
        var cliente_id          = $('#cliente_id').select2().val();
        var subcanal_id         = $('#subcanal_id').select2().val();
        var producto_id         = $('#producto_id').select2().val();
        var empresa_id         = $('#empresa_id').select2().val();

        /****** VALIDACIONES ********/
        if(responsable_id.length<=0){
            alerterrorajax("Seleccione un responsable para la asignacion");
            return false;
        }
        if(producto_id.length<=0){
            alerterrorajax("Seleccione un producto para la asignacion");
            return false;
        }

        if(empresa_id.length<=0){
            alerterrorajax("Seleccione una empresa para la asignacion");
            return false;
        }


        abrircargando();
        var textoajax   = $('.listacontratomasiva').html(); 
        $(".listacontratomasiva").html("");
        debugger;


        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-lista-precio-producto_masivo",
            data    :   {
                            _token          : _token,
                            responsable_id  : responsable_id,
                            canal_id        : canal_id,
                            cliente_id      : cliente_id,
                            subcanal_id     : subcanal_id,
                            producto_id     : producto_id,
                            empresa_id      : empresa_id,                           
                        },
            success: function (data) {
                cerrarcargando();
                $(".listacontratomasiva").html(data);                
            },
            error: function (data) {

                cerrarcargando();
                if(data.status = 500){

                    var contenido = $(data.responseText);
                    alerterror505ajax($(contenido).find('.trace-message').html()); 
                    $(".listacontratomasiva").html(textoajax);  
                    console.log($(contenido).find('.trace-message').html());     
                }
                
            }
        });
    }





    function eliminar_reglas_masivas(datastring){

        /**** ACTUALIZAMOS LAS REGLAS *****/
        var _token              = $('#token').val();
        abrircargando();
        regla_id                = $("#regla_id").val();
        $.ajax({
            type    :   "POST",
            async   :   false,
            url     :   carpeta+"/ajax-elimnar-reglas-masivas",
            data    :   {
                            _token          : _token,
                            datastring      : datastring,
                            regla_id        : regla_id                    
                        },
            success: function (data) {

                JSONdata     = JSON.parse(data);
                error        = JSONdata[0].error;
                mensaje      = JSONdata[0].mensaje;
                if(error==false){
                    alertajax(mensaje);
                }else{
                    alerterrorajax(mensaje); 
                }
                cerrarcargando();

            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });

        /**********************************/

    }



    function actualizar_reglas_masivas(datastring){

        /**** ACTUALIZAMOS LAS REGLAS *****/
        var _token              = $('#token').val();
        abrircargando();
        regla_id                = $("#regla_id").val();
        $.ajax({
            type    :   "POST",
            async   :   false,
            url     :   carpeta+"/ajax-actualizar-reglas-masivas",
            data    :   {
                            _token          : _token,
                            datastring      : datastring,
                            regla_id        : regla_id                       
                        },
            success: function (data) {

                JSONdata     = JSON.parse(data);
                error        = JSONdata[0].error;
                mensaje      = JSONdata[0].mensaje;
                if(error==false){
                    alertajax(mensaje);
                }else{
                    alerterrorajax(mensaje); 
                }
                cerrarcargando();

            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });

        /**********************************/

    }

    //18-10-2019
    function actualizar_producto_masivas(datastring){

        /**** ACTUALIZAMOS LAS REGLAS *****/
        var _token              = $('#token').val();
        abrircargando();
        precio_total            = $("#precio_total").val();
        $.ajax({
            type    :   "POST",
            async   :   false,
            url     :   carpeta+"/ajax-actualizar-precio-producto-masivas",
            data    :   {
                            _token          : _token,
                            datastring      : datastring,
                            precio_total    : precio_total                    
                        },
            success: function (data) {

                JSONdata     = JSON.parse(data);
                error        = JSONdata[0].error;
                mensaje      = JSONdata[0].mensaje;
                if(error==false){
                    alertajax(mensaje);
                }else{
                    alerterrorajax(mensaje); 
                }
                cerrarcargando();

            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });

        /**********************************/

    }



    function validarrelleno(accion,name,estado,check,token){


        if (accion=='todas_asignar') {

            var table = $('#tablereglamasivo').DataTable();
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

                    console.log($(this).find('.input_asignar').length);

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


    function validarrellenoeliminar(accion,name,estado,check,token){


        if (accion=='todas_eliminar') {

            var table = $('#tablereglamasivo').DataTable();
            $(".listatabla tr").each(function(){
                nombre = $(this).find('.input_eliminar').attr('id');
                if(nombre != 'todo_eliminar'){
                    $(this).find('.input_eliminar').prop("checked", estado);
                }
            });
        }else{

            sw = 0;
            if(estado){
                $(".listatabla tr").each(function(){
                    nombre = $(this).find('.input_eliminar').attr('id');

                    console.log($(this).find('.input_eliminar').length);

                    if(nombre != 'todo_eliminar' && $(this).find('.input_eliminar').length > 0){
                        if(!($(this).find('.input_eliminar').is(':checked'))){
                            sw = sw + 1;
                        }
                    }
                });
                if(sw==1){
                    $("#todo_eliminar").prop("checked", estado);
                }
            }else{
                $("#todo_eliminar").prop("checked", estado);
            }           
        }
    }




});


