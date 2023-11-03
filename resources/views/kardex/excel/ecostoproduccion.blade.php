<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {!! Html::style('public/css/excel/excelmeta.css') !!}
</head>

<h3>Costo Produccion</h3>
<table class="table table-condensed table-striped" >
    <thead>
      <tr>
        <th class="titulo center">Materia Prima</th>
        @foreach($listaperido as $indexp => $itemp)
          <th class="titulo center">{{$itemp->TXT_NOMBRE}}</th>
        @endforeach
        <th class="titulo center">TOTAL</th>

      </tr>
    </thead>
    <tbody>
    @foreach($listasaldoinicial as $index => $item)
        <tr>
            <td>{{$item->NOM_PRODUCTO}}</td>
            @foreach($listaperido as $indexp => $itemp)
              @php $monto    =   $coleccionarrozcascara->where('periodo_id','=',$itemp->COD_PERIODO)
                                 ->where('producto_id','=',$item->producto_id)->where('tipo','=','MP')->sum('salida_importe'); @endphp

              <td class="negrita izquierda">{{number_format($monto, 2, '.',',')}}</td>
            @endforeach
            @php $montop    =   $coleccionarrozcascara->where('producto_id','=',$item->producto_id)->where('tipo','=','MP')->sum('salida_importe'); @endphp
            <td class="titulo negrita izquierda">{{number_format($montop, 2, '.',',')}}</td>
        </tr>               
    @endforeach
        <tr>
            <td class="titulo negrita izquierda" >TOTALES</td>
            @foreach($listaperido as $indexp => $itemp)
              @php $montom    =   $coleccionarrozcascara->where('periodo_id','=',$itemp->COD_PERIODO)->where('tipo','=','MP')->sum('salida_importe'); @endphp
              <td class="titulo negrita izquierda" >{{number_format($montom, 2, '.',',')}}</td>
            @endforeach
            @php $montott    =   $coleccionarrozcascara->where('tipo','=','MP')->sum('salida_importe'); @endphp
            <td class="titulo negrita izquierda" >{{number_format($montott, 2, '.',',')}}</td>
        </tr> 
    </tbody>
</table>



<table class="table table-condensed table-striped" >
    <thead>
      <tr>
        <th class="titulo center">Costos Vinculados</th>
        @foreach($listaperido as $indexp => $itemp)
          <th class="titulo center">{{$itemp->TXT_NOMBRE}}</th>
        @endforeach
        <th class="titulo center">TOTAL</th>

      </tr>
    </thead>
    <tbody>
    @foreach($listacostovinculado as $index => $item)
        <tr>
            <td>{{$item->TXT_ABREVIATURA}}</td>
            @foreach($listaperido as $indexp => $itemp)
              @php $monto    =   $coleccionarrozcascara->where('periodo_id','=',$itemp->COD_PERIODO)
                                 ->where('producto_id','=',$item->NOM_CATEGORIA)->where('tipo','=','CV')->sum('salida_importe'); @endphp

              <td class="negrita izquierda">{{number_format($monto, 2, '.',',')}}</td>
            @endforeach
            @php $montop    =   $coleccionarrozcascara->where('producto_id','=',$item->NOM_CATEGORIA)->where('tipo','=','CV')->sum('salida_importe'); @endphp
            <td class="titulo negrita izquierda">{{number_format($montop, 2, '.',',')}}</td>
        </tr>               
    @endforeach
        <tr>
            <td class="titulo negrita izquierda">TOTALES</td>
            @foreach($listaperido as $indexp => $itemp)
              @php $montom    =   $coleccionarrozcascara->where('periodo_id','=',$itemp->COD_PERIODO)->where('tipo','=','CV')->sum('salida_importe'); @endphp
              <td class="titulo negrita izquierda">{{number_format($montom, 2, '.',',')}}</td>
            @endforeach
            @php $montott    =   $coleccionarrozcascara->where('tipo','=','CV')->sum('salida_importe'); @endphp
            <td class="titulo negrita izquierda">{{number_format($montott, 2, '.',',')}}</td>
        </tr> 
    </tbody>
