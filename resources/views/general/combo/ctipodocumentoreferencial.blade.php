<div class="form-group quitar-tb ">
  <label class="col-sm-12 control-label izquierda">Tipo documento Referencia</label>
  <div class="col-sm-12">
    {!! Form::select( 'tipo_documento_referencia', $combo_tipo_documento_re, $defecto_tipo_documento,
                      [
                        'class'       => 'select2 form-control control input-xs' ,
                        'id'          => 'tipo_documento_referencia',
                        'data-aw'     => '9'
                      ]) !!}
      @include('error.erroresvalidate', [ 'id' => $errors->has('tipo_documento_referencia')  , 
                                          'error' => $errors->first('tipo_documento_referencia', ':message') , 
                                          'data' => '9'])
  </div>
</div>

