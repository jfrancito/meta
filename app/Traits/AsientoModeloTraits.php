<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\STDEmpresa;
use App\Modelos\CMPCategoria;
use App\Modelos\TESCajaBanco;
use App\Modelos\CONPeriodo;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


trait AsientoModeloTraits
{

	private function am_debe_haber($cod_partida)
	{
		$txt_partida 		= 		'';
		if($cod_partida=='COP0000000000001'){
			$txt_partida 	= 		'DEBE';
		}else{
			$txt_partida 	= 		'HABER';
		}
		return $txt_partida;
	}


	public function am_array_rel_ter()
	{
		$rel_ter 		= 	array(	
								array(
									'ABREVIATURA' => 'COB.REL.SOL',
									'DOCUMENTO' => "121201",
									'ANTICIPO'  => "122101"
								),
								array(
									'ABREVIATURA' => 'COB.REL.DOL',
									'DOCUMENTO' => "121201",
									'ANTICIPO'  => "122102"
								),
								array(
									'ABREVIATURA' => 'COB.TER.SOL',
									'DOCUMENTO' => "131211",
									'ANTICIPO'  => "132111"
								),
								array(
									'ABREVIATURA' => 'COB.TER.DOL',
									'DOCUMENTO' => "121201",
									'ANTICIPO'  => "121201"
								),

								array(
									'ABREVIATURA' => 'PAG.REL.SOL',
									'DOCUMENTO' => "421203",
									'ANTICIPO'  => "422101"
								),
								array(
									'ABREVIATURA' => 'PAG.REL.DOL',
									'DOCUMENTO' => "421204",
									'ANTICIPO'  => "422102"
								),
								array(
									'ABREVIATURA' => 'PAG.TER.SOL',
									'DOCUMENTO' => "431211",
									'ANTICIPO'  => "432111"
								),
								array(
									'ABREVIATURA' => 'PAG.TER.DOL',
									'DOCUMENTO' => "431211",
									'ANTICIPO'  => "432111"
								)
							);
	 	return  $rel_ter;
	}



	private function am_abreviatura_asiento_modelo($pago_cobro,$empresa_rel_ter,$movimiento)
	{

		if($pago_cobro=='EPC0000000000001'){
			$abrevitura 		= 		'PAG';
		}else{
			$abrevitura 		= 		'COB';
		}
		
		if($empresa_rel_ter=='1'){
			$abrevitura 		= 		$abrevitura.'.REL';
		}else{
			$abrevitura 		= 		$abrevitura.'.TER';
		}

		if($movimiento->COD_CATEGORIA_MONEDA=='MON0000000000001'){
			$abrevitura 		= 		$abrevitura.'.SOL';
		}else{
			$abrevitura 		= 		$abrevitura.'.DOL';
		}


		return $abrevitura;
	}

	private function am_asiento_modelo_detalle($cod_categoria_tipo_documento,$buscar_modelo_asiento)
	{
		$asientomodelodetalle 				= 	'';
		if($buscar_modelo_asiento['encontro'] == '1'){
			$listaasientomodelodetalle  =   WEBAsientoModeloReferencia::where('asiento_modelo_id','=',$buscar_modelo_asiento['msg'])
											->where('documento_id','=',$cod_categoria_tipo_documento)
											->where('activo','=',1)
											->get();

			if(count($listaasientomodelodetalle)>0){

				$asientomodelodetalle  =    WEBAsientoModeloDetalle::join('WEB.cuentacontables', 'WEB.cuentacontables.id', '=', 'WEB.asientomodelodetalles.cuenta_contable_id')
											->where('WEB.asientomodelodetalles.asiento_modelo_id','=',$buscar_modelo_asiento['msg'])
											->whereNotIn('WEB.cuentacontables.nro_cuenta', ['1041','776101','676101'])
											->where('WEB.cuentacontables.nombre', 'not like', '%ANTICIPO%')
											->first();
			}else{

				$asientomodelodetalle  =    WEBAsientoModeloDetalle::join('WEB.cuentacontables', 'WEB.cuentacontables.id', '=', 'WEB.asientomodelodetalles.cuenta_contable_id')
											->where('WEB.asientomodelodetalles.asiento_modelo_id','=',$buscar_modelo_asiento['msg'])
											->whereNotIn('WEB.cuentacontables.nro_cuenta', ['1041','776101','676101'])
											->where('WEB.cuentacontables.nombre', 'like', '%ANTICIPO%')
											->first();
			}
		}
		return $asientomodelodetalle;
	}

	private function am_totales_asiento_array($movimiento,$tipo_asiento,$anio,$pago_cobro,$empresa_rel_ter,$buscar_modelo_asiento,$nro_operacion,$nro_referencia,$asiento_array,$detalle_asiento_array)
	{
		


	    	if($movimiento->COD_CATEGORIA_MONEDA=='MON0000000000001'){
	    		$asiento_array['CAN_TOTAL_DEBE'] = array_sum(array_column($detalle_asiento_array,'CAN_DEBE_MN'));
				$asiento_array['CAN_TOTAL_HABER'] = array_sum(array_column($detalle_asiento_array,'CAN_HABER_MN'));
	    	}else{

	    		$asiento_array['CAN_TOTAL_DEBE'] = array_sum(array_column($detalle_asiento_array,'CAN_DEBE_ME'));
				$asiento_array['CAN_TOTAL_HABER'] = array_sum(array_column($detalle_asiento_array,'CAN_HABER_ME'));
	    	}

	    
		return $asiento_array;

	}


