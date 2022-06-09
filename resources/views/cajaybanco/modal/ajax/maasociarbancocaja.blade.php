<div class="modal-header">
		<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
		<h3 class="modal-title">
	        @if($cajabanco->IND_CAJA == '1') 
	            {{$cajabanco->TXT_CAJA_BANCO}}
	        @else
	            {{$cajabanco->TXT_BANCO}}
	        @endif
			<span>({{{$cajabanco->TXT_NRO_CCI}}})</span></h3>

</div>
	<div class="modal-body">
        <div class="tab-container">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#configuracion" data-toggle="tab">Comfiguración</a></li>
          </ul>
          <div class="tab-content">
            <div id="configuracion" class="tab-pane active cont">

				<form method="POST" action="{{ url('/guardar-asociacion-banco-caja/'.$idopcion) }}">
				      {{ csrf_field() }}
					<div  class="row regla-modal">

						<input type="hidden" name="caja_banco_id" value="{{$cajabanco->COD_CAJA_BANCO}}">

					    <div class="col-md-6">

					    	<p class="titulo-contenedor-modal">Asociar caja y banco :</p>
					        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					              <div class="form-group">
					                <label class="col-sm-12 control-label labelleft negrita" >Caja y banco :</label>
					                <div class="col-sm-12 abajocaja" >
					                  {!! Form::select( 'cuenta_contable_id', $combo_cuenta_contable, '',
					                                    [
					                                      'class'       => 'select2 form-control control input-xs combo' ,
					                                      'id'          => 'cuenta_contable_id',
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
          </div>
        </div>
	</div>





