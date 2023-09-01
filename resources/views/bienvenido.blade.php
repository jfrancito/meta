@extends('template_lateral')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jqvmap/jqvmap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />
@stop

@section('section')

  <div class="be-content bienvenido registrodiario">
        <div class="main-content container-fluid">
          <div class="container">
            <div class="row">
              <div class="col-md-6">
                <div class="panel panel-default panel-table">
                  <div class="panel-heading"> 
                    <div class="tools tooltiptop">
                      <a href="#" class="tooltipcss opciones buscarobsasiento">
                        <span class="tooltiptext">Buscar</span>
                        <span class="icon mdi mdi-search"></span>
                      </a>
                    </div>

                    <div class="title">Observaciones de asientos</div>

                    {!! Form::select( 'anio', $combo_anio_pc, $anio,
                                      [
                                        'class'       => 'form-control control input-xs' ,
                                        'id'          => 'anio',
                                        'required'    => '',
                                        'data-aw'     => '1',
                                      ]) !!}

                  </div>
                  <div class="panel-body table-responsive listajax">
                    @include('usuario.ajax.alistaobservacionesasiento')
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
            </div>


            <div class="row">


              <div class="col-md-6">
                <div class="panel panel-default panel-table">
                  <div class="panel-heading"> 
                    <div class="title">Asientos con diferencias de monto</div>
                  </div>
                  <div class="panel-body table-responsive">
                    <table class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>Documento</th>
                          <th>Fecha</th>
                          <th>Tipo Asiento</th>
                        </tr>
                      </thead>
                      <tbody class="no-border-x">
                      @foreach($diferencimontos as $index => $item)
                        <tr data_asiento_id = "{{$item['COD_ASIENTO']}}" 
                            class='dobleclickpc seleccionar'>
                           <td>{{$item['NRO_SERIE']}} - {{$item['NRO_DOC']}}</td>
                           <td>{{$item['FEC_ASIENTO']}}</td>
                           <td>{{$item['TXT_CATEGORIA_TIPO_ASIENTO']}}</td>
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
                    <div class="title">Documentos con item con "0"</div>
                  </div>
                  <div class="panel-body table-responsive">
                    <table class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>Documento</th>
                          <th>Fecha</th>
                          <th>Tipo Asiento</th>
                        </tr>
                      </thead>
                      <tbody class="no-border-x">
                      @foreach($documentositemcero as $index => $item)
                        <tr data_asiento_id = "{{$item['COD_ASIENTO']}}" 
                            class='dobleclickpc seleccionar'>
                           <td>{{$item['NRO_SERIE']}} - {{$item['NRO_DOC']}}</td>
                           <td>{{$item['FEC_ASIENTO']}}</td>
                           <td>{{$item['TXT_CATEGORIA_TIPO_ASIENTO']}}</td>
                        </tr>                    
                      @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>





            </div>



            <div class='row'>



              <div class="col-md-6">
                <div class="panel panel-default panel-table">
                  <div class="panel-heading"> 
                    <div class="title">Asientos con diferencias en el destino</div>
                  </div>
                  <div class="panel-body table-responsive">
                    <table class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>Documento</th>
                          <th>Fecha</th>
                          <th>Tipo Asiento</th>
                        </tr>
                      </thead>
                      <tbody class="no-border-x">
                      @foreach($documentosdiferenciadestino as $index => $item)
                        <tr data_asiento_id = "{{$item['COD_ASIENTO']}}" 
                            class='dobleclickpc seleccionar'>
                           <td>{{$item['NRO_SERIE']}} - {{$item['NRO_DOC']}}</td>
                           <td>{{$item['FEC_ASIENTO']}}</td>
                           <td>{{$item['TXT_CATEGORIA_TIPO_ASIENTO']}}</td>
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
        </div>

    @include('registrodiario.modal.mregistrodiario')    
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
  <script src="{{ asset('public/js/registrodiario/registrodiario.js?v='.$version) }}" type="text/javascript"></script>

@stop
