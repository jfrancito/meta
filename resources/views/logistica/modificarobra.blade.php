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
          <div class="panel-heading panel-heading-divider">Modificación de Activo Fijo
            <span class="panel-subtitle">Modificación de Activo Fijo</span>
          </div>
          <div  id="activos-fijos" class="panel-body">
            @if (isset($mensaje) && $mensaje != '') 
            <div class="alert {{ isset($alerta) ? $alerta : '' }} alert-dismissible">                          
              {{ $mensaje }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif                      

                <form method="POST"  action="{{ url('/modificar-obra/'.$activofijo->id) }}" style="border-radius: 0px;" class="form-horizontal group-border-dashed">
                    {{ csrf_field() }}

                    @include('logistica.form.obra')

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
          } else {
            $("#activoprincipal").prop('disabled', true);
          }
        });

      });
    </script> 

    <script src="{{ asset('public/js/catalogo/producto.js?v='.$version) }}" type="text/javascript"></script>

@stop