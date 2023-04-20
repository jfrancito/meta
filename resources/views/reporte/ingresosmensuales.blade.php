@extends('template_lateral')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
@stop
@section('section')

  <div class="be-content contenido ingresosmensuales">
    <div class="main-content container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <div class="panel panel-default panel-border-color panel-border-color-success">
                <div class="panel-heading">Ingresos Mensuales
                  <div class="tools tooltiptop">

                    <div class="dropdown">
                      <span class="icon mdi mdi-more-vert dropdown-toggle" id="menudespacho" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></span>
                      <a href="#" class="tooltipcss opciones buscarim">
                        <span class="tooltiptext">Buscar Ingresos Mensuales</span>
                        <span class="icon mdi mdi-search"></span>
                      </a>

                      <ul class="dropdown-menu" aria-labelledby="menudespacho" style="margin: 7px -169px 0px;">

                        <li>
                          <a href="#" class='descargararchivo' data_archivo = 'registrocompra'><b>Ingresos Mensuales excel</b> <span class="mdi mdi-check-circle"></span></a>
                        </li>
                      </ul>
                    </div>

                  </div>
                  <span class="panel-subtitle">{{Session::get('empresas_meta')->NOM_EMPR}} </span>

                </div>

                <div class="panel-body">
                  <div class='filtrotabla row'>
                    <div class="col-xs-12">


                      <form method="POST"
                      id="formdescargar"
                      target="_blank"
                      action="{{ url('/descargar-ingresos-mensuales-excel') }}" 
                      style="border-radius: 0px;" 
                      >
                        {{ csrf_field() }}


                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte">
                            <div class="form-group">
                              <label class="col-sm-12 control-label labelleft" >Año :</label>
                              <div class="col-sm-12 abajocaja" >
                                {!! Form::select( 'anio', $combo_anio_pc, array(),
                                                  [
                                                    'class'       => 'select2 form-control control input-xs' ,
                                                    'id'          => 'anio',
                                                    'required'    => '',
                                                    'data-aw'     => '1',
                                                  ]) !!}
                              </div>
                            </div>
                        </div>


                        <div class='ajax_anio'>
                          @include('general.combo.cperiodotitulo')
                        </div>

                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte">
                            <div class="form-group">
                              <label class="col-sm-12 control-label labelleft" >Moneda :</label>
                              <div class="col-sm-12 abajocaja" >
                                {!! Form::select( 'moneda_id', $combo_moneda, array(),
                                                  [
                                                    'class'       => 'select2 form-control control input-xs' ,
                                                    'id'          => 'moneda_id',
                                                    'required'    => '',
                                                    'data-aw'     => '1',
                                                  ]) !!}
                              </div>
                            </div>
                        </div>



                        <div class='ajax_cuentas'>
                          @include('general.combo.ccuentastitulo')
                        </div>





                        <input type="hidden" name="idopcion" id='idopcion' value='{{$idopcion}}'>


                      </form>


                    </div>





                  </div>

                  <div class='listajax'>
                    
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
    $(".select3").select2({
        width: '100%'
    });

  </script>
  <script src="{{ asset('public/js/reporte/ingresosmensuales.js?v='.$version) }}" type="text/javascript"></script>

@stop