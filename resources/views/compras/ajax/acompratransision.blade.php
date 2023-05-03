<table id="tran" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>NRO SERIE</th>
      <th>NRO DOCUMENTO</th>
      <th>PROVEEDOR</th>
      <th>TOTAL DEBE</th>
      <th>TOTAL HABER</th>
      <th>ESTADO</th>
      <th>ASIENTO</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listacompras as $index => $item)
      <tr>
        <td>{{$item->NRO_SERIE}}</td>
        <td>{{$item->NRO_DOC}}</td>
        <td class="cell-detail">
          <span>{{$item->TXT_EMPR_CLI}}</span>
          <span class="cell-detail-description"><b>FECHA EMISION : </b> {{date_format(date_create($item->FEC_ASIENTO), 'd-m-Y')}}</span>
          <span class="cell-detail-description"><b>TIPO DOCUMENTO : </b> {{$item->TXT_CATEGORIA_TIPO_DOCUMENTO}}</span>
          <span class="cell-detail-description"><b>MONEDA : </b> {{$item->TXT_CATEGORIA_MONEDA}}</span>
          @if($item->COD_CATEGORIA_MONEDA_CONVERSION == 'MON0000000000002') 
            <span class="cell-detail-description"><b>MONEDA CONVERSION : </b> {{$item->TXT_CATEGORIA_MONEDA_CONVERSION}}</span>
            <span class="cell-detail-description"><b>TIPO CAMBIO : </b> {{$item->CAN_TIPO_CAMBIO}}</span>
          @endif
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
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li>
                <a href="#" class='clickasientocompra' data_asiento_id = "{{$item->COD_ASIENTO}}">
                  Asiento compra
                </a>
              </li>
              <li>
                <a href="#" class='clickasientodiariocompra' data_asiento_id = "{{$item->COD_ASIENTO}}">
                  Asiento diario compra
                </a>
              </li>
              <li>
                <a href="#" class='clickasientodiario' data_asiento_id = "{{$item->COD_ASIENTO}}">
                  Asiento diario
                </a>
              </li>
            </ul>
          </div>
        </td>
      </tr>                    
    @endforeach
  </tbody>
</table>