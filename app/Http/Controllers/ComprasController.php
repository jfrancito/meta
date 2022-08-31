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
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\WEBCuentaDetraccion;
use App\Modelos\CONPeriodo;
use App\Modelos\STDEmpresa;



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


	public function actionConfiguracionCuentaDetraccion($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Configuracion de cuenta detraccion');

	   	$listacuentadetraccion   =   WEBCuentaDetraccion::where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	   								->where('COD_ESTADO','=',1)
	   								->get();
                 

		return View::make('compras/listacuentadetraccion',
						 [
						 	'listacuentadetraccion' => $listacuentadetraccion,
						 	'idopcion' => $idopcion,
						 ]);
	}


	public function actionAgregarCuentaDetraccion($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar cuenta detraccion');
		if($_POST)
		{


			$NRO_CUENTA 	 		 	= 	$request['NRO_CUENTA'];
			$PORCENTAJE_DETRACION 	 	= 	$request['PORCENTAJE_DETRACION'];
			$TIPO_OPERACION 	 		= 	$request['TIPO_OPERACION'];
			$TIPO_BIEN_SERVICIO 	 	= 	$request['TIPO_BIEN_SERVICIO'];
			$DOCUMENTO 	 		 		= 	$request['DOCUMENTO'];

			$cuentadetraccion 			=   WEBCuentaDetraccion::where('DOCUMENTO','=',$DOCUMENTO)
													->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
													->first();

			if(count($cuentadetraccion)>0){
				return Redirect::back()->withInput()->with('errorbd', 'Proveedor ya registrado');
			}


			$empresa 					=   STDEmpresa::where('NRO_DOCUMENTO','=',$DOCUMENTO)->first();

			$cabecera            	 	=	new WEBCuentaDetraccion;
			$cabecera->DOCUMENTO 	    =   $DOCUMENTO;
			$cabecera->PROVEEDOR 	    =   $empresa->NOM_EMPR;
			$cabecera->NRO_CUENTA 	    =   $NRO_CUENTA;
			$cabecera->PORCENTAJE_DETRACION =   $PORCENTAJE_DETRACION;
			$cabecera->TIPO_OPERACION 	=   $TIPO_OPERACION;
			$cabecera->TIPO_BIEN_SERVICIO 	=   $TIPO_BIEN_SERVICIO;
			$cabecera->COD_EMPR 	 	=   Session::get('empresas_meta')->COD_EMPR;
			$cabecera->FEC_USUARIO_CREA_AUD 	=   $this->fechaactual;
			$cabecera->COD_USUARIO_CREA_AUD 	=   Session::get('usuario_meta')->id;
			$cabecera->save();
 
 			return Redirect::to('/gestion-configuracion-cuenta-detraccion/'.$idopcion)->with('bienhecho', 'Proveedor '.$empresa->NRO_DOCUMENTO.' - '.$empresa->NOM_EMPR.' registrado con exito');

		}else{

	    	$combo_empresa_xcuenta_detraccion  = 	$this->gn_generacion_combo_cuenta_detraccion('Seleccione empresa', '');
	    	$defecto_empresa_xcuenta_detraccion= 	'';
	    	$sw_acccion = 	'1';


			return View::make('compras/agregarcuentadetraccion',
						[
							'combo_empresa_xcuenta_detraccion'  	=> $combo_empresa_xcuenta_detraccion,
							'defecto_empresa_xcuenta_detraccion'  	=> $defecto_empresa_xcuenta_detraccion,
							'sw_acccion'  	=> $sw_acccion,			
						  	'idopcion'  		=> $idopcion
						]);
		}
	}



	public function actionModificarCuentaDetraccion($idopcion,$documento,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/

	    View::share('titulo','Modificar cuenta detraccion');

		if($_POST)
		{


			$NRO_CUENTA 	 		 	= 	$request['NRO_CUENTA'];
			$PORCENTAJE_DETRACION 	 	= 	$request['PORCENTAJE_DETRACION'];
			$TIPO_OPERACION 	 		= 	$request['TIPO_OPERACION'];
			$TIPO_BIEN_SERVICIO 	 	= 	$request['TIPO_BIEN_SERVICIO'];
			$DOCUMENTO 	 		 		= 	$request['DOCUMENTO'];

			$empresa 					=   STDEmpresa::where('NRO_DOCUMENTO','=',$documento)->first();
			$cabecera 					=   WEBCuentaDetraccion::where('DOCUMENTO','=',$documento)
											->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
											->first();

			$cabecera->NRO_CUENTA 	    =   $NRO_CUENTA;
			$cabecera->PORCENTAJE_DETRACION =   $PORCENTAJE_DETRACION;
			$cabecera->TIPO_OPERACION 	=   $TIPO_OPERACION;
			$cabecera->TIPO_BIEN_SERVICIO 	=   $TIPO_BIEN_SERVICIO;
			$cabecera->FEC_USUARIO_MODIF_AUD 	=   $this->fechaactual;
			$cabecera->COD_USUARIO_MODIF_AUD 	=   Session::get('usuario_meta')->id;
			$cabecera->save();

 			return Redirect::to('/gestion-configuracion-cuenta-detraccion/'.$idopcion)->with('bienhecho', 'Proveedor '.$empresa->NRO_DOCUMENTO.' - '.$empresa->NOM_EMPR.' modificado con exito');
		}else{


			$empresa 							=   STDEmpresa::where('NRO_DOCUMENTO','=',$documento)->first();
			$cuentadetraccion 					=   WEBCuentaDetraccion::where('DOCUMENTO','=',$documento)
													->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
													->first();


	    	$combo_empresa_xcuenta_detraccion   = 	$this->gn_generacion_combo_cuenta_detraccion('Seleccione empresa', '');
	    	$defecto_empresa_xcuenta_detraccion = 	$documento;
	    	$sw_acccion 						= 	'0';


			return View::make('compras/modificarcuentadetraccion',
						[
							'combo_empresa_xcuenta_detraccion'  	=> $combo_empresa_xcuenta_detraccion,
							'defecto_empresa_xcuenta_detraccion'  	=> $defecto_empresa_xcuenta_detraccion,
							'sw_acccion'  							=> $sw_acccion,
							'empresa'  								=> $empresa,
							'cuentadetraccion'  					=> $cuentadetraccion,		
						  	'idopcion'  							=> $idopcion
						]);


		}
	}


	public function actionListarDepositoMasivoDetraccion($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Deposito masivo detraccion de compras');

		$sel_periodo 			=	'';
		$anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$funcion 				= 	$this;
		$listadetracciones 		=   array();



		return View::make('compras/listadetraccion',
						 [
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'listadetracciones'		=> $listadetracciones,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_periodo'	 		=> $sel_periodo,			 	
						 	'idopcion' 				=> $idopcion,
						 	'ruc'					=> '',
						 	'nombre_empresa'		=> '',
						 	'sum_total_detraccion'	=> '',
						 	'lote'					=> '',
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxListarDepositoMasivoDetraccion(Request $request)
	{

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
        $listadetracciones     	= 	$this->co_lista_compras_detracciones($anio,$periodo_id,Session::get('empresas_meta')->COD_EMPR);
		$funcion 				= 	$this;
		
		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
	   	$mes 					= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT);		


		$ruc 					=   Session::get('empresas_meta')->NRO_DOCUMENTO;
		$nombre_empresa			= 	Session::get('empresas_meta')->NOM_EMPR;
	    $sum_total_detraccion 	= 	WEBAsiento::where('WEB.asientos.COD_PERIODO','=',$periodo_id)
	    										->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    										->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000004')
	    										->where('WEB.asientos.COD_ESTADO','=','1')
	    										->where('WEB.asientos.CAN_TOTAL_DETRACCION','>',0)
	    										->sum('CAN_TOTAL_DETRACCION');
		$anio 					= 	substr($anio, -2);
		$dd 					= 	'00';
		$lote 					=	$anio.$dd.$mes;

		return View::make('compras/ajax/alistadetracciones',
						 [
						 	'listadetracciones'		=> $listadetracciones,
						 	'anio'					=> $anio,
						 	'periodo_id'			=> $periodo_id,

						 	'ruc'					=> $ruc,
						 	'nombre_empresa'		=> $nombre_empresa,
						 	'sum_total_detraccion'	=> $sum_total_detraccion,
						 	'lote'					=> $lote,

						 	'funcion'				=> $funcion,			 	
						 	'ajax' 					=> true,						 	
						 ]);
	}




	public function actionDescargarArchivoDetraccion(Request $request)
	{


		set_time_limit(0);

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$data_archivo 			=   $request['data_archivo'];
		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
	   	$mes 					= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT); 

        $listadetracciones     	= 	$this->co_lista_compras_detracciones($anio,$periodo_id,Session::get('empresas_meta')->COD_EMPR);

	    if($data_archivo == 'gar'){

	    	$listadetracciones   = 	$this->co_lista_compras_detracciones($anio,$periodo_id,Session::get('empresas_meta')->COD_EMPR);

			$nombre = $this->co_crear_nombre_compra_detraccion($anio,$mes).'.txt';
			$path = storage_path('compras/detraccion/'.$nombre);
	    	$this->co_archivo_ple_compras($anio,$mes,$listadetracciones,$nombre,$path,$periodo_id,Session::get('empresas_meta')->COD_EMPR);
		    if (file_exists($path)){
		        return Response::download($path);
		    }	 

	    }


	}

	public function actionGonfirmarConfiguracionAsientoContablesXDocumentos($idopcion,$idasiento,Request $request)
	{

		if($_POST)
		{


			$anio_asiento 					= $request['anio_asiento'];
			$periodo_asiento_id 			= $request['periodo_asiento_id'];
			$tipo_descuento 				= $request['tipo_descuento'];
			$porcentaje_detraccion 			= $request['porcentaje_detraccion'];
			$total_detraccion 				= $request['total_detraccion'];

			$anio_confirmar 				= $request['anio_configuracion'];
			$periodo_id_confirmar 			= $request['periodo_id_configuracion'];
			$nro_serie_confirmar 			= $request['serie_configuracion'];
			$nro_doc_confirmar 				= $request['documento_configuracion'];

			$asiento 								= 	WEBAsiento::where('COD_ASIENTO','=',$idasiento)->first();

			$asiento->COD_CATEGORIA_ESTADO_ASIENTO 	=   'IACHTE0000000025';
			$asiento->TXT_CATEGORIA_ESTADO_ASIENTO 	=   'CONFIRMADO';
			$asiento->FEC_USUARIO_MODIF_AUD 		=   $this->fechaactual;
			$asiento->COD_USUARIO_MODIF_AUD 		=   Session::get('usuario_meta')->id;
			$asiento->COD_PERIODO 					=   $periodo_asiento_id;
			$asiento->COD_CATEGORIA_TIPO_DETRACCION =   $tipo_descuento;
			$asiento->CAN_DESCUENTO_DETRACCION 		=   $porcentaje_detraccion;
			$asiento->CAN_TOTAL_DETRACCION 			=   $total_detraccion;
			$asiento->save();

			sleep(1);
			$reversion = $this->co_reversion_compra($idasiento);
			sleep(4);


			Session::flash('periodo_id_confirmar', $periodo_id_confirmar);
			Session::flash('nro_serie_confirmar', $nro_serie_confirmar);
			Session::flash('nro_doc_confirmar', $nro_doc_confirmar);
			Session::flash('anio_confirmar', $anio_confirmar);

 		 	return Redirect::to('/gestion-listado-compras/'.$idopcion)->with('bienhecho', 'Asiento Modelo '.$asiento->NRO_SERIE.'-'.$asiento->NRO_DOC.' confirmado con exito');
		
		}


	}


	public function actionAjaxModalDetalleAsientoConfirmar(Request $request)
	{


		$asiento_id 			=   $request['asiento_id'];
		$idopcion 				=   $request['idopcion'];

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$serie 					=   $request['serie'];
		$documento 				=   $request['documento'];


	    $asiento 				= 	WEBAsiento::where('COD_ASIENTO','=',$asiento_id)->first();
	    $listaasientomovimiento = 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento_id)->orderBy('NRO_LINEA', 'asc')->get();

        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
	    $anio  					=   $this->anio;
	    $combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$sel_periodo 			=	'';

		$orden					=	$this->co_orden_xdocumento_contable($asiento->TXT_REFERENCIA);
		$sel_tipo_descuento		=	$this->co_orden_compra_tipo_descuento($orden);
		$combo_descuento 		= 	$this->co_generacion_combo_detraccion('DESCUENTO','Seleccione tipo descuento','');
		$funcion 				= 	$this;
		

		return View::make('compras/modal/ajax/mdetalleasientoconfirmar',
						 [
						 	'asiento'					=> $asiento,
						 	'listaasientomovimiento'	=> $listaasientomovimiento,
						 	'combo_periodo'				=> $combo_periodo,
						 	'combo_anio_pc'				=> $combo_anio_pc,
						 	'anio'						=> $anio,
						 	'sel_periodo'				=> $sel_periodo,

						 	'sel_tipo_descuento'		=> $sel_tipo_descuento,
						 	'combo_descuento'			=> $combo_descuento,
						 	'orden'						=> $orden,
						 	'idopcion'					=> $idopcion,

						 	'anio'						=> $anio,
						 	'periodo_id'				=> $periodo_id,
						 	'serie'						=> $serie,
						 	'documento'					=> $documento,


						 	'ajax' 						=> true,						 	
						 ]);
	}


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
