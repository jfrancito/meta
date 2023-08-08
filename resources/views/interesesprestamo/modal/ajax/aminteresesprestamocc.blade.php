<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<div class="modal-header" style="padding: 12px 20px;">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<div class="col-xs-12">
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{-- {{$glosa}} --}}
            ASIENTOS INTERESES PR&Eacute;STAMO
		</h5>
		<h5 class="modal-title" style="font-size: 1.2em;">
			{{$periodo->TXT_NOMBRE}}
		</h5>		
	</div>
</div>
<div class="modal-body">
	<div class="scroll_text scroll_text_heigth_aler" style = "padding: 0px !important;"> 

        
        <div class="panel-group" id="accordion">
            @foreach($asientos_intereses_prestamo as $index_asiento => $asiento)
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$index_asiento}}">
                            {{$asientos_intereses_prestamo[$index_asiento]['cabecera'][0]['glosa']}}  -  {{$asientos_intereses_prestamo[$index_asiento]['cabecera'][0]['moneda'] == 'SOLES' ? 'S/. ' : '$ '}} {{number_format($asientos_intereses_prestamo[$index_asiento]['cabecera'][0]['total_debe'], 2, '.', ',')}}
                    </a>
                </h4>
              </div>
              <div id="collapse{{$index_asiento}}" class="panel-collapse collapse">
                <div class="panel-body">
                    
                    @foreach($asiento['cabecera'] as $index => $item)
                    
                        <table class="table table-condensed table-striped">
                            <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Fecha</th>
                                <th>Glosa</th>
                                <th>Moneda</th>
                                <th>T.C.</th>
                                <th>Total Debe</th>
                                <th>Total Haber</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                <td>{{$item['nombre_periodo']}}</td>
                                <td>{{date_format(date_create($item['fecha']), 'd-m-Y')}}</td>
                                <td>{{$item['glosa']}}</td>
                                <td>{{$item['moneda']}}</td>
                                <td>{{$item['tipo_cambio']}}</td>
                                <td>{{number_format($item['total_debe'], 2, '.', ',')}}</td>
                                <td>{{number_format($item['total_haber'], 2, '.', ',')}}</td>
                                </tr>                  
                            </tbody>
                        </table>            
                        
                    @endforeach
                        
                        <table class="table table-condensed table-striped">
                            <thead>
                            <tr>
                                <th>Linea</th>
                                <th>Cuenta</th>
                                <th>Glosa</th>
                                <th>Debe MN</th>
                                <th>Haber MN</th>
                                <th>Debe ME</th>
                                <th>Haber ME</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($asiento['detalle'] as $index => $item)
                                <tr>
                                <td>{{$item['linea']}}</td>
                                <td>{{$item['cuenta_nrocuenta']}}</td>
                                <td>{{$item['glosa']}}</td>
                                <td>{{number_format($item['total_debe'], $redondeo, '.', ',')}}</td>
                                <td>{{number_format($item['total_haber'], $redondeo, '.', ',')}}</td>
                                <td>{{number_format($item['total_debe_dolar'], $redondeo, '.', ',')}}</td>
                                <td>{{number_format($item['total_haber_dolar'], $redondeo, '.', ',')}}</td>
                                </tr>                  
                            @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                <th colspan="3">Totales</th>
                                <th>{{number_format(array_sum(array_column($asiento['detalle'],'total_debe')), $redondeo, '.', ',')}}</th>
                                <th>{{number_format(array_sum(array_column($asiento['detalle'],'total_haber')), $redondeo, '.', ',')}}</th>
                                <th>{{number_format(array_sum(array_column($asiento['detalle'],'total_debe_dolar')), $redondeo, '.', ',')}}</th>
                                <th>{{number_format(array_sum(array_column($asiento['detalle'],'total_haber_dolar')), $redondeo, '.', ',')}}</th>
                                </tr>
                            </tfoot>

                        </table>
                        
                    
                </div>
              </div>
            </div>
            @endforeach
        </div>

	</div>
</div>

	<form method="POST"
	id="formguardar"
	action="{{ url('/intereses-prestamo-guardar-data/'.$idopcion) }}" 
	style="border-radius: 0px;" 
	>
		{{ csrf_field() }}
{{-- 		<input type="hidden" name="cabecera" id='cabecera' value='{{json_encode($asiento['cabecera'])}}'>
		<input type="hidden" name="detalle" id='detalle' value='{{json_encode($asiento['detalle'])}}'> --}}
        <input type="hidden" name="asientos_intereses_prestamo" id='asientos_intereses_prestamo' value='{{json_encode($asientos_intereses_prestamo)}}'>
		<input type="hidden" name="periodog_id" id='periodog_id' value='{{$periodo->COD_PERIODO}}'>

	<div class="modal-footer">



			<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cerrar</button>
			<button type="submit" data-dismiss="modal" class="btn btn-success btn-guardar-configuracion">Guardar</button>



	</div>
	</form>