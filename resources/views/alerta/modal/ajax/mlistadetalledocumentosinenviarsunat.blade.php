
<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$empresa->NOM_EMPR}}
		</h5>
	</div>
</div>
<div class="modal-body">
	<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;"> 
		<table class="table table-condensed table-striped">
		    <thead>
		      <tr>
		      	<th>EMPRESA EMISOR</th>
		        <th>TIPO DOCUMENTO</th>
		        <th>SERIE</th>
		        <th>NRO DOCUMENTO</th>
		        <th>CLIENTE</th>
		        <th>FECHA EMISION</th>
		        <th>ESTADO DOCUMENTO</th>
		        <th>TRABAJADOR</th>
		      </tr>
		    </thead>
		    <tbody>
		    @foreach($lista_documento_sin_enviar as $index => $item)
		      	<tr>
		      	   <td>{{$item->EMPR_EMISOR}}</td>
			       <td>{{$item->TIPO_DOC}}</td>
			       <td>{{$item->NRO_SERIE}}</td>
		      	   <td>{{$item->NRO_DOC}}</td>
			       <td>{{$item->CLIENTE}}</td>
			       <td>{{date_format(date_create($item->FEC_EMISION), 'd-m-Y')}}</td>
			       <td>{{$item->ESTADO_DOC_CTBLE}}</td>
			       <td>{{$item->NOM_TRABAJADOR}}</td>
		      	</tr>                  
		    @endforeach
		    </tbody>
		</table>
	</div>
</div>

<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn btn-default btn-space">Cerrar</button>

</div>




