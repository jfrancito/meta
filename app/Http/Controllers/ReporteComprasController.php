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


class ReporteComprasController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use ArchivoTraits;
	use ReporteTraits;

	public function actionListarEstadoComprobanteCompra($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Reporte de estado de comprobante compra');


	    $sel_periodo 			=	'';
	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
	    $listaasiento 			= 	array();
		$funcion 				= 	$this;
		
		return View::make('compras/reportes/listaestadocomprobante',
						 [
						 	'listaasiento' 			=> $listaasiento,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_periodo'	 		=> $sel_periodo,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxListarEstadoComprobanteCompra(Request $request)
	{

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];

	    $listaasiento 			= 	WEBAsiento::where('COD_PERIODO','=',$periodo_id)
	    							->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000004')
	    							->where('COD_CATEGORIA_ESTADO_ASIENTO','<>','IACHTE0000000024')
	    							->orderBy('COD_CATEGORIA_ESTADO_ASIENTO', 'asc')
	    							->orderBy('FEC_ASIENTO', 'asc')
	    							->get();

		$funcion 				= 	$this;
		
		return View::make('compras/reportes/ajax/alistaestadocomprobante',
						 [

						 	'listaasiento'			=> $listaasiento,
						 	'funcion'				=> $funcion,			 	
						 	'ajax' 					=> true,						 	
						 ]);
	}



	public function actionAjaxDescargarAsientoContable(Request $request)
	{


		set_time_limit(0);

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$idopcion 				=   $request['idopcion'];

	    $listaasiento 			= 	WEBAsiento::where('COD_PERIODO','=',$periodo_id)
	    							->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000004')
	    							->where('COD_CATEGORIA_ESTADO_ASIENTO','<>','IACHTE0000000024')
	    							->orderBy('COD_CATEGORIA_ESTADO_ASIENTO', 'asc')
	    							->orderBy('FEC_ASIENTO', 'asc')
	    							->get();

	    $funcion 				= 	$this;
		$titulo 				=   'ESTADO-DOCUMENTOS-COMPRA-'.Session::get('empresas_meta')->NOM_EMPR;

	    Excel::create($titulo, function($excel) use ($funcion,$listaasiento) {
	        $excel->sheet('estados', function($sheet) use ($funcion,$listaasiento) {
	            $sheet->loadView('compras/excel/elistaestadocomprobante')->with('listaasiento',$listaasiento)
	            														->with('funcion',$funcion);         
	        });
	    })->export('xls');



	}


	public function actionAjaxDescargarAsientoAlterado(Request $request)
	{

		set_time_limit(0);

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$idopcion 				=   $request['idopcion'];

	    $arrayalterado 			= 	WEBAsiento::where('COD_PERIODO','=',$periodo_id)
	    							->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000004')
	    							->where(function ($query){
									            $query->where('CAN_TOTAL_DEBE', '<>', 0)
									            ->orWhere('CAN_TOTAL_HABER', '<>', 0);
											})
	    							->selectRaw('max(COD_ASIENTO) AS COD_ASIENTO')
	    							->groupBy('TXT_REFERENCIA')
	    							->havingRaw('count(TXT_REFERENCIA) > ?', [1])
	    							->pluck('COD_ASIENTO')
                                    ->toArray();					

	    $listaasiento 			= 	WEBAsiento::whereIn('COD_ASIENTO',$arrayalterado)
	    							->orderBy('COD_CATEGORIA_ESTADO_ASIENTO', 'asc')
	    							->orderBy('FEC_ASIENTO', 'asc')
	    							->get();


	    $funcion 				= 	$this;
		$titulo 				=   'ASIENTO-ALTERADOS-COMPRA-'.Session::get('empresas_meta')->NOM_EMPR;

	    Excel::create($titulo, function($excel) use ($funcion,$listaasiento) {
	        $excel->sheet('estados', function($sheet) use ($funcion,$listaasiento) {
	            $sheet->loadView('compras/excel/elistaasientoalterado')->with('listaasiento',$listaasiento)
	            														->with('funcion',$funcion);         
	        });
	    })->export('xls');



	}




	public function actionListarAsientoAlteradoCompra($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Reporte de asiento alterado compra');

	    $sel_periodo 			=	'';
	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
	    $listaasiento 			= 	array();
		$funcion 				= 	$this;
		
		return View::make('compras/reportes/listaasientoalterado',
						 [
						 	'listaasiento' 			=> $listaasiento,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_periodo'	 		=> $sel_periodo,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxListarAsientoAlteradoCompra(Request $request)
	{

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];

	    $arrayalterado 			= 	WEBAsiento::where('COD_PERIODO','=',$periodo_id)
	    							->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000004')
	    							->where(function ($query){
									            $query->where('CAN_TOTAL_DEBE', '<>', 0)
									            ->orWhere('CAN_TOTAL_HABER', '<>', 0);
											})
	    							->selectRaw('max(COD_ASIENTO) AS COD_ASIENTO')
	    							->groupBy('TXT_REFERENCIA')
	    							->havingRaw('count(TXT_REFERENCIA) > ?', [1])
	    							->pluck('COD_ASIENTO')
                                    ->toArray();					

	    $listaasiento 			= 	WEBAsiento::whereIn('COD_ASIENTO',$arrayalterado)
	    							->orderBy('COD_CATEGORIA_ESTADO_ASIENTO', 'asc')
	    							->orderBy('FEC_ASIENTO', 'asc')
	    							->get();

		$funcion 				= 	$this;
		
		return View::make('compras/reportes/ajax/alistaasientoalterado',
						 [

						 	'listaasiento'			=> $listaasiento,
						 	'funcion'				=> $funcion,			 	
						 	'ajax' 					=> true,						 	
						 ]);
	}


}
