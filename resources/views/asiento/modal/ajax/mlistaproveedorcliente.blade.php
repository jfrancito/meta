
<div class="modal-header">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<h3 class="modal-title">
		 LISTA DOCUMENTOS CON SALDO
	</h3>
</div>
<div class="modal-body">
	<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;"> 

  	<table id="table1" class="table table-striped table-hover table-fw-widget listatabla">
        <thead>
          <tr>
            <th>Documento</th>
            <th>Fecha</th>
            <th>Nombre o Razon Social</th>
            <th>Moneda</th>
            <th>Saldo</th>
            <th>
<!-- 	          <input  type="checkbox"
	                  class="todo_asignar input_asignar"
	                  id="todo_asignar"
	          >
	          <label  for="todo_asignar"
	                  data-atr = "todas_asignar"
	                  class = "checkbox_asignar"                    
	                  name="todo_asignar"
	            ></label> -->
            </th>
          </tr>
        </thead>
        <tbody>
          	@foreach($listaasiento as $index=>$item)
                <tr data_cod_asiento = "{{$item->COD_ASIENTO}}">
                    <td>{{$item->NRO_SERIE}} - {{$item->NRO_DOC}}</td>
                    <td>{{date_format(date_create($item->FEC_ASIENTO), 'd-m-Y')}}</td>
                    <td>{{$item->TXT_EMPR_CLI}}</td>
                    <td>{{$item->TXT_CATEGORIA_MONEDA}}</td>
                    <td>{{$item->CAN_TOTAL_DEBE}}</td>
                    <td>
                    	
		              <div class="text-center be-checkbox be-checkbox-sm has-primary">
		                <input  type="checkbox"
		                  class="{{$item->COD_ASIENTO}}{{$index}} input_asignar"
		                  id="{{$item->COD_ASIENTO}}{{$index}}" >

		                <label  for="{{$item->COD_ASIENTO}}{{$index}}"
		                      data-atr = "ver"
		                      class = "checkbox checkbox_asignar"                    
		                      name="{{$item->COD_ASIENTO}}{{$index}}"
		                ></label>
		              </div>

                    </td>
                </tr>                    
          	@endforeach
        </tbody>
  	</table>

	</div>

</div>

<div class="modal-footer">
  <button type="submit" data-dismiss="modal" class="btn btn-success btn-asigna-asiento-pc">Asignar</button>
</div>




