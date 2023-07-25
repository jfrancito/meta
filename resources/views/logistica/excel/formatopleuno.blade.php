<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	{!! Html::style('public/css/excel/excel.css') !!}

    <!-- titulo -->
    <table>
        <tr>
            <th class= 'tabladp'></th>                                    
            @for ($i = 1; $i <= 37; $i++)
            <th class= 'tabladp'>{{$i}}</th>                                    
            @endfor
        </tr>

        <tr>
            <th class= 'tabladp'>Fila/Col</th>
            <th class= 'tabladp'>Periodo</th>
            <th class= 'tabladp'>Periodo Codigo</th>              
            <th class= 'tabladp'>Periodo Registro</th> 
            <th class= 'tabladp'>Catálogo- Ver Tabla 13.</th>
            <th class= 'tabladp'>Codigo Activo. Según Campo 04</th>
            <th class= 'tabladp'>Codigo UNSPSC o GTIN.  Ver Tabla 13</th>
            <th class= 'tabladp'>Tipo Activo. Según campo 06.</th>
            <th class= 'tabladp'>Codigo Activo. Ver Tabla 18</th>
            <th class= 'tabladp'>Cuenta Activo</th>
            <th class= 'tabladp'>Estado Activo</th>
            <th class= 'tabladp'>Descripción Activo</th>
            <th class= 'tabladp'>Marca</th>
            <th class= 'tabladp'>Modelo</th>
            <th class= 'tabladp'>Serie</th>
            <th class= 'tabladp'>Saldo Inicial</th>
            <th class= 'tabladp'>Adquis. o Adiciones</th>
            <th class= 'tabladp'>Mejoras</th>
            <th class= 'tabladp'>Retiros</th>
            <th class= 'tabladp'>Ajustes</th>
            <th class= 'tabladp'>Revaluacion Voluntaria</th>        
            <th class= 'tabladp'>Revaluacion por Reorganizacion</th>   
            <th class= 'tabladp'>Otras Revaluaciones</th>
            <th class= 'tabladp'>Ajuste por Inflación</th>
            <th class= 'tabladp'>Adquisicion</th>
            <th class= 'tabladp'>Activado el</th>
            <th class= 'tabladp'>Metodo Depreciacion</th>            
            <th class= 'tabladp'>Autorizacion para Cambiar Metodo</th>            
            <th class= 'tabladp'>Tasa Deprec.</th>            
            <th class= 'tabladp'>Depreciacion Acumulada Ej. Anterior</th>            
            <th class= 'tabladp'>Depreciacion sin Revaluacion</th>            
            <th class= 'tabladp'>Depreciacion de Retiros/bajas</th>            
            <th class= 'tabladp'>Depreciacion de Otros Aj.</th>            
            <th class= 'tabladp'>Deprec. Revaluacion Volunt.</th>            
            <th class= 'tabladp'>Depreciac. por Reorg. de Sociedades</th>
            <th class= 'tabladp'>Deprec. de Otras Revaluaciones</th>
            <th class= 'tabladp'>Ajuste por Inflac. de la Deprec</th>
            <th class= 'tabladp'>Estado</th>
        </tr>

            {{$fila = 1}}
            @foreach($catalogo as $activo) 

                    @if(count($catalogo)>0) 
                        
                            <tr>
                                <td>{{$fila}}</td>
                                <td>{{$anio_actual}}0000</td>
                                <td>{{$activo['cod_periodo']}}{{$activo['nro_asiento']}}</td>
                                <td>{{$activo['cond_asiento']=='APERTURA' ? 'A' : 'M'}}{{$activo['nro_asiento']}}</td>
                                <td>9</td>
                                <td>{{$activo['item_ple']}}</td>
                                <td></td>
                                <td></td>
                                <td>1</td>
                                <td>{{$activo['cuenta_activo']}}</td>
                                <td>9</td>
                                <td>{{$activo['nombre']}}</td>
                                <td>{{$activo['marca']}}</td>
                                <td>{{$activo['modelo']}}</td>
                                <td>{{$activo['numero_serie']}}</td>
                                <td>{{$activo['cond_asiento']=='APERTURA' ? number_format($activo['saldo_inicial'], 2, '.', ',') : '0.00'}}</td>
                                <td>{{date("Y",strtotime($activo['fecha_adquisicion'])) == $anio_actual ? number_format($activo['adquisicion'], 2, '.', ',') : '0.00'}}</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>{{($activo['fecha_adquisicion'] != '') ? date("d/m/Y",strtotime($activo['fecha_adquisicion'])) : ''}}</td>                        
                                <td>{{($activo['fecha_inicio_depreciacion'] != '') ? $activo['fecha_inicio_depreciacion'] : ''}}</td>                        
                                <td>1</td>
                                <td>-</td>
                                <td>{{number_format($activo['tasa_depreciacion'], 2, '.', ',')}}</td>
                                <td>{{$activo['cond_asiento']=='SALDO' ? number_format($activo['depreciacion_acumulada_anio_anterior'], 2, '.', ',') : '0.00'}}</td>
                                <td>{{$activo['cond_asiento']=='MOVIMIENTO' ? number_format($activo['depreciacion_anio'], 2, '.', ',') : '0.00'}}</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>1</td>

{{--                                 <td >{{date("m",strtotime($activo['fecha_registro']))}}</td>
                                <td>{{$activo['cuenta_activo']}}</td>
                                <td>{{$activo['tipo_activo']}}</td>
                                <td>{{$activo['factura']}}</td>
                                <td>{{$activo['empresa']}}</td>
                                <td>{{$activo['categoria']}}</td>
                                <td>{{($activo['mes_adquisicion'] != '') ? $activo['mes'][$activo['mes_adquisicion']] : ''}}</td>
                                <td>{{($activo['fecha_baja'] != '') ? date("m", $activo['fecha_baja']) : ''}}</td>
                                <td></td>
                                <td>S/. {{number_format($activo['base_de_calculo'], 4, '.', ',')}}</td>                        
                                <td>{{$activo['fecha_inicio_depreciacion']}}</td>
                                <td>{{$activo['dias']}}</td>
                                <td>S/. {{number_format($activo['por_depreciar'], 4, '.', ',')}}</td>
                                <td>{{$activo['condicion']}}</td>
                                @for ($i=1; $i<=$mes; $i++)                         
                                    <td>
                                        @isset($activo['meses'][$i])
                                        S/. {{number_format($activo['meses'][$i], 4, '.', ',')}}
                                        @endisset
                                    </td>
                                @endfor
                                <td>S/. {{number_format($activo['depreciacion_anio'], 4, '.', ',')}}</td>
                                <td>S/. {{number_format($activo['depreciacion_acumulada_total'], 4, '.', ',')}}</td>
                                <td></td>
                                <td>S/. {{number_format($activo['saldo_final_depreciacion'], 4, '.', ',')}}</td>
                                <td>S/. {{number_format($activo['saldo_a_depreciar'], 4, '.', ',')}}</td> --}}
                            </tr>
                        
                    @else 
                        <tr>
                        <td colspan="38"></td>   
                        </tr>
                    @endif
                
            {{$fila++}}
            @endforeach


    </table>
</html>
