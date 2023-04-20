<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte" style="margin-top: 20px;">
    <div class="form-group">
      <label class="col-sm-12 control-label labelleft" >Cuenta Inicio :</label>
      <div class="col-sm-12 abajocaja" >
        {!! Form::select( 'cuenta_inicio_id', $combo_cuenta, array(),
                          [
                            'class'       => 'select3 form-control control input-xs' ,
                            'id'          => 'cuenta_inicio_id',
                            'required'    => '',
                            'data-aw'     => '1',
                          ]) !!}
      </div>
    </div>
</div>


<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 cajareporte" style="margin-top: 20px;">
    <div class="form-group">
      <label class="col-sm-12 control-label labelleft" >Cuenta Fin :</label>
      <div class="col-sm-12 abajocaja" >
        {!! Form::select( 'cuenta_fin_id', $combo_cuenta, array(),
                          [
                            'class'       => 'select3 form-control control input-xs' ,
                            'id'          => 'cuenta_fin_id',
                            'required'    => '',
                            'data-aw'     => '1',
                          ]) !!}
      </div>
    </div>
</div>


@if(isset($ajax))
<script type="text/javascript">
	$(".select3").select2({
      width: '100%'
    });
</script> 
@endif
