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
    .primary{
      color : #ffffff;
      background : #5f99f5;
    }

    .success{
      color : #ffffff;
      background : #34a853;
    }

  </style>

<table>
  <tbody>


  <tbody>

    @php $totales     =   0; @endphp
    @foreach($listacuentacontable as $index=>$item)
        @php $monto       =   $funcion->sumar_total_resultado_naturaleza($item->COD_CATEGORIA,$periodo_array,$array_cuenta,$anio); @endphp
        @php $totales     =   $totales + $monto*$item->IND_GEN_ASIENTO; @endphp
        <tr>
          <td width="50">
            @if($item->IND_GEN_ASIENTO <1)
            (-)
            @else
            (+)
            @endif
            {{$item->NOM_CATEGORIA}}</td>
          <td width="20">{{number_format($monto, $redondeo, '.', '')}}</td>
        </tr>
        @if($item->CODIGO_SUNAT == 1) 
          <tr>
            <td class='center negrita'>{{$item->TXT_REFERENCIA}}</td>
            <td class='negrita'>{{number_format($totales, $redondeo, '.', '')}}</td>
          </tr>
        @endif
    @endforeach
  </tbody>

</table>




</html>
