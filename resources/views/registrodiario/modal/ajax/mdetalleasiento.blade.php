
<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$asiento->TXT_GLOSA}}
		</h5>
	</div>
	<div class="col-xs-1">
		Año : {{$asiento->periodo->COD_ANIO}}
	</div>
	<div class="col-xs-2">
		Fecha : {{date_format(date_create($asiento->FEC_ASIENTO), 'd-m-Y')}}
	</div>	
	<div class="col-xs-2">
		Moneda : {{$asiento->TXT_CATEGORIA_MONEDA}}
	</div>
	<div class="col-xs-2">
		Tipo cambio : {{$asiento->CAN_TIPO_CAMBIO}}
	</div>
	<div class="col-xs-2">
		Total debe : {{$asiento->CAN_TOTAL_DEBE}}
	</div>
	<div class="col-xs-2">
		Total haber : {{$asiento->CAN_TOTAL_HABER}}
	</div>
</div>
<div class="modal-body">
	<table class="table table-condensed table-striped">
	    <thead>
	      <tr>
	      	<th>Linea</th>
	        <th>Cuenta</th>
	        <th>Glosa</th>
	        <th>Debe MN</th>
	        <th>Haber MN</th>
	        <th>Debe ME</th>
	        <th>Haber ME</th>
	      </tr>
	    </thead>
	    <tbody>
	    @foreach($listaasientomovimiento as $index => $item)
	      	<tr>
	      	   <td>{{$item->NRO_LINEA}}</td>
		       <td>{{$item->TXT_CUENTA_CONTABLE}}</td>
		       <td>{{$item->TXT_GLOSA}}</td>
		       <td>{{number_format($item->CAN_DEBE_MN, $redondeo, '.', ',')}}</td>
		       <td>{{number_format($item->CAN_HABER_MN, $redondeo, '.', ',')}}</td>
		       <td>{{number_format($item->CAN_DEBE_ME, $redondeo, '.', ',')}}</td>
		       <td>{{number_format($item->CAN_HABER_ME, $redondeo, '.', ',')}}</td>
	      	</tr>                  
	    @endforeach
	    </tbody>

	    <tfoot>
	      <tr>
	      	<th colspan="3">Totales</th>
	      	<th>{{number_format($listaasientomovimiento->sum("CAN_DEBE_MN"), $redondeo, '.', ',')}}</th>
	      	<th>{{number_format($listaasientomovimiento->sum("CAN_HABER_MN"), $redondeo, '.', ',')}}</th>
	      	<th>{{number_format($listaasientomovimiento->sum("CAN_DEBE_ME"), $redondeo, '.', ',')}}</th>
	      	<th>{{number_format($listaasientomovimiento->sum("CAN_HABER_ME"), $redondeo, '.', ',')}}</th>
	      </tr>
	    </tfoot>

	</table>
</div>

<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn btn-default btn-space">Cerrar</button>

</div>




