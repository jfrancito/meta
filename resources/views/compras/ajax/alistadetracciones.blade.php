
<table class="table table-condensed table-hover table-bordered table-striped">
  <thead>
    <tr>
      <th>RUC DEL ADQUIRIENTE</th>
      <th>NOMBRE DEL ADQUIRIENTE</th>
      <th>Nº DE LOTE</th>
      <th>IMPORTE TOTAL</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>{{$ruc}}</td>
      <td>{{$nombre_empresa}}</td>
      <td>{{$lote}}</td>
      <td>{{$sum_total_detraccion}}</td>

    </tr>
  </tbody>
</table>

<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>RUC PROVEEDOR</th>
      <th>NOMBRE O RAZON SOCIAL</th>
      <th>N ° DE PROFORMA</th>
      <th>CÓDIGO BIEN O SERVICIO</th>
      <th>N° CUENTA CORRIENTE PROVEEDOR</th>
      <th>IMPORTE DEL DEPÓSITO</th>
      <th>COD DEL TIPO DE OPERACIÓN REALIZADA</th>
      <th>PERIODO TRIBUTARIO</th>
      <th>TIPO DE COMPROBANTE</th>
      <th>SERIE DE COMPROBANTE</th>
      <th>NUMERO DE COMPROBANTE</th>


    </tr>
  </thead>
  <tbody>
    @foreach($listadetracciones as $index => $item)

      <tr>
        <td>{{$item['nro_documento']}}</td>
        <td>{{$item['nombre_proveedor']}}</td>
        <td>{{$item['plataforma']}}</td>
        <td>{{$item['codigobienservicio']}}</td>
        <td>{{$item['nro_cuenta']}}</td>
        <td>{{$item['importe']}}</td>
        <td>{{$item['tipo_operacion']}}</td>
        <td>{{$item['periodo']}}</td>
        <td>{{$item['tipo_documento']}}</td>
        <td>{{$item['nro_serie']}}</td>
        <td>{{$item['nro_correlativo']}}</td>
      </tr>   

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