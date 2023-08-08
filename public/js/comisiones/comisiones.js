$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".movilidad").on('click','.crearcuentacontable', function() {

        var _token                  =   $('#token').val();
        var idopcion                =   $('#idopcion').val();
        var data_tabla              =   $(this).attr('data_tabla');
        var periodo_registrado      =   $('#periodo_registrado').val();
        var data_archivo            =   $(this).attr('data_archivo');


        //var array_item              =   dataenviargeneralsincheck(data_tabla);
        var array_item              =   dataenviargeneral(data_tabla);
        if(array_item.length<=0){alerterrorajax('No existe ningun registro'); return false;}
        datastring = array_item; //JSON.stringify(array_item);

        data                        =   {
                                            _token                  : _token,
                                            data_tabla              : data_tabla,
                                            datastring              : datastring,
                                            periodo_registrado      : periodo_registrado,
                                            data_archivo            : data_archivo,
                                            idopcion                : idopcion
                                        };

        ajax_modal(data,"/ajax-modal-configuracion-comisiones-cuenta-contable",
                  "modal-configuracion-comisiones-cuenta-contable","modal-configuracion-comisiones-cuenta-contable-container");

    });


    $(".movilidad").on('change','#anio', function() {

        event.preventDefault();
        var anio        =   $('#anio').val();
        var _token      =   $('#token').val();
        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un anio."); return false;}
        data            =   {
                                _token      : _token,
                                anio        : anio
                            };

        ajax_normal_combo(data,"/ajax-combo-periodo-xanio-xempresa","ajax_anio")                    

    });


    $(".movilidad").on('click','.enviardata', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var enviardata              =   $(this).attr('data_archivo');
        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        $('input[type=search]').val('').change();
        $("#nso").DataTable().search("").draw();
        var array_item              =   dataenviar();
        if(array_item.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}
        datastring = JSON.stringify(array_item);
        //console.log(array_item);
        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        $('#opcion_val').val(enviardata);
        $('#data_archivo').val(datastring);
        $('#formguardar').submit();

    });


    $(".movilidad").on('click','.quitardata', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var enviardata              =   $(this).attr('data_archivo');
        var data_tabla              =   $(this).attr('data_tabla');
        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();


        $('input[type=search]').val('').change();
        $("#nso").DataTable().search("").draw();
        var array_item              =   dataenviargeneral(data_tabla);
        if(array_item.length<=0){alerterrorajax('Seleccione por lo menos una fila'); return false;}
        datastring = JSON.stringify(array_item);
        //console.log(array_item);
        //validacioones

        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}
        $('#opcion_val').val(enviardata);
        $('#data_archivo').val(datastring);
        $('#formguardar').submit();

    });



    $(".movilidad").on('click','.buscarcomision', function() {

        event.preventDefault();
        var anio                    =   $('#anio').val();
        var periodo_id              =   $('#periodo_id').val();
        var idopcion                =   $('#idopcion').val();
        var _token                  =   $('#token').val();

        //validacioones
        if(anio ==''){ alerterrorajax("Seleccione un año."); return false;}
        if(periodo_id ==''){ alerterrorajax("Seleccione un periodo."); return false;}

        data            =   {
                                _token                  : _token,
                                anio                    : anio,
                                periodo_id              : periodo_id,
                                idopcion                : idopcion,
                            };
        ajax_normal(data,"/ajax-registro-comisiones");

    });

 

});

function dataenviargeneral(data_tabla){
    var data = [];
    $(".listatabla"+data_tabla+" tr").each(function(){
        nombre          = $(this).find('.input_asignar').attr('id');
        if(nombre != 'todo_asignar'){

            check            = $(this).find('.input_asignar');
            documento_id     = $(this).attr('data_documento_id');
            periodo_id       = $(this).attr('data_periodo_id');
            cuenta_bancaria  = $(this).attr('data_cuenta_bancaria');

            if($(check).is(':checked')){
                data.push({
                    documento_id     : documento_id,
                    periodo_id       : periodo_id,
                    cuenta_bancaria  : cuenta_bancaria
                });
            }               
        }
    });
    return data;
}

function dataenviargeneralsincheck(data_tabla){
    var data = [];
    $(".listatabla"+data_tabla+" tr").each(function(){
        nombre          = $(this).find('.input_asignar').attr('id');
        if(nombre != 'todo_asignar'){
            check            = $(this).find('.input_asignar');
            documento_id     = $(this).attr('data_documento_id');
            periodo_id       = $(this).attr('data_periodo_id');
            cuenta_bancaria  = $(this).attr('data_cuenta_bancaria');

            data.push({
                documento_id     : documento_id,
                periodo_id       : periodo_id,
                cuenta_bancaria  : cuenta_bancaria
            });              
        }
    });
    return data;
}




function dataenviar(){
    var data = [];
    $(".listatabla tr").each(function(){
        nombre          = $(this).find('.input_asignar').attr('id');
        if(nombre != 'todo_asignar'){

            check            = $(this).find('.input_asignar');
            documento_id     = $(this).attr('data_documento_id');
            periodo_id       = $(this).attr('data_periodo_id');
            cuenta_bancaria  = $(this).attr('data_cuenta_bancaria');


            if($(check).is(':checked')){
                data.push({
                    documento_id     : documento_id,
                    periodo_id       : periodo_id,
                    cuenta_bancaria  : cuenta_bancaria
                });
            }               
        }
    });
    return data;
}