	private function am_asiento_detalle_array($movimiento,$tipo_asiento,$anio,$pago_cobro,$empresa_rel_ter,$buscar_modelo_asiento,$nro_operacion,$nro_referencia,$listadetallemovimientos,$funcion,$existe_rel_ter,$abreviatura_asiento_modelo)
	{
		$array_detalle_asiento  		= 	array();

		if($buscar_modelo_asiento['encontro'] == '1'){

			$listaasientomodelodetalle  =   WEBAsientoModeloDetalle::where('asiento_modelo_id','=',$buscar_modelo_asiento['msg'])
											->get();


			//CAJA BANCO 1041
			foreach($listaasientomodelodetalle as $index => $item){

				if($item->cuentacontable->nro_cuenta == '1041'){
					$debe_haber  = $this->am_debe_haber($item->partida_id);
					$DEBE_MN=0;
					$HABER_MN=0;
					$DEBE_ME=0;
					$HABER_ME=0;

                  	$tipo_cambio_cp   =   $funcion->gn_tipo_cambio(date_format(date_create($movimiento->FEC_OPERACION), 'd-m-Y'));

					if($debe_haber == 'DEBE'){
			            if($nro_referencia == '42'){
			            	if($movimiento->TXT_CATEGORIA_MONEDA == 'DOLARES'){
				            	$DEBE_MN=$movimiento->CAN_HABER_ME*$tipo_cambio_cp->CAN_VENTA_SBS;
								$DEBE_ME=$movimiento->CAN_HABER_ME; 
			            	}else{

			            		$DEBE_MN=$movimiento->CAN_HABER_MN;
								$DEBE_ME=$movimiento->CAN_HABER_MN/$tipo_cambio_cp->CAN_VENTA_SBS;
			            	}
			            }else{
			            	if($movimiento->TXT_CATEGORIA_MONEDA == 'DOLARES'){
				            	$DEBE_MN=$movimiento->CAN_DEBE_ME*$tipo_cambio_cp->CAN_VENTA_SBS;
								$DEBE_ME=$movimiento->CAN_DEBE_ME;
			            	}else{

			            		$DEBE_MN=$movimiento->CAN_DEBE_MN;
								$DEBE_ME=$movimiento->CAN_DEBE_MN/$tipo_cambio_cp->CAN_VENTA_SBS;
			            	}
			            }
					}else{
						                  		    
			            if($nro_referencia == '42'){
			            	if($movimiento->TXT_CATEGORIA_MONEDA == 'DOLARES'){
				            	$HABER_MN=$movimiento->CAN_HABER_ME*$tipo_cambio_cp->CAN_VENTA_SBS;
								$HABER_ME=$movimiento->CAN_HABER_ME;
			            	}else{

			            		$HABER_MN=$movimiento->CAN_HABER_MN;
								$HABER_ME=$movimiento->CAN_HABER_MN/$tipo_cambio_cp->CAN_VENTA_SBS;

			            	}
			            }else{
			            	if($movimiento->TXT_CATEGORIA_MONEDA == 'DOLARES'){
				            	$HABER_MN=$movimiento->CAN_DEBE_ME*$tipo_cambio_cp->CAN_VENTA_SBS;
								$HABER_ME=$movimiento->CAN_DEBE_ME;
			            	}else{
				            	$HABER_MN=$movimiento->CAN_DEBE_MN;
								$HABER_ME=$movimiento->CAN_DEBE_MN/$tipo_cambio_cp->CAN_VENTA_SBS;
			            	}
			            }
					}


					$tescajabanco 		= 	TESCajaBanco::where('COD_CAJA_BANCO','=',$movimiento->COD_CAJA_BANCO)->first();
				    $cuentacontable 	= 	WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
											->where('anio','=',$anio)
											->where('id','=',$tescajabanco->TXT_TIPO_REFERENCIA)
											->where('activo','=',1)
											->first();
					$COD_CUENTA_CONTABLE='';
					$TXT_CUENTA_CONTABLE='';
					if(count($cuentacontable)>0){
						$COD_CUENTA_CONTABLE=$cuentacontable->id;
						$TXT_CUENTA_CONTABLE=$cuentacontable->nro_cuenta;
					}

					$IND_TIPO_OPERACION='I';
					$COD_EMPR=Session::get('empresas_meta')->COD_EMPR;
					$COD_CENTRO=$movimiento->COD_CENTRO;
					$COD_CUENTA_CONTABLE=$COD_CUENTA_CONTABLE;
					$TXT_CUENTA_CONTABLE=$TXT_CUENTA_CONTABLE;
					$TXT_GLOSA='CANC: DOC-VARIOS: ';//falta
					$CAN_DEBE_MN=$DEBE_MN;
					$CAN_HABER_MN=$HABER_MN;
					$CAN_DEBE_ME=$DEBE_ME;
					$CAN_HABER_ME=$HABER_ME;

					$NRO_LINEA=1;
					$COD_CUO='';
					$IND_EXTORNO=0;
					$TXT_TIPO_REFERENCIA='';
					$TXT_REFERENCIA='';
					$COD_ESTADO=1;
					$COD_USUARIO_REGISTRO=Session::get('usuario_meta')->name;
					$COD_DOC_CTBLE_REF='';
					$COD_ORDEN_REF='';

			    	$array_nuevo_asiento 	=	array();
					$array_nuevo_asiento    =	array(
						"IND_TIPO_OPERACION" 		=> $IND_TIPO_OPERACION,
						"COD_EMPR" 					=> $COD_EMPR,
						"COD_CENTRO" 				=> $COD_CENTRO,
						"COD_CUENTA_CONTABLE" 		=> $COD_CUENTA_CONTABLE,
						"TXT_CUENTA_CONTABLE" 		=> $TXT_CUENTA_CONTABLE,
						"TXT_GLOSA" 				=> $TXT_GLOSA,
			            "CAN_DEBE_MN" 				=> $CAN_DEBE_MN,
			            "CAN_HABER_MN" 				=> $CAN_HABER_MN,
			            "CAN_DEBE_ME" 				=> $CAN_DEBE_ME,
			            "CAN_HABER_ME" 				=> $CAN_HABER_ME,

			            "NRO_LINEA" 				=> $NRO_LINEA,
			            "COD_CUO" 					=> $COD_CUO,
			            "IND_EXTORNO" 				=> $IND_EXTORNO,
			            "TXT_TIPO_REFERENCIA" 		=> $TXT_TIPO_REFERENCIA,
			            "TXT_REFERENCIA" 			=> $TXT_REFERENCIA,
			            "COD_ESTADO" 				=> $COD_ESTADO,
			            "COD_USUARIO_REGISTRO" 		=> $COD_USUARIO_REGISTRO,
			            "COD_DOC_CTBLE_REF" 		=> $COD_DOC_CTBLE_REF,
			            "COD_ORDEN_REF" 			=> $COD_ORDEN_REF,
					);

					array_push($array_detalle_asiento,$array_nuevo_asiento);

				}
			}




			$count = 2;
			//DOCUMMENTOS Y ANTICIPOS
			foreach($listadetallemovimientos as $index => $item){

				$asientomodelodetalle     	= 	$this->am_asiento_modelo_detalle($item->COD_CATEGORIA_TIPO_DOC,$buscar_modelo_asiento);
				$debe_haber  				= 	$this->am_debe_haber($asientomodelodetalle->partida_id);



                
				$array_relacion_rt   		=   $this->am_array_rel_ter();
			    $empresa_rt 				= 	STDEmpresa::where('COD_EMPR','=',$item->COD_EMPR_AFECTA)->first();
			    if(is_null($empresa_rt->IND_RELACIONADO)){
			    	$valor_re_te   			=   0;
			    }else{
			    	$valor_re_te   			=   $empresa_rt->IND_RELACIONADO;
			    }
                //empresa_relacionada
                if($empresa_rel_ter == $valor_re_te){
					$COD_CUENTA_CONTABLE=$asientomodelodetalle->cuentacontable->id;
					$TXT_CUENTA_CONTABLE=$asientomodelodetalle->cuentacontable->nro_cuenta;
                }else{
				    $key = array_search($abreviatura_asiento_modelo, array_column($array_relacion_rt, 'ABREVIATURA'));
					$cadena_de_texto 		= $asientomodelodetalle->nombre;
					$cadena_buscada   		= 'ANTICIPO';
					$posicion_coincidencia 	= strpos($cadena_de_texto, $cadena_buscada);

					if ($posicion_coincidencia === false) {
					    $nro_cuenta_encontrada = $array_relacion_rt[$key]['DOCUMENTO'];
					}else {
					    $nro_cuenta_encontrada = $array_relacion_rt[$key]['ANTICIPO'];
					}
				    $cuentacontable_e 	= 	WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
											->where('anio','=',$anio)
											->where('nro_cuenta','=',$nro_cuenta_encontrada)
											->first();
					$COD_CUENTA_CONTABLE=$cuentacontable_e->id;
					$TXT_CUENTA_CONTABLE=$cuentacontable_e->nro_cuenta;
                }


				$DEBE_MN=0;
				$HABER_MN=0;
				$DEBE_ME=0;
				$HABER_ME=0;

                $tipo_cambio_cp   			=   $funcion->gn_tipo_cambio(date_format(date_create($item->FEC_OPERACION), 'd-m-Y'));

				if($debe_haber == 'DEBE'){

		            if($nro_referencia == '42'){
		            	if($movimiento->TXT_CATEGORIA_MONEDA == 'DOLARES'){
			            	$DEBE_MN=$item->CAN_DEBE_ME*$tipo_cambio_cp->CAN_VENTA_SBS;
							$DEBE_ME=$item->CAN_DEBE_ME; 
		            	}else{
		            		$DEBE_MN=$item->CAN_DEBE_MN;
							$DEBE_ME=$item->CAN_DEBE_MN/$tipo_cambio_cp->CAN_VENTA_SBS;
		            	}
		            }else{
		            	if($movimiento->TXT_CATEGORIA_MONEDA == 'DOLARES'){
			            	$DEBE_MN=$item->CAN_HABER_ME*$tipo_cambio_cp->CAN_VENTA_SBS;
							$DEBE_ME=$item->CAN_HABER_ME;
		            	}else{
		            		$DEBE_MN=$item->CAN_HABER_MN;
							$DEBE_ME=$item->CAN_HABER_MN/$tipo_cambio_cp->CAN_VENTA_SBS;
		            	}
		            }

				}else{

		            if($nro_referencia == '42'){
		            	if($movimiento->TXT_CATEGORIA_MONEDA == 'DOLARES'){
			            	$HABER_MN=$item->CAN_DEBE_ME*$tipo_cambio_cp->CAN_VENTA_SBS;
							$HABER_ME=$item->CAN_DEBE_ME; 
		            	}else{
		            		$HABER_MN=$item->CAN_DEBE_MN;
							$HABER_ME=$item->CAN_DEBE_MN/$tipo_cambio_cp->CAN_VENTA_SBS;
		            	}
		            }else{
		            	if($movimiento->TXT_CATEGORIA_MONEDA == 'DOLARES'){
			            	$HABER_MN=$item->CAN_HABER_ME*$tipo_cambio_cp->CAN_VENTA_SBS;
							$HABER_ME=$item->CAN_HABER_ME;
		            	}else{
		            		$HABER_MN=$item->CAN_HABER_MN;
							$HABER_ME=$item->CAN_HABER_MN/$tipo_cambio_cp->CAN_VENTA_SBS;
		            	}
		            }


				}



				$IND_TIPO_OPERACION='I';
				$COD_EMPR=Session::get('empresas_meta')->COD_EMPR;
				$COD_CENTRO=$movimiento->COD_CENTRO;
				$COD_CUENTA_CONTABLE=$COD_CUENTA_CONTABLE;
				$TXT_CUENTA_CONTABLE=$TXT_CUENTA_CONTABLE;
				$TXT_GLOSA=$item->TXT_GLOSA;
				$CAN_DEBE_MN=$DEBE_MN;
				$CAN_HABER_MN=$HABER_MN;
				$CAN_DEBE_ME=$DEBE_ME;
				$CAN_HABER_ME=$HABER_ME;

				$NRO_LINEA=$count;
				$COD_CUO='';
				$IND_EXTORNO=0;
				$TXT_TIPO_REFERENCIA='';
				$TXT_REFERENCIA='';
				$COD_ESTADO=1;
				$COD_USUARIO_REGISTRO=Session::get('usuario_meta')->name;
				$COD_DOC_CTBLE_REF='';
				$COD_ORDEN_REF='';

		    	$array_nuevo_asiento 	=	array();
				$array_nuevo_asiento    =	array(
					"IND_TIPO_OPERACION" 		=> $IND_TIPO_OPERACION,
					"COD_EMPR" 					=> $COD_EMPR,
					"COD_CENTRO" 				=> $COD_CENTRO,
					"COD_CUENTA_CONTABLE" 		=> $COD_CUENTA_CONTABLE,
					"TXT_CUENTA_CONTABLE" 		=> $TXT_CUENTA_CONTABLE,
					"TXT_GLOSA" 				=> $TXT_GLOSA,
		            "CAN_DEBE_MN" 				=> $CAN_DEBE_MN,
		            "CAN_HABER_MN" 				=> $CAN_HABER_MN,
		            "CAN_DEBE_ME" 				=> $CAN_DEBE_ME,
		            "CAN_HABER_ME" 				=> $CAN_HABER_ME,

		            "NRO_LINEA" 				=> $NRO_LINEA,
		            "COD_CUO" 					=> $COD_CUO,
		            "IND_EXTORNO" 				=> $IND_EXTORNO,
		            "TXT_TIPO_REFERENCIA" 		=> $TXT_TIPO_REFERENCIA,
		            "TXT_REFERENCIA" 			=> $TXT_REFERENCIA,
		            "COD_ESTADO" 				=> $COD_ESTADO,
		            "COD_USUARIO_REGISTRO" 		=> $COD_USUARIO_REGISTRO,
		            "COD_DOC_CTBLE_REF" 		=> $COD_DOC_CTBLE_REF,
		            "COD_ORDEN_REF" 			=> $COD_ORDEN_REF,
				);

				array_push($array_detalle_asiento,$array_nuevo_asiento);
				$count = $count + 1;
			}	



			//CUENTA PARA DOLARES 
			$array_77_67  = 	array();
			if($movimiento->COD_CATEGORIA_MONEDA == 'MON0000000000002'){
				foreach($listadetallemovimientos as $index => $item){

					$asientomodelodetalle     = 	$this->am_asiento_modelo_detalle($item->COD_CATEGORIA_TIPO_DOC,$buscar_modelo_asiento);
					$debe_haber  			  = 	$this->am_debe_haber($asientomodelodetalle->partida_id);

					$DEBE_MN=0;
					$HABER_MN=0;
					$DEBE_ME=0;
					$HABER_ME=0;

	                $tipo_cambio_doc  		  =     $funcion->gn_tipo_cambio(date_format(date_create($item->FEC_EMISION), 'd-m-Y'));
	                $tipo_cambio_cp   		  =     $funcion->gn_tipo_cambio(date_format(date_create($item->FEC_OPERACION), 'd-m-Y'));

					if($debe_haber == 'DEBE'){


			            if($nro_referencia == '42'){
			            	if($movimiento->TXT_CATEGORIA_MONEDA == 'DOLARES'){
				            	$DEBE_MN=$item->CAN_DEBE_ME*$tipo_cambio_cp->CAN_VENTA_SBS;
								$DEBE_ME=$item->CAN_DEBE_ME; 
			            	}else{
			            		$DEBE_MN=$item->CAN_DEBE_MN;
								$DEBE_ME=$item->CAN_DEBE_MN/$tipo_cambio_cp->CAN_VENTA_SBS;
			            	}
			            }else{
			            	if($movimiento->TXT_CATEGORIA_MONEDA == 'DOLARES'){
				            	$DEBE_MN=$item->CAN_HABER_ME*$tipo_cambio_cp->CAN_VENTA_SBS;
								$DEBE_ME=$item->CAN_HABER_ME;
			            	}else{
			            		$DEBE_MN=$item->CAN_HABER_MN;
								$DEBE_ME=$item->CAN_HABER_MN/$tipo_cambio_cp->CAN_VENTA_SBS;
			            	}
			            }


			    //         if($nro_referencia == '42'){
			    //         	$DEBE_MN=$item->CAN_DEBE_MN;
							// $DEBE_ME=$item->CAN_DEBE_ME;
			    //         }else{
			    //         	$DEBE_MN=$item->CAN_HABER_MN;
							// $DEBE_ME=$item->CAN_HABER_ME;
			    //         }


					}else{


			            if($nro_referencia == '42'){
			            	if($movimiento->TXT_CATEGORIA_MONEDA == 'DOLARES'){
				            	$HABER_MN=$item->CAN_DEBE_ME*$tipo_cambio_cp->CAN_VENTA_SBS;
								$HABER_ME=$item->CAN_DEBE_ME; 
			            	}else{
			            		$HABER_MN=$item->CAN_DEBE_MN;
								$HABER_ME=$item->CAN_DEBE_MN/$tipo_cambio_cp->CAN_VENTA_SBS;
			            	}
			            }else{
			            	if($movimiento->TXT_CATEGORIA_MONEDA == 'DOLARES'){
				            	$HABER_MN=$item->CAN_HABER_ME*$tipo_cambio_cp->CAN_VENTA_SBS;
								$HABER_ME=$item->CAN_HABER_ME;
			            	}else{
			            		$HABER_MN=$item->CAN_HABER_MN;
								$HABER_ME=$item->CAN_HABER_MN/$tipo_cambio_cp->CAN_VENTA_SBS;
			            	}
			            }

			    //         if($nro_referencia == '42'){
			    //         	$HABER_MN=$item->CAN_DEBE_MN;
							// $HABER_ME=$item->CAN_DEBE_ME;
			    //         }else{
			    //         	$HABER_MN=$item->CAN_HABER_MN;
							// $HABER_ME=$item->CAN_HABER_ME;
			    //         }
					}

					$CAN_DOC_MN=$item->CAN_TOTAL*$tipo_cambio_doc->CAN_VENTA_SBS;
					$CAN_DEBE_MN=$DEBE_MN;
					$CAN_HABER_MN=$HABER_MN;
					

					$CAN_MONTO_DEBE_MN=0;
					$CAN_MONTO_HABER_MN=0;
					$CAN_MONTO_DEBE_ME=0;
					$CAN_MONTO_HABER_ME=0;

					//PAGO DEBE
					if($nro_referencia == '42'){

						if($CAN_DOC_MN>$CAN_DEBE_MN){

							$TXT_CUENTA_CONTABLE = '776101';
						    $cuentacontable 	= 	WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
													->where('anio','=',$anio)
													->where('nro_cuenta','=',$TXT_CUENTA_CONTABLE)
													->where('activo','=',1)
													->first();
							$COD_CUENTA_CONTABLE=$cuentacontable->id;
							$TXT_GLOSA=$cuentacontable->nombre;
							$TXT_DEBE_HABER = 'HABER';
							$CAN_MONTO_SOLES = $CAN_DOC_MN - $CAN_DEBE_MN;
							$CAN_MONTO_DOLAR = $CAN_MONTO_SOLES/$tipo_cambio_cp->CAN_VENTA_SBS;

						}else{

							$TXT_CUENTA_CONTABLE = '676101';
						    $cuentacontable 	= 	WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
													->where('anio','=',$anio)
													->where('nro_cuenta','=',$TXT_CUENTA_CONTABLE)
													->where('activo','=',1)
													->first();
							$COD_CUENTA_CONTABLE=$cuentacontable->id;
							$TXT_GLOSA=$cuentacontable->nombre;
							$TXT_DEBE_HABER = 'DEBE';
							$CAN_MONTO_SOLES = $CAN_DEBE_MN - $CAN_DOC_MN;
							$CAN_MONTO_DOLAR = $CAN_MONTO_SOLES/$tipo_cambio_cp->CAN_VENTA_SBS;

						}
					//COBRO - HABER 
					}else{
						if($CAN_DOC_MN<$CAN_HABER_MN){

							$TXT_CUENTA_CONTABLE = '776101';
						    $cuentacontable 	= 	WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
													->where('anio','=',$anio)
													->where('nro_cuenta','=',$TXT_CUENTA_CONTABLE)
													->where('activo','=',1)
													->first();
							$COD_CUENTA_CONTABLE=$cuentacontable->id;
							$TXT_GLOSA=$cuentacontable->nombre;
							$TXT_DEBE_HABER = 'HABER';
							$CAN_MONTO_SOLES = $CAN_HABER_MN - $CAN_DOC_MN;
							$CAN_MONTO_DOLAR = $CAN_MONTO_SOLES/$tipo_cambio_cp->CAN_VENTA_SBS;

						}else{

							$TXT_CUENTA_CONTABLE = '676101';
						    $cuentacontable 	= 	WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
													->where('anio','=',$anio)
													->where('nro_cuenta','=',$TXT_CUENTA_CONTABLE)
													->where('activo','=',1)
													->first();
							$COD_CUENTA_CONTABLE=$cuentacontable->id;
							$TXT_GLOSA=$cuentacontable->nombre;
							$TXT_DEBE_HABER = 'DEBE';
							$CAN_MONTO_SOLES = $CAN_DOC_MN - $CAN_HABER_MN;
							$CAN_MONTO_DOLAR = $CAN_MONTO_SOLES/$tipo_cambio_cp->CAN_VENTA_SBS;

						}
					}


			    	$array_nuevo_asiento 	=	array();
					$array_nuevo_asiento    =	array(
						"COD_CUENTA_CONTABLE" 		=> $COD_CUENTA_CONTABLE,
						"TXT_CUENTA_CONTABLE" 		=> $TXT_CUENTA_CONTABLE,
						"TXT_DEBE_HABER" 			=> $TXT_DEBE_HABER,
						"CAN_MONTO_SOLES" 			=> $CAN_MONTO_SOLES,
						"CAN_MONTO_DOLAR" 			=> $CAN_MONTO_DOLAR,
						"TXT_GLOSA" 				=> $TXT_GLOSA
					);

					array_push($array_77_67,$array_nuevo_asiento);
					$count = $count + 1;
				}
				$result = array();
				foreach($array_77_67 as $t) {
					$repeat=false;
					for($i=0;$i<count($result);$i++)
					{
						if($result[$i]['TXT_CUENTA_CONTABLE']==$t['TXT_CUENTA_CONTABLE'])
						{
							$result[$i]['CAN_MONTO_SOLES']+=$t['CAN_MONTO_SOLES'];
							$result[$i]['CAN_MONTO_DOLAR']+=$t['CAN_MONTO_DOLAR'];
							$repeat=true;
							break;
						}
					}
					if($repeat==false){
						$result[] = array(
											'COD_CUENTA_CONTABLE' => $t['COD_CUENTA_CONTABLE'],
											'TXT_CUENTA_CONTABLE' => $t['TXT_CUENTA_CONTABLE'],
											'TXT_DEBE_HABER' => $t['TXT_DEBE_HABER'],
											'CAN_MONTO_SOLES' => $t['CAN_MONTO_SOLES'],
											'CAN_MONTO_DOLAR' => $t['CAN_MONTO_DOLAR'],
											'TXT_GLOSA' => $t['TXT_GLOSA']
										);
					}
				}

				foreach($result as $t) {

					$CAN_HABER_MN=0;
					$CAN_HABER_ME=0;
					$CAN_DEBE_MN=0;
					$CAN_DEBE_ME=0;
					$IND_TIPO_OPERACION='I';
					$COD_EMPR=Session::get('empresas_meta')->COD_EMPR;
					$COD_CENTRO=$movimiento->COD_CENTRO;
					$COD_CUENTA_CONTABLE=$t['COD_CUENTA_CONTABLE'];
					$TXT_CUENTA_CONTABLE=$t['TXT_CUENTA_CONTABLE'];
					$TXT_GLOSA=$t['TXT_GLOSA'];

					if($t['TXT_DEBE_HABER'] == 'HABER'){
		            	$CAN_HABER_MN=$t['CAN_MONTO_SOLES'];
						$CAN_HABER_ME=$t['CAN_MONTO_DOLAR'];
		            }else{
		            	$CAN_DEBE_MN=$t['CAN_MONTO_SOLES'];
						$CAN_DEBE_ME=$t['CAN_MONTO_DOLAR'];
		            }


					$NRO_LINEA=$count;
					$COD_CUO='';
					$IND_EXTORNO=0;
					$TXT_TIPO_REFERENCIA='';
					$TXT_REFERENCIA='';
					$COD_ESTADO=1;
					$COD_USUARIO_REGISTRO=Session::get('usuario_meta')->name;
					$COD_DOC_CTBLE_REF='';
					$COD_ORDEN_REF='';

			    	$array_nuevo_asiento 	=	array();
					$array_nuevo_asiento    =	array(
						"IND_TIPO_OPERACION" 		=> $IND_TIPO_OPERACION,
						"COD_EMPR" 					=> $COD_EMPR,
						"COD_CENTRO" 				=> $COD_CENTRO,
						"COD_CUENTA_CONTABLE" 		=> $COD_CUENTA_CONTABLE,
						"TXT_CUENTA_CONTABLE" 		=> $TXT_CUENTA_CONTABLE,
						"TXT_GLOSA" 				=> $TXT_GLOSA,

			            "CAN_DEBE_MN" 				=> $CAN_DEBE_MN,
			            "CAN_HABER_MN" 				=> $CAN_HABER_MN,
			            "CAN_DEBE_ME" 				=> $CAN_DEBE_ME,
			            "CAN_HABER_ME" 				=> $CAN_HABER_ME,

			            "NRO_LINEA" 				=> $NRO_LINEA,
			            "COD_CUO" 					=> $COD_CUO,
			            "IND_EXTORNO" 				=> $IND_EXTORNO,
			            "TXT_TIPO_REFERENCIA" 		=> $TXT_TIPO_REFERENCIA,
			            "TXT_REFERENCIA" 			=> $TXT_REFERENCIA,
			            "COD_ESTADO" 				=> $COD_ESTADO,
			            "COD_USUARIO_REGISTRO" 		=> $COD_USUARIO_REGISTRO,
			            "COD_DOC_CTBLE_REF" 		=> $COD_DOC_CTBLE_REF,
			            "COD_ORDEN_REF" 			=> $COD_ORDEN_REF,
					);


					array_push($array_detalle_asiento,$array_nuevo_asiento);
					$count = $count + 1;
				}
			}
		}




		return $array_detalle_asiento;

	}






