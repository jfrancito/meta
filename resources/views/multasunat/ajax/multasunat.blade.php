  <div class="be-content contenido kardex">
    <div class="main-content container-fluid">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <div class="tools tooltiptop">
                    <div class="dropdown">

                        <span class="icon mdi mdi-plus-circle-o dropdown-toggle negrita" id="menudespacho" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> Opciones</span>

                        <ul class="dropdown-menu" aria-labelledby="menudespacho" style="margin: 7px -169px 0px;">

                            <li>
                              <a href="#" class='crearcuentacontable' data_tabla = 'general' data_archivo = 'agregar-multa-sunat' >
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
        <th>MEDIO DE PAGO</th>
        <th>BANCO</th>
        <th>CUENTA</th>
        <th>FECHA</th>      
        <th>DESCRIPCI&Oacute;N</th>      
        <th>FLUJO</th>
        <th>MONTO</th>
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
      @foreach($lista_multa_sunat as $index => $item)
  
        <tr data_documento_id = "{{$item['COD_OPERACION_CAJA']}}"
            data_periodo_id = "{{$item['COD_PERIODO_CONCILIA']}}"
            data_cuenta_bancaria="{{$item['NRO_CUENTA_BANCARIA']}}"
            data_cod_banco="{{$item['COD_CAJA_BANCO']}}"
            data_nombre_banco="{{$item['TXT_BANCO']}}">
            <td>{{$item['TXT_CATEGORIA_MEDIO_PAGO']}}</td>
            <td>{{$item['TXT_BANCO']}}</td>
            <td>{{$item['NRO_CUENTA_BANCARIA']}}</td>
            <td>{{date_format(date_create($item["FEC_OPERACION"]), 'd-m-Y')}}</td>
            <td>{{$item['TXT_DESCRIPCION']}}</td>
            <td>{{$item['TXT_FLUJO_CAJA']}}</td>
            <td>S/. {{number_format($item['CAN_HABER_MN'], 2, '.', ',')}}</td>
            <td>
  
                <div class="text-center be-checkbox be-checkbox-sm has-primary">
                  <input  type="checkbox"
                    class="{{$item['COD_OPERACION_CAJA']}}{{$index}} input_asignar"
                    id="{{$item['COD_OPERACION_CAJA']}}{{$index}}" >
  
                  <label  for="{{$item['COD_OPERACION_CAJA']}}{{$index}}"
                        data-atr = "ver"
                        class = "checkbox checkbox_asignar"                    
                        name="{{$item['COD_OPERACION_CAJA']}}{{$index}}"
                  ></label>
                </div>
  
            </td>
        </tr>  
  
      @endforeach
    </tbody>
  </table>