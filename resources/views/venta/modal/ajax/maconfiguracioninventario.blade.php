<div class="modal-header" style="padding: 15px 20px;">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title">
			<strong>{{$periodo->TXT_CODIGO}} - </strong> 
			{{$producto->NOM_PRODUCTO}} <br>
			<small>Cantidad Compra : <strong>({{number_format($data_monto, $redondeo, '.', ',')}})</strong></small>
			<small>Cantidad Asocida : <strong>({{number_format($data_asociada, $redondeo, '.', ',')}})</strong></small>
			<small>Cantidad x Asociar : <strong>({{number_format($monto_por_asociar, $redondeo, '.', ',')}})</strong></small><br>

			<small>Contador item: <strong class='contitem'>0.00</strong></small>
		</h3>
</div>
	<div class="modal-body" style="padding: 12px 20px 20px;">
                <div class="tab-container">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#asociar" data-toggle="tab">Asociar a segunda Venta</a></li>
                    <li><a href="#ventassociadas"  data-toggle="tab">Ventas Asociadas</a></li>
                  </ul>
                  <div class="tab-content">
                    <div id="asociar" class="tab-pane active cont">
						<form method="POST" action="{{ url('/guardar-configuracio-segunda-venta/'.$idopcion) }}" id='formguardar'>
						      {{ csrf_field() }}

						    <div class="col-md-6">
						            <div class="form-group">
										<label class="col-sm-12 control-label labelleft negrita" style="padding-left: 0px;" >Agregar Inventario: </label>
										<div class="col-sm-12 abajocaja"  style="padding-left: 0px;">
									      <input  type="text"
									              id="agregarinventario" name='agregarinventario' 
									              value="0.0000" 
									              placeholder="Codigo migracion"
									              autocomplete="off" class="form-control dinero input-sm" data-aw="1"/>
										</div>
									</div>
						    </div>

						    <input type="hidden" name="producto_id" value="{{$producto->COD_PRODUCTO}}">
						    <input type="hidden" name="periodo_id" value="{{$periodo->COD_PERIODO}}">

						    <input type="hidden" name="cantidad_descuento" id="cantidad_descuento" value="{{$monto_por_asociar}}">


						    <input type="hidden" name="cantidad_documento" id="cantidad_documento">
						    <input type="hidden" name="anio" id="anio" value="{{$anio}}">


						    <input type="hidden" name="data_archivo" id="data_archivo" value="">

						</form>

						    <div class="col-md-12">
								<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;"> 
								<table class="table listatabla table-condensed table-striped">
								    <thead>
								      <tr>
								      		<th>Item</th>
									      	<th>Fecha</th>
									      	<th>Serie</th>
									      	<th>Correlativo</th>
									      	<th>Moneda</th>      
									      	<th>T.C.</th>
									      	<th>Cantidad</th>
								      </tr>
								    </thead>
								    <tbody>
								    @foreach($listaasiento as $index => $item)
								      	<tr>
							              	<td>
							                  	<div class="text-center be-checkbox be-checkbox-sm has-primary">
								                    <input  type="checkbox"
								                      class="{{$item->COD_ASIENTO}} input_asignar selectcheck"
								                      id="{{$item->COD_ASIENTO}}" 
								                      data_cantidad = '{{$item->CD}}'>
								                    <label  for="{{$item->COD_ASIENTO}}"
								                          data-atr = "ver"
								                          class = "checkbox checkbox_asignar"                    
								                          name="{{$item->COD_ASIENTO}}"
								                    ></label>
							                  	</div>
							              	</td>
									        <td>{{date_format(date_create($item->FEC_ASIENTO), 'd-m-Y')}}</td>
									        <td>{{$item->NRO_SERIE}}</td>
									        <td>{{$item->NRO_DOC}}</td>
									        <td>{{$item->TXT_CATEGORIA_MONEDA}}</td>
									        <td>{{number_format($item->CAN_TIPO_CAMBIO, $redondeo, '.', ',')}}</td>
									        <td>{{number_format($item->CD, $redondeo, '.', ',')}}</td>
								      	</tr>                  
								    @endforeach
								    </tbody>
								</table>
								</div>
							</div>

						 	<div class="col-md-12" style="margin-top: 15px;margin-bottom: 18px;text-align: right;">
							   	<button type="submit" class="btn btn-success guardarasociar">Guardar</button>
							</div>



                    </div>
                    <div id="ventassociadas" class="tab-pane cont">

						    <div class="col-md-12">
								<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;"> 
								<table class="table table-condensed table-striped">
								    <thead>
								      <tr>

									      	<th>Fecha</th>
									      	<th>Serie</th>
									      	<th>Correlativo</th>
									      	<th>Moneda</th>      
									      	<th>T.C.</th>
									      	<th>Cantidad</th>
								      </tr>
								    </thead>
								    <tbody>
								    @foreach($listaasociada as $index => $item)
								      	<tr>
									        <td>{{date_format(date_create($item->FEC_ASIENTO), 'd-m-Y')}}</td>
									        <td>{{$item->NRO_SERIE}}</td>
									        <td>{{$item->NRO_DOC}}</td>
									        <td>{{$item->TXT_CATEGORIA_MONEDA}}</td>
									        <td>{{number_format($item->CAN_TIPO_CAMBIO, $redondeo, '.', ',')}}</td>
									        <td>{{number_format($item->cantidad_descargo, $redondeo, '.', ',')}}</td>
								      	</tr>                  
								    @endforeach
								    </tbody>
								</table>
								</div>
							</div>



                    </div>
                  </div>
                </div>
	</div>

  <script type="text/javascript">
      $('.dinero').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});
  </script> 




