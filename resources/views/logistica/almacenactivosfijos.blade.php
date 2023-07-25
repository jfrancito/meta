@extends('template_lateral')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datatables/css/dataTables.bootstrap.min.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/activos-fijos.css') }} "/>
@stop

@section('section')

<div class="be-content precioproducto">
    <div class="main-content container-fluid">
      <div class="row">
        <div class="col-sm-12">
          <!--Dropdowns-->
          <div class="panel panel-default">
            <div class="panel-heading panel-heading-divider">Almacén de Activos Fijos<span class="panel-subtitle">Listado de productos pendientes de registrar en el módulo.</span></div>

            {{-- {!! Session::get('empresas')->COD_EMPR !!}
            {!! Session::get('centros')->COD_CENTRO !!} --}}

            <!--Tabs-->
            <div class="row">
              <!--Success Tabs-->
              <div class="col-sm-12">
                <div class="panel panel-default">
                  <div class="panel-heading">Lista de Productos del Almacén de Activos Fijos</div>
                  <div id="activos-fijos" class="tab-container">
                    <ul class="nav nav-tabs nav-tabs-success">
                      <li class="active"><a href="#activos" data-toggle="tab">Activos Fijos por Transferir</a></li>
                      <li><a href="{{ url('/registrar-obra-activo-fijo/')}}"><b>Crear Obra</b></a></li>
                    </ul>
                    <div class="tab-content">
                      <div class="tab-pane active cont">

                        @if (session()->has('mensaje')) 
                          <div class="alert alert-success">                          
                            {{ session('mensaje') }}
                          </div>
                        @endif
                        {!! session()->forget('mensaje') !!}

                        <table id="tableactivo" class="tablaprecio table table-striped table-striped dt-responsive nowrap listatabla" style='width: 100%;'>

                          <thead>
                            <tr>
                              <th>PRODUCTO</th>
                              <th>CANTIDAD</th>
                              <th>FEC. DOCUMENTO</th>
                              <th>DOCUMENTO</th>
                              <th>PROVEEDOR</th>
                              <th class='columna-success-titulo'>COSTO</th>
                              <th class='' style="text-align: center;">ACCI&Oacute;N</th>
                            </tr>
                          </thead>
                          <tbody>

                            @foreach($productos as $item)
                              <tr data-id ="{{Hashids::encode(substr($item->COD_PRODUCTO, -13))}}"
                                  data-pref ="{{substr($item->COD_PRODUCTO,0,3)}}">
                                <td>{{strlen($item->NOM_PRODUCTO)>60 ? substr($item->NOM_PRODUCTO,0,60).'...' : $item->NOM_PRODUCTO}} </td>
                                <td>{{$item->CAN_PRODUCTO}}</td>
                                <td>{{ date("d/m/Y",strtotime($item->FEC_EMISION))}}</td>
                                <td>{{$item->NRO_SERIE}} - {{$item->NRO_DOC}}</td>
                                <td>{{strlen($item->NOM_EMPR)>60 ? substr($item->NOM_EMPR, 0, 60).'...' : $item->NOM_EMPR}}</td>
                                <td class='columna-precio @if(is_null($item->CAN_PRECIO_UNIT)) columna-default @else columna-success @endif'>
                                    @if(is_null($item->CAN_PRECIO_UNIT))
                                        0.00
                                    @else 
                                        <i class="mdi mdi-check-circle"></i>
                                        {{$item->CAN_PRECIO_UNIT}}
                                    @endif
                                </td>                                
                                <td style="text-align: center;"><a class="action-row" href="{{ url('/registrar-activo-fijo/'.$item->COD_PRODUCTO.'/'.$item->COD_DOCUMENTO_CTBLE) }}">Transferir</a></td>
                              </tr>                    
                            @endforeach

                          </tbody>
                        </table>


                      </div>
                      
                      <!-- -->

                    </div>
                  </div>
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

<script src="{{ asset('public/js/general/inputmask/inputmask.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/inputmask.extensions.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/inputmask.numeric.extensions.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/inputmask.date.extensions.js') }}" type="text/javascript"></script> 
    <script src="{{ asset('public/js/general/inputmask/jquery.inputmask.js') }}" type="text/javascript"></script>

    <script src="{{ asset('public/lib/datatables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/js/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/dataTables.buttons.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.html5.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.flash.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.print.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.colVis.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/lib/datatables/plugins/buttons/js/buttons.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/app-tables-datatables.js?v='.$version) }}" type="text/javascript"></script>



    <script type="text/javascript">
      $(document).ready(function(){
        //initialize the javascript
        App.init();
        App.dataTables();
        $('[data-toggle="tooltip"]').tooltip(); 

        $('.dinero').inputmask({ 'alias': 'numeric', 
        'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
        'digitsOptional': false, 
        'prefix': '', 
        'placeholder': '0'});

      });
    </script>


    <script src="{{ asset('public/js/catalogo/producto.js?v='.$version) }}" type="text/javascript"></script> 
    
@stop