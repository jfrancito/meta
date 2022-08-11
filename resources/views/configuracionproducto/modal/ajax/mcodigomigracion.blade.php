
<div class="modal-header">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<h3 class="modal-title">
		 Configuración <span>(codigo migracion)</span>
	</h3>
	<input type="hidden" name="array_productos_cm" id="array_productos" value='{{$array_productos}}'>
</div>
<div class="modal-body">
	<div  class="row regla-modal">
	    <div class="col-md-12">
              <div class="panel panel-default">
                <div class="tab-container">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#cm" id="ventastab" data-toggle="tab">Codigo Migracion</a></li>
                  </ul>
                  <div class="tab-content">
                    <div id="cm" class="tab-pane active cont">

				        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

							<div class="form-group">
							  <label class="col-sm-3 control-label">Codigo Migracion</label>
							  <div class="col-sm-6">

							      <input  type="text"
							              id="codigo_migracion" name='codigo_migracion' 
							              placeholder="Codigo Migracion"
							              required = ""
							              autocomplete="off" class="form-control input-sm" data-aw="1"/>

							  </div>
							</div>
				        </div>
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
  <button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-configuracion-cm">Guardar</button>
</div>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
      App.formElements();
    });
  </script>
@endif




