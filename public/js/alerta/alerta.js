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

    $(".bienvenido").on('click','.btndetallecorrelativo', function() {

        var _token                  =   $('#token').val();
        var empresa                 =   $(this).attr('data_empresa');
        var empresa_txt             =   $(this).attr('data_empresa_txt');
        var categoria               =   $(this).attr('data_categoria');
        var categoria_txt           =   $(this).attr('data_categoria_txt');
        var serie                   =   $(this).attr('data_serie');
        var min_doc                 =   $(this).attr('data_min_doc');
        var max_doc                 =   $(this).attr('data_max_doc');

        data                        =   {
                                            _token                  : _token,
                                            empresa                 : empresa,
                                            empresa_txt             : empresa_txt,
                                            categoria               : categoria,
                                            categoria_txt           : categoria_txt,
                                            serie                   : serie,
                                            min_doc                 : min_doc,
                                            max_doc                 : max_doc,
                                        };

        ajax_modal(data,"/ajax-modal-detalle-documento-correlativos",
                  "modal-lista-detalle-documento-correlativo","modal-lista-detalle-documento-correlativo-container");

    });



});
