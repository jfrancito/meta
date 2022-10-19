<table class="table table-condensed table-striped table-detalle-asientos">
    <thead>
      <tr>
        <th>Linea</th>
        <th>Cuenta</th>
        <th>Glosa</th>
        <th>Partida</th>
        <th>Debe</th>
        <th>Haber</th>
        <th>Accion</th>
      </tr>
    </thead>
    <tbody>
    @foreach($array_detalle_asiento as $index => $item)
        <tr data_linea = "{{$item['ultimalinea']}}" class="fila_pedido">
          <td>{{$index + 1}}</td>
          <td>{{$item['nro_cuenta']}}</td>
          <td>{{$item['nombre']}}</td>
          <td>{{$item['nombre_partida']}}</td>
          <td>{{number_format($item['montod'], $redondeo, '.', ',')}}</td>
          <td>{{number_format($item['montoh'], $redondeo, '.', ',')}}</td>
          <td class='center'>
            <span class="badge badge-danger cursor eliminar-detalle-asiento">
              <span class="mdi mdi-close" style='color: #fff;'></span>
            </span>
          </td>
        </tr>                  
    @endforeach
    </tbody>
</table>

<input type="hidden" name="array_detalle_asiento" id='array_detalle_asiento' value='{{json_encode($array_detalle_asiento)}}'>