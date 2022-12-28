<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$tipoproducto->NOM_CATEGORIA}}
		</h5>
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$periodo->TXT_NOMBRE}}
		</h5>		
	</div>
</div>
<div class="modal-body">
	<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;"> 

		<table class="table table-condensed table-striped">
		    <thead>
		      <tr>
		        <th>PERIODO</th>
		        <th>FECHA</th>
		        <th>GLOSA</th>
		        <th>MONEDA</th>
		        <th>TOTAL DEBE</th>
		        <th>TOTAL HABER</th>
		      </tr>
		    </thead>

		    <tbody>
		    @foreach($cabecera_asiento as $index => $item)
		      	<tr>
			       <td>{{$item['nombre_periodo']}}</td>
			       <td>{{date_format(date_create($item['fecha']), 'd-m-Y')}}</td>
			       <td>{{$item['glosa']}}</td>
			       <td>{{$item['moneda']}}</td>
			       <td>{{number_format($item['total_debe'], 2, '.', ',')}}</td>
			       <td>{{number_format($item['total_haber'], 2, '.', ',')}}</td>
		      	</tr>                  
		    @endforeach
		    </tbody>
		</table>


	<table class="table table-condensed table-striped">
		    <thead>
		      <tr>
		        <th>LINEA</th>
		        <th>CUENTA</th>
		        <th>GLOSA</th>
		        <th>DEBE</th>
		        <th>HABER</th>
		      </tr>
		    </thead>

		    <tbody>
		    @foreach($detalle_asiento as $index => $item)
		      	<tr>
			       <td>{{$item['linea']}}</td>
			       <td>{{$item['cuenta_nrocuenta']}}</td>
			       <td>{{$item['glosa']}}</td>
			       <td>{{number_format($item['total_debe'], 2, '.', ',')}}</td>
			       <td>{{number_format($item['total_haber'], 2, '.', ',')}}</td>
		      	</tr>                  
		    @endforeach
		    </tbody>
		</table>

	</div>
</div>

<div class="modal-footer">
<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>

</div>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){

      App.init();
      App.formElements();
      $('form').parsley();

    });
  </script>
@endif


