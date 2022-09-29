<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBProductoEmpresa;
use App\Modelos\ALMProducto;
use App\Modelos\CMPTipoCambio;



use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

use App\Traits\GeneralesTraits;
use App\Traits\PlanContableTraits;
use App\Traits\ConfiguracionProductoTraits;
use App\Traits\MigrarVentaTraits;

class ConfiguracioTipoCambioController extends Controller
{

	use GeneralesTraits;
	use PlanContableTraits;
	use ConfiguracionProductoTraits;
	use MigrarVentaTraits;


	public function actionAjaxGuardarTipoCambio(Request $request)
	{

		$data_tipo_cambio 		= 	$request['data_tipo_cambio'];
		$anio 					= 	$request['anio'];
		$mes 					= 	$request['mes'];

		foreach($data_tipo_cambio as $key => $row) {

			$data_fecha_tipo_cambio 	=  	date_format(date_create($row['fecha_tipo_cambio']), 'Y-m-d');
			$fechaComoEntero = strtotime($data_fecha_tipo_cambio);
			//$data_fecha_tipo_cambio 	=  	str_replace("-","",$data_fecha_tipo_cambio);
			$can_compra_sbs 			=  	(float)$row['can_compra_sbs'];
			$can_venta_sbs 				=  	(float)$row['can_venta_sbs'];


			$dia 						=  	str_replace('0', '', date("d", $fechaComoEntero));
			$mes 						=  	str_replace('0', '', date("m", $fechaComoEntero));
			$anio 						=  	date("Y", $fechaComoEntero);

            DB::connection('sqlsrv')->table('CMP.TIPO_CAMBIO')->whereRaw('day(FEC_CAMBIO) = ?', [$dia])
											->whereRaw('MONTH(FEC_CAMBIO) = ?', [$mes])
											->whereRaw('YEAR(FEC_CAMBIO) = ?', [$anio])
            ->update(['CAN_COMPRA_SBS' => $can_compra_sbs,'CAN_VENTA_SBS' => $can_venta_sbs]);

			// $tipocambio 				=   CMPTipoCambio::whereRaw('day(FEC_CAMBIO) = ?', [$dia])
			// 								->whereRaw('MONTH(FEC_CAMBIO) = ?', [$mes])
			// 								->whereRaw('YEAR(FEC_CAMBIO) = ?', [$anio])
			// 								->first();

			// $tipocambio->CAN_COMPRA_SBS = 	$can_compra_sbs;
			// $tipocambio->CAN_VENTA_SBS 	= 	$can_venta_sbs;
			// $tipocambio->save();
			// dd($tipocambio);


	    } 


		$listatipocambio 		=   CMPTipoCambio::where('NRO_ANIO','=',$anio)
									->where('NRO_MES','=',$mes)
									->get();
		$arraymeses  			= 	$this->gn_array_meses();

		$funcion 				= 	$this;

		return View::make('tipocambio/ajax/alistatipocambio',
						 [
						 	'listatipocambio' 	=> $listatipocambio,
						 	'arraymeses' 		=> $arraymeses,
						 	'funcion' 			=> $funcion,
						 	'ajax'   		    => true,
						 ]);

	}



	public function actionListarTipoCambio($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Configuracion de tipo de cambio');

        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$anio  					=   $this->anio;
		$mes  					=   $this->mes;
		if(Session::has('anio_pc')){$anio = Session::get('anio_pc');}
		$combo_mes  			= 	$this->gn_generacion_combo_meses_array('Seleccione mes', '');

		$listatipocambio 		=   CMPTipoCambio::where('NRO_ANIO','=',$anio)
									->where('NRO_MES','=',$mes)
									->get();

		$arraymeses  			= 	$this->gn_array_meses();


		$funcion 				= 	$this;

		return View::make('tipocambio/listatipocambio',
						 [
						 	'listatipocambio' 		=> $listatipocambio,
						 	'combo_anio_pc'	 		=> $combo_anio_pc,
						 	'combo_mes'	 			=> $combo_mes,
						 	'arraymeses'	 		=> $arraymeses,
						 	'anio'	 				=> $anio,	
						 	'mes'	 				=> $mes,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxListarTipoCambio(Request $request)
	{
		$anio 					=   $request['anio'];
		$mes 					=   $request['mes'];
		$idopcion 				=   $request['idopcion'];
		$listatipocambio 		=   CMPTipoCambio::where('NRO_ANIO','=',$anio)
									->where('NRO_MES','=',$mes)
									->get();
		$arraymeses  			= 	$this->gn_array_meses();

		$funcion 				= 	$this;

		return View::make('tipocambio/ajax/alistatipocambio',
						 [
						 	'listatipocambio' 		=> $listatipocambio,
						 	'arraymeses' 			=> $arraymeses,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}



}
