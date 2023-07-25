@extends('template_lateral')
@section('style')

    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/bootsnipp.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/activos-fijos.css') }} "/>


@stop
@section('section')


<div class="be-content crearcupon">
  <div class="main-content container-fluid">

    <!--Basic forms-->
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default panel-border-color panel-border-color-default">
          <div class="panel-heading panel-heading-divider" >Registro de Activo Fijo
            @if (isset($producto))
              <span class="panel-subtitle">Registro de Activo Fijo transferido desde Almacén de Activos</span></div>
            @else
              <span class="panel-subtitle">Registro de Obra</span></div>
            @endif
          <div  id="activos-fijos" class="panel-body">
              @if (isset($producto))
                <form method="POST"  action="{{ url('/registrar-activo-fijo/'.$producto->COD_PRODUCTO.'/'.$producto->COD_DOCUMENTO_CTBLE) }}" style="border-radius: 0px;" class="form-horizontal group-border-dashed">
              @else
                <form method="POST"  action="{{ url('/registrar-obra-activo-fijo/') }}" style="border-radius: 0px;" class="form-horizontal group-border-dashed">
              @endif

                    {{ csrf_field() }}

                    @include('logistica.form.registro')

                </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div> 

@stop

@section('script')

    <script src="{{ asset('public/js/general/inputmask/inputmask.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/inputmask.extensions.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/inputmask.numeric.extensions.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/inputmask.date.extensions.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/jquery.inputmask.js') }}" type="text/javascript"></script>

    <script src="{{ asset('public/lib/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery.nestable/jquery.nestable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/moment.js/min/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>        
    <script src="{{ asset('public/lib/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/bootstrap-slider/js/bootstrap-slider.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/app-form-elements.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/parsley/parsley.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
      $(document).ready(function(){
        //initialize the javascript
        App.init();
        App.formElements();
        $('form').parsley();

        $('.importe').inputmask({ 'alias': 'numeric', 
        'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
        'digitsOptional': false, 
        'prefix': '', 
        'placeholder': '0'});

        $("#acompra").click(function() {
          $("#tab-compra").click();
        });
        
        $("#adepreciacion").click(function() {
          $("#tab-depreciacion").click();
        });
        
        $("#activoprincipal").prop('disabled', true);
        
        $("#tipoactivo").change(function() {
          if($("#tipoactivo option:selected").text()=='COMPUESTO'){
            $("#activoprincipal").prop('disabled', false);
            $("#base-calculo-compuesto").css('display', 'block');
            $("#base-calculo").css("display", "none");            
            if($("#canproducto").val() > 1){
              $("#itemple").prop('disabled', false);
              $(".datos-series").css('display', 'none');
            }
          } else {
            $("#activoprincipal").prop('disabled', true);
            $("#base-calculo-compuesto").css('display', 'none');
            $("#base-calculo").css('display', 'block');
            if($("#canproducto").val() > 1){
              $("#itemple").prop('disabled', true);
              $(".datos-series").css('display', 'block');
            }
          }
        });

      });
    </script> 

    <script src="{{ asset('public/js/catalogo/producto.js?v='.$version) }}" type="text/javascript"></script>

@stop