<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />



<h3>{{$nombreproducto}}</h3>
<table class="table table-condensed table-striped" >
    <thead>
      <tr>

        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;"></th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;"></th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;"></th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;"></th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;"></th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;"></th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;"></th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;"></th>
        <th colspan="3" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;text-align: center;" class="center color-ich">ENTRADAS</th>
        <th colspan="3" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;text-align: center;" class="center color-ich">SALIDAS</th>
        <th colspan="3" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;text-align: center;" class="center color-ich">SALDO</th>


      </tr>


      <tr>


        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">MES</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">FECHA</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">SERVICIO</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">PRODUCTO</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">SERIE</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">CORRELATIVO</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">RUC</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">CLIENTE</th>



        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">CANTIDAD</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">C.U.</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">IMPORTE</th>

        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">CANTIDAD</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">C.U.</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">IMPORTE</th>     

        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">CANTIDAD</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">C.U.</th>
        <th class="color-ich" style="background-color: #ADD8E6 !important;border-bottom-color: #ADD8E6 !important;">IMPORTE</th>

      </tr>
    </thead>
    <tbody>
    @foreach($listakardexif as $index => $item)
        <tr>
           <td>{{$item['nombre_periodo']}}</td>
           <td>{{date_format(date_create($item["fecha"]), 'd-m-Y')}}</td>
           <td>{{$item['servicio']}}</td>
           <td>{{$item['nombre_producto']}}</td>
           <td>{{$item['serie']}}</td>
           <td>{{$item['correlativo']}}</td>
           <td>{{$item['ruc']}}</td>
           <td>{{$item['nombre_cliente']}}</td>
           <td class="negrita">{{number_format($item['entrada_cantidad'], 2, '.','')}}</td>
           <td class="negrita">{{number_format($item['entrada_cu'], 2, '.', '')}}</td>
           <td class="negrita">{{number_format($item['entrada_importe'], 2, '.', '')}}</td>

           <td class="negrita">{{number_format($item['salida_cantidad'], 2, '.', '')}}</td>
           <td class="negrita">{{number_format($item['salida_cu'], 2, '.', '')}}</td>
           <td class="negrita">{{number_format($item['salida_importe'], 2, '.', '')}}</td>

           <td class="negrita">{{number_format($item['saldo_cantidad'], 2, '.', '')}}</td>
           <td class="negrita">{{number_format($item['saldo_cu'], 2, '.', '')}}</td>
           <td class="negrita">{{number_format($item['saldo_importe'], 2, '.', '')}}</td>
        </tr>                  
    @endforeach
    </tbody>
</table>
</html>


