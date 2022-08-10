<table id="ple" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>fecha</th>
      <th>cdocu</th>
      <th>ndocu</th>
      <th>nomcli</th>
      <th>ruccli</th>

      <th>codi</th>
      <th>MONE</th>
      <th>TCAM</th>
      <th>CANT</th>
      <th>PREU</th>

      <th>TOTA</th>
      <th>TOTI</th>
      <th>TOTIVA</th>
      <th>TOTN</th>
      <th>aigv</th>


      <th>codalm</th>
      <th>codvta</th>
      <th>CODSCC</th>
    </tr>
  </thead>
  <tbody>
    @foreach($lista_migracion as $index => $item)
      <tr>
        <td>{{$item['fecha_emision']}}</td>
        <td>{{$item['tipo_documento']}}</td>
        <td>{{$item['ndoc']}}</td>
        <td>{{$item['nombre_cliente']}}</td>
        <td>{{$item['ruc']}}</td>

        <td>{{$item['codi']}}</td>
        <td>{{$item['MONE']}}</td>
        <td>{{$item['TCAM']}}</td>
        <td>{{$item['CANT']}}</td>
        <td>{{$item['PREU']}}</td>

        <td>{{$item['TOTA']}}</td>
        <td>{{$item['TOTI']}}</td>
        <td>{{$item['TOTIVA']}}</td>
        <td>{{$item['TOTN']}}</td>
        <td>{{$item['aigv']}}</td>

        <td>{{$item['codalm']}}</td>
        <td>{{$item['codvta']}}</td>
        <td>{{$item['CODSCC']}}</td>

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