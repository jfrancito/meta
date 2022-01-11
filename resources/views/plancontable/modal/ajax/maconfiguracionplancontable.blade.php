
<form method="POST" action="{{ url('/guardar-configuracion-cuenta-contable/'.$idopcion) }}">
      {{ csrf_field() }}

	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title"><small>{{$anio}}</small> - {{$cuenta_contable->nombre}} <span>({{{$cuenta_contable->nro_cuenta}}})</span></h3>

	</div>
	<div class="modal-body">
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
		    <div class="col-md-6">

		    </div>

		</div>
	</div>

	<div class="modal-footer">
	  <button type="submit" data-dismiss="modal" class="btn btn-success modal-close">Guardar</button>
	</div>
</form>




