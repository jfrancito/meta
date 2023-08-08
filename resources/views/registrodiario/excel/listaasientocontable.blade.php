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
  <thead>
    <tr>

      <th class= 'center tablaho'>Linea</th>
      <th class= 'center tablaho'>Cuenta</th>
      <th class= 'center tablaho'>Glosa</th>
      <th class= 'center tablaho'>Debe MN</th>
      <th class= 'center tablaho'>Haber MN</th>
      <th class= 'center tablaho'>Debe ME</th>
      <th class= 'center tablaho'>Haber ME</th>
      <th class= 'center tablaho'>Asiento</th>
      
      <th class= 'center tablaho'>Nomref</th>
      <th class= 'center tablaho'>RUC/DNI</th>

      <th class= 'center tablaho'>Fecha</th>
      <th class= 'center tablaho'>Tipo Documento</th>
      <th class= 'center tablaho'>Documento</th>
      <th class= 'center tablaho'>Moneda</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listaasiento as $index => $item)
      <tr>
        <td width="6">{{$item->NRO_LINEA}}</td>
        <td width="10">{{$item->TXT_CUENTA_CONTABLE}}</td>
        <td width="60">{{$item->TXT_GLOSA}}</td>
        <td width="12">{{number_format($item->CAN_DEBE_MN, $redondeo, '.', '')}}</td>
        <td width="12">{{number_format($item->CAN_HABER_MN, $redondeo, '.', '')}}</td>
        <td width="12">{{number_format($item->CAN_DEBE_ME, $redondeo, '.', '')}}</td>
        <td width="12">{{number_format($item->CAN_HABER_ME, $redondeo, '.', '')}}</td>
        <td width="12">{{$item->TXT_CATEGORIA_TIPO_ASIENTO}}</td>


        <td width="35">{{$item->TXT_EMPR_CLI}}</td>
        <td width="35">{{$item->NRO_DOCUMENTO}}</td>

        <td width="12">{{$item->FEC_ASIENTO}}</td>
        <td width="25">{{$item->TXT_CATEGORIA_TIPO_DOCUMENTO}}</td>

        <td width="18">
          @if($item->TXT_TIPO_REFERENCIA_CAB == 'REVERSION_IGV_COMPRA')
            {{$item->DOCUMENTO_REF}}
          @else
            {{$item->NRO_SERIE}}-{{$item->NRO_DOC}}
          @endif
        </td>

        <td width="12">{{$item->TXT_CATEGORIA_MONEDA}}</td>
      </tr>                    
    @endforeach
  </tbody>

  <tfoot>
    <tr>
      <th colspan="3">Totales</th>
      <th>{{number_format($listaasiento->sum("CAN_DEBE_MN"), $redondeo, '.', '')}}</th>
      <th>{{number_format($listaasiento->sum("CAN_HABER_MN"), $redondeo, '.', '')}}</th>
      <th>{{number_format($listaasiento->sum("CAN_DEBE_ME"), $redondeo, '.', '')}}</th>
      <th>{{number_format($listaasiento->sum("CAN_HABER_ME"), $redondeo, '.', '')}}</th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>

    </tr>
  </tfoot>


</table>


</html>