	private function am_asiento_array($movimiento,$tipo_asiento,$anio,$pago_cobro,$empresa_rel_ter,$buscar_modelo_asiento,$nro_operacion,$nro_referencia,$glosa,$text_pago_cobro)
	{

		$array_asiento  		= 	array();

		if($buscar_modelo_asiento['encontro'] == '1'){

			$TOTAL = 0;
            if($nro_referencia == '42'){
            	if($movimiento->COD_CATEGORIA_MONEDA == 'MON0000000000001'){
            		$TOTAL = $movimiento->CAN_HABER_MN;
            	}else{
            		$TOTAL = $movimiento->CAN_HABER_ME;
            	}
            }else{
            	if($movimiento->COD_CATEGORIA_MONEDA == 'MON0000000000001'){
            		$TOTAL = $movimiento->CAN_DEBE_MN;
            	}else{
            		$TOTAL = $movimiento->CAN_DEBE_ME;
            	}
            }



			$periodo 			= 	CONPeriodo::where('COD_PERIODO','=',$movimiento->COD_PERIODO_CONCILIA)->first();
			$tipoasiento 		= 	CMPCategoria::where('COD_CATEGORIA','=',$tipo_asiento)->first();

			$IND_TIPO_OPERACION='I';
			$COD_EMPR=Session::get('empresas_meta')->COD_EMPR;
			$COD_CENTRO=$movimiento->COD_CENTRO;
			$COD_PERIODO=$movimiento->COD_PERIODO_CONCILIA;
			$TXT_PERIODO=$periodo->TXT_NOMBRE;
			$COD_CATEGORIA_TIPO_ASIENTO=$tipoasiento->COD_CATEGORIA;
			$TXT_CATEGORIA_TIPO_ASIENTO=$tipoasiento->NOM_CATEGORIA;
			$NRO_ASIENTO='';
			$FEC_ASIENTO=$movimiento->FEC_MOVIMIENTO_CAJABANCO;
			$TXT_GLOSA=$tipoasiento->NOM_CATEGORIA.' ('.$text_pago_cobro.' OP '.$nro_operacion.') : '.$glosa;
			$COD_CATEGORIA_ESTADO_ASIENTO='IACHTE0000000025';


			$TXT_CATEGORIA_ESTADO_ASIENTO='CONFIRMADO';
			$COD_CATEGORIA_MONEDA=$movimiento->COD_CATEGORIA_MONEDA;
			$TXT_CATEGORIA_MONEDA=$movimiento->TXT_CATEGORIA_MONEDA;
			$CAN_TIPO_CAMBIO=$movimiento->CAN_TIPO_CAMBIO;
			$CAN_TOTAL_DEBE=$TOTAL;
			$CAN_TOTAL_HABER=$TOTAL;
			$COD_ASIENTO_EXTORNO='';
			$COD_ASIENTO_EXTORNADO='';
			$IND_EXTORNO=0;
			$COD_ASIENTO_MODELO=$buscar_modelo_asiento['msg'];

			$TXT_TIPO_REFERENCIA='TES.OPERACION_CAJA';
			$TXT_REFERENCIA=$nro_operacion;
			$COD_ESTADO=1;
			$COD_USUARIO_REGISTRO=Session::get('usuario_meta')->name;
			$COD_MOTIVO_EXTORNO='';
			$GLOSA_EXTORNO='';
			$COD_EMPR_CLI='';
			$TXT_EMPR_CLI='';
			$COD_CATEGORIA_TIPO_DOCUMENTO='';
			$TXT_CATEGORIA_TIPO_DOCUMENTO='';

			$NRO_SERIE='';
			$NRO_DOC='';
			$FEC_DETRACCION='';
			$NRO_DETRACCION='';
			$CAN_DESCUENTO_DETRACCION=0;
			$CAN_TOTAL_DETRACCION=0;
			$COD_CATEGORIA_TIPO_DOCUMENTO_REF='';
			$TXT_CATEGORIA_TIPO_DOCUMENTO_REF='';
			$NRO_SERIE_REF='';
			$NRO_DOC_REF='';
			$FEC_VENCIMIENTO='';
			$IND_AFECTO='';
			$NRO_REFERENCIA=$nro_operacion;

			$array_asiento    				=	array(
				"IND_TIPO_OPERACION" 					=> $IND_TIPO_OPERACION,
				"COD_EMPR" 								=> $COD_EMPR,
				"COD_CENTRO" 							=> $COD_CENTRO,
				"COD_PERIODO" 							=> $COD_PERIODO,
				"TXT_PERIODO" 							=> $TXT_PERIODO,
				"COD_CATEGORIA_TIPO_ASIENTO" 			=> $COD_CATEGORIA_TIPO_ASIENTO,
				"TXT_CATEGORIA_TIPO_ASIENTO" 			=> $TXT_CATEGORIA_TIPO_ASIENTO,
	            "NRO_ASIENTO" 							=> $NRO_ASIENTO,
	            "FEC_ASIENTO" 							=> $FEC_ASIENTO,
	            "TXT_GLOSA" 							=> $TXT_GLOSA,
	            "COD_CATEGORIA_ESTADO_ASIENTO" 			=> $COD_CATEGORIA_ESTADO_ASIENTO,

	            "TXT_CATEGORIA_ESTADO_ASIENTO" 			=> $TXT_CATEGORIA_ESTADO_ASIENTO,
	            "COD_CATEGORIA_MONEDA" 					=> $COD_CATEGORIA_MONEDA,
	            "TXT_CATEGORIA_MONEDA" 					=> $TXT_CATEGORIA_MONEDA,
	            "CAN_TIPO_CAMBIO" 						=> $CAN_TIPO_CAMBIO,
	            "CAN_TOTAL_DEBE" 						=> $CAN_TOTAL_DEBE,
	            "CAN_TOTAL_HABER" 						=> $CAN_TOTAL_HABER,
	            "COD_ASIENTO_EXTORNO" 					=> $COD_ASIENTO_EXTORNO,
	            "COD_ASIENTO_EXTORNADO" 				=> $COD_ASIENTO_EXTORNADO,
	            "IND_EXTORNO" 							=> $IND_EXTORNO,
	            "COD_ASIENTO_MODELO" 					=> $COD_ASIENTO_MODELO,

	            "TXT_TIPO_REFERENCIA" 					=> $TXT_TIPO_REFERENCIA,
	            "TXT_REFERENCIA" 						=> $TXT_REFERENCIA,
	            "COD_ESTADO" 							=> $COD_ESTADO,
	            "COD_USUARIO_REGISTRO" 					=> $COD_USUARIO_REGISTRO,
	            "COD_MOTIVO_EXTORNO" 					=> $COD_MOTIVO_EXTORNO,
	            "GLOSA_EXTORNO" 						=> $GLOSA_EXTORNO,
	            "COD_EMPR_CLI" 							=> $COD_EMPR_CLI,
	            "TXT_EMPR_CLI" 							=> $TXT_EMPR_CLI,
	            "COD_CATEGORIA_TIPO_DOCUMENTO" 			=> $COD_CATEGORIA_TIPO_DOCUMENTO,
	            "TXT_CATEGORIA_TIPO_DOCUMENTO" 			=> $TXT_CATEGORIA_TIPO_DOCUMENTO,

	            "NRO_SERIE" 							=> $NRO_SERIE,
	            "NRO_DOC" 								=> $NRO_DOC,
	            "FEC_DETRACCION" 						=> $FEC_DETRACCION,
	            "NRO_DETRACCION" 						=> $NRO_DETRACCION,
	            "CAN_DESCUENTO_DETRACCION" 				=> $CAN_DESCUENTO_DETRACCION,
	            "CAN_TOTAL_DETRACCION" 					=> $CAN_TOTAL_DETRACCION,
	            "COD_CATEGORIA_TIPO_DOCUMENTO_REF" 		=> $COD_CATEGORIA_TIPO_DOCUMENTO_REF,
	            "TXT_CATEGORIA_TIPO_DOCUMENTO_REF" 		=> $TXT_CATEGORIA_TIPO_DOCUMENTO_REF,
	            "NRO_SERIE_REF" 						=> $NRO_SERIE_REF,
	            "NRO_DOC_REF" 							=> $NRO_DOC_REF,
	            "FEC_VENCIMIENTO" 						=> $FEC_VENCIMIENTO,
	            "IND_AFECTO" 							=> $IND_AFECTO,
	            "NRO_REFERENCIA" 						=> $NRO_REFERENCIA
			);


		}



		return $array_asiento;

	}


