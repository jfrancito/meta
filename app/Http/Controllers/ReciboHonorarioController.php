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
use App\Modelos\WEBHistorialMigrar;
use App\Modelos\SGDUsuario;

use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;
use App\Traits\ComprasTraits;
use App\Traits\MovilidadTraits;
use App\Traits\MigrarCompraTraits;

use App\Traits\ReciboHonorarioTraits;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;


use Illuminate\Support\Facades\Storage;

class ReciboHonorarioController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use ComprasTraits;
	use ReciboHonorarioTraits;


	use MovilidadTraits;
	use MigrarCompraTraits;


	public function actionListarReciboHonorario($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Gestionar asiento de compras');
	    $empresa_id = Session::get('empresas_meta')->COD_EMPR;

		if(Session::has('periodo_id_confirmar')){
			$sel_periodo 			=	Session::get('periodo_id_confirmar');
			$sel_serie 				=	Session::get('nro_serie_confirmar');
			$sel_nrodoc 			=	Session::get('nro_doc_confirmar');
			$anio 					=	Session::get('anio_confirmar');





        	$listacompras     		= 	$this->rh_lista_compras_asiento($anio,$sel_periodo,Session::get('empresas_meta')->COD_EMPR,$sel_serie,$sel_nrodoc);

        	$listacomprasterminado  = 	$this->rh_lista_compras_terminado_asiento($anio,$sel_periodo,Session::get('empresas_meta')->COD_EMPR,$sel_serie,$sel_nrodoc);




		}else{
			$sel_periodo 			=	'';
			$sel_serie 				=	'';
			$sel_nrodoc 			=	'';
			$anio  					=   $this->anio;
	    	$listacompras 			= 	array();
	    	$listacomprasterminado 	= 	array();



		}

        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');

		$funcion 				= 	$this;
		
		return View::make('recibohonorario/listarecibohonorario',
						 [
						 	'listacompras' 			=> $listacompras,
						 	'listacomprasterminado' => $listacomprasterminado,
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


	public function actionAjaxListarReciboHonorario(Request $request)
	{

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$serie 					=   $request['serie'];
		$documento 				=   $request['documento'];
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;



        $listacompras     		= 	$this->rh_lista_compras_asiento($anio,$periodo_id,Session::get('empresas_meta')->COD_EMPR,$serie,$documento);

        $listacomprasterminado  = 	$this->rh_lista_compras_terminado_asiento($anio,$periodo_id,Session::get('empresas_meta')->COD_EMPR,$serie,$documento);


		$funcion 				= 	$this;
		
		return View::make('recibohonorario/ajax/alistarecibohonorario',
						 [
						 	'listacompras'			=> $listacompras,
						 	'listacomprasterminado'	=> $listacomprasterminado,
						 	'funcion'				=> $funcion,			 	
						 	'ajax' 					=> true,						 	
						 ]);
	}




	public function actionAjaxModalDetalleAsientoRHConfirmar(Request $request)
	{


		$asiento_id 			=   $request['asiento_id'];
		$idopcion 				=   $request['idopcion'];

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$serie 					=   $request['serie'];
		$documento 				=   $request['documento'];
		$ruta 					=   $request['ruta'];

	    $asiento 				= 	WEBAsiento::where('COD_ASIENTO','=',$asiento_id)->first();
	    $listaasientomovimiento = 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento_id)->where('COD_ESTADO','=',1)->orderBy('NRO_LINEA', 'asc')->get();

        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
	    $anio  					=   $this->anio;
	    $combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$sel_periodo 			=	'';

		$orden					=	$this->co_orden_xdocumento_contable($asiento->TXT_REFERENCIA);
		$sel_tipo_descuento		=	$this->co_orden_compra_tipo_descuento($orden);
		$combo_descuento 		= 	$this->co_generacion_combo_detraccion('DESCUENTO','Seleccione tipo descuento','');
		$funcion 				= 	$this;
		
        $array_nivel_pc     	= 	$this->pc_array_nivel_cuentas_contable(Session::get('empresas_meta')->COD_EMPR,$anio);
		$combo_nivel_pc  		= 	$this->gn_generacion_combo_array('Seleccione nivel', '' , $array_nivel_pc);
		$defecto_nivel 			= 	'6';

		$combo_partida 			= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_PARTIDA','Seleccione partida','');
		$defecto_cuenta			= 	'';
		$defecto_partida		= 	'';
		$asiento_modelo_detalle_id = '';

		$combo_activo 			= 	array('1' => 'ACTIVO','0' => 'ELIMINAR');
		$defecto_activo			= 	'1';
		$array_cuenta 	    	= 	$this->pc_array_nro_cuentas_nombre_xnivel(Session::get('empresas_meta')->COD_EMPR,$defecto_nivel,$anio);
		$combo_cuenta  			= 	$this->gn_generacion_combo_array('Seleccione cuenta contable', '' , $array_cuenta);



		return View::make('recibohonorario/modal/ajax/mdetalleasientoconfirmar',
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

						 	'array_nivel_pc'			=> $array_nivel_pc,
						 	'combo_nivel_pc'			=> $combo_nivel_pc,
						 	'defecto_nivel'				=> $defecto_nivel,


						 	'combo_partida'				=> $combo_partida,
						 	'defecto_cuenta'			=> $defecto_cuenta,
						 	'defecto_partida'			=> $defecto_partida,
						 	'asiento_modelo_detalle_id'	=> $asiento_modelo_detalle_id,
						 	'array_cuenta'				=> $array_cuenta,
						 	'combo_cuenta'				=> $combo_cuenta,

						 	'combo_activo'				=> $combo_activo,
						 	'defecto_activo'			=> $defecto_activo,
						 	'ruta'						=> $ruta,


						 	'ajax' 						=> true,						 	
						 ]);
	}



	public function actionAjaxEditarAsientoContableMovimientoRH(Request $request)
	{

		$cuenta_contable_id 					=   $request['cuenta_contable_id'];
		$monto 									=   $request['monto'];
		$asiento_movimiento_id 					=   $request['asiento_movimiento_id'];
		$partida_id 							=   $request['partida_id'];
		$activo 								=   $request['activo'];
		$accion 								=   $request['accion'];
		$COD_ASIENTO 							=   $request['asiento_id'];
		$anio 									=   $request['anio'];
		$idopcion 								=   $request['idopcion'];
		$periodo_id 							=   $request['periodo_id'];
		$serie 									=   $request['serie'];
		$documento 								=   $request['documento'];
		$ruta 									=   $request['ruta'];



		if($accion == 'editar'){

			$glosa_editar 							=	'';
			$asientomovimiento 						=	WEBAsientoMovimiento::where('COD_ASIENTO_MOVIMIENTO','=',$asiento_movimiento_id)->first();
			$cuentacontable 						=	WEBCuentaContable::where('id','=',$cuenta_contable_id)->first();
			$asiento 								=	WEBAsiento::where('COD_ASIENTO','=',$asientomovimiento->COD_ASIENTO)->first();
			$cuenta_sinmodificar 					=	WEBCuentaContable::where('id','=',$asientomovimiento->COD_CUENTA_CONTABLE)->first();

			$periodo 								= 	CONPeriodo::where('COD_PERIODO','=',$asiento->COD_PERIODO)->first();

			$COD_ASIENTO 							=	$asientomovimiento->COD_ASIENTO;


			$m_debe_mn =0;
			$m_haber_mn =0;
			$m_debe_me =0;
			$m_haber_me =0;

			if($partida_id=='COP0000000000001'){//debe
				if($asiento->COD_CATEGORIA_MONEDA=='MON0000000000001'){//soles
					$m_debe_mn 	=	$monto;
					$m_debe_me 	=	$monto/$asiento->CAN_TIPO_CAMBIO;
				}else{//DOLARES
					$m_debe_mn 	=	$monto*$asiento->CAN_TIPO_CAMBIO;
					$m_debe_me 	=	$monto;	
				}
			}else{
				if($asiento->COD_CATEGORIA_MONEDA=='MON0000000000001'){//soles
					$m_haber_mn 	=	$monto;
					$m_haber_me 	=	$monto/$asiento->CAN_TIPO_CAMBIO;
				}else{//DOLARES
					$m_haber_mn 	=	$monto*$asiento->CAN_TIPO_CAMBIO;
					$m_haber_me 	=	$monto;	
				}
			}

			$glosa_editar 			=	$cuentacontable->nombre;
			if($asientomovimiento->IND_PRODUCTO == '1'){
				$glosa_editar 		=	$asientomovimiento->TXT_GLOSA;
			}



			$asientomovimiento->COD_CUENTA_CONTABLE 	= 	$cuentacontable->id;
			$asientomovimiento->TXT_CUENTA_CONTABLE 	= 	$cuentacontable->nro_cuenta;
			$asientomovimiento->TXT_GLOSA 				= 	$glosa_editar;
			$asientomovimiento->CAN_DEBE_MN 			= 	$m_debe_mn;
			$asientomovimiento->CAN_HABER_MN 			= 	$m_haber_mn;
			$asientomovimiento->CAN_DEBE_ME 			= 	$m_debe_me;				
			$asientomovimiento->CAN_HABER_ME 			= 	$m_haber_me;
			$asientomovimiento->COD_ESTADO 				=   $activo;
			$asientomovimiento->FEC_USUARIO_MODIF_AUD 	=   $this->fechaactual;
			$asientomovimiento->COD_USUARIO_MODIF_AUD 	=   Session::get('usuario_meta')->id;
			$asientomovimiento->save();

								
			//CREAR DESTINOS
			//tiene destino la cuenta anterior?
			$sw_tiene_destino 			= 0;
			$cuenta_destino_debe 	    = '';
			$cuenta_destino_haber 	    = '';
			$sw_destino_debe 	    	= 0;
			$sw_destino_haber 	    	= 0;
			$monto_original				= 0;
			$monto     					= 0;


			if($cuenta_sinmodificar->cuenta_contable_transferencia_debe <> '' and $cuenta_sinmodificar->cuenta_contable_transferencia_haber <>''){
				$sw_tiene_destino = 1;
				$cuenta_destino_debe = $cuenta_sinmodificar->cuenta_contable_transferencia_debe;
				$cuenta_destino_haber = $cuenta_sinmodificar->cuenta_contable_transferencia_haber;
				$monto_original	= $cuenta_sinmodificar->CAN_DEBE_MN+$cuenta_sinmodificar->CAN_HABER_MN;

			}

			if($sw_tiene_destino == 1){

				$listaasientomovimientoer 					=	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asientomovimiento->COD_ASIENTO)
																->where('COD_ESTADO','=','1')
																->where('IND_PRODUCTO','=','2')
																->where('TXT_REFERENCIA','=',$asientomovimiento->COD_ASIENTO_MOVIMIENTO)
																->orderBy('NRO_LINEA','ASC')
																->get();
				//existe referencia
				if(count($listaasientomovimientoer)<=0){
					//agregar referencia
					$listaasientomovimientoar 					=	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asientomovimiento->COD_ASIENTO)
																	->where('COD_ESTADO','=','1')
																	->where('IND_PRODUCTO','=','2')
																	->orderBy('NRO_LINEA','ASC')
																	->get();
					//asignarreferencia
					foreach($listaasientomovimientoar as $key => $item){


						$monto = $item->CAN_DEBE_MN+$item->CAN_HABER_MN;

						if($item->TXT_CUENTA_CONTABLE==$cuenta_destino_debe and $monto_original = $monto and $sw_destino_debe == 0){
							$item->TXT_TIPO_REFERENCIA = 'WEB.asientomovimientos';
							$item->TXT_REFERENCIA = $asientomovimiento->COD_ASIENTO_MOVIMIENTO;
							$item->save();
							$sw_destino_debe = 1;

						}
						if($item->TXT_CUENTA_CONTABLE==$cuenta_destino_haber and $monto_original = $monto and $sw_destino_haber == 0 ){
							$item->TXT_TIPO_REFERENCIA = 'WEB.asientomovimientos';
							$item->TXT_REFERENCIA = $asientomovimiento->COD_ASIENTO_MOVIMIENTO;
							$item->save();
							$sw_destino_haber = 1;
						}

					}


				}

				//cambiar cuenta destino
				$listaasientomovimientocc 					=	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asientomovimiento->COD_ASIENTO)
																->where('COD_ESTADO','=','1')
																->where('IND_PRODUCTO','=','2')
																->where('TXT_REFERENCIA','=',$asientomovimiento->COD_ASIENTO_MOVIMIENTO)
																->orderBy('NRO_LINEA','ASC')
																->get();
				$sw_destino_debe = 0;
				$sw_destino_haber = 0;	

				$cuentacontable_seleccionado            	=   WEBCuentaContable::where('id','=',$cuenta_contable_id)
										    					->first();

				$nrocuentadebe 								=	trim($cuentacontable_seleccionado->cuenta_contable_transferencia_debe);
				$nrocuentahaber 							=	trim($cuentacontable_seleccionado->cuenta_contable_transferencia_haber);



				foreach($listaasientomovimientocc as $key => $item){



					if($nrocuentadebe <> '' and $nrocuentahaber <>''){
						

						$monto = $item->CAN_DEBE_MN+$item->CAN_HABER_MN;
						//debe
						if($item->TXT_CUENTA_CONTABLE==$cuenta_destino_debe and $monto_original = $monto and $sw_destino_debe == 0){

							$cuentacontable_debe            =   WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
																->where('anio','=',$periodo->COD_ANIO)
																->where('nro_cuenta','=',$nrocuentadebe)
										    					->first();

							$item->COD_CUENTA_CONTABLE 		= 	$cuentacontable_debe->id;
							$item->TXT_CUENTA_CONTABLE 		= 	$cuentacontable_debe->nro_cuenta;
							$item->TXT_GLOSA 				= 	$cuentacontable_debe->nombre;
							$item->CAN_DEBE_MN 				= 	$m_debe_mn+$m_haber_mn;
							$item->CAN_HABER_MN 			= 	0;
							$item->CAN_DEBE_ME 				= 	$m_debe_me+$m_haber_me;				
							$item->CAN_HABER_ME 			= 	0;
							$item->COD_ESTADO 				=   $activo;
							$item->FEC_USUARIO_MODIF_AUD 	=   $this->fechaactual;
							$item->COD_USUARIO_MODIF_AUD 	=   Session::get('usuario_meta')->id;
							$item->save();
							$sw_destino_debe = 1;

						}

						//haber
						if($item->TXT_CUENTA_CONTABLE==$cuenta_destino_haber and $monto_original = $monto and $sw_destino_haber == 0 ){

							$cuentacontable_haber            =   WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
																->where('anio','=',$periodo->COD_ANIO)
																->where('nro_cuenta','=',$nrocuentahaber)
										    					->first();

							$item->COD_CUENTA_CONTABLE 		= 	$cuentacontable_haber->id;
							$item->TXT_CUENTA_CONTABLE 		= 	$cuentacontable_haber->nro_cuenta;
							$item->TXT_GLOSA 				= 	$cuentacontable_haber->nombre;
							$item->CAN_DEBE_MN 				= 	0;
							$item->CAN_HABER_MN 			= 	$m_debe_mn+$m_haber_mn;
							$item->CAN_DEBE_ME 				= 	0;				
							$item->CAN_HABER_ME 			= 	$m_debe_me+$m_haber_me;
							$item->COD_ESTADO 				=   $activo;
							$item->FEC_USUARIO_MODIF_AUD 	=   $this->fechaactual;
							$item->COD_USUARIO_MODIF_AUD 	=   Session::get('usuario_meta')->id;
							$item->save();

							$sw_destino_haber = 1;
						}
					}else{

							$item->COD_ESTADO 				=   0;
							$item->FEC_USUARIO_MODIF_AUD 	=   $this->fechaactual;
							$item->COD_USUARIO_MODIF_AUD 	=   Session::get('usuario_meta')->id;
							$item->save();

					}
				}
			}




			$listaasientomovimientolinea 				=	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asientomovimiento->COD_ASIENTO)
															->where('COD_ESTADO','=','1')
															->orderBy('NRO_LINEA','ASC')
															->get();

			//REEORGANIZAR LINEAS											
			$count 										=	1;												
			foreach($listaasientomovimientolinea as $key => $item){
				$item->NRO_LINEA    =   $count;
				$item->save();
				$count 				=	$count+1;	
			}


		}else{

				$asiento 								=	WEBAsiento::where('COD_ASIENTO','=',$COD_ASIENTO)->first();

				$periodo 								= 	CONPeriodo::where('COD_PERIODO','=',$asiento->COD_PERIODO)->first();

				$CAN_DEBE_MN =0;
				$CAN_HABER_MN =0;
				$CAN_DEBE_ME =0;
				$CAN_HABER_ME =0;

				if($partida_id=='COP0000000000001'){//debe
					if($asiento->COD_CATEGORIA_MONEDA=='MON0000000000001'){//soles
						$CAN_DEBE_MN 	=	$monto;
						$CAN_DEBE_ME 	=	$monto/$asiento->CAN_TIPO_CAMBIO;
					}else{//DOLARES
						$CAN_DEBE_MN 	=	$monto*$asiento->CAN_TIPO_CAMBIO;
						$CAN_DEBE_ME 	=	$monto;	
					}
				}else{
					if($asiento->COD_CATEGORIA_MONEDA=='MON0000000000001'){//soles
						$CAN_HABER_MN 	=	$monto;
						$CAN_HABER_ME 	=	$monto/$asiento->CAN_TIPO_CAMBIO;
					}else{//DOLARES
						$CAN_HABER_MN 	=	$monto*$asiento->CAN_TIPO_CAMBIO;
						$CAN_HABER_ME 	=	$monto;	
					}
				}

				$IND_TIPO_OPERACION = 'I';
				$COD_ASIENTO_MOVIMIENTO = '';
				$COD_EMPR = $asiento->COD_EMPR;
				$COD_CENTRO = $asiento->COD_CENTRO;
				$COD_ASIENTO = $asiento->COD_ASIENTO;

				$cuentacontable 						=	WEBCuentaContable::where('id','=',$cuenta_contable_id)->first();

				$COD_CUENTA_CONTABLE = $cuentacontable->id;
				$TXT_CUENTA_CONTABLE = $cuentacontable->nro_cuenta;
				$TXT_GLOSA = $cuentacontable->nombre;

				$asientomovimiento 						=	WEBAsientoMovimiento::where('COD_ASIENTO','=',$COD_ASIENTO)
															->where('COD_ESTADO','=','1')
															->get();

				$NRO_LINEA = count($asientomovimiento)+1;
				$COD_CUO = '';
				$IND_EXTORNO = '0';
				$TXT_TIPO_REFERENCIA = '';
				$TXT_REFERENCIA = '';
				$COD_ESTADO = '1';
				$COD_USUARIO_REGISTRO = Session::get('usuario_meta')->name;
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


				$this->gn_crear_asiento_destino($COD_ASIENTO,$detalle,$periodo->COD_ANIO);

		}


		$this->gn_generar_total_asientos($COD_ASIENTO);




		$asiento 				=	WEBAsiento::where('COD_ASIENTO','=',$COD_ASIENTO)->first();
	    $listaasientomovimiento = 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento->COD_ASIENTO)->where('COD_ESTADO','=','1')->orderBy('NRO_LINEA', 'asc')->get();

        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);

	    $combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$sel_periodo 			=	'';

		$orden					=	$this->co_orden_xdocumento_contable($asiento->TXT_REFERENCIA);
		$sel_tipo_descuento		=	$this->co_orden_compra_tipo_descuento($orden);
		$combo_descuento 		= 	$this->co_generacion_combo_detraccion('DESCUENTO','Seleccione tipo descuento','');


		$funcion 				= 	$this;
		
        $array_nivel_pc     	= 	$this->pc_array_nivel_cuentas_contable(Session::get('empresas_meta')->COD_EMPR,$anio);
		$combo_nivel_pc  		= 	$this->gn_generacion_combo_array('Seleccione nivel', '' , $array_nivel_pc);
		$defecto_nivel 			= 	'6';

		$array_cuenta 	    	= 	$this->pc_array_nro_cuentas_nombre_xnivel(Session::get('empresas_meta')->COD_EMPR,$defecto_nivel,$anio);
		$combo_cuenta  			= 	$this->gn_generacion_combo_array('Seleccione cuenta contable', '' , $array_cuenta);

		$combo_partida 			= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_PARTIDA','Seleccione partida','');

		$defecto_cuenta			= 	'';
		$defecto_partida		= 	'';
		$asiento_modelo_detalle_id = '';

		$combo_activo 			= 	array('1' => 'ACTIVO','0' => 'ELIMINAR');
		$defecto_activo			= 	'1';

	    $usuario 				= 	SGDUsuario::where('COD_USUARIO','=',$asiento->COD_USUARIO_CREA_AUD)->first();


		return View::make('recibohonorario/modal/ajax/mdetalleasientoconfirmar',
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


						 	'periodo_id'				=> $periodo_id,
						 	'serie'						=> $serie,
						 	'documento'					=> $documento,

						 	'combo_nivel_pc' 			=> $combo_nivel_pc,
						 	'combo_cuenta' 				=> $combo_cuenta,
						 	'combo_partida' 			=> $combo_partida,
						 	'combo_activo' 				=> $combo_activo,

						 	'defecto_nivel' 			=> $defecto_nivel,
						 	'defecto_cuenta' 			=> $defecto_cuenta,
						 	'defecto_partida' 			=> $defecto_partida,
						 	'defecto_activo' 			=> $defecto_activo,
						 	'ruta' 						=> $ruta,
						 	'usuario' 					=> $usuario,

						 	'ajax' 						=> true,						 	
						 ]);



	}


	public function actionGonfirmarConfiguracionAsientoRHContablesXDocumentos($idopcion,$idasiento,Request $request)
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

 		 	return Redirect::to('/gestion-recibo-honorario/'.$idopcion)->with('bienhecho', 'Asiento Modelo '.$asiento->NRO_SERIE.'-'.$asiento->NRO_DOC.' confirmado con exito');
		
		}


	}


    public function actionAjaxModalDetalleAsientoRHTransicion(Request $request)
    {


        $asiento_id             =   $request['asiento_id'];
        $idopcion               =   $request['idopcion'];

        $anio                   =   $request['anio'];
        $periodo_id             =   $request['periodo_id'];
        $serie                  =   $request['serie'];
        $documento              =   $request['documento'];


        $asiento                =   WEBAsiento::where('COD_ASIENTO','=',$asiento_id)->first();
        $listaasientomovimiento =   WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento_id)->where('COD_ESTADO','=',1)->orderBy('NRO_LINEA', 'asc')->get();

        $array_anio_pc          =   $this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
        $anio                   =   $this->anio;
        $combo_anio_pc          =   $this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
        $combo_periodo          =   $this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
        $sel_periodo            =   '';

        $orden                  =   $this->co_orden_xdocumento_contable($asiento->TXT_REFERENCIA);
        $sel_tipo_descuento     =   $this->co_orden_compra_tipo_descuento($orden);
        $combo_descuento        =   $this->co_generacion_combo_detraccion('DESCUENTO','Seleccione tipo descuento','');
        $funcion                =   $this;
        

        return View::make('recibohonorario/modal/ajax/mdetalleasientotransicion',
                         [
                            'asiento'                   => $asiento,
                            'listaasientomovimiento'    => $listaasientomovimiento,
                            'combo_periodo'             => $combo_periodo,
                            'combo_anio_pc'             => $combo_anio_pc,
                            'anio'                      => $anio,
                            'sel_periodo'               => $sel_periodo,
                            'sel_tipo_descuento'        => $sel_tipo_descuento,
                            'combo_descuento'           => $combo_descuento,
                            'orden'                     => $orden,
                            'idopcion'                  => $idopcion,
                            'anio'                      => $anio,
                            'periodo_id'                => $periodo_id,
                            'serie'                     => $serie,
                            'documento'                 => $documento,
                            'ajax'                      => true,                            
                         ]);
    }


	public function actionTransicionConfiguracionAsientoContablesRHXDocumentos($idopcion,$idasiento,Request $request)
	{

		if($_POST)
		{


			$anio_asiento 					= $request['anio_asiento'];


			$anio_confirmar 				= $request['anio_configuracion'];
			$periodo_id_confirmar 			= $request['periodo_id_configuracion'];
			$nro_serie_confirmar 			= $request['serie_configuracion'];
			$nro_doc_confirmar 				= $request['documento_configuracion'];

			$asiento 								= 	WEBAsiento::where('COD_ASIENTO','=',$idasiento)->first();

			$asiento->COD_CATEGORIA_ESTADO_ASIENTO 	=   'IACHTE0000000032';
			$asiento->TXT_CATEGORIA_ESTADO_ASIENTO 	=   'TRANSICION';
			$asiento->FEC_USUARIO_MODIF_AUD 		=   $this->fechaactual;
			$asiento->COD_USUARIO_MODIF_AUD 		=   Session::get('usuario_meta')->id;
			$asiento->save();


			Session::flash('periodo_id_confirmar', $periodo_id_confirmar);
			Session::flash('nro_serie_confirmar', $nro_serie_confirmar);
			Session::flash('nro_doc_confirmar', $nro_doc_confirmar);
			Session::flash('anio_confirmar', $anio_confirmar);

 		 	return Redirect::to('/gestion-recibo-honorario/'.$idopcion)->with('bienhecho', 'Asiento Modelo '.$asiento->NRO_SERIE.'-'.$asiento->NRO_DOC.' transicion con exito');
		
		}


	}

}
