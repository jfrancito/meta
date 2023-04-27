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
    .success{
      background-color: #37b358;
    }
    .warning{
      background-color: #f6c163;
    }
    .danger{
      background-color: #eb6357;
    }   

  </style>


<table>
  <thead>
    <tr>
      <th class= 'center tablaho'>Item</th>
      <th class= 'center tablaho'>Periodo</th>
      <th class= 'center tablaho'>Serie</th>
      <th class= 'center tablaho'>Nro. Doc.</th>
      <th class= 'center tablaho'>Fecha</th>
      <th class= 'center tablaho'>Glosa</th>
      <th class= 'center tablaho'>Moneda</th>
      <th class= 'center tablaho'>T.C.</th>
      <th class= 'center tablaho'>Total Debe</th>
      <th class= 'center tablaho'>Total Haber</th>
      <th class= 'center tablaho'>Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listaasiento as $index => $item)
        @if($item->COD_CATEGORIA_ESTADO_ASIENTO == 'IACHTE0000000025')
          @php $class = 'success'; @endphp 
        @else
          @if($item->COD_CATEGORIA_ESTADO_ASIENTO == 'IACHTE0000000032')
            @php $class = 'warning'; @endphp 
          @else
            @php $class = 'danger'; @endphp
          @endif
        @endif
      <tr>
        <td width="6">{{$index + 1}}</td>
        <td width="15">{{$item->periodo->TXT_NOMBRE}}</td>
        <td width="15">{{$item->NRO_SERIE}}</td>
        <td width="15">{{$item->NRO_DOC}}</td>
        <td width="15">{{date_format(date_create($item->FEC_ASIENTO), 'd-m-Y')}}</td>
        <td width="40">{{$item->TXT_GLOSA}}</td>
        <td width="15">{{$item->TXT_CATEGORIA_MONEDA}}</td>
        <td width="12">{{number_format($item->CAN_TIPO_CAMBIO, $redondeo, '.', ',')}}</td>
        <td width="12">{{number_format($item->CAN_TOTAL_DEBE, $redondeo, '.', ',')}}</td>
        <td width="12">{{number_format($item->CAN_TOTAL_HABER, $redondeo, '.', ',')}}</td>
        <td width="15" class='{{$class}}'>
            {{$item->TXT_CATEGORIA_ESTADO_ASIENTO}}
        </td>
      </tr>                    
    @endforeach
  </tbody>

</table>


</html>
