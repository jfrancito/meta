<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	{!! Html::style('public/css/excel/excel.css') !!}

    <!-- titulo -->
    <table>
        <tr>
            <th class= 'tabladp'></th>                                    
            @for ($i = 1; $i <= 11; $i++)
            <th class= 'tabladp'>{{$i}}</th>                                    
            @endfor
        </tr>

        <tr>
            <th class= 'tabladp'>Fila/Col</th>
            <th class= 'tabladp'>Periodo</th>
            <th class= 'tabladp'>Periodo Codigo</th>              
            <th class= 'tabladp'>Periodo Registro</th> 
            <th class= 'tabladp'>Catálogo</th>
            <th class= 'tabladp'>Codigo Activo</th>
            <th class= 'tabladp'>Fecha Arrendamiento</th>
            <th class= 'tabladp'>Codigo Propio del Activo Fijo</th>
            <th class= 'tabladp'>Fecha de Inicio del Arrendamiento</th>
            <th class= 'tabladp'>Número de Cuotas Pactadas</th>
            <th class= 'tabladp'>Monto Total del Contrato</th>
            <th class= 'tabladp'>Indica el Estado de la Operación</th>
        </tr>

            {{$fila = 1}}
            @foreach($catalogo as $activo) 

                    @if(count($catalogo)>0) 
                        
                            <tr>
                                <td>{{$fila}}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>9</td>
                                <td>{{$activo['item_ple']}}</td>
                                <td>{{($activo['fecha_adquisicion'] != '') ? date("d/m/Y",strtotime($activo['fecha_adquisicion'])) : ''}}</td>                        
                                <td>{{$activo['item_ple']}}</td>
                                <td>{{($activo['fecha_adquisicion'] != '') ? date("d/m/Y",strtotime($activo['fecha_adquisicion'])) : ''}}</td>                        
                                <td>1</td>
                                <td>{{number_format($activo['saldo_inicial'], 2, '.', ',')}}</td>
                                <td>1</td>
                            </tr>
                        
                    @else 
                        <tr>
                        <td colspan="12"></td>   
                        </tr>
                    @endif
                
            {{$fila++}}
            @endforeach


    </table>
</html>
