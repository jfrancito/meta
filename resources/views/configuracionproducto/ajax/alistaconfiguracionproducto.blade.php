<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th colspan="3" class='center background-th-celeste'>Información </th>

      <th colspan="2" class='center background-th-verde'>Venta</th>
      <th class='center background-th-celeste'>Compra</th>
      <th class='center background-th-verde'>Sel</th>
    </tr>

    <tr>
      <th class='background-th-celeste'>Año</th>
      <th class='background-th-celeste'>Producto</th>
      <th class='background-th-celeste'>Tipo</th>
      <th class='background-th-verde'>CC Relacionada</th>
      <th class='background-th-verde'>CC Tercero</th>
      <th class='background-th-celeste'>Cuenta contable</th>
      <th class='background-th-verde'>
        <div class="text-center be-checkbox be-checkbox-sm has-primary">
          <input  type="checkbox"
                  class="todo_asignar input_asignar"
                  id="todo_asignar"
          >
          <label  for="todo_asignar"
                  data-atr = "todas_asignar"
                  class = "checkbox_asignar"                    
                  name="todo_asignar"
            ></label>
        </div>
      </th>
    </tr>
  </thead>
  <tbody>
    @foreach($lista_configuracion_producto as $index=>$item)
      <tr data_producto_id = "{{$item->producto_id}}"
        >
        <td>{{$item->anio}}</td>
        <td>{{$item->producto_nombre}}</td>
        <td>
            @if($item->material_servicio == 'M')  
              MATERIAL
            @else 
              SERVICIO 
            @endif
        </td>
        <td>{{$item->nombre_nro_cuenta_r}}</td>
        <td>{{$item->nombre_nro_cuenta_t}}</td>
        <td>{{$item->nombre_nro_cuenta_compra}}</td>
        <td>

            <div class="text-center be-checkbox be-checkbox-sm has-primary">
              <input  type="checkbox"
                class="{{$item->producto_id}}{{$index}} input_asignar"
                id="{{$item->producto_id}}{{$index}}" >

              <label  for="{{$item->producto_id}}{{$index}}"
                    data-atr = "ver"
                    class = "checkbox checkbox_asignar"                    
                    name="{{$item->producto_id}}{{$index}}"
              ></label>
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