
$(document).ready(function(){


$(".bienvenido").on('click','.cargando', function() {
    abrircargando();
});





$(".be-content").on('click','.checkbox_asignar', function() {

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


$(".be-content").on('click','.checkbox_asignar_otro', function() {

    var input   = $(this).siblings('.input_asignar');
    var accion  = $(this).attr('data-atr');
    var tabla   = $(this).attr('data_tabla');

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
    validarrelleno_otro(accion,name,estado,check,token,tabla);
});









});


var carpeta = $("#carpeta").val();
//ajax normal

function activaTab(tab){
    $('.nav-tabs a[href="#' + tab + '"]').tab('show');
}

function ajax_normal_combo(data,link,contenedor) {
    abrircargando();
    $.ajax({
        type    :   "POST",
        url     :   carpeta+link,
        data    :   data,
        success: function (data) {
            cerrarcargando();
            $("."+contenedor).html(data);

        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });
}

function ajax_normal(data,link) {
    $(".listajax").html("");
    abrircargando();
    $.ajax({
        type    :   "POST",
        url     :   carpeta+link,
        data    :   data,
        success: function (data) {
            cerrarcargando();
            $(".listajax").html(data);

        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });
}

function ajax_normal_seccion(data,link,contenedor) {
    $("."+contenedor).html("");
    abrircargando();
    $.ajax({
        type    :   "POST",
        url     :   carpeta+link,
        data    :   data,
        success: function (data) {
            cerrarcargando();
            $("."+contenedor).html(data);

        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });
}


function ajax_normal_guardar_lista(data,link,btnclick) {

    abrircargando();
    $.ajax({
        type    :   "POST",
        url     :   carpeta+link,
        data    :   data,
        success: function (data) {
            cerrarcargando();
            location.reload();
        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });
}



function ajax_modal_actualizar(data,link,modal,contenedor_ajax) {

    abrircargando();

    $.ajax({
        type    :   "POST",
        url     :   carpeta+link,
        data    :   data,
        success: function (data) {
            cerrarcargando();
            $('.'+contenedor_ajax).html(data);

            $('#'+modal).niftyModal("show");
        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });
}
function ajax_modal(data,link,modal,contenedor_ajax) {

    abrircargando();

    $.ajax({
        type    :   "POST",
        url     :   carpeta+link,
        data    :   data,
        success: function (data) {
            cerrarcargando();
            $('.'+contenedor_ajax).html(data);
            $('#'+modal).niftyModal();
        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });
}


function ajax_modal_syn(data,link,modal,contenedor_ajax) {

    abrircargando();

    $.ajax({
        async   :   false,
        cache   :   false,
        type    :   'POST',
        url     :   carpeta+link,
        data    :   data,
        success: function (data) {
            cerrarcargando();
            $('.'+contenedor_ajax).html(data);
            $('#'+modal).niftyModal();

        },
        error: function (data) {
            cerrarcargando();
            error500(data);
        }
    });
}


//atibutos vacios
function errorvacio(atributo,texto) {
    if(atributo ==''){ alerterrorajax(texto); return false;}
}




function validarrelleno_otro(accion,name,estado,check,token,tabla){


    if (accion=='todas_asignar') {

        var table = $('.listatabla'+tabla).DataTable();
        $(".listatabla"+tabla+" tr").each(function(){
            nombre = $(this).find('.input_asignar').attr('id');
            if(nombre != 'todo_asignar'+tabla){
                $(this).find('.input_asignar').prop("checked", estado);
            }
        });
    }else{

        sw = 0;
        if(estado){
            $(".listatabla"+tabla+" tr").each(function(){
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


function validarrelleno(accion,name,estado,check,token){


    if (accion=='todas_asignar') {

        var table = $('.listatabla').DataTable();
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
