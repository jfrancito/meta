@extends('template_lateral')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jqvmap/jqvmap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />
@stop

@section('section')

  <div class="be-content bienvenido">
    <div class="main-content container-fluid">
        <div class="row">

            <div class="col-xs-12 col-md-4">
              <div class="panel panel-default">
                <div class="panel-heading panel-heading-divider xs-pb-15">Documentos sin enviar a sunat</div>
                <div class="panel-body xs-pt-25">
                  @foreach($lista_documento_sin_enviar as $index => $item)
                                <div class="row user-progress user-progress-small">
                                  <div class="col-md-12"><span class="title">
                                      <a  href="#" 
                                          class='btndetallesunat' 
                                          data_empresa = "{{$item->COD_EMPR_EMISOR}}">{{$item->TXT_EMPR_EMISOR}} ({{$item->cantidad}})</a>
                                  </span></div>
                                </div>
                  @endforeach
                </div>
              </div>
            </div>

            <div class="col-xs-12 col-md-3">
              <div class="panel panel-default">
                <div class="panel-heading panel-heading-divider xs-pb-15">Diario Ventas</div>
                <div class="panel-body xs-pt-25">
                  <div class="row user-progress user-progress-small">
                    <div class="col-md-12"><span class="title">
                        <a href="{{ url('/gestion-observacion-documentos-ventas') }}">Ventas sin asiento ({{count($lista_ventas)}})</a>
                    </span></div>
                  </div>

                  <div class="row user-progress user-progress-small">
                    <div class="col-md-12"><span class="title">
                        <a href="{{ url('/gestion-configuracion-producto/1R') }}">Productos sin configurar ({{count($lista_productos_sc)}})</a>
                    </span></div>
                  </div>

                </div>
              </div>
            </div>

        </div>
    </div>
    @include('alerta.modal.malertas')
  </div>


@stop 

@section('script')

  <script src="{{ asset('public/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/dataTables.buttons.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/jszipoo.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/pdfmake.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/vfs_fonts.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.html5.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.flash.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.print.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.colVis.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.bootstrap.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/js/app-tables-datatables.js?v='.$version) }}" type="text/javascript"></script>

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
      App.dataTables();
      $('[data-toggle="tooltip"]').tooltip();
      $('form').parsley();

    });

  </script>
  <script src="{{ asset('public/js/alerta/alerta.js?v='.$version) }}" type="text/javascript"></script>

@stop
