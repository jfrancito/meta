<input type="hidden" name="array_cp" id='array_cp' value='{{json_encode($array_cp)}}'>
<table class="table table-striped table-borderless table-detalle-asientos-p-c">
  <thead>
    <tr>
      <th>Fecha</th>
      <th>Cuenta</th>
      <th>Tipo Documento</th>
      <th>Serie</th>
      <th>Numero</th>
      <th>Glosa</th>
      <th>Saldo</th>
      <th>Abono</th>

    </tr>
  </thead>
  <tbody >
    @foreach($lista_asiento as $index => $item)
        <tr data_asiento_movimiento = "{{$item->COD_ASIENTO_MOVIMIENTO}}" 
          data_txt_documento = "{{$item->TXT_CATEGORIA_TIPO_DOCUMENTO}}"
          data_moneda = "{{$item->TXT_CATEGORIA_MONEDA}}"
          data_serie = "{{$item->NRO_SERIE}}"
          data_nro = "{{$item->NRO_DOC}}"
          data_cliente = "{{$item->TXT_EMPR_CLI}}"
          class="fila_asiento_p_c">
          <td>{{$item->FEC_ASIENTO}}</td>
          <td>{{$item->nro_cuenta}}</td>
          <td>{{$item->TXT_CATEGORIA_TIPO_DOCUMENTO}}</td>
          <td>{{$item->NRO_SERIE}}</td>
          <td>{{$item->NRO_DOC}}</td>
          <td>{{$item->TXT_GLOSA}}</td>
          <td>{{$item->CAN_TOTAL_DEBE}}</td>
          <td>{{$item->CAN_TOTAL_DEBE}}</td>
        </tr>                  
    @endforeach
  </tbody>
</table>