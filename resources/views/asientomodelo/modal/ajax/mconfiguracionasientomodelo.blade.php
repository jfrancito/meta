
<form method="POST" action="{{ url('/configurar-asiento-modelo/'.$idopcion.'/'.Hashids::encode(substr($asientomodelo->id, -8))) }}">
      {{ csrf_field() }}

	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title">
			 {{$asientomodelo->nombre}} <span>({{$asientomodelo->tipoasiento->NOM_CATEGORIA}})</span>
		</h3>
		<input type="hidden" name="asiento_modelo_detalle_id" id="asiento_modelo_detalle_id" value='{{$asiento_modelo_detalle_id}}'>
	</div>
	<div class="modal-body">
		<div  class="row regla-modal">
		    <div class="col-md-12">

		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		              <div class="form-group">
		                <label class="col-sm-12 control-label labelleft negrita" >Nivel :</label>
		                <div class="col-sm-12 abajocaja" >
		                  {!! Form::select( 'nivel', $combo_nivel_pc, $defecto_nivel,
		                                    [
		                                      'class'       => 'select2 form-control control input-xs combo' ,
		                                      'id'          => 'nivel',
		                                      'data-aw'     => '1',
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
		                                      'data-aw'     => '1',
		                                    ]) !!}
		                </div>
		              </div>
		        </div>

		        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ajax_nivel">
		        	@include('plancontable.combo.cnrocuentanombre', ['defecto_cuenta' => $defecto_cuenta])
		        </div>

		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<div class="form-group">
					  <label class="col-sm-12 control-label labelleft negrita" >Orden :</label>
					  <div class="col-sm-12">

					      <input  type="text"
					              id="orden" name='orden' 
					              value="@if(isset($asientomodelodetalle)){{old('orden' ,$asientomodelodetalle->orden)}}@else{{old('orden' ,$max_nro_asientomodelo)}}@endif" 
					              placeholder="Orden"
					              autocomplete="off" class="form-control input-sm importe" data-aw="1"/>

					  </div>
					</div>
				</div>

				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<div class="form-group">
					  <label class="col-sm-12 control-label labelleft negrita" >Activo :</label>
					  <div class="col-sm-12">
					    <div class="be-radio has-success inline">
					      <input type="radio" value='1' name="activo" id="rad6"
					      	@if(isset($asientomodelodetalle))
						      @if($asientomodelodetalle->activo == 1) 
						      	checked 
						      @endif
						    @endif
					      >
					      <label for="rad6">Activado</label>
					    </div>
					    <div class="be-radio has-danger inline">
					      <input type="radio" value='0' name="activo" id="rad8" @
					     	@if(isset($asientomodelodetalle))
						      @if($asientomodelodetalle->activo == 0) 
						      	checked 
						      @endif
						    @endif
					      >
					      <label for="rad8">Desactivado</label>
					    </div>
					  </div>
					</div> 
				</div>

		    </div>
		    <div class="col-md-6">

		    </div>

		</div>
	</div>

	<div class="modal-footer">
	  <button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-configuracion">Guardar</button>
	</div>
</form>
@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){

      App.formElements();
      $('.importe').inputmask({ 'alias': 'numeric', 
      'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});

    });
  </script>
@endif




