<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7">
  <thead>
    <tr>
      <th>Banco</th>
      <th>Nro Cuenta</th>
      <th>Ind Caja</th>
      <th>Ind Banco</th>
      <th>Cuenta Asociado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listacajabanco as $item)
      <tr class="dobleclickcb seleccionar"
        data_banco_caja_id = "{{$item->COD_CAJA_BANCO}}"
        >
        <td>
          @if($item->IND_CAJA == '1') 
            {{$item->TXT_CAJA_BANCO}}
          @else
            {{$item->TXT_BANCO}}
          @endif
        </td>
        <td>{{$item->TXT_NRO_CCI}}</td>
        <td>{{$item->IND_CAJA}}</td>
        <td>{{$item->IND_BANCO}}</td>

        <td>
          @if($item->TXT_TIPO_REFERENCIA != '') 
            {{$item->cuentacontable->nro_cuenta}} - {{$item->cuentacontable->nombre}}
          @endif

          
        </td>
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