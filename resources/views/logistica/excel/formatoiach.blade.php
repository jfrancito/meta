<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	{!! Html::style('public/css/excel/excel.css') !!}

    <!-- titulo -->
    <table>
        <tr>
            <td class = 'subtitulos' colspan="4">{{$empresa}} - {{$centro}}</td>                    
        </tr>

        <tr>
            <th class= 'tabladp'>Cuenta</th>
            <th class= 'tabladp'>Descripcion</th>
            <th class= 'tabladp'>Codigo PLE</th>              
            <th class= 'tabladp'>RUC</th>
            <th class= 'tabladp'>Factura</th>
            <th class= 'tabladp'>Modelo/Marca</th>
            <th class= 'tabladp'>Serie</th>            
            <th class= 'tabladp'>Tipo de Activos</th>
            <th class= 'tabladp'>Mes Adquisiciones</th>
            <th class= 'tabladp'>Saldo Inicial</th>
            <th class= 'tabladp'>Adquisiciones</th>
            <th class= 'tabladp'>Bajas Ejercicios Anteriores</th>
            <th class= 'tabladp'>Bajas</th>
            <th class= 'tabladp'>Valor Residual</th>
            <th class= 'tabladp'>Base de Calculo</th>
            <th class= 'tabladp'>Fecha de Adq</th>
            <th class= 'tabladp'>Fecha de Inicio</th>
            <th class= 'tabladp'>Tasa</th>
            <th class= 'tabladp'>Saldo Inicial Deprec Acumulada</th>
            @for ($i=1; $i<=$mes; $i++)
                <th class= 'tabladp'>{{$meses_esp[$i]}}</th>   
            @endfor
            <th class= 'tabladp'>Deprec {{ $anio_actual }}</th>
            <th class= 'tabladp'>Deprec Acumulada al {{ $anio_actual }}</th>
            <th class= 'tabladp'>Saldo a depreciar {{ $anio_actual }}</th>            
        </tr>

            @foreach($catalogo as $activo) 

                @if(count($catalogo)>0) 

                    <tr>
                        <td>{{$activo['cuenta_activo']}}</td>
                        <td>{{$activo['nombre']}}</td>
                        <td>{{$activo['item_ple']}}</td>
                        <td>{{$activo['ruc']}}</td>
                        <td>{{$activo['factura']}}</td>
                        <td>{{$activo['modelo']}}/{{$activo['marca']}}</td>
                        <td>{{$activo['numero_serie']}}</td>
                        <td>{{$activo['categoria']}}</td>
                        <td>{{($activo['mes_adquisicion'] != '') ? date("m",$activo['mes_adquisicion']) : ''}}</td>
                        <td>S/. {{number_format($activo['saldo_inicial'], 4, '.', ',')}}</td>
                        <td>S/. {{number_format($activo['adquisicion'], 4, '.', ',')}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>S/. {{number_format($activo['base_de_calculo'], 4, '.', ',')}}</td>                        
                        <td>{{($activo['fecha_adquisicion'] != '') ? $activo['fecha_adquisicion'] : ''}}</td>                        
                        <td>{{$activo['fecha_inicio_depreciacion']}}</td>
                        <td>% {{number_format($activo['tasa_depreciacion'], 4, '.', ',')}}</td>
                        <td>S/. {{number_format($activo['depreciacion_acumulada_anio_anterior'], 4, '.', ',')}}</td>
                        @for ($i=1; $i<=$mes; $i++)                         
                            <td>
                                @isset($activo['meses'][$i])
                                S/. {{number_format($activo['meses'][$i], 4, '.', ',')}}
                                @endisset
                            </td>
                        @endfor
                        <td>S/. {{number_format($activo['depreciacion_anio'], 4, '.', ',')}}</td>
                        <td>S/. {{number_format($activo['depreciacion_acumulada_total'], 4, '.', ',')}}</td>
                        <td>S/. {{number_format($activo['saldo_a_depreciar'], 4, '.', ',')}}</td>
                      </tr>
                @else 
                    <tr>
                      <td></td>
                    </tr>
                @endif

            @endforeach


    </table>
</html>