	private function am_buscar_asiento_modelo($movimiento,$tipo_asiento,$anio,$pago_cobro,$empresa_rel_ter)
	{

		$array_respuesta  		= 	array();
	    $listaasientomodelo 	= 	WEBAsientoModelo::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('anio','=',$anio)
	    							->tipoasiento($tipo_asiento)
	    							->where('pago_cobro_id','=',$pago_cobro)
	    							->where('moneda_id','=',$movimiento->COD_CATEGORIA_MONEDA)
	    							->where('tipo_cliente','=',$empresa_rel_ter)
			    					->first();

		if(count($listaasientomodelo)>0){
			$array_respuesta  		= 	array('encontro' => '1','msg' => $listaasientomodelo->id);
		}else{
			$array_respuesta  		= 	array('encontro' => '0','msg' => 'No existe asientos modelos que representen');
		}
			
		//	tiene asociada caja y banco
		$tescajabanco 		= 	TESCajaBanco::where('COD_CAJA_BANCO','=',$movimiento->COD_CAJA_BANCO)->first();

	    //dd($tescajabanco);

	    $cuentacontable 	= 	WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
								->where('anio','=',$anio)
								->where('id','=',$tescajabanco->TXT_TIPO_REFERENCIA)
								->where('activo','=',1)
								->first();

		if(count($cuentacontable)<=0){
			$array_respuesta  		= 	array('encontro' => '0','msg' => 'La Caja o banco no esta asociado');
		}


		return $array_respuesta;

	}


