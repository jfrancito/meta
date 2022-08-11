$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".configuracionproducto").on('change','#categoria_producto_id', function() {

        event.preventDefault();
        var categoria_producto_id       =   $('#categoria_producto_id').val();
        var _token                      =   $('#token').val();


        data            =   {
                                _token      : _token,
                                categoria_producto_id        : categoria_producto_id
                            };


        ajax_normal_combo(data,"/ajax-combo-servicio-material-xcategoria-producto","ajax_categoria")                    

    });



    $(".configuracionproducto").on('click','.buscarproducto', function() {

        event.preventDefault();
        var producto_id     =   $('#producto_select').val();
        var sub_categoria_id     =   $('#sub_categoria_id').val();
        var categoria_producto_id     =   $('#categoria_producto_id').val();

        var idopcion        =   $('#idopcion').val();
        var _token          =   $('#token').val();
        var anio            =   $('#anio').val();

        //validacioones

        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(producto_id =='' && (sub_categoria_id == '' || sub_categoria_id == null)){ alerterrorajax("Seleccione un por lo menos una sub categoria o un producto."); return false;}


        data            =   {
                                _token      : _token,
                                producto_id : producto_id,
                                sub_categoria_id : sub_categoria_id,
                                categoria_producto_id : categoria_producto_id,
                                anio        : anio,
                                idopcion    : idopcion,
                            };
        ajax_normal(data,"/ajax-configuracion-producto");

    });


    $(".configuracionproducto").on('click','.agregarcuentacontable', function() {

        var _token                  =   $('#token').val();
        var array_productos         =   dataenviar();
        var idopcion                =   $('#idopcion').val();
        if(array_productos.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}

        data                        =   {
                                            _token                  : _token,
                                            array_productos         : array_productos,
                                            idopcion                : idopcion
                                        };

        ajax_modal(data,"/ajax-modal-configuracion-producto-cuenta-contable",
                  "modal-configuracion-producto-cuenta-contable","modal-configuracion-producto-cuenta-contable-container");

    });


    $(".configuracionproducto").on('click','.agregarcodigomigracion', function() {

        var _token                  =   $('#token').val();
        var array_productos         =   dataenviar();
        var idopcion                =   $('#idopcion').val();
        if(array_productos.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}

        data                        =   {
                                            _token                  : _token,
                                            array_productos         : array_productos,
                                            idopcion                : idopcion
                                        };

        ajax_modal(data,"/ajax-modal-configuracion-producto-codigo-migracion",
                  "modal-configuracion-producto-cuenta-contable","modal-configuracion-producto-cuenta-contable-container");

    });



    $(".configuracionproducto").on('change','#nivel', function() {

        event.preventDefault();
        var nivel       =   $('#nivel').val();
        var _token      =   $('#token').val();
        //validacioones
        if(nivel ==''){ alerterrorajax("Seleccione un nivel."); return false;}
        data            =   {
                                _token      : _token,
                                nivel       : nivel
                            };

        ajax_normal_combo(data,"/ajax-combo-cuentacontable-xnivel","ajax_nivel");                  

    });

    $(".configuracionproducto").on('click','.btn-guardar-configuracion', function() {

        var array_productos           =   $('#array_productos').val();

        var cuenta_contable_rel_id    =   $('#cuenta_contable_rel_id').val();
        var cuenta_contable_ter_id    =   $('#cuenta_contable_ter_id').val();
        var cuenta_contable_compra_id =   $('#cuenta_contable_compra_id').val();
        var ind_venta_compra          =   $('#ind_venta_compra').val();
        var _token                    =   $('#token').val();
        //validacioones
        if(ind_venta_compra=='1'){
            if(cuenta_contable_rel_id  ==''){ alerterrorajax("Seleccione una cuenta contable relacionadas."); return false;}
            if(cuenta_contable_ter_id  ==''){ alerterrorajax("Seleccione una cuenta contable tercero."); return false;} 
        }else{
            if(cuenta_contable_compra_id  ==''){ alerterrorajax("Seleccione una cuenta contable de compra."); return false;} 
        }

        //cerrar modal
        $('#modal-configuracion-producto-cuenta-contable').niftyModal('hide');

        data            =   {
                                _token                   : _token,
                                cuenta_contable_rel_id   : cuenta_contable_rel_id,
                                cuenta_contable_ter_id   : cuenta_contable_ter_id,
                                cuenta_contable_compra_id   : cuenta_contable_compra_id,
                                ind_venta_compra         : ind_venta_compra,
                                array_productos          : array_productos,
                            };

        ajax_normal_guardar_lista(data,"/ajax-guardar-cuenta-contable","buscarproducto");                 

    });


    $(".configuracionproducto").on('click','.btn-guardar-configuracion-cm', function() {

        var array_productos           =   $('#array_productos').val();
        var codigo_migracion          =   $('#codigo_migracion').val();
        var _token                    =   $('#token').val();
        //validacioones
        if(codigo_migracion  ==''){ alerterrorajax("Ingrese un codigo de migracion."); return false;} 

        //cerrar modal
        $('#modal-configuracion-producto-cuenta-contable').niftyModal('hide');

        data            =   {
                                _token                   : _token,
                                codigo_migracion         : codigo_migracion,
                                array_productos          : array_productos,
                            };

        ajax_normal_guardar_lista(data,"/ajax-guardar-codigo-migracion","buscarproducto");                 

    });





    $(".configuracionproducto").on('click','#ventastab', function() {
        $("#ind_venta_compra").val("1");
    });
    $(".configuracionproducto").on('click','#comprastab', function() {
        $("#ind_venta_compra").val("2");
    });



});


function dataenviar(){
    var data = [];
    $(".listatabla tr").each(function(){

        nombre          = $(this).find('.input_asignar').attr('id');

        if(nombre != 'todo_asignar'){

            check           = $(this).find('.input_asignar');
            producto_id     = $(this).attr('data_producto_id');
            if($(check).is(':checked')){
                data.push({
                    producto_id     : producto_id
                });
            }               
        }
    });
    return data;
}