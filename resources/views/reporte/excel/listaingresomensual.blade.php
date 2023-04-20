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

.bggris{
  background: #f5f5f5!important;
}

.bgblanco{
  background: #ffffff!important;
}


  </style>

<table>

  <thead>
    <tr>
      <th>Cuenta</th>
      <th>Glosa</th>
      @foreach($lista_periodo as $index=>$item)
        <th>{{$item->TXT_NOMBRE}}</th>
      @endforeach
      <th>TOTALES</th>
    </tr>
  </thead>
  <tbody>
    @foreach($ingresosmensuales as $index=>$item)
    <tr class="{{$item['bg']}}">
        @for ($i = 0; $i < $item['cantidadarray']; $i++)
            <td class="{{$item['negrita']}}">{{$item['item'.$i]}}</td>
        @endfor
        <td class="{{$item['negrita']}}">{{$item['totales']}}</td>
    </tr>
    @endforeach
  </tbody>

</table>




</html>
