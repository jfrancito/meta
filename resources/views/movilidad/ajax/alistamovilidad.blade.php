<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
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
    @foreach($listamovilidad as $index => $item)
      <tr>
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
                      class = "checkbox checkbox_asignar"                    
                      name="{{$item['COD_DOCUMENTO_CTBLE_MOVILIDAD']}}{{$index}}"
                ></label>
              </div>

          </td>
        
      </tr>                    
    @endforeach
  </tbody>
</table>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif