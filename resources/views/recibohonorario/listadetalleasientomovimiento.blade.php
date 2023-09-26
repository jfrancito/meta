<div style="text-align: right;"
          class='fila'
          data_id=''
          data_cuenta_id=''
          data_debe_mn=''
          data_haber_mn=''
          data_debe_me=''
          data_haber_me=''
          data_moneda = '{{$asiento->COD_CATEGORIA_MONEDA}}'
          data_registro = 'nuevo'

>
  <span class="mdi mdi-playlist-plus cursor editar-cuenta"></span>
</div>
<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;">

<table class="table table-condensed table-striped">
    <thead>
      <tr>
        <th>Linea</th>
        <th>Cuenta</th>
        <th>Glosa</th>
        <th>Debe MN</th>
        <th>Haber MN</th>
        <th>Debe ME</th>
        <th>Haber ME</th>
        <th>Editar</th>
      </tr>
    </thead>
    <tbody>
    @foreach($listaasientomovimiento as $index => $item)
        <tr class='fila'
          data_id='{{$item->COD_ASIENTO_MOVIMIENTO}}'
          data_cuenta_id='{{$item->COD_CUENTA_CONTABLE}}'
          data_debe_mn='{{$item->CAN_DEBE_MN}}'
          data_haber_mn='{{$item->CAN_HABER_MN}}'
          data_debe_me='{{$item->CAN_DEBE_ME}}'
          data_haber_me='{{$item->CAN_HABER_ME}}'
          data_moneda = '{{$asiento->COD_CATEGORIA_MONEDA}}'
          data_registro = 'editar'

          >
          <td>{{$item->NRO_LINEA}}</td>
          <td>{{$item->TXT_CUENTA_CONTABLE}}</td>
          <td>{{$item->TXT_GLOSA}}</td>
          <td>{{number_format($item->CAN_DEBE_MN, $redondeo, '.', ',')}}</td>
          <td>{{number_format($item->CAN_HABER_MN, $redondeo, '.', ',')}}</td>
          <td>{{number_format($item->CAN_DEBE_ME, $redondeo, '.', ',')}}</td>
          <td>{{number_format($item->CAN_HABER_ME, $redondeo, '.', ',')}}</td>
          <td><span class="mdi mdi-edit cursor editar-cuenta"></span></td>
        </tr>                  
    @endforeach
    </tbody>
    <tfoot>
      <tr>
        <th colspan="3">Totales</th>
        <th>{{number_format($listaasientomovimiento->sum("CAN_DEBE_MN"), $redondeo, '.', ',')}}</th>
        <th>{{number_format($listaasientomovimiento->sum("CAN_HABER_MN"), $redondeo, '.', ',')}}</th>
        <th>{{number_format($listaasientomovimiento->sum("CAN_DEBE_ME"), $redondeo, '.', ',')}}</th>
        <th>{{number_format($listaasientomovimiento->sum("CAN_HABER_ME"), $redondeo, '.', ',')}}</th>
        <th></th>
      </tr>
    </tfoot>
</table>

</div>