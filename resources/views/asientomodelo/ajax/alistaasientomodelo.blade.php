<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Id</th>
      <th>Año</th>
      <th>Nombre</th>
      <th>Moneda</th>
      <th>Tipo de asiento</th>
      <th>Estado</th>
      <th>Opciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listamodeloasiento as $index => $item)
      <tr data_asiento_modelo_id = "{{$item->id}}" class='{{$funcion->gn_background_fila_activo($item->activo)}}'>
        <td>{{$index + 1 }}</td>
        <td>{{$item->anio}}</td>
        <td>{{$item->nombre}}</td>
        <td>{{$item->moneda->NOM_CATEGORIA}}</td>
        <td>{{$item->tipoasiento->NOM_CATEGORIA}}</td>
        <td>
          @if($item->activo == 1)
            Activo
          @else
            Inactivo
          @endif
        </td>
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">

              <li>
                <a href="{{ url('/modificar-asiento-modelo/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Modificar
                </a>  
              </li>

              <li>
                <a href="{{ url('/configurar-asiento-modelo/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  Configurar
                </a>  
              </li>

            </ul>
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