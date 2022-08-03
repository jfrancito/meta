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
use App\Modelos\ALMProducto;
use App\Modelos\CONPeriodo;
use App\Modelos\CMPCategoria;




use App\Traits\PlanContableTraits;
use App\Traits\GeneralesTraits;
use App\Traits\KardexTraits;

use Maatwebsite\Excel\Facades\Excel;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


class KardexController extends Controller
{

	use GeneralesTraits;
	use KardexTraits;
	use PlanContableTraits;

	public function actionListarSaldoInicial($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Saldo Inicial');
	   	$sel_tipo_producto 		=	'';
	    $combo_tipo_producto 	= 	$this->gn_generacion_combo_categoria('TIPO_PRODUCTO_KARDEX','Seleccione tipo producto','');
	    $listasaldoinicial 		= 	$this->kd_lista_saldo_inicial(Session::get('empresas_meta')->COD_EMPR,$sel_tipo_producto);
		$funcion 				= 	$this;
	

		return View::make('kardex/listasaldoinicial',
						 [
						 	'listasaldoinicial' 	=> $listasaldoinicial,
						 	'combo_tipo_producto'	=> $combo_tipo_producto,
						 	'sel_tipo_producto'	 	=> $sel_tipo_producto,						 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxListarSaldoInicial(Request $request)
	{

		$tipo_producto_id 		=   $request['tipo_producto_id'];

		$idopcion 				=   $request['idopcion'];
	    $listasaldoinicial 		= 	$this->kd_lista_saldo_inicial(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id);
		$funcion 				= 	$this;

		return View::make('kardex/ajax/alistasaldoinicial',
						 [
						 	'listasaldoinicial' 	=> $listasaldoinicial,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}



	public function actionListarMovimientoKardex($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Movimiento del kardex');

	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	   	$sel_tipo_movimiento 	=	'';
	   	$sel_tipo_producto 		=	'';
	    $combo_tipo_movimiento 	= 	$this->gn_generacion_combo_categoria('TIPO_MOVIMIENTO_KARDEX','Seleccione movimiento','');
	    $combo_tipo_producto 	= 	$this->gn_generacion_combo_categoria('TIPO_PRODUCTO_KARDEX','Seleccione tipo producto','');


	    $listamovimiento 		= 	array();
	    $listasaldoinicial      = 	array();
	    $listaperido            = 	array();
		$funcion 				= 	$this;
	
		return View::make('kardex/listamovimientokardex',
						 [
						 	'listamovimiento' 		=> $listamovimiento,
						 	'listasaldoinicial' 	=> $listasaldoinicial,
						 	'listaperido' 			=> $listaperido,
						 	'combo_tipo_movimiento'	=> $combo_tipo_movimiento,
						 	'sel_tipo_movimiento'	=> $sel_tipo_movimiento,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_tipo_producto'	=> $combo_tipo_producto,
						 	'sel_tipo_producto'	 	=> $sel_tipo_producto,	
						 	'anio'					=> $anio,
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}






	public function actionAjaxListarMovimientoKardex(Request $request)
	{
		$anio 					=   $request['anio'];
		$tipo_movimiento_id 	=   $request['tipo_movimiento_id'];
		$tipo_producto_id 		=   $request['tipo_producto_id'];
		$tipo_asiento_id        =   '';


		$tipo_asiento_id    	=   'TAS0000000000003';
		$idopcion 				=   $request['idopcion'];
	    $listasaldoinicial 		= 	$this->kd_lista_saldo_inicial(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id);
	    $listaperido 			= 	$this->gn_lista_periodo($anio,Session::get('empresas_meta')->COD_EMPR);	
		$listamovimiento 		= 	$this->kd_lista_movimiento(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id);
		$funcion 				= 	$this;

		$tipo_asiento_id    	=   'TAS0000000000004';
		$listamovimientocommpra = 	$this->kd_lista_movimiento(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id);


		return View::make('kardex/ajax/alistamovimientokardex',
						 [
						 	'listasaldoinicial'      => $listasaldoinicial,
						 	'listamovimiento' 		 => $listamovimiento,
						 	'listamovimientocommpra' => $listamovimientocommpra,
						 	'listasaldoinicial' 	 => $listasaldoinicial,
						 	'listaperido' 			 => $listaperido,				 	
						 	'idopcion' 				 => $idopcion,
						 	'anio' 					 => $anio,
						 	'tipo_asiento_id' 		 => $tipo_asiento_id,
						 	'tipo_producto_id' 		 => $tipo_producto_id,
						 	'funcion' 				 => $funcion,
						 	'ajax' 					 => true,						 	
						 ]);
	}

	public function actionAjaxModalDetalleKardex(Request $request)
	{

		$data_producto_id 		=   $request['data_producto_id'];
		$data_periodo_id 		=   $request['data_periodo_id'];
		$data_anio 				=   $request['data_anio'];
		$data_tipo_asiento_id 	=   $request['data_tipo_asiento_id'];


		$producto 				= 	ALMProducto::where('COD_PRODUCTO','=',$data_producto_id)->first();
		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$data_periodo_id)->first();

		$idopcion 				=   $request['idopcion'];
	    $listadetalleproducto 	= 	$this->kd_lista_producto_periodo(Session::get('empresas_meta')->COD_EMPR, 
	    															$data_anio, $data_tipo_asiento_id,$data_producto_id,$data_periodo_id);

	    //dd($producto);
		return View::make('kardex/modal/ajax/adetalleproducto',
						 [
						 	'listadetalleproducto' 	=> $listadetalleproducto,
						 	'producto' 	=> $producto,
						 	'periodo' 	=> $periodo,					 	
						 	'idopcion' 				=> $idopcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}


	public function actionAjaxModalDetalleTotalKardex(Request $request)
	{

		$data_producto_id 		=   $request['data_producto_id'];
		$data_periodo_id 		=   $request['data_periodo_id'];
		$data_anio 				=   $request['data_anio'];
		$data_tipo_asiento_id 	=   $request['data_tipo_asiento_id'];
		$tipo_producto_id 		=   $request['tipo_producto_id'];


		$producto 				= 	ALMProducto::where('COD_PRODUCTO','=',$data_producto_id)->first();
		$periodo_enero 			= 	CONPeriodo::where('COD_ANIO','=',$data_anio)
									->where('COD_MES','=',1)
									->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
									->first();

		$idopcion 				=   $request['idopcion'];
	    $saldoinicial 			= 	$this->kd_saldo_inicial_producto_id(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id,$data_producto_id);
	    $listadetalleproducto 	= 	$this->kd_lista_producto_periodo(Session::get('empresas_meta')->COD_EMPR, 
	    															$data_anio, $data_tipo_asiento_id,$data_producto_id,$data_periodo_id);
	    $listakardexif 			= 	$this->kd_lista_kardex_inventario_final(Session::get('empresas_meta')->COD_EMPR, 
	    																	$saldoinicial,
	    																	$listadetalleproducto,
	    																	$producto,
	    																	$periodo_enero);
		return View::make('kardex/modal/ajax/adetallekardexif',
						 [
						 	'listakardexif' 	=> $listakardexif,
						 	'producto' 			=> $producto,
						 	'periodo_enero' 	=> $periodo_enero,					 	
						 	'idopcion' 			=> $idopcion,
						 	'ajax' 				=> true,						 	
						 ]);
	}


	public function actionDescargarExcelKardex(Request $request)
	{


		set_time_limit(0);

		$anio 					=   $request['anio'];
		$tipo_producto_id 		=   $request['tipo_producto_id'];
		$tipo_asiento_id        =   '';

		$tipoproducto 			= 	CMPCategoria::where('COD_CATEGORIA','=',$tipo_producto_id)->first();


		$tipo_asiento_id    	=   'TAS0000000000003';
		$idopcion 				=   $request['idopcion'];
	    $listasaldoinicial 		= 	$this->kd_lista_saldo_inicial(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id);
	    $listaperido 			= 	$this->gn_lista_periodo($anio,Session::get('empresas_meta')->COD_EMPR);	
		$listamovimiento 		= 	$this->kd_lista_movimiento(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id);
		$funcion 				= 	$this;

		$tipo_asiento_id    	=   'TAS0000000000004';
		$listamovimientocommpra = 	$this->kd_lista_movimiento(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id);

		$titulo 				=   'KARDEX-'.$tipoproducto->NOM_CATEGORIA.'-'.Session::get('empresas_meta')->NOM_EMPR;

	    Excel::create($titulo, function($excel) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio) {

	        $excel->sheet('Inventario Final', function($sheet) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio) {
	            $sheet->loadView('kardex/excel/einventariofinal')->with('listasaldoinicial',$listasaldoinicial)
	            												 ->with('listamovimiento',$listamovimiento)
	            												 ->with('listamovimientocommpra',$listamovimientocommpra)
	            												 ->with('listaperido',$listaperido)
	            												 ->with('anio',$anio)
	            												 ->with('funcion',$funcion);         
	        });

	        $excel->sheet('Saldo Inicial', function($sheet) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio) {
	            $sheet->loadView('kardex/excel/esaldoinicial')->with('listasaldoinicial',$listasaldoinicial)
	            												 ->with('listamovimiento',$listamovimiento)
	            												 ->with('listamovimientocommpra',$listamovimientocommpra)
	            												 ->with('listaperido',$listaperido)
	            												 ->with('anio',$anio)
	            												 ->with('funcion',$funcion);         
	        });

	        $excel->sheet('Ventas Consolidado', function($sheet) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio) {
	            $sheet->loadView('kardex/excel/eventasconsolidado')->with('listasaldoinicial',$listasaldoinicial)
	            												 ->with('listamovimiento',$listamovimiento)
	            												 ->with('listamovimientocommpra',$listamovimientocommpra)
	            												 ->with('listaperido',$listaperido)
	            												 ->with('anio',$anio)
	            												 ->with('funcion',$funcion);         
	        });


	        $excel->sheet('Compras Consolidado', function($sheet) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio) {
	            $sheet->loadView('kardex/excel/ecomprasconsolidado')->with('listasaldoinicial',$listasaldoinicial)
	            												 ->with('listamovimiento',$listamovimiento)
	            												 ->with('listamovimientocommpra',$listamovimientocommpra)
	            												 ->with('listaperido',$listaperido)
	            												 ->with('anio',$anio)
	            												 ->with('funcion',$funcion);         
	        });


	    })->export('xls');





	}




}