	private function am_empresa_relacionada($movimiento,$listadetallemovimientos)
	{


	    $count_rela 		= 		STDEmpresa::whereIn('COD_EMPR', $listadetallemovimientos->pluck('COD_EMPR_AFECTA')->toArray())
	    							->where('IND_RELACIONADO','=',1)
			    					->get();
		return count($count_rela);
	}


	private function am_empresa_tercero($counttotal,$countercero)
	{

		$count_ter 		= 		$counttotal - $countercero;
		return $count_ter;

	}

	private function am_empresa_relacionada_tercero($count_rela,$count_ter)
	{
		if($count_rela<=$count_ter){
			$empresa_rel_ter = 0;
		}else{
			$empresa_rel_ter = 1;
		}
		return $empresa_rel_ter;
		
	}


	private function am_empresa_existe_relacionada_tercero($count_rela,$count_ter)
	{
		$existe = 0;
		if($count_rela>0 and $count_ter>0){
			$existe = 1;
		}
		return $existe;
		
	}



	private function am_lista_asiento_modelo($empresa_id, $tipo_asiento_id,$anio)
	{


	    $listaasientomodelo 	= 	WEBAsientoModelo::where('empresa_id','=',$empresa_id)
	    							->where('anio','=',$anio)
	    							->tipoasiento($tipo_asiento_id)
									->orderBy('id', 'asc')
			    					->get();

		return $listaasientomodelo;

	}

