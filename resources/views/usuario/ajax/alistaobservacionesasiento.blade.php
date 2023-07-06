<table class="table table-striped table-borderless">
  <thead>
    <tr>
      <th>Tipo de asiento</th>
      <th>Observación</th>
      <th>Cantidad</th>
      <th>Migrar</th>
    </tr>
  </thead>
  <tbody class="no-border-x">
    <tr>
      <td>Ventas</td>
      <td>Ventas sin asientos</td>
      <td class="actions">
        <a href="{{ url('/gestion-observacion-documentos/3/'.$anio) }}">
        <span class="badge badge-primary">{{count($lista_ventas)}}</span>
        </a>
      </td>

      <td class="actions">

        @if($empresa_id == 'EMP0000000000007')
        <a href="{{ url('/migrar-ventas') }}" class='cargando'>
        @else
          @if($empresa_id == 'IACHEM0000010394')
          <a href="{{ url('/migrar-ventas-internacional') }}" class='cargando'>
          @else
          <a href="{{ url('/migrar-ventas-comercial') }}" class='cargando'>
          @endif
        @endif
        
        <span class="badge badge-primary">migrar</span>
        </a>
      </td>



    </tr>

    <tr>
      <td>Ventas</td>
      <td>Productos sin configurar</td>
      <td class="actions">
        <a href="{{ url('/gestion-configuracion-producto/1R/3/'.$anio) }}">
        <span class="badge badge-primary">{{count($lista_productos_sc)}}</span>
        </a>
      </td>
      <td class="actions">
      </td>

    </tr>


    <tr>
      <td>Compras</td>
      <td>Compras sin asientos</td>
      <td class="actions">
        <a href="{{ url('/gestion-observacion-documentos/4/'.$anio) }}">
        <span class="badge badge-success">{{count($lista_compras)}}</span>
        </a>
      </td>

      <td class="actions">
        <a href="{{ url('/migrar-compras') }}" class='cargando'>
        <span class="badge badge-primary">migrar</span>
        </a>
      </td>

    </tr>

    <tr>
      <td>Compras</td>
      <td>Productos sin configurar</td>
      <td class="actions">
        <a href="{{ url('/gestion-configuracion-producto/1R/4/'.$anio) }}">
        <span class="badge badge-success">{{count($lista_productos_sc_comp)}}</span>
        </a>
      </td>
      <td class="actions">
      </td>
    </tr>
  </tbody>
</table>