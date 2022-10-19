<div class="form-group">
<label class="col-sm-12 control-label labelleft negrita" >Cuenta contable : </label>
<div class="col-sm-12 abajocaja" >
  {!! Form::select( 'cuenta_contable_id', $combo_cuenta, $defecto_cuenta,
                    [
                      'class'       => 'select3 form-control control input-xs combo' ,
                      'id'          => 'cuenta_contable_id',
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
