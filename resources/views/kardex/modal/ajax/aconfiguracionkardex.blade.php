
<form method="POST" action="{{ url('/configurar-transferencia-producto/'.$idopcion ) }}">
      {{ csrf_field() }}

	<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title">
			 Agregar <span>(Transferencia de cantidad entre productos)</span>
		</h3>
	</div>
	<div class="modal-body">
		<div  class="row regla-modal">
		    <div class="col-md-12">

		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		              <div class="form-group">
		                <label class="col-sm-12 control-label labelleft negrita" >Producto Salida :</label>
		                <div class="col-sm-12 abajocaja" >
		                  {!! Form::select( 'producto_salida_id', $combo_producto, array(),
		                                    [
		                                      'class'       => 'select2 form-control control input-xs combo' ,
		                                      'id'          => 'producto_salida_id',
		                                      'data-aw'     => '1',
		                                    ]) !!}
		                </div>
		              </div>
		        </div>

		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		              <div class="form-group">
		                <label class="col-sm-12 control-label labelleft negrita" >Producto Ingreso :</label>
		                <div class="col-sm-12 abajocaja" >
		                  {!! Form::select( 'producto_ingreso_id', $combo_producto, array(),
		                                    [
		                                      'class'       => 'select2 form-control control input-xs combo' ,
		                                      'id'          => 'producto_ingreso_id',
		                                      'data-aw'     => '1',
		                                    ]) !!}
		                </div>
		              </div>
		        </div>

		        <div class="col-xs-6 col-sm-6 col-md-12 col-lg-9">
					<div class="form-group">
					  	<label class="col-sm-12 control-label">
					  		<label class="col-sm-12 control-label labelleft negrita" >Fecha Transferencia :</label>
					  	</label>
						<div class="col-sm-12">

                              <div data-min-view="2" 
                                     data-date-format="dd-mm-yyyy"  
                                     class="input-group date datetimepicker " style = 'padding: 0px 0;margin-top: -3px;'>
                                     <input size="16" type="text" 
                                            value="" 
                                            placeholder="Fecha"
                                            id='fecha' 
                                            name='fecha' 
                                            required = ""
                                            class="form-control input-sm"/>
                                      <span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
                              </div>


						</div>
					</div>
				</div>

		        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
		        	<div class="col-sm-12" style="margin-top: 35px;">
					<button type="button" data-dismiss="modal" class="btn btn-success btn_calcular_cu">CU</button>
					</div>
				</div>



		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<div class="form-group">
					  <label class="col-sm-12 control-label labelleft negrita" >Cantidad :</label>
					  <div class="col-sm-12">

					      <input  type="text"
					              id="cantidad" name='cantidad' 
					              value="0" 
					              placeholder="Orden"
					              autocomplete="off" class="form-control input-sm importe" data-aw="1"/>

					  </div>
					</div>
				</div>

		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<div class="form-group">
					  <label class="col-sm-12 control-label labelleft negrita" >CU :</label>
					  <div class="col-sm-12">

					      <input  type="text"
					              id="cu" name='cu' 
					              value="0"
					              readonly = 'readonly'
					              placeholder="Orden"
					              autocomplete="off" class="form-control input-sm importe" data-aw="1"/>

					  </div>
					</div>
				</div>

		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<div class="form-group">
					  <label class="col-sm-12 control-label labelleft negrita" >Importe :</label>
					  <div class="col-sm-12">

					      <input  type="text"
					              id="importe" name='importe' 
					              value="0"
					              readonly = 'readonly'
					              placeholder="Orden"
					              autocomplete="off" class="form-control input-sm importe" data-aw="1"/>

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
      'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
      'digitsOptional': false, 
      'prefix': '', 
      'placeholder': '0'});

    });
  </script>
@endif




