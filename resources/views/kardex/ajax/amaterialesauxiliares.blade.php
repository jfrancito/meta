<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th rowspan="2" class="color-ich">FECHA</th>
      <th rowspan="2" class="color-ich">TIPODOCUMENTO</th>
      <th rowspan="2" class="color-ich">NRODOCUMENTO</th>
      <th rowspan="2" class="color-ich">NOMREF</th>
      <th rowspan="2" class="color-ich">RUC</th>
      <th rowspan="2" class="color-ich">DESCRIPCION</th>

      <th colspan="3" class="center color-ich">INGRESOS</th>
    </tr>
    <tr>
      <th class="color-ich">CANTIDAD</th>
      <th class="color-ich">C.U.</th>
      <th class="color-ich">ENTRADA</th>
    </tr>
  </thead>
  <tbody>



    @foreach($arraymovimientocommpra as $index => $item)
      <tr>
         <td>{{date_format(date_create($item["FECHA"]), 'd-m-Y')}}</td>
         <td>{{$item['TIPODOCUMENTO']}}</td>
         <td>{{$item['NRODOCUMENTO']}}</td>
         <td>{{$item['NOMREF']}}</td>
         <td>{{$item['RUC']}}</td>

         <td>{{$item['DESCRIPCION']}}</td>
         <td>{{$item['CANTIDAD']}}</td>
         <td>{{$item['COSTOUNITARIO']}}</td>
         <td>{{$item['ENTRADA']}}</td>
        
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