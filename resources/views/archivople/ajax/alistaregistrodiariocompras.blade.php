<table id="ple" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>

    <tr>
      <th rowspan="2">PERIODO</th>
      <th rowspan="2">CUO</th>
      <th rowspan="2">NUMERO CORRELATIVO DEL ASIENTO CONTABLE</th>
      <th rowspan="2">FECHA DE EMISION DEL COMPROBANTE DE PAGO O DOCUMENTO</th>
      <th rowspan="2">FECHA DE VENCIMIENTO O FECHA DE PAGO</th>
      <th colspan="5">COMPROBANTE DE PAGO O DOCUMENTO</th>


      <th colspan="3">INFORMACION PROVEEDOR</th>
      <th colspan="2">ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES GRAVADAS Y/O EXPORTACION</th>
      <th colspan="2">GRAVADAS DESTINADAS A  GRAVADAS Y/O EXPORTACION Y NO GRAVADAS</th>
      <th colspan="2">ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES NO GRAVADAS</th>
      <th>VALOR DE LAS ADQUISICIONES NO GRAVADAS</th>


      <th rowspan="2">ISC</th>
      <th rowspan="2">ICBP</th>
      <th rowspan="2">DE LA BASE IMPONIBLE</th>
      <th rowspan="2">IMPORTE TOTAL</th>
      <th rowspan="2">MONEDA</th>
      <th rowspan="2">TIPO DE CAMBIO</th>
      <th colspan="5">REFERENCIA DEL COMPROBANTE DE PAGO O DOCUMENTO ORIGINAL QUE SE MODIFICA</th>


      <th colspan="2">CONSTANCIA DEPOSITO</th>
      <th rowspan="2">VALOR 34</th>
      <th rowspan="2">VALOR 35</th>
      <th rowspan="2">VALOR 36</th>
      <th rowspan="2">VALOR 37</th>
      <th rowspan="2">VALOR 38</th>
      <th rowspan="2">VALOR 39</th>
      <th rowspan="2">VALOR 40</th>

      <th rowspan="2">VALOR 41</th>
      <th rowspan="2">ESTADO QUE IDENTIFICA LA OPORTUNIDAD DE LA ANOTACIÓN O INDICACIÓN SI CORRESPONDE A UN AJUSTE</th>
      <th rowspan="2">LIBRE UTILIZACION</th>

    </tr>

    <tr>

      <th>TIPO</th>
      <th>SERIE/CODIGO ADUANA</th>
      <th>VALOR 08</th>
      <th>NUMERO</th>
      <th>VALOR 10</th>

      <th>TIPO</th>
      <th>NUMERO</th>
      <th>APELLIDOS Y NOMBRES, DENOMINACION O RAZON SOCIAL</th>
      <th>BASE IMPONIBLE</th>
      <th>IGV</th>
      <th>BASE IMPONIBLE</th>
      <th>IGV</th>
      <th>BASE IMPONIBLE</th>
      <th>IGV</th>
      <th>NACIONALES</th>


      <th>FECHA</th>
      <th>TIPO</th>
      <th>SERIE</th>
      <th>CODIGO DUA</th>

      <th>N° DEL COMPROBANTE DE PAGO O DOCUMENTO</th>
      <th>FECHA</th>
      <th>NUMERO</th>


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
        <td>{{$item['anio_emision_dua']}}</td>
        <td>{{$item['nro_documento']}}</td>
        <td>{{$item['campo_10']}}</td>


        <td>{{$item['tipo_documento_identidad']}}</td>
        <td>{{$item['documento_identidad']}}</td>
        <td>{{$item['nombre_cliente']}}</td>
        <td>{{$item['valor_14']}}</td>
        <td>{{$item['valor_15']}}</td>
        <td>{{$item['valor_16']}}</td>
        <td>{{$item['valor_17']}}</td>
        <td>{{$item['valor_18']}}</td>
        <td>{{$item['valor_19']}}</td>
        <td>{{$item['valor_20']}}</td>

        <td>{{$item['valor_21']}}</td>
        <td>{{$item['valor_22']}}</td>
        <td>{{$item['valor_23']}}</td>
        <td>{{$item['valor_24']}}</td>
        <td>{{$item['valor_25']}}</td>
        <td>{{$item['valor_26']}}</td>
        <td>{{$item['valor_27']}}</td>
        <td>{{$item['valor_28']}}</td>
        <td>{{$item['valor_29']}}</td>
        <td>{{$item['valor_30']}}</td>

        <td>{{$item['valor_31']}}</td>
        <td>{{$item['valor_32']}}</td>
        <td>{{$item['valor_33']}}</td>
        <td>{{$item['valor_34']}}</td>
        <td>{{$item['valor_35']}}</td>
        <td>{{$item['valor_36']}}</td>
        <td>{{$item['valor_37']}}</td>
        <td>{{$item['valor_38']}}</td>
        <td>{{$item['valor_39']}}</td>
        <td>{{$item['valor_40']}}</td>

        <td>{{$item['valor_41']}}</td>
        <td>{{$item['valor_42']}}</td>
        <td>{{$item['valor_43']}}</td>


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