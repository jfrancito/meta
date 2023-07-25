<div class="container">
    <div class="row">
        <ul class="nav nav-tabs">
            <li class="active"><a id="tab-general" data-toggle="tab" href="#general">Depreciar Activos</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="general">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">Mes
                                    <span class="tooltiptext">Mes</span>
                                </div>
                            </label>
                            {!! Form::select( 'mes', $combo_mes, array(),
                            [
                            'class' => 'form-control control select2' ,
                            'id' => 'mes',
                            'data-aw' => '1',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">C&aacute;lculo de Depreciaci&oacute;n
                                    <span class="tooltiptext">C&aacute;lculo de Depreciaci&oacute;n</span>
                                </div>
                            </label>
                              <div class="be-radio inline">
                                <input type="radio" 
                                class='documentorb' 
                                name="calculo" id="unico"  value='unico'>
                                <label for="unico">Del periodo elegido</label>

                                <input type="radio" 
                                class='documentorb'
                                name="calculo" id="ultimo"  value='ultimo' checked="checked">
                                <label for="ultimo">Desde la &uacute;ltima depreciaci&oacute;n</label>
                              </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">Condici&oacute;n del Proceso
                                    <span class="tooltiptext">Condici&oacute;n del Proceso</span>
                                </div>
                            </label>
                           
                            <div class="be-radio inline">
                                <input type="radio" 
                                class='documentorb' 
                                name="condicion" id="simulado"  value='simulado' checked="checked">
                                <label for="simulado">Simular depreciaci&oacute;n</label>

                                <input type="radio" 
                                class='documentorb'
                                name="condicion" id="procesado"  value='procesado'>
                                <label for="procesado">Procesar depreciaci&oacute;n</label>
                              </div>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2" style="display: none;">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">Fecha
                                    <span class="tooltiptext">Fecha</span>
                                </div>
                            </label>
                            
                            <div 	data-start-view="2"  
                            data-date-format="dd-mm-yyyy hh:ii" 
                            class="input-group date datetimepicker">
                                <input size="16" type="text" 
                                value="{{ date("d-m-Y") }}"
                                id='fecha' name='fecha' required = ""
                                placeholder="Fecha Depreciación" class="form-control input-sm">
                                <span class="input-group-addon btn btn-primary">
                                    <i class="icon-th mdi mdi-calendar"></i>
                                </span>
                            </div>
    
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">Depreciar
                                    <span class="tooltiptext">Elegir objeto de depreciaci&oacute;n</span>
                                </div>
                            </label>

                            {!! Form::select( 'activofijo', $combo_activo_fijo, array(),
                            [
                            'class' => 'form-control control select2' ,
                            'id' => 'activofijo',
                            'data-aw' => '1',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">
                                    <span class="tooltiptext"></span>
                                </div>
                            </label>
                            <div class="be-checkbox">
                                <input class='documentorb'  type="checkbox" name="todos" id="todos"><label for="todos">Todos los activos</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label">
                                <div class="tooltipfr">
                                    <span class="tooltiptext"></span>
                                </div>
                            </label>
                            <div class="be-checkbox">
                                <input class='documentorb'  type="checkbox" name="asientos" id="asientos"><label for="asientos">Generar Asientos</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12" style="display:flex; justify-content:end;">
                        <button type="submit" class="btn btn-primary btn-lg" style="width: 110px;">Procesar <i
                                class="icon mdi mdi-chevron-right"></i></button>
                    </div>
                </div>
                <div class="row justify-content-center panel panel-danger" style="padding-top: 40px;">
                        @if(isset($resultado_depreciacion) && $resultado_depreciacion!='')
                            <div class="col-sm-4 panel-body" style="margin: auto;">
                            <table class="table table-responsive" style="background-color: #EEEEEE;overflow: hidden;border-radius: 8px;border: solid 1px #333333;">
                                <tbody>
                                    <tr>
                                        <th><span class="title">Activo Fijo</span></th>
                                        <td><span class="description">{{$resultado_depreciacion["nombre"]}}</span></td>
                                    </tr>
                                    <tr>
                                        <th><span class="title">Cuenta Contable Activo</span></th>
                                        <td><span class="description">{{$resultado_depreciacion["cuenta_activo"]}}</span></td>
                                    </tr>
                                    <tr>
                                        <th><span class="title">Tasa de Depreciación</span></th>
                                        <td><span class="description">{{$resultado_depreciacion["tasa_depreciacion"]}} {{ isset($resultado_depreciacion["tasa_depreciacion"]) ? '%' : ''}}</span></td>
                                    </tr>
                                    <tr>
                                        <th><span class="title">Fecha de Inicio de Depreciación del Activo</span></th>
                                        <td><span class="description">{{(isset($resultado_depreciacion["fecha_inicio_depreciacion"]) && $resultado_depreciacion["fecha_inicio_depreciacion"] != '') ? date("d/m/Y", strtotime($resultado_depreciacion["fecha_inicio_depreciacion"])) : '--'}}</span></td>
                                    </tr>
                                    <tr>
                                        <th><span class="title">Periodo</span></th>
                                        <td><span class="description">{{$resultado_depreciacion["mes"]}}</span></td>
                                    </tr>
                                    <tr>
                                        <th><span class="title">Valor de Depreciación</span></th>
                                        <td><span class="description">S/. {{number_format((float)$resultado_depreciacion["monto_depreciar"],2)}}</span></td>
                                    </tr>
                                    <tr>
                                        <th><span class="title">Depreciación acumulada hasta la fecha</span></th>
                                        <td><span class="description">S/. {{(isset($resultado_depreciacion["depreciacion_acumulada_actualizada"]) && $resultado_depreciacion["depreciacion_acumulada_actualizada"] != '') ? number_format((float)$resultado_depreciacion["depreciacion_acumulada_actualizada"],2) : '--'}}</span></td>
                                    </tr>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        @endif
                        @if (isset($resultado_asientos))
                            <div class="col-sm-8 panel-body" style="margin: auto;">
                                @foreach ($resultado_asientos as $clave => $asiento)
                                    @if (isset($asiento[0]))
                                        <div class="container asientos-depreciacion">
                                            <table class="cabecera table table-responsive">
                                                    <thead>
                                                        <tr>
                                                            <th scope="row" class="col-md-2">Fecha</td>
                                                            <th class="col-md-4">Glosa</td>
                                                            <th class="col-md-2">Debe</td>
                                                            <th class="col-md-2">Haber</td>
                                                            <th class="col-md-2"></td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td scope="row">{{$asiento[0]['fecha']}}</td>
                                                            <td>{{$asiento[0]['glosa']}}</td>
                                                            <td>{{$asiento[0]['debe']}}</td>
                                                            <td>{{$asiento[0]['haber']}}</td>
                                                            <td><button type="button" class="btn btn-info" data-toggle="collapse" data-target="#detalle{{$clave}}">Ver detalle</button></td>
                                                        </tr>
                                                    </tbody>
                                            </table>
                                            <div id="detalle{{$clave}}" class="collapse">
                                                <table class="table table-striped table-responsive">      
                                                    <thead>
                                                        <tr>
                                                            <th scope="row" class="col-md-2">Cuenta</td>
                                                            <th class="col-md-4">Glosa</td>
                                                            <th class="col-md-2">Debe</td>
                                                            <th class="col-md-2">Haber</td>
                                                            <th class="col-md-2"></td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($asiento[0]['detalle'] as $detalle)
                                                            <tr>
                                                                <td scope="row">{{$detalle['cuenta']}}</td>
                                                                <td>{{$detalle['glosa']}}</td>
                                                                <td>{{$detalle['debe']}}</td>
                                                                <td>{{$detalle['haber']}}</td>
                                                                <td></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
