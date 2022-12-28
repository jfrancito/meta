<table id="ple" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Periodo</th>
      <th>Codigo unico de la operación</th>
      <th>Correlativo</th>
      <th>Codigo de la cuenta</th>
      <th>Codigo de la unidad</th>
      <th>Codigo del centro</th>
      <th>Codigo moneda</th>
      <th>Tipo de documento identidad</th>
      <th>Numero de documento identidad</th>
      <th>Tipo de comprobante</th>
      <th>Nro serie</th>
      <th>Nro comprobante</th>
      <th>Fecha contable</th>
      <th>Fecha de vencimiento</th>
      <th>Fecha de emisión</th>
      <th>Glosa</th>
      <th>Glosa de referencia</th>
      <th>Debe</th>
      <th>Haber</th>
      <th>Dato estructurado</th>
      <th>Estado</th>
      <th>Campo libre</th>
    </tr>
  </thead>
  <tbody>
    @foreach($lista_asiento as $index => $item)
      <tr>
        <td>{{$item['periodo_01']}}</td>
        <td>{{$item['correlativo_02']}}</td>
        <td>{{$item['codigo_03']}}</td>
        <td>{{$item['cuenta_04']}}</td>
        <td>{{$item['codigo_unidad_05']}}</td>
        <td>{{$item['centro_costo_06']}}</td>
        <td>{{$item['moneda_07']}}</td>
        <td>{{$item['identidad_cliente_08']}}</td>
        <td>{{$item['documento_cliente_09']}}</td>
        <td>{{$item['tipo_documento_10']}}</td>
        <td>{{$item['nro_serie_11']}}</td>
        <td>{{$item['nro_correlativo_12']}}</td>
        <td>{{$item['fecha_emision_13']}}</td>
        <td>{{$item['fecha_vencimiento_14']}}</td>
        <td>{{$item['fecha_emision_15']}}</td>
        <td>{{$item['glosa_16']}}</td>
        <td>{{$item['referencia_glosa_17']}}</td>
        <td>{{$item['debe_18']}}</td>
        <td>{{$item['haber_19']}}</td>
        <td>{{$item['datoestructurado_20']}}</td>
        <td>{{$item['campo_21']}}</td>
        <td>{{$item['campo_22']}}</td>

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