<h3 style="font-size: 1.2em;
    font-weight: bold;">{{$nombre_asiento}}</h3>
<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th colspan="3" class='center background-th-celeste'>Información </th>

      @if($nro_asiento != '4')
        @if(Session::get('empresas_meta')->COD_EMPR == 'IACHEM0000007086')
          <th colspan="4" class='center background-th-verde'>Venta</th>
        @else
          <th colspan="2" class='center background-th-verde'>Venta</th>
        @endif
        
      @endif

      @if($nro_asiento != '3')
        <th class='center background-th-verde'>Compra</th>
      @endif

      <th class='center background-th-verde'>Sel</th>
    </tr>

    <tr>
      <th class='background-th-celeste'>Año</th>
      <th class='background-th-celeste'>Producto</th>
      <th class='background-th-celeste'>Tipo</th>

      @if($nro_asiento != '4')
        @if(Session::get('empresas_meta')->COD_EMPR == 'IACHEM0000007086')
        <th class='background-th-verde'>CC Relacionada PV</th>
        <th class='background-th-verde'>CC Tercero PV</th>
        <th class='background-th-verde'>CC Relacionada SV</th>
        <th class='background-th-verde'>CC Tercero SV</th>

        @else
        <th class='background-th-verde'>CC Relacionada</th>
        <th class='background-th-verde'>CC Tercero</th>
        @endif
      @endif


      @if($nro_asiento != '3')
        <th class='background-th-verde'>Cuenta contable</th>
      @endif


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

        <td class="user-avatar cell-detail user-info" style="text-align: left;">
          <span>{{$item->producto_nombre}}</span>
          <span class="cell-detail-description negrita" style="color: #000000;">{{$item->nom_servicio}}</span>
          <span class="cell-detail-description negrita" style="color: #000000;">{{$item->nom_material}}</span>
          <span class="cell-detail-description negrita" style="color: #000000;">{{$item->codigo_migracion}}</span>
        </td>

        <td>
            @if($item->material_servicio == 'M')  
              MATERIAL
            @else 
              SERVICIO 
            @endif
        </td>


        @if($nro_asiento != '4')
          @if(Session::get('empresas_meta')->COD_EMPR == 'IACHEM0000007086')
            <td>{{$item->nombre_nro_cuenta_r}}</td>
            <td>{{$item->nombre_nro_cuenta_t}}</td> 
            <td>{{$item->nombre_nro_cuenta_r_sv}}</td>
            <td>{{$item->nombre_nro_cuenta_t_sv}}</td>
          @else
            <td>{{$item->nombre_nro_cuenta_r}}</td>
            <td>{{$item->nombre_nro_cuenta_t}}</td>
          @endif
        @endif


        @if($nro_asiento != '3')
          <td>{{$item->nombre_nro_cuenta_compra}}</td>
        @endif
        


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