<table id="ple" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Periodo</th>
      <th>Código Único de operación</th>
      <th>Correlativo del Mes</th>
      <th>Codigo del establecimiento anexo</th>
      <th>Código del catálogo utilizado</th>

      <th>Tipo de existencia</th>
      <th>Código propio de la existencia</th>
      <th>Código del catálogo utilizado</th>
      <th>Código de la existencia de acuerdo al CUBSO</th>
      <th>Fec. Emision</th>

      <th>Tipo</th>
      <th>Serie</th>
      <th>Numero</th>
      <th>Tipo Operación efectuada</th>
      <th>Descripción</th>

      <th>Und. Medida</th>
      <th>Metodo de valuación</th>
      <th>ENTRADA Cantidad</th>
      <th>ENTRADA C. Unitario</th>
      <th>ENTRADA Total</th>

      <th>SALIDAS Cantidad</th>
      <th>SALIDAS C. Unitario</th>
      <th>SALIDAS Total</th>
      <th>SALDO FINAL Cantidad</th>
      <th>SALDO FINAL C. Unitario</th>

      <th>SALDO FINAL Total</th>
      <th>EStado</th>
      <th>Libre</th>

    </tr>
  </thead>
  <tbody>
    @foreach($lista_asiento as $index => $item)
      <tr>
        <td>{{$item['periodo_01']}}</td>
        <td>{{$item['cu']}}</td>
        <td>{{$item['codigo_03']}}</td>
        <td>{{$item['codigo_estable_04']}}</td>
        <td>{{$item['codigo_catalogo_05']}}</td>

        <td>{{$item['tipo_existencia_06']}}</td>
        <td>{{$item['codigo_existencia_07']}}</td>
        <td>{{$item['codigo_catalogo_08']}}</td>
        <td>{{$item['codigo_cubso_09']}}</td>
        <td>{{$item['comp_fecha_10']}}</td>

        <td>{{$item['comp_tipo_11']}}</td>
        <td>{{$item['comp_serie_12']}}</td>
        <td>{{$item['comp_numero_13']}}</td>
        <td>{{$item['tipo_op_efectu_14']}}</td>
        <td>{{$item['exis_descrip_15']}}</td>

        <td>{{$item['unidad_medida_16']}}</td>
        <td>{{$item['metodo_valuacion_17']}}</td>
        <td>{{$item['entrada_cantidad']}}</td>
        <td>{{$item['entrada_cu']}}</td>
        <td>{{$item['entrada_importe']}}</td>

        <td>{{$item['salida_cantidad']}}</td>
        <td>{{$item['salida_cu']}}</td>
        <td>{{$item['salida_importe']}}</td>
        <td>{{$item['saldo_cantidad']}}</td>
        <td>{{$item['saldo_cu']}}</td>

        <td>{{$item['saldo_importe']}}</td>
        <td>{{$item['estado']}}</td>
        <td>{{$item['libre']}}</td>

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