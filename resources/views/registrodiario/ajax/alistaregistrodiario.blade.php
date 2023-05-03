<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Item</th>
      <th>Periodo</th>
      <th>Fecha</th>
      <th>Glosa</th>
      <th>Moneda</th>      
      <th>T.C.</th>
      <th>Total Debe</th>
      <th>Total Haber</th>
      <th>Opcion</th>      
    </tr>
  </thead>
  <tbody>
    @foreach($listaasiento as $index => $item)
      <tr data_asiento_id = "{{$item->COD_ASIENTO}}" 
        class='dobleclickpc seleccionar {{$funcion->gn_background_fila_ind_extorno($item->IND_EXTORNO)}}'
        style="cursor: pointer;">
        <td>{{$index + 1}}</td>
        <td>{{$item->periodo->TXT_NOMBRE}}</td>
        <td>{{date_format(date_create($item->FEC_ASIENTO), 'd-m-Y')}}</td>
        <td>{{$item->TXT_GLOSA}}</td>
        <td>
            <span class="cell-detail-description"><b>MONEDA : </b> {{$item->TXT_CATEGORIA_MONEDA}}</span>
          @if($item->COD_CATEGORIA_MONEDA_CONVERSION == 'MON0000000000002') 
            <span class="cell-detail-description"><b>MONEDA CONVERSION : </b> {{$item->TXT_CATEGORIA_MONEDA_CONVERSION}}</span>
          @endif
        </td>


        <td>{{number_format($item->CAN_TIPO_CAMBIO, $redondeo, '.', ',')}}</td>
        <td>{{number_format($item->CAN_TOTAL_DEBE, $redondeo, '.', ',')}}</td>
        <td>{{number_format($item->CAN_TOTAL_HABER, $redondeo, '.', ',')}}</td>
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li>
                <a href="{{ url('/asiento-contable-excel-xasiento/'.$item->COD_ASIENTO) }}" target="_blank">
                  Exportar excel
                </a>
              </li>
            </ul>
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