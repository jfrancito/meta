
<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$empresa_txt}} - {{$categoria_txt}} - {{$serie}}
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
		      </tr>
		    </thead>
		    <tbody>
				@for ($i = 0; $i < count($lista_documento_correlativo_detalle); $i++)
		      	<tr>
			      	   <td>{{$empresa_txt}}</td>
				       <td>{{$categoria_txt}}</td>
				       <td>{{$serie}}</td>
			      	   <td>{{$lista_documento_correlativo_detalle[$i]}}</td>
			      	</tr>  
				@endfor
		    </tbody>
		</table>
	</div>
</div>

<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn btn-default btn-space">Cerrar</button>
</div>




