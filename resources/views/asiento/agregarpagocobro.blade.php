@extends('template_lateral')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>

@stop
@section('section')

<div class="be-content asiento">
  <div class="main-content container-fluid">

    <!--Basic forms-->
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default panel-border-color panel-border-color-primary">
          <div class="panel-heading panel-heading-divider">Pago y Cobro

            <div class="tools tooltiptop">
              <a href="#" class="tooltipcss opciones agregardetalleasiento">
                <span class="tooltiptext">Agregar pago y cobro</span>
                <span class="icon glyphicon glyphicon-plus-sign"></span>
              </a>
            </div>

            <span class="panel-subtitle">Crear un nuevo Asiento</span>
          </div>
          <div class="panel-body">
            <form method="POST" 
            id='formasiento'
            action="{{ url('/gestion-pago-cobro/'.$idopcion) }}" 
            style="border-radius: 0px;" 
            class="form-horizontal group-border-dashed">
                  {{ csrf_field() }}
              @include('asiento.form.fpagocobro')
            </form>
          </div>
          @include('asiento.modal.mdetalleasiento')
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
  <script src="{{ asset('public/lib/jquery.niftymodals/dist/jquery.niftymodals.js') }}" type="text/javascript"></script>

  <script type="text/javascript">

    $.fn.niftyModal('setDefaults',{
      overlaySelector: '.modal-overlay',
      closeSelector: '.modal-close',
      classAddAfterOpen: 'modal-show',
    });

    $(document).ready(function(){
      //initialize the javascript
      App.init();
      App.formElements();
      $('form').parsley();




      $('.dinero').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});

      $('.datetimepicker2').datetimepicker({
        autoclose: true,
        pickerPosition: "bottom-left",
        componentIcon: '.mdi.mdi-calendar',
        navIcons:{
          rightIcon: 'mdi mdi-chevron-right',
          leftIcon: 'mdi mdi-chevron-left'
        },

      })
      .on('changeDate', function (ev) {
        event.preventDefault();
        var fechadocumento       =   $('#fechadocumento').val();
        var _token               =   $('#token').val();

        data                    =   {
                                        _token               : _token,
                                        fechadocumento       : fechadocumento
                                    };
        ajax_normal_combo(data,"/ajax-input-tipo-cambio","ajax_tipocambio");
      });



    });


      $(".select3").select2({
        width: '100%'
      });
    
  </script> 

  <script src="{{ asset('public/js/asiento/asiento.js?v='.$version) }}" type="text/javascript"></script>

@stop