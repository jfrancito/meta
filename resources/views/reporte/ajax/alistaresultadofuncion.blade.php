<table id="ple" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <tbody>

    @php $totales     =   0; @endphp
    @foreach($listacuentacontable as $index=>$item)

        @php $monto       =   $funcion->sumar_total_resultado_funcion($item->COD_CATEGORIA,$periodo_array,$array_cuenta,$anio); @endphp

        @php $totales     =   $totales + $monto*$item->IND_GEN_ASIENTO; @endphp

        <tr>
          <td>{{$item->NOM_CATEGORIA}}</td>
          <td>{{number_format($monto*$item->IND_GEN_ASIENTO, $redondeo, '.', ',')}}</td>
        </tr>

        @if($item->CODIGO_SUNAT == 1) 
          <tr>
            <td class='center negrita'>{{$item->TXT_REFERENCIA}}</td>
            <td class='negrita'>{{number_format($totales, $redondeo, '.', ',')}}</td>
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