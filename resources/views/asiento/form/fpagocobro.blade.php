<div class="col-sm-12">
  <div class="panel panel-default">
    <div class="tab-container">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#paso01" class='tabpaso1' data-toggle="tab">PASO 01</a></li>
        <li><a href="#paso02" class='tabpaso2' data-toggle="tab">PASO 02</a></li>
      </ul>
      <div class="tab-content">
        <div id="paso01" class="tab-pane active cont">


          <div class="col-sm-4">

            <div class="form-group quitar-tb">
                <label class="col-sm-12 control-label izquierda">Fecha Documento</label>
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
          <div class="col-sm-4">
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
          </div>

          <div class="col-sm-4">
            <div class='ajax_tipocambio'>
              @include('asiento.ajax.atipocambio')
            </div>
          </div>

          <div class="col-sm-4">          
            <div class="form-group quitar-tb">
              <label class="col-sm-12 control-label izquierda" >Cuenta referencia <b>(*)</b></label>
              <div class="col-sm-12" >
                {!! Form::select( 'cuenta_referencia', $combo_cuenta_referencia, '42',
                                  [
                                    'class'       => 'select2 form-control control input-xs' ,
                                    'id'          => 'cuenta_referencia',
                                    'required'    => '',
                                    'data-aw'     => '1',
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
          </div>
          <div class="col-sm-4">
            <div class="form-group quitar-tb">
              <label class="col-sm-12 control-label izquierda">Serie</label>
              <div class="col-sm-12">

                  <input  type="text"
                          id="serie" name='serie' 
                          value="{{$serie}}"
                          placeholder="Serie"
                          autocomplete="off" class="form-control input-sm" data-aw="7"/>

                  @include('error.erroresvalidate', [ 'id' => $errors->has('serie')  , 
                                                      'error' => $errors->first('serie', ':message') , 
                                                      'data' => '7'])

              </div>
            </div>
          </div>

          <div class="col-sm-4">
            <div class="form-group quitar-tb">
              <label class="col-sm-12 control-label izquierda">Nro. Comprobante </label>
              <div class="input-group col-sm-12">

                  <input  type="text"
                          id="nrocomprobante" name='nrocomprobante' 
                          value="{{$nrocomprobante}}"
                          placeholder="Nro. Comprobante"
                          autocomplete="off" class="form-control input-sm" data-aw="8"/>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-primary buscarasiento" style="height: 37px;">Buscar</button>
                  </span>
                  @include('error.erroresvalidate', [ 'id' => $errors->has('nrocomprobante')  , 
                                                      'error' => $errors->first('nrocomprobante', ':message') , 
                                                      'data' => '8'])

              </div>
            </div>

          </div>

          <input type="hidden" name="cod_asiento" id='cod_asiento' value='{{$asiento_id}}'>

          <div class="col-sm-12 listajax">
              @include('asiento.ajax.aasientopagocobro')
          </div>




        </div>
        <div id="paso02" class="tab-pane cont">



          <div class="col-sm-4">
              <div class="form-group">
                <label class="col-sm-12 control-label izquierda">Año :</label>
                <div class="col-sm-12 abajocaja" >
                  {!! Form::select( 'anio', $combo_anio_pc, $anio,
                                    [
                                      'class'       => 'select2 form-control control input-xs' ,
                                      'id'          => 'anio',
                                      'required'    => '',
                                      'data-aw'     => '1',
                                    ]) !!}
                </div>
              </div>
          </div>


          <div class="col-sm-4">
              <div class="form-group">
              <label class="col-sm-12 control-label izquierda">Cancelar con el libro </label>
                <div class="col-sm-12 abajocaja" >
                  {!! Form::select( 'tipo_asiento_id', $combo_tipo_asiento, $sel_tipo_asiento,
                                    [
                                      'class'       => 'select2 form-control control input-xs' ,
                                      'id'          => 'tipo_asiento_id',
                                      'required'    => '',
                                      'data-aw'     => '1',
                                    ]) !!}
                </div>
              </div>
          </div>



          <div class="col-sm-4 ajax_cuentapagocuenta">
              @include('asiento.ajax.acuentapagocobro')
          </div>



          <div class="col-sm-4">
            <div class="form-group">
              <label class="col-sm-12 control-label izquierda">Documento</label>
              <div class="col-sm-12 abajocaja">

                  <input  type="text"
                          id="documento" 
                          name='documento'
                          placeholder="Documento"
                          autocomplete="off" 
                          class="form-control input-sm" 
                          data-aw="7"/>

              </div>
            </div>
          </div>


          <div class="col-sm-8">
            <div class="form-group">
              <label class="col-sm-12 control-label izquierda">Glosa</label>
              <div class="col-sm-12 abajocaja">

                  <input  type="text"
                          id="glosa" 
                          name='glosa'
                          placeholder="Glosa"
                          autocomplete="off" 
                          class="form-control input-sm" 
                          data-aw="7"/>

              </div>
            </div>
          </div>



        </div>

      </div>
    </div>
  </div>
</div>




<div class="row xs-pt-15">
  <div class="col-xs-6">
      <div class="be-checkbox">

      </div>
  </div>
  <div class="col-xs-6">
    <p class="text-right">
      <button type="submit" class="btn btn-space btn-primary btn_guardar_asiento_cobro_pago ocultar">Guardar</button>
    </p>
  </div>
</div>