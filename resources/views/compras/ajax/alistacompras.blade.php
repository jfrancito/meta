<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>NRO SERIE</th>
      <th>NRO DOCUMENTO</th>
      <th>FECHA EMISION</th>
      <th>TIPO DOCUMENTO</th>
      <th>PROVEEDOR</th>
      <th>MONEDA</th>
      <th>SUB TOTAL</th>
      <th>IGV</th>
      <th>TOTAL</th>
      <th>ESTADO</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listacompras as $index => $item)
      <tr>
        <td>{{$item->NRO_SERIE}}</td>
        <td>{{$item->NRO_DOC}}</td>
        <td>{{date_format(date_create($item->FEC_EMISION), 'd-m-Y')}}</td>
        <td>{{$item->NOM_TIPO_DOC}}</td>
        <td>{{$item->NOM_PROVEEDOR}}</td>
        <td>{{$item->NOM_MONEDA}}</td>
        <td>{{number_format($item->CAN_SUB_TOTAL, $redondeo, '.', ',')}}</td>
        <td>{{number_format($item->CAN_IMPUESTO_VTA, $redondeo, '.', ',')}}</td>
        <td>{{number_format($item->CAN_TOTAL, $redondeo, '.', ',')}}</td>
        <td>{{$item->NOM_ESTADO}}</td>
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