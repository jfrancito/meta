$(document).ready(function(){

var carpeta = $("#carpeta").val();


    $('.enviarcomosionautorizacion').on('click', function(event){

        event.preventDefault();

        $('input[type=search]').val('').change();
        $("#table1").DataTable().search("").draw();

        data = dataenviarautorizacion();
        if(data.length<=0){alerterrorajax("Seleccione por lo menos un pedido");return false;}
        var datastring = JSON.stringify(data);
        $('#pedido').val(datastring);

        $('#cod_estado_re').val($(this).attr('data_estado'));

        console.log(data);
        abrircargando();
        $( "#formpedido" ).submit();
        
    });


});




function dataenviarautorizacion(){
        var data = [];
        $(".listatabla tr").each(function(){

            
            check   = $(this).find('input');
            codperiodo   = $(this).find('input').attr('data_codperiodo');
            codvendedor   = $(this).find('input').attr('data_codvendedor');
            proviene   = $(this).find('input').attr('data_proviene');
            nombre  = $(this).find('input').attr('id');
            if(nombre != 'todo'){
                if($(check).is(':checked')){
                    data.push({
                        codperiodo      : codperiodo,
                        codvendedor     : codvendedor,
                        proviene        : proviene
                    });
                }               
            }
        });
        return data;
}