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
use App\Modelos\TESCajaBanco;
use App\Modelos\CMPReferecenciaAsoc;



use App\Traits\GeneralesTraits;
use App\Traits\CajaBancoTraits;
use App\Traits\AsientoModeloTraits;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;



use Illuminate\Support\Facades\Storage;

class CajaBancoController extends Controller
{

	use GeneralesTraits;
	use CajaBancoTraits;
	use AsientoModeloTraits;



	public function actionListarBancoCaja($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Asociar Banco y Caja');

	    $listacajabanco 		= 	$this->cb_lista_banco_caja();
		$funcion 				= 	$this;

		return View::make('cajaybanco/listabancocaja',
						 [
						 	'listacajabanco' 		=> $listacajabanco,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxModalAsociarBancoCaja(Request $request)
	{
		$banco_caja_id 			=   $request['banco_caja_id'];
		$idopcion 				=   $request['idopcion'];
		$anio  					=   $this->anio;
		$cajabanco 				= 	TESCajaBanco::where('COD_CAJA_BANCO','=',$banco_caja_id)->first();
		$combo_cuenta_contable 	= 	$this->gn_genecombo_cuenta_contable_xnrocuentapadre('1041',$anio);
		$funcion 				= 	$this;

		return View::make('cajaybanco/modal/ajax/maasociarbancocaja',
						 [		 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'banco_caja_id' 		=> $banco_caja_id,
						 	'cajabanco' 			=> $cajabanco,
						 	'combo_cuenta_contable'	=> $combo_cuenta_contable,
						 	'ajax' 					=> true,						 	
						 ]);
	}


	public function actionGuardarAsociacionCajaBanco($idopcion,Request $request)
	{


		if($_POST)
		{
			$cuenta_contable_id = $request['cuenta_contable_id'];
			$caja_banco_id 		= $request['caja_banco_id'];
			$bancocaja 			= TESCajaBanco::where('COD_CAJA_BANCO','=',$caja_banco_id)->first();
			$bancocaja->TXT_TIPO_REFERENCIA  						=	$cuenta_contable_id;
			$bancocaja->FEC_USUARIO_MODIF_AUD 	 					=   $this->fechaactual;
			$bancocaja->COD_USUARIO_MODIF_AUD 						=   Session::get('usuario_meta')->id;
			$bancocaja->save();
			return Redirect::to('/gestion-asociar-banco-caja/'.$idopcion)->with('bienhecho', 'Caja y Banco '.$bancocaja->TXT_CAJA_BANCO.' asociado con exito');

		}


	}

	public function actionCancelarDocumentoClienteProveedor($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Asistente para cancelación de documentos (cliente y proveedor)');


	    $combo_cuenta_referencia = $this->cb_combo_cuenta_referencia();
	    $listadetallemovimientos = array();
	    $buscar_modelo_asiento   = array('encontro' => '0','msg' => 'Seleccione una operación');
	    $asiento_array 			 = array();
	    $detalle_asiento_array 	 = array();
	    $asientomodelo 		     = array();



		return View::make('cajaybanco/cancelaciondocumento',
						 [
						 	'combo_cuenta_referencia' => $combo_cuenta_referencia,
						 	'listadetallemovimientos' => $listadetallemovimientos,
						 	'cuenta_referencia' 	  => '',
						 	'buscar_modelo_asiento'   => $buscar_modelo_asiento,
						 	'asiento_array'   		  => $asiento_array,
						 	'detalle_asiento_array'   => $detalle_asiento_array,
						 	'asientomodelo'   		  => $asientomodelo,
						 	'idopcion' => $idopcion,
						 ]);
	}

	public function actionGuardarAsientoCajaBanco($idopcion,Request $request)
	{

			$cabecera 				=   json_decode($request['cabecera'],false);
			$detalle 				=   json_decode($request['detalle'],false);

			$nro_operacion 			= 	$request['nro_operacion'];
			$cod_caja_banco 		= 	$request['cod_caja_banco'];
			$nro_referencia 		= 	$request['nro_referencia'];
			$fec_movimiento_caja 	= 	$request['fec_movimiento_caja'];


	    	$listadetallemovimientos 	= 	$this->cb_detalle_operacion_caja_movimientos($cod_caja_banco,$nro_operacion,$nro_referencia,$fec_movimiento_caja);


			$empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
			$centro_id 				=	'CEN0000000000001';

			$COD_ASIENTO 			= 	'';



    		$asientocontable     	= 	$this->gn_crear_asiento_contable($cabecera->IND_TIPO_OPERACION,
												$COD_ASIENTO,
												$cabecera->COD_EMPR,
												$cabecera->COD_CENTRO,
												$cabecera->COD_PERIODO,
												$cabecera->COD_CATEGORIA_TIPO_ASIENTO,
												$cabecera->TXT_CATEGORIA_TIPO_ASIENTO,
												$cabecera->NRO_ASIENTO,
												$cabecera->FEC_ASIENTO,
												$cabecera->TXT_GLOSA,
												
												$cabecera->COD_CATEGORIA_ESTADO_ASIENTO,
												$cabecera->TXT_CATEGORIA_ESTADO_ASIENTO,
												$cabecera->COD_CATEGORIA_MONEDA,
												$cabecera->TXT_CATEGORIA_MONEDA,
												$cabecera->CAN_TIPO_CAMBIO,
												$cabecera->CAN_TOTAL_DEBE,
												$cabecera->CAN_TOTAL_HABER,
												$cabecera->COD_ASIENTO_EXTORNO,
												$cabecera->COD_ASIENTO_EXTORNADO,
												$cabecera->IND_EXTORNO,

												$cabecera->COD_ASIENTO_MODELO,
												$cabecera->TXT_TIPO_REFERENCIA,
												$cabecera->TXT_REFERENCIA,
												$cabecera->COD_ESTADO,
												$cabecera->COD_USUARIO_REGISTRO,
												$cabecera->COD_MOTIVO_EXTORNO,
												$cabecera->GLOSA_EXTORNO,
												$cabecera->COD_EMPR_CLI,
												$cabecera->TXT_EMPR_CLI,
												$cabecera->COD_CATEGORIA_TIPO_DOCUMENTO,

												$cabecera->TXT_CATEGORIA_TIPO_DOCUMENTO,
												$cabecera->NRO_SERIE,
												$cabecera->NRO_DOC,
												$cabecera->FEC_DETRACCION,
												$cabecera->NRO_DETRACCION,
												$cabecera->CAN_DESCUENTO_DETRACCION,
												$cabecera->CAN_TOTAL_DETRACCION,
												$cabecera->COD_CATEGORIA_TIPO_DOCUMENTO_REF,
												$cabecera->TXT_CATEGORIA_TIPO_DOCUMENTO_REF,
												$cabecera->NRO_SERIE_REF,

												$cabecera->NRO_DOC_REF,
												$cabecera->FEC_VENCIMIENTO,
												$cabecera->IND_AFECTO);



		foreach($detalle as $index => $item){

			$COD_ASIENTO_MOVIMIENTO = '';

    		$detalle     	= 	$this->gn_crear_detalle_asiento_contable($item->IND_TIPO_OPERACION,
														$COD_ASIENTO_MOVIMIENTO,
														$item->COD_EMPR,
														$item->COD_CENTRO,
														$asientocontable,
														$item->COD_CUENTA_CONTABLE,
														$item->TXT_CUENTA_CONTABLE,
														$item->TXT_GLOSA,
														$item->CAN_DEBE_MN,
														$item->CAN_HABER_MN,

														$item->CAN_DEBE_ME,
														$item->CAN_HABER_ME,
														$item->NRO_LINEA,
														$item->COD_CUO,
														$item->IND_EXTORNO,
														$item->TXT_TIPO_REFERENCIA,
														$item->TXT_REFERENCIA,
														$item->COD_ESTADO,
														$item->COD_USUARIO_REGISTRO,
														$item->COD_DOC_CTBLE_REF,

														$item->COD_ORDEN_REF);

		}	


		foreach($listadetallemovimientos as $index => $item){

            $referenciaasoc = new CMPReferecenciaAsoc;
            $referenciaasoc->COD_TABLA        =   $item->COD_ASIENTO_MOVIMIENTO;
            $referenciaasoc->COD_TABLA_ASOC   =   $asientocontable;
            $referenciaasoc->TXT_TABLA        =   'CON.ASIENTO_MOVIMIENTO';
            $referenciaasoc->TXT_TABLA_ASOC   =   'WEB.asientos';
            $referenciaasoc->CAN_AUX1         =   0;
            $referenciaasoc->COD_USUARIO_CREA_AUD  =   $cabecera->COD_USUARIO_REGISTRO;
            $referenciaasoc->TXT_GLOSA  	  =   'OP '.$nro_operacion;
            $referenciaasoc->FEC_USUARIO_CREA_AUD  =   date('d-m-Y H:i:s');
            $referenciaasoc->FEC_USUARIO_MODIF_AUD  =   date('d-m-Y H:i:s');
            $referenciaasoc->TXT_TIPO_REFERENCIA   =   $item->NRO_SERIE;
            $referenciaasoc->TXT_REFERENCIA    =   $item->NRO_DOC;
            $referenciaasoc->TXT_DESCRIPCION    =   $item->COD_DOCUMENTO_CTBLE;
            $referenciaasoc->COD_ESTADO        =   1;
            $referenciaasoc->save();

		}




    	return Redirect::to('/gestion-cancelar-documentos/'.$idopcion)->with('bienhecho', 'Registro de asiento contable exitoso');

	}




	public function actionAjaxModalListaMovimientoCajaBanco(Request $request)
	{
		
		$cuenta_referencia 			=   $request['cuenta_referencia'];
		$nrooperacion 				=   $request['nrooperacion'];
		$idopcion 					=   $request['idopcion'];

	    $listamovimientos 			= 	$this->cb_operacion_caja_movimientos($cuenta_referencia,$nrooperacion);


		return View::make('cajaybanco/modal/ajax/mlistamovimientos',
						 [		 	
						 	'listamovimientos' 		=> $listamovimientos,
						 	'cuenta_referencia' 	=> $cuenta_referencia,
						 	'nrooperacion' 			=> $nrooperacion,
						 	'idopcion' 				=> $idopcion,
						 	'ajax' 					=> true,						 	
						 ]);

	}

	public function actionAjaxListaMovimientoCajaBanco(Request $request)
	{
		
		$nro_operacion 				=   $request['nro_operacion'];
		$cod_caja_banco 			=   $request['cod_caja_banco'];
		$nro_referencia 			=   $request['nro_referencia'];
		$fec_movimiento_caja 		=   $request['fec_movimiento_caja'];



		$idopcion 					=   $request['idopcion'];
	    $funcion 					= 	$this;
	    $anio  						=   $this->anio;
		$movimiento 				= 	$this->cb_operacion_caja_movimientos_first($nro_referencia,$nro_operacion,$cod_caja_banco,$fec_movimiento_caja);
	    $listadetallemovimientos 	= 	$this->cb_detalle_operacion_caja_movimientos($cod_caja_banco,$nro_operacion,$nro_referencia,$fec_movimiento_caja);

	    $glosa 						=	$this->cb_glosa_de_asiento($listadetallemovimientos);


	    $tipo_asiento 				= 	$this->cb_buscar_tipo_asiento($movimiento);
	    $pago_cobro 				= 	$this->cb_cod_pago_cobro($nro_referencia);
	    $text_pago_cobro 			= 	$this->cb_txt_pago_cobro($pago_cobro);


	    $count_rel 					= 	$this->am_empresa_relacionada($movimiento,$listadetallemovimientos);
	   	$count_ter 					= 	$this->am_empresa_tercero(count($listadetallemovimientos),$count_rel);
	    $empresa_rel_ter 			= 	$this->am_empresa_relacionada_tercero($count_rel,$count_ter);
	    $existe_rel_ter 			= 	$this->am_empresa_existe_relacionada_tercero($count_rel,$count_ter);
	    $abreviatura_asiento_modelo = 	$this->am_abreviatura_asiento_modelo($pago_cobro,$empresa_rel_ter,$movimiento);

	    $buscar_modelo_asiento 		= 	$this->am_buscar_asiento_modelo($movimiento,$tipo_asiento,$anio,$pago_cobro,$empresa_rel_ter);



	    $asiento_array 				= 	$this->am_asiento_array($movimiento,$tipo_asiento,$anio,$pago_cobro,$empresa_rel_ter,$buscar_modelo_asiento,$nro_operacion,$nro_referencia,$glosa,$text_pago_cobro);

	    $detalle_asiento_array 		= 	$this->am_asiento_detalle_array($movimiento,$tipo_asiento,$anio,$pago_cobro,$empresa_rel_ter,$buscar_modelo_asiento,$nro_operacion,$nro_referencia,$listadetallemovimientos,$funcion,$existe_rel_ter,$abreviatura_asiento_modelo);

	    $asiento_array 				= 	$this->am_totales_asiento_array($movimiento,$tipo_asiento,$anio,$pago_cobro,$empresa_rel_ter,$buscar_modelo_asiento,$nro_operacion,$nro_referencia,$asiento_array,$detalle_asiento_array);


	    $asientomodelo 				= 	array();
	    if($buscar_modelo_asiento['encontro'] == '1'){
	    	$asientomodelo 			= 	WEBAsientoModelo::where('id','=',$buscar_modelo_asiento['msg'])->first();
	    }

		return View::make('cajaybanco/ajax/alistadetallemovimientos',
						 [		 	
						 	'listadetallemovimientos' 		=> $listadetallemovimientos,
						 	'movimiento' 					=> $movimiento,

						 	'nro_operacion' 				=> $nro_operacion,
						 	'cod_caja_banco' 				=> $cod_caja_banco,
						 	'cuenta_referencia' 			=> $nro_referencia,
						 	'fec_movimiento_caja' 			=> $fec_movimiento_caja,

						 	'tipo_asiento' 					=> $tipo_asiento,
						 	'buscar_modelo_asiento' 		=> $buscar_modelo_asiento,
						 	'asientomodelo' 				=> $asientomodelo,
						 	'asiento_array' 				=> $asiento_array,
						 	'detalle_asiento_array' 		=> $detalle_asiento_array,
						 	'idopcion' 						=> $idopcion,
						 	'funcion' 						=> $funcion,
						 	'ajax' 							=> true,						 	
						 ]);

	}






}
