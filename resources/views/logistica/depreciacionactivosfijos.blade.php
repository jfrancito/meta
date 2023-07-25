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
          <div class="panel-heading panel-heading-divider">Panel de Depreciación de Activos Fijos
            <span class="panel-subtitle">Panel para la ejecución del proceso de Depreciación de Activos Fijos</span></div>
          <div  id="activos-fijos" class="panel-body">
            
                @if (isset($mensaje)) 
                <div class="alert {{ isset($alerta) ? $alerta : '' }} alert-dismissible">                          
                  {{ $mensaje }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endif

                <form method="POST"  action="{{ url('/depreciacion-activo-fijo/success') }}" style="border-radius: 0px;" class="form-horizontal group-border-dashed">
                    {{ csrf_field() }}

                    @include('logistica.form.depreciacion')

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
        
        $("#todos").change(function() {
          if($("#todos").prop('checked')){
            $("#activofijo").prop('disabled', true);
          } else {
            $("#activofijo").prop('disabled', false);
          }
        });
        
        $("#asientos").change(function() {
          if($("#asientos").prop('checked')){
            $("#todos").prop('checked', true);
            $("#unico").prop('checked', true);
            $("#procesado").prop('checked', true);
            $("#activofijo").prop('disabled', true);
            //$("#todos").prop('disabled', true);
            $("#ultimo").prop('disabled', true);
            $("#simulado").prop('disabled', true);
            var fecha = new Date();
            var mes = fecha.getMonth() + 1;
            $('#mes').val(mes);
            $('#mes').trigger('change');
            //$('#mes').prop('disabled', true);
          } else {
            //$("#todos").prop('checked', false);
            $("#activofijo").prop('disabled', false);
            $("#todos").prop('disabled', false);
            $("#ultimo").prop('disabled', false);
            $("#simulado").prop('disabled', false);
            //$('#mes').prop('disabled', false);
          }
        });

      });
    </script> 

    <script src="{{ asset('public/js/catalogo/producto.js?v='.$version) }}" type="text/javascript"></script>

@stop