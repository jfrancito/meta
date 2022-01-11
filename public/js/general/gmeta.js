
var carpeta = $("#carpeta").val();
//ajax normal
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


//atibutos vacios
function errorvacio(atributo,texto) {
    if(atributo ==''){ alerterrorajax(texto); return false;}
}

