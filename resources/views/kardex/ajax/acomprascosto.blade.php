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
    @php $array_totales      =   array(0,0,0,0,0,0,0,0,0,0,0,0); @endphp
    @foreach($listasaldoinicial as $index => $item)
      <tr>
        <td class="dobleclickto seleccionar"
            data_producto_id = "{{$item->producto_id}}"
            data_tipo_producto_id = "{{$tipo_producto_id}}"
            data_periodo_id = ""
            data_mes = ""
            data_anio = "{{$anio}}"
            data_tipo_asiento_id = "">{{$item->producto->NOM_PRODUCTO}}</td>
         @php 
          $listakardexif     =   $funcion->kd_cantidad_producto_venta_costo($item->producto_id,$anio,$tipo_producto_id);
         @endphp

        @foreach($listaperido as $indexp => $itemp)
         @php 
          $monto     =   $funcion->kd_monto_producto_venta_costo($listakardexif,'COMPRAS',$itemp->COD_PERIODO);
         @endphp
         @php $array_totales[$indexp] = $array_totales[$indexp] + (float) $monto @endphp
          <td class="dobleclickpc seleccionar"
              data_producto_id = "{{$item->producto_id}}"
              data_periodo_id = "{{$itemp->COD_PERIODO}}"
              data_anio = "{{$anio}}"
              data_tipo_asiento_id = "TAS0000000000003"
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

          @for ($i = 0; $i < count($listaperido); $i++)
              <td>
                <b>{{number_format($array_totales[$i], 2, '.', ',')}}</b>
              </td>
          @endfor
      </tr>                    
  </tfoot>
</table>