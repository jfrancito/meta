$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".plancontable").on('click','.buscarplancontable', function() {

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
        ajax_normal(data,"/ajax-plan-contable");

    });


    $(".plancontable").on('dblclick','.dobleclickpc', function(e) {

        var _token                  =   $('#token').val();
        var cuenta_contable_id      =   $(this).attr('data_cuenta_contable_id');
        var idopcion                =   $('#idopcion').val();
        var anio                    =   $('#anio').val();

        data                        =   {
                                            _token                  : _token,
                                            cuenta_contable_id      : cuenta_contable_id,
                                            idopcion                : idopcion,
                                            anio                    : anio,
                                        };
        ajax_modal(data,"/ajax-modal-configuracion-plan-contable",
                  "modal-configuracion-plan-contable","modal-configuracion-plan-contable-container");

    });

 

});
