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
            <div class="col-md-6">
              <div class="panel panel-default panel-table">
                <div class="panel-heading"> 
                  <div class="title">Observaciones de asientos</div>
                </div>
                <div class="panel-body table-responsive">
                  <table class="table table-striped table-borderless">
                    <thead>
                      <tr>
                        <th>Tipo de asiento</th>
                        <th>Observación</th>
                        <th>Cantidad</th>
                      </tr>
                    </thead>
                    <tbody class="no-border-x">
                      <tr>
                        <td>Ventas</td>
                        <td>Ventas sin asientos</td>
                        <td class="actions">
                          <a href="{{ url('/gestion-observacion-documentos/3') }}">
                          <span class="badge badge-primary">{{count($lista_ventas)}}</span>
                          </a>
                        </td>
                      </tr>

                      <tr>
                        <td>Ventas</td>
                        <td>Productos sin configurar</td>
                        <td class="actions">
                          <a href="{{ url('/gestion-configuracion-producto/1R/3') }}">
                          <span class="badge badge-primary">{{count($lista_productos_sc)}}</span>
                          </a>
                        </td>
                      </tr>


                      <tr>
                        <td>Compras</td>
                        <td>Compras sin asientos</td>
                        <td class="actions">
                          <a href="{{ url('/gestion-observacion-documentos/4') }}">
                          <span class="badge badge-success">{{count($lista_compras)}}</span>
                          </a>
                        </td>
                      </tr>

                      <tr>
                        <td>Compras</td>
                        <td>Productos sin configurar</td>
                        <td class="actions">
                          <a href="{{ url('/gestion-configuracion-producto/1R/4') }}">
                          <span class="badge badge-success">{{count($lista_productos_sc_comp)}}</span>
                          </a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="panel panel-default panel-table">
                <div class="panel-heading"> 
                  <div class="title">Documentos sin enviar a sunat</div>
                </div>
                <div class="panel-body table-responsive">
                  <table class="table table-striped table-borderless">
                    <thead>
                      <tr>
                        <th>Empresa</th>
                        <th>Cantidad</th>
                      </tr>
                    </thead>
                    <tbody class="no-border-x">

                    @foreach($lista_documento_sin_enviar as $index => $item)
                        <tr>
                          <td>{{$item->TXT_EMPR_EMISOR}}</td>
                          <td class="actions">
                              <a  href="#" 
                                  class='btndetallesunat' 
                                  data_empresa = "{{$item->COD_EMPR_EMISOR}}"><span class="badge badge-primary">{{$item->cantidad}}</span></a>
                          </td>
                        </tr>
                    @endforeach

                    </tbody>
                  </table>
                </div>
              </div>
            </div>



            <div class="col-md-6">
              <div class="panel panel-default panel-table">
                <div class="panel-heading"> 
                  <div class="title">Correlativos Faltantes</div>
                </div>
                <div class="panel-body table-responsive">
                  <table class="table table-striped table-borderless">
                    <thead>
                      <tr>
                        <th>Observación</th>
                      </tr>
                    </thead>
                    <tbody class="no-border-x">
                        @foreach($lista_documento_correlativo as $index => $item)
                          @if($item->DIFERENCIA > 0)  
                          <tr>
                            <td class="actions">
                                  <a  href="#" 
                                      class='btndetallecorrelativo' 
                                      data_empresa = "{{$item->COD_EMPR}}"
                                      data_empresa_txt = "{{$item->TXT_EMPR_EMISOR}}"
                                      data_categoria = "{{$item->COD_CATEGORIA}}"
                                      data_categoria_txt = "{{$item->NOM_CATEGORIA}}"
                                      data_serie = "{{$item->NRO_SERIE}}"
                                      data_min_doc = "{{$item->MINDOC}}"
                                      data_max_doc = "{{$item->MANDOC}}"
                                      >{{$item->TXT_EMPR_EMISOR}} - {{$item->NOM_CATEGORIA}} - {{$item->NRO_SERIE}} ({{$item->DIFERENCIA}})</a>
                            </td>
                          </tr>
                          @endif
                        @endforeach
                    </tbody>
                  </table>
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
