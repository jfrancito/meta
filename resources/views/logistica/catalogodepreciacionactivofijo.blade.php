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
            <div class="panel-heading panel-heading-divider">Catálogo Depreciación Activos Fijos<span class="panel-subtitle">Catálogo Depreciación Activos Fijos</span></div>

            {{-- {!! Session::get('empresas')->COD_EMPR !!}
            {!! Session::get('centros')->COD_CENTRO !!} --}}

            <!--Tabs-->
            <div class="row">
              <!--Success Tabs-->
              <div class="col-sm-12">
                <div class="panel panel-default">
                  <div class="panel-heading">Catálogo Depreciación Activos Fijos</div>


                  <div class="panel-body selectfiltro">

                    <div class='filtrotabla row'>
                      <div class="col-xs-12">
  
                        <form method="POST"  action="{{ url('/catalogo-activos-fijos/'.$idopcion.'/') }}" style="border-radius: 0px;" class="form-horizontal group-border-dashed">
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
                                  Cargar
                                  </button>
                              </div>
                          </div> 
                        
                        </form>
    
                        
                      </div>
                    </div>
                  </div>

                  <div class="tab-container">
                    <ul class="nav nav-tabs nav-tabs-success">
                      <li class="active"><a href="#activos-fijos" data-toggle="tab">Activos Fijos</a></li>
                    </ul>
                    <div class="tab-content">
                      <div id="activos-fijos" class="tab-pane active cont">

                        @if (session()->has('mensaje')) 
                          <div class="alert alert-success">                          
                            {{ session('mensaje') }}
                          </div>
                        @endif
                        {!! session()->forget('mensaje') !!}

                        <table id="table1" class="tablaprecio table table-striped table-striped dt-responsive nowrap listatabla" style='width: 100%;'>

                          <thead>
                            <tr>
                              <th>CÓDIGO</th>
                              <th>NOMBRE</th>
                             {{--  <th>CUENTA</th>
                              <th>BASE DE CÁLCULO</th> --}}
                              <th>FEC. INICIO DEP.</th>
                              <th>TASA</th>
                              <th>SALDO INCIAL</th>                            
                              <th>ENE</th>
                              <th>FEB</th>
                              <th>MAR</th>
                              <th>ABR</th>
                              <th>MAY</th>
                              <th>JUN</th>
                              <th>JUL</th>
                              <th>AGO</th>
                              <th>SET</th>
                              <th>OCT</th>
                              <th>NOV</th>
                              <th>DIC</th>
                              {{-- <th class='columna-success-titulo'>COSTO</th>
                              <th class='' style="text-align: center;">ACCI&Oacute;N</th> --}}
                            </tr>
                          </thead>
                          <tbody>

                            @foreach($catalogo as $item)
                              <tr data-id ="{{Hashids::encode(substr($item["item_ple"], -13))}}"
                                  data-pref ="{{substr($item["item_ple"],0,3)}}">
                                <td>{{$item["item_ple"]}}</td>
                                <td>{{strlen($item["nombre"])>60 ? substr($item["nombre"],0,60).'...' : $item["nombre"]}} </td>
                                {{-- <td>{{$item->cuenta}}</td>
                                <td>{{$item->base_de_calculo}}</td> --}}
                                <td>{{ date("d/m/Y",strtotime($item["fecha_inicio_depreciacion"]))}}</td>
                                <td>{{$item["tasa_depreciacion"]}}%</td>
                                <td>{{($item["monto_acumulado"] > 0) ? 'S/. ' .$item["monto_acumulado"] : ''}}</td>
                                {{-- <td>{{$item->NRO_SERIE}} - {{$item->NRO_DOC}}</td> --}}
                                <td>{{isset($item[1]) ? 'S/. ' .number_format($item[1],2) : ''}}</td>
                                <td>{{isset($item[2]) ? 'S/. ' . number_format($item[2],2) : ''}}</td>
                                <td>{{isset($item[3]) ? 'S/. ' . number_format($item[3],2) : ''}}</td>
                                <td>{{isset($item[4]) ? 'S/. ' . number_format($item[4],2) : ''}}</td>
                                <td>{{isset($item[5]) ? 'S/. ' . number_format($item[5],2) : ''}}</td>
                                <td>{{isset($item[6]) ? 'S/. ' . number_format($item[6],2) : ''}}</td>
                                <td>{{isset($item[7]) ? 'S/. ' . number_format($item[7],2) : ''}}</td>
                                <td>{{isset($item[8]) ? 'S/. ' . number_format($item[8],2) : ''}}</td>
                                <td>{{isset($item[9]) ? 'S/. ' . number_format($item[9],2) : ''}}</td>
                                <td>{{isset($item[10]) ? 'S/. ' . number_format($item[10],2) : ''}}</td>
                                <td>{{isset($item[11]) ? 'S/. ' . number_format($item[11],2) : ''}}</td>
                                <td>{{isset($item[12]) ? 'S/. ' . number_format($item[12],2) : ''}}</td>                                
                                {{-- <td class='columna-precio @if(is_null($item->CAN_PRECIO_UNIT_IGV)) columna-default @else columna-success @endif'>
                                  @if(is_null($item->CAN_PRECIO_UNIT_IGV))
                                      0.00
                                  @else 
                                      <i class="mdi mdi-check-circle"></i>
                                      {{$item->CAN_PRECIO_UNIT_IGV}}
                                  @endif
                                </td> --}}
                                <!-- <td class='columna-warning'>
                                  <input type="text"  
                                         id="precio" 
                                         name="precio"
                                         class="form-control input-sm dinero updateprice"
                                         >
                                </td> -->
                                {{-- <td style="text-align: center;"><a class="action-row" href="{{ url('/registrar-activo-fijo/'.$item->COD_PRODUCTO.'/'.$item->COD_DOCUMENTO_CTBLE) }}">Transferir</a></td> --}}
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
    <script src="{{ asset('public/js/app-tables-datatables.js') }}" type="text/javascript"></script>



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