<table id="ple" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Año</th>
      <th>Nro Cuenta</th>
      <th>Nombre</th>
      <th>Sumas Inicial Deudor</th>
      <th>Sumas Inicial Acreedor</th>

      <th>Sumas Debe</th>
      <th>Sumas Haber</th>

      <th>Saldo Final Deudor</th>
      <th>Saldo Final Acreedor</th>

      <th>Balance Activo</th>
      <th>Balance Pasivo+Pat.</th>

      <th>Est. Naturaleza Perdidas</th>
      <th>Est. Naturaleza Ganancias</th>

      <th>Est. Función Perdidas</th>
      <th>Est. Función Ganancias</th>

    </tr>
  </thead>
  <tbody>
      @php

        $sdt       =   0;
        $sht       =   0;

        $sfdt      =   0;
        $sfht      =   0;

        $bdt       =   0;
        $bht       =   0;

        $endt      =   0;
        $enht      =   0;

        $efdt      =   0;
        $efht      =   0;

        $rbdt      =   0;
        $rbht      =   0;

        $rendt      =   0;
        $renht      =   0;

        $refdt      =   0;
        $refht      =   0;
      @endphp


    @foreach($listacuentacontable as $item)

      @php

        $sfd       =   0;
        $sfa       =   0;
        $sum_debe  =   $sumat->where('TXT_CUENTA_CONTABLE','=',$item->nro_cuenta)->sum('CAN_DEBE_MN');
        $sum_haber =   $sumat->where('TXT_CUENTA_CONTABLE','=',$item->nro_cuenta)->sum('CAN_HABER_MN');

        $sdt       =   $sdt + $sum_debe;
        $sht       =   $sht + $sum_haber;

      @endphp

      @if($sum_debe > $sum_haber) 
        @php 
          $sfd       =   ($sum_debe - $sum_haber);
          $sfdt      =   $sfdt + $sfd;
        @endphp
      @endif

      @if($sum_haber > $sum_debe) 
        @php 
          $sfa       =   ($sum_haber - $sum_debe);
          $sfht      =   $sfht + $sfa;
        @endphp
      @endif

      <tr>
        <td>{{$item->anio}}</td>
        <td>{{$item->nro_cuenta}}</td>
        <td>{{$item->nombre}}</td>

        <td>{{number_format(0, $redondeo, '.', ',')}}</td>
        <td>{{number_format(0, $redondeo, '.', ',')}}</td>

        <td>{{number_format($sum_debe, $redondeo, '.', ',')}}</td>
        <td>{{number_format($sum_haber, $redondeo, '.', ',')}}</td>

        <td>{{number_format($sfd, $redondeo, '.', ',')}}</td>
        <td>{{number_format($sfa, $redondeo, '.', ',')}}</td>


        <td>
          @if($item->clase_categoria_id == 'COC0000000000001') 
            {{number_format($sfd, $redondeo, '.', ',')}}

            @php
              $bdt       =   $bdt + $sfd;
            @endphp

          @else
            {{number_format(0, $redondeo, '.', ',')}}
          @endif
        </td>
        <td>
          @if($item->clase_categoria_id == 'COC0000000000001') 
            {{number_format($sfa, $redondeo, '.', ',')}}

            @php
              $bht       =   $bht + $sfa;
            @endphp

          @else
            {{number_format(0, $redondeo, '.', ',')}}  
          @endif
        </td>


        <td>
          @if($item->clase_categoria_id == 'COC0000000000002') 
            {{number_format($sfd, $redondeo, '.', ',')}}
            @php
              $endt       =   $endt + $sfd;
            @endphp
          @else
            {{number_format(0, $redondeo, '.', ',')}}  
          @endif
        </td>
        <td>
          @if($item->clase_categoria_id == 'COC0000000000002') 
            {{number_format($sfa, $redondeo, '.', ',')}}
            @php
              $enht       =   $enht + $sfa;
            @endphp

          @else
            {{number_format(0, $redondeo, '.', ',')}}  
          @endif
        </td>


        <td>
          @if($item->clase_categoria_id == 'COC0000000000004') 
            {{number_format($sfd, $redondeo, '.', ',')}}
            @php
              $efdt       =   $efdt + $sfd;
            @endphp

          @else
            {{number_format(0, $redondeo, '.', ',')}}  
          @endif
        </td>
        <td>
          @if($item->clase_categoria_id == 'COC0000000000004') 
            {{number_format($sfa, $redondeo, '.', ',')}}
            @php
              $efht       =   $efht + $sfa;
            @endphp

          @else
            {{number_format(0, $redondeo, '.', ',')}}  
          @endif
        </td>

      </tr>                    
    @endforeach
  </tbody>

  <tfoot>
    <tr>
      <th colspan="3">Totales</th>
      <th>{{number_format(0, $redondeo, '.', ',')}}</th>
      <th>{{number_format(0, $redondeo, '.', ',')}}</th>

      <th>{{number_format($sdt, $redondeo, '.', ',')}}</th>
      <th>{{number_format($sht, $redondeo, '.', ',')}}</th>

      <th>{{number_format($sfdt, $redondeo, '.', ',')}}</th>
      <th>{{number_format($sfht, $redondeo, '.', ',')}}</th>

      <th>{{number_format($bdt, $redondeo, '.', ',')}}</th>
      <th>{{number_format($bht, $redondeo, '.', ',')}}</th>

      <th>{{number_format($endt, $redondeo, '.', ',')}}</th>
      <th>{{number_format($enht, $redondeo, '.', ',')}}</th>

      <th>{{number_format($efdt, $redondeo, '.', ',')}}</th>
      <th>{{number_format($efht, $redondeo, '.', ',')}}</th>
    </tr>

    <tr>
      <th colspan="3">RESULTADO DEL EJERCICIO O PERIODO</th>

      <th>{{number_format(0, $redondeo, '.', ',')}}</th>
      <th>{{number_format(0, $redondeo, '.', ',')}}</th>


      <th>{{number_format(0, $redondeo, '.', ',')}}</th>
      <th>{{number_format(0, $redondeo, '.', ',')}}</th>

      <th>{{number_format(0, $redondeo, '.', ',')}}</th>
      <th>{{number_format(0, $redondeo, '.', ',')}}</th>


      <th>
        @if($bdt > $bht)
          {{number_format(0, $redondeo, '.', ',')}}
            @php
              $rbdt       =   $bdt;
            @endphp

        @else
          {{number_format($bht-$bdt, $redondeo, '.', ',')}}
            @php
              $rbdt       =   ($bht-$bdt) + $bdt;
            @endphp
        @endif
      </th>
      <th>        
        @if($bdt > $bht)
          {{number_format($bdt-$bht, $redondeo, '.', ',')}}
            @php
              $rbht       =   ($bdt-$bht) + $bht;
            @endphp
        @else
          {{number_format(0, $redondeo, '.', ',')}}
            @php
              $rbht       =   $bht;
            @endphp

        @endif
      </th>


      <th>
        @if($endt > $enht)
          {{number_format(0, $redondeo, '.', ',')}}
            @php
              $rendt       =   $endt;
            @endphp

        @else
          {{number_format($enht-$endt, $redondeo, '.', ',')}}
            @php
              $rendt       =   ($enht-$endt) + $endt;
            @endphp

        @endif
      </th>
      <th>
        @if($endt > $enht)
          {{number_format($endt-$enht, $redondeo, '.', ',')}}
            @php
              $renht       =   ($endt-$enht) + $enht;
            @endphp

        @else
          {{number_format(0, $redondeo, '.', ',')}}
            @php
              $renht       =    $enht;
            @endphp

        @endif
      </th>


      <th>
        @if($efdt > $efht)
          {{number_format(0, $redondeo, '.', ',')}}
            @php
              $refdt       =   $efdt;
            @endphp

        @else
          {{number_format($efht-$efdt, $redondeo, '.', ',')}}
            @php
              $refdt       =   ($efht-$efdt) + $efdt;
            @endphp

        @endif
      </th>
      <th>
        @if($efdt > $efht)
          {{number_format($efdt-$efht, $redondeo, '.', ',')}}
            @php
              $refht       =   ($efdt-$efht) + $efht;
            @endphp
        @else
          {{number_format(0, $redondeo, '.', ',')}}
            @php
              $refht       =  $efht;
            @endphp

        @endif
      </th>
    </tr>

    <tr>
      <th colspan="3">Totales</th>
      <th>{{number_format(0, $redondeo, '.', ',')}}</th>
      <th>{{number_format(0, $redondeo, '.', ',')}}</th>

      <th>{{number_format(0, $redondeo, '.', ',')}}</th>
      <th>{{number_format(0, $redondeo, '.', ',')}}</th>

      <th>{{number_format(0, $redondeo, '.', ',')}}</th>
      <th>{{number_format(0, $redondeo, '.', ',')}}</th>



      <th>{{number_format($rbdt, $redondeo, '.', ',')}}</th>
      <th>{{number_format($rbht, $redondeo, '.', ',')}}</th>

      <th>{{number_format($rendt, $redondeo, '.', ',')}}</th>
      <th>{{number_format($renht, $redondeo, '.', ',')}}</th>

      <th>{{number_format($refdt, $redondeo, '.', ',')}}</th>
      <th>{{number_format($refht, $redondeo, '.', ',')}}</th>
    </tr>

  </tfoot>




</table>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif