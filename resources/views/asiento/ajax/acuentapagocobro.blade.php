<div class="form-group">
<label class="col-sm-12 control-label izquierda">Correspondiente a la cuenta </label>
  <div class="col-sm-12 abajocaja" >
    {!! Form::select( 'cuenta_id', $combo_cuenta_corriente, $sel_cuenta_corriente,
                      [
                        'class'       => 'select3 form-control control input-xs' ,
                        'id'          => 'cuenta_id',
                        'required'    => '',
                        'data-aw'     => '1',
                      ]) !!}
  </div>
</div>
@if(isset($ajax))
<script type="text/javascript">
	$(".select3").select2({
      width: '100%'
    });
</script> 
@endif