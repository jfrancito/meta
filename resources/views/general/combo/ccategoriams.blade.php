<div class="form-group">
<label class="col-sm-12 control-label labelleft" >Sub Categoria :</label>
<div class="col-sm-12 abajocaja" >
  {!! Form::select( 'sub_categoria_id', $combo_sub_categoria, $sel_sub_categoria_id,
                    [
                      'class'       => 'select2 form-control control input-xs' ,
                      'id'          => 'sub_categoria_id',
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
