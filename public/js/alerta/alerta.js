$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".bienvenido").on('click','.btndetallesunat', function() {

        var _token                  =   $('#token').val();
        var empresa_id              =   $(this).attr('data_empresa');

        data                        =   {
                                            _token                  : _token,
                                            empresa_id              : empresa_id,
                                        };

        ajax_modal(data,"/ajax-modal-detalle-documento-sin-enviar-sunat",
                  "modal-lista-detalle-documento-sin-enviar-sunat","modal-lista-detalle-documento-sin-enviar-sunat-container");

    });


});
