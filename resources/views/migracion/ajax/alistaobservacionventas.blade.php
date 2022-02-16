<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>Periodo</th>
      <th>Tipo Documento</th>
      <th>Serie</th>
      <th>Nro</th>
      <th>Fecha Emision</th>
      <th>Observación</th>
<!-- 
      <th>
        <div class="text-center be-checkbox be-checkbox-sm has-primary">
          <input  type="checkbox"
                  class="todo_asignar input_asignar"
                  id="todo_asignar"
          >
          <label  for="todo_asignar"
                  data-atr = "todas_asignar"
                  class = "checkbox_asignar"                    
                  name="todo_asignar"
            ></label>
        </div>
      </th> -->

    </tr>
  </thead>
  <tbody>
    @foreach($lista_ventas as $index=>$item)
      <tr class='dobleclickpc seleccionar'
          data_cod_documento = "{{$item->COD_REFERENCIA}}"
          data_cod_tipo_asiento = "{{$item->COD_CATEGORIA_TIPO_ASIENTO}}"
        >
        <td>{{$item->periodo->TXT_NOMBRE}}</td>
        <td>{{$item->documento_ctble->TXT_CATEGORIA_TIPO_DOC}}</td>
        <td>{{$item->documento_ctble->NRO_SERIE}}</td>
        <td>{{$item->documento_ctble->NRO_DOC}}</td>
        <td>{{date_format(date_create($item->documento_ctble->FEC_EMISION), 'd-m-Y')}}</td>
        <td>{{$item->TXT_ERROR}}</td>
<!--         
        <td>

            <div class="text-center be-checkbox be-checkbox-sm has-primary">
              <input  type="checkbox"
                class="{{$item->COD_REFERENCIA}}{{$index}} input_asignar"
                id="{{$item->COD_REFERENCIA}}{{$index}}" >

              <label  for="{{$item->COD_REFERENCIA}}{{$index}}"
                    data-atr = "ver"
                    class = "checkbox checkbox_asignar"                    
                    name="{{$item->COD_REFERENCIA}}{{$index}}"
              ></label>
            </div>

        </td> -->
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