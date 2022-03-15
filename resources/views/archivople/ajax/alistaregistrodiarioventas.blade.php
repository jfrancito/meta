<table id="ple" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Periodo</th>
      <th>Codigo unico de la operación</th>
      <th>Correlativo</th>
      <th>Fecha de emisión</th>
      <th>Fecha de vencimiento</th>
      <th>Tipo de comprobante</th>
      <th>Nro serie</th>
      <th>Nro comprobante</th>
      <th>Registro de tickets</th>
      <th>Tipo de documento identidad</th>
      <th>Documento identidad</th>
      <th>Nombre del cliente</th>
      <th>Valor facturado de la exportación</th>
      <th>Base imponible de la operación gravada</th>
      <th>Descuento de la base gravada</th>
      <th>Impuesto general a la ventas</th>
      <th>Descuento del impuesto general a la ventas</th>
      <th>Importe total de la operación exonerada</th>
      <th>Importe total de la operación inafecta</th>
      <th>Impuesto selectivo al consumo</th>
      <th>Base imponible de la operación gravada con el impuesto</th>
      <th>Impuesto de la venta de arroz pilado</th>
      <th>Impuesto al consumo de bolsa plastico</th>  
      <th>Otros conceptos</th>
      <th>Importe total del comprobante de pago</th>
      <th>Codigo moneda</th>
      <th>Tipo de cambio</th>
      <th>Fecha emision del comprobante de pago modifica</th>
      <th>Tipo del comprobante de pago modifica</th>
      <th>Nro serie modifica</th>
      <th>Nro comprobante modifica</th>

      <th>Identificacion del contrato</th>
      <th>Inconsistencia tipo de cambio</th>
      <th>Indicador del comprobante de pago</th>
      <th>Estado que indica la oportunidad de annotacion</th>
      <th>Campo libre utilizacion</th>








    </tr>
  </thead>
  <tbody>
    @foreach($lista_asiento as $index => $item)
      <tr>
        <td>{{$item['periodo']}}</td>
        <td>{{$item['codigo_unico_operacion']}}</td>
        <td>{{$item['correlativo_asiento']}}</td>
        <td>{{$item['fecha_emision']}}</td>
        <td>{{$item['fecha_vencimiento']}}</td>
        <td>{{$item['tipo_comprobante']}}</td>
        <td>{{$item['nro_serie']}}</td>
        <td>{{$item['nro_documento']}}</td>
        <td>{{$item['registro_tickets']}}</td>
        <td>{{$item['tipo_documento_identidad']}}</td>
        <td>{{$item['documento_identidad']}}</td>
        <td>{{$item['nombre_cliente']}}</td>
        <td>{{$item['valor_facturado_exportacion']}}</td>
        <td>{{$item['base_imponible_gravada']}}</td>
        <td>{{$item['descuento_base_imponible']}}</td>
        <td>{{$item['impuesto_generl_ventas']}}</td>
        <td>{{$item['descuento_impuesto_generl_ventas']}}</td>
        <td>{{$item['importe_total_operacion_exonerada']}}</td>
        <td>{{$item['importe_total_operacion_inafecta']}}</td>
        <td>{{$item['impuesto_selectivo_consumo']}}</td>
        <td>{{$item['base_imponible_operacion_gravada_impuesto_operacion_gravada']}}</td>
        <td>{{$item['impuesto_venta_arroz_pilado']}}</td>
        <td>{{$item['impuesto_consumo_bolsa_plastico']}}</td>
        <td>{{$item['otros_conceptos']}}</td>
        <td>{{$item['importe_total_comprobnte_pago']}}</td>
        <td>{{$item['codigo_moneda']}}</td>
        <td>{{$item['tipo_cambio']}}</td>
        <td>{{$item['fecha_emision_comprobante_pago_modifica']}}</td>
        <td>{{$item['tipo_comprobante_pago_modifica']}}</td>
        <td>{{$item['nro_serie_modifica']}}</td>
        <td>{{$item['nro_documento_modifica']}}</td>

        <td>{{$item['identificacion_contrato']}}</td>
        <td>{{$item['inconsistencia_tipo_cambio']}}</td>
        <td>{{$item['indicador_comprobante_pago']}}</td>
        <td>{{$item['estado_indica_oportunidad_anotacion']}}</td>
        <td>{{$item['campo_libre_utilizacion']}}</td>

      </tr>                    
    @endforeach
  </tbody>
</table>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif