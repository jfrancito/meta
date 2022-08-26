  <div class="be-content contenido kardex">
    <div class="main-content container-fluid">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <div class="tools tooltiptop">
                    <div class="dropdown">

                        <span class="icon mdi mdi-plus-circle-o dropdown-toggle negrita" id="menudespacho" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> Opciones</span>

                        <ul class="dropdown-menu" aria-labelledby="menudespacho" style="margin: 7px -169px 0px;">

                            <li>
                              <a href="#" class='crearcuentacontable' data_tabla = 'general' data_archivo = 'agregarmobilidadgeneral' >
                                <b>Crear Asiento Contable</b> <span class="mdi mdi-check-circle"></span>
                              </a>
                              <a href="#" class='quitardata' data_archivo = 'quitarmobilidad' data_tabla = 'general'>
                                <b>Quitar item</b> <span class="mdi mdi-close-circle"></span>
                              </a>
                            </li>

                        </ul>

                    </div>
                  </div>
                </div>
              </div>
    </div>
  </div>


<table id="item1" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatablageneral">
  <thead>
    <tr>
      <th>TRABAJADOR</th>
      <th>PERIODO</th>
      <th>FECHA LIQUIDACION</th>      
      <th>NRO LIQUIDACION</th>
      <th>TIPO DOCUMENTO</th>
      <th>FECHA DOCUMENTO</th>
      <th>NRO DOCUMENTO</th>
      <th>IMPORTE</th>
      <th>
<!--         <div class="text-center be-checkbox be-checkbox-sm has-primary">
          <input  type="checkbox"
                  class="todo_asignar_general input_asignar"
                  id="todo_asignar_general"
          >
          <label  for="todo_asignar_general"
                  data-atr = "todas_asignar"
                  class = "checkbox_asignar_otro" 
                  data_tabla = 'general'                   
                  name="todo_asignar_general"
            ></label>
        </div>
 -->      </th>
    </tr>
  </thead>
  <tbody>
    @foreach($listamovilidad_general as $index => $item)
    @if(in_array($item['COD_DOCUMENTO_CTBLE_MOVILIDAD'], $array_asoc_general))

      <tr data_documento_id = "{{$item['COD_DOCUMENTO_CTBLE_MOVILIDAD']}}"
          data_periodo_id = "{{$item['COD_PERIODO']}}">
          <td>{{$item['TXT_EMPR_RECEPTOR']}}</td>
          <td>{{$item['TXT_NOMBRE']}}</td>
          <td>{{date_format(date_create($item["FEC_EMISION"]), 'd-m-Y')}}</td>
          <td>{{$item['NUMERO']}}</td>
          <td>{{$item['TIPO_DOC']}}</td>
          <td>{{date_format(date_create($item["FEC_EMISION_DOC"]), 'd-m-Y')}}</td>
          <td>{{$item['NUMERO_DOC']}}</td>
          <td>{{number_format($item['IMPORTE'], 2, '.', ',')}}</td>
          <td>

              <div class="text-center be-checkbox be-checkbox-sm has-primary">
                <input  type="checkbox"
                  class="{{$item['COD_DOCUMENTO_CTBLE_MOVILIDAD']}}{{$index}} input_asignar"
                  id="{{$item['COD_DOCUMENTO_CTBLE_MOVILIDAD']}}{{$index}}" >

                <label  for="{{$item['COD_DOCUMENTO_CTBLE_MOVILIDAD']}}{{$index}}"
                      data-atr = "ver"
                      class = "checkbox checkbox_asignar_otro"                    
                      name="{{$item['COD_DOCUMENTO_CTBLE_MOVILIDAD']}}{{$index}}"
                ></label>
              </div>

          </td>
      </tr>  
    @endif

    @endforeach
  </tbody>
</table>