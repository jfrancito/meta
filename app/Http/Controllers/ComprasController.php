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

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;


use Illuminate\Support\Facades\Storage;

class ComprasController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use ComprasTraits;
	use MovilidadTraits;
	use MigrarCompraTraits;



	public function actionAjaxModalCambiarAsientoFechaEmision(Request $request)
	{


		$asiento_id 			=   $request['asiento_id'];
		$idopcion 				=   $request['idopcion'];
		$ruta 					=   $request['ruta'];
		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$serie 					=   $request['serie'];
		$documento 				=   $request['documento'];
		$igv 					=   $request['igv'];

		$fechaemision 			=   date_format(date_create($request['fechaemision']), 'Ymd');
	    $asiento2 				= 	WEBAsiento::where('COD_ASIENTO','=',$asiento_id)->first();
	    $tipo_asiento 			= 	'TAS0000000000004';

		$anular_asiento 		=   $this->movilidad_anular_asiento($asiento_id,
																	Session::get('usuario_meta')->name,
																	$this->fechaactual);

		WEBHistorialMigrar::where('COD_REFERENCIA','=', $asiento2->TXT_REFERENCIA)->delete();

		$lista_compras_migrar_emitido = $this->mv_lista_compras_migrar_agrupado_emitidoxdocumento($this->array_empresas,
																								  $this->anio_inicio,
																								  $asiento2->TXT_REFERENCIA);

		$lista_compras_migrar_anulado = array();

		$this->mv_agregar_historial_compras($lista_compras_migrar_emitido,$lista_compras_migrar_anulado,$tipo_asiento);


		foreach($lista_compras_migrar_emitido as $index => $item){
			$respuesta = $this->mv_update_historial_compras($item->COD_DOCUMENTO_CTBLE,$tipo_asiento);
		}

		//asignar asiento
		$lista_compras 				= 	$this->mv_lista_compras_asignarxdocumento($this->array_empresas,$tipo_asiento,$asiento2->TXT_REFERENCIA);

		$igv  = (1+($igv/100));
		foreach($lista_compras as $index => $item){
			$respuesta2 = $this->mv_asignar_asiento_modelo_x_fechaemision($item,$tipo_asiento,$fechaemision,$igv);
		}


	    $asiento 					= 	WEBAsiento::where('TXT_REFERENCIA','=',$asiento2->TXT_REFERENCIA)
	    								->where('COD_CATEGORIA_ESTADO_ASIENTO','<>','IACHTE0000000024')
	    								->where('COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000004')
	    								->first();

	    $listaasientomovimientodes 	= 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento->COD_ASIENTO)
	    								->where('COD_ESTADO','=','1')
	    								->where('IND_PRODUCTO','<>','2')
	    								->orderBy('NRO_LINEA', 'asc')
	    								->get();

		$periodo 					= 	CONPeriodo::where('COD_PERIODO','=',$asiento->COD_PERIODO)->first();
	    $anio  						=   $periodo->COD_ANIO;

		foreach($listaasientomovimientodes as $index => $item){
			$this->gn_crear_asiento_destino($asiento->COD_ASIENTO,$item->COD_ASIENTO_MOVIMIENTO,$anio);
		}

		$this->gn_generar_total_asientos($asiento->COD_ASIENTO);


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
		
	    $asiento 					= 	WEBAsiento::where('TXT_REFERENCIA','=',$asiento2->TXT_REFERENCIA)
	    								->where('COD_CATEGORIA_ESTADO_ASIENTO','<>','IACHTE0000000024')
	    								->where('COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000004')
	    								->first();


	    $usuario 				= 	SGDUsuario::where('COD_USUARIO','=',$asiento->COD_USUARIO_CREA_AUD)->first();

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

						 	'combo_nivel_pc' 			=> $combo_nivel_pc,
						 	'combo_cuenta' 				=> $combo_cuenta,
						 	'combo_partida' 			=> $combo_partida,
						 	'combo_activo' 			=> $combo_activo,

						 	'defecto_nivel' 			=> $defecto_nivel,
						 	'defecto_cuenta' 			=> $defecto_cuenta,
						 	'defecto_partida' 			=> $defecto_partida,
						 	'defecto_activo' 			=> $defecto_activo,
						 	'ruta' 						=> $ruta,
						 	'usuario' 					=> $usuario,


						 	'ajax' 						=> true,						 	
						 ]);
	}

	public function actionAjaxEditarAsientoContableMovimiento(Request $request)
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

		//dd($asiento);


		if($accion == 'editar'){

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


			$asientomovimiento->COD_CUENTA_CONTABLE 	= 	$cuentacontable->id;
			$asientomovimiento->TXT_CUENTA_CONTABLE 	= 	$cuentacontable->nro_cuenta;
			$asientomovimiento->TXT_GLOSA 				= 	$cuentacontable->nombre;

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


				foreach($listaasientomovimientocc as $key => $item){



					if($cuentacontable_seleccionado->cuenta_contable_transferencia_debe <> '' and $cuentacontable_seleccionado->cuenta_contable_transferencia_haber <>''){
						

						$monto = $item->CAN_DEBE_MN+$item->CAN_HABER_MN;
						//debe
						if($item->TXT_CUENTA_CONTABLE==$cuenta_destino_debe and $monto_original = $monto and $sw_destino_debe == 0){

							$cuentacontable_debe            =   WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
																->where('anio','=',$periodo->COD_ANIO)
																->where('nro_cuenta','=',$cuentacontable_seleccionado->cuenta_contable_transferencia_debe)
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
																->where('nro_cuenta','=',$cuentacontable_seleccionado->cuenta_contable_transferencia_haber)
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




	public function actionAjaxModalCrearDetalleAsientoDiario(Request $request)
	{

		$asiento_compra_id 		=   $request['asiento_id'];
		$idopcion 				=   $request['idopcion'];

		$anio 					=   $request['anio'];
		$ruta 					=   $request['ruta'];

		$periodo_id 			=   $request['periodo_id'];
		$serie 					=   $request['serie'];
		$documento 				=   $request['documento'];
		$empresa 				=   Session::get('empresas_meta')->COD_EMPR;

	    $asiento_compra 		= 	WEBAsiento::where('COD_ASIENTO','=',$asiento_compra_id)->first();

	    $existe_asiento_diario  =	$this->co_existe_asiento_diario_compra($asiento_compra->TXT_REFERENCIA,'TAS0000000000007','COMPRA-DIARIO');

	    if(count($existe_asiento_diario)<=0){

	  
		    //ASIENTO
			$IND_TIPO_OPERACION = 'I';
			$COD_ASIENTO = '';
			$COD_EMPR = $asiento_compra->COD_EMPR;
			$COD_CENTRO = $asiento_compra->COD_CENTRO;
			$COD_PERIODO = $asiento_compra->COD_PERIODO;
			$COD_CATEGORIA_TIPO_ASIENTO = 'TAS0000000000007';
			$TXT_CATEGORIA_TIPO_ASIENTO = 'DIARIO';
			$NRO_ASIENTO = $asiento_compra->NRO_ASIENTO;
			$FEC_ASIENTO = $asiento_compra->FEC_ASIENTO;
			$TXT_GLOSA = str_replace("COMPRA", "DIARIO", $asiento_compra->TXT_GLOSA);

			$COD_CATEGORIA_ESTADO_ASIENTO = 'IACHTE0000000032';
			$TXT_CATEGORIA_ESTADO_ASIENTO = 'TRANSICION';
			$COD_CATEGORIA_MONEDA = $asiento_compra->COD_CATEGORIA_MONEDA;
			$TXT_CATEGORIA_MONEDA = $asiento_compra->TXT_CATEGORIA_MONEDA;
			$CAN_TIPO_CAMBIO = $asiento_compra->CAN_TIPO_CAMBIO;
			$CAN_TOTAL_DEBE = $asiento_compra->CAN_TOTAL_DEBE;
			$CAN_TOTAL_HABER = $asiento_compra->CAN_TOTAL_HABER;
			$COD_ASIENTO_EXTORNO = $asiento_compra->COD_ASIENTO_EXTORNO;
			$COD_ASIENTO_EXTORNADO = $asiento_compra->COD_ASIENTO_EXTORNADO;
			$IND_EXTORNO = $asiento_compra->IND_EXTORNO;

			$COD_ASIENTO_MODELO = $asiento_compra->COD_ASIENTO_MODELO;
			$TXT_TIPO_REFERENCIA = $asiento_compra->TXT_TIPO_REFERENCIA;
			$TXT_REFERENCIA = $asiento_compra->TXT_REFERENCIA;
			$COD_ESTADO = $asiento_compra->COD_ESTADO;
			$COD_USUARIO_REGISTRO = $asiento_compra->COD_USUARIO_REGISTRO;
			$COD_MOTIVO_EXTORNO = $asiento_compra->COD_MOTIVO_EXTORNO;
			$GLOSA_EXTORNO = $asiento_compra->GLOSA_EXTORNO;
			$COD_EMPR_CLI = $asiento_compra->COD_EMPR_CLI;
			$TXT_EMPR_CLI = $asiento_compra->TXT_EMPR_CLI;
			$COD_CATEGORIA_TIPO_DOCUMENTO = $asiento_compra->COD_CATEGORIA_TIPO_DOCUMENTO;

			$TXT_CATEGORIA_TIPO_DOCUMENTO = $asiento_compra->TXT_CATEGORIA_TIPO_DOCUMENTO;
			$NRO_SERIE = $asiento_compra->NRO_SERIE;
			$NRO_DOC = $asiento_compra->NRO_DOC;
			$FEC_DETRACCION = $asiento_compra->FEC_DETRACCION;
			$NRO_DETRACCION = $asiento_compra->NRO_DETRACCION;
			$CAN_DESCUENTO_DETRACCION = $asiento_compra->CAN_DESCUENTO_DETRACCION;
			$CAN_TOTAL_DETRACCION = $asiento_compra->CAN_TOTAL_DETRACCION;
			$COD_CATEGORIA_TIPO_DOCUMENTO_REF = $asiento_compra->COD_CATEGORIA_TIPO_DOCUMENTO_REF;
			$TXT_CATEGORIA_TIPO_DOCUMENTO_REF = $asiento_compra->TXT_CATEGORIA_TIPO_DOCUMENTO_REF;
			$NRO_SERIE_REF = $asiento_compra->NRO_SERIE_REF;
			$NRO_DOC_REF = $asiento_compra->NRO_DOC_REF;
			$FEC_VENCIMIENTO = $asiento_compra->FEC_VENCIMIENTO;
			$IND_AFECTO = $asiento_compra->IND_AFECTO;

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

			$asientomodificado 				= 	WEBAsiento::where('COD_ASIENTO','=',$asientocontable)->first();
			$asientomodificado->COD_OBJETO_ORIGEN = 'COMPRA-DIARIO';
			$asientomodificado->save();
			//DETALLE ASIENTO

		    $detalle_asiento_compra 		= 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento_compra_id)->get();



		    foreach($detalle_asiento_compra as $index => $item){

				$IND_TIPO_OPERACION = 'I';
				$COD_ASIENTO_MOVIMIENTO = '';
				$COD_EMPR = $item->COD_EMPR;
				$COD_CENTRO = $item->COD_CENTRO;
				$COD_ASIENTO = $asientocontable;
				$COD_CUENTA_CONTABLE = $item->COD_CUENTA_CONTABLE;
				$TXT_CUENTA_CONTABLE = $item->TXT_CUENTA_CONTABLE;
				$TXT_GLOSA = $item->TXT_GLOSA;
				$CAN_DEBE_MN = $item->CAN_DEBE_MN;
				$CAN_HABER_MN = $item->CAN_HABER_MN;

				$CAN_DEBE_ME = $item->CAN_DEBE_ME;
				$CAN_HABER_ME = $item->CAN_HABER_ME;
				$NRO_LINEA = $item->NRO_LINEA;
				$COD_CUO = $item->COD_CUO;
				$IND_EXTORNO = $item->IND_EXTORNO;
				$TXT_TIPO_REFERENCIA = $item->TXT_TIPO_REFERENCIA;
				$TXT_REFERENCIA = $item->TXT_REFERENCIA;
				$COD_ESTADO = $item->COD_ESTADO;
				$COD_USUARIO_REGISTRO = $item->COD_USUARIO_REGISTRO;
				$COD_DOC_CTBLE_REF = $item->COD_DOC_CTBLE_REF;

				$COD_ORDEN_REF 	= $item->COD_ORDEN_REF;
				$IND_PRODUCTO 	= $item->IND_PRODUCTO;


				$detalle     	= 	$this->gn_crear_detalle_asiento_contable_movimiento(	$IND_TIPO_OPERACION,
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

															$COD_ORDEN_REF,
															$IND_PRODUCTO);

			}

		}else{
			$asientocontable 	=	$existe_asiento_diario->COD_ASIENTO;
		}

		$asiento 				= 	WEBAsiento::where('COD_ASIENTO','=',$asientocontable)->first();
	    $listaasientomovimiento = 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asientocontable)
	    							->where('COD_ESTADO','=','1')->orderBy('NRO_LINEA', 'asc')->get();

        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);

	    $combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$sel_periodo 			=	'';

		$orden					=	$this->co_orden_xdocumento_contable($asiento_compra->TXT_REFERENCIA);
		$sel_tipo_descuento		=	$this->co_orden_compra_tipo_descuento($orden);
		$combo_descuento 		= 	$this->co_generacion_combo_detraccion('DESCUENTO','Seleccione tipo descuento','');
		$funcion 				= 	$this;
		


        $array_nivel_pc     	= 	$this->pc_array_nivel_cuentas_contable(Session::get('empresas_meta')->COD_EMPR,$anio);
		$combo_nivel_pc  		= 	$this->gn_generacion_combo_array('Seleccione nivel', '' , $array_nivel_pc);
		$defecto_nivel 			= 	'6';

		//dd($combo_nivel_pc);

		$array_cuenta 	    	= 	$this->pc_array_nro_cuentas_nombre_xnivel(Session::get('empresas_meta')->COD_EMPR,$defecto_nivel,$anio);
		$combo_cuenta  			= 	$this->gn_generacion_combo_array('Seleccione cuenta contable', '' , $array_cuenta);

		$combo_partida 			= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_PARTIDA','Seleccione partida','');
		$funcion 				= 	$this;

		$defecto_cuenta			= 	'';
		$defecto_partida		= 	'';
		$asiento_modelo_detalle_id = '';

		$combo_activo 			= 	array('1' => 'ACTIVO','0' => 'ELIMINAR');
		$defecto_activo			= 	'1';



		return View::make('compras/modal/ajax/mdetalleasientodiario',
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

						 	'combo_nivel_pc' 			=> $combo_nivel_pc,
						 	'combo_cuenta' 				=> $combo_cuenta,
						 	'combo_partida' 			=> $combo_partida,
						 	'combo_activo' 			=> $combo_activo,

						 	'defecto_nivel' 			=> $defecto_nivel,
						 	'defecto_cuenta' 			=> $defecto_cuenta,
						 	'defecto_partida' 			=> $defecto_partida,
						 	'defecto_activo' 			=> $defecto_activo,
						 	'ruta' 						=> $ruta,


						 	'ajax' 						=> true,						 	
						 ]);
	}




	public function actionGuardarDiarioReversionCuentaContable($idopcion,Request $request)
	{


		$cabecera 				=   json_decode($request['cabecera_diario'],false);
		$detalle 				=   json_decode($request['detalle_diario'],false);

		$cabecera_compra 		=   json_decode($request['cabecera_compra'],false);
		$detalle_compra 		=   json_decode($request['detalle_compra'],false);


		$periodo_f_id 			=   $request['periodore_id'];
		$serie_f 				=   $request['seriere'];
		$documento_f 			=   $request['documentore'];
		$anio_f 				=   $request['aniore'];

		$anio 					=   $request['anio_asiento'];
		$periodo_id 			=   $request['periodo_asiento_id'];

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
			$FEC_ASIENTO = $item->fecha;
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
			$TXT_REFERENCIA = $item->referencia;
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
			$TXT_CUENTA_CONTABLE = $item->cuenta_nrocuenta;
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


		//CABECERA
		foreach($cabecera_compra as $index => $item){


			$IND_TIPO_OPERACION = 'I';
			$COD_ASIENTO = '';
			$COD_EMPR = $empresa_id;
			$COD_CENTRO = $centro_id;
			$COD_PERIODO = $periodo->COD_PERIODO;
			$COD_CATEGORIA_TIPO_ASIENTO = 'TAS0000000000004';
			$TXT_CATEGORIA_TIPO_ASIENTO = 'COMPRA';
			$NRO_ASIENTO = '';
			$FEC_ASIENTO = $item->fecha;
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
			$TXT_REFERENCIA = $item->referencia;
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

    		$asientocontablecompra     	= 	$this->gn_crear_asiento_contable($IND_TIPO_OPERACION,
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

		foreach($detalle_compra as $index => $item){

			$IND_TIPO_OPERACION = 'I';
			$COD_ASIENTO_MOVIMIENTO = '';
			$COD_EMPR = $empresa_id;
			$COD_CENTRO = $centro_id;
			$COD_ASIENTO = $asientocontablecompra;
			$COD_CUENTA_CONTABLE = $item->cuenta_id;
			$TXT_CUENTA_CONTABLE = $item->cuenta_nrocuenta;
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


		Session::flash('periodo_id_confirmar', $periodo_f_id);
		Session::flash('nro_serie_confirmar', $serie_f);
		Session::flash('nro_doc_confirmar', $documento_f);
		Session::flash('anio_confirmar', $anio_f);
		return Redirect::to('/gestion-listado-compras/'.$idopcion)->with('bienhecho', 'Registro cuenta contable exitoso');

	}



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



	public function actionTransicionConfiguracionAsientoContablesXDocumentos($idopcion,$idasiento,Request $request)
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

 		 	return Redirect::to('/gestion-listado-compras/'.$idopcion)->with('bienhecho', 'Asiento Modelo '.$asiento->NRO_SERIE.'-'.$asiento->NRO_DOC.' transicion con exito');
		
		}


	}



	public function actionGonfirmarConfiguracionAsientoContablesXDocumentos($idopcion,$idasiento,Request $request)
	{

		if($_POST)
		{

			//dd($request['periodo_asiento_id']);
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

	public function actionAjaxModalDetalleAsientoTransicion(Request $request)
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

	    $combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$sel_periodo 			=	'';

		$orden					=	$this->co_orden_xdocumento_contable($asiento->TXT_REFERENCIA);
		$sel_tipo_descuento		=	$this->co_orden_compra_tipo_descuento($orden);
		$combo_descuento 		= 	$this->co_generacion_combo_detraccion('DESCUENTO','Seleccione tipo descuento','');
		$funcion 				= 	$this;
		

		return View::make('compras/modal/ajax/mdetalleasientotransicion',
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






	public function actionAjaxModalDetalleAsientoConfirmar(Request $request)
	{


		$asiento_id 			=   $request['asiento_id'];
		$idopcion 				=   $request['idopcion'];

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$serie 					=   $request['serie'];
		$documento 				=   $request['documento'];
		$ruta 					=   $request['ruta'];

		$anio  					=   $anio;

	    $asiento 				= 	WEBAsiento::where('COD_ASIENTO','=',$asiento_id)->first();
	    $listaasientomovimiento = 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento_id)->where('COD_ESTADO','=','1')->orderBy('NRO_LINEA', 'asc')->get();

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
		$funcion 				= 	$this;

		$defecto_cuenta			= 	'';
		$defecto_partida		= 	'';
		$asiento_modelo_detalle_id = '';

		$combo_activo 			= 	array('1' => 'ACTIVO','0' => 'ELIMINAR');
		$defecto_activo			= 	'1';


		//usuario registro factura
	    $usuario 				= 	SGDUsuario::where('COD_USUARIO','=',$asiento->COD_USUARIO_CREA_AUD)->first();



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

						 	'combo_nivel_pc' 			=> $combo_nivel_pc,
						 	'combo_cuenta' 				=> $combo_cuenta,
						 	'combo_partida' 			=> $combo_partida,
						 	'defecto_nivel' 			=> $defecto_nivel,
						 	'defecto_cuenta' 			=> $defecto_cuenta,
						 	'defecto_partida' 			=> $defecto_partida,
						 	'combo_activo' 				=> $combo_activo,
						 	'defecto_activo' 			=> $defecto_activo,
						 	'ruta' 						=> $ruta,
						 	'usuario' 					=> $usuario,

						 	'ajax' 						=> true,						 	
						 ]);
	}

	public function actionAjaxModalDetalleAsientoDiarioCompra(Request $request)
	{

		$asiento_compra_id 		=   $request['asiento_id'];
		$idopcion 				=   $request['idopcion'];

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$serie 					=   $request['serie'];
		$documento 				=   $request['documento'];
		$empresa 				=   Session::get('empresas_meta')->COD_EMPR;

	    $asiento_compra 		= 	WEBAsiento::where('COD_ASIENTO','=',$asiento_compra_id)->first();
	    $cod_contable           =	$asiento_compra->TXT_REFERENCIA;
	    $tipo_asiento 			=   'TAS0000000000007';
	    $documento_anulado		=   1;

		//generar_asiemto  DIARIO
		$respuesta              =	$this->co_buscar_asiento_diario_compra($anio,$empresa,$cod_contable,$tipo_asiento,$documento_anulado);

		$encontro_asiento       =   $respuesta[0]['codigo'];
		$mensaje_asiento        =   $respuesta[0]['mensaje'];
		$asiento_modelo_id      =   $respuesta[0]['asiento_modelo_id'];
		$existe_asiento_diario  =	$this->co_existe_asiento_diario_compra($cod_contable,$tipo_asiento,'COMPRA-DIARIOCOMPRA');

		$asiento_id 		    =   '';

		if($encontro_asiento=='1'){
			if(count($existe_asiento_diario) <=0){

				$respuesta          =	$this->co_asignar_asiento_diario_compra($anio,$empresa,$cod_contable,$tipo_asiento,$documento_anulado,$asiento_modelo_id);


				$existe_asiento_diario  =	$this->co_existe_asiento_diario_compra($cod_contable,$tipo_asiento,'COMPRA-DIARIOCOMPRA');		
			}
		}

		if(count($existe_asiento_diario) >0){
			$asiento_id 		    =   $existe_asiento_diario->COD_ASIENTO;		
		}


		$asiento 				= 	WEBAsiento::where('COD_ASIENTO','=',$asiento_id)->first();
	    $listaasientomovimiento = 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento_id)->where('COD_ESTADO','=','1')->orderBy('NRO_LINEA', 'asc')->get();

        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
	    $combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$sel_periodo 			=	'';

		$orden					=	$this->co_orden_xdocumento_contable($asiento_compra->TXT_REFERENCIA);
		$sel_tipo_descuento		=	$this->co_orden_compra_tipo_descuento($orden);
		$combo_descuento 		= 	$this->co_generacion_combo_detraccion('DESCUENTO','Seleccione tipo descuento','');
		$funcion 				= 	$this;
		
		return View::make('compras/modal/ajax/mdetalleasientodiariocompra',
						 [
						 	'asiento'					=> $asiento,
						 	'listaasientomovimiento'	=> $listaasientomovimiento,
						 	'combo_periodo'				=> $combo_periodo,
						 	'combo_anio_pc'				=> $combo_anio_pc,
						 	'anio'						=> $anio,
						 	'sel_periodo'				=> $sel_periodo,
						 	'mensaje_asiento'			=> $mensaje_asiento,
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


	public function actionAjaxModalDetalleAsientoDiarioReversion(Request $request)
	{

		$asiento_id 			=   $request['asiento_id'];
		$idopcion 				=   $request['idopcion'];

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$serie 					=   $request['serie'];
		$documento 				=   $request['documento'];
		$empresa 				=   Session::get('empresas_meta')->COD_EMPR;



	    $asiento 				= 	WEBAsiento::where('COD_ASIENTO','=',$asiento_id)->first();
	    $detalleasiento 		= 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento_id)->first();

	    $cod_contable           =	$asiento->TXT_REFERENCIA;
	    $tipo_asiento 			=   'TAS0000000000007';
	    $documento_anulado		=   1;
		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
		$empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;


		$monto_total 			=	$asiento->CAN_TOTAL_DEBE;
		$glosa 					= 	$asiento->TXT_GLOSA;
		$moneda_id 				=	$asiento->COD_CATEGORIA_MONEDA;
		$moneda 				= 	$asiento->TXT_CATEGORIA_MONEDA;
		$tipo_cambio 			= 	$asiento->CAN_TIPO_CAMBIO;
		$tipo_referencia 		= 	'WEB.asientos';
		$referencia 			= 	$asiento->COD_ASIENTO;

		$cabecera_diario 		=	$this->co_cabecera_asiento($periodo,$empresa_id,$monto_total,$glosa,$moneda_id,$moneda,$tipo_cambio,$tipo_referencia,$referencia);

		$detalle_diario 		=   $this->co_detalle_asiento($asiento_id,$periodo,$empresa_id,$moneda_id,$moneda,$tipo_cambio,$asiento);


	    $tipo_asiento 			=   'TAS0000000000004';

		$cabecera_compra 		=	$this->co_cabecera_asiento($periodo,$empresa_id,$monto_total,$glosa,$moneda_id,$moneda,$tipo_cambio,$tipo_referencia,$referencia);

		$detalle_compra 		=   $this->co_detalle_asiento_compra($asiento_id,$periodo,$empresa_id,$moneda_id,$moneda,$tipo_cambio,$asiento);


		$funcion 				= 	$this;
		$glosa   				=	'COMPRAS QUE NO SE PAGARON DETRACCION';


        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);

	    $combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$sel_periodo 			=	'';

		$ind_existe_asiento 	=   $this->co_existe_asiento_reversion($empresa_id,$asiento);


		return View::make('compras/modal/ajax/mdetalleasientodiarioreversion',
						 [
						 	'asiento'					=> $asiento,
						 	'cabecera_diario'			=> $cabecera_diario,
						 	'detalle_diario'			=> $detalle_diario,
						 	'cabecera_compra'			=> $cabecera_compra,
						 	'detalle_compra'			=> $detalle_compra,
						 	'glosa'						=> $glosa,
						 	'periodo' 					=> $periodo,
						 	'idopcion' 					=> $idopcion,
						 	'combo_periodo'				=> $combo_periodo,
						 	'combo_anio_pc'				=> $combo_anio_pc,
						 	'anio'						=> $anio,
						 	'sel_periodo'				=> $sel_periodo,
						 	'serie'						=> $serie,
						 	'documento'					=> $documento,
						 	'ind_existe_asiento'		=> $ind_existe_asiento,
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
	    $empresa_id = Session::get('empresas_meta')->COD_EMPR;

		if(Session::has('periodo_id_confirmar')){

			$sel_periodo 			=	Session::get('periodo_id_confirmar');
			$sel_serie 				=	Session::get('nro_serie_confirmar');
			$sel_nrodoc 			=	Session::get('nro_doc_confirmar');
			$anio 					=	Session::get('anio_confirmar');

			$array_terminado_diario =   $this->co_array_terminado_diario($sel_periodo,$empresa_id);
        	$listacompras     		= 	$this->co_lista_compras_asiento($anio,$sel_periodo,Session::get('empresas_meta')->COD_EMPR,$sel_serie,$sel_nrodoc,$array_terminado_diario);
        	$listacomprasterminado  = 	$this->co_lista_compras_terminado_asiento($anio,$sel_periodo,Session::get('empresas_meta')->COD_EMPR,$sel_serie,$sel_nrodoc,$array_terminado_diario);
        	$listadiariosterminado  = 	$this->co_lista_diarios_terminado_asiento($anio,$sel_periodo,Session::get('empresas_meta')->COD_EMPR,$sel_serie,$sel_nrodoc,$array_terminado_diario);

		}else{

			$sel_periodo 			=	'';
			$sel_serie 				=	'';
			$sel_nrodoc 			=	'';
			$anio  					=   $this->anio;
	    	$listacompras 			= 	array();
	    	$listacomprasterminado 	= 	array();
	    	$listadiariosterminado 	= 	array();
	    	
		}

        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');

		$funcion 				= 	$this;
		
		return View::make('compras/listacompras',
						 [
						 	'listacompras' 			=> $listacompras,
						 	'listacomprasterminado' => $listacomprasterminado,
						 	'listadiariosterminado' => $listadiariosterminado,
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
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;

		$array_terminado_diario =   $this->co_array_terminado_diario($periodo_id,$empresa_id);
        $listacompras     		= 	$this->co_lista_compras_asiento($anio,$periodo_id,Session::get('empresas_meta')->COD_EMPR,$serie,$documento,$array_terminado_diario);

        $listacomprasterminado  = 	$this->co_lista_compras_terminado_asiento($anio,$periodo_id,Session::get('empresas_meta')->COD_EMPR,$serie,$documento,$array_terminado_diario);

        $listadiariosterminado 	= 	$this->co_lista_diarios_terminado_asiento($anio,$periodo_id,Session::get('empresas_meta')->COD_EMPR,$serie,$documento,$array_terminado_diario);

		$funcion 				= 	$this;
		
		return View::make('compras/ajax/alistacompras',
						 [
						 	'listacompras'			=> $listacompras,
						 	'listacomprasterminado'	=> $listacomprasterminado,
						 	'listadiariosterminado'	=> $listadiariosterminado,
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
