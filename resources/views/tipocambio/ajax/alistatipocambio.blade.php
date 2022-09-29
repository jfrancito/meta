<div class="tools dropdown show">
  <div class="dropdown" style="text-align: right;
    margin-top: 10px;">
    <button class="btn btn-rounded btn-space btn-success guardarcambios" >
      <i class="icon icon-left mdi mdi-save"></i> Guardar
    </button>
  </div>
</div>


<table id="nso" class="tablatipocambio table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Fecha</th>
      <th>Dia</th>
      <th>Mes</th>
      <th>Año</th>
      


      <th>Compra Sunat</th>
      <th>Venta Sunat</th>
      <th>Compra SBS</th>
      <th>Venta SBS</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listatipocambio as $item)
      <tr 
          class='fila_tipo_cambio'
          data_fecha_tipo_cambio = "{{str_replace('-','',$item->FEC_CAMBIO)}}"
          data_edit_tipo_cambio = "0"
        >
        <td>{{date_format(date_create($item->FEC_CAMBIO), 'd-m-Y')}}</td>
        <td>{{date("d", strtotime($item->FEC_CAMBIO))}}</td>
        <td>{{$arraymeses[$item->NRO_MES]}}</td>
        <td>{{$item->NRO_ANIO}}</td>
        <td>{{number_format($item->CAN_COMPRA, $redondeo, '.', ',')}}</td>
        <td>{{number_format($item->CAN_VENTA, $redondeo, '.', ',')}}</td>
        <td>
            <input type="text"  
                   id="CAN_COMPRA_SBS" 
                   name="CAN_COMPRA_SBS"
                   value="{{number_format($item->CAN_COMPRA_SBS, 4, '.', ',')}}"
                   class="form-control input-sm dinero tipocambio_edit"
                   >
        </td>
        <td>
            <input type="text"  
                   id="CAN_VENTA_SBS" 
                   name="CAN_VENTA_SBS"
                   value="{{number_format($item->CAN_VENTA_SBS, 4, '.', ',')}}"
                   class="form-control input-sm dinero tipocambio_edit"
                   >
        </td>
      </tr>                    
    @endforeach
  </tbody>
</table>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
      $('.dinero').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});
    });
  </script> 
@endif