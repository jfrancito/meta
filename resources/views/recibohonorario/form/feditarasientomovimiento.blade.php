<div class="col-md-12">

	        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	              <div class="form-group">
	                <label class="col-sm-12 control-label labelleft negrita" >Nivel:</label>
	                <div class="col-sm-12 abajocaja" >
	                  {!! Form::select( 'nivel', $combo_nivel_pc, $defecto_nivel,
	                                    [
	                                      'class'       => 'select2 form-control control input-xs combo' ,
	                                      'id'          => 'nivel',
	                                      'data-aw'     => '1',
	             						  'disabled'   => 'disabled'
	                                    ]) !!}
	                </div>
	              </div>
	        </div>


	        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	              <div class="form-group">
	                <label class="col-sm-12 control-label labelleft negrita" >Partida :</label>
	                <div class="col-sm-12 abajocaja" >
	                  {!! Form::select( 'partida_id', $combo_partida, $defecto_partida,
	                                    [
	                                      'class'       => 'select2 form-control control input-xs combo' ,
	                                      'id'          => 'partida_id',
	                                      'data-aw'     => '2',
	                                    ]) !!}
	                </div>
	              </div>
	        </div>


	        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ajax_nivel">
	        	<div class="form-group">
					<label class="col-sm-12 control-label labelleft negrita" >Cuenta contable : </label>
					<div class="col-sm-12 abajocaja" >
					  {!! Form::select( 'cuenta_contable_id', $combo_cuenta, $defecto_cuenta,
					                    [
					                      'class'       => 'select2 form-control control input-xs combo' ,
					                      'id'          => 'cuenta_contable_id',
					                      'data-aw'     => '1',
					                    ]) !!}
					</div>
				</div>
	        </div>

	        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			  	<div class="form-group">
				    <label class="col-sm-12 control-label labelleft negrita" >Monto :</label>
				    <div class="col-sm-12">

				        <input  type="text"
				                id="monto" 
				                name='monto' 
				                value=""
				                placeholder="Monto"
				                autocomplete="off" class="form-control dinero input-sm" data-aw="4"/>

				    </div>
			  	</div>
	        </div>


	        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
	              <div class="form-group">
	                <label class="col-sm-12 control-label labelleft negrita" >Estado :</label>
	                <div class="col-sm-12 abajocaja" >
	                  {!! Form::select( 'activo', $combo_activo, $defecto_activo,
	                                    [
	                                      'class'       => 'select2 form-control control input-xs combo' ,
	                                      'id'          => 'activo',
	                                      'data-aw'     => '2',
	                                    ]) !!}
	                </div>
	              </div>
	        </div>


	        <input type="hidden" name="asiento_movimiento_id" id='asiento_movimiento_id'>
	        <input type="hidden" name="asiento_id_editar" id='asiento_id_editar' value='{{$asiento->COD_ASIENTO}}'>
	        <input type="hidden" name="partida_id" id='partida_id' value='{{$asiento->COD_ASIENTO}}'>
	        <input type="hidden" name="accion" id='accion' >
	        <input type="hidden" name="ruta" id='ruta' value='{{$ruta}}'>

	        <div class="col-lg-12" style="    margin-top: 20px;
			    text-align: right;
			    margin-bottom: 40px;">
	        	<div class="col-lg-12">
	        	  	<button type="button" class="btn btn-default btn-space btn-regresar-lista">Regresar</button>
						<button type="button" class="btn btn-primary btn-editar-asiento">Editar</button>
					</div>
				</div>
</div>