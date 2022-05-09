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
          <th class= 'center tablaho'>Año</th>
          <th class= 'center tablaho'>Mes</th>
          <th class= 'center tablaho'>Dia</th>
          <th class= 'center tablaho'>Compro</th>
          <th class= 'center tablaho'>Cuenta</th>
          <th class= 'center tablaho'>Coddoc</th>
          <th class= 'center tablaho'>NroDoc</th>
          <th class= 'center tablaho'>Coddoc_ref</th>
          <th class= 'center tablaho'>NroDoc_ref</th>
          <th class= 'center tablaho'>FVenci</th>
          <th class= 'center tablaho'>Proveedor</th>
          <th class= 'center tablaho'>Ruc</th>
          <th class= 'center tablaho'>Glosa</th>
          <th class= 'center tablaho'>Debe</th>
          <th class= 'center tablaho'>Haber</th>
          <th class= 'center tablaho'>Debe_d</th>
          <th class= 'center tablaho'>Haber_d</th>
          <th class= 'center tablaho'>Moneda</th>
          <th class= 'center tablaho'>Tipo cambio</th>
          <th class= 'center tablaho'>Linea</th>

          <th class= 'center tablaho'>Fecha detraccion</th>
          <th class= 'center tablaho'>Numero detraccion</th>
          <th class= 'center tablaho'>Importe detraccion</th>
          <th class= 'center tablaho'>Tasa detraccion</th>


        </tr>
        @foreach($listadetalleasiento as $index => $item)
           <tr>
              <td width="5">{{substr($item->asiento->FEC_ASIENTO,0,4)}}</td>
              <td width="5">{{substr($item->asiento->FEC_ASIENTO,5,2)}}</td>
              <td width="5">{{substr($item->asiento->FEC_ASIENTO,8,2)}}</td>
              <td width="12">{{$item->asiento->NRO_ASIENTO}}</td>
              <td width="12">{{$item->TXT_CUENTA_CONTABLE}}</td>
              <td width="8">{{$item->asiento->tipo_documento->CODIGO_SUNAT}}</td>
              <td width="14">{{$item->asiento->NRO_SERIE}} - {{$item->asiento->NRO_DOC}}</td>
              <td width="8">{{$item->asiento->tipo_documento_ref->CODIGO_SUNAT}}</td>
              <td width="15">{{$item->asiento->NRO_SERIE_REF}} - {{$item->asiento->NRO_DOC_REF}}</td>
              <td width="10">{{date_format(date_create($item->asiento->FEC_VENCIMIENTO), 'd-m-Y')}}</td>
              <td width="50">{{$item->asiento->TXT_EMPR_CLI}}</td>
              <td width="12">{{$item->asiento->empresa->NRO_DOCUMENTO}}</td>
              <td width="60">{{$item->TXT_GLOSA}}</td>

              <td width="10">{{number_format($item->CAN_DEBE_MN, 2, '.', '')}}</td>
              <td width="10">{{number_format($item->CAN_HABER_MN, 2, '.', '')}}</td>
              <td width="10">{{number_format($item->CAN_DEBE_ME, 2, '.', '')}}</td>
              <td width="10">{{number_format($item->CAN_HABER_ME, 2, '.', '')}}</td>
              <td width="10">{{$item->asiento->TXT_CATEGORIA_MONEDA}}</td>
              <td width="10">{{number_format($item->asiento->CAN_TIPO_CAMBIO, 2, '.', '')}}</td>
              <td width="5">{{$item->NRO_LINEA}}</td>
              <td width="10">{{date_format(date_create($item->asiento->FEC_DETRACCION), 'd-m-Y')}}</td>
              <td width="10">{{$item->asiento->NRO_DETRACCION}}</td>
              <td width="10">{{number_format($item->asiento->CAN_TOTAL_DETRACCION, 2, '.', '')}}</td>
              <td width="10">{{number_format($item->asiento->CAN_DESCUENTO_DETRACCION, 2, '.', '')}}</td>

            </tr>     
        @endforeach     
    </table>
</html>
