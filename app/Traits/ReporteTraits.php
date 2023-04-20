<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;


use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use App\Modelos\CONPeriodo;
use App\Modelos\WEBViewMigrarVenta;
use App\Modelos\CMPDocumentoCtble;
use App\Modelos\WEBHistorialMigrar;
use App\Modelos\CMPDetalleProducto;
use App\Modelos\WEBProductoEmpresa;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\CMPReferecenciaAsoc;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\CMPCategoria;

use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\WEBAsiento;
use App\Modelos\STDEmpresa;



use ZipArchive;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait ReporteTraits
{
	

	public function rp_identificador_fijo(){
        $identificador_fijo  		    = 		'LE';
        return $identificador_fijo;
    }


	public function rp_ingresos_mensuales($anio,$periodo_inicio_id,$periodo_fin_id,$moneda_id,$cuenta_inicio_id,$cuenta_fin_id){



		$periodoinicio   		=   CONPeriodo::where('COD_PERIODO','=',$periodo_inicio_id)->first();
		$periodofin   			=   CONPeriodo::where('COD_PERIODO','=',$periodo_fin_id)->first();

		$periodo_array 			=   CONPeriodo::where('COD_ANIO','=',$anio)
									->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('CON.PERIODO.COD_MES','>=',$periodoinicio->COD_MES)
	    							->where('CON.PERIODO.COD_MES','<=',$periodofin->COD_MES)
									->pluck('COD_PERIODO')->toArray();

		$cuentainicio   		=   WEBCuentaContable::where('id','=',$cuenta_inicio_id)->first();
		$cuentafin   			=   WEBCuentaContable::where('id','=',$cuenta_fin_id)->first();

		$cuentas_array 			=   WEBCuentaContable::where('anio','=',$anio)
									->where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('nro_cuenta','>=',strval($cuentainicio->nro_cuenta))
	    							->where('nro_cuenta','<',strval($cuentafin->nro_cuenta+1))
									->pluck('id')->toArray();


	    $suma 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    							->join('WEB.cuentacontables', 'WEB.cuentacontables.id', '=', 'WEB.asientomovimientos.COD_CUENTA_CONTABLE')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array)
	    							->whereIn('WEB.asientomovimientos.COD_CUENTA_CONTABLE', $cuentas_array)
	    							->selectRaw('WEB.asientomovimientos.*,
	    										 WEB.asientos.*,
	    										 SUBSTRING(nro_cuenta, 1, 2) as nro_cuenta_2,
	    										 SUBSTRING(nro_cuenta, 1, 3) as nro_cuenta_3,
	    										 SUBSTRING(nro_cuenta, 1, 4) as nro_cuenta_4,
	    										 SUBSTRING(nro_cuenta, 1, 5) as nro_cuenta_5,
	    										 SUBSTRING(nro_cuenta, 1, 6) as nro_cuenta_6')
	    							->get();


		$lista_cuentas 			=   WEBCuentaContable::where('anio','=',$anio)
									->where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('nro_cuenta','>=',strval($cuentainicio->nro_cuenta))
	    							->where('nro_cuenta','<',strval($cuentafin->nro_cuenta+1))
	    							->select('nro_cuenta','nombre','orden','nivel')
	    							->groupby('nro_cuenta')
	    							->groupby('nivel')
	    							->groupby('nombre')
	    							->groupby('orden')
									->orderby('orden','asc')
									->get();

		$lista_periodo 			=   CONPeriodo::where('COD_ANIO','=',$anio)
									->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('CON.PERIODO.COD_MES','>=',$periodoinicio->COD_MES)
	    							->where('CON.PERIODO.COD_MES','<=',$periodofin->COD_MES)
									->get();

		$array_tabla_ingresos 	=	array();


	    foreach($lista_cuentas as $index => $item){
	    	$array_nuevo_ingreso 	=	array();
			$contador = 0;
			$cantidad = 0;

    		$array_nuevo_ingreso = $array_nuevo_ingreso + array(
							"item".$contador 			=> $item->nro_cuenta,
						);
    		$contador = $contador + 1;
    		$cantidad = $cantidad + 1;

    		$array_nuevo_ingreso = $array_nuevo_ingreso + array(
							"item".$contador 			=> $item->nombre,
						);
    		$contador = $contador + 1;
    		$cantidad = $cantidad + 1;

    		$totales  =	0;


	    	foreach($lista_periodo as $indexp => $itemp){

				$total_h 				=	$suma->where('COD_PERIODO','=',$itemp->COD_PERIODO)
											->where('nro_cuenta_'.$item->nivel,'=',$item->nro_cuenta)
											->sum('CAN_HABER_MN');
				$total_d 				=	$suma->where('COD_PERIODO','=',$itemp->COD_PERIODO)
											->where('nro_cuenta_'.$item->nivel,'=',$item->nro_cuenta)
											->sum('CAN_DEBE_MN');
	    		$total_mes 				=  	$total_d + $total_h;
    			$totales  				=	$totales + $total_mes;

	    		$total_mes = number_format($total_mes, 2, '.', ',');

	    		$array_nuevo_ingreso = $array_nuevo_ingreso + array(
								"item".$contador 			=> $total_mes,
							);

	    		$contador = $contador + 1;
	    		$cantidad = $cantidad + 1;

	    	}

	    	if($item->nivel<6){
	    		$bg = 'bggris';
	    	}else{
	   			$bg = 'bgblanco';
	    	}
	    	if($item->nivel==2){
	    		$negrita = 'negrita';
	    	}else{
	   			$negrita = '';
	    	}


	    	$totales = number_format($totales, 2, '.', ',');

	    	$array_nuevo_ingreso = $array_nuevo_ingreso + array("bg" => $bg);
	    	$array_nuevo_ingreso = $array_nuevo_ingreso + array("negrita" => $negrita);
	    	$array_nuevo_ingreso = $array_nuevo_ingreso + array("totales" => $totales);
	    	$array_nuevo_ingreso = $array_nuevo_ingreso + array("cantidadarray" => $cantidad);
			array_push($array_tabla_ingresos,$array_nuevo_ingreso);

	    }


	    //eliminar valores
		$r = array_filter( $array_tabla_ingresos, function( $e ) {
		        return $e['totales'] > 0;
		});

	    return $r;

    }



	public function rp_crear_nombre_diario($anio,$mes){

		$identificador 					=       $this->rp_identificador_fijo();
		$ruc 							=       Session::get('empresas_meta')->NRO_DOCUMENTO;
		$dd 							= 		'00';
		$identificador_libro 			= 		'000501';
		$cc 							= 		'00';
		$identificador_operaciones 		= 		'1';
		$i 								= 		'1';
		$m 								= 		'1';
		$g 								= 		'1';
        $nombre_archivo  		    	= 		$identificador.$ruc.$anio.$mes.$dd.$identificador_libro.$cc.$identificador_operaciones.$i.$m.$g;

        return $nombre_archivo;
    }

	public function rp_crear_nombre_mayor($anio,$mes){

		$identificador 					=       $this->rp_identificador_fijo();
		$ruc 							=       Session::get('empresas_meta')->NRO_DOCUMENTO;
		$dd 							= 		'00';
		$identificador_libro 			= 		'000601';
		$cc 							= 		'00';
		$identificador_operaciones 		= 		'1';
		$i 								= 		'1';
		$m 								= 		'1';
		$g 								= 		'1';
        $nombre_archivo  		    	= 		$identificador.$ruc.$anio.$mes.$dd.$identificador_libro.$cc.$identificador_operaciones.$i.$m.$g;

        return $nombre_archivo;
    }

	public function rp_crear_nombre_plan_contable($anio,$mes){

		$identificador 					=       $this->rp_identificador_fijo();
		$ruc 							=       Session::get('empresas_meta')->NRO_DOCUMENTO;
		$dd 							= 		'00';
		$identificador_libro 			= 		'000503';
		$cc 							= 		'00';
		$identificador_operaciones 		= 		'1';
		$i 								= 		'1';
		$m 								= 		'1';
		$g 								= 		'1';
        $nombre_archivo  		    	= 		$identificador.$ruc.$anio.$mes.$dd.$identificador_libro.$cc.$identificador_operaciones.$i.$m.$g;

        return $nombre_archivo;
    }


	public function rp_archivo_ple_diario($anio,$mes,$listaasiento,$nombre,$path){

	    if (file_exists($path)) {
	        unlink("storage/diario/ple/".$nombre);
	    } 
		$datos = fopen("storage/diario/ple/".$nombre, "a");
		//llenado de datalle
		$array_detalle_asiento 		=	array();


	    foreach($listaasiento as $index => $item){

	    	//$documento 				= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$item->TXT_REFERENCIA)->first();
	   		$empresa 				= 	STDEmpresa::where('COD_EMPR','=',$item->COD_EMPR_CLI)->first();
	   		$categoria 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_DOCUMENTO)->first();
	   		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$item->COD_PERIODO)->first();
	   		$tipoasiento 			= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_ASIENTO)->first();

	   		$indicador_afecto 		=   $item->IND_AFECTO;
	    	$nro_asiento 			=   $item->NRO_ASIENTO;
	    	$mes_01 				= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT); 
	    	$periodo_01  			= 	$periodo->COD_ANIO.$mes_01."00";

	    	$correlativo_02  		= 	$tipoasiento->TXT_ABREVIATURA.'-'.$nro_asiento;
	    	$codigo_03  			= 	'M00'.$item->NRO_LINEA;

			$cuenta_04 				= 	$item->TXT_CUENTA_CONTABLE;
			$codigo_unidad_05 		= 	'';
			$centro_costo_06 		= 	'';

			$moneda_07 				= 	$item->moneda->CODIGO_SUNAT;

			if(count($empresa)>0){
				$identidad_cliente_08  	= 	intval($empresa->tipo_documento->CODIGO_SUNAT);
				$documento_cliente_09  	= 	$empresa->NRO_DOCUMENTO;
			}else{
				$identidad_cliente_08  	= 	'';
				$documento_cliente_09  	= 	'';
			}


			if(count($categoria)>0){
				$tipo_documento_10  	= 	$categoria->CODIGO_SUNAT;
			}else{
				$tipo_documento_10  	= 	'';
			}

			
			$nro_serie_11  			= 	$item->NRO_SERIE;
			$nro_correlativo_12  	= 	$item->NRO_DOC;

	    	$fecha_emision_13  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');
			$fecha_vencimiento_14  	= 	date_format(date_create($item->FEC_VENCIMIENTO), 'd/m/Y');
	    	$fecha_emision_15  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');

			$glosa_16 				= 	$item->TXT_GLOSA;
			$referencia_glosa_17 	= 	'Documento referencia N° '.$item->NRO_SERIE_REF.' - '.$item->NRO_DOC_REF;

			$debe_18 				=	0;
			$haber_19 				=	0;

			if($item->COD_CATEGORIA_MONEDA == 'MON0000000000001'){
				$debe_18 				=	number_format($item->CAN_DEBE_MN, 2, '.', '');
				$haber_19 				=	number_format($item->CAN_HABER_MN, 2, '.', '');
			}else{
				$debe_18 				=	number_format($item->CAN_DEBE_ME, 2, '.', '');
				$haber_19 				=	number_format($item->CAN_HABER_ME, 2, '.', '');
			}

			$de_01 						=	'140100';//casosfalta
			if($item->COD_CATEGORIA_TIPO_ASIENTO == 'TAS0000000000003' or $item->COD_CATEGORIA_TIPO_ASIENTO == 'TAS0000000000004'){
				$de_01 						=	'080100';
			}


			$datoestructurado_20 		= 	$de_01.'&'.$periodo_01.'&'.$correlativo_02.'&'.$codigo_03;

			$campo_21 				= 	"1";
			$campo_22 				= 	"";


	      	fwrite($datos, $periodo_01."|");
	      	fwrite($datos, $correlativo_02."|");
	      	fwrite($datos, $codigo_03."|");
	      	fwrite($datos, $cuenta_04."|");
	      	fwrite($datos, $codigo_unidad_05."|");
	      	fwrite($datos, $centro_costo_06."|");
	      	fwrite($datos, $moneda_07."|");
	      	fwrite($datos, $identidad_cliente_08."|");
	      	fwrite($datos, $documento_cliente_09."|");
	      	fwrite($datos, $tipo_documento_10."|");
	      	fwrite($datos, $nro_serie_11."|");
	      	fwrite($datos, $nro_correlativo_12."|");

	      	fwrite($datos, $fecha_emision_13."|");
	      	fwrite($datos, $fecha_vencimiento_14."|");
	      	fwrite($datos, $fecha_emision_15."|");

	      	fwrite($datos, $glosa_16."|");
	      	fwrite($datos, $referencia_glosa_17."|");
	      	fwrite($datos, $debe_18."|");
	      	fwrite($datos, $haber_19."|");
	      	fwrite($datos, $datoestructurado_20."|");
	      	fwrite($datos, $campo_21."|");


	      	fwrite($datos, $campo_22.PHP_EOL);

	    	$array_nuevo_asiento 	=	array();
			$array_nuevo_asiento    =	array(
				"periodo_01" 					=> $periodo_01,
				"correlativo_02" 				=> $correlativo_02,
				"codigo_03" 					=> $codigo_03,
				"cuenta_04" 					=> $cuenta_04,
				"codigo_unidad_05" 				=> $codigo_unidad_05,
				"centro_costo_06" 				=> $centro_costo_06,
				"moneda_07" 					=> $moneda_07,
				"identidad_cliente_08" 			=> $identidad_cliente_08,
				"documento_cliente_09" 			=> $documento_cliente_09,
				"tipo_documento_10" 			=> $tipo_documento_10,
				"nro_serie_11" 					=> $nro_serie_11,
				"nro_correlativo_12" 			=> $nro_correlativo_12,

				"fecha_emision_13" 				=> $fecha_emision_13,
				"fecha_vencimiento_14" 			=> $fecha_vencimiento_14,
				"fecha_emision_15" 				=> $fecha_emision_15,

				"glosa_16" 						=> $glosa_16,
				"referencia_glosa_17" 			=> $referencia_glosa_17,
				"debe_18" 						=> $debe_18,
				"haber_19" 						=> $haber_19,
				"datoestructurado_20" 			=> $datoestructurado_20,
				"campo_21" 						=> $campo_21,

	            "campo_22" 						=> $campo_22
			);

			array_push($array_detalle_asiento,$array_nuevo_asiento);
			

	    }

	    fclose($datos);

	    //dd("dd");
	    return $array_detalle_asiento;

    }

	public function rp_archivo_ple_mayor($anio,$mes,$listaasiento,$nombre,$path){

	    if (file_exists($path)) {
	        unlink("storage/mayor/ple/".$nombre);
	    } 
		$datos = fopen("storage/mayor/ple/".$nombre, "a");
		//llenado de datalle
		$array_detalle_asiento 		=	array();


	    foreach($listaasiento as $index => $item){

	    	//$documento 				= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$item->TXT_REFERENCIA)->first();
	   		$empresa 				= 	STDEmpresa::where('COD_EMPR','=',$item->COD_EMPR_CLI)->first();
	   		$categoria 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_DOCUMENTO)->first();
	   		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$item->COD_PERIODO)->first();
	   		$tipoasiento 			= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_ASIENTO)->first();

	   		$indicador_afecto 		=   $item->IND_AFECTO;
	    	$nro_asiento 			=   $item->NRO_ASIENTO;
	    	$mes_01 				= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT); 
	    	$periodo_01  			= 	$periodo->COD_ANIO.$mes_01."00";

	    	$correlativo_02  		= 	$tipoasiento->TXT_ABREVIATURA.'-'.$nro_asiento;
	    	$codigo_03  			= 	'M00'.$item->NRO_LINEA;

			$cuenta_04 				= 	$item->TXT_CUENTA_CONTABLE;
			$codigo_unidad_05 		= 	'';
			$centro_costo_06 		= 	'';

			$moneda_07 				= 	$item->moneda->CODIGO_SUNAT;

			if(count($empresa)>0){
				$identidad_cliente_08  	= 	intval($empresa->tipo_documento->CODIGO_SUNAT);
				$documento_cliente_09  	= 	$empresa->NRO_DOCUMENTO;
			}else{
				$identidad_cliente_08  	= 	'';
				$documento_cliente_09  	= 	'';
			}


			if(count($categoria)>0){
				$tipo_documento_10  	= 	$categoria->CODIGO_SUNAT;
			}else{
				$tipo_documento_10  	= 	'';
			}

			
			$nro_serie_11  			= 	$item->NRO_SERIE;
			$nro_correlativo_12  	= 	$item->NRO_DOC;

	    	$fecha_emision_13  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');
			$fecha_vencimiento_14  	= 	date_format(date_create($item->FEC_VENCIMIENTO), 'd/m/Y');
	    	$fecha_emision_15  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');

			$glosa_16 				= 	$item->TXT_GLOSA;
			$referencia_glosa_17 	= 	'Documento referencia N° '.$item->NRO_SERIE_REF.' - '.$item->NRO_DOC_REF;

			$debe_18 				=	0;
			$haber_19 				=	0;

			if($item->COD_CATEGORIA_MONEDA == 'MON0000000000001'){
				$debe_18 				=	number_format($item->CAN_DEBE_MN, 2, '.', '');
				$haber_19 				=	number_format($item->CAN_HABER_MN, 2, '.', '');
			}else{
				$debe_18 				=	number_format($item->CAN_DEBE_ME, 2, '.', '');
				$haber_19 				=	number_format($item->CAN_HABER_ME, 2, '.', '');
			}

			$de_01 						=	'140100';//casosfalta
			if($item->COD_CATEGORIA_TIPO_ASIENTO == 'TAS0000000000003' or $item->COD_CATEGORIA_TIPO_ASIENTO == 'TAS0000000000004'){
				$de_01 						=	'080100';
			}


			$datoestructurado_20 		= 	$de_01.'&'.$periodo_01.'&'.$correlativo_02.'&'.$codigo_03;

			$campo_21 				= 	"1";
			$campo_22 				= 	"";


	      	fwrite($datos, $periodo_01."|");
	      	fwrite($datos, $correlativo_02."|");
	      	fwrite($datos, $codigo_03."|");
	      	fwrite($datos, $cuenta_04."|");
	      	fwrite($datos, $codigo_unidad_05."|");
	      	fwrite($datos, $centro_costo_06."|");
	      	fwrite($datos, $moneda_07."|");
	      	fwrite($datos, $identidad_cliente_08."|");
	      	fwrite($datos, $documento_cliente_09."|");
	      	fwrite($datos, $tipo_documento_10."|");
	      	fwrite($datos, $nro_serie_11."|");
	      	fwrite($datos, $nro_correlativo_12."|");

	      	fwrite($datos, $fecha_emision_13."|");
	      	fwrite($datos, $fecha_vencimiento_14."|");
	      	fwrite($datos, $fecha_emision_15."|");

	      	fwrite($datos, $glosa_16."|");
	      	fwrite($datos, $referencia_glosa_17."|");
	      	fwrite($datos, $debe_18."|");
	      	fwrite($datos, $haber_19."|");
	      	fwrite($datos, $datoestructurado_20."|");
	      	fwrite($datos, $campo_21."|");


	      	fwrite($datos, $campo_22.PHP_EOL);

	    	$array_nuevo_asiento 	=	array();
			$array_nuevo_asiento    =	array(
				"periodo_01" 					=> $periodo_01,
				"correlativo_02" 				=> $correlativo_02,
				"codigo_03" 					=> $codigo_03,
				"cuenta_04" 					=> $cuenta_04,
				"codigo_unidad_05" 				=> $codigo_unidad_05,
				"centro_costo_06" 				=> $centro_costo_06,
				"moneda_07" 					=> $moneda_07,
				"identidad_cliente_08" 			=> $identidad_cliente_08,
				"documento_cliente_09" 			=> $documento_cliente_09,
				"tipo_documento_10" 			=> $tipo_documento_10,
				"nro_serie_11" 					=> $nro_serie_11,
				"nro_correlativo_12" 			=> $nro_correlativo_12,

				"fecha_emision_13" 				=> $fecha_emision_13,
				"fecha_vencimiento_14" 			=> $fecha_vencimiento_14,
				"fecha_emision_15" 				=> $fecha_emision_15,

				"glosa_16" 						=> $glosa_16,
				"referencia_glosa_17" 			=> $referencia_glosa_17,
				"debe_18" 						=> $debe_18,
				"haber_19" 						=> $haber_19,
				"datoestructurado_20" 			=> $datoestructurado_20,
				"campo_21" 						=> $campo_21,

	            "campo_22" 						=> $campo_22
			);

			array_push($array_detalle_asiento,$array_nuevo_asiento);
			

	    }

	    fclose($datos);

	    //dd("dd");
	    return $array_detalle_asiento;

    }



	public function rp_archivo_ple_plan_contable($anio,$mes,$listaasiento,$nombre,$path,$periodo){

	    if (file_exists($path)) {
	        unlink("storage/plancontable/ple/".$nombre);
	    } 
		$datos = fopen("storage/plancontable/ple/".$nombre, "a");
		//llenado de datalle
		$array_detalle_asiento 		=	array();


	    foreach($listaasiento as $index => $item){

	    	$mes_01 				= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT); 
	    	$periodo_01  			= 	$periodo->COD_ANIO.$mes_01."00";
			$cuenta_02 				= 	$item->nro_cuenta;
			$nombre_cuenta_03 		= 	$item->nombre;
			$valor_04 				= 	'01';
			$valor_05 				= 	'-';
			$valor_06 				= 	'';
			$valor_07 				= 	'';
			$valor_08 				= 	'1';
			$valor_09 				= 	'';


	      	fwrite($datos, $periodo_01."|");
	      	fwrite($datos, $cuenta_02."|");
	      	fwrite($datos, $nombre_cuenta_03."|");
	      	fwrite($datos, $valor_04."|");
	      	fwrite($datos, $valor_05."|");
	      	fwrite($datos, $valor_06."|");
	      	fwrite($datos, $valor_07."|");
	      	fwrite($datos, $valor_08."|");
	      	fwrite($datos, $valor_09.PHP_EOL);

	    	$array_nuevo_asiento 	=	array();
			$array_nuevo_asiento    =	array(
				"periodo_01" 					=> $periodo_01,
				"cuenta_02" 					=> $cuenta_02,
				"nombre_cuenta_03" 				=> $nombre_cuenta_03,
				"valor_04" 						=> $valor_04,
				"valor_05" 						=> $valor_05,
				"valor_06" 						=> $valor_06,
				"valor_07" 						=> $valor_07,
				"valor_08" 						=> $valor_08,
				"valor_09" 						=> $valor_09
			);

			array_push($array_detalle_asiento,$array_nuevo_asiento);
			

	    }

	    fclose($datos);

	    //dd("dd");
	    return $array_detalle_asiento;

    }




}