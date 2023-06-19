<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;


use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;



use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
class RegistroDiarioController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;

	public function actionAsientoContableExcelXAsiento($cod_asiento,Request $request)
	{


		set_time_limit(0);


	    $listaasiento 			= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->where('WEB.asientos.COD_ASIENTO','=',$cod_asiento)
	    							->get();					

	    $funcion 				= 	$this;
		$titulo 				=   'ASIENTO-CONTABLE-'.Session::get('empresas_meta')->NOM_EMPR;

	    Excel::create($titulo, function($excel) use ($funcion,$listaasiento) {
	        $excel->sheet('asiento', function($sheet) use ($funcion,$listaasiento) {
	            $sheet->loadView('registrodiario/excel/listaasientocontable')->with('listaasiento',$listaasiento)
	            														->with('funcion',$funcion);         
	        });
	    })->export('xls');



	}



	public function actionAjaxDescargarAsientoContable(Request $request)
	{


		set_time_limit(0);

		$anio 					=   $request['anio'];
		$tipo_asiento_id 		=   $request['tipo_asiento_id'];
		$periodo_id 			=   $request['periodo_id'];
		$idopcion 				=   $request['idopcion'];

	    $listaasiento 			= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
	    							->OrdFecha($tipo_asiento_id)
	    							->get();					

	    $funcion 				= 	$this;
		$titulo 				=   'ASIENTO-CONTABLE-'.Session::get('empresas_meta')->NOM_EMPR;



	    Excel::create($titulo, function($excel) use ($funcion,$listaasiento) {
	        $excel->sheet('asiento', function($sheet) use ($funcion,$listaasiento) {
	            $sheet->loadView('registrodiario/excel/listaasientocontable')->with('listaasiento',$listaasiento)
	            														->with('funcion',$funcion);         
	        });
	    })->export('xls');



	}


	public function actionListarRegistroDiario($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Comprobantes Diario');
	    $sel_tipo_asiento 		=	'';
	    $sel_periodo 			=	'';
	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_tipo_asiento 	= 	$this->gn_generacion_combo_categoria('TIPO_ASIENTO','Seleccione tipo asiento','');
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
	    $listaasiento 			= 	array();
		$funcion 				= 	$this;
		
		return View::make('registrodiario/listaregistrodiario',
						 [
						 	'listaasiento' 			=> $listaasiento,
						 	'combo_tipo_asiento'	=> $combo_tipo_asiento,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_tipo_asiento'	 	=> $sel_tipo_asiento,
						 	'sel_periodo'	 		=> $sel_periodo,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxComboPeriodoAnioEmpresa(Request $request)
	{


		$anio 					=   $request['anio'];
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
	    $sel_periodo 			=	'';
		$funcion 				= 	$this;
		
		return View::make('general/combo/cperiodo',
						 [

						 	'combo_periodo'			=> $combo_periodo,
						 	'sel_periodo'	 		=> $sel_periodo,					 	
						 	'ajax' 					=> true,						 	
						 ]);
	}
	public function actionAjaxComboPeriodoAnioEmpresaGC(Request $request)
	{


		$anio 					=   $request['anio'];
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
	    $sel_periodo 			=	'';
		$funcion 				= 	$this;
		
		return View::make('general/combo/cperiodogc',
						 [

						 	'combo_periodo'			=> $combo_periodo,
						 	'sel_periodo'	 		=> $sel_periodo,					 	
						 	'ajax' 					=> true,						 	
						 ]);
	}



	public function actionAjaxRegistroDiario(Request $request)
	{


		$tipo_asiento_id 		=   $request['tipo_asiento_id'];
		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];

	    $listaasiento 			= 	WEBAsiento::where('COD_PERIODO','=',$periodo_id)
	    							->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
	    							->OrdFecha($tipo_asiento_id)
	    							->get();
	    //dd($listaasiento);
		$funcion 				= 	$this;
		
		return View::make('registrodiario/ajax/alistaregistrodiario',
						 [

						 	'listaasiento'			=> $listaasiento,
						 	'funcion'				=> $funcion,			 	
						 	'ajax' 					=> true,						 	
						 ]);
	}

	public function actionAjaxModalDetalleAsiento(Request $request)
	{


		$asiento_id 			=   $request['asiento_id'];
		$idopcion 				=   $request['idopcion'];
	    $asiento 				= 	WEBAsiento::where('COD_ASIENTO','=',$asiento_id)->first();
	    $listaasientomovimiento = 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento_id)->orderBy('NRO_LINEA', 'asc')->get();
		$funcion 				= 	$this;
		
		return View::make('registrodiario/modal/ajax/mdetalleasiento',
						 [

						 	'asiento'					=> $asiento,
						 	'listaasientomovimiento'	=> $listaasientomovimiento,				 	
						 	'ajax' 						=> true,						 	
						 ]);
	}






}
