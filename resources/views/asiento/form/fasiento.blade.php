
<div class="col-sm-4">

  <div class="form-group quitar-tb">
    <label class="col-sm-12 control-label izquierda" >Año <b>(*)</b></label>
    <div class="col-sm-12 abajocaja" >
      {!! Form::select( 'anio', $combo_anio_pc, $anio,
                        [
                          'class'       => 'select2 form-control control input-xs' ,
                          'id'          => 'anio',
                          'required'    => '',
                          'data-aw'     => '1',
                        ]) !!}

        @include('error.erroresvalidate', [ 'id' => $errors->has('tipo_asiento_id')  , 
                                            'error' => $errors->first('tipo_asiento_id', ':message') , 
                                            'data' => '1'])

    </div>
  </div>

  <div class="form-group quitar-tb">
    <label class="col-sm-12 control-label izquierda" >Periodo <b>(*)</b></label>
    <div class="col-sm-12 abajocaja" >
      {!! Form::select( 'periodo_id', $combo_periodo, $sel_periodo,
                        [
                          'class'       => 'select2 form-control control input-xs' ,
                          'id'          => 'periodo_id',
                          'required'    => '',
                          'data-aw'     => '2',
                        ]) !!}

        @include('error.erroresvalidate', [ 'id' => $errors->has('tipo_asiento_id')  , 
                                            'error' => $errors->first('tipo_asiento_id', ':message') , 
                                            'data' => '2'])

    </div>
  </div>

  <div class="form-group quitar-tb">
    <label class="col-sm-12 control-label izquierda">Tipo de asiento <b>(*)</b></label>
    <div class="col-sm-12">
      {!! Form::select( 'tipo_asiento_id'
                        , $combo_tipo_asiento
                        , $defecto_tipo_asiento
                        ,[
                          'class'       => 'select2 form-control control input-xs' ,
                          'id'          => 'tipo_asiento_id',
                          'required'    => '',
                          'data-aw'     => '3'
                        ]) !!}

        @include('error.erroresvalidate', [ 'id' => $errors->has('tipo_asiento_id')  , 
                                            'error' => $errors->first('tipo_asiento_id', ':message') , 
                                            'data' => '3'])

    </div>
  </div>

  <div class="form-group quitar-tb">

    <label class="col-sm-12 control-label izquierda">Moneda <b>(*)</b></label>
    <div class="col-sm-12">
      {!! Form::select( 'moneda_id', $combo_moneda, $defecto_moneda,
                        [
                          'class'       => 'select2 form-control control input-xs' ,
                          'id'          => 'moneda_id',
                          'required'    => '',
                          'data-aw'     => '4'
                        ]) !!}

        @include('error.erroresvalidate', [ 'id' => $errors->has('moneda_id')  , 
                                            'error' => $errors->first('moneda_id', ':message') , 
                                            'data' => '4'])

    </div>
  </div>


  <div class='ajax_tipocambio'>

    @include('asiento.ajax.atipocambio')
    
  </div>

  <div class="form-group quitar-tb">

    <label class="col-sm-12 control-label izquierda">RUC </label>
    <div class="col-sm-12">
      {!! Form::select( 'cliente_id', $combo_empresa, $defecto_empresa,
                        [
                          'class'       => 'select2 form-control control input-xs' ,
                          'id'          => 'cliente_id',
                          'required'    => '',
                          'data-aw'     => '4'
                        ]) !!}



    </div>
  </div>



</div>