</table>


<table class="table table-condensed table-striped" >
    <thead>
      <tr>
        <th class="titulo center">Producción Encargada de Terceros</th>
        @foreach($listaperido as $indexp => $itemp)
          <th class="titulo center">{{$itemp->TXT_NOMBRE}}</th>
        @endforeach
        <th class="titulo center">TOTAL</th>

      </tr>
    </thead>
    <tbody>
    @foreach($listaproduccionet as $index => $item)
        <tr>
            <td>{{$item->TXT_ABREVIATURA}}</td>
            @foreach($listaperido as $indexp => $itemp)
              @php $monto    =   $coleccionarrozcascara->where('periodo_id','=',$itemp->COD_PERIODO)
                                 ->where('producto_id','=',$item->NOM_CATEGORIA)->where('tipo','=','PE')->sum('salida_importe'); @endphp
              <td class="negrita izquierda">{{number_format($monto, 2, '.',',')}}</td>
            @endforeach
            @php $montop    =   $coleccionarrozcascara->where('producto_id','=',$item->NOM_CATEGORIA)->where('tipo','=','PE')->sum('salida_importe'); @endphp
            <td class="titulo negrita izquierda">{{number_format($montop, 2, '.',',')}}</td>
        </tr>               
    @endforeach
        <tr>
            <td class="titulo negrita izquierda">TOTALES</td>
            @foreach($listaperido as $indexp => $itemp)
              @php $montom    =   $coleccionarrozcascara->where('periodo_id','=',$itemp->COD_PERIODO)->where('tipo','=','PE')->sum('salida_importe'); @endphp
              <td class="titulo negrita izquierda">{{number_format($montom, 2, '.',',')}}</td>
            @endforeach
            @php $montott    =   $coleccionarrozcascara->where('tipo','=','PE')->sum('salida_importe'); @endphp
            <td class="titulo negrita izquierda">{{number_format($montott, 2, '.',',')}}</td>
        </tr> 
    </tbody>
</table>

<table class="table table-condensed table-striped" >
    <thead>
      <tr>
        <th class="titulo center">Envases</th>
        @foreach($listaperido as $indexp => $itemp)
          <th class="titulo center">{{$itemp->TXT_NOMBRE}}</th>
        @endforeach
        <th class="titulo center">TOTAL</th>
      </tr>
    </thead>
    <tbody>
    @foreach($listacostoenvases as $index => $item)
        <tr>
            <td>{{$item->TXT_ABREVIATURA}}</td>
            @foreach($listaperido as $indexp => $itemp)
              @php $monto    =   $coleccionarrozcascara->where('periodo_id','=',$itemp->COD_PERIODO)
                                 ->where('producto_id','=',$item->NOM_CATEGORIA)->where('tipo','=','CE')->sum('salida_importe'); @endphp
              <td class="negrita izquierda">{{number_format($monto, 2, '.',',')}}</td>
            @endforeach
            @php $montop    =   $coleccionarrozcascara->where('producto_id','=',$item->NOM_CATEGORIA)->where('tipo','=','CE')->sum('salida_importe'); @endphp
            <td class="titulo negrita izquierda">{{number_format($montop, 2, '.',',')}}</td>
        </tr>               
    @endforeach
        <tr>
            <td class="titulo negrita izquierda">TOTALES</td>
            @foreach($listaperido as $indexp => $itemp)
              @php $montom    =   $coleccionarrozcascara->where('periodo_id','=',$itemp->COD_PERIODO)->where('tipo','=','CE')->sum('salida_importe'); @endphp
              <td class="titulo negrita izquierda">{{number_format($montom, 2, '.',',')}}</td>
            @endforeach
            @php $montott    =   $coleccionarrozcascara->where('tipo','=','CE')->sum('salida_importe'); @endphp
            <td class="titulo negrita izquierda">{{number_format($montott, 2, '.',',')}}</td>
        </tr> 
    </tbody>
</table>


</html>


