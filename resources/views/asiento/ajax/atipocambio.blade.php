<div class="form-group quitar-tb">
  <label class="col-sm-12 control-label izquierda">Tipo de cambio <b>(*)</b></label>
  <div class="col-sm-12">

      <input  type="text"
              id="tipocambio" name='tipocambio' 
              value="{{$tipo_cambio->CAN_VENTA_SBS}}"
              placeholder="Tipo de cambio"
              required = ""
              autocomplete="off" class="form-control dinero input-sm" data-aw="5"/>

      @include('error.erroresvalidate', [ 'id' => $errors->has('tipo_asiento_id')  , 
                                          'error' => $errors->first('tipo_asiento_id', ':message') , 
                                          'data' => '5'])

  </div>
</div>