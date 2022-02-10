@extends('template_lateral')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jqvmap/jqvmap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />
@stop

@section('section')

  <div class="be-content">
    <div class="main-content container-fluid">
        <div class="row">

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
  </div>


@stop 

@section('script')

    <script src="{{ asset('public/lib/jquery-flot/jquery.flot.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery-flot/jquery.flot.pie.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery-flot/jquery.flot.resize.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery-flot/plugins/jquery.flot.orderBars.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery-flot/plugins/curvedLines.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery.sparkline/jquery.sparkline.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/countup/countUp.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jqvmap/jquery.vmap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/jqvmap/maps/jquery.vmap.world.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/app-dashboard.js') }}" type="text/javascript"></script>


    <script type="text/javascript">
      $(document).ready(function(){
        App.init();
        App.dashboard();
      });
    </script>   

@stop
