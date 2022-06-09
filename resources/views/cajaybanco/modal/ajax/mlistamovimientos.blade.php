
<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			LISTA DE MOVIMIENTOS PARA CANCELACION DE DOCUMENTOS
		</h5>
	</div>

</div>
<div class="modal-body">
	<table class="table table-condensed table-striped">
	    <thead>
	      <tr>
	      	<th>NRO OPERACION</th>
	      	<th>BANCO</th>
	        <th>NRO CUENTA BANCARIA</th>
	        <th>FECHA MOVIMIENTO</th>
	        <th>OPERACION</th>
	        <th>MONEDA</th>
	        <th>TIPO DE CAMBIO</th>
	        <th>MONTO SOLES</th>
	        <th>MONTO DOLARES</th>
	      </tr>
	    </thead>
	    <tbody>
	    @foreach($listamovimientos as $index => $item)
	      	<tr data_nro_operacion = "{{$item->NRO_VOUCHER}}"
	      		data_cod_caja_banco = "{{$item->COD_CAJA_BANCO}}"

	      		data_nro_cuenta_bancaria = "{{$item->NRO_CUENTA_BANCARIA}}"
	      		data_txt_categoria_operacion_caja = "{{$item->TXT_CATEGORIA_OPERACION_CAJA}}"
	      		data_txt_categoria_moneda = "{{$item->TXT_CATEGORIA_MONEDA}}"
	      		data_can_tipo_cambio = "{{$item->CAN_TIPO_CAMBIO}}"
	      		data_fec_movimiento_caja = "{{$item->FEC_MOVIMIENTO_CAJABANCO}}"
	      		
	      		data_nro_referencia = "{{$cuenta_referencia}}"
        		class='dobleclickpc seleccionar'>
	      	   	<td><b>{{$item->NRO_VOUCHER}}</b></td>
	      	   	<td>{{$item->cajabanco->TXT_BANCO}}</td>
		       	<td>{{$item->NRO_CUENTA_BANCARIA}}</td>
		       	<td><b>{{date_format(date_create($item->FEC_MOVIMIENTO_CAJABANCO), 'd-m-Y')}}</b></td>
		       	<td>{{$item->TXT_CATEGORIA_OPERACION_CAJA}}</td>
		       	<td>{{$item->TXT_CATEGORIA_MONEDA}}</td>
		       	<td>{{$item->CAN_TIPO_CAMBIO}}</td>
	            @if($cuenta_referencia == '42') 
	            	<td><b>{{number_format($item->CAN_HABER_MN, $redondeo, '.', ',')}}</b></td>
			   		<td><b>{{number_format($item->CAN_HABER_ME, $redondeo, '.', ',')}}</b></td>
	            @else
	                <td><b>{{number_format($item->CAN_DEBE_MN, $redondeo, '.', ',')}}</b></td>
			   		<td><b>{{number_format($item->CAN_DEBE_ME, $redondeo, '.', ',')}}</b></td>
	            @endif
	      	</tr>                  
	    @endforeach
	    </tbody>
	</table>
</div>
<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn btn-default modal-close">Cerrar</button>
</div>




