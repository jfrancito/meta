
<div class="modal-header">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<h3 class="modal-title">
		 Configuración <span>(cuenta contable)</span>
	</h3>
	<input type="hidden" name="array_productos" id="array_productos" value='{{$array_productos}}'>
</div>
<div class="modal-body">
	<div  class="row regla-modal">
	    <div class="col-md-12">

              <div class="panel panel-default">
                <div class="tab-container">
                  <ul class="nav nav-tabs">

                    <li class="active {{$ocultar_venta}}"><a href="#ventas" id="ventastab" data-toggle="tab">Ventas</a></li>

                    <li class="{{$ocultar_compra}}"><a href="#compras" id="comprastab" data-toggle="tab">Compras</a></li>


                  </ul>
                  <div class="tab-content">
                    <div id="ventas" class="tab-pane active cont ">

				        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				            <div class="form-group">
								<label class="col-sm-12 control-label labelleft negrita" >Cuenta contable relacionada: </label>
								<div class="col-sm-12 abajocaja" >
								  {!! Form::select( 'cuenta_contable_rel_id', $combo_cuenta_rel, $defecto_cuenta_rel,
								                    [
								                      'class'       => 'select2 form-control control input-xs combo' ,
								                      'id'          => 'cuenta_contable_rel_id',
								                      'data-aw'     => '1',
								                    ]) !!}
								</div>
							</div>
				        </div>

				        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				            <div class="form-group">
								<label class="col-sm-12 control-label labelleft negrita" >Cuenta contable tercero: </label>
								<div class="col-sm-12 abajocaja" >
								  {!! Form::select( 'cuenta_contable_ter_id', $combo_cuenta_ter, $defecto_cuenta_ter,
								                    [
								                      'class'       => 'select2 form-control control input-xs combo' ,
								                      'id'          => 'cuenta_contable_ter_id',
								                      'data-aw'     => '1',
								                    ]) !!}
								</div>
							</div>
				        </div>
                    </div>
                    <div id="compras" class="tab-pane cont ">
  				        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				            <div class="form-group">
								<label class="col-sm-12 control-label labelleft negrita" >Cuenta contable compras: </label>
								<div class="col-sm-12 abajocaja" >
								  {!! Form::select( 'cuenta_contable_compra_id', $combo_cuenta_com, $defecto_cuenta_com,
								                    [
								                      'class'       => 'select2 form-control control input-xs combo' ,
								                      'id'          => 'cuenta_contable_compra_id',
								                      'data-aw'     => '1',
								                    ]) !!}
								</div>
							</div>
				        </div>
                    </div>
                  </div>
                </div>

                
              </div>



                <input type="hidden" name="ind_venta_compra" id ="ind_venta_compra" value="1">

	    </div>
	    <div class="col-md-6">

	    </div>

	</div>
</div>

<div class="modal-footer">
  <button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-configuracion">Guardar</button>
</div>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
      	App.formElements();

      	 var nro_asiento  =   $('#nro_asiento').val();

      	 var tab = 'ventas';
      	 if(nro_asiento == '4'){
      	 	tab = 'compras';
      	 	$('#ind_venta_compra').val("2");
      	 }
		$('.nav-tabs a[href="#' + tab + '"]').tab('show');

    });
  </script>
@endif




