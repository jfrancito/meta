
$(document).ready(function(){

	var carpeta = $("#carpeta").val();

    $('#buscarcarros').on('click', function(event){

        event.preventDefault();
        var finicio     = $('#finicio').val();
        var ffin        = $('#ffin').val();
        var idopcion    = $('#idopcion').val();
        var estadocarro_id        = $('#estadocarro_id').val();


        var _token      = $('#token').val();
        $(".listatablapedido").html("");
        abrircargando();

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-listar-carros-ingreso-salida",
            data    :   {
                            _token  : _token,
                            finicio : finicio,
                            estadocarro_id : estadocarro_id,
                            ffin    : ffin,
                            idopcion    : idopcion,
                        },
            success: function (data) {
                cerrarcargando();
                $(".listatablapedido").html(data);

            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });

    });


    $(".listapedidoosiris").on('click','.btn-edit-estado-carro', function() {


        var _token              = $('#token').val();
        var carro_id           = $(this).attr('data-id');
        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/ajax-modal-detalle-carro",
            data    :   {
                            _token                  : _token,
                            carro_id               : carro_id
                        },    
            success: function (data) {
                $('.modal-detallecarro-container').html(data);
                $('#modal-carro').niftyModal();
            },
            error: function (data) {
                error500(data);
            }
        });
    });


    $("#modal-carro").on('click','#guardarcarro', function() {


        var _token              = $('#token').val();
        var carro_id           = $(this).attr('data-id');
        var estado_id           = $(this).attr('data-estado-id');
        var estado_cambiar_id           = $(this).attr('data-estado-cambiar-id');

        $.ajax({
            
            type    :   "POST",
            url     :   carpeta+"/editar-carro-despacho",
            data    :   {
                            _token                       : _token,
                            carro_id                     : carro_id,
                            estado_id                   : estado_id,
                            estado_cambiar_id       : estado_cambiar_id,
                        },    
            success: function (data) {

                $('#modal-carro').niftyModal('hide');
                actualizar_lista(_token,carpeta);

            },
            error: function (data) {
                error500(data);
            }
        });
    });





});



function actualizar_lista(_token,carpeta){


        var finicio     = $('#finicio').val();
        var ffin        = $('#ffin').val();
        var estadocarro_id        = $('#estadocarro_id').val();

        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-listar-carros-ingreso-salida",
            data    :   {
                            _token  : _token,
                            finicio : finicio,
                            estadocarro_id : estadocarro_id,
                            ffin    : ffin,
                        },
            success: function (data) {
                $(".listatablapedido").html(data);

            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });

}






