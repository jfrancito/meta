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


class KardexController extends Controller
{

	use GeneralesTraits;
	use KardexTraits;
	use PlanContableTraits;
	use MovilidadTraits;



	public function actionDescargarArchivoPle(Request $request)
	{


		set_time_limit(0);

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$data_archivo 			=   $request['data_archivo'];

		$documento 				=   $request['documento'];
		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
	   	$mes 					= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT); 

	    if($data_archivo == 'ple'){

			$empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
			$tipo_producto_id 		=	'TPK0000000000001';
			//insertar envases ;
			$this->kd_insertar_envases_saldoinicial_kardex($empresa_id,$tipo_producto_id);
		    $listasaldoinicial 		= 	$this->kd_lista_saldo_inicial($empresa_id,$tipo_producto_id);

			$nombre 				= 	$this->kd_crear_nombre_venta($anio,$mes).'.txt';
			$path 					= 	storage_path('kardex/ple/'.$nombre);
	    	$this->kd_archivo_ple_kardex($anio,$mes,$listasaldoinicial,$nombre,$path,$tipo_producto_id);
		    if (file_exists($path)){
		        return Response::download($path);
		    }	 

	    }else{
			    if($data_archivo == 'registro'){

					$empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
					$tipo_producto_id 		=	'TPK0000000000001';
					//insertar envases ;
					$this->kd_insertar_envases_saldoinicial_kardex($empresa_id,$tipo_producto_id);
				    $listasaldoinicial 		= 	$this->kd_lista_saldo_inicial($empresa_id,$tipo_producto_id);
					$nombre 				= 	$this->kd_crear_nombre_venta($anio,$mes).'.txt';
					$path 					= 	storage_path('kardex/'.$nombre);
			    	$lista_asiento 			= 	$this->kd_archivo_ple_kardex($anio,$mes,$listasaldoinicial,$nombre,$path,$tipo_producto_id);
					$titulo 				=   'KARDEX-EVASES-'.Session::get('empresas_meta')->NOM_EMPR;
				    Excel::create($titulo, function($excel) use ($lista_asiento,$periodo) {
				        $excel->sheet($periodo->TXT_CODIGO, function($sheet) use ($lista_asiento,$periodo) {
				            $sheet->loadView('kardex/excel/listakardexenvases')->with('lista_asiento',$lista_asiento);         
				        });
				    })->export('xls');
			    }
	    	}

	}


	public function actionAjaxBuscarListaPleKardex(Request $request)
	{

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$documento 				=   $request['documento'];
		$idopcion 				=   $request['idopcion'];
		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
	   	$mes 					= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT);
		$empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
		$tipo_producto_id 		=	'TPK0000000000001';
		//insertar envases ;
		$this->kd_insertar_envases_saldoinicial_kardex($empresa_id,$tipo_producto_id);
	    $listasaldoinicial 		= 	$this->kd_lista_saldo_inicial($empresa_id,$tipo_producto_id);

		$nombre 				= 	$this->kd_crear_nombre_venta($anio,$mes).'.txt';
		$path 					= 	storage_path('kardex/'.$nombre);
    	$lista_asiento 			= 	$this->kd_archivo_ple_kardex($anio,$mes,$listasaldoinicial,$nombre,$path,$tipo_producto_id);

		return View::make('kardex/ajax/alistakardexenvase',
						 [
						 	'lista_asiento'			=> $lista_asiento,					 	
						 	'idopcion' 				=> $idopcion,
						 	'ajax' 					=> true,					 	
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


			$tipo_asiento_id    		 =   'TAS0000000000003';

		    $listasaldoinicial 			 = 	 $this->kd_lista_saldo_inicial(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id);
			$listamovimiento 			 = 	 $this->kd_lista_movimiento(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id);

			$tipo_asiento_id    		 =   'TAS0000000000004';
			$listamovimientocommpra 	 = 	$this->kd_lista_movimiento(Session::get('empresas_meta')->COD_EMPR, $anio, $tipo_producto_id,$tipo_asiento_id);


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



	public function actionGestionKardexEnvasesPle($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Kardex envases - PLE');
	    $sel_tipo_asiento 		=	'';
	    $sel_periodo 			=	'';

	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$funcion 				= 	$this;

		$lista_kardex           =   array();

		return View::make('kardex/descargararchivopleenvase',
						 [
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_periodo'	 		=> $sel_periodo,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'lista_kardex' 		=> $lista_kardex,						 	
						 ]);
	}

	public function actionConfigurarELiminarItemKardex($idopcion,$codigo,Request $request)
	{

		WEBKardexTransferencia::where('codigo','=',$codigo)
								->update([	'activo' => 0,
											'cu' => 0,
											'cantidad' => 0,
											'importe' => 0,
											'fecha_mod' 	=> $this->fechaactual,
											'usuario_mod' 	=> Session::get('usuario_meta')->id
										 ]);
		return Redirect::to('/gestion-transferencia-cantidades-productos/'.$idopcion)->with('bienhecho', 'Transferencia ('.$codigo.') elminado con exito');

	}
	public function actionAjaxCalcularUltimoCU(Request $request)
	{
		$idopcion 				=   $request['idopcion'];
		$producto_salida_id 	=   $request['producto_salida_id'];
		$fecha 					=   $request['fecha'];				
		$tipo_producto_id 		=	'TPK0000000000001';



	    $fechaSegundos = strtotime($fecha);
	    $dia = date( "j", $fechaSegundos);
	    $mes = date("n", $fechaSegundos);
	    $anio =  date("Y", $fechaSegundos);
	    $data_tipo_asiento_id = '';
	    $data_periodo_id='';
	    $fecha 					= date_format(date_create($fecha), 'Y-m-d');
		$periodo_enero 			= 	CONPeriodo::where('COD_ANIO','=',$anio)
									->where('COD_MES','=',1)
									->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
									->first();

		$producto 				= 	ALMProducto::where('COD_PRODUCTO','=',$producto_salida_id)->first();
	    $saldoinicial 			= 	$this->kd_saldo_inicial_producto_id(Session::get('empresas_meta')->COD_EMPR,
	    																	$tipo_producto_id,
	    																	$producto_salida_id);

	    $listadetalleproducto 	= 	$this->kd_lista_producto_periodo_view(Session::get('empresas_meta')->COD_EMPR, 
			    															$anio,
			    															$data_tipo_asiento_id,
			    															$producto_salida_id,
			    															$data_periodo_id);

	    $listakardexif 			= 	$this->kd_lista_kardex_inventario_final(Session::get('empresas_meta')->COD_EMPR, 
	    																	$saldoinicial,
	    																	$listadetalleproducto,
	    																	$producto,
	    																	$periodo_enero);
	    $cu = -1;
		foreach($listakardexif as $item){
			$fechal = $item['fecha'];
			if($fechal == $fecha){
	    		$cu = $item['saldo_cu'];				
			}
		}
		print_r($cu);
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

	    // dd($listakardexif);


		return View::make('kardex/modal/ajax/adetallekardexif',
						 [
						 	'listakardexif' 	=> $listakardexif,
						 	'producto' 			=> $producto,
						 	'periodo_enero' 	=> $periodo_enero,					 	
						 	'idopcion' 			=> $idopcion,
						 	'ajax' 				=> true,						 	
						 ]);
	}

	public function actionGuardarKardexCuentaContable($idopcion,Request $request)
	{

		$cabecera 				=   json_decode($request['cabecera'],false);
		$detalle 				=   json_decode($request['detalle'],false);
		$periodo_id 			=   $request['periodog_id'];
		$empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
		$centro_id 				=	'CEN0000000000001';
		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
		$tipo_asiento_id		=	'TAS0000000000007';
		$tipo_referencia		=	'TAS0000000000007';

		//CABECERA
		foreach($cabecera as $index => $item){


			$IND_TIPO_OPERACION = 'I';
			$COD_ASIENTO = '';
			$COD_EMPR = $empresa_id;
			$COD_CENTRO = $centro_id;
			$COD_PERIODO = $periodo->COD_PERIODO;
			$COD_CATEGORIA_TIPO_ASIENTO = 'TAS0000000000007';
			$TXT_CATEGORIA_TIPO_ASIENTO = 'DIARIO';
			$NRO_ASIENTO = '';
			$FEC_ASIENTO = substr($periodo->FEC_FIN, 0, 10);
			$TXT_GLOSA = $item->glosa;

			$COD_CATEGORIA_ESTADO_ASIENTO = 'IACHTE0000000025';
			$TXT_CATEGORIA_ESTADO_ASIENTO = 'CONFIRMADO';
			$COD_CATEGORIA_MONEDA = $item->moneda_id;
			$TXT_CATEGORIA_MONEDA = $item->moneda;
			$CAN_TIPO_CAMBIO = $item->tipo_cambio;
			$CAN_TOTAL_DEBE = $item->total_debe;
			$CAN_TOTAL_HABER = $item->total_haber;
			$COD_ASIENTO_EXTORNO = '';
			$COD_ASIENTO_EXTORNADO = '';
			$IND_EXTORNO = '0';

			$COD_ASIENTO_MODELO = '';
			$TXT_TIPO_REFERENCIA = $item->tipo_referencia;
			$TXT_REFERENCIA = '';
			$COD_ESTADO = '1';
			$COD_USUARIO_REGISTRO = Session::get('usuario_meta')->id;
			$COD_MOTIVO_EXTORNO = '';
			$GLOSA_EXTORNO = '';
			$COD_EMPR_CLI = '';
			$TXT_EMPR_CLI = '';
			$COD_CATEGORIA_TIPO_DOCUMENTO = '';

			$TXT_CATEGORIA_TIPO_DOCUMENTO = '';
			$NRO_SERIE = '';
			$NRO_DOC = '';
			$FEC_DETRACCION = '';
			$NRO_DETRACCION = '';
			$CAN_DESCUENTO_DETRACCION = '0';
			$CAN_TOTAL_DETRACCION = '0';
			$COD_CATEGORIA_TIPO_DOCUMENTO_REF = '';
			$TXT_CATEGORIA_TIPO_DOCUMENTO_REF = '';
			$NRO_SERIE_REF = '';
			$NRO_DOC_REF = '';
			$FEC_VENCIMIENTO = '';
			$IND_AFECTO = '0';

			$asiento_id				=   $this->gn_encontrar_cod_asiento($empresa_id, $centro_id, 
														$periodo_id, $tipo_asiento_id,$item->tipo_referencia);

			$anular_asiento 		=   $this->movilidad_anular_asiento($asiento_id,
																		Session::get('usuario_meta')->name,$this->fechaactual);



    		$asientocontable     	= 	$this->gn_crear_asiento_contable($IND_TIPO_OPERACION,
												$COD_ASIENTO,
												$COD_EMPR,
												$COD_CENTRO,
												$COD_PERIODO,
												$COD_CATEGORIA_TIPO_ASIENTO,
												$TXT_CATEGORIA_TIPO_ASIENTO,
												$NRO_ASIENTO,
												$FEC_ASIENTO,
												$TXT_GLOSA,
												
												$COD_CATEGORIA_ESTADO_ASIENTO,
												$TXT_CATEGORIA_ESTADO_ASIENTO,
												$COD_CATEGORIA_MONEDA,
												$TXT_CATEGORIA_MONEDA,
												$CAN_TIPO_CAMBIO,
												$CAN_TOTAL_DEBE,
												$CAN_TOTAL_HABER,
												$COD_ASIENTO_EXTORNO,
												$COD_ASIENTO_EXTORNADO,
												$IND_EXTORNO,

												$COD_ASIENTO_MODELO,
												$TXT_TIPO_REFERENCIA,
												$TXT_REFERENCIA,
												$COD_ESTADO,
												$COD_USUARIO_REGISTRO,
												$COD_MOTIVO_EXTORNO,
												$GLOSA_EXTORNO,
												$COD_EMPR_CLI,
												$TXT_EMPR_CLI,
												$COD_CATEGORIA_TIPO_DOCUMENTO,

												$TXT_CATEGORIA_TIPO_DOCUMENTO,
												$NRO_SERIE,
												$NRO_DOC,
												$FEC_DETRACCION,
												$NRO_DETRACCION,
												$CAN_DESCUENTO_DETRACCION,
												$CAN_TOTAL_DETRACCION,
												$COD_CATEGORIA_TIPO_DOCUMENTO_REF,
												$TXT_CATEGORIA_TIPO_DOCUMENTO_REF,
												$NRO_SERIE_REF,

												$NRO_DOC_REF,
												$FEC_VENCIMIENTO,
												$IND_AFECTO);

		}


		//DETALLE

		foreach($detalle as $index => $item){

			$IND_TIPO_OPERACION = 'I';
			$COD_ASIENTO_MOVIMIENTO = '';
			$COD_EMPR = $empresa_id;
			$COD_CENTRO = $centro_id;
			$COD_ASIENTO = $asientocontable;
			$COD_CUENTA_CONTABLE = $item->cuenta_id;
			$TXT_CUENTA_CONTABLE = $item->glosa;
			$TXT_GLOSA = $item->glosa;
			$CAN_DEBE_MN = $item->total_debe;
			$CAN_HABER_MN = $item->total_haber;

			$CAN_DEBE_ME = $item->total_debe_dolar;
			$CAN_HABER_ME = $item->total_haber_dolar;
			$NRO_LINEA = $item->linea;
			$COD_CUO = '';
			$IND_EXTORNO = '0';
			$TXT_TIPO_REFERENCIA = '';
			$TXT_REFERENCIA = '';
			$COD_ESTADO = '1';
			$COD_USUARIO_REGISTRO = Session::get('usuario_meta')->id;
			$COD_DOC_CTBLE_REF = '';

			$COD_ORDEN_REF = '';

    		$detalle     	= 	$this->gn_crear_detalle_asiento_contable(	$IND_TIPO_OPERACION,
														$COD_ASIENTO_MOVIMIENTO,
														$COD_EMPR,
														$COD_CENTRO,
														$COD_ASIENTO,
														$COD_CUENTA_CONTABLE,
														$TXT_CUENTA_CONTABLE,
														$TXT_GLOSA,
														$CAN_DEBE_MN,
														$CAN_HABER_MN,

														$CAN_DEBE_ME,
														$CAN_HABER_ME,
														$NRO_LINEA,
														$COD_CUO,
														$IND_EXTORNO,
														$TXT_TIPO_REFERENCIA,
														$TXT_REFERENCIA,
														$COD_ESTADO,
														$COD_USUARIO_REGISTRO,
														$COD_DOC_CTBLE_REF,

														$COD_ORDEN_REF);

		}	


		return Redirect::to('/gestion-movimiento-kardex/'.$idopcion)->with('bienhecho', 'Registro cuenta contable exitoso');
	}
	public function actionConfigurarTransferenciaProducto($idopcion,Request $request)
	{

		if($_POST)
		{


			$producto_salida_id 	 		= 	$request['producto_salida_id'];
			$producto_ingreso_id 	 		= 	$request['producto_ingreso_id'];
			$fecha 	 	 					= 	$request['fecha'];
			$empresa_id 					= 	Session::get('empresas_meta')->COD_EMPR;

			$cantidad 	 					= 	floatval(str_replace(",", "", $request['cantidad']));
			$cu 	 						= 	floatval(str_replace(",", "", $request['cu']));
			$importe 	 					= 	floatval(str_replace(",", "", $request['importe']));

			$codigo 						= 	$this->funciones->generar_codigo('web.kardextransferencias',8);
			$producto 						=	ALMProducto::where('COD_PRODUCTO','=',$producto_salida_id)->first();

		    $fechaSegundos = strtotime($fecha);
		    $dia = date( "j", $fechaSegundos);
		    $mes = date("n", $fechaSegundos);
		    $anio =  date("Y", $fechaSegundos);

		    $periodo 						=	CONPeriodo::where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
		    									->where('COD_ANIO','=',$anio)
		    									->where('COD_MES','=',$mes)
		    									->first();

			$kardexproducto 				=	WEBKardexProducto::where('producto_id','=',$producto->COD_PRODUCTO)
												->where('empresa_id','=',$empresa_id)
												->first();
			$correlativo 					= 	$this->funciones->generar_codigo_transferencia($kardexproducto->correlativosalida,7);
			$kardexproducto->correlativosalida = $correlativo;
			$kardexproducto->save();

			$seriesalida 								=   $kardexproducto->seriesalida;
			$rucsalida									=   $kardexproducto->ruc;
			if(is_null($seriesalida)){
				$seriesalida 								=   '';
				$rucsalida 								=   '';				
			}




			$id 										=   $this->funciones->getCreateIdMaestra('web.kardextransferencias');
			$cabecera            	 					=	new WEBKardexTransferencia;
			$cabecera->id 	     	 					=   $id;
			$cabecera->codigo 	     	 				=   $codigo;
			$cabecera->COD_PERIODO 	     	 			=   $periodo->COD_PERIODO;
			$cabecera->NOMBRE_PERIODO 	     	 		=   $periodo->TXT_NOMBRE;
			$cabecera->TXT_CATEGORIA_TIPO_DOCUMENTO 	=   'TRANSFERENCIA';
			$cabecera->COD_CATEGORIA_TIPO_ASIENTO 		=   'TAS0000000000003';			
			$cabecera->TXT_CATEGORIA_TIPO_ASIENTO 		=   'VENTAS';

			$cabecera->producto_id 	   					=   $producto_salida_id;
			$cabecera->producto_nombre 					=   $producto->NOM_PRODUCTO;
			$cabecera->ingreso_salida 					=   'SALIDA';
			$cabecera->cantidad 						=   $cantidad;

			$cabecera->serie 							=   $seriesalida;
			$cabecera->correlativo 						=   $correlativo;
			$cabecera->ruc 								=   $rucsalida;


			$cabecera->cu 								=   $cu;
			$cabecera->importe 							=   $importe;

			$cabecera->fecha 	   						=   $fecha;
			$cabecera->empresa_id 	 					=   Session::get('empresas_meta')->COD_EMPR;
			$cabecera->fecha_crea 	 					=   $this->fechaactual;
			$cabecera->usuario_crea 					=   Session::get('usuario_meta')->id;
			$cabecera->save();


			$productoi 									=	ALMProducto::where('COD_PRODUCTO','=',$producto_ingreso_id)->first();
			$kardexproducto 							=	WEBKardexProducto::where('producto_id','=',$productoi->COD_PRODUCTO)
															->where('empresa_id','=',$empresa_id)
															->first();



			$serieingreso 								=   $kardexproducto->serieingreso;
			$rucingreso 								=   $kardexproducto->ruc;
			if(is_null($serieingreso)){
				$serieingreso 								=   '';
				$rucingreso 								=   '';				
			}


			$correlativo 								= 	$this->funciones->generar_codigo_transferencia($kardexproducto->correlativoingreso,7);
			$kardexproducto->correlativoingreso 		= 	$correlativo;
			$kardexproducto->save();


			$id 										=   $this->funciones->getCreateIdMaestra('web.kardextransferencias');
			$cabecera            	 					=	new WEBKardexTransferencia;
			$cabecera->id 	     	 					=   $id;
			$cabecera->codigo 	     	 				=   $codigo;
			
			$cabecera->COD_PERIODO 	     	 			=   $periodo->COD_PERIODO;
			$cabecera->NOMBRE_PERIODO 	     	 		=   $periodo->TXT_NOMBRE;
			$cabecera->TXT_CATEGORIA_TIPO_DOCUMENTO 	=   'TRANSFERENCIA';
			$cabecera->COD_CATEGORIA_TIPO_ASIENTO 		=   'TAS0000000000004';			
			$cabecera->TXT_CATEGORIA_TIPO_ASIENTO 		=   'COMPRAS';			

			$cabecera->producto_id 	   					=   $producto_ingreso_id;
			$cabecera->producto_nombre 					=   $productoi->NOM_PRODUCTO;
			$cabecera->ingreso_salida 					=   'INGRESO';
			$cabecera->cantidad 						=   $cantidad;

			$cabecera->serie 							=   $serieingreso;
			$cabecera->correlativo 						=   $correlativo;
			$cabecera->ruc 								=   $rucingreso;

			$cabecera->cu 								=   $cu;
			$cabecera->importe 							=   $importe;

			$cabecera->fecha 	   						=   $fecha;
			$cabecera->empresa_id 	 					=   Session::get('empresas_meta')->COD_EMPR;
			$cabecera->fecha_crea 	 					=   $this->fechaactual;
			$cabecera->usuario_crea 					=   Session::get('usuario_meta')->id;
			$cabecera->save();

 			return Redirect::to('/gestion-transferencia-cantidades-productos/'.$idopcion)->with('bienhecho', 'Transferencia '.$producto->NOM_PRODUCTO.' al '.$productoi->NOM_PRODUCTO.' creado con éxito');

 		}
	}
	public function actionAjaxModalConfiguracionTranferenciaPorducto(Request $request)
	{

		$idopcion 				=   $request['idopcion'];
		$anio  					=   $this->anio;
		$funcion 				= 	$this;
		$combo_producto 		=   $this->gn_generacion_combo_producto_kardex('Seleccione producto','');

		return View::make('kardex/modal/ajax/aconfiguracionkardex',
						 [		 	
						 	'combo_producto' 		=> $combo_producto,
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'anio' 					=> $anio,
						 	'ajax' 					=> true,						 	
						 ]);
	}
	public function actionTransferenciaCantidadesProductos($idopcion,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Transferencia de Cantidad Productos');

	    $listatranferencia  	=	WEBKardexTransferencia::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('activo','=','1')->get();

        return View::make('kardex/transferenciacantidad', 
        				[
        					'idopcion'  			=> $idopcion,
        					'listatranferencia'  	=> $listatranferencia,
        				]);

	}

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




		$empresa_id 	=	Session::get('empresas_meta')->COD_EMPR;
		//insertar envases ;
		$this->kd_insertar_envases_saldoinicial_kardex($empresa_id,$tipo_producto_id);


	    $listasaldoinicial 		= 	$this->kd_lista_saldo_inicial(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id);
		$tipo_asiento_id    	=   'TAS0000000000003';


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

		$fecha_cambio			=   date_format(date_create(substr($periodo->FEC_FIN, 0, 10)), 'Ymd');
		$tipo_cambio 			=	$this->gn_tipo_cambio($fecha_cambio);

		$tipo_referencia   		=	'KARDEX_ENVASES';
	    $cabecera 				= 	$this->kd_cabecera_asiento($periodo,$empresa_id,$monto_total,$tipoproducto,$tipo_referencia,$tipo_cambio);
	    $detalle 				= 	$this->kd_detalle_asiento($periodo,$empresa_id,$monto_total,$data_anio,$tipoproducto,$tipo_cambio);
		//dd($detalle);
		return View::make('kardex/modal/ajax/adetalleasientocontable',
						 [
						 	'periodo' 	=> $periodo,
						 	'tipoproducto' 	=> $tipoproducto,
						 	'cabecera' 	=> $cabecera,	
						 	'detalle' 	=> $detalle,					 	
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





}
