<table id="ple" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>

    @if($pantalla)
      <tr>
        <th rowspan="2">Tipo de venta</th>  
        <th rowspan="2">Periodo</th>
        <th rowspan="2">Codigo unico de la operación</th>
        <th rowspan="2">Correlativo</th>
        <th rowspan="2">Fecha de emisión</th>
        <th rowspan="2">Fecha de vencimiento</th>
        <th colspan="3" style="text-align: center;">COMPROBANTE DE PAGO</th>
        <th colspan="3" style="text-align: center;">INFORMACIÓN DEL CLIENTE</th>
        
        <th rowspan="2">Valor facturado de la exportación</th>
        <th rowspan="2">Base imponible de la operación gravada</th>
        <th rowspan="2">Descuento de la base gravada</th>
        <th rowspan="2">Impuesto general a la ventas</th>

        <th colspan="2" style="text-align: center;">IMPORTE TOTAL DE LA OPERACIÓN EXONERADA O INAFECTA</th>

        <th rowspan="2">Base imponible de la operacion gravada</th>
        <th rowspan="2">IVAP</th>
        <th rowspan="2">Importe total del comprobante de pago</th>
        <th rowspan="2">Tipo de cambio</th>

        <th colspan="4" style="text-align: center;">REFERENCIA DEL COMPROBANTE DE PAGO O DOCUMENTO ORIGINAL QUE SE MODIFICA</th>
      </tr>
      <tr>
        <th>Tipo</th>
        <th>Nro serie</th>
        <th>Nro comprobante</th>

        <th>Tipo de documento identidad</th>
        <th>Documento identidad</th>
        <th>Nombre del cliente</th>

        <th>Exonerada</th>
        <th>Inafecta</th>

        <th>Fecha</th>
        <th>Tipo</th>
        <th>Serie</th>
        <th>Nro comprobante</th>
        
      </tr>

    @else
      <tr>
      </tr>
      <tr>
      </tr>
    @endif

  </thead>
  <tbody>
    @foreach($lista_asiento as $index => $item)
      <tr>
        <td>{{$item['tipo_venta_00']}}</td>
        <td>{{$item['periodo_01']}}</td>
        <td>{{$item['correlativo_02']}}</td>
        <td>{{$item['codigo_03']}}</td>
        <td>{{$item['fecha_emision_04']}}</td>
        <td>{{$item['fecha_vencimiento_05']}}</td>
        <td>{{$item['tipo_documento_06']}}</td>
        <td>{{$item['nro_serie_07']}}</td>
        <td>{{$item['nro_correlativo_08']}}</td>

        <td>{{$item['identidad_cliente_10']}}</td>
        <td>{{$item['documento_cliente_11']}}</td>
        <td>{{$item['nombre_cliente_12']}}</td>

        <td>{{number_format($item['v_f_e_13'], 2, '.', '')}}</td>
        <td>{{number_format($item['suma_70_14'], 2, '.', '')}}</td>
        <td>{{number_format($item['codigo_15'], 2, '.', '')}}</td>
        <td>{{number_format($item['suma_40_16'], 2, '.', '')}}</td>

        <td>{{number_format($item['codigo_17'], 2, '.', '')}}</td>
        <td>{{number_format($item['codigo_18'], 2, '.', '')}}</td>

        <td>{{number_format($item['codigo_19'], 2, '.', '')}}</td>
        <td>{{number_format($item['codigo_20'], 2, '.', '')}}</td>
        <td>{{number_format($item['importe_total_21'], 2, '.', '')}}</td>
        <td>{{number_format($item['tipo_cambio_22'], 3, '.', '')}}</td>
        <td>{{$item['fecha_asociada_23']}}</td>
        <td>{{$item['tipo_asociado_24']}}</td>
        <td>{{$item['serie_asociada_25']}}</td>
        <td>{{$item['nro_asociada_26']}}</td>


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