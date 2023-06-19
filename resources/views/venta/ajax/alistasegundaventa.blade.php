<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Año</th>
      <th>Producto</th>
      <th>Periodo</th>

    </tr>
  </thead>
  <tbody>
    @foreach($listasegundaventa as $item)
      <tr class="dobleclickpc seleccionar"
        data_segunda_venta_id = "{{$item->id}}"
        >
        <td>{{$item->anio}}</td>
        <td>{{$item->producto->NOM_PRODUCTO}}</td>
        <td>{{$item->periodo->TXT_NOMBRE}}</td>
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