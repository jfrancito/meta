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
      @php

        $cabecera   =   '';
        $detalle    =   '';
        $nivel      =   '';
        $subcuenta  =   '';
        $cuentatop  =   '';
        $nombretotal  =   '';
        $nombretituototal  =   '';

      @endphp


    @foreach($listacuentacontable as $index=>$item)

      @php

        $sfd       =   0;
        $sfa       =   0;
        $sum_debe  =   $sumat->where('TXT_CUENTA_CONTABLE','=',$item->nro_cuenta)->sum('CAN_DEBE_MN');
        $sum_haber =   $sumat->where('TXT_CUENTA_CONTABLE','=',$item->nro_cuenta)->sum('CAN_HABER_MN');

      @endphp

      @if($sum_debe > $sum_haber) 
        @php 
          $sfd       =   ($sum_debe - $sum_haber);
        @endphp
      @endif

      @if($sum_haber > $sum_debe) 
        @php 
          $sfa       =   ($sum_haber - $sum_debe);
        @endphp
      @endif

      @if($index == 0)
        @php $nro_cuenta       =   substr($item->nro_cuenta, 0, 2); @endphp
      @endif


      @if($nro_cuenta != substr($item->nro_cuenta, 0, 2))
        @php $nro_cuenta       =   substr($item->nro_cuenta, 0, 2); @endphp
      @endif


      <!-- TOTAL ACTIVO CORRIENTE ETC -->
      @if($index == 0)
        @php $subcuenta       =   $item->tipo_cuenta_balance_id; @endphp
      @endif

      @if($subcuenta != $item->tipo_cuenta_balance_id)
          @php
            $sum_scd   =   $sumat->where('tipo_cuenta_balance_id','=',$subcuenta)->sum('CAN_DEBE_MN');
            $sum_sch   =   $sumat->where('tipo_cuenta_balance_id','=',$subcuenta)->sum('CAN_HABER_MN');
          @endphp

          <tr>
            <td colspan="4" class='center negrita'>TOTAL : {{$nombretotal}}</td>
            <td class='negrita'>{{$sum_scd + $sum_sch}}</td>
          </tr> 
        @php $subcuenta       =   $item->tipo_cuenta_balance_id; @endphp
        @php $nombretotal     =   $item->tipo_cuenta_balance; @endphp
      @else
        @php $nombretotal     =   $item->tipo_cuenta_balance; @endphp
      @endif


      <!-- TOTAL ACTIVO, PASIVO ETC -->
      @if($index == 0)
        @php $cuentatop       =   $item->tipo_cuenta_id; @endphp
      @endif

      @if($cuentatop != $item->tipo_cuenta_id)
          @php
            $sum_tcd   =   $sumat->where('tipo_cuenta_id','=',$cuentatop)->sum('CAN_DEBE_MN');
            $sum_tch   =   $sumat->where('tipo_cuenta_id','=',$cuentatop)->sum('CAN_HABER_MN');
          @endphp

          <tr>
            <td colspan="4" class='center negrita'>TOTAL : {{$nombretituototal}}</td>
            <td class='negrita'>{{$sum_tcd + $sum_tch}}</td>
          </tr> 
        @php $cuentatop       =   $item->tipo_cuenta_id; @endphp
        @php $nombretituototal     =   $item->tipo_cuenta;    @endphp
      @else
        @php $nombretituototal     =   $item->tipo_cuenta; @endphp
      @endif




      <!-- TITULO -->
      @if($index == 0)
        <tr>
          <td colspan="5" class='center primary negrita'>{{$item->tipo_cuenta}}</td>
        </tr> 
        @php $cabecera       =   $item->tipo_cuenta; @endphp
      @endif

      @if($cabecera != $item->tipo_cuenta)
        <tr>
          <td colspan="5" class='center primary negrita'>{{$item->tipo_cuenta}}</td>
        </tr> 
        @php $cabecera       =   $item->tipo_cuenta; @endphp
      @endif

      <!-- SUBTITULO -->
      @if($index == 0)
        <tr>
          <td colspan="5" class='success negrita'>{{$item->tipo_cuenta_balance}}</td>
        </tr> 
        @php $detalle       =   $item->tipo_cuenta_balance; @endphp
      @endif

      @if($detalle != $item->tipo_cuenta_balance)
        <tr>
          <td colspan="5" class='success negrita'>{{$item->tipo_cuenta_balance}}</td>
        </tr> 
        @php $detalle       =   $item->tipo_cuenta_balance; @endphp
      @endif



      @if($item->nivel == '2')

          @php
            $sum_tncd   =   $sumat->where('GRUPO','=',$nro_cuenta)->sum('CAN_DEBE_MN');
            $sum_tnch   =   $sumat->where('GRUPO','=',$nro_cuenta)->sum('CAN_HABER_MN');
          @endphp

        <tr>
          <td width="5">{{$item->anio}}</td>
          <td width="5">{{$item->nro_cuenta}}</td>
          <td width="50" colspan="2">{{$item->nombre}}</td>
          <td width="20">{{$sum_tncd + $sum_tnch}}</td>
        </tr>
      @endif

  
      <!-- EL ultimo valor -->
      @if($index+1 == (count($listacuentacontable)))


          @php
            $sum_tncd   =   $sumat->where('GRUPO','=',$nro_cuenta)->sum('CAN_DEBE_MN');
            $sum_tnch   =   $sumat->where('GRUPO','=',$nro_cuenta)->sum('CAN_HABER_MN');
          @endphp


          @php
            $sum_scd   =   $sumat->where('tipo_cuenta_balance_id','=',$subcuenta)->sum('CAN_DEBE_MN');
            $sum_sch   =   $sumat->where('tipo_cuenta_balance_id','=',$subcuenta)->sum('CAN_HABER_MN');
          @endphp

          <tr>
            <td colspan="4" class='center negrita'>TOTAL : {{$item->tipo_cuenta_balance}}</td>
            <td class='negrita'>{{$sum_scd + $sum_sch}}</td>
          </tr> 


          @php
            $sum_tcd   =   $sumat->where('tipo_cuenta_id','=',$cuentatop)->sum('CAN_DEBE_MN');
            $sum_tch   =   $sumat->where('tipo_cuenta_id','=',$cuentatop)->sum('CAN_HABER_MN');
          @endphp
          <tr>
            <td colspan="4" class='center negrita'>TOTAL : {{$item->tipo_cuenta}}</td>
            <td class='negrita'>{{$sum_tcd + $sum_tch}}</td>
          </tr> 
      @endif
    @endforeach
  </tbody>


</table>




</html>
