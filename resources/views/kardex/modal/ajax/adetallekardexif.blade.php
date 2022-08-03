<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$producto->NOM_PRODUCTO}}
		</h5>		
	</div>
</div>
<div class="modal-body">
	<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;"> 
        <div style="width: 1750px;margin-bottom: 10px;">
			<table class="table table-condensed table-striped" >
			    <thead>
			      <tr>

			        <th rowspan="2" class="color-ich">MES</th>
			        <th rowspan="2" class="color-ich">FECHA</th>
			        <th rowspan="2" class="color-ich">SERVICIO</th>
			        <th rowspan="2" class="color-ich">PRODUCTO</th>
			        <th rowspan="2" class="color-ich">SERIE</th>
			        <th rowspan="2" class="color-ich">CORRELATIVO</th>
			        <th rowspan="2" class="color-ich">RUC</th>
			        <th rowspan="2" class="color-ich">CLIENTE</th>

			        <th colspan="3" class="center color-ich">ENTRADAS</th>


			        <th colspan="3" class="center color-ich">SALIDAS</th>
	   

			        <th colspan="3" class="center color-ich">SALDO</th>


			      </tr>


			      <tr>


			        <th class="color-ich">CANTIDAD</th>
			        <th class="color-ich">C.U.</th>
			        <th class="color-ich">IMPORTE</th>

			        <th class="color-ich">CANTIDAD</th>
			        <th class="color-ich">C.U.</th>
			        <th class="color-ich">IMPORTE</th>	   

			        <th class="color-ich">CANTIDAD</th>
			        <th class="color-ich">C.U.</th>
			        <th class="color-ich">IMPORTE</th>

			      </tr>
			    </thead>
			    <tbody>
			    @foreach($listakardexif as $index => $item)
			      	<tr>
				       <td>{{$item['nombre_periodo']}}</td>
				       <td>{{date_format(date_create($item["fecha"]), 'd-m-Y')}}</td>
				       <td>{{$item['servicio']}}</td>
				       <td>{{$item['nombre_producto']}}</td>
				       <td>{{$item['serie']}}</td>
				       <td>{{$item['correlativo']}}</td>
				       <td>{{$item['ruc']}}</td>
				       <td>{{$item['nombre_cliente']}}</td>


				       <td class="negrita">{{number_format($item['entrada_cantidad'], 2, '.', ',')}}</td>
				       <td class="negrita">{{number_format($item['entrada_cu'], 2, '.', ',')}}</td>
				       <td class="negrita">{{number_format($item['entrada_importe'], 2, '.', ',')}}</td>

				       <td class="negrita">{{number_format($item['salida_cantidad'], 2, '.', ',')}}</td>
				       <td class="negrita">{{number_format($item['salida_cu'], 2, '.', ',')}}</td>
				       <td class="negrita">{{number_format($item['salida_importe'], 2, '.', ',')}}</td>

				       <td class="negrita">{{number_format($item['saldo_cantidad'], 2, '.', ',')}}</td>
				       <td class="negrita">{{number_format($item['saldo_cu'], 2, '.', ',')}}</td>
				       <td class="negrita">{{number_format($item['saldo_importe'], 2, '.', ',')}}</td>

			      	</tr>                  
			    @endforeach
			    </tbody>
			</table>
		</div>	
	</div>
</div>

<div class="modal-footer">
<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>

</div>




