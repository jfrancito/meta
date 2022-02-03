<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Orden</th>
      <th>Cuenta contable</th>
      <th>Nombre</th>
      <th>Debe</th>
      <th>Haber</th>
      <th>Opciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listaasientomodelodetalle as $index => $item)
      <tr >
        <td>{{$item->orden}}</td>
        <td>{{$item->cuentacontable->nro_cuenta}}</td>
        <td>{{$item->cuentacontable->nombre}}</td>
        <td><b>{{$funcion->am_pertenece_debe_haber_rno_cuenta($item->id,'DEBE')}}</b></td>
        <td><b>{{$funcion->am_pertenece_debe_haber_rno_cuenta($item->id,'HABER')}}</b></td>
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">

              <li>
                <a href="#" 
                  class= 'modificarcuentacontable' 
                  data_asiento_modelo_id_id = "{{$item->asiento_modelo_id}}"
                  data_detalle_asiento_modelo_id = "{{$item->id}}" >
                  Modificar
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