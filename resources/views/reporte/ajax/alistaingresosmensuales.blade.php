<table class="table table-condensed table-hover table-bordered table-striped"> 

  <thead>
    <tr>
      <th>Cuenta</th>
      <th>Glosa</th>
      @foreach($lista_periodo as $index=>$item)
        <th>{{$item->TXT_NOMBRE}}</th>
      @endforeach
      <th>TOTALES</th>
    </tr>
  </thead>
  <tbody>
    @foreach($ingresosmensuales as $index=>$item)


    <tr class="{{$item['bg']}}">
        @for ($i = 0; $i < $item['cantidadarray']; $i++)
            <td class="{{$item['negrita']}}">{{$item['item'.$i]}}</td>
        @endfor
        <td class="{{$item['negrita']}}">{{$item['totales']}}</td>
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