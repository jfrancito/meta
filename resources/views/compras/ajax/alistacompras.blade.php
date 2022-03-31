<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>NRO SERIE</th>
      <th>NRO DOCUMENTO</th>
      <th>PROVEEDOR</th>
      <th>TOTAL DEBE</th>
      <th>TOTAL HABER</th>
      <th>ESTADO</th>

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
    @foreach($listacompras as $index => $item)
      <tr data_asiento_id = "{{$item->COD_ASIENTO}}" 
        class='dobleclickpc seleccionar'
        style="cursor: pointer;">
        <td>{{$item->NRO_SERIE}}</td>
        <td>{{$item->NRO_DOC}}</td>
        <td class="cell-detail">
          <span>{{$item->TXT_EMPR_CLI}}</span>
          <span class="cell-detail-description"><b>FECHA EMISION : </b> {{date_format(date_create($item->FEC_ASIENTO), 'd-m-Y')}}</span>
          <span class="cell-detail-description"><b>TIPO DOCUMENTO : </b> {{$item->TXT_CATEGORIA_TIPO_DOCUMENTO}}</span>
          <span class="cell-detail-description"><b>MONEDA : </b> {{$item->TXT_CATEGORIA_MONEDA}}</span>
        </td>
        <td>{{number_format($item->CAN_TOTAL_DEBE, $redondeo, '.', ',')}}</td>
        <td>{{number_format($item->CAN_TOTAL_HABER, $redondeo, '.', ',')}}</td>
        <td>            
            @if($item->COD_CATEGORIA_ESTADO_ASIENTO == 'IACHTE0000000025') 
              <span class="badge badge-success">{{$item->TXT_CATEGORIA_ESTADO_ASIENTO}}</span> 
            @else
                <span class="badge badge-warning">{{$item->TXT_CATEGORIA_ESTADO_ASIENTO}}</span>
            @endif
        </td>        
        <td>
            <div class="text-center be-checkbox be-checkbox-sm has-primary">
              <input  type="checkbox"
                class="{{$item->COD_REFERENCIA}}{{$index}} input_asignar"
                id="{{$item->COD_REFERENCIA}}{{$index}}" >

              <label  for="{{$item->COD_REFERENCIA}}{{$index}}"
                    data-atr = "ver"
                    class = "checkbox checkbox_asignar"                    
                    name="{{$item->COD_REFERENCIA}}{{$index}}"
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