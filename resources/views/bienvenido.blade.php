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
            <div class="col-xs-12 col-md-6 col-lg-3">
                <div class="widget widget-tile">
                  <div id="spark1" class="chart sparkline"></div>
                  <div class="data-info">
                    <div class="desc">Ventas sin asiento</div>
                    <div class="value">
                        <a href="{{ url('/gestion-observacion-documentos-ventas') }}">
                            <span class="indicator indicator-equal mdi mdi-chevron-right">
                            </span><span data-toggle="counter" data-end="{{count($lista_ventas)}}" class="number">0</span>
                        </a>
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