	public function am_pertenece_debe_haber_rno_cuenta($asienti_modelo_detalle_id,$valor)
	{

		$nro_cuenta				= 	'';
	    $asientomodelodetalle 	= 	WEBAsientoModeloDetalle::where('id','=',$asienti_modelo_detalle_id)->first();
	    $cuentacontable 		= 	WEBCuentaContable::where('id','=',$asientomodelodetalle->cuenta_contable_id)->first();

	    //DEBE
	    if($asientomodelodetalle->partida_id == 'COP0000000000001' and $valor == 'DEBE'){
	    	$nro_cuenta			= 	$cuentacontable->nro_cuenta;
	    }
	    //HABER
	    if($asientomodelodetalle->partida_id == 'COP0000000000002' and $valor == 'HABER'){
	    	$nro_cuenta			= 	$cuentacontable->nro_cuenta;
	    }

		return $nro_cuenta;

	}

	private function am_array_asiento_modelo_referencia_xreferencia($referencia,$asiento_modelo_id) {
		
		$array 						= 	WEBAsientoModeloReferencia::where('activo','=',1)
        								->where('referencia','=',$referencia)
        								->where('asiento_modelo_id','=',$asiento_modelo_id)
		        						->pluck('documento_id')
										->toArray();
	 	return  $array;					 			
	}

