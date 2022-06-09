
<div class="main-content container-fluid">
  <div class="row">


    <!--Default Tabs-->
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="tab-container">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#documentos" data-toggle="tab">Documentos</a></li>
            <li><a href="#asientocontable" data-toggle="tab">Asiento contable</a></li>

          </ul>
          <div class="tab-content">
            <div id="documentos" class="tab-pane active cont">

                <div class="panel panel-border">
                  <div class="panel-heading" style='font-size: 12px;'>

                      <b>NRO VOUCHER</b> : @if(isset($movimiento)){{$movimiento->NRO_VOUCHER}}@endif<br>
                      <b>BANCO</b> : @if(isset($movimiento)){{$movimiento->cajabanco->TXT_BANCO}}@endif<br>
                      <b>NRO CUENTA BANCARIA</b> : @if(isset($movimiento)){{$movimiento->NRO_CUENTA_BANCARIA}}@endif<br>
                      <b>FECHA OPERACION</b> : @if(isset($movimiento)){{date_format(date_create($movimiento->FEC_OPERACION), 'd-m-Y')}}@endif<br>
                      <b>FECHA MOVIMIENTO</b> : @if(isset($movimiento)){{date_format(date_create($movimiento->FEC_MOVIMIENTO_CAJABANCO), 'd-m-Y')}}@endif<br>
                      <b>MONEDA</b> : @if(isset($movimiento)){{$movimiento->TXT_CATEGORIA_MONEDA}}@endif<br>
                      <b>CANTIDAD DE DOCUMENTOS</b> : {{count($listadetallemovimientos)}}<br>
                  </div>

                  <div class="panel-body">
                    <table class="table table-condensed table-striped" style="font-size: 0.9em;">
                      <thead>
                        <tr>
                          <th>EMPRESA</th>
                          <th>DOCUMENTO</th>
                          <th>MONEDA</th>
                          <th>DOCUMENTO</th>
                          <th>PAGO O COBRO</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php 
                          $total_doc_mn     =   0;
                          $total_doc_me     =   0;
                          $total_pac_mn     =   0;
                          $total_pac_me     =   0;
                        @endphp
                        @foreach($listadetallemovimientos as $index => $item)
                          <tr>

                            @php 
                              $tipo_cambio_doc  =   $funcion->gn_tipo_cambio(date_format(date_create($item->FEC_EMISION), 'd-m-Y'));
                              $tipo_cambio_cp   =   $funcion->gn_tipo_cambio(date_format(date_create($item->FEC_OPERACION), 'd-m-Y'));
                            @endphp
                            <td class="user-avatar cell-detail user-info" >
                              <span style="padding-left: 0px;">
                                <b>{{str_limit($item->TXT_EMPR_AFECTA, 40, "...")}}</b>
                              </span>
                              <span class="cell-detail-description" style="padding-left: 0px;color: #5f99f5;">
                                <b>{{$item->TXT_FLUJO_CAJA}}</b>
                              </span>
                              <span class="cell-detail-description" style="padding-left: 0px;color: #34a853;">
                                <b>{{$item->TXT_CATEGORIA_TIPO_DOC}}</b>
                              </span>
                              <span class="cell-detail-description" style="padding-left: 0px;">
                                {{$item->COD_DOCUMENTO_CTBLE}}
                              </span>
                            </td>
                            <td>{{$item->NRO_SERIE}} - {{$item->NRO_DOC}}</td>
                            <td>{{$item->TXT_CATEGORIA_MONEDA}}</td>
                            <td class="user-avatar cell-detail user-info" >
                              @if($item->TXT_CATEGORIA_MONEDA == 'DOLARES')
                                <span style="padding-left: 0px;"><b>FECHA EMISION :</b> {{date_format(date_create($item->FEC_EMISION), 'd-m-Y')}}</span>
                                <span style="padding-left: 0px;"><b>TIPO CAMBIO SBS :</b>{{number_format($tipo_cambio_doc->CAN_VENTA_SBS, $redondeo, '.', ',')}}</span>
                                <span style="padding-left: 0px;"><b>MONTO SOLES :</b> {{number_format($item->CAN_TOTAL*$tipo_cambio_doc->CAN_VENTA_SBS, $redondeo, '.', ',')}}</span>
                                <span style="padding-left: 0px;"><b>MONTO DOLARES :</b> {{number_format($item->CAN_TOTAL, $redondeo, '.', ',')}}</span>

                                @php 
                                  $total_doc_mn     =   $total_doc_mn + ($item->CAN_TOTAL*$tipo_cambio_doc->CAN_VENTA_SBS);
                                  $total_doc_me     =   $total_doc_me + $item->CAN_TOTAL;
                                @endphp

                              @else

                                <span style="padding-left: 0px;"><b>FECHA EMISION :</b> {{date_format(date_create($item->FEC_EMISION), 'd-m-Y')}}</span>
                                <span style="padding-left: 0px;"><b>TIPO CAMBIO SBS :</b> {{number_format($tipo_cambio_doc->CAN_VENTA_SBS, $redondeo, '.', ',')}}</span>
                                <span style="padding-left: 0px;"><b>MONTO SOLES :</b> {{number_format($item->CAN_TOTAL, $redondeo, '.', ',')}}</span>
                                <span style="padding-left: 0px;"><b>MONTO DOLARES :</b> {{number_format(($item->CAN_TOTAL/$tipo_cambio_doc->CAN_VENTA_SBS), $redondeo, '.', ',')}}</span>

                                @php 
                                  $total_doc_mn     =   $total_doc_mn + $item->CAN_TOTAL;
                                  $total_doc_me     =   $total_doc_me + ($item->CAN_TOTAL/$tipo_cambio_doc->CAN_VENTA_SBS);
                                @endphp

                              @endif
                            </td>
                            @if($cuenta_referencia == '42')
                              <td class="user-avatar cell-detail user-info" >
                                  <span style="padding-left: 0px;"><b>FECHA EMISION :</b> {{date_format(date_create($item->FEC_OPERACION), 'd-m-Y')}}</span>
                                  <span style="padding-left: 0px;"><b>TIPO CAMBIO SBS :</b> {{number_format($tipo_cambio_cp->CAN_VENTA_SBS, $redondeo, '.', ',')}}</span>
                                @if($item->TXT_CATEGORIA_MONEDA == 'DOLARES')
                                  <span style="padding-left: 0px;"><b>MONTO SOLES :</b> {{number_format($item->CAN_DEBE_ME*$tipo_cambio_cp->CAN_VENTA_SBS, $redondeo, '.', ',')}}</span>
                                  <span style="padding-left: 0px;"><b>MONTO DOLARES :</b> {{number_format($item->CAN_DEBE_ME, $redondeo, '.', ',')}}</span>
                                  @php 
                                    $total_pac_mn     =   $total_pac_mn + ($item->CAN_DEBE_ME*$tipo_cambio_cp->CAN_VENTA_SBS);
                                    $total_pac_me     =   $total_pac_me + ($item->CAN_DEBE_ME);
                                  @endphp
                                @else
                                  <span style="padding-left: 0px;"><b>MONTO SOLES :</b> 
                                    {{number_format($item->CAN_DEBE_MN, $redondeo, '.', ',')}}
                                  </span>
                                  <span style="padding-left: 0px;"><b>MONTO DOLARES :</b> 
                                    {{number_format(($item->CAN_DEBE_MN/$tipo_cambio_cp->CAN_VENTA_SBS), $redondeo, '.', ',')}}
                                  </span>
                                  @php 
                                    $total_pac_mn     =   $total_pac_mn + ($item->CAN_DEBE_MN);
                                    $total_pac_me     =   $total_pac_me + ($item->CAN_DEBE_MN/$tipo_cambio_cp->CAN_VENTA_SBS);
                                  @endphp
                                @endif
                              </td>
                            @else
                              <td class="user-avatar cell-detail user-info" >
                                  <span style="padding-left: 0px;"><b>FECHA EMISION :</b> {{date_format(date_create($item->FEC_OPERACION), 'd-m-Y')}}</span>
                                  <span style="padding-left: 0px;"><b>TIPO CAMBIO SBS :</b> {{number_format($tipo_cambio_cp->CAN_VENTA_SBS, $redondeo, '.', ',')}}</span>
                                @if($item->TXT_CATEGORIA_MONEDA == 'DOLARES')
                                  <span style="padding-left: 0px;"><b>MONTO SOLES :</b> {{number_format($item->CAN_HABER_ME*$tipo_cambio_cp->CAN_VENTA_SBS, $redondeo, '.', ',')}}</span>
                                  <span style="padding-left: 0px;"><b>MONTO DOLARES :</b> {{number_format($item->CAN_HABER_ME, $redondeo, '.', ',')}}</span>

                                  @php 
                                    $total_pac_mn     =   $total_pac_mn + ($item->CAN_HABER_ME*$tipo_cambio_cp->CAN_VENTA_SBS);
                                    $total_pac_me     =   $total_pac_me + ($item->CAN_HABER_ME);
                                  @endphp

                                @else

                                  <span style="padding-left: 0px;"><b>MONTO SOLES :</b> {{number_format($item->CAN_HABER_MN, $redondeo, '.', ',')}}</span>
                                  <span style="padding-left: 0px;"><b>MONTO DOLARES :</b> {{number_format($item->CAN_HABER_MN/$tipo_cambio_cp->CAN_VENTA_SBS, $redondeo, '.', ',')}}</span>

                                  @php 
                                    $total_pac_mn     =   $total_pac_mn + ($item->CAN_HABER_MN);
                                    $total_pac_me     =   $total_pac_me + ($item->CAN_HABER_MN/$tipo_cambio_cp->CAN_VENTA_SBS);
                                  @endphp

                                @endif
                              </td>
                            @endif
                          </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                          <th colspan="3">Totales</th>

                          <th class="user-avatar cell-detail user-info" >
                              <span style="padding-left: 0px;"><b>MONTO SOLES :</b> 
                                {{number_format($total_doc_mn, $redondeo, '.', ',')}}
                              </span><br>
                              <span style="padding-left: 0px;"><b>MONTO DOLARES :</b> 
                                {{number_format($total_doc_me, $redondeo, '.', ',')}}
                              </span>
                          </th>
                          <th class="user-avatar cell-detail user-info">

                              <span style="padding-left: 0px;"><b>MONTO SOLES :</b> 
                                {{number_format($total_pac_mn, $redondeo, '.', ',')}}
                              </span><br>
                              <span style="padding-left: 0px;"><b>MONTO DOLARES :</b> 
                                {{number_format($total_pac_me, $redondeo, '.', ',')}}
                              </span>

                            </th>
                        </tr>
                      </tfoot>

                    </table>
                  </div>
                </div>

            </div>
            <div id="asientocontable" class="tab-pane cont">

                <div class="panel panel-border">

                  <div class="panel-heading" style='font-size: 12px;'>
                      <b>ASIENTO MODELO</b> : @if(count($asientomodelo)>0){{$asientomodelo->nombre}}@endif<br>
                  </div>

                  <div class="panel-body">
                      @if($buscar_modelo_asiento['encontro'] == '0') 
                          <b>{{$buscar_modelo_asiento['msg']}}</b>
                      @else
                          <table class="table table-condensed table-striped" style="font-size: 0.9em;">
                          <thead>
                            <tr>
                              <th>Periodo</th>
                              <th>Fecha</th>
                              <th>Glosa</th>
                              <th>Moneda</th>
                              <th>T.C.</th>
                              <th>Total Debe</th>
                              <th>Total Haber</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>{{$asiento_array['TXT_PERIODO']}}</td>
                              <td>{{date_format(date_create($asiento_array['FEC_ASIENTO']), 'd-m-Y')}}</td>
                              <td>{{$asiento_array['TXT_GLOSA']}}</td>
                              <td>{{$asiento_array['TXT_CATEGORIA_MONEDA']}}</td>
                              <td>{{number_format($asiento_array['CAN_TIPO_CAMBIO'], $redondeo, '.', ',')}}</td>
                              <td>{{number_format($asiento_array['CAN_TOTAL_DEBE'], $redondeo, '.', ',')}}</td>
                              <td>{{number_format($asiento_array['CAN_TOTAL_HABER'], $redondeo, '.', ',')}}</td>


                            </tr>                    
                          </tbody>
                        </table>


                          <table class="table table-condensed table-striped" style="font-size: 0.9em;">
                          <thead>
                            <tr>
                              <th>Linea</th>
                              <th>Cuenta</th>
                              <th>Glosa</th>
                              <th>Debe MN</th>
                              <th>Haber MN</th>
                              <th>Debe ME</th>
                              <th>Haber ME</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($detalle_asiento_array as $index => $item)
                                <tr>
                                  <td>{{$item['NRO_LINEA']}}</td>
                                  <td>{{$item['TXT_CUENTA_CONTABLE']}}</td>
                                  <td>{{$item['TXT_GLOSA']}}</td>
                                  <td>{{number_format($item['CAN_DEBE_MN'], $redondeo, '.', ',')}}</td>
                                  <td>{{number_format($item['CAN_HABER_MN'], $redondeo, '.', ',')}}</td>
                                  <td>{{number_format($item['CAN_DEBE_ME'], $redondeo, '.', ',')}}</td>
                                  <td>{{number_format($item['CAN_HABER_ME'], $redondeo, '.', ',')}}</td>
                                </tr>                  
                            @endforeach                  
                          </tbody>
                          <tfoot>
                            <tr>
                              <th colspan="3">Totales</th>
                              <th>{{number_format(array_sum(array_column($detalle_asiento_array,'CAN_DEBE_MN')), $redondeo, '.', ',')}}</th>
                              <th>{{number_format(array_sum(array_column($detalle_asiento_array,'CAN_HABER_MN')), $redondeo, '.', ',')}}</th>
                              <th>{{number_format(array_sum(array_column($detalle_asiento_array,'CAN_DEBE_ME')), $redondeo, '.', ',')}}</th>
                              <th>{{number_format(array_sum(array_column($detalle_asiento_array,'CAN_HABER_ME')), $redondeo, '.', ',')}}</th>
                            </tr>
                          </tfoot>
                        </table>


                      @endif
                  </div>
                </div>

            </div>

          </div>
        </div>
      </div>
    </div>







  </div>
</div>


