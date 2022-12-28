<table id="ple" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Periodo</th>
      <th>Codigo de la cuenta</th>
      <th>Descripcion de la cuenta</th>
      <th>Codido del plan</th>
      <th>Descripcion del plan de cuentas</th>
      <th>Codigo de la cuenta contable corporativa</th>
      <th>Descripcion de la cuenta contable corporativa</th>
      <th>Estado</th>
      <th>Campo libre</th>
    </tr>
  </thead>
  <tbody>
    @foreach($lista_asiento as $index => $item)
      <tr>
        <td>{{$item['periodo_01']}}</td>
        <td>{{$item['cuenta_02']}}</td>
        <td>{{$item['nombre_cuenta_03']}}</td>
        <td>{{$item['valor_04']}}</td>
        <td>{{$item['valor_05']}}</td>
        <td>{{$item['valor_06']}}</td>
        <td>{{$item['valor_07']}}</td>
        <td>{{$item['valor_08']}}</td>
        <td>{{$item['valor_09']}}</td>

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