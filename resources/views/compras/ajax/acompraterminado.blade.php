<table id="item1" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>NRO SERIE</th>
      <th>NRO DOCUMENTO</th>
      <th>PROVEEDOR</th>
      <th>TOTAL DEBE</th>
      <th>TOTAL HABER</th>
      <th>ESTADO</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listacomprasterminado as $index => $item)
      <tr data_asiento_id = "{{$item->COD_ASIENTO}}" 
        class='dobleclickpcreversion seleccionar'
        style="cursor: pointer;">
        <td>{{$item->NRO_SERIE}}</td>
        <td>{{$item->NRO_DOC}}</td>
        <td class="cell-detail">
          <span>{{$item->TXT_EMPR_CLI}}</span>
          <span class="cell-detail-description"><b>FECHA EMISION : </b> {{date_format(date_create($item->FEC_ASIENTO), 'd-m-Y')}}</span>
          <span class="cell-detail-description"><b>TIPO DOCUMENTO : </b> {{$item->TXT_CATEGORIA_TIPO_DOCUMENTO}}</span>
          <span class="cell-detail-description"><b>MONEDA : </b> {{$item->TXT_CATEGORIA_MONEDA}}</span>
          <span class="cell-detail-description"><b>TIPO ASIENTO : </b> {{$item->TXT_CATEGORIA_TIPO_ASIENTO}}</span>
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
      </tr>                    
    @endforeach
  </tbody>
</table>