<table id="" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>

      <th>PRODUCTO</th>
      @foreach($listaperido as $index => $item)
      <th>{{substr($item->TXT_NOMBRE, 3)}}</th>   
      @endforeach

    </tr>
  </thead>
  <tbody>
    @foreach($listasaldoinicial as $index => $item)
      <tr>
        <td>{{$item->producto->NOM_PRODUCTO}}</td>
        @foreach($listaperido as $indexp => $itemp)
          @php 
            $monto     =   $funcion->kd_cantidad_producto_if($listamovimientocommpra,
            $listamovimiento,
            number_format($item->unidades, 2, '.', ''),
            $item->producto_id,
            $itemp->COD_MES);
           @endphp
          <td class="seleccionar"
              data_producto_id = "{{$item->producto_id}}"
              data_periodo_id = "{{$itemp->COD_PERIODO}}"
              data_mes = "{{$itemp->COD_MES}}"
              data_anio = "{{$anio}}"
              data_tipo_asiento_id = "TAS0000000000004"
              >
            <b>{{number_format($monto, 2, '.', ',')}}</b>
          </td>
        @endforeach
      </tr>                    
    @endforeach
  </tbody>
</table>