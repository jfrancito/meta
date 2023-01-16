<div class="form-group">
<label class="col-sm-12 control-label labelleft" >Periodo :</label>
<div class="col-sm-12 abajocaja" >
  {!! Form::select( 'periodo_asiento_id', $combo_periodo, $sel_periodo,
                    [
                      'class'       => 'select2 form-control control input-xs' ,
                      'id'          => 'periodo_asiento_id',
                      'required'    => '',
                      'data-aw'     => '1',
                    ]) !!}
</div>
</div>


@if(isset($ajax))
<script type="text/javascript">
	$(".select2").select2({
      width: '100%'
    });
</script> 
@endif
