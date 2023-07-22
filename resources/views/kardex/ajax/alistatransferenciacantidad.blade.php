<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Item</th>
      <th>Codigo</th>
      <th>Fecha</th>
      <th>Producto</th>
      <th>Ingreso/Salida</th>
      <th>Cantidad</th>
      <th>CU</th>
      <th>Importe</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listatranferencia as $index => $item)
      <tr >
        <td>{{$index + 1}}</td>
        <td>{{$item->codigo}}</td>

        <td>{{$item->fecha}}</td>
        <td>{{$item->producto_nombre}}</td>
        <td>{{$item->ingreso_salida}}</td>

        <td>{{$item->cantidad}}</td>
        <td>{{$item->cu}}</td>  
        <td>{{$item->importe}}</td>

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