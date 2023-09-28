<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Id</th>
      <th>Producto</th>
      <th>Unidades</th>
      <th>Soles C.U.</th>
      <th>Soles inicial</th>
      <th>Tipo producto</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listasaldoinicial as $index => $item)
      <tr>
        <td>{{$index + 1 }}</td>
        <td class="dobleclickto seleccionar"
            data_producto_id = "{{$item->producto_id}}"
            data_tipo_producto_id = "{{$tipo_producto_id}}"
            data_periodo_id = ""
            data_mes = ""
            data_anio = "{{$anio}}"
            data_tipo_asiento_id = "">{{$item->producto->NOM_PRODUCTO}}</td>
        <td><b>{{number_format($item->unidades, 2, '.', '')}}</b></td>
        <td>{{number_format($item->cu_soles, 2, '.', '')}}</td>
        <td>{{number_format($item->inicial_soles, 2, '.', '')}}</td>
        <td>{{$item->tipoproducto->NOM_CATEGORIA}}</td>
      </tr>                    
    @endforeach
  </tbody>
  <tfoot>
      <tr>
        <td></td>
        <td>TOTALES</td>
        <td><b>{{number_format($listasaldoinicial->sum('unidades'), 2, '.', '')}}</b></td>
        <td><b>{{number_format($listasaldoinicial->sum('cu_soles'), 2, '.', '')}}</b></td>
        <td><b>{{number_format($listasaldoinicial->sum('inicial_soles'), 2, '.', '')}}</b></td>
        <td></td>
      </tr>                    
  </tfoot>

</table>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif