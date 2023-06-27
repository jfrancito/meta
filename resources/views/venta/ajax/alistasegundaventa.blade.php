<table id="nsv" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th rowspan="2">Año</th>
      <th rowspan="2">Producto</th>
      @foreach($listaperiodo as $item)
        <th colspan="2" class="center">{{$item->TXT_NOMBRE}}</th>
      @endforeach
    </tr>
    <tr>
      @foreach($listaperiodo as $item)
        <th>Compra</th>
        <th>Asociada</th>
      @endforeach
    </tr>


  </thead>
  <tbody>
    @foreach($listasegundaventa as $item)
      <tr >
        <td>{{$item['anio']}}</td>
        <td>{{$item['producto_nombre']}}</td>
        @foreach($listaperiodo as $index => $itemp)
          <td class="dobleclickpc seleccionar center"
              data_producto = "{{$item['producto_id']}}"
              data_periodo = '{{$itemp->COD_PERIODO}}'
              data_filtro = "{{$item['filtro']}}"
              data_anio = "{{$item['anio']}}"
              data_monto = "{{$item['monto'.$index]}}"
              data_asociada = "{{$item['asociada'.$index]}}">
              {{number_format($item['monto'.$index], 2, '.', ',')}}
          </td>
          <td class="dobleclickpc seleccionar center"
              data_producto = "{{$item['producto_id']}}"
              data_periodo = '{{$itemp->COD_PERIODO}}'
              data_filtro = "{{$item['filtro']}}"
              data_anio = "{{$item['anio']}}"
              data_monto = "{{$item['monto'.$index]}}"
              data_asociada = "{{$item['asociada'.$index]}}">
              {{number_format($item['asociada'.$index], 2, '.', ',')}}
          </td>
        @endforeach
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