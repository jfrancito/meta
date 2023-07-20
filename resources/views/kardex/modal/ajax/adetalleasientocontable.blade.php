<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$tipoproducto->NOM_CATEGORIA}}
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
                  <th>Periodo</th>
                  <th>Fecha</th>
                  <th>Glosa</th>
                  <th>Moneda</th>
                  <th>T.C.</th>
                  <th>Total Debe</th>
                  <th>Total Haber</th>
		      </tr>
		    </thead>

		    <tbody>
		    @foreach($cabecera as $index => $item)
		      	<tr>
			       <td>{{$item['nombre_periodo']}}</td>
			       <td>{{date_format(date_create($item['fecha']), 'd-m-Y')}}</td>
			       <td>{{$item['glosa']}}</td>
			       <td>{{$item['moneda']}}</td>
			       <td>{{$item['tipo_cambio']}}</td>
			       <td>{{number_format($item['total_debe'], 2, '.', ',')}}</td>
			       <td>{{number_format($item['total_haber'], 2, '.', ',')}}</td>
		      	</tr>                   
		    @endforeach
		    </tbody>
		</table>


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
		    @foreach($detalle as $index => $item)
		      	<tr>
                  <td>{{$item['linea']}}</td>
                  <td>{{$item['cuenta_nrocuenta']}}</td>
                  <td>{{$item['glosa']}}</td>
                  <td>{{number_format($item['total_debe'], $redondeo, '.', ',')}}</td>
                  <td>{{number_format($item['total_haber'], $redondeo, '.', ',')}}</td>
                  <td>{{number_format($item['total_debe_dolar'], $redondeo, '.', ',')}}</td>
                  <td>{{number_format($item['total_haber_dolar'], $redondeo, '.', ',')}}</td>
		      	</tr>                  
		    @endforeach
		    </tbody>

	        <tfoot>
	            <tr>
	              <th colspan="3">Totales</th>
	              <th>{{number_format(array_sum(array_column($detalle,'total_debe')), $redondeo, '.', ',')}}</th>
	              <th>{{number_format(array_sum(array_column($detalle,'total_haber')), $redondeo, '.', ',')}}</th>
	              <th>{{number_format(array_sum(array_column($detalle,'total_debe_dolar')), $redondeo, '.', ',')}}</th>
	              <th>{{number_format(array_sum(array_column($detalle,'total_haber_dolar')), $redondeo, '.', ',')}}</th>
	            </tr>
	        </tfoot>
		</table>

	</div>
</div>

	<form method="POST"
	id="formguardar"
	action="{{ url('/kardex-guardar-data/'.$idopcion) }}" 
	style="border-radius: 0px;" 
	>
		{{ csrf_field() }}
		<input type="hidden" name="cabecera" id='cabecera' value='{{json_encode($cabecera)}}'>
		<input type="hidden" name="detalle" id='detalle' value='{{json_encode($detalle)}}'>
		<input type="hidden" name="periodog_id" id='periodog_id' value='{{$periodo->COD_PERIODO}}'>

	<div class="modal-footer">



			<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>
			<button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-configuracion">Guardar</button>



	</div>
	</form>


@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){

      App.init();
      App.formElements();
      $('form').parsley();

    });
  </script>
@endif


