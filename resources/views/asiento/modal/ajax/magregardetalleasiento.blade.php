
<div class="modal-header">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<h3 class="modal-title">
		 DETALLE ASIENTO
	</h3>
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
		                                      'class'       => 'select3 form-control control input-xs combo' ,
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
		                                      'class'       => 'select3 form-control control input-xs combo' ,
		                                      'id'          => 'partida_id',
		                                      'data-aw'     => '2',
		                                    ]) !!}
		                </div>
		              </div>
		        </div>


		        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ajax_nivel">
		        	@include('plancontable.combo.cnrocuentanombre', ['defecto_cuenta' => $defecto_cuenta])
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

		    </div>
	    <div class="col-md-6">
	    </div>
	</div>
</div>

<div class="modal-footer">
  <button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-detalle-asiento">Guardar</button>
</div>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){

		$(".select3").select2({
	      width: '100%'
	    });

	    $('.dinero').inputmask({ 'alias': 'numeric', 
	      'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
	      'digitsOptional': false, 
	      'prefix': '', 
	      'placeholder': '0'});

    });
  </script>
@endif



