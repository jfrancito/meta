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
use App\Traits\ComprasTraits;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;



use Illuminate\Support\Facades\Storage;

class ComprasController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use ComprasTraits;



	public function actionGonfirmarAsientoContablesXDocumentos(Request $request)
	{

		if($_POST)
		{

			$msjarray  						= array();
			$respuesta 						= json_decode($request['documentos'], true);
			$idopcion 						= $request['idopcion'];
			$periodo_id_confirmar 			= $request['periodo_id_confirmar'];
			$nro_serie_confirmar 			= $request['nro_serie_confirmar'];
			$nro_doc_confirmar 				= $request['nro_doc_confirmar'];
			$anio_confirmar 				= $request['anio_confirmar'];
			$conts 							= 0;
			$contw 							= 0;
			$contd 							= 0;			

			foreach($respuesta as $obj){

				try {

					$asiento_id 							= 	$obj['asiento_id'];
					$asiento 								= 	WEBAsiento::where('COD_ASIENTO','=',$asiento_id)->first();

					$asiento->COD_CATEGORIA_ESTADO_ASIENTO 	=   'IACHTE0000000025';
					$asiento->TXT_CATEGORIA_ESTADO_ASIENTO 	=   'CONFIRMADO';
					$asiento->FEC_USUARIO_MODIF_AUD 		=   $this->fechaactual;
					$asiento->COD_USUARIO_MODIF_AUD 		=   Session::get('usuario_meta')->id;
					$asiento->save();	


				    if($asiento->COD_CATEGORIA_ESTADO_ASIENTO == 'IACHTE0000000025'){ 

				    	$msjarray[] 		= 	array(	"data_0" => $asiento->NRO_ASIENTO.'-'.$asiento->NRO_DOC, 
				    									"data_1" => 'Se confirmo su asiento contable', 
				    									"tipo" => 'S');
				    	$conts 				= 	$conts + 1;

				    }else{

						/**** ERROR DE PROGRMACION O SINTAXIS ****/
						$msjarray[] = array("data_0" => $asiento->NRO_ASIENTO.'-'.$asiento->NRO_DOC, 
											"data_1" => 'Paso algo con el asiento contable comuniuense con sistemas', 
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


			Session::flash('periodo_id_confirmar', $periodo_id_confirmar);
			Session::flash('nro_serie_confirmar', $nro_serie_confirmar);
			Session::flash('nro_doc_confirmar', $nro_doc_confirmar);
			Session::flash('anio_confirmar', $anio_confirmar);

			return Redirect::to('/gestion-listado-compras/'.$idopcion)->with('xmlmsj', $msjjson);

		
		}


	}



	public function actionListarCompras($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Gestionar asiento de compras');


		if(Session::has('periodo_id_confirmar')){
			$sel_periodo 			=	Session::get('periodo_id_confirmar');
			$sel_serie 				=	Session::get('nro_serie_confirmar');
			$sel_nrodoc 			=	Session::get('nro_doc_confirmar');
			$anio 					=	Session::get('anio_confirmar');
        	$listacompras     		= 	$this->co_lista_compras_asiento($anio,$sel_periodo,Session::get('empresas_meta')->COD_EMPR,$sel_serie,$sel_nrodoc);

		}else{
			$sel_periodo 			=	'';
			$sel_serie 				=	'';
			$sel_nrodoc 			=	'';
			$anio  					=   $this->anio;
	    	$listacompras 			= 	array();
		}

        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');

		$funcion 				= 	$this;
		
		return View::make('compras/listacompras',
						 [
						 	'listacompras' 			=> $listacompras,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_periodo'	 		=> $sel_periodo,
						 	'sel_serie'	 			=> $sel_serie,
						 	'sel_nrodoc'	 		=> $sel_nrodoc,				 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxListarCompras(Request $request)
	{

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$serie 					=   $request['serie'];
		$documento 				=   $request['documento'];
        $listacompras     		= 	$this->co_lista_compras_asiento($anio,$periodo_id,Session::get('empresas_meta')->COD_EMPR,$serie,$documento);

		$funcion 				= 	$this;
		
		return View::make('compras/ajax/alistacompras',
						 [

						 	'listacompras'			=> $listacompras,
						 	'funcion'				=> $funcion,			 	
						 	'ajax' 					=> true,						 	
						 ]);
	}

	public function actionAjaxBuscarCompraseleccionada(Request $request)
	{

		$documento_ctble_id 	=   $request['documento_ctble_id'];
		$compra     			= 	$this->co_documento_compra($documento_ctble_id);
		$listadetallecompra     = 	$this->co_detalle_compra($documento_ctble_id);

		$tipo_asiento_id 		= 	'TAS0000000000004';
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;
		$cod_moneda 			= 	$compra->COD_MONEDA;
		$cod_proveedor 			= 	$compra->COD_PROVEEDOR;
		$proveedor     			= 	$this->gn_data_empresa($cod_proveedor);
		$relacionado 			=   $proveedor->IND_RELACIONADO;

		$iconotipocliente     	= 	$this->co_asiento_modelo($tipo_asiento_id,$empresa_id,$relacionado,$cod_moneda,'tipo_cliente');
		$iconomoneda     		= 	$this->co_asiento_modelo($tipo_asiento_id,$empresa_id,$relacionado,$cod_moneda,'moneda_id');



		$funcion 				= 	$this;
		
		return View::make('compras/ajax/aasignarasiento',
						 [

						 	'compra'				=> $compra,
						 	'listadetallecompra'	=> $listadetallecompra,
						 	'iconotipocliente'		=> $iconotipocliente,
						 	'iconomoneda'			=> $iconomoneda,
						 	'funcion'				=> $funcion,			 	
						 	'ajax' 					=> true,						 	
						 ]);
	}




}
