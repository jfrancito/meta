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
            <div class="panel-heading panel-heading-divider">Almacén de Activos Fijos Transferidos<span class="panel-subtitle">Listado de productos transferidos al módulo.</span></div>


            <!--Tabs-->
            <div class="row">
              <!--Success Tabs-->
              <div class="col-sm-12">
                <div class="panel panel-default">
                  <div class="panel-heading">Lista de Productos del Almacén de Activos Fijos Transferidos</div>
                  <div id="activos-fijos" class="tab-container">
                    <ul class="nav nav-tabs nav-tabs-success">
                      <li class="active"><a href="#activos" data-toggle="tab">Activo Fijos</a></li>
                      <li><a href="#obras" data-toggle="tab">Obras</a></li>
                    </ul>
                    <div class="tab-content">
                      <div id="activos" class="tab-pane active cont">

                        @if (session()->has('mensaje')) 
                          <div class="alert alert-success">                          
                            {{ session('mensaje') }}
                          </div>
                        @endif
                        {!! session()->forget('mensaje') !!}

                        <table id="tableactivo" class="tablaprecio table table-striped table-striped dt-responsive nowrap listatabla" style='width: 100%;'>

                          <thead>
                            <tr>
                              <th>ITEM PLE</th>
                              <th>PRODUCTO</th>
                              <th>CANTIDAD</th>
                              <th>FEC. DOCUMENTO</th>
                              <th>DOCUMENTO</th>
                              <th>PROVEEDOR</th>
                              <th>DEPRECIACI&Oacute;N</th>
                              <th>ESTADO</th>
                              <th class='columna-success-titulo'>COSTO</th>
                              <th class='' style="text-align: center;">ACCI&Oacute;N</th>
                            </tr>
                          </thead>
                          <tbody>

                            @foreach($productos as $item)
                              <tr data-id ="{{Hashids::encode(substr($item->COD_PRODUCTO, -13))}}"
                                  data-pref ="{{substr($item->COD_PRODUCTO,0,3)}}">
                                <td>{{$item->item_ple}}</td>
                                <td>{{strlen($item->nombre)>60 ? substr($item->nombre,0,60).'...' : $item->nombre}} </td>
                                <td>{{$item->cantidad}}</td>
                                <td>{{ date("d/m/Y",strtotime($item->documento->FEC_EMISION))}}</td>
                                <td>{{$item->documento->NRO_SERIE}} - {{$item->documento->NRO_DOC}}</td>
                                <td>{{strlen($item->NOM_EMPR)>60 ? substr($item->empresa->NOM_EMPR, 0, 60).'...' : $item->empresa->NOM_EMPR}}</td>
                                <td>{{ $item->estado_depreciacion }}</td>
                                <td class="@if($item->estado == 'BAJA')  columna-warning-table @else columna-default @endif">{{$item->estado}}</td>                                
                                <td class='columna-precio @if(is_null($item->base_de_calculo)) columna-default @else columna-success @endif'>
                                  @if(is_null($item->base_de_calculo))
                                      0.00
                                  @else 
                                      <i class="mdi mdi-check-circle"></i>
                                      {{$item->base_de_calculo}}
                                  @endif
                                </td>
                                <!-- <td class='columna-warning'>
                                  <input type="text"  
                                         id="precio" 
                                         name="precio"
                                         class="form-control input-sm dinero updateprice"
                                         >
                                </td> -->
                                <td style="text-align: center;"><a class="action-row" href="{{ url('/modificar-activo-fijo/'.$item->id) }}">Editar</a></td>
                              </tr>                    
                            @endforeach

                          </tbody>
                        </table>


                      </div>
                      
                      <div id="obras" class="tab-pane cont">

                        @if (session()->has('mensaje')) 
                          <div class="alert alert-success">                          
                            {{ session('mensaje') }}
                          </div>
                        @endif
                        {!! session()->forget('mensaje') !!}

                        <table id="table-obras" class="tablaprecio table table-striped table-striped dt-responsive nowrap listatabla" style='width: 100%;'>

                          <thead>
                            <tr>
                              <th>ITEM PLE</th>                              
                              <th>PRODUCTO</th>
                              <th>FEC. INICIO DEPRECIACI&Oacute;N</th>
                              <th>DEPRECIACI&Oacute;N</th>
                              <th class='columna-success-titulo'>COSTO</th>
                              <th class='' style="text-align: center;">ACCI&Oacute;N</th>
                            </tr>
                          </thead>
                          <tbody>

                            @foreach($obras as $item)
                              <tr data-id ="{{Hashids::encode(substr($item->id, -13))}}"
                                  data-pref ="{{substr($item->id,0,3)}}">
                                <td>{{$item->item_ple}} </td>
                                <td>{{strlen($item->nombre)>60 ? substr($item->nombre,0,60).'...' : $item->nombre}} </td>
                                <td>{{ date("d/m/Y",strtotime($item->fecha_inicio_depreciacion))}}</td>
                                <td>{{ $item->estado_depreciacion }}</td>
                                <td class='columna-precio @if(is_null($item->base_de_calculo)) columna-default @else columna-success @endif'>
                                  @if(is_null($item->base_de_calculo))
                                      0.00
                                  @else 
                                      <i class="mdi mdi-check-circle"></i>
                                      {{$item->base_de_calculo}}
                                  @endif
                                </td>
                                <td style="text-align: center;"><a class="action-row" href="{{ url('/modificar-obra/'.$item->id) }}">Editar</a></td>
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