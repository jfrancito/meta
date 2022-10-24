
<form method="POST" action="{{ url('/asiento-contables-confirmado-rh-configuracion-xdocumentos/'.$idopcion.'/'.$asiento->COD_ASIENTO) }}">
      {{ csrf_field() }}

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
<div class="modal-body" style="padding: 0px;">
	      <div class="panel panel-default" style="margin-bottom: 0px;">
	        <div class="tab-container">
	          <ul class="nav nav-tabs">
	            <li class="active"><a href="#asientocontable" data-toggle="tab">Asiento Contable</a></li>
	            <li><a href="#configuracion" data-toggle="tab">Configuración</a></li>
	          </ul>
	          <div class="tab-content" style="margin-bottom: 0px;">
	            <div id="asientocontable" class="tab-pane active cont">

<!-- 					<div class="form-group quitar-tb">
					      <label class="col-sm-1 control-label izquierda">Fecha Emision</label>
					      <div class="col-sm-3">
					          <div data-min-view="2" 
					                 data-date-format="dd-mm-yyyy"  
					                 class="input-group date datetimepicker2" style = 'padding: 0px 0;margin-top: -3px;'>
					                 <input size="16" type="text"  
					                        placeholder="Fecha emision"
					                        id='fechaemision' 
					                        name='fechaemision' 
					                        value = "{{date_format(date_create($asiento->FEC_ASIENTO), 'd-m-Y')}}"
					                        class="form-control input-sm"/>
					                  <span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
					            </div>

					      </div>
					</div>
					<button type="button" data-dismiss="modal" class="btn btn-primary btn-space generarasiento">Generar asientos</button> -->

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
			            <div class="col-md-4 ocultar">
			              <div class="panel panel-default">
			                <div class="panel-heading"> 
			                  <div class="tools"><span class="icon s7-upload"></span><span class="icon s7-edit"></span><span class="icon s7-close"></span></div><span class="title">Detracción</span>
			                </div>
			                <div class="panel-body">
						        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		                          <div class="form-group">
		                            <label class="col-sm-12 control-label labelleft" >Tipo Descuento :</label>
		                            <div class="col-sm-12 abajocaja" >
		                              {!! Form::select( 'tipo_descuento', $combo_descuento, $sel_tipo_descuento,
		                                                [
		                                                  'class'       => 'select2 form-control control input-xs' ,
		                                                  'id'          => 'tipo_descuento',
		                                                  'data-aw'     => '1',
		                                                ]) !!}
		                            </div>
		                          </div>
						        </div>

						        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		                          <div class="form-group">
		                            <label class="col-sm-12 control-label labelleft" >Porcentaje :</label>
		                            <div class="col-sm-12 abajocaja" >

								      <input  type="text"
								              id="porcentaje_detraccion" 
								              data_valor="@if(isset($orden->CAN_DSCTO)){{$orden->CAN_DSCTO}}@endif"
								              name='porcentaje_detraccion' 
								              value="@if(isset($orden->CAN_DSCTO)){{$orden->CAN_DSCTO}}@endif"
								              placeholder="0.00"
								              autocomplete="off" 
								              class="form-control input-sm dinero" 
								              data-aw="1"/>
		                            </div>
		                          </div>
						        </div>

						        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		                          <div class="form-group">
		                            <label class="col-sm-12 control-label labelleft" >Total :</label>
		                            <div class="col-sm-12 abajocaja" >

								      <input  type="text"
								              id="total_detraccion"
								              data_valor="@if(isset($orden->CAN_DETRACCION)){{$orden->CAN_DETRACCION}}@endif"
								              name='total_detraccion'
								              value="@if(isset($orden->CAN_DETRACCION)){{$orden->CAN_DETRACCION}}@endif"
								              placeholder="0.00"
								              autocomplete="off" 
								              class="form-control input-sm dinero" 
								              data-aw="1"
								              readonly="readonly"/>
		                            </div>
		                          </div>
						        </div>


				        <input type="hidden" name="total_documento" id = 'total_documento' value='{{$asiento->CAN_TOTAL_DEBE}}'>
				        <input type="hidden" name="anio_configuracion" id = 'anio_configuracion' value='{{$anio}}'>
				        <input type="hidden" name="periodo_id_configuracion" id = 'periodo_id_configuracion' value='{{$periodo_id}}'>
				        <input type="hidden" name="serie_configuracion" id = 'serie_configuracion' value='{{$serie}}'>
				        <input type="hidden" name="documento_configuracion" id = 'documento_configuracion' value='{{$documento}}'>
				        <input type="hidden" name="asiento_id_configuracion" id = 'asiento_id_configuracion' value='{{$asiento->COD_ASIENTO}}'>
				        <input type="hidden" name="opcion_configuracion" id = 'opcion_configuracion' value='{{$idopcion}}'>



			                </div>


			              </div>
			            </div>
			        </div>


	            </div>

	          </div>
	        </div>
	      </div>
</div>

	<div class="modal-footer">
	  <button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>
	  <button type="submit" data-dismiss="modal" class="btn btn-success btn-modificar-asiento">Guardar</button>
	</div>
</form>


@if(isset($ajax))
<script type="text/javascript">

	$(".select2").select2({
      width: '100%'
    });

    $(".datetimepicker2").datetimepicker({
    	autoclose: true,
      	pickerPosition: "bottom-left",
    	componentIcon: '.mdi.mdi-calendar',
    	navIcons:{
    		rightIcon: 'mdi mdi-chevron-right',
    		leftIcon: 'mdi mdi-chevron-left'
    	}
    });



    $('.dinero').inputmask({ 'alias': 'numeric', 
    'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
    'digitsOptional': false, 
    'prefix': '', 
    'placeholder': '0'});
</script> 
@endif
