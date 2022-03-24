
<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			Documento : {{$historialmigrar->documento_ctble->NRO_SERIE}}-{{$historialmigrar->documento_ctble->NRO_DOC}}
		</h5>
		<h5 class="modal-title" style="font-size: 1.2em;">
			Fecha Emision : {{date_format(date_create($historialmigrar->documento_ctble->FEC_EMISION), 'd-m-Y')}}
		</h5>
		<h5 class="modal-title" style="font-size: 1.2em;">
			Cliente : {{$historialmigrar->documento_ctble->TXT_EMPR_RECEPTOR}} - {{$funcion->gn_cliente_relacionado_tercero_xempresa($historialmigrar->documento_ctble->COD_EMPR_RECEPTOR)}}
		</h5>
	</div>

</div>
<div class="modal-body">
	<table class="table table-condensed table-striped">
	    <thead>
	      <tr>
	      	<th>Linea</th>
	        <th>Producto</th>
	        <th>Cantidad</th>

		    @if($tipo_asiento == 'TAS0000000000003')
		        <th class='@if($indclienterelter == 1) background-th @endif'>CC Relacionada</th>
		        <th class='@if($indclienterelter == 0) background-th @endif'>CC Tercero</th>
		    @else
	        	<th class='background-th'>Cuenta Corriente</th>
		    @endif

	      </tr>
	    </thead>
	    <tbody>
	    @foreach($listadetalleproducto as $index => $item)
	      	<tr>
	      	   <td>{{$index + 1}}</td>
		       <td>{{$item->TXT_NOMBRE_PRODUCTO}}</td>
		       <td>{{$item->CAN_PRODUCTO}}</td>
			    @if($tipo_asiento == 'TAS0000000000003')
			       <td>
			       	{{$funcion->gn_cuenta_contable_xproducto_xempresa_xanio(
			       		$item->COD_PRODUCTO,
			       		Session::get('empresas_meta')->COD_EMPR,
			       		1,
			       		$anio_documento,
			       		$tipo_asiento)
			       	}}
			       </td>
			       <td>
			       	{{$funcion->gn_cuenta_contable_xproducto_xempresa_xanio(
			       		$item->COD_PRODUCTO,
			       		Session::get('empresas_meta')->COD_EMPR,
			       		0,
			       		$anio_documento,
			       		$tipo_asiento)
			       	}}
			       </td>
			    @else
			       <td>
			       	{{$funcion->gn_cuenta_contable_xproducto_xempresa_xanio(
			       		$item->COD_PRODUCTO,
			       		Session::get('empresas_meta')->COD_EMPR,
			       		0,
			       		$anio_documento,
			       		$tipo_asiento)
			       	}}
			       </td>
			    @endif




	      	</tr>                  
	    @endforeach
	    </tbody>

	</table>
</div>

<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn btn-default btn-space">Cerrar</button>

</div>