<div class="col-sm-4">
  <div class="form-group quitar-tb">
    <label class="col-sm-12 control-label izquierda">Tipo documento</label>
    <div class="col-sm-12">
      {!! Form::select( 'tipo_documento[]', $combo_tipo_documento, $defecto_tipo_documento,
                        [
                          'class'       => 'select2 form-control control input-xs' ,
                          'id'          => 'tipo_documento',
                          'data-aw'     => '6'
                        ]) !!}
        @include('error.erroresvalidate', [ 'id' => $errors->has('tipo_documento')  , 
                                            'error' => $errors->first('tipo_documento', ':message') , 
                                            'data' => '6'])
    </div>
  </div>


  <div class="form-group quitar-tb">
    <label class="col-sm-12 control-label izquierda">Serie</label>
    <div class="col-sm-12">

        <input  type="text"
                id="serie" name='serie' 
                value=""
                placeholder="Serie"
                autocomplete="off" class="form-control input-sm" data-aw="7"/>

        @include('error.erroresvalidate', [ 'id' => $errors->has('serie')  , 
                                            'error' => $errors->first('serie', ':message') , 
                                            'data' => '7'])

    </div>
  </div>

  <div class="form-group quitar-tb">
    <label class="col-sm-12 control-label izquierda">Nro. Comprobante </label>
    <div class="col-sm-12">

        <input  type="text"
                id="nrocomprobante" name='nrocomprobante' 
                value=""
                placeholder="Nro. Comprobante"
                autocomplete="off" class="form-control input-sm" data-aw="8"/>

        @include('error.erroresvalidate', [ 'id' => $errors->has('nrocomprobante')  , 
                                            'error' => $errors->first('nrocomprobante', ':message') , 
                                            'data' => '8'])

    </div>
  </div>



  <div class="form-group quitar-tb">
      <label class="col-sm-12 control-label izquierda">Fecha Vencimiento</label>
      <div class="col-sm-12">
          <div data-min-view="2" 
                 data-date-format="dd-mm-yyyy"  
                 class="input-group date datetimepicker" style = 'padding: 0px 0;margin-top: -3px;'>
                 <input size="16" type="text"  
                        placeholder="Fecha vencimiento"
                        id='fechavencimiento' 
                        name='fechavencimiento' 
                        class="form-control input-sm"/>
                  <span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
            </div>

      </div>
  </div>


  <div class="form-group quitar-tb">
      <label class="col-sm-12 control-label izquierda">Fecha Documento <b>(*)</b></label>
      <div class="col-sm-12">
          <div data-min-view="2" 
                 data-date-format="dd-mm-yyyy"  
                 class="input-group date datetimepicker2" style = 'padding: 0px 0;margin-top: -3px;'>
                 <input size="16" type="text" 
                        value="{{$fecha}}" 
                        placeholder="Fecha Documento"
                        id='fechadocumento' 
                        name='fechadocumento' 
                        class="form-control input-sm"/>
                  <span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
            </div>
      </div>
  </div>
</div>


<div class="col-sm-4 ">

  <div class='ajax_tiporeferencia'>
    @include('general.combo.ctipodocumentoreferencial')
  </div>

  <div class="form-group quitar-tb">
    <label class="col-sm-12 control-label izquierda">Serie Referencia</label>
    <div class="col-sm-12">

        <input  type="text"
                id="seriereferencia" name='seriereferencia' 
                value=""
                placeholder="Serie Referencia"
                autocomplete="off" class="form-control input-sm" data-aw="10"/>

        @include('error.erroresvalidate', [ 'id' => $errors->has('seriereferencia')  , 
                                            'error' => $errors->first('seriereferencia', ':message') , 
                                            'data' => '10'])

    </div>
  </div>

  <div class="form-group quitar-tb">
    <label class="col-sm-12 control-label izquierda">Nro. Comprobante Referencia</label>
    <div class="col-sm-12">

        <input  type="text"
                id="nrocomprobantereferencia" name='nrocomprobantereferencia' 
                value=""
                placeholder="Nro. Comprobante Referencia"
                autocomplete="off" class="form-control input-sm" data-aw="11"/>

        @include('error.erroresvalidate', [ 'id' => $errors->has('nrocomprobantereferencia')  , 
                                            'error' => $errors->first('nrocomprobantereferencia', ':message') , 
                                            'data' => '11'])

    </div>
  </div>


  <div class="form-group quitar-tb">
      <label class="col-sm-12 control-label izquierda">Fecha Referencia</label>
      <div class="col-sm-12">

          <div data-min-view="2" 
                 data-date-format="dd-mm-yyyy"  
                 class="input-group date datetimepicker" style = 'padding: 0px 0;margin-top: -3px;'>
                 <input size="16" type="text" 
                        placeholder="Fecha referencia"
                        id='fechareferencia' 
                        name='fechareferencia' 
                        class="form-control input-sm"/>
                  <span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar"></i></span>
            </div>

      </div>
  </div>


  <div class="form-group quitar-tb">
    <label class="col-sm-12 control-label izquierda">Glosa <b>(*)</b></label>
    <div class="col-sm-12">

        <input  type="text"
                id="glosa" name='glosa' 
                value=""
                placeholder="Glosa"
                required    = 'required',
                autocomplete="off" class="form-control input-sm" data-aw="10"/>

        @include('error.erroresvalidate', [ 'id' => $errors->has('glosa')  , 
                                            'error' => $errors->first('glosa', ':message') , 
                                            'data' => '10'])

    </div>
  </div>


</div>

<input type="hidden" name="pagocobro" id='pagocobro' value='0'>
<input type="hidden" name="ultimalinea" id='ultimalinea' value='0'>

<div class="col-sm-12 listajax">
  @include('asiento.ajax.adetalleasiento')
</div>


<div class="row xs-pt-15">
  <div class="col-xs-6">
      <div class="be-checkbox">

      </div>
  </div>
  <div class="col-xs-6">
    <p class="text-right">
      <button type="button" class="btn btn-space btn-primary btn_guardar_asiento">Guardar</button>
    </p>
  </div>
</div>