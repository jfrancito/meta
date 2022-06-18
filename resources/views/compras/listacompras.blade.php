@extends('template_lateral')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/responsive.dataTables.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/select2/css/select2.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/bootstrap-slider/css/bootstrap-slider.css') }} "/>
@stop
@section('section')

  <div class="be-content contenido compras">
    <div class="main-content container-fluid">
          <div class="row">
            <div class="col-sm-12">
              <div class="panel panel-default panel-border-color panel-border-color-success">
                <div class="panel-heading">Listado de compras
                  <div class="tools tooltiptop">

                    <a href="#" class="tooltipcss opciones buscarcompras">
                      <span class="tooltiptext">Buscar Compras</span>
                      <span class="icon mdi mdi-search"></span>
                    </a>

<!--                     <form method="POST" id='formgenerarasiento' action="{{ url('/asiento-contables-confirmado-xdocumentos') }}" style="display: inline-block;" >
                      {{ csrf_field() }}
                      <input type="hidden" id='documentos' name='documentos' >
                      <input type="hidden" name="idopcion" id='idopcion' value='{{$idopcion}}'>
                      <input type="hidden" id='periodo_id_confirmar' name='periodo_id_confirmar' >
                      <input type="hidden" id='nro_serie_confirmar' name='nro_serie_confirmar' >
                      <input type="hidden" id='nro_doc_confirmar' name='nro_doc_confirmar' >
                      <input type="hidden" id='anio_confirmar' name='anio_confirmar' >


                      <a href="#" class="tooltipcss" id='confirmarasientos' >
                        <span class="tooltiptext">Confirmar Asiento Contable</span>
                        <span class="icon mdi mdi-mail-send"></span>
                      </a>
                    </form>
 -->
                  </div>
                  <span class="panel-subtitle">{{Session::get('empresas_meta')->NOM_EMPR}}</span>
                </div>

                <div class="panel-body">
                  <div class='filtrotabla row'>
                    <div class="col-xs-12">


                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte">
                          <div class="form-group">
                            <label class="col-sm-12 control-label labelleft" >Año :</label>
                            <div class="col-sm-12 abajocaja" >
                              {!! Form::select( 'anio', $combo_anio_pc, $anio,
                                                [
                                                  'class'       => 'select2 form-control control input-xs' ,
                                                  'id'          => 'anio',
                                                  'required'    => '',
                                                  'data-aw'     => '1',
                                                ]) !!}
                            </div>
                          </div>
                      </div>


                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte ajax_anio">
                          @include('general.combo.cperiodo', ['sel_periodo' => $sel_periodo])

                      </div>

                                            <input type="hidden" name="idopcion" id='idopcion' value='{{$idopcion}}'>
                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte">
                      <div class="form-group">
                        <label class="col-sm-12 control-label labelleft" >Nro Serie</label>
                        <div class="col-sm-12">

                            <input  type="text"
                                    id="serie" name='serie' 
                                    value="{{$sel_serie}}"
                                    placeholder="Nro Serie"
                                    required = ""
                                    autocomplete="off" class="form-control input-sm" data-aw="1"/>

                        </div>
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte">
                      <div class="form-group">
                        <label class="col-sm-12 control-label labelleft" >Nro Documento</label>
                        <div class="col-sm-12">

                            <input  type="text"
                                    id="documento" name='documento' 
                                    value="{{$sel_nrodoc}}"
                                    placeholder="Nro Documento"
                                    required = ""
                                    autocomplete="off" class="form-control input-sm" data-aw="1"/>

                        </div>
                      </div>
                    </div>




                    </div>

                  </div>

                      <div class='listajax'>
                        @include('compras.ajax.alistacompras')
                      </div>
                </div>

              </div>
            </div>
          </div>
    </div>
    @include('compras.modal.mcompras')
  </div>

@stop

@section('script')

  <script src="{{ asset('public/js/general/inputmask/inputmask.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/general/inputmask/inputmask.extensions.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/general/inputmask/inputmask.numeric.extensions.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/general/inputmask/inputmask.date.extensions.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('public/js/general/inputmask/jquery.inputmask.js') }}" type="text/javascript"></script>


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

      $('.dinero').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});

    });

  </script>
  <script src="{{ asset('public/js/compras/compras.js?v='.$version) }}" type="text/javascript"></script>

@stop