	private function am_agregar_modificar_asiento_modelo_referencia($array,$referencia,$asiento_modelo_id,$fechaactual) {
		

			//limpiar todo en referencia
			WEBAsientoModeloReferencia::where('referencia','=', $referencia)
										->where('asiento_modelo_id','=', $asiento_modelo_id)
										->update([	'activo' 		=> 	0,
													'fecha_mod'		=> 	$fechaactual,
													'usuario_mod'	=>	Session::get('usuario_meta')->id
												]);

			foreach($array as $item=>$id)
			{

				$cabecera						=  	WEBAsientoModeloReferencia::where('referencia','=', 'TIPO_DOCUMENTO')
													->where('asiento_modelo_id','=', $asiento_modelo_id)
													->where('documento_id','=', $id)
													->first();
                if (count($cabecera)<=0) {

					$idasientomodeloreferencia 	=   $this->funciones->getCreateIdMaestra('web.asientomodeloreferencias');
					$cabecera            	 	=	new WEBAsientoModeloReferencia;
					$cabecera->id 	     	 	=   $idasientomodeloreferencia;
					$cabecera->asiento_modelo_id=   $asiento_modelo_id;
					$cabecera->documento_id 	=   $id;
					$cabecera->referencia 		=   'TIPO_DOCUMENTO';
					$cabecera->empresa_id 	 	=   Session::get('empresas_meta')->COD_EMPR;
					$cabecera->fecha_crea 	 	=   $fechaactual;
					$cabecera->usuario_crea 	=   Session::get('usuario_meta')->id;
					$cabecera->save();

                }else{

					$cabecera->activo 			=  1;
					$cabecera->save();	

                }
			}

			return 1;
	
	}


}