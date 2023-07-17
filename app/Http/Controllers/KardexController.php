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
	    $anio  					=   $this->anio;
		$idopcion 				=   $request['idopcion'];
	    $listasaldoinicial 		= 	$this->kd_lista_saldo_inicial(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id);
		$funcion 				= 	$this;

		return View::make('kardex/ajax/alistasaldoinicial',
						 [
						 	'listasaldoinicial' 	=> $listasaldoinicial,
						 	'tipo_producto_id' 		=> $tipo_producto_id,
						 	'anio' 					=> $anio,					 	
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


		set_time_limit(0);
		$anio 					=   $request['anio'];
		$tipo_movimiento_id 	=   $request['tipo_movimiento_id'];
		$tipo_producto_id 		=   $request['tipo_producto_id'];
		$tipo_asiento_id        =   '';
		$idopcion 				=   $request['idopcion'];
		$funcion 				= 	$this;
		$cod_almacen 			= 	'ITCHAL0000000026';
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;
	    $listaperido 			= 	$this->gn_lista_periodo($anio,Session::get('empresas_meta')->COD_EMPR);	

		if(Session::get('empresas_meta')->COD_EMPR == 'IACHEM0000001339'){
			$cod_almacen 			= 	'ICCHAL0000000109';
		}

		//materiales auxiliares
		if($tipo_producto_id=='TPK0000000000003'){

			$tipo_asiento_id    	=   'TAS0000000000004';

			$fecha_inicio			= 	$anio.'-01-01';
			$fecha_fin				= 	$anio.'-12-31';

			$listarequerimiento 	=   $this->kd_lista_requerimiento($empresa_id,$fecha_inicio,$fecha_fin);

			$clistarequerimiento 	=   collect($listarequerimiento);



			$listamovimientocommpra = 	$this->kd_lista_materialesauxiliares(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id,$cod_almacen);

			$arraymovimientocommpra = 	$this->kd_array_materialesauxiliares(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id,$cod_almacen,$listamovimientocommpra);


			return View::make('kardex/ajax/alistamovimientokardexmaux',
							 [
							 	'listamovimientocommpra' => $listamovimientocommpra,
							 	'arraymovimientocommpra' => $arraymovimientocommpra,				 	
							 	'idopcion' 				 => $idopcion,
							 	'anio' 					 => $anio,
							 	'tipo_asiento_id' 		 => $tipo_asiento_id,
							 	'tipo_producto_id' 		 => $tipo_producto_id,
							 	'listaperido' 		 	 => $listaperido,
							 	'listarequerimiento' 	 => $clistarequerimiento,
							 	'funcion' 				 => $funcion,
							 	'ajax' 					 => true,						 	
							 ]);
		}


		$tipo_asiento_id    	=   'TAS0000000000003';

	    $listasaldoinicial 		= 	$this->kd_lista_saldo_inicial(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id);

		$listamovimiento 		= 	$this->kd_lista_movimiento(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id);


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


	public function actionAjaxModalAsientoContableKardex(Request $request)
	{

		$data_tipo_producto_id 	=   $request['data_tipo_producto_id'];
		$monto_total 			=   $request['monto_total'];
		$periodo 				=   $request['periodo'];
		$data_anio 				=   $request['data_anio'];
		$idopcion 				=   $request['idopcion'];

		$periodo 				= 	CONPeriodo::where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
									->where('COD_ANIO','=',$data_anio)
									->where('COD_MES','=',$periodo)
									->first();
		$tipoproducto 			= 	CMPCategoria::where('COD_CATEGORIA','=',$data_tipo_producto_id)->first();
		$empresa_id 			=   Session::get('empresas_meta')->COD_EMPR;
	    $cabecera_asiento 		= 	$this->kd_cabecera_asiento($periodo,$empresa_id,$monto_total,$tipoproducto);
	    $detalle_asiento 		= 	$this->kd_detalle_asiento($periodo,$empresa_id,$monto_total,$data_anio,$tipoproducto);



		return View::make('kardex/modal/ajax/adetalleasientocontable',
						 [
						 	'periodo' 	=> $periodo,
						 	'tipoproducto' 	=> $tipoproducto,
						 	'cabecera_asiento' 	=> $cabecera_asiento,	
						 	'detalle_asiento' 	=> $detalle_asiento,					 	
						 	'idopcion' 				=> $idopcion,
						 	'ajax' 					=> true,						 	
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


		set_time_limit(0);
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

		$funcion 				= 	$this;

	    $saldoinicial 			= 	$this->kd_saldo_inicial_producto_id(Session::get('empresas_meta')->COD_EMPR,
	    																	$tipo_producto_id,
	    																	$data_producto_id);

	    $listadetalleproducto 	= 	$this->kd_lista_producto_periodo_view(Session::get('empresas_meta')->COD_EMPR, 
			    															$data_anio, 
			    															$data_tipo_asiento_id,
			    															$data_producto_id,
			    															$data_periodo_id);

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
		$idopcion 				=   $request['idopcion'];
		$cod_almacen 			= 	'ITCHAL0000000026';
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;
	    $listaperido 			= 	$this->gn_lista_periodo($anio,Session::get('empresas_meta')->COD_EMPR);	
		$funcion 				= 	$this;
		if(Session::get('empresas_meta')->COD_EMPR == 'IACHEM0000001339'){
			$cod_almacen 			= 	'ICCHAL0000000109';
		}


		//materiales auxiliares
		if($tipo_producto_id=='TPK0000000000003'){

			$tipo_asiento_id    	=   'TAS0000000000004';

			$fecha_inicio			= 	$anio.'-01-01';
			$fecha_fin				= 	$anio.'-12-31';

			$listarequerimiento 	=   $this->kd_lista_requerimiento($empresa_id,$fecha_inicio,$fecha_fin);

			$clistarequerimiento 	=   collect($listarequerimiento);

			$listamovimientocommpra = 	$this->kd_lista_materialesauxiliares(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id,$cod_almacen);

			$arraymovimientocommpra = 	$this->kd_array_materialesauxiliares(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id,$cod_almacen,$listamovimientocommpra);

			$titulo 				=   'KARDEX-'.$tipoproducto->NOM_CATEGORIA.'-'.Session::get('empresas_meta')->NOM_EMPR;

		    Excel::create($titulo, function($excel) use ($listamovimientocommpra,$arraymovimientocommpra,$idopcion,$anio,$tipo_asiento_id,$tipo_producto_id,$listaperido,$clistarequerimiento,$funcion) {

		        $excel->sheet('Materiales_auxiliares', function($sheet) use ($listamovimientocommpra,$arraymovimientocommpra,$idopcion,$anio,$tipo_asiento_id,$tipo_producto_id,$listaperido,$clistarequerimiento,$funcion) {
		            $sheet->loadView('kardex/excel/ematerialesauxiliares')->with('listamovimientocommpra',$listamovimientocommpra)
		            												 ->with('arraymovimientocommpra',$arraymovimientocommpra)
		            												 ->with('idopcion',$idopcion)
		            												 ->with('anio',$anio)
		            												 ->with('tipo_asiento_id',$tipo_asiento_id)
		            												 ->with('tipo_producto_id',$tipo_producto_id)
		            												 ->with('listaperido',$listaperido)
		            												 ->with('listarequerimiento',$clistarequerimiento)
		            												 ->with('funcion',$funcion);         
		        });

		    })->export('xls');




		}else{


			$tipo_asiento_id    	=   'TAS0000000000003';

		    $listasaldoinicial 		= 	$this->kd_lista_saldo_inicial(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id);
			$listamovimiento 		= 	$this->kd_lista_movimiento(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id);

			$tipo_asiento_id    	=   'TAS0000000000004';
			$listamovimientocommpra = 	$this->kd_lista_movimiento(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id);

			$titulo 				=   'KARDEX-'.$tipoproducto->NOM_CATEGORIA.'-'.Session::get('empresas_meta')->NOM_EMPR;

		    Excel::create($titulo, function($excel) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio,$empresa_id,$tipo_producto_id) {

		        $excel->sheet('Inventario Final', function($sheet) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio,$tipo_producto_id) {
		            $sheet->loadView('kardex/excel/einventariofinal')->with('listasaldoinicial',$listasaldoinicial)
		            												 ->with('listamovimiento',$listamovimiento)
		            												 ->with('listamovimientocommpra',$listamovimientocommpra)
		            												 ->with('listaperido',$listaperido)
		            												 ->with('anio',$anio)
		            												 ->with('tipo_producto_id',$tipo_producto_id)
		            												 ->with('funcion',$funcion);         
		        });

		        $excel->sheet('Saldo Inicial', function($sheet) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio,$tipo_producto_id) {
		            $sheet->loadView('kardex/excel/esaldoinicial')->with('listasaldoinicial',$listasaldoinicial)
		            												 ->with('listamovimiento',$listamovimiento)
		            												 ->with('listamovimientocommpra',$listamovimientocommpra)
		            												 ->with('listaperido',$listaperido)
		            												 ->with('anio',$anio)
		            												 ->with('tipo_producto_id',$tipo_producto_id)
		            												 ->with('funcion',$funcion);         
		        });

		        $excel->sheet('Ventas Consolidado', function($sheet) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio,$tipo_producto_id) {
		            $sheet->loadView('kardex/excel/eventasconsolidado')->with('listasaldoinicial',$listasaldoinicial)
		            												 ->with('listamovimiento',$listamovimiento)
		            												 ->with('listamovimientocommpra',$listamovimientocommpra)
		            												 ->with('listaperido',$listaperido)
		            												 ->with('anio',$anio)
		            												 ->with('tipo_producto_id',$tipo_producto_id)
		            												 ->with('funcion',$funcion);         
		        });


		        $excel->sheet('Compras Consolidado', function($sheet) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio,$tipo_producto_id) {
		            $sheet->loadView('kardex/excel/ecomprasconsolidado')->with('listasaldoinicial',$listasaldoinicial)
		            												 ->with('listamovimiento',$listamovimiento)
		            												 ->with('listamovimientocommpra',$listamovimientocommpra)
		            												 ->with('listaperido',$listaperido)
		            												 ->with('anio',$anio)
		            												 ->with('tipo_producto_id',$tipo_producto_id)
		            												 ->with('funcion',$funcion);         
		        });


		        $excel->sheet('Costo Inventario Final', function($sheet) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio,$tipo_producto_id) {
		            $sheet->loadView('kardex/excel/einventariofinalcosto')->with('listasaldoinicial',$listasaldoinicial)
		            												 ->with('listamovimiento',$listamovimiento)
		            												 ->with('listamovimientocommpra',$listamovimientocommpra)
		            												 ->with('listaperido',$listaperido)
		            												 ->with('anio',$anio)
		            												 ->with('tipo_producto_id',$tipo_producto_id)
		            												 ->with('funcion',$funcion);         
		        });


		        $excel->sheet('Costo Ventas Consolidado', function($sheet) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio,$tipo_producto_id) {
		            $sheet->loadView('kardex/excel/eventasconsolidadocosto')->with('listasaldoinicial',$listasaldoinicial)
		            												 ->with('listamovimiento',$listamovimiento)
		            												 ->with('listamovimientocommpra',$listamovimientocommpra)
		            												 ->with('listaperido',$listaperido)
		            												 ->with('anio',$anio)
		            												 ->with('tipo_producto_id',$tipo_producto_id)
		            												 ->with('funcion',$funcion);         
		        });

		        $excel->sheet('Costo Compras Consolidado', function($sheet) use ($listasaldoinicial,$listamovimiento,$listamovimientocommpra,$listaperido,$funcion,$anio,$tipo_producto_id) {
		            $sheet->loadView('kardex/excel/ecomprasconsolidadocosto')->with('listasaldoinicial',$listasaldoinicial)
		            												 ->with('listamovimiento',$listamovimiento)
		            												 ->with('listamovimientocommpra',$listamovimientocommpra)
		            												 ->with('listaperido',$listaperido)
		            												 ->with('anio',$anio)
		            												 ->with('tipo_producto_id',$tipo_producto_id)
		            												 ->with('funcion',$funcion);         
		        });

		        foreach($listasaldoinicial as $index => $item){



			        $saldoinicial 			= 	$funcion->kd_saldo_inicial_producto_id($empresa_id,$tipo_producto_id,$item->producto_id);

			    	$listadetalleproducto 	= 	$funcion->kd_lista_producto_periodo_view($empresa_id, 
			    																 $anio, 
			    																 '',
			    																 $item->producto_id,
			    																 '');
					$producto 				= 	ALMProducto::where('COD_PRODUCTO','=',$item->producto_id)->first();
					$periodo_enero 			= 	CONPeriodo::where('COD_ANIO','=',$anio)
												->where('COD_MES','=',1)
												->where('COD_EMPR','=',$empresa_id)
												->first();

				    $listakardexif 			= 	$funcion->kd_lista_kardex_inventario_final($empresa_id, 
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










	}




}
