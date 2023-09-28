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
        <td class="dobleclickto seleccionar"
            data_producto_id = "{{$item->producto_id}}"
            data_tipo_producto_id = "{{$tipo_producto_id}}"
            data_periodo_id = ""
            data_mes = ""
            data_anio = "{{$anio}}"
            data_tipo_asiento_id = "">{{$item->producto->NOM_PRODUCTO}}</td>
        @foreach($listaperido as $indexp => $itemp)
          @php 
            $monto     =   $funcion->kd_cantidad_producto_venta($listamovimientocommpra,$item->producto_id,$itemp->COD_PERIODO);
           @endphp
          <td class="dobleclickpc seleccionar"
              data_producto_id = "{{$item->producto_id}}"
              data_periodo_id = "{{$itemp->COD_PERIODO}}"
              data_anio = "{{$anio}}"
              data_tipo_asiento_id = "TAS0000000000004"
              >
            {{number_format($monto, 2, '.', '')}}
          </td>
        @endforeach
      </tr>                    
    @endforeach
  </tbody>
  <tfoot>
      <tr>
        <td></td>
        @foreach($listaperido as $indexp => $itemp)
          @php 
            $monto     =   $funcion->kd_cantidad_producto_venta_totales($listamovimientocommpra,$itemp->COD_PERIODO);
           @endphp
          <td>{{number_format($monto, 2, '.', '')}}
          </td>
        @endforeach
      </tr>                    
  </tfoot>
</table>