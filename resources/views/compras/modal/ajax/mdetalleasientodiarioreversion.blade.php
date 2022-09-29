
<form method="POST" 
	action="{{ url('/diario-reversion-guardar-data/'.$idopcion) }}" 

>
      {{ csrf_field() }}

<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$glosa}}
		</h5>
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$periodo->TXT_NOMBRE}}
		</h5>		
	</div>
</div>
<div class="modal-body">

	      <div class="panel panel-default" style="margin-bottom: 0px;">
	        <div class="tab-container">

	          <ul class="nav nav-tabs">
	            <li class="active"><a href="#asientodiario" data-toggle="tab">Asiento Diario</a></li>
	            <li><a href="#asientocompra" data-toggle="tab">Asiento Compra</a></li>
	            <li><a href="#configuracion" data-toggle="tab">Configuración</a></li>
	          </ul>

	          <div class="tab-content" style="margin-bottom: 0px;">
	            <div id="asientodiario" class="tab-pane active cont">

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
					    @foreach($cabecera_diario as $index => $item)
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
					    @foreach($detalle_diario as $index => $item)
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
				              <th>{{number_format(array_sum(array_column($detalle_diario,'total_debe')), $redondeo, '.', ',')}}</th>
				              <th>{{number_format(array_sum(array_column($detalle_diario,'total_haber')), $redondeo, '.', ',')}}</th>
				              <th>{{number_format(array_sum(array_column($detalle_diario,'total_debe_dolar')), $redondeo, '.', ',')}}</th>
				              <th>{{number_format(array_sum(array_column($detalle_diario,'total_haber_dolar')), $redondeo, '.', ',')}}</th>
				            </tr>
				        </tfoot>

					</table>




	            </div>
	            <div id="asientocompra" class="tab-pane cont">


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
					    @foreach($cabecera_compra as $index => $item)
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
					    @foreach($detalle_compra as $index => $item)
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
				              <th>{{number_format(array_sum(array_column($detalle_compra,'total_debe')), $redondeo, '.', ',')}}</th>
				              <th>{{number_format(array_sum(array_column($detalle_compra,'total_haber')), $redondeo, '.', ',')}}</th>
				              <th>{{number_format(array_sum(array_column($detalle_compra,'total_debe_dolar')), $redondeo, '.', ',')}}</th>
				              <th>{{number_format(array_sum(array_column($detalle_compra,'total_haber_dolar')), $redondeo, '.', ',')}}</th>
				            </tr>
				        </tfoot>

					</table>


	            </div>



	            <div id="configuracion" class="tab-pane cont">

			        <div class="row">
			            <div class="col-md-4">
			              <div class="panel panel-flat">
			                <div class="panel-heading">Periodo</div>
			                <div class="panel-body">

						        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		                          <div class="form-group">
		                            <label class="col-sm-12 control-label labelleft" >Año :</label>
		                            <div class="col-sm-12 abajocaja" >
		                              {!! Form::select( 'anio_asiento', $combo_anio_pc, $anio,
		                                                [
		                                                  'class'       => 'select2 form-control control input-xs' ,
		                                                  'id'          => 'anio_asiento',
		                                                  'data-aw'     => '1',
		                                                ]) !!}
		                            </div>
		                          </div>
						        </div>

			                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ajax_anio_asiento">
									<div class="form-group">
									<label class="col-sm-12 control-label labelleft" >Periodo :</label>
									<div class="col-sm-12 abajocaja" >
									  {!! Form::select( 'periodo_asiento_id', $combo_periodo, $sel_periodo,
									                    [
									                      'class'       => 'select2 form-control control input-xs' ,
									                      'id'          => 'periodo_asiento_id',
									                      'data-aw'     => '1',
									                    ]) !!}
									</div>
									</div>
			                    </div>


			                </div>
			              </div>
			            </div>

			        </div>


	            </div>



	          </div>


	        </div>
	      </div>





</div>


	{{ csrf_field() }}

	<input type="hidden" name="cabecera_diario" id='cabecera_diario' value='{{json_encode($cabecera_diario)}}'>
	<input type="hidden" name="detalle_diario" id='detalle_diario' value='{{json_encode($detalle_diario)}}'>
	<input type="hidden" name="cabecera_compra" id='cabecera_compra' value='{{json_encode($cabecera_compra)}}'>
	<input type="hidden" name="detalle_compra" id='detalle_compra' value='{{json_encode($detalle_compra)}}'>
	<input type="hidden" name="periodore_id" id='periodore_id' value='{{$periodo->COD_PERIODO}}'>
	<input type="hidden" name="seriere" id='seriere' value='{{$serie}}'>
	<input type="hidden" name="documentore" id='documentore' value='{{$documento}}'>
	<input type="hidden" name="aniore" id='aniore' value='{{$anio}}'>

<div class="modal-footer">
		<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>
		@if($ind_existe_asiento==0)
		<button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-configuracion-reversion">Guardar</button>
		@endif
</div>
</form>

@if(isset($ajax))
<script type="text/javascript">
	$(".select2").select2({
      width: '100%'
    });
    $('.dinero').inputmask({ 'alias': 'numeric', 
    'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
    'digitsOptional': false, 
    'prefix': '', 
    'placeholder': '0'});
</script> 
@endif