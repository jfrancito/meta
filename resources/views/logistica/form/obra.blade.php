<div class="container">
    <div class="row">
      <ul class="nav nav-tabs">
        <li class="active"><a id="tab-general" data-toggle="tab" href="#general">Informaci&oacute;n General</a></li>
        <li><a id="tab-depreciacion" data-toggle="tab" href="#depreciacion">Informaci&oacute;n de Depreciaci&oacute;n</a></li>
        @if (isset($activos_obra) and count($activos_obra)>0)
            <li><a id="tab-activosobra" data-toggle="tab" href="#activosobra">Activos</a></li>
        @endif        
        @if (isset($activofijo))
          <li class="{{ ($activofijo->estado == 'BAJA') ? 'info-baja' : 'info-depreciacion' }}"><a>{{ ($activofijo->estado != 'BAJA') ? $activofijo->estado_depreciacion : 'BAJA' }}</a></li>          
        @endif
      </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="general">
                <div class="row" style="margin-top: 20px; margin-bottom: 32px;">
                    <div class="col-sm-6">
                        <div class="form-group" style="padding-top:0px;">
                            <label class="control-label" style="padding-top: 0px;">
                                <div class="tooltipfr">Nombre <span class='requerido'>*</span>
                                    <span class="tooltiptext">Nombre</span>
                                </div>
                            </label>
                            <input type="text" id="nomproducto" name='nomproducto' value="{{ isset($activofijo) ? old('nomproducto', $activofijo->nombre) : old('nomproducto') }}" placeholder="Nombre del Activo Fijo" required="" maxlength="120" autocomplete="off" class="form-control input-sm" data-aw="1" />
                            @include('error.erroresvalidate', [ 'id' => $errors->has('nomproducto') ,
                            'error' => $errors->first('nomproducto', ':message') ,
                            'data' => '1'])
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="card border-primary mb-3">
                            <div class="card-icon"><i class="icon mdi mdi-present-to-all info-card"></i></div>
                            <div class="card-body">
                                <h5 class="card-title">Origen</h5>
                                <p class="card-text">{{ isset($activofijo) ? $activofijo->origen : "MANUAL" }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="card border-primary mb-3">
                            <div class="card-icon"><i class="icon mdi mdi-calendar-alt info-card"></i></div>
                            <div class="card-body">
                                <h5 class="card-title">Creado</h5>
                                <p class="card-text">{{ isset($activofijo) ? date("d/m/Y H:i", strtotime($activofijo->fecha_registro)) : date("d-m-Y") }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="card border-primary mb-3">
                            <div class="card-icon"><i class="icon mdi mdi-calendar-alt info-card"></i></div>
                            <div class="card-body">
                                <h5 class="card-title">Modificado</h5>
                                <p class="card-text">{{ isset($activofijo) ? date("d/m/Y H:i", strtotime($activofijo->fecha_modificacion)) :  date("d-m-Y") }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">Item PLE <span class='requerido'>*</span>
                                    <span class="tooltiptext">Item PLE</span>
                                </div>
                            </label>
                            <input type="text" id="itemple" name='itemple' value="{{ isset($activofijo) ? old('itemple', $activofijo->item_ple) : old('itemple') }}" placeholder="Item PLE" @if(isset($producto) && $cantidad_productos > 1) disabled @else required="" @endif maxlength="10" autocomplete="off" class="form-control input-sm" data-aw="1" />
                            @include('error.erroresvalidate', [ 'id' => $errors->has('itemple') ,
                            'error' => $errors->first('itemple', ':message') ,
                            'data' => '1'])
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">Categor&iacute;a
                                    <span class="tooltiptext">Categor&iacute;a.</span>
                                </div>
                            </label>
                            {!! Form::select( 'categoria', $combo_categoria_activo_fijo, array(),
                            [
                            'class' => 'form-control control select2' ,
                            'id' => 'categoria',
                            'data-aw' => '1',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-sm-2" style="display: none;">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">Tipo de Activo
                                    <span class="tooltiptext">Tipo de Activo</span>
                                </div>
                            </label>
                            {!! Form::select( 'tipoactivo', $combo_tipo_activo_fijo, array(),
                            [
                            'class' => 'form-control control select2' ,
                            'id' => 'tipoactivo',
                            'data-aw' => '1',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-sm-4" style="display: none;">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">Activo Principal
                                    <span class="tooltiptext">Activo Principal</span>
                                </div>
                            </label>
  
                            {!! Form::select( 'activoprincipal', $combo_obra, array(),
                            [
                            'class' => 'form-control control select2' ,
                            'id' => 'activoprincipal',
                            'data-aw' => '1',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">Estado
                                    <span class="tooltiptext">Estado del Activo</span>
                                </div>
                            </label>
                            {!! Form::select( 'estado', $combo_estado_activo_fijo, array(),
                            [
                            'class' => 'form-control control select2' ,
                            'id' => 'estado',
                            'data-aw' => '1',
                            ]) !!}
                        </div>
                    </div>
                    @if (isset($activofijo) && $activofijo->estado == 'BAJA')
                    <div class="col-sm-2">
                        <div class="form-group">
                          <label class="control-label">
                              <div class="tooltipfr">
                                  Fecha de Baja
                              </div>
                          </label>
                        <input type="text" id="fechabaja" name='fechabaja' value="{{ date("d/m/Y H:i", strtotime($activofijo->fecha_baja)) }}" autocomplete="off" class="form-control input-sm" data-aw="1" disabled/>
                      </div>
                    </div> 
                    @endif                
                    <div class="col-sm-2" style="display: none;">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">Estado de Conservaci&oacute;n
                                    <span class="tooltiptext">Estado de Conservaci&oacute;n</span>
                                </div>
                            </label>
                            {!! Form::select( 'estadoconservacion', $combo_estado_conservacion_activo_fijo, array(),
                            [
                            'class' => 'form-control control select2' ,
                            'id' => 'estadoconservacion',
                            'data-aw' => '1',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">Observaci&oacute;n
                                    <span class="tooltiptext">Observaci&oacute;n</span>
                                </div>
                            </label>
                            <input type="textarea" id="observacion" name='observacion' value="{{ isset($activofijo->observacion) ?  old('observacion', $activofijo->observacion) : old('observacion') }}" placeholder="Observacion" maxlength="200" autocomplete="off" class="form-control input-sm" data-aw="1" />
                            @include('error.erroresvalidate', [ 'id' => $errors->has('observacion') ,
                            'error' => $errors->first('observacion', ':message') ,
                            'data' => '1'])
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                </div>
                <div class="row">
                    <div class="col-sm-12" style="display:flex; justify-content:end;">
                        <a id="adepreciacion" class="btn btn-primary btn-lg" style="width: 80px;"><i class="icon mdi mdi-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="depreciacion">
              <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label">
                            <div class="tooltipfr">Fecha de Inicio <span class='requerido'>*</span>
                                <span class="tooltiptext">Fecha de Inicio de Depreciaci&oacute;n</span>
                            </div>
                        </label>
                        {{-- <input type="text" id="fechainiciodepreciacion" name='fechainiciodepreciacion' value="{{ old('fechainiciodepreciacion') }}" placeholder="dd/mm/aaaa" required="" maxlength="10" autocomplete="off" class="form-control input-sm" data-aw="1" /> --}}
                        <div 	data-start-view="2"  
                          data-date-format="dd/mm/yyyy" 
                          class="input-group date datetimepicker">
                              <input size="16" type="text" 
                              value="@if(isset($activofijo)){{old('fechainiciodepreciacion',$activofijo->fecha_inicio_depreciacion)}}@elseif(isset($producto)){{old('fechainiciodepreciacion',date_format(date_create($producto->FEC_EMISION), 'd/m/Y'))}}@else{{old('fechainiciodepreciacion',date('d/m/Y'))}}@endif"
                              id='fechainiciodepreciacion' name='fechainiciodepreciacion' required = ""
                              placeholder="Fecha Inicio" class="form-control input-sm" {{ (isset($activofijo) && ($activofijo->estado_depreciacion != "SIN DEPRECIAR")) ? 'disabled' : '' }}>
                              <span class="input-group-addon btn btn-primary">
                                  <i class="icon-th mdi mdi-calendar"></i>
                              </span>
                          </div>
                        {{-- @include('error.erroresvalidate', [ 'id' => $errors->has('fechainiciodepreciacion') ,
                        'error' => $errors->first('fechainiciodepreciacion', ':message') ,
                        'data' => '1']) --}}
                    </div>
                </div>
                <div class="col-sm-2" id="base-calculo">
                  <div class="form-group">
                      <label class="control-label">
                          <div class="tooltipfr">Base de C&aacute;lculo <span class='requerido'>*</span>
                              <span class="tooltiptext">Base de C&aacute;lculo</span>
                          </div>
                      </label>
                      <input type="text" id="basedecalculo" name='basedecalculo' value="@if(isset($activofijo) || isset($producto)){{ isset($activofijo) ? old('basedecalculo', $activofijo->base_de_calculo) : old('basedecalculo', $producto->CAN_PRECIO_UNIT) }}@endif" placeholder="Saldo Inicial de Depreciación" required="" maxlength="14" autocomplete="off" class="form-control input-sm" data-aw="1" {{ (isset($activofijo) && ($activofijo->estado_depreciacion != "SIN DEPRECIAR")) ? 'disabled' : '' }}/>
                      @include('error.erroresvalidate', [ 'id' => $errors->has('basedecalculo') ,
                      'error' => $errors->first('basedecalculo', ':message') ,
                      'data' => '1'])
                  </div>
                </div>
                <div class="col-sm-2" id="base-calculo-compuesto" style="display: none;">
                  <div class="form-group">
                      <label class="control-label">
                          <div class="tooltipfr">Base de C&aacute;lculo Compuesto<span class='requerido'>*</span>
                              <span class="tooltiptext">Base de C&aacute;lculo Activo Fijo Compuesto</span>
                          </div>
                      </label>
                      <input type="text" id="basedecalculocompuesto" name='basedecalculocompuesto' value="@if(isset($activofijo) || isset($producto)){{ isset($activofijo) ? old('basedecalculo', $activofijo->base_de_calculo) : old('basedecalculo', $producto->CAN_VALOR_VTA) }}@endif" placeholder="Saldo Inicial de Depreciación" maxlength="14" autocomplete="off" class="form-control input-sm" data-aw="1" {{ (isset($activofijo) && ($activofijo->estado_depreciacion != "SIN DEPRECIAR")) ? 'disabled' : '' }}/>
                      @include('error.erroresvalidate', [ 'id' => $errors->has('basedecalculocompuesto') ,
                      'error' => $errors->first('basedecalculocompuesto', ':message') ,
                      'data' => '1'])
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                      <label class="control-label">
                          <div class="tooltipfr">{{ (isset($activofijo) && ($activofijo->estado_depreciacion != "SIN DEPRECIAR")) ? 'Depreciación Acumulada' : 'Saldo Inicial D. Acumulada'}}  <span class='requerido'>*</span>
                              <span class="tooltiptext">Saldo Inicial de Depreciaci&oacute;n Acumulada</span>
                          </div>
                      </label>
                      <input type="text" id="saldoiniciodepreciacionacumulada" name='saldoiniciodepreciacionacumulada' value="{{ isset($activofijo) ? old('saldoiniciodepreciacionacumulada', $activofijo->depreciacion_acumulada) : old('saldoiniciodepreciacionacumulada', 0) }}" placeholder="Saldo Inicial Dep. Acumulada" required="" maxlength="14" autocomplete="off" class="form-control input-sm" data-aw="1" {{ (isset($activofijo) && ($activofijo->estado_depreciacion != "SIN DEPRECIAR")) ? 'disabled' : '' }}/>
                      @include('error.erroresvalidate', [ 'id' => $errors->has('saldoiniciodepreciacionacumulada') ,
                      'error' => $errors->first('saldoiniciodepreciacionacumulada', ':message') ,
                      'data' => '1'])
                  </div>
                </div>
              </div> 
              <div class="row">
                  <div class="col-sm-12" style="display:flex; justify-content:end;">
                      <button type="submit" class="btn btn-primary btn-lg" style="width: 110px;">{{ isset($activofijo) ? 'Modificar': 'Transferir'}} <i class="icon mdi mdi-chevron-right"></i></button>
                  </div>
              </div>           
            </div>

            @if ((isset($activos_obra) and count($activos_obra)>0) or (isset($bajas_obra) and count($bajas_obra)>0))
            <div class="tab-pane fade" id="activosobra">

                @if ((isset($activos_obra) and count($activos_obra)>0))                
                <div class="row">
                  <div class="col-sm-8">
                      <div class="form-group">
                          <label class="control-label">
                              <div class="tooltipfr">Activos en Obra
                              </div>
                          </label>
                          <table class="table activos-en-obra">
                            <thead>
                                <tr>
                                    <th>C&oacute;digo PLE</th>
                                    <th>Activo</th>
                                    <th style="text-align: right;">Monto</th>                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activos_obra as $activo)
                                <tr>
                                    <td scope="row">{{$activo->item_ple}}</td>
                                    <td scope="row">{{$activo->nombre}}</td>
                                    <td style="text-align: right;">S/. {{$activo->base_de_calculo}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <td colspan="2"><b>Total Activos:</b></td>
                                <td  style="text-align: right;" class='columna-success-titulo'>S/. {{$total_activos_obra}}</td>
                            </tfoot>
                          </table>
                      </div>
                  </div>                  
                </div>
                @endif
                
                @if (isset($bajas_obra) and count($bajas_obra)>0)
                <div class="row">
                  <div class="col-sm-8">
                      <div class="form-group">
                          <label class="control-label">
                              <div class="tooltipfr">Bajas en Obra
                              </div>
                          </label>
                          <table class="table activos-en-obra">
                            <thead>
                                <tr>
                                    <th>C&oacute;digo PLE</th>
                                    <th>Activo</th>
                                    <th style="text-align: right;">Monto</th>                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bajas_obra as $baja)
                                <tr>
                                    <td scope="row">{{$baja->item_ple}}</td>
                                    <td scope="row">{{$baja->nombre}}</td>
                                    <td style="text-align: right;">S/. {{$baja->base_de_calculo}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <td colspan="2"><b>Total Bajas:</b></td>
                                <td  style="text-align: right;" class='columna-warning-table '>S/. {{$total_bajas_obra}}</td>
                            </tfoot>
                          </table>
                      </div>
                  </div>                  
                </div>
                @endif

            </div>
            @endif
        </div>
    </div>
  </div>
  