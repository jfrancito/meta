  <div class="be-content contenido kardex">
    <div class="main-content container-fluid">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <div class="tools tooltiptop">
                    <div class="dropdown">

                        <span class="icon mdi mdi-plus-circle-o dropdown-toggle negrita" id="menudespacho" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> Opciones</span>

                        <ul class="dropdown-menu" aria-labelledby="menudespacho" style="margin: 7px -169px 0px;">

                            <li>
                              <a href="#" class='crearcuentacontable' data_tabla = 'general' data_archivo = 'agregar-leasing' >
                                <b>Crear Asiento Contable</b> <span class="mdi mdi-check-circle"></span>
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
        <th>
          <div class="text-center be-checkbox be-checkbox-sm has-primary">
            <input  type="checkbox"
                    class="todo_asignar input_asignar"
                    id="todo_asignar"
            >
            <label  for="todo_asignar"
                    data-atr = "todas_asignar"
                    class = "checkbox_asignar"                    
                    name="todo_asignar"
              ></label>
          </div>
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach($lista_leasing as $index => $item)
  
        <tr data_cod_pagare = "{{$item->COD_PAGARE}}"
            data_cod_detalle_pagare = "{{$item->COD_DETALLE}}"
            {{-- data_periodo_id = "{{$item->COD_PERIODO_CONCILIA}}" --}}
            data_cod_banco="{{$item->COD_EMPR_BANCO}}"
            data_nombre_banco="{{$item->TXT_EMPR_BANCO}}">
            <td>{{$item->TXT_EMPR_BANCO}}</td>
            <td>{{date_format(date_create($item->FEC_CUOTA), 'd-m-Y')}}</td>
            <td>{{date_format(date_create($item->FEC_VENCIMIENTO), 'd-m-Y')}}</td>          
            <td><a class="action-row" href="{{ url('/detalle-intereses-prestamo/'.$item->COD_PAGARE) }}" target="blank">{{$item->TXT_GLOSA}}</a></td>
            <td>{{number_format($item->CAN_MONTO_MN, 2, '.', ',')}}</td>
            <td>{{number_format($item->CAN_CUOTA_MN, 2, '.', ',')}}</td>
            <td>{{number_format($item->CAN_INT_MENSUAL_MN, 2, '.', ',')}}</td>
            <td>
  
                <div class="text-center be-checkbox be-checkbox-sm has-primary">
                  <input  type="checkbox"
                    class="{{$item->COD_DETALLE}}{{$index}} input_asignar"
                    id="{{$item->COD_DETALLE}}{{$index}}" >
  
                  <label  for="{{$item->COD_DETALLE}}{{$index}}"
                        data-atr = "ver"
                        class = "checkbox checkbox_asignar"                    
                        name="{{$item->COD_DETALLE}}{{$index}}"
                  ></label>
                </div>
  
            </td>
        </tr>  
  
      @endforeach
    </tbody>
  </table>