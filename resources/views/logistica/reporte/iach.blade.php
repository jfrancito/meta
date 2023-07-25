@extends('template_lateral')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
@stop
@section('section')



  <div class="be-content">
    <div class="main-content container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <div class="panel panel-default panel-table">


                <div class="panel-heading">Reporte Formato Contable IACH
                  <div class="tools tooltiptop">
                  </div>
                </div>
                <div class="panel-body selectfiltro">

                  <div class='filtrotabla row'>
                    <div class="col-xs-12">

                      <form method="POST"  action="{{ url('/exportar-formato-iach/') }}" style="border-radius: 0px;" class="form-horizontal group-border-dashed">
                        {{ csrf_field() }}
                      
                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 cajareporte">

                            <div class="form-group">
                                <label class="col-sm-12 control-label labelleft" >Año :</label>
                                <div class="col-sm-12 abajocaja" >
                                {!! Form::select( 'anio', $combo_anios, array(),
                                                    [
                                                    'class'       => 'select2 form-control control input-sm' ,
                                                    'id'          => 'anio',
                                                    'required'    => '',
                                                    'data-aw'     => '1',
                                                    ]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 cajareporte" style="padding-top: 28px;">

                            <div class="form-group">
                                <button type="submit"
                                class='tooltipcss'
                                id="iatr" 
                                title="Descargar Formato Contable IATR"
                                style="padding: 4px;">
                                <span class="tooltiptext">Descargar Formato Contable IACH</span>
                                <i class="fa fa-file-excel-o" style="font-size: 36px;"></i>
                                </button>
                            </div>
                        </div> 
                      
                    </form>

                    
                  </div>
                </div>
              </div>
            </div>
          </div>
    </div>
  </div>
</div>

@stop

@section('script')


  <script src="{{ asset('public/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/plugins/buttons/js/dataTables.buttons.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/lib/datatables/js/responsive.bootstrap.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/js/app-tables-datatables.js?v='.$version) }}" type="text/javascript"></script>


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
        App.dataTables();
        $('[data-toggle="tooltip"]').tooltip();
        
        /* $('#iatr').click(function(event){
          event.preventDefault();
          var anio = $('#anio').select2().val();
          $.post('/web/exportar-formato-iatr/', { 'anio' : anio }, function(){
            window.location.href = '/web/exportar-formato-iatr/';
          });
        }); */
      });

      
    </script> 

    <script src="{{ asset('public/js/reporte/regla.js?v='.$version) }}" type="text/javascript"></script> 
@stop