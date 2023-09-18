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
use App\Modelos\CONPeriodo;



use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;
use App\Traits\MovilidadTraits;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;



use Illuminate\Support\Facades\Storage;

class MovilidadController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use MovilidadTraits;



	public function actionGuardarMovilidadCuentaContable($idopcion,Request $request)
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

		Session::flash('periodo_id_confirmar', $periodo->COD_PERIODO);
		return Redirect::to('/gestion-planilla-movilidad/'.$idopcion)->with('bienhecho', 'Registro cuenta contable exitoso');

	}







	public function actionAjaxModalConfiguracionMovilidadCuentaContable(Request $request)
	{
		
		$datastring 			=   json_encode($request['datastring'],false);

		$data_tabla 			=   $request['data_tabla'];
		$periodo_registrado 	=   $request['periodo_registrado'];
		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_registrado)->first();
		$empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
		$data_archivo 			=   $request['data_archivo'];
    	$idopcion 				=   $request['idopcion'];


    	$listamovilidad     	= 	$this->movilidad_lista_movilidad('LISTA_MOVILIDAD',$empresa_id,$periodo_registrado);
		$array_asoc  			=   $this->movilidad_array_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $periodo_registrado, $empresa_id,$data_archivo);
		$monto_total 			=   $this->movilidad_monto_total_asiento($listamovilidad,$array_asoc);
		//$glosanumdoc			=	$this->movilidad_glosa_asiento($listamovilidad,$array_asoc);

		$glosa   				=	'';
		$tipo_referencia   		=	'';

		if($data_archivo=='agregarmobilidadgeneral'){
			$glosa   			=	'MOVILIDAD : PLANILLA DE MOVILIDAD';
			$tipo_referencia   	=	'MOVILIDAD';
		}else{
			$glosa   			=	'MOVILIDAD : PLANILLA DE MOVILIDAD  REPARABLE';
			$tipo_referencia   	=	'MOVILIDAD_REPARABLE';
		}
		$moneda_id   			=	'MON0000000000001';
		$moneda   				=	'SOLES';
		//$glosa   				=	$glosa.' ('.$glosanumdoc.')';


		$fecha_cambio			=   date_format(date_create(substr($periodo->FEC_FIN, 0, 10)), 'Ymd');
		$tipo_cambio 			=	$this->gn_tipo_cambio($fecha_cambio);

		$array_asiento_modelo 	=   $this->movilidad_array_asiento_modelo($data_archivo);
		$cabecera 				=   $this->movilidad_cabecera_asiento($periodo,$empresa_id,$monto_total,$glosa,$moneda_id,$moneda,$tipo_cambio,$tipo_referencia);
		$detalle 				=   $this->movilidad_detalle_asiento($array_asiento_modelo,$periodo,$empresa_id,$moneda_id,$moneda,$monto_total,$tipo_cambio);

		$funcion 				= 	$this;




		return View::make('movilidad/modal/ajax/ammovilidadcc',
						 [		 	
						 	'cabecera' 				=> $cabecera,
						 	'detalle' 				=> $detalle,
						 	'periodo' 				=> $periodo,
						 	'glosa' 				=> $glosa,
						 	'funcion' 				=> $funcion,
						 	'idopcion' 				=> $idopcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}




	public function actionMobilidadGuardarData(Request $request)
	{

		$data_archivo 			=   json_decode($request['data_archivo'], true);
		$anio 					=   $request['anio'];
		$idopcion 				=   $request['idopcion'];
		$opcion_val 			=   $request['opcion_val'];				
		$vacio 					=	'';
		$valor_cero 			=	0;
		$accion 				=	'I';
		$empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
		$codestado 				=	1;
		$estado 				=	'IACHTE0000000032';
		$glosa 					=	'ASOCIACION DE MOVILIDAD CONTABILIDAD';	
		$descripcion 			=	'CC_MOBILIDAD_CONTABLE';
		$referencia    			=	$opcion_val;

		$centro_id 				=	'CEN0000000000001';
		$tipo_asiento_id		=	'TAS0000000000007';
		$tipo_referencia		=	'TAS0000000000007';

		foreach($data_archivo as $key => $obj){

			$periodo_id 		=   $obj['periodo_id'];
			$documento_id 		=   $obj['documento_id'];

			//quitar
			if($opcion_val=='quitarmobilidad'){
				$quitar  		=   $this->movilidad_extornar_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $periodo_id, $empresa_id,$documento_id);
			}else{

				if($opcion_val=='eliminarmobilidadgeneral'){
					$quitar  		=   $this->movilidad_extornar_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $periodo_id, $empresa_id,$documento_id);

					$asiento_id				=   $this->gn_encontrar_cod_asiento($empresa_id, $centro_id, 
																$periodo_id, $tipo_asiento_id,'MOVILIDAD');

					$anular_asiento 		=   $this->movilidad_anular_asiento($asiento_id,
																				Session::get('usuario_meta')->name,$this->fechaactual);

				}else{

					if($opcion_val=='eliminarmobilidadreparacion'){
						$quitar  		=   $this->movilidad_extornar_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $periodo_id, $empresa_id,$documento_id);

						$asiento_id				=   $this->gn_encontrar_cod_asiento($empresa_id, $centro_id, 
																	$periodo_id, $tipo_asiento_id,'MOVILIDAD_REPARABLE');

						$anular_asiento 		=   $this->movilidad_anular_asiento($asiento_id,
																					Session::get('usuario_meta')->name,$this->fechaactual);

					}else{
								$existeasoc  	=   $this->movilidad_existe_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $periodo_id, $empresa_id,$documento_id);
								if(count($existeasoc)>0){
						        	$existeasoc->TXT_TABLA_ASOC = 'IACHTE0000000032';
						        	$existeasoc->TXT_REFERENCIA = $referencia;
						        	$existeasoc->COD_ESTADO = 1;
						        	$existeasoc->save();
						        }else{
						        	$asociacionmobilidad     = 	$this->movilidad_guardar_asociacion(
						        								$accion,//@IND_TIPO_OPERACION='I',
						        								$periodo_id,//@COD_TABLA='IILMNC0000000495',
						        								$documento_id,//@COD_TABLA_ASOC='IILMFC0000005728',
						        								$empresa_id,//@TXT_TABLA='CMP.DOCUMENTO_CTBLE',
						        								$estado,//@TXT_TABLA_ASOC='CMP.DOCUMENTO_CTBLE',
						        								$glosa,//@TXT_GLOSA='NOTA DE CREDITO F005-00000420 / ',
						        								$vacio,//@TXT_TIPO_REFERENCIA='',
						        								$referencia,//@TXT_REFERENCIA='',
						        								$codestado,//@COD_ESTADO=1,
						        								$vacio,//@COD_USUARIO_REGISTRO='PHORNALL',
						        								$descripcion,//@TXT_DESCRIPCION='',
						        								$valor_cero,//@CAN_AUX1=0,
						        								$valor_cero,//@CAN_AUX2=0,
						        								$valor_cero//@CAN_AUX3=0,
						        								);
						        }
					}

				}
			}
		}	

		Session::flash('periodo_id_confirmar', $periodo_id);
		return Redirect::to('/gestion-planilla-movilidad/'.$idopcion)->with('bienhecho', 'Registro exitoso');

	}


	public function actionListarMovilidad($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Lista de registro de movilidad');

	    $empresa_id 				= 	Session::get('empresas_meta')->COD_EMPR;

		if(Session::has('periodo_id_confirmar')){
			$sel_periodo 			=	Session::get('periodo_id_confirmar');
		}else{
			$sel_periodo 			=	'';
		}


		$referencia 			=	'';
		$array_asoc  			=   $this->movilidad_array_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $sel_periodo, $empresa_id,$referencia);
        $listamovilidad     	= 	$this->movilidad_lista_movilidad('LISTA_MOVILIDAD',$empresa_id,$sel_periodo);

        //general
		$referencia 			=	'agregarmobilidadgeneral';
		$array_asoc_general  	=   $this->movilidad_array_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $sel_periodo, $empresa_id,$referencia);    
        $listamovilidad_general =	$this->movilidad_lista_movilidad('LISTA_MOVILIDAD',$empresa_id,$sel_periodo);

        //reparacion
		$referencia 			=	'agregarmobilidadreparacion';
		$array_asoc_reparacion  =   $this->movilidad_array_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $sel_periodo, $empresa_id,$referencia);    
        $listamovilidad_reparacion =	$this->movilidad_lista_movilidad('LISTA_MOVILIDAD',$empresa_id,$sel_periodo);



	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$funcion 				= 	$this;
		
		return View::make('movilidad/listamovilidad',
						 [
						 	'array_asoc' 			=> $array_asoc,
						 	'listamovilidad' 		=> $listamovilidad,

						 	'array_asoc_general' 	=> $array_asoc_general,
						 	'listamovilidad_general'=> $listamovilidad_general,

						 	'array_asoc_reparacion' => $array_asoc_reparacion,
						 	'listamovilidad_reparacion' => $listamovilidad_reparacion,

						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_periodo'	 		=> $sel_periodo,

						 	'periodo_id'			=> $sel_periodo,
						 							 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}



	public function actionAjaxRegistroMovilidad(Request $request)
	{


		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$empresa_id 			=   Session::get('empresas_meta')->COD_EMPR;

		$referencia 			=	'';
		$array_asoc  			=   $this->movilidad_array_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $periodo_id, $empresa_id,$referencia);
        $listamovilidad     	= 	$this->movilidad_lista_movilidad('LISTA_MOVILIDAD',$empresa_id,$periodo_id);

        //general
		$referencia 			=	'agregarmobilidadgeneral';
		$array_asoc_general  	=   $this->movilidad_array_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $periodo_id, $empresa_id,$referencia);    
        $listamovilidad_general =	$this->movilidad_lista_movilidad('LISTA_MOVILIDAD',$empresa_id,$periodo_id);

        //reparacion
		$referencia 			=	'agregarmobilidadreparacion';
		$array_asoc_reparacion  =   $this->movilidad_array_asociacion_proviciones('CC_MOBILIDAD_CONTABLE', $periodo_id, $empresa_id,$referencia);    
        $listamovilidad_reparacion =	$this->movilidad_lista_movilidad('LISTA_MOVILIDAD',$empresa_id,$periodo_id);


		$funcion 				= 	$this;
		
		return View::make('movilidad/ajax/alistamovilidad',
						 [
						 	'array_asoc' 			=> $array_asoc,
						 	'listamovilidad'		=> $listamovilidad,

						 	'array_asoc_general' 	=> $array_asoc_general,
						 	'listamovilidad_general'=> $listamovilidad_general,

						 	'array_asoc_reparacion' => $array_asoc_reparacion,
						 	'listamovilidad_reparacion' => $listamovilidad_reparacion,

						 	'funcion'				=> $funcion,

						 	'periodo_id'			=> $periodo_id,

						 	'ajax' 					=> true,						 	
						 ]);
	}




}
