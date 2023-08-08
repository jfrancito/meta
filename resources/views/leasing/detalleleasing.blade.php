@extends('template_lateral')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
@stop
@section('section')

  <div class="be-content contenido movilidad">
    <div class="main-content container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <div class="panel panel-default panel-border-color panel-border-color-success">
                <div class="panel-heading">Detalle Intereses por Pr&eacute;stamo
                  <div class="tools tooltiptop">
                    <a href="#" class="tooltipcss opciones buscar-intereses-prestamo">
                      <span class="tooltiptext">Buscar Intereses Pr&eacute;stamo</span>
                      <span class="icon mdi mdi-search"></span>
                    </a>
                  </div>
                  <span class="panel-subtitle">{{Session::get('empresas_meta')->NOM_EMPR}} </span>

                </div>

                <div class="panel-body">
                  <div class='filtrotabla row'>

                  </div>


                  <div class='listajax'>
                    
                    <div class="panel panel-default">
                      <div class="tab-container">
                        <ul class="nav nav-tabs">
                          <li class="active"><a href="#intereses-prestamo" data-toggle="tab">Detalle Pagos Intereses Pr&eacute;stamo</a></li>
                        </ul>
                    
                        <div class="tab-content">
                          <div id="intereses-prestamo" class="tab-pane active cont">
                            <div class="be-content contenido kardex">
                              <div class="main-content container-fluid">
                                        <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <div class="tools tooltiptop">
                                              <div class="dropdown">                    
                          
                                                  <ul class="dropdown-menu" aria-labelledby="menudespacho" style="margin: 7px -169px 0px;">
                          
                                                      <li>
                                                        <a href="#" class='crearcuentacontable' data_tabla = 'general' data_archivo = 'agregar-intereses-prestamo' >
                                                        </a>
                                                      </li>
                          
                                                  </ul>
                          
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                              </div>
                            </div>
                            
                            
                            <table id="dtitf" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatablageneral">
                                <thead>
                                <tr>
                                  <th>BANCO</th>
                                  <th>FECHA</th>      
                                  <th>VENCIMIENTO</th>      
                                  <th>DESCRIPCI&Oacute;N</th>      
                                  <th>PR&Eacute;STAMO</th>
                                  <th>CUOTA MES</th>
                                  <th>INTER&Eacute;S MES</th>
                                  <th>INTER&Eacute;S MES ACTUAL</th>
                                  <th>INTER&Eacute;S MES SIGUIENTE</th>
                                  <th>INTER&Eacute;S A PAGAR</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($lista_detalle_prestamo as $index => $item)
                            
                                  <tr>
                                      <td>{{$item['TXT_EMPR_BANCO']}}</td>
                                      <td>{{date_format(date_create($item["FEC_CUOTA"]), 'd-m-Y')}}</td>
                                      <td>{{date_format(date_create($item["FEC_VENCIMIENTO"]), 'd-m-Y')}}</td>          
                                      <td>{{$item['NRO_PAGARE']}}</td>
                                      <td>{{number_format($item['CAN_MONTO_MN'], 2, '.', ',')}}</td>
                                      <td>{{number_format($item['CAN_CUOTA_MN'], 2, '.', ',')}}</td>
                                      <td>{{number_format($item['CAN_INT_MENSUAL_MN'], 2, '.', ',')}}</td>
                                      <td>{{number_format($item['INTERES_MES_ACTUAL'], 2, '.', ',')}}</td>
                                      <td>{{number_format($item['INTERES_MES_SIGUIENTE'], 2, '.', ',')}}</td>
                                      <td>{{number_format($item['INTERES_A_PAGAR'], 2, '.', ',')}}</td>
                                  </tr>  
                            
                                @endforeach
                              </tbody>
                            </table>
                          </div>
                        </div>
                        
                      </div>
                    </div>
                    
                    @if(isset($ajax))
                      <script type="text/javascript">
                        $(document).ready(function(){
                           App.dataTables();
                        });
                      </script> 
                    @endif

                  </div>


                </div>
              </div>
            </div>
          </div>
    </div>
    @include('interesesprestamo.modal.minteresesprestamocc')
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
  <script src="{{ asset('public/js/intereses-prestamo/intereses-prestamo.js?v='.$version) }}" type="text/javascript"></script>

@stop