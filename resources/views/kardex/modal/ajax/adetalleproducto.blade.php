<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$producto->NOM_PRODUCTO}}
		</h5>
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$periodo->TXT_NOMBRE}}
		</h5>		
	</div>
</div>
<div class="modal-body">

	<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;"> 	
	<table class="table table-condensed table-striped">
	    <thead>
	      <tr>
	        <th>PERIODO</th>
	        <th>TIPO DOCUMENTO</th>
	        <th>FECHA</th>
	        <th>SERIE</th>
	        <th>NRO DOCUMENTO</th>
	        <th>PRODUCTO</th>
	        <th>CANTIDAD</th>
	      </tr>
	    </thead>
	    <tbody>
	    @foreach($listadetalleproducto as $index => $item)
	      	<tr>
		       <td>{{$item->NOMBRE_PERIODO}}</td>
		       <td>{{$item->TXT_CATEGORIA_TIPO_DOCUMENTO}}</td>
		       <td>{{date_format(date_create($item->FEC_ASIENTO), 'd-m-Y')}}</td>
		       <td>{{$item->NRO_SERIE}}</td>
		       <td>{{$item->NRO_DOC}}</td>
		       <td>{{$item->TXT_NOMBRE_PRODUCTO}}</td>
		       <td>{{number_format($item->CAN_PRODUCTO, 2, '.', ',')}}</td>
	      	</tr>                  
	    @endforeach
	    </tbody>
		<tfoot>
		    <tr>
		        <td>TOTALES</td>
		        <td></td>
		        <td></td>
		        <td></td>
		        <td></td>
		        <td></td>
		        <td><b>{{number_format($listadetalleproducto->sum('CAN_PRODUCTO'), 2, '.', ',')}}</b></td>
		    </tr>                    
		</tfoot>
	</table>
</div>
</div>

<div class="modal-footer">
<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>

</div>




