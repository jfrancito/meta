<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;

use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\ALMProducto;
use App\Modelos\CONPeriodo;
use App\Modelos\CMPCategoria;
use App\Modelos\WEBKardexTransferencia;
use App\Modelos\WEBKardexProducto;


use App\Traits\PlanContableTraits;
use App\Traits\GeneralesTraits;
use App\Traits\KardexTraits;
use App\Traits\MovilidadTraits;


use Maatwebsite\Excel\Facades\Excel;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


class KardexCascaraController extends Controller
{

	use GeneralesTraits;
	use KardexTraits;
	use PlanContableTraits;
	use MovilidadTraits;

	public function actionListarKardexCascara($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Movimiento del kardex Cascara');

	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	   	$sel_tipo_movimiento 	=	'';
	   	$sel_tipo_producto 		=	'TPK0000000000004';



	    $combo_tipo_movimiento 	= 	$this->gn_generacion_combo_categoria('TIPO_MOVIMIENTO_KARDEX','Seleccione movimiento','');
	    $combo_tipo_producto 	= 	$this->gn_generacion_combo_categoria('TIPO_PRODUCTO_KARDEX','Seleccione tipo producto','');

	    $listamovimiento 		= 	array();
	    $listasaldoinicial      = 	array();
	    $listaperido            = 	array();
		$funcion 				= 	$this;
	
	   	//dd($combo_tipo_producto);

		return View::make('kardex/listakardexenvases',
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


	public function actionDescargarExcelKardexCascara(Request $request)
	{


		set_time_limit(0);

		//$anio 					=   $request['anio'];
		$tipo_producto_id 		=   'TPK0000000000004';
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;

	    $saldoincial 			= 	WEBKardexProducto::where('empresa_id','=',$empresa_id)
	    							->where('tipo_producto_id','=',$tipo_producto_id)
	    							->where('activo','=',1)
									->orderBy('fecha_saldo_inicial', 'asc')
			    					->first();
		$anio 					= 	substr($saldoincial->fecha_saldo_inicial, 0, 4);

		$tipo_asiento_id        =   '';
		$tipo_producto_id 		=   'TPK0000000000004';
		$tipoproducto 			= 	CMPCategoria::where('COD_CATEGORIA','=',$tipo_producto_id)->first();


		$idopcion 				=   $request['idopcion'];
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;
	    $listaperido 			= 	$this->gn_lista_periodo($anio,Session::get('empresas_meta')->COD_EMPR);	
		$funcion 				= 	$this;

		$tipo_asiento_id    	=   'TAS0000000000003';
	    $listasaldoinicial 		= 	 $this->kd_lista_saldo_inicial(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id);

		$titulo 				=   'KARDEX-'.$tipoproducto->NOM_CATEGORIA.'-'.Session::get('empresas_meta')->NOM_EMPR;





		//dd($titulo);
	    Excel::create($titulo, function($excel) use ($listasaldoinicial,$listaperido,$funcion,$anio,$empresa_id,$tipo_producto_id) {

	        $excel->sheet('Saldo Inicial', function($sheet) use ($listasaldoinicial,$listaperido,$funcion,$anio,$empresa_id,$tipo_producto_id){
	            $sheet->loadView('kardex/excel/esaldoinicial')->with('listasaldoinicial',$listasaldoinicial)
	            												 ->with('listaperido',$listaperido)
	            												 ->with('anio',$anio)
	            												 ->with('tipo_producto_id',$tipo_producto_id)
	            												 ->with('funcion',$funcion);         
	        });

	        foreach($listasaldoinicial as $index => $item){

		        $saldoinicial 			= 	$funcion->kd_saldo_inicial_producto_id($empresa_id,$tipo_producto_id,$item->producto_id);

		    	$listadetalleproducto 	= 	$funcion->kd_lista_producto_periodo_cascara_view($empresa_id, 
		    																 $anio, 
		    																 '',
		    																 $item->producto_id,
		    																 '');
				$producto 				= 	ALMProducto::where('COD_PRODUCTO','=',$item->producto_id)->first();
				$periodo_enero 			= 	CONPeriodo::where('COD_ANIO','=',$anio)
											->where('COD_MES','=',1)
											->where('COD_EMPR','=',$empresa_id)
											->first();

			    $listakardexif 			= 	$funcion->kd_lista_kardex_inventario_cascara_final($empresa_id, 
			    																		$saldoinicial,
			    																		$listadetalleproducto,
			    																		$producto,
			    																		$periodo_enero);

			    $nombreproducto 		= 	$producto->NOM_PRODUCTO;
			    $titulo 				= 	str_replace("Ñ", "N", substr($nombreproducto, 0, 25));

		        $excel->sheet($titulo, function($sheet) use ($listakardexif,$nombreproducto) {
		            $sheet->loadView('kardex/excel/edetallekardexif')->with('listakardexif',$listakardexif)
		            												 ->with('nombreproducto',$nombreproducto);         
	        	});
	        }
	    })->export('xls');

	}


	public function actionAjaxListarMovimientoKardex(Request $request)
	{


		set_time_limit(0);
		//$anio 					=   $request['anio'];
		$tipo_producto_id 		=   'TPK0000000000004';
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;
		$tipo_asiento_id        =   '';
		$idopcion 				=   $request['idopcion'];
		$funcion 				= 	$this;
	    $listasaldoinicial 		= 	$this->kd_lista_saldo_inicial(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id);

		return View::make('kardex/ajax/alistakardexcascara',
						 [
						 	'listasaldoinicial'      => $listasaldoinicial,			 	
						 	'idopcion' 				 => $idopcion,
						 	'tipo_asiento_id' 		 => $tipo_asiento_id,
						 	'tipo_producto_id' 		 => $tipo_producto_id,
						 	'funcion' 				 => $funcion,
						 	'ajax' 					 => true,						 	
						 ]);
	}




	public function actionAjaxModalDetalleTotalKardex(Request $request)
	{

		set_time_limit(0);

		//$anio 					=   $request['anio'];
		$tipo_producto_id 		=   'TPK0000000000004';
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;

	    $saldoincial 			= 	WEBKardexProducto::where('empresa_id','=',$empresa_id)
	    							->where('tipo_producto_id','=',$tipo_producto_id)
	    							->where('activo','=',1)
									->orderBy('fecha_saldo_inicial', 'asc')
			    					->first();
		$data_anio 				= 	substr($saldoincial->fecha_saldo_inicial, 0, 4);
		$data_producto_id 		=   $request['data_producto_id'];
		$data_periodo_id 		=   $request['data_periodo_id'];
		$data_tipo_asiento_id 	=   $request['data_tipo_asiento_id'];
		$tipo_producto_id 		=   $request['tipo_producto_id'];


		$producto 				= 	ALMProducto::where('COD_PRODUCTO','=',$data_producto_id)->first();
		$periodo_enero 			= 	CONPeriodo::where('COD_ANIO','=',$data_anio)
									->where('COD_MES','=',1)
									->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
									->first();

		$idopcion 				=   $request['idopcion'];

		$funcion 				= 	$this;


	    $saldoinicial 			= 	$this->kd_saldo_inicial_producto_id(Session::get('empresas_meta')->COD_EMPR,
	    																	$tipo_producto_id,
	    																	$data_producto_id);

	    $listadetalleproducto 	= 	$this->kd_lista_producto_periodo_cascara_view(Session::get('empresas_meta')->COD_EMPR, 
			    															$data_anio, 
			    															$data_tipo_asiento_id,
			    															$data_producto_id,
			    															$data_periodo_id);

	    $listakardexif 			= 	$this->kd_lista_kardex_inventario_cascara_final(Session::get('empresas_meta')->COD_EMPR, 
	    																	$saldoinicial,
	    																	$listadetalleproducto,
	    																	$producto,
	    																	$periodo_enero);


		return View::make('kardex/modal/ajax/adetallekardexifcascara',
						 [
						 	'listakardexif' 	=> $listakardexif,
						 	'producto' 			=> $producto,
						 	'periodo_enero' 	=> $periodo_enero,					 	
						 	'idopcion' 			=> $idopcion,
						 	'ajax' 				=> true,						 	
						 ]);
	}






}
