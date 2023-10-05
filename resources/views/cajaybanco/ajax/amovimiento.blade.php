
<table id="tnso" class="table table-striped table-striped dt-responsive nowrap listatabla" style='width: 100%;'>

  <thead>
    <tr>
      <th>ITEM</th>
      <th>OPERACION</th>
      <th>FECHA OPERACION</th>      
      <th>DESCRIPCION</th>
      <th>INGRESO</th>
      <th>EGRESO</th>
      <th>AFECTA</th>
      <th>CAJA</th>

      <th>OBSERVACIONES</th>

    </tr>
  </thead>
  <tbody>
    @foreach($listamovimientos as $index => $item)

      <tr data_cod_operacion_caja = "{{$item->COD_OPERACION}}" 
          class='dobleclickmc seleccionar'
          style="cursor: pointer;"
        >
          <td>{{$index + 1}}</td>

          <td>{{$item->COD_OPERACION}}</td>
          <td>{{date_format(date_create($item->FEC_OPERACION), 'd-m-Y')}}</td>
          <td>{{$item->TXT_DESCRIPCION}}</td>
          <td>{{number_format($item->CAN_INGRESO, 2, '.', ',')}}</td>
          <td>{{number_format($item->CAN_EGRESO, 2, '.', ',')}}</td>
          <td>{{$item->EMPR_AFECTA}}</td>
          <td>{{$item->COD_CONCEPTO_CUENTA}}</td>  

          <td>{{$item->TXT_GLOSA}}</td>
 
      </tr>
    @endforeach
  </tbody>
</table>