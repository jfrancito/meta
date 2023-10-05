<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBMovimientoEfectivo;
use App\Modelos\CONAsiento;
use App\Modelos\CONPeriodo;
use App\Modelos\WEBAsientoModelo;



use View;
use Session;
use Hashids;
use PDF;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;
use App\Traits\PagoCobroEfectivo;
use App\Traits\CajaBancoTraits;

class PagoCobroEfectivoController extends Controller
{

	use CajaBancoTraits;
	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use PagoCobroEfectivo;

	public function actionListarMovimiento($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Lista de movimientos');
	    $empresa_id 				= 	Session::get('empresas_meta')->COD_EMPR;


		if(Session::has('periodo_id_confirmar')){
			$sel_periodo 			=	Session::get('periodo_id_confirmar');
			$sel_caja 				=	Session::get('caja_id_confirmar');
		}else{
			$sel_periodo 			=	'';
			$sel_caja 				=	'';
		}

		$referencia 			=	'';
		// $array_asoc  			=   $this->movilidad_array_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $sel_periodo, $empresa_id,$referencia);
        $listamovimientos     	= 	$this->pce_lista_movimiento($sel_periodo,$sel_caja);

	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$combo_caja  			= 	$this->gn_combo_caja_banco_efectivo('','Seleccione caja');
		$funcion 				= 	$this;
		
		return View::make('cajaybanco/listamovientoefectivo',
						 [
						 	'listamovimientos' 		=> $listamovimientos,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'combo_caja'			=> $combo_caja,
						 	'anio'					=> $anio,
						 	'sel_caja'	 			=> $sel_caja,
						 	'sel_periodo'	 		=> $sel_periodo,
						 	'periodo_id'			=> $sel_periodo,
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}

	public function actionAjaxModalConfiguracionMovimientoEfectivo(Request $request)
	{
		

		$cod_operacion_caja 		=   $request['cod_operacion_caja'];
    	$idopcion 					=   $request['idopcion'];
		$empresa_id 				=	Session::get('empresas_meta')->COD_EMPR;
		$idopcion 					=   $request['idopcion'];
	    $funcion 					= 	$this;
		$movimiento 				= 	$this->cb_operacion_caja_movimientos_efectivo($cod_operacion_caja);
	    $listadetallemovimientos 	= 	$this->cb_detalle_operacion_caja_movimientos_efectivo($cod_operacion_caja);
	    $glosa 						=	$this->cb_glosa_de_asiento($listadetallemovimientos);
	    $nro_referencia   			=	$movimiento->COD_CATEGORIA_ACTIVIDAD_NEGOCIO;
	    $asiento 					=	CONAsiento::where('COD_ASIENTO','=',$movimiento->COD_ASIENTO)->first();
	    $periodo 					=	CONPeriodo::where('COD_PERIODO','=',$asiento->COD_PERIODO)->first();
	    $anio 						=	$periodo->COD_ANIO;
	    $nro_operacion 				=	$movimiento->COD_OPERACION_CAJA;

	    $tipo_asiento 				= 	$this->cb_buscar_tipo_asiento($movimiento);
	    $pago_cobro 				= 	$this->cb_cod_pago_cobro_efectivo($nro_referencia);
	    $text_pago_cobro 			= 	$this->cb_txt_pago_cobro($pago_cobro);


	    $count_rel 					= 	$this->am_empresa_relacionada($movimiento,$listadetallemovimientos);
	   	$count_ter 					= 	$this->am_empresa_tercero(count($listadetallemovimientos),$count_rel);
	    $empresa_rel_ter 			= 	$this->am_empresa_relacionada_tercero($count_rel,$count_ter);
	    $existe_rel_ter 			= 	$this->am_empresa_existe_relacionada_tercero($count_rel,$count_ter);
	    $abreviatura_asiento_modelo = 	$this->am_abreviatura_asiento_modelo($pago_cobro,$empresa_rel_ter,$movimiento);
	    $buscar_modelo_asiento 		= 	$this->am_buscar_asiento_modelo_efectivo($movimiento,$tipo_asiento,$anio,$pago_cobro,$empresa_rel_ter);


	    $asiento_array 				= 	$this->am_asiento_efectivo_array($movimiento,$tipo_asiento,$anio,$pago_cobro,$empresa_rel_ter,$buscar_modelo_asiento,$nro_operacion,$nro_referencia,$glosa,$text_pago_cobro,$periodo);


	    $detalle_asiento_array 		= 	$this->am_asiento_detalle_efectivo_array($movimiento,$tipo_asiento,$anio,$pago_cobro,$empresa_rel_ter,$buscar_modelo_asiento,$nro_operacion,$nro_referencia,$listadetallemovimientos,$funcion,$existe_rel_ter,$abreviatura_asiento_modelo);


	    $asiento_array 				= 	$this->am_totales_asiento_array($movimiento,$tipo_asiento,$anio,$pago_cobro,$empresa_rel_ter,$buscar_modelo_asiento,$nro_operacion,$nro_referencia,$asiento_array,$detalle_asiento_array);


	    $asientomodelo 				= 	array();
	    if($buscar_modelo_asiento['encontro'] == '1'){
	    	$asientomodelo 			= 	WEBAsientoModelo::where('id','=',$buscar_modelo_asiento['msg'])->first();
	    }


		return View::make('cajaybanco/modal/ajax/alistadetallemovimientosefectivo',
						 [		 	
						 	'listadetallemovimientos' 		=> $listadetallemovimientos,
						 	'movimiento' 					=> $movimiento,
						 	'cod_operacion' 				=> $nro_operacion,
						 	'cuenta_referencia' 			=> $nro_referencia,

						 	'tipo_asiento' 					=> $tipo_asiento,
						 	'buscar_modelo_asiento' 		=> $buscar_modelo_asiento,
						 	'asientomodelo' 				=> $asientomodelo,
						 	'asiento_array' 				=> $asiento_array,
						 	'detalle_asiento_array' 		=> $detalle_asiento_array,
						 	'idopcion' 						=> $idopcion,
						 	'funcion' 						=> $funcion,
						 	'ajax' 							=> true,						 	
						 ]);

	}




	public function actionAjaxRegistroMovimientoEfectivo(Request $request)
	{


		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$caja_id 				=   $request['caja_id'];

		$empresa_id 			=   Session::get('empresas_meta')->COD_EMPR;
		$referencia 			=	'';
		//$array_asoc  			=   $this->movilidad_array_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $periodo_id, $empresa_id,$referencia);
        $listamovimientos     	= 	$this->pce_lista_movimiento($periodo_id,$caja_id);
		$funcion 				= 	$this;

		
		return View::make('cajaybanco/ajax/alistamovientoefectivo',
						 [
						 	'listamovimientos'		=> $listamovimientos,
						 	'funcion'				=> $funcion,
						 	'periodo_id'			=> $periodo_id,
						 	'ajax' 					=> true,						 	
						 ]);
	}






}