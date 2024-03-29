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
use App\Modelos\WEBHistorialMigrar;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

use App\Traits\GeneralesTraits;
use App\Traits\PlanContableTraits;
use App\Traits\ConfiguracionProductoTraits;
use App\Traits\MigrarVentaTraits;


class MigrarVentaController extends Controller
{

	use GeneralesTraits;
	use PlanContableTraits;
	use ConfiguracionProductoTraits;
	use MigrarVentaTraits;
	

	public function actionGenerarAsientoContablesXDocumentos(Request $request)
	{

		if($_POST)
		{


			$msjarray  			= array();
			$respuesta 			= json_decode($request['documentos'], true);
			$conts 				= 0;
			$contw 				= 0;
			$contd 				= 0;			

			foreach($respuesta as $obj){

				try {

					$documento_id 		= $obj['cod_documento'];
					$tipo_asiento 		= $obj['cod_tipo_asiento'];

					$respuesta 			= $this->mv_update_historial_ventas($documento_id,$tipo_asiento);

					$lista_ventas 		= $this->mv_lista_ventas_asignar_xdocumento($documento_id,$tipo_asiento);
					foreach($lista_ventas as $index => $item){
						$respuesta2 = $this->mv_asignar_asiento_modelo($item,$tipo_asiento);
					}

      				$historialmigrar 	=   WEBHistorialMigrar::where('COD_REFERENCIA','=',$documento_id)
      										->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)
      										->first();
      				$nro_documento 		= 	$historialmigrar->documento_ctble->NRO_SERIE.'-'.$historialmigrar->documento_ctble->NRO_DOC;


				    if($historialmigrar->IND_ASIENTO_MODELO == 1){ 

				    	$msjarray[] 		= 	array(	"data_0" => $nro_documento, 
				    									"data_1" => 'Se genero su asiento contable', 
				    									"tipo" => 'S');
				    	$conts 				= 	$conts + 1;

				    }else{

						/**** ERROR DE PROGRMACION O SINTAXIS ****/
						$msjarray[] = array("data_0" => $nro_documento, 
											"data_1" => $historialmigrar->TXT_ERROR, 
											"tipo" => 'D');
						$contd 		= 	$contd + 1;

				    }

				}catch(\Exception $e){

					$msjarray[] = array("data_0" => 'sistemas', 
										"data_1" => 'comprobante contienen errores '. $e->getMessage(), 
										"tipo" => 'D');
					$contd 		= 	$contd + 1;
				}

			}



	    	$msjarray[] = array("data_0" => $conts, 
	    						"data_1" => 'documentos con asiento', 
	    						"tipo" => 'TS');

	    	$msjarray[] = array("data_0" => $contw, 
	    						"data_1" => 'documentos rechazados', 
	    						"tipo" => 'TW');	 

	    	$msjarray[] = array("data_0" => $contd, 
	    						"data_1" => 'documentos observados', 
	    						"tipo" => 'TD');

			$msjjson = json_encode($msjarray);


			return Redirect::to('/gestion-observacion-documentos-ventas')->with('xmlmsj', $msjjson);

		
		}


	}

	public function actionAjaxModalDetalleProductoMigracionVentas(Request $request)
	{

		$cod_documento_id 		=   $request['cod_documento_id'];
		$tipo_asiento 			=   $request['cod_tipo_asiento'];

		$idopcion 				=   $request['idopcion'];
	    $listadetalleproducto 	= 	$this->gn_detalle_producto_xcoddocumento($cod_documento_id);
		$funcion 				= 	$this;



		$historialmigrar 		=   WEBHistorialMigrar::where('COD_REFERENCIA','=',$cod_documento_id)
									->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)->first();



		$indclienterelter 		= 	$this->gn_ind_relacionado_tercero_xempresa($historialmigrar->documento_ctble->COD_EMPR_RECEPTOR);

		$anio_documento 		=   $historialmigrar->documento_ctble->periodo->COD_ANIO;

		//dd($indclienterelter);

		return View::make('migracion/modal/ajax/adetalleproducto',
						 [
						 	'listadetalleproducto' 	=> $listadetalleproducto,
						 	'historialmigrar' 		=> $historialmigrar,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'indclienterelter' 		=> $indclienterelter,
						 	'anio_documento' 		=> $anio_documento,
						 	'tipo_asiento' 			=> $tipo_asiento,
						 	'ajax' 					=> true,						 	
						 ]);
	}


	public function actionListarObservacionDocumentos($tipo_asiento,$anio)
	{
	    View::share('titulo','Observaciones en documentos de venta');
	    if($tipo_asiento == '3'){$tipo_asiento = 'TAS0000000000003';}
	    if($tipo_asiento == '4'){$tipo_asiento = 'TAS0000000000004';}


		$lista_ventas 					= 	$this->mv_lista_ventas_observadas($tipo_asiento,Session::get('empresas_meta')->COD_EMPR,$anio);
		$funcion 						= 	$this;
	    $sel_tipo_asiento 				=	'';
	    $sel_periodo 					=	'';
	    $anio  							=   $anio;
        $array_anio_pc     				= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  				= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_tipo_asiento 			= 	$this->gn_generacion_combo_categoria('TIPO_ASIENTO','Seleccione tipo asiento','');
	    $combo_periodo 					= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');

		return View::make('migracion/listaobservacionventas',
						 [
						 	'funcion' 			=> $funcion,	
						 	'lista_ventas' 		=> $lista_ventas,
						 	'combo_tipo_asiento'	=> $combo_tipo_asiento,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_tipo_asiento'	 	=> $sel_tipo_asiento,
						 	'sel_periodo'	 		=> $sel_periodo,

						 ]);
	}

	public function actionAjaxObeservacionDocumentosVentas(Request $request)
	{


		$tipo_asiento_id 		=   $request['tipo_asiento_id'];
		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];

		$lista_ventas 			= 	$this->mv_lista_ventas_observadas_xperiodo($tipo_asiento_id,Session::get('empresas_meta')->COD_EMPR,$periodo_id);

		$funcion 				= 	$this;
		
		return View::make('migracion/ajax/alistaobservacionventas',
						 [

						 	'lista_ventas'			=> $lista_ventas,
						 	'funcion'				=> $funcion,			 	
						 	'ajax' 					=> true,						 	
						 ]);
	}


	public function actionMigrarVentas()
	{
		set_time_limit(0);
		$tipo_asiento 						=	'TAS0000000000003';	
		//buscar asiento 
		$lista_ventas_migrar_emitido 		= 	$this->mv_lista_ventas_migrar_agrupado_emitido();
		$lista_ventas_migrar_anulado 		= 	$this->mv_lista_ventas_migrar_agrupado_anulado();
		$lista_ventas_migrar_anulado_nuevo  = 	$this->mv_lista_ventas_migrar_agrupado_anulado_nuevo();

		//dd($lista_ventas_migrar_emitido);

		$this->mv_agregar_historial_ventas($lista_ventas_migrar_emitido,$lista_ventas_migrar_anulado,$tipo_asiento);


		foreach($lista_ventas_migrar_emitido as $index => $item){
			$respuesta = $this->mv_update_historial_ventas($item->COD_DOCUMENTO_CTBLE,$tipo_asiento);
		}
		foreach($lista_ventas_migrar_anulado as $index => $item){
			$respuesta = $this->mv_update_historial_ventas($item->COD_DOCUMENTO_CTBLE,$tipo_asiento);
		}	

		foreach($lista_ventas_migrar_anulado_nuevo as $index => $item){
			$respuesta = $this->mv_update_historial_ventas($item->COD_DOCUMENTO_CTBLE,$tipo_asiento);
		}	

		//asignar asiento
		$lista_ventas 				= 	$this->mv_lista_ventas_asignar($tipo_asiento);

		//dd($lista_ventas);

		foreach($lista_ventas as $index => $item){
			$respuesta2 = $this->mv_asignar_asiento_modelo($item,$tipo_asiento);
		}
		//$this->mv_asignar_totales_ceros();

		print_r("se realizo con exito");
		return Redirect::to('/bienvenido');

	}



	public function actionMigrarTotalCeros()
	{
		set_time_limit(0);
		$asignar_totales_ceros 				= 	$this->mv_asignar_totales_ceros();
		print_r("se realizo con exito");
	}



}
