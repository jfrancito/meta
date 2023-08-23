<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style type="text/css">
    h1{
      text-align: center;
    }
    .subtitulos{
      font-weight: bold;
      font-style: italic;
    }
    .titulotabla{
      background: #4285f4;
      color: #fff;
      font-weight: bold;
    }
    .tabladp{
        background: #bababa;
        color:#fff;
    }
    .tablaho{
      background: #37b358;
        color:#fff;
    }
    .tablamar{
        background: #4285f4;
        color:#fff;
    }
    .tablaagrupado{
        background: #ea4335;
        color:#fff;
    }
    .negrita{
        font-weight: bold;
    }
    .center{
      text-align: center;
    }
    .reportevacadesc{
            background: #ea4335;
        color: #fff;
        font-weight: bold;
    }
    .tablafila2{
      background: #f5f5f5;
    }
    .tablafila1{
      background: #ffffff;
    }
    .warning{
      background-color: #f6c163 !important;
    }
    .vcent{ display: table-cell; vertical-align:middle;text-align: center;}
    .gris{
        background: #C8C9CA;
    }
    .blanco{
      background: #ffffff;
    }
  </style>
    <table>
        <tr>
          <th class= 'center tablaho'>Periodo</th>
          <th class= 'center tablaho'>Código Único de operación</th>
          <th class= 'center tablaho'>Correlativo del Mes</th>
          <th class= 'center tablaho'>Codigo del establecimiento anexo</th>
          <th class= 'center tablaho'>Código del catálogo utilizado</th>

          <th class= 'center tablaho'>Tipo de existencia</th>
          <th class= 'center tablaho'>Código propio de la existencia</th>
          <th class= 'center tablaho'>Código del catálogo utilizado</th>
          <th class= 'center tablaho'>Código de la existencia de acuerdo al CUBSO</th>
          <th class= 'center tablaho'>Fec. Emision</th>

          <th class= 'center tablaho'>Tipo</th>
          <th class= 'center tablaho'>Serie</th>
          <th class= 'center tablaho'>Numero</th>
          <th class= 'center tablaho'>Tipo Operación efectuada</th>
          <th class= 'center tablaho'>Descripción</th>

          <th class= 'center tablaho'>Und. Medida</th>
          <th class= 'center tablaho'>Metodo de valuación</th>
          <th class= 'center tablaho'>ENTRADA Cantidad</th>
          <th class= 'center tablaho'>ENTRADA C. Unitario</th>
          <th class= 'center tablaho'>ENTRADA Total</th>

          <th class= 'center tablaho'>SALIDAS Cantidad</th>
          <th class= 'center tablaho'>SALIDAS C. Unitario</th>
          <th class= 'center tablaho'>SALIDAS Total</th>
          <th class= 'center tablaho'>SALDO FINAL Cantidad</th>
          <th class= 'center tablaho'>SALDO FINAL C. Unitario</th>

          <th class= 'center tablaho'>SALDO FINAL Total</th>
          <th class= 'center tablaho'>EStado</th>
          <th class= 'center tablaho'>Libre</th>
        </tr>
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
    </table>
</html>
