<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Año</th>
      <th>Nro Cuenta</th>
      <th>Nivel</th>
      <th>Nombre</th>
      <th>Transferencia debe 01</th>
      <th>Transferencia debe 02</th>
      <th>Transferencia haber</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listacuentacontable as $item)
      <tr class="{{$funcion->pc_color_fila($item)}} dobleclickpc seleccionar"
        data_cuenta_contable_id = "{{$item->id}}"
        >
        <td>{{$item->anio}}</td>
        <td>{{$item->nro_cuenta}}</td>
        <td>{{$item->nivel}}</td>
        <td>{{$item->nombre}}</td>
        <td>
            @if($item->cuenta_contable_transferencia_debe <> '')  
              {{$item->cuenta_contable_transferencia_debe}} - {{$item->transferencia_debe_porcentaje}}%
            @endif
        </td>
        <td>
            @if($item->cuenta_contable_transferencia_debe02 <> '')  
              {{$item->cuenta_contable_transferencia_debe02}} - {{$item->transferencia_debe02_porcentaje}}%
            @endif
        </td>
        <td>
          {{$item->cuenta_contable_transferencia_haber}}
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