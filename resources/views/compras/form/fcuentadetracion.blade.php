<div class="form-group">
  <label class="col-sm-3 control-label">Nrº cuenta</label>
  <div class="col-sm-5">

    <input  type="text"
            id="NRO_CUENTA" name='NRO_CUENTA'
            value="@if(isset($cuentadetraccion)){{old('NRO_CUENTA' ,$cuentadetraccion->NRO_CUENTA)}}@else{{old('NRO_CUENTA')}}@endif" 
            placeholder="Nro cuenta"
            required = ""
            autocomplete="off" class="form-control input-sm" data-aw="1"/>

      @include('error.erroresvalidate', [ 'id' => $errors->has('NRO_CUENTA')  , 
                                          'error' => $errors->first('NRO_CUENTA', ':message') , 
                                          'data' => '4'])

  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label">Porcentaje</label>
  <div class="col-sm-5">

    <input  type="text"
            id="PORCENTAJE_DETRACION" name='PORCENTAJE_DETRACION'
            value="@if(isset($cuentadetraccion)){{old('PORCENTAJE_DETRACION' ,$cuentadetraccion->PORCENTAJE_DETRACION)}}@else{{old('PORCENTAJE_DETRACION')}}@endif" 
            placeholder="Porcentaje"
            required = ""
            autocomplete="off" class="form-control input-sm" data-aw="2"/>

      @include('error.erroresvalidate', [ 'id' => $errors->has('PORCENTAJE_DETRACION')  , 
                                          'error' => $errors->first('PORCENTAJE_DETRACION', ':message') , 
                                          'data' => '4'])

  </div>
</div>


<div class="form-group">
  <label class="col-sm-3 control-label">Tipo operacion</label>
  <div class="col-sm-5">

    <input  type="text"
            id="TIPO_OPERACION" name='TIPO_OPERACION'
            value="@if(isset($cuentadetraccion)){{old('TIPO_OPERACION' ,$cuentadetraccion->TIPO_OPERACION)}}@else{{old('TIPO_OPERACION')}}@endif"
            placeholder="Tipo operacion"
            required = ""
            autocomplete="off" class="form-control input-sm" data-aw="3"/>

      @include('error.erroresvalidate', [ 'id' => $errors->has('TIPO_OPERACION')  , 
                                          'error' => $errors->first('TIPO_OPERACION', ':message') , 
                                          'data' => '4'])

  </div>
</div>


<div class="form-group">
  <label class="col-sm-3 control-label">Tipo bien o servicio</label>
  <div class="col-sm-5">

    <input  type="text"
            id="TIPO_BIEN_SERVICIO" name='TIPO_BIEN_SERVICIO'
            value="@if(isset($cuentadetraccion)){{old('TIPO_BIEN_SERVICIO' ,$cuentadetraccion->TIPO_BIEN_SERVICIO)}}@else{{old('TIPO_BIEN_SERVICIO')}}@endif"
            placeholder="Tipo bien o servicio"
            required = ""
            autocomplete="off" class="form-control input-sm" data-aw="4"/>

      @include('error.erroresvalidate', [ 'id' => $errors->has('TIPO_BIEN_SERVICIO')  , 
                                          'error' => $errors->first('TIPO_BIEN_SERVICIO', ':message') , 
                                          'data' => '4'])

  </div>
</div>

<input type="hidden" 
value="@if(isset($cuentadetraccion)){{old('DOCUMENTO' ,$cuentadetraccion->DOCUMENTO)}}@else{{old('DOCUMENTO')}}@endif"
name="DOCUMENTO" id="DOCUMENTO">

<input type="hidden" name="sw_acccion" id="sw_acccion" value='{{$sw_acccion}}'>


<div class="row xs-pt-15">
  <div class="col-xs-6">
      <div class="be-checkbox">

      </div>
  </div>
  <div class="col-xs-6">
    <p class="text-right">
      <button type="submit" class="btn btn-space btn-primary guardarcuentadetraccion">Guardar</button>
    </p>
  </div>
</div>