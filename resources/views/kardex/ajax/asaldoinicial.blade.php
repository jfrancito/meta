<table id="" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
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
        <td>{{$item->producto->NOM_PRODUCTO}}</td>
        <td><b>{{number_format($item->unidades, 2, '.', '')}}</b></td>
        <td><b>{{number_format($item->cu_soles, 2, '.', '')}}</b></td>
        <td><b>{{number_format($item->inicial_soles, 2, '.', '')}}</b></td>
        <td>{{$item->tipoproducto->NOM_CATEGORIA}}</td>
      </tr>                    
    @endforeach
  </tbody>
  @if(count($listasaldoinicial)>0)
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
  @endif
</table>