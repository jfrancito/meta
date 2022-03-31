<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title"><small>{{$anio}}</small> - {{$cuenta_contable->nombre}} <span>({{{$cuenta_contable->nro_cuenta}}})</span></h3>

</div>
	<div class="modal-body">
                <div class="tab-container">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#configuracion" data-toggle="tab">Comfiguración</a></li>
                    <li><a href="#compras"  data-toggle="tab">Compras</a></li>
                  </ul>
                  <div class="tab-content">
                    <div id="configuracion" class="tab-pane active cont">

						<form method="POST" action="{{ url('/guardar-configuracion-cuenta-contable/'.$idopcion) }}">
						      {{ csrf_field() }}
							<div  class="row regla-modal">
								<input type="hidden" name="cuenta_contable_id" value="{{$cuenta_contable->id}}">
								<input type="hidden" name="anio" value="{{$anio}}">
							    <div class="col-md-6">

							    	<p class="titulo-contenedor-modal">Clasificación :</p>

							        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							              <div class="form-group">
							                <label class="col-sm-12 control-label labelleft negrita" >Clase :</label>
							                <div class="col-sm-12 abajocaja" >
							                  {!! Form::select( 'clase_id', $combo_clase, $cuenta_contable->clase_categoria_id,
							                                    [
							                                      'class'       => 'select2 form-control control input-xs combo' ,
							                                      'id'          => 'clase_id',
							                                      'required'    => '',
							                                      'data-aw'     => '1',
							                                    ]) !!}
							                </div>
							              </div>
							        </div>


							        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							              <div class="form-group">
							                <label class="col-sm-12 control-label labelleft negrita" >Tipo de saldo :</label>
							                <div class="col-sm-12 abajocaja" >
							                  {!! Form::select( 'tiposaldo_id', $combo_tipo_saldo, $cuenta_contable->tipo_saldo_categoria_id,
							                                    [
							                                      'class'       => 'select2 form-control control input-xs combo' ,
							                                      'id'          => 'tiposaldo_id',
							                                      'required'    => '',
							                                      'data-aw'     => '1',
							                                    ]) !!}
							                </div>
							              </div>
							        </div>


							        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							              <div class="form-group">
							                <label class="col-sm-12 control-label labelleft negrita" >Tipo de cuenta :</label>
							                <div class="col-sm-12 abajocaja" >
							                  {!! Form::select( 'tipocuenta_id', $combo_tipo_cuenta, $cuenta_contable->tipo_cuenta_categoria_id,
							                                    [
							                                      'class'       => 'select2 form-control control input-xs combo' ,
							                                      'id'          => 'tipocuenta_id',
							                                      'required'    => '',
							                                      'data-aw'     => '1',
							                                    ]) !!}
							                </div>
							              </div>
							        </div>


							    </div>
							    <div class="col-md-12" style="margin-top: 15px;text-align: right;">
							    		  <button type="submit" data-dismiss="modal" class="btn btn-success modal-close">Guardar</button>
							    </div>
							</div>
						</form>

                    </div>
                    <div id="compras" class="tab-pane cont">
						<form method="POST" action="{{ url('/guardar-compras-cuenta-contable/'.$idopcion) }}">
						      {{ csrf_field() }}
							<div  class="row regla-modal">
								<input type="hidden" name="cuenta_contable_id" value="{{$cuenta_contable->id}}">
								<input type="hidden" name="anio" value="{{$anio}}">


							    <div class="col-md-6">
							        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							            <div class="form-group">
											<label class="col-sm-12 control-label labelleft negrita" >Transferencia debe 01: </label>
											<div class="col-sm-12 abajocaja" >
											  {!! Form::select( 'cuenta_contable_tran_debe01_id', $combo_cuenta_tran_debe01, $cuenta_contable->cuenta_contable_transferencia_debe,
											                    [
											                      'class'       => 'select2 form-control control input-xs combo' ,
											                      'id'          => 'cuenta_contable_tran_debe01_id',
											                      'data-aw'     => '1',
											                    ]) !!}
											</div>
										</div>
							        </div>
							    </div>

							    <div class="col-md-6">
							        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							            <div class="form-group">
											<label class="col-sm-12 control-label labelleft negrita" >Porcentaje debe 01: </label>
											<div class="col-sm-12 abajocaja" >
										      <input  type="text"
										              id="cuenta_contable_por_debe01_id" name='cuenta_contable_por_debe01_id' 
										              value="{{$cuenta_contable->transferencia_debe_porcentaje}}" 
										              placeholder="Porcentaje transferencia debe 01"
										              autocomplete="off" class="form-control input-sm" data-aw="1"/>
											</div>
										</div>
							        </div>
							    </div>


							    <div class="col-md-6">
							        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							            <div class="form-group">
											<label class="col-sm-12 control-label labelleft negrita" >Transferencia debe 02: </label>
											<div class="col-sm-12 abajocaja" >
											  {!! Form::select( 'cuenta_contable_tran_debe02_id', $combo_cuenta_tran_debe02, $cuenta_contable->cuenta_contable_transferencia_debe02,
											                    [
											                      'class'       => 'select2 form-control control input-xs combo' ,
											                      'id'          => 'cuenta_contable_tran_debe02_id',
											                      'data-aw'     => '1',
											                    ]) !!}
											</div>
										</div>
							        </div>
							    </div>


							    <div class="col-md-6">
							        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							            <div class="form-group">
											<label class="col-sm-12 control-label labelleft negrita" >Porcentaje debe 02: </label>
											<div class="col-sm-12 abajocaja" >
										      <input  type="text"
										              id="cuenta_contable_por_debe02_id" name='cuenta_contable_por_debe02_id' 
										              value="{{$cuenta_contable->transferencia_debe02_porcentaje}}" 
										              placeholder="Porcentaje transferencia debe 01"
										              autocomplete="off" class="form-control input-sm" data-aw="1"/>
											</div>
										</div>
							        </div>
							    </div>

							    <div class="col-md-6">
							        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							            <div class="form-group">
											<label class="col-sm-12 control-label labelleft negrita" >Transferencia haber: </label>
											<div class="col-sm-12 abajocaja" >
											  {!! Form::select( 'cuenta_contable_tran_haber_id', $combo_cuenta_tran_haber, $cuenta_contable->cuenta_contable_transferencia_haber,
											                    [
											                      'class'       => 'select2 form-control control input-xs combo' ,
											                      'id'          => 'cuenta_contable_tran_haber_id',
											                      'data-aw'     => '1',
											                    ]) !!}
											</div>
										</div>
							        </div>
							    </div>


							    <div class="col-md-12" style="margin-top: 15px;text-align: right;">
							    		  <button type="submit" data-dismiss="modal" class="btn btn-success modal-close">Guardar</button>
							    </div>
							</div>
						</form>
                    </div>
                  </div>
                </div>
	</div>





