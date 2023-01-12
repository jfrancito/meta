<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;


use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\CMPDocumentoCtble;
use App\Modelos\CONPeriodo;

use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;
use App\Traits\ArchivoTraits;
use App\Traits\ReporteTraits;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use ZipArchive;
use Maatwebsite\Excel\Facades\Excel;


class ReporteController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use ArchivoTraits;
	use ReporteTraits;



	public function actionGestionResultadoFuncion($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Estado de resultado por funcion');
	    $sel_periodo 			=	'';


	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);

	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$funcion 				= 	$this;
		$lista_situacion_fin    =   array();


		return View::make('reporte/resultadofuncion',
						 [
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_periodo'	 		=> $sel_periodo,

						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'lista_situacion_fin' 	=> $lista_situacion_fin,						 	
						 ]);
	}


	public function actionAjaxBuscarResultadoFuncion(Request $request)
	{

		$anio 					=   $request['anio'];
		$periodo_inicio_id 		=   $request['periodo_inicio_id'];
		$periodo_fin_id 		=   $request['periodo_fin_id'];
		$idopcion 				=   $request['idopcion'];

		$periodoinicio   		=   CONPeriodo::where('COD_PERIODO','=',$periodo_inicio_id)->first();
		$periodofin   			=   CONPeriodo::where('COD_PERIODO','=',$periodo_fin_id)->first();

		$periodo_array 			=   CONPeriodo::where('COD_ANIO','=',$anio)
									->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('CON.PERIODO.COD_MES','>=',$periodoinicio->COD_MES)
	    							->where('CON.PERIODO.COD_MES','<=',$periodofin->COD_MES)
									->pluck('COD_PERIODO')->toArray();


	    $suma 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array);


		$array_cuenta 			=	$suma->select('TXT_CUENTA_CONTABLE')->distinct()->pluck('TXT_CUENTA_CONTABLE')->toArray();

	    $listacuentacontable 	= 	$this->pc_lista_cuentas_contable_funcion_detallado_array_cuenta(Session::get('empresas_meta')->COD_EMPR,$anio,$array_cuenta);


	    $funcion 				= 	$this;

		return View::make('reporte/ajax/alistaresultadofuncion',
						 [
						 	'listacuentacontable'	=> $listacuentacontable,
						 	'periodo_inicio_id'		=> $periodo_inicio_id,
						 	'periodo_fin_id'		=> $periodo_fin_id,					 	
						 	'idopcion' 				=> $idopcion,
						 	'suma' 					=> $suma,
						 	'funcion' 				=> $funcion,
						 	'array_cuenta' 			=> $array_cuenta,
						 	'periodo_array' 		=> $periodo_array,
						 	'anio' 					=> $anio,
						 	'ajax' 					=> true,					 	
						 ]);
	   
	}



	public function actionGestionSituacionFinanciera($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Estado de situación financiera');
	    $sel_periodo 			=	'';


	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);

	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$funcion 				= 	$this;
		$lista_situacion_fin    =   array();

		$combo_reporte  		= 	array('DETALLADO' => 'DETALLADO' , 'CONSOLIDADO' => 'CONSOLIDADO');


		return View::make('reporte/situacionfinanciera',
						 [
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'combo_reporte'			=> $combo_reporte,
						 	'anio'					=> $anio,
						 	'sel_periodo'	 		=> $sel_periodo,

						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'lista_situacion_fin' 	=> $lista_situacion_fin,						 	
						 ]);
	}


	public function actionAjaxBuscarSituacionFinanciera(Request $request)
	{

		$anio 					=   $request['anio'];
		$periodo_inicio_id 		=   $request['periodo_inicio_id'];
		$periodo_fin_id 		=   $request['periodo_fin_id'];
		$idopcion 				=   $request['idopcion'];
		$reporte 				=   $request['reporte'];


		$periodoinicio   		=   CONPeriodo::where('COD_PERIODO','=',$periodo_inicio_id)->first();
		$periodofin   			=   CONPeriodo::where('COD_PERIODO','=',$periodo_fin_id)->first();


		$periodo_array 			=   CONPeriodo::where('COD_ANIO','=',$anio)
									->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('CON.PERIODO.COD_MES','>=',$periodoinicio->COD_MES)
	    							->where('CON.PERIODO.COD_MES','<=',$periodofin->COD_MES)
									->pluck('COD_PERIODO')->toArray();



	    $suma 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array);


		$array_cuenta 			=	$suma->select('TXT_CUENTA_CONTABLE')->distinct()->pluck('TXT_CUENTA_CONTABLE')->toArray();

	    $listacuentacontable 	= 	$this->pc_lista_cuentas_contable_balance_detallado_array_cuenta(Session::get('empresas_meta')->COD_EMPR,$anio,$array_cuenta);

	    $sumat 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')

	    							->join('WEB.cuentacontables', 'WEB.cuentacontables.id', '=', 'WEB.asientomovimientos.COD_CUENTA_CONTABLE')
									->leftJoin('CMP.CATEGORIA AS TC', 'TC.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_categoria_id')
									->leftJoin('CMP.CATEGORIA AS BTC', 'BTC.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_balance_id')

	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array)
	    							->selectRaw('WEB.asientomovimientos.*,
	    										 WEB.asientos.*,
	    										 CON.PERIODO.*,
	    										 SUBSTRING(WEB.asientomovimientos.TXT_CUENTA_CONTABLE, 1, 2) as GRUPO,
	    										 TC.NOM_CATEGORIA AS tipo_cuenta,
	    										 BTC.NOM_CATEGORIA AS tipo_cuenta_balance,
	    										 TC.COD_CATEGORIA AS tipo_cuenta_id,
	    										 BTC.COD_CATEGORIA AS tipo_cuenta_balance_id')
	    							->get();					

	    $funcion 				= 	$this;


	    if($reporte=='DETALLADO'){

			return View::make('reporte/ajax/alistasituacionfinanciera',
							 [
							 	'listacuentacontable'	=> $listacuentacontable,
							 	'periodo_inicio_id'		=> $periodo_inicio_id,
							 	'periodo_fin_id'		=> $periodo_fin_id,					 	
							 	'idopcion' 				=> $idopcion,
							 	'suma' 					=> $suma,
							 	'sumat' 				=> $sumat,
							 	'funcion' 				=> $funcion,
							 	'ajax' 					=> true,					 	
							 ]);

	    }else{

			return View::make('reporte/ajax/alistasituacionfinancieraconsolidado',
							 [
							 	'listacuentacontable'	=> $listacuentacontable,
							 	'periodo_inicio_id'		=> $periodo_inicio_id,
							 	'periodo_fin_id'		=> $periodo_fin_id,					 	
							 	'idopcion' 				=> $idopcion,
							 	'suma' 					=> $suma,
							 	'sumat' 				=> $sumat,
							 	'funcion' 				=> $funcion,
							 	'ajax' 					=> true,					 	
							 ]);
	    

	    }



	}


	public function actionAjaxComboPeriodoAnioEmpresa(Request $request)
	{


		$anio 					=   $request['anio'];
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
	    $sel_periodo 			=	'';
		$funcion 				= 	$this;
		
		return View::make('general/combo/cperiodotitulo',
						 [

						 	'combo_periodo'			=> $combo_periodo,
						 	'sel_periodo'	 		=> $sel_periodo,					 	
						 	'ajax' 					=> true,						 	
						 ]);
	}


	public function actionResutadoFuncionExcel(Request $request)
	{


		set_time_limit(0);

		$anio 					=   $request['anio'];
		$periodo_inicio_id 		=   $request['periodo_inicio_id'];
		$periodo_fin_id 		=   $request['periodo_fin_id'];
		$idopcion 				=   $request['idopcion'];



		$periodoinicio   		=   CONPeriodo::where('COD_PERIODO','=',$periodo_inicio_id)->first();
		$periodofin   			=   CONPeriodo::where('COD_PERIODO','=',$periodo_fin_id)->first();

		$periodo_array 			=   CONPeriodo::where('COD_ANIO','=',$anio)
									->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('CON.PERIODO.COD_MES','>=',$periodoinicio->COD_MES)
	    							->where('CON.PERIODO.COD_MES','<=',$periodofin->COD_MES)
									->pluck('COD_PERIODO')->toArray();


	    $suma 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array);


		$array_cuenta 			=	$suma->select('TXT_CUENTA_CONTABLE')->distinct()->pluck('TXT_CUENTA_CONTABLE')->toArray();

	    $listacuentacontable 	= 	$this->pc_lista_cuentas_contable_funcion_detallado_array_cuenta(Session::get('empresas_meta')->COD_EMPR,$anio,$array_cuenta);


	    $funcion 				= 	$this;


		$titulo 				=   'RESULTADO-FUNCION-'.Session::get('empresas_meta')->NOM_EMPR;

	    Excel::create($titulo, function($excel) use ($listacuentacontable,$funcion,$anio,$array_cuenta,$periodo_array) {
	        $excel->sheet('resultadofuncion', function($sheet) use ($listacuentacontable,$funcion,$anio,$array_cuenta,$periodo_array) {
	            $sheet->loadView('reporte/excel/listaresultadofuncion')->with('listacuentacontable',$listacuentacontable)
	            														->with('funcion',$funcion)
	            														->with('anio',$anio)
	            														->with('array_cuenta',$array_cuenta)
	            														->with('periodo_array',$periodo_array);         
	        });
	    })->export('xls');



	}


	public function actionSituacionFinancieraExcel(Request $request)
	{


		set_time_limit(0);

		$anio 					=   $request['anio'];
		$periodo_inicio_id 		=   $request['periodo_inicio_id'];
		$periodo_fin_id 		=   $request['periodo_fin_id'];
		$idopcion 				=   $request['idopcion'];
		$reporte 				=   $request['reporte'];

		$periodoinicio   		=   CONPeriodo::where('COD_PERIODO','=',$periodo_inicio_id)->first();
		$periodofin   			=   CONPeriodo::where('COD_PERIODO','=',$periodo_fin_id)->first();


		$periodo_array 			=   CONPeriodo::where('COD_ANIO','=',$anio)
									->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('CON.PERIODO.COD_MES','>=',$periodoinicio->COD_MES)
	    							->where('CON.PERIODO.COD_MES','<=',$periodofin->COD_MES)
									->pluck('COD_PERIODO')->toArray();



	    $suma 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array);


		$array_cuenta 			=	$suma->select('TXT_CUENTA_CONTABLE')->distinct()->pluck('TXT_CUENTA_CONTABLE')->toArray();

	    $listacuentacontable 	= 	$this->pc_lista_cuentas_contable_balance_detallado_array_cuenta(Session::get('empresas_meta')->COD_EMPR,$anio,$array_cuenta);

	    $sumat 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')

	    							->join('WEB.cuentacontables', 'WEB.cuentacontables.id', '=', 'WEB.asientomovimientos.COD_CUENTA_CONTABLE')
									->leftJoin('CMP.CATEGORIA AS TC', 'TC.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_categoria_id')
									->leftJoin('CMP.CATEGORIA AS BTC', 'BTC.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_balance_id')

	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array)
	    							->selectRaw('WEB.asientomovimientos.*,
	    										 WEB.asientos.*,
	    										 CON.PERIODO.*,
	    										 SUBSTRING(WEB.asientomovimientos.TXT_CUENTA_CONTABLE, 1, 2) as GRUPO,
	    										 TC.NOM_CATEGORIA AS tipo_cuenta,
	    										 BTC.NOM_CATEGORIA AS tipo_cuenta_balance,
	    										 TC.COD_CATEGORIA AS tipo_cuenta_id,
	    										 BTC.COD_CATEGORIA AS tipo_cuenta_balance_id')
	    							->get();					

	    $funcion 				= 	$this;


	    if($reporte=='DETALLADO'){

			$titulo 				=   'SITUACION-FINANCIERA-'.$reporte.'-'.Session::get('empresas_meta')->NOM_EMPR;

		    Excel::create($titulo, function($excel) use ($sumat,$listacuentacontable,$funcion) {
		        $excel->sheet('situacionfinanciera', function($sheet) use ($sumat,$listacuentacontable,$funcion) {
		            $sheet->loadView('reporte/excel/listasituacionfinanciera')->with('listacuentacontable',$listacuentacontable)
		            														  ->with('funcion',$funcion)
		            														  ->with('sumat',$sumat);         
		        });
		    })->export('xls');

	    }else{

			$titulo 				=   'SITUACION-FINANCIERA-'.$reporte.'-'.Session::get('empresas_meta')->NOM_EMPR;

		    Excel::create($titulo, function($excel) use ($sumat,$listacuentacontable,$funcion) {
		        $excel->sheet('situacionfinanciera', function($sheet) use ($sumat,$listacuentacontable,$funcion) {
		            $sheet->loadView('reporte/excel/listasituacionfinancieraconsolidado')->with('listacuentacontable',$listacuentacontable)
		            														  ->with('funcion',$funcion)
		            														  ->with('sumat',$sumat);         
		        });
		    })->export('xls');

	    }

	}


	public function actionDescargarBalanceComprobacionExcel(Request $request)
	{


		set_time_limit(0);

		$anio 					=   $request['anio'];
		$periodo_inicio_id 		=   $request['periodo_inicio_id'];
		$periodo_fin_id 		=   $request['periodo_fin_id'];
		$idopcion 				=   $request['idopcion'];

		$periodoinicio   		=   CONPeriodo::where('COD_PERIODO','=',$periodo_inicio_id)->first();
		$periodofin   			=   CONPeriodo::where('COD_PERIODO','=',$periodo_fin_id)->first();


		$periodo_array 			=   CONPeriodo::where('COD_ANIO','=',$anio)
									->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('CON.PERIODO.COD_MES','>=',$periodoinicio->COD_MES)
	    							->where('CON.PERIODO.COD_MES','<=',$periodofin->COD_MES)
									->pluck('COD_PERIODO')->toArray();

	    $suma 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array);


		$array_cuenta 			=	$suma->select('TXT_CUENTA_CONTABLE')->distinct()->pluck('TXT_CUENTA_CONTABLE')->toArray();

	    $listacuentacontable 	= 	$this->pc_lista_cuentas_contable_array_cuenta(Session::get('empresas_meta')->COD_EMPR,$anio,$array_cuenta);


	    $sumat 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array)->get();					

	    $funcion 				= 	$this;


		$titulo 				=   'BALANCE-COMPROBACION-'.Session::get('empresas_meta')->NOM_EMPR;

	    Excel::create($titulo, function($excel) use ($sumat,$listacuentacontable) {
	        $excel->sheet('Balance', function($sheet) use ($sumat,$listacuentacontable) {
	            $sheet->loadView('reporte/excel/listabalancecomprobacion')->with('listacuentacontable',$listacuentacontable)
	            														->with('sumat',$sumat);         
	        });
	    })->export('xls');



	}


	public function actionGestionBalanceComprobacion($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Balance de Comprobacion');
	    $sel_periodo 			=	'';


	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);

	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$funcion 				= 	$this;
		$lista_balance          =   array();
		return View::make('reporte/balancecomprobacion',
						 [
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_periodo'	 		=> $sel_periodo,

						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'lista_balance' 		=> $lista_balance,						 	
						 ]);
	}


	public function actionAjaxBuscarBalanceComprobacion(Request $request)
	{

		$anio 					=   $request['anio'];
		$periodo_inicio_id 		=   $request['periodo_inicio_id'];
		$periodo_fin_id 		=   $request['periodo_fin_id'];
		$idopcion 				=   $request['idopcion'];

		$periodoinicio   		=   CONPeriodo::where('COD_PERIODO','=',$periodo_inicio_id)->first();
		$periodofin   			=   CONPeriodo::where('COD_PERIODO','=',$periodo_fin_id)->first();




		$periodo_array 			=   CONPeriodo::where('COD_ANIO','=',$anio)
									->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('CON.PERIODO.COD_MES','>=',$periodoinicio->COD_MES)
	    							->where('CON.PERIODO.COD_MES','<=',$periodofin->COD_MES)
									->pluck('COD_PERIODO')->toArray();



	    $suma 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array);


		$array_cuenta 			=	$suma->select('TXT_CUENTA_CONTABLE')->distinct()->pluck('TXT_CUENTA_CONTABLE')->toArray();

	    $listacuentacontable 	= 	$this->pc_lista_cuentas_contable_array_cuenta(Session::get('empresas_meta')->COD_EMPR,$anio,$array_cuenta);


	    $sumat 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array)->get();					

	    $funcion 				= 	$this;

		return View::make('reporte/ajax/alistabalancecomprobacion',
						 [
						 	'listacuentacontable'	=> $listacuentacontable,
						 	'periodo_inicio_id'		=> $periodo_inicio_id,
						 	'periodo_fin_id'		=> $periodo_fin_id,					 	
						 	'idopcion' 				=> $idopcion,
						 	'suma' 					=> $suma,
						 	'sumat' 				=> $sumat,
						 	'funcion' 				=> $funcion,
						 	'ajax' 					=> true,					 	
						 ]);

	}



	public function actionGestionLibrosMayorDiario($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Generacion del libro mayor y diario');
	    $sel_libro 				=	'';
	    $sel_periodo 			=	'';



	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_libro 			= 	$this->gn_generacion_combo_libro('Seleccione tipo asiento');
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$funcion 				= 	$this;
		$combo_tran_gratuita 	= 	$this->gn_combo_transferencia_gratuita();
		$lista_asiento          =   array();

		return View::make('reporte/librodiarioymayor',
						 [
						 	'combo_libro'		=> $combo_libro,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'combo_tran_gratuita'	=> $combo_tran_gratuita,
						 	'anio'					=> $anio,
						 	'sel_libro'	 			=> $sel_libro,
						 	'sel_periodo'	 		=> $sel_periodo,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'lista_asiento' 		=> $lista_asiento,						 	
						 ]);
	}



	public function actionDescargarArchivoDiarioMayor(Request $request)
	{


		set_time_limit(0);

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$libro_id 				=   $request['libro_id'];
		$data_archivo 			=   $request['data_archivo'];

		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
	   	$mes 					= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT); 

	    //LIBRO DIARIO 
	    if($libro_id == 'LD'){

		    if($data_archivo == 'ple'){

			    $listaasiento 			= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
			    							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
			    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
			    							//->where('WEB.asientos.COD_ASIENTO','=','ICCHAC0000002607')
			    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
			    							->orderby('WEB.asientomovimientos.TXT_CUENTA_CONTABLE','asc')
			    							->orderby('WEB.asientos.FEC_ASIENTO','asc')
			    							->get();


				$nombre = $this->rp_crear_nombre_diario($anio,$mes).'.txt';
				$path = storage_path('diario/ple/'.$nombre);

		    	$this->rp_archivo_ple_diario($anio,$mes,$listaasiento,$nombre,$path);

			    if (file_exists($path)){
			        return Response::download($path);
			    }	 

		    }
	    }

	    //LIBRO MAYOR 
	    if($libro_id == 'LM'){

		    if($data_archivo == 'ple'){

			    $listaasiento 			= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
			    							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
			    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
			    							//->where('WEB.asientos.COD_ASIENTO','=','ICCHAC0000002607')
			    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
			    							->orderby('WEB.asientomovimientos.TXT_CUENTA_CONTABLE','asc')
			    							->orderby('WEB.asientos.FEC_ASIENTO','asc')
			    							->get();


				$nombre = $this->rp_crear_nombre_mayor($anio,$mes).'.txt';
				$path = storage_path('mayor/ple/'.$nombre);

		    	$this->rp_archivo_ple_mayor($anio,$mes,$listaasiento,$nombre,$path);

			    if (file_exists($path)){
			        return Response::download($path);
			    }	 

		    }
	    }


	    //PLAN CONTABLE 
	    if($libro_id == 'PC'){

		    if($data_archivo == 'ple'){

			    $listaasiento 	= 	$this->pc_lista_cuentas_contable(Session::get('empresas_meta')->COD_EMPR,$anio);

				$nombre = $this->rp_crear_nombre_plan_contable($anio,$mes).'.txt';
				$path = storage_path('plancontable/ple/'.$nombre);



		    	$this->rp_archivo_ple_plan_contable($anio,$mes,$listaasiento,$nombre,$path,$periodo);

			    if (file_exists($path)){
			        return Response::download($path);
			    }	 

		    }
	    }


	}



	public function actionAjaxBuscarListaPleDiario(Request $request)
	{

		$anio 					=   $request['anio'];
		$libro_id 				=   $request['libro_id'];
		$periodo_id 			=   $request['periodo_id'];
		$idopcion 				=   $request['idopcion'];

		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
	   	$mes 					= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT);


	    //compras 
	    if($libro_id == 'LD'){

			$listaasiento 		= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
			    							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
			    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
			    							//->where('WEB.asientos.COD_ASIENTO','=','ICCHAC0000002607')
			    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
			    							->orderby('WEB.asientomovimientos.TXT_CUENTA_CONTABLE','asc')
			    							->orderby('WEB.asientos.FEC_ASIENTO','asc')
			    							->get();

			$nombre = $this->rp_crear_nombre_diario($anio,$mes).'.txt';
			$path = storage_path('diario/ple/'.$nombre);
			//dd($listaasiento);
	    	$lista_asiento = $this->rp_archivo_ple_diario($anio,$mes,$listaasiento,$nombre,$path);


	    	$funcion 				= 	$this;

			return View::make('reporte/ajax/alistadiario',
							 [
							 	'lista_asiento'			=> $lista_asiento,					 	
							 	'idopcion' 				=> $idopcion,
							 	'funcion' 				=> $funcion,
							 	'ajax' 					=> true,					 	
							 ]);



	    }



	    if($libro_id == 'LM'){

			$listaasiento 		= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
			    							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
			    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
			    							//->where('WEB.asientos.COD_ASIENTO','=','ICCHAC0000002607')
			    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
			    							->orderby('WEB.asientomovimientos.TXT_CUENTA_CONTABLE','asc')
			    							->orderby('WEB.asientos.FEC_ASIENTO','asc')
			    							->get();

			$nombre = $this->rp_crear_nombre_mayor($anio,$mes).'.txt';
			$path = storage_path('diario/ple/'.$nombre);
			//dd($listaasiento);
	    	$lista_asiento = $this->rp_archivo_ple_mayor($anio,$mes,$listaasiento,$nombre,$path);


	    	$funcion 				= 	$this;

			return View::make('reporte/ajax/alistamayor',
							 [
							 	'lista_asiento'			=> $lista_asiento,					 	
							 	'idopcion' 				=> $idopcion,
							 	'funcion' 				=> $funcion,
							 	'ajax' 					=> true,					 	
							 ]);



	    }


	    if($libro_id == 'PC'){

			$listaasiento 	= 	$this->pc_lista_cuentas_contable(Session::get('empresas_meta')->COD_EMPR,$anio);

			$nombre = $this->rp_crear_nombre_plan_contable($anio,$mes).'.txt';
			$path = storage_path('diario/ple/'.$nombre);

	    	$lista_asiento = $this->rp_archivo_ple_plan_contable($anio,$mes,$listaasiento,$nombre,$path,$periodo);
			//dd($lista_asiento);
	    	$funcion 				= 	$this;

			return View::make('reporte/ajax/alistaplancontable',
							 [
							 	'lista_asiento'			=> $lista_asiento,					 	
							 	'idopcion' 				=> $idopcion,
							 	'funcion' 				=> $funcion,
							 	'ajax' 					=> true,					 	
							 ]);



	    }




	}



}
