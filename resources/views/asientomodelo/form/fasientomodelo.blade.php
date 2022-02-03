<div class="form-group">
  <label class="col-sm-3 control-label">Nombres</label>
  <div class="col-sm-6">

      <input  type="text"
              id="nombre" name='nombre' 
              value="@if(isset($asientomodelo)){{old('nombre' ,$asientomodelo->nombre)}}@else{{old('nombre')}}@endif"
              value="{{ old('nombre') }}" 
              placeholder="Nombre"
              required = ""
              autocomplete="off" class="form-control input-sm" data-aw="1"/>

      @include('error.erroresvalidate', [ 'id' => $errors->has('nombre')  , 
                                          'error' => $errors->first('nombre', ':message') , 
                                          'data' => '1'])

  </div>
</div>


<div class="form-group">

  <label class="col-sm-3 control-label">Tipo de asiento</label>
  <div class="col-sm-6">
    {!! Form::select( 'tipo_asiento_id'
                      , $combo_tipo_asiento
                      , $defecto_tipo_asiento
                      ,[
                        'class'       => 'select2 form-control control input-xs' ,
                        'id'          => 'tipo_asiento_id',
                        'required'    => '',
                        'data-aw'     => '2'
                      ]) !!}

      @include('error.erroresvalidate', [ 'id' => $errors->has('tipo_asiento_id')  , 
                                          'error' => $errors->first('tipo_asiento_id', ':message') , 
                                          'data' => '2'])

  </div>
</div>


<div class="form-group">

  <label class="col-sm-3 control-label">Moneda</label>
  <div class="col-sm-6">
    {!! Form::select( 'moneda_id', $combo_moneda, $defecto_moneda,
                      [
                        'class'       => 'select2 form-control control input-xs' ,
                        'id'          => 'moneda_id',
                        'required'    => '',
                        'data-aw'     => '3'
                      ]) !!}

      @include('error.erroresvalidate', [ 'id' => $errors->has('moneda_id')  , 
                                          'error' => $errors->first('moneda_id', ':message') , 
                                          'data' => '3'])

  </div>
</div>


<div class="form-group">

  <label class="col-sm-3 control-label">Tipo cliente</label>
  <div class="col-sm-6">
    {!! Form::select( 'tipo_cliente', $combo_tipo_cliente, $defecto_tipo_cliente,
                      [
                        'class'       => 'select2 form-control control input-xs' ,
                        'id'          => 'tipo_cliente',
                        'required'    => '',
                        'data-aw'     => '4'
                      ]) !!}

      @include('error.erroresvalidate', [ 'id' => $errors->has('tipo_cliente')  , 
                                          'error' => $errors->first('tipo_cliente', ':message') , 
                                          'data' => '4'])

  </div>
</div>


<div class="form-group">

  <label class="col-sm-3 control-label">Tipo igv</label>
  <div class="col-sm-6">
    {!! Form::select( 'tipo_igv_id', $combo_tipo_igv, $defecto_tipo_igv,
                      [
                        'class'       => 'select2 form-control control input-xs' ,
                        'id'          => 'tipo_igv_id',
                        'required'    => '',
                        'data-aw'     => '5'
                      ]) !!}

      @include('error.erroresvalidate', [ 'id' => $errors->has('tipo_igv_id')  , 
                                          'error' => $errors->first('tipo_igv_id', ':message') , 
                                          'data' => '5'])

  </div>
</div>


<div class="form-group">

  <label class="col-sm-3 control-label">Tipo documento</label>
  <div class="col-sm-6">
    {!! Form::select( 'tipo_documento[]', $combo_tipo_documento, $defecto_tipo_documento,
                      [
                        'class'       => 'select2 form-control control input-xs' ,
                        'id'          => 'tipo_documento',
                        'required'    => '',
                        'multiple'    => '',
                        'data-aw'     => '6'
                      ]) !!}

      @include('error.erroresvalidate', [ 'id' => $errors->has('tipo_documento')  , 
                                          'error' => $errors->first('tipo_documento', ':message') , 
                                          'data' => '6'])

  </div>
</div>






@if(isset($asientomodelo))
<div class="form-group">
  <label class="col-sm-3 control-label">Activo</label>
  <div class="col-sm-6">
    <div class="be-radio has-success inline">
      <input type="radio" value='1' name="activo" id="rad6" @if($asientomodelo->activo == 1) checked @endif>
      <label for="rad6">Activado</label>
    </div>
    <div class="be-radio has-danger inline">
      <input type="radio" value='0' name="activo" id="rad8" @if($asientomodelo->activo == 0) checked @endif >
      <label for="rad8">Desactivado</label>
    </div>
  </div>
</div> 
@endif

<div class="row xs-pt-15">
  <div class="col-xs-6">
      <div class="be-checkbox">

      </div>
  </div>
  <div class="col-xs-6">
    <p class="text-right">
      <button type="submit" class="btn btn-space btn-primary">Guardar</button>
    </p>
  </div>
</div>