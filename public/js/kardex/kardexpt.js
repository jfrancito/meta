$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".kardexcascara").on('click','.descargarexcel', function() {

        event.preventDefault();
        var idopcion                 =   $('#idopcion').val();
        var _token                   =   $('#token').val();

        $('#formdescargar').submit();

    });



});
