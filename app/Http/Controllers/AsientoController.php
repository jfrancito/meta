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
use App\Modelos\CMPCategoria;
use App\Modelos\CONPeriodo;
use App\Modelos\STDTipoDocumento;


use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;
use App\Traits\AsientoTraits;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


class AsientoController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use AsientoTraits;
	use PlanContableTraits;

	public function actionAjaxEliminarDetalleAsiento(Request $request)
	{

		$array_detalle_asiento_request 		= 	json_decode($request['array_detalle_asiento'],true);
		$array_detalle_asiento 				=	array();
		$fila 								= 	$request['fila'];



		$disminuir 							= 	0;
		$grupo_oc							= 	"";
		$orden_cen							= 	"";
		$disminuir_gm 						= 	0;
		$grupo_movil						= 	"";
		$grupo_orden_movil					= 	0;


		//eliminar la fila del array
		foreach ($array_detalle_asiento_request as $key => $item) {
            if((int)$item['ultimalinea'] == $fila) {
                unset($array_detalle_asiento_request[$key]);
            }
		}

	    //agregar a un array nuevo para listar en la vista
		foreach ($array_detalle_asiento_request as $key => $item) {
			array_push($array_detalle_asiento,$item);
		}

		return View::make('asiento/ajax/adetalleasiento',
					[
						'array_detalle_asiento'  	=> $array_detalle_asiento,
					]);
	}



	public function actionAjaxAgregarDetalleAsiento(Request $request)
	{
	
		$nivel 	 		 					= 	$request['nivel'];
		$partida_id 	 		 			= 	$request['partida_id'];
		$cuenta_contable_id 	 			= 	$request['cuenta_contable_id'];
		$monto 	 		 					= 	$request['monto'];
		$ultimalinea 	 		 			= 	$request['ultimalinea'];

		$array_detalle_asiento_request 		= 	json_decode($request['array_detalle_asiento'],true);
		$array_detalle_asiento 				=	array();
		$array_nuevo_item 					=	array();

	    $cuenta_contable 					= 	WEBCuentaContable::where('id','=',$cuenta_contable_id)->first();
	    $partida 							= 	CMPCategoria::where('COD_CATEGORIA','=',$partida_id)->first();

		foreach ($array_detalle_asiento_request as $key => $item) {
			array_push($array_detalle_asiento,$item);
		}

		$montod = 0;
		$montoh = 0;

		if($partida_id == 'COP0000000000001'){
			$montod = $monto;
			$montoh = 0;
		}else{
			$montod = 0;
			$montoh = $monto;
		}

		$array_nuevo_item					= 	$this->llenar_array_detalle_asiento($nivel,$partida_id,$cuenta_contable->id,
																								$cuenta_contable->nro_cuenta,$cuenta_contable->nombre,$montod,$montoh,$partida->NOM_CATEGORIA,$ultimalinea);
		array_push($array_detalle_asiento,$array_nuevo_item);


		return View::make('asiento/ajax/adetalleasiento',
					[
						'array_detalle_asiento'  	=> $array_detalle_asiento,
					]);

	}

	public function actionAjaxInputTipoCambio(Request $request)
	{
	
		$fechadocumento 	 		 		= 	$request['fechadocumento'];
		$tipo_cambio 						=	$this->gn_tipo_cambio($fechadocumento);

		return View::make('asiento/ajax/atipocambio',
					[
						'tipo_cambio'  	=> $tipo_cambio,
					]);

	}




	public function actionGestionarAsiento($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar Asiento');
		if($_POST)
		{

				$anio 	 		 			= 	$request['anio'];
				$periodo_id 	 		 	= 	$request['periodo_id'];
				$tipo_asiento_id 	 		= 	$request['tipo_asiento_id'];
				$moneda_id 	 		 		= 	$request['moneda_id'];
				$tipocambio 	 		 	= 	$request['tipocambio'];

				$tipo_documento 	 		= 	$request['tipo_documento'];
				$serie 	 		 			= 	$request['serie'];
				$nrocomprobante 	 		= 	$request['nrocomprobante'];

				$fechavencimiento 	 		= 	$request['fechavencimiento'];
				$fechadocumento 	 		= 	$request['fechadocumento'];


				$tipo_documento_referencia 	= 	$request['tipo_documento_referencia'];
				$seriereferencia 	 		= 	$request['seriereferencia'];
				$nrocomprobantereferencia 	= 	$request['nrocomprobantereferencia'];
				$fechareferencia 	 		= 	$request['fechareferencia'];
				$glosa 	 		 			= 	$request['glosa'];
				$empresa_id 				=	Session::get('empresas_meta')->COD_EMPR;
				$centro_id 					=	'CEN0000000000001';
				$periodo 					= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
				$tipo_asiento 				= 	CMPCategoria::where('COD_CATEGORIA','=',$tipo_asiento_id)->first();
				$moneda 					= 	CMPCategoria::where('COD_CATEGORIA','=',$moneda_id)->first();
				$tipodocumento 				= 	STDTipoDocumento::where('COD_TIPO_DOCUMENTO','=',$tipo_documento)->first();
				$tipodocumentoreferencia 	= 	STDTipoDocumento::where('COD_TIPO_DOCUMENTO','=',$tipo_documento_referencia)->first();


				if(count($tipodocumento)>0){
					$TXT_CATEGORIA_TIPO_DOCUMENTO = $tipodocumento->TXT_TIPO_DOCUMENTO;
				}else{
					$TXT_CATEGORIA_TIPO_DOCUMENTO = '';
				}
				$glosa  =	$tipo_asiento->NOM_CATEGORIA.' : '.$TXT_CATEGORIA_TIPO_DOCUMENTO.' '.$serie.' '.$nrocomprobante.' // '.$glosa;


				$array_detalle_asiento_request 		= 	json_decode($request['array_detalle_asiento'],true);
				$debe 						= 	0;
				$haber 						= 	0;
				foreach ($array_detalle_asiento_request as $key => $item) {
					$debe = $debe + $item['montod'];
					$haber = $haber + $item['montoh'];
				}



				$IND_TIPO_OPERACION = 'I';
				$COD_ASIENTO = '';
				$COD_EMPR = $empresa_id;
				$COD_CENTRO = $centro_id;
				$COD_PERIODO = $periodo->COD_PERIODO;
				$COD_CATEGORIA_TIPO_ASIENTO = $tipo_asiento->COD_CATEGORIA;
				$TXT_CATEGORIA_TIPO_ASIENTO = $tipo_asiento->NOM_CATEGORIA;
				$NRO_ASIENTO = '';
				$FEC_ASIENTO = $fechadocumento;
				$TXT_GLOSA = $tipo_asiento->NOM_CATEGORIA.' : '.$glosa;
				$COD_CATEGORIA_ESTADO_ASIENTO = 'IACHTE0000000025';
				$TXT_CATEGORIA_ESTADO_ASIENTO = 'CONFIRMADO';
				$COD_CATEGORIA_MONEDA = $moneda->COD_CATEGORIA;
				$TXT_CATEGORIA_MONEDA = $moneda->NOM_CATEGORIA;
				$CAN_TIPO_CAMBIO = $tipocambio;

				$CAN_TOTAL_DEBE = $debe;
				$CAN_TOTAL_HABER = $haber;

				$COD_ASIENTO_EXTORNO = '';
				$COD_ASIENTO_EXTORNADO = '';
				$IND_EXTORNO = '0';
				$COD_ASIENTO_MODELO = '';
				$TXT_TIPO_REFERENCIA = 'META';
				$TXT_REFERENCIA = '';

				$COD_ESTADO = '1';
				$COD_USUARIO_REGISTRO = Session::get('usuario_meta')->id;
				$COD_MOTIVO_EXTORNO = '';
				$GLOSA_EXTORNO = '';
				$COD_EMPR_CLI = '';
				$TXT_EMPR_CLI = '';

				if(count($tipodocumento)>0){
					$COD_CATEGORIA_TIPO_DOCUMENTO = $tipodocumento->COD_TIPO_DOCUMENTO;
					$TXT_CATEGORIA_TIPO_DOCUMENTO = $tipodocumento->TXT_TIPO_DOCUMENTO;
				}else{
					$COD_CATEGORIA_TIPO_DOCUMENTO = '';
					$TXT_CATEGORIA_TIPO_DOCUMENTO = '';
				}

				$NRO_SERIE = $serie;
				$NRO_DOC = $nrocomprobante;


				$FEC_DETRACCION = '';
				$NRO_DETRACCION = '';
				$CAN_DESCUENTO_DETRACCION = '0';
				$CAN_TOTAL_DETRACCION = '0';

				if(count($tipodocumentoreferencia)>0){
					$COD_CATEGORIA_TIPO_DOCUMENTO_REF = $tipodocumentoreferencia->COD_TIPO_DOCUMENTO;
					$TXT_CATEGORIA_TIPO_DOCUMENTO_REF = $tipodocumentoreferencia->TXT_TIPO_DOCUMENTO;
				}else{
					$COD_CATEGORIA_TIPO_DOCUMENTO_REF = '';
					$TXT_CATEGORIA_TIPO_DOCUMENTO_REF = '';
				}

				$NRO_SERIE_REF = $seriereferencia;
				$NRO_DOC_REF = $nrocomprobantereferencia;
				$FEC_VENCIMIENTO = $fechareferencia;
				$IND_AFECTO = '0';



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




			//DETALLE

			foreach ($array_detalle_asiento_request as $key => $item) {

				//soles
				if($moneda_id=='MON0000000000001'){
					$CAN_DEBE_MN = $item['montod'];
					$CAN_HABER_MN = $item['montoh'];
					$CAN_DEBE_ME = $item['montod']*$tipocambio;
					$CAN_HABER_ME = $item['montoh']*$tipocambio;
				}else{
					$CAN_DEBE_MN = $item['montod']*$tipocambio;
					$CAN_HABER_MN = $item['montoh']*$tipocambio;
					$CAN_DEBE_ME = $item['montod'];
					$CAN_HABER_ME = $item['montoh'];
				}

				$IND_TIPO_OPERACION = 'I';
				$COD_ASIENTO_MOVIMIENTO = '';
				$COD_EMPR = $empresa_id;
				$COD_CENTRO = $centro_id;
				$COD_ASIENTO = $asientocontable;

				$COD_CUENTA_CONTABLE = $item['cuenta_contable_id'];
				$TXT_CUENTA_CONTABLE = $item['nro_cuenta'];
				$TXT_GLOSA = $item['nombre'];


				$NRO_LINEA = $key+1;
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

 			return Redirect::to('/gestion-asiento/'.$idopcion)->with('bienhecho', 'Asiento '.$glosa.' registrado con exito');

		}else{

			$combo_tipo_asiento 	= 	$this->gn_generacion_combo_categoria('TIPO_ASIENTO','Seleccione tipo asiento','');
			$combo_moneda 			= 	$this->gn_generacion_combo_categoria('MONEDA','Seleccione moneda','');
			$combo_tipo_documento	= 	$this->gn_generacion_combo_tabla_osiris('STD.TIPO_DOCUMENTO','COD_TIPO_DOCUMENTO','TXT_TIPO_DOCUMENTO','Seleccione tipo documento','');

		    $sel_periodo 			=	'';
		    $anio  					=   $this->anio;
	        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
			$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    	$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');

			$defecto_tipo_asiento 	= 	'';
			$defecto_moneda			= 	'';
			$defecto_tipo_documento	= 	'-1';

			$tipo_cambio 			=	$this->gn_tipo_cambio($this->fin);
			$array_detalle_asiento  = 	array();

			return View::make('asiento/agregarasiento',
						[
							'combo_tipo_asiento'  		=> $combo_tipo_asiento,
							'combo_moneda'  			=> $combo_moneda,
							'combo_tipo_documento'  	=> $combo_tipo_documento,

							'sel_periodo'  				=> $sel_periodo,
							'anio'  					=> $anio,
							'combo_anio_pc'  			=> $combo_anio_pc,
							'combo_periodo'  			=> $combo_periodo,
							'tipo_cambio'  				=> $tipo_cambio,
							'fecha'						=> $this->fin,
							'defecto_tipo_documento'  	=> $defecto_tipo_documento,
							'defecto_tipo_asiento'  	=> $defecto_tipo_asiento,
							'defecto_moneda'  			=> $defecto_moneda,
							'array_detalle_asiento'  	=> $array_detalle_asiento,
						  	'idopcion'  				=> $idopcion
						]);
		}
	}

	public function actionAjaxModalDetalleAsiento(Request $request)
	{
		
		$funcion 				= 	$this;

		$anio  					=   $this->anio;
        $array_nivel_pc     	= 	$this->pc_array_nivel_cuentas_contable(Session::get('empresas_meta')->COD_EMPR,$anio);
		$combo_nivel_pc  		= 	$this->gn_generacion_combo_array('Seleccione nivel', '' , $array_nivel_pc);
		$defecto_nivel 			= 	'6';


		$array_cuenta 	    	= 	$this->pc_array_nro_cuentas_nombre_xnivel(Session::get('empresas_meta')->COD_EMPR,$defecto_nivel,$anio);
		$combo_cuenta  			= 	$this->gn_generacion_combo_array('Seleccione cuenta contable', '' , $array_cuenta);

		$combo_partida 			= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_PARTIDA','Seleccione partida','');
		$funcion 				= 	$this;

		$defecto_cuenta			= 	'';
		$defecto_partida		= 	'';
		$asiento_modelo_detalle_id = '';


		return View::make('asiento/modal/ajax/magregardetalleasiento',
						 [		 	

						 	'combo_nivel_pc' 		=> $combo_nivel_pc,
						 	'combo_cuenta' 			=> $combo_cuenta,
						 	'combo_partida' 		=> $combo_partida,
						 	'defecto_nivel' 		=> $defecto_nivel,
						 	'defecto_cuenta' 		=> $defecto_cuenta,
						 	'defecto_partida' 		=> $defecto_partida,

						 	'funcion' 				=> $funcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}





}
