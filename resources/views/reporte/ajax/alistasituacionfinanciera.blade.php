<table id="ple" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
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
          @php
            $sum_tncd   =   $sumat->where('GRUPO','=',$nro_cuenta)->sum('CAN_DEBE_MN');
            $sum_tnch   =   $sumat->where('GRUPO','=',$nro_cuenta)->sum('CAN_HABER_MN');
          @endphp

          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"><b>{{$sum_tncd + $sum_tnch}}</b></td>
          </tr> 
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
            <td colspan="4" class='center'><b>TOTAL  : {{$nombretotal}}</b></td>
            <td><b>{{$sum_scd + $sum_sch}}</b></td>
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
            <td colspan="4" class='center'><b>TOTAL : {{$nombretituototal}}</b></td>
            <td><b>{{$sum_tcd + $sum_tch}}</b></td>
          </tr> 
        @php $cuentatop            =   $item->tipo_cuenta_id; @endphp
        @php $nombretituototal     =   $item->tipo_cuenta;    @endphp
      @else
        @php $nombretituototal     =   $item->tipo_cuenta; @endphp
      @endif

      <!-- TITULO -->
      @if($index == 0)
        <tr>
          <td colspan="5" class='center primary'><b>{{$item->tipo_cuenta}}</b></td>
        </tr> 
        @php $cabecera       =   $item->tipo_cuenta; @endphp
      @endif

      @if($cabecera != $item->tipo_cuenta)
        <tr>
          <td colspan="5" class='center primary'><b>{{$item->tipo_cuenta}}</b></td>
        </tr> 
        @php $cabecera       =   $item->tipo_cuenta; @endphp
      @endif

      <!-- SUBTITULO -->
      @if($index == 0)
        <tr>
          <td colspan="5" class='success'><b>{{$item->tipo_cuenta_balance}}</b></td>
        </tr> 
        @php $detalle       =   $item->tipo_cuenta_balance; @endphp
      @endif

      @if($detalle != $item->tipo_cuenta_balance)
        <tr>
          <td colspan="5" class='success'><b>{{$item->tipo_cuenta_balance}}</b></td>
        </tr> 
        @php $detalle       =   $item->tipo_cuenta_balance; @endphp
      @endif



      @if($item->nivel == '2')
        <tr>
          <td>{{$item->anio}}</td>
          <td>{{$item->nro_cuenta}}</td>
          <td colspan="2">{{$item->nombre}}</td>
          <td></td>
        </tr>
      @else
        <tr>
          <td>{{$item->anio}}</td>
          <td></td>
          <td>{{$item->nro_cuenta}}</td>
          <td>{{$item->nombre}}</td>
          <td class='right'>{{$sfd + $sfa}}</td>
        </tr>
      @endif

  
      <!-- EL ultimo valor -->
      @if($index+1 == (count($listacuentacontable)))


          @php
            $sum_tncd   =   $sumat->where('GRUPO','=',$nro_cuenta)->sum('CAN_DEBE_MN');
            $sum_tnch   =   $sumat->where('GRUPO','=',$nro_cuenta)->sum('CAN_HABER_MN');
          @endphp

          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="5"><b>{{$sum_tncd + $sum_tnch}}</b></td>
          </tr> 

          @php
            $sum_scd   =   $sumat->where('tipo_cuenta_balance_id','=',$subcuenta)->sum('CAN_DEBE_MN');
            $sum_sch   =   $sumat->where('tipo_cuenta_balance_id','=',$subcuenta)->sum('CAN_HABER_MN');
          @endphp

          <tr>
            <td colspan="4" class='center'><b>TOTAL : {{$item->tipo_cuenta_balance}}</b></td>
            <td><b>{{$sum_scd + $sum_sch}}</b></td>
          </tr> 


          @php
            $sum_tcd   =   $sumat->where('tipo_cuenta_id','=',$cuentatop)->sum('CAN_DEBE_MN');
            $sum_tch   =   $sumat->where('tipo_cuenta_id','=',$cuentatop)->sum('CAN_HABER_MN');
          @endphp

          <tr>
            <td colspan="4" class='center'><b>TOTAL : {{$item->tipo_cuenta}}</b></td>
            <td><b>{{$sum_tcd + $sum_tch}}</b></td>
          </tr> 



      @endif

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