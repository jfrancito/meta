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
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\STDEmpresa;



use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;
use App\Traits\AsientoTraits;
use App\Traits\CajaBancoTraits;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

class AsientoController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use AsientoTraits;
	use PlanContableTraits;
	use CajaBancoTraits;


	public function actionAjaxComboDocumentoReferencia(Request $request)
	{


		$tipo_documento 		=   $request['tipo_documento'];


		if($tipo_documento=='TDO0000000000007' or $tipo_documento=='TDO0000000000008'){
			$combo_tipo_documento_re= 	$this->gn_generacion_combo_tabla_osiris_referencial('STD.TIPO_DOCUMENTO','COD_TIPO_DOCUMENTO','TXT_TIPO_DOCUMENTO','Seleccione tipo documento','');	
		}else{
			
			$combo_tipo_documento_re	= 	array('' => 'Seleccione tipo documento');
		}



		$funcion 				= 	$this;
		$defecto_tipo_documento = '';
		return View::make('general/combo/ctipodocumentoreferencial',
						 [

						 	'combo_tipo_documento_re'			=> $combo_tipo_documento_re,
						 	'defecto_tipo_documento'	 		=> $defecto_tipo_documento,					 	
						 	'ajax' 								=> true,						 	
						 ]);
	}




	public function actionGestionarPagoCobro($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Gestion Pago y Cobro');
		if($_POST)
		{

			$fechadocumento 	 		= 	$request['fechadocumento'];
			$moneda_id 	 				= 	$request['moneda_id'];
			$tipocambio 	 		 	= 	$request['tipocambio'];
			$cuenta_referencia 	 		= 	$request['cuenta_referencia'];
			$tipo_documento 	 		= 	$request['tipo_documento'];
			$serie 	 		 			= 	$request['serie'];
			$nrocomprobante 	 		= 	$request['nrocomprobante'];
			$cod_asiento 	 			= 	$request['cod_asiento'];

			$array_cp 					=   json_decode($request['array_cp'], true);

			$anio 	 					= 	$request['anio'];
			$mes 						=	date_format(date_create($fechadocumento), 'y');
			//dd($anio);

			$tipo_asiento_id 	 		= 	$request['tipo_asiento_id'];
			$cuenta_id 	 		 		= 	$request['cuenta_id'];
			$documento 	 				= 	$request['documento'];
			$glosa 	 					= 	$request['glosa'];

			$can_total_debe_a 			=	0;
			$can_total_haber_a 			=	0;

			$cod_emor_cli_a 			=	'';
			$txt_empr_cli_a 			=	'';



			if($cod_asiento==''){

				$asiento					=	WEBAsiento::whereIn('COD_ASIENTO',$array_cp)
												->get();
				foreach ($asiento as $key => $item) {
					$can_total_debe_a 		= 	$can_total_debe_a+$item->CAN_TOTAL_DEBE;
					$can_total_haber_a 		= 	$can_total_haber_a+$item->CAN_TOTAL_HABER;	
				}

			}else{
				$asiento					=	WEBAsiento::where('COD_ASIENTO','=',$cod_asiento)
												->first();
				$can_total_debe_a 			= 	$asiento->CAN_TOTAL_DEBE;
				$can_total_haber_a 			= 	$asiento->CAN_TOTAL_HABER;

				$cod_emor_cli_a 			=	$asiento->COD_EMPR_CLI;
				$txt_empr_cli_a 			=	$asiento->TXT_EMPR_CLI;


			}


	    	$detalle_asiento 			= 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$cod_asiento)
	    									->get();

	    	$mes 						=	date_format(date_create($fechadocumento), 'm');
	    	$periodo 					=   $this->gn_periodo_xanio_xmes($anio,$mes,Session::get('empresas_meta')->COD_EMPR);
	    	$tipo_asiento 				= 	CMPCategoria::where('COD_CATEGORIA','=',$tipo_asiento_id)->first();
	    	$moneda 					= 	CMPCategoria::where('COD_CATEGORIA','=',$moneda_id)->first();

	    	$cod_categoria_tipo_documento_a = '';
	    	$txt_categoria_tipo_documento_a = '';


			$tipodocumento 				= 	STDTipoDocumento::where('COD_TIPO_DOCUMENTO','=',$tipo_documento)->first();

			if(count($tipodocumento)>0){
				$cod_categoria_tipo_documento_a = $tipodocumento->COD_TIPO_DOCUMENTO;
				$txt_categoria_tipo_documento_a = $tipodocumento->TXT_TIPO_DOCUMENTO;
			}



			$IND_TIPO_OPERACION = 'I';
			$COD_ASIENTO = '';
			$COD_EMPR = Session::get('empresas_meta')->COD_EMPR;
			$COD_CENTRO = 'CEN0000000000001';
			$COD_PERIODO = $periodo->COD_PERIODO;
			$COD_CATEGORIA_TIPO_ASIENTO = $tipo_asiento->COD_CATEGORIA;
			$TXT_CATEGORIA_TIPO_ASIENTO = $tipo_asiento->NOM_CATEGORIA;
			$NRO_ASIENTO = '';
			$FEC_ASIENTO = $fechadocumento;

			$TXT_GLOSA = $tipo_asiento->NOM_CATEGORIA.' ('.$documento.') '.' : '.$glosa;
			$COD_CATEGORIA_ESTADO_ASIENTO = 'IACHTE0000000025';
			$TXT_CATEGORIA_ESTADO_ASIENTO = 'CONFIRMADO';
			$COD_CATEGORIA_MONEDA = $moneda->COD_CATEGORIA;
			$TXT_CATEGORIA_MONEDA = $moneda->NOM_CATEGORIA;
			$CAN_TIPO_CAMBIO = $tipocambio;

			$CAN_TOTAL_DEBE = $can_total_debe_a;
			$CAN_TOTAL_HABER = $can_total_haber_a;

			$COD_ASIENTO_EXTORNO = '';
			$COD_ASIENTO_EXTORNADO = '';
			$IND_EXTORNO = '0';
			$COD_ASIENTO_MODELO = '';
			$TXT_TIPO_REFERENCIA = 'WEB.asientos';
			$TXT_REFERENCIA = $cod_asiento;

			$COD_ESTADO = '1';
			$COD_USUARIO_REGISTRO = Session::get('usuario_meta')->id;
			$COD_MOTIVO_EXTORNO = '';
			$GLOSA_EXTORNO = '';

			$COD_EMPR_CLI = $cod_emor_cli_a;
			$TXT_EMPR_CLI = $txt_empr_cli_a;

			$COD_CATEGORIA_TIPO_DOCUMENTO = $cod_categoria_tipo_documento_a;
			$TXT_CATEGORIA_TIPO_DOCUMENTO = $txt_categoria_tipo_documento_a;

			$NRO_SERIE = $serie;
			$NRO_DOC = $nrocomprobante;


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


			if($cuenta_referencia=='42'){
		    	$lista_asiento 						= 	$this->as_lista_asiento_pago_cobro_array_compra($array_cp);
			}else{
				$lista_asiento 						= 	$this->as_lista_asiento_pago_cobro_array_venta($array_cp);
			}



	    	$montomn							=	0;
		    $montome							=	0;    	

		    $montome_md							=	0; 
		    $montome_mn							=	0; 

		    $i 									=   0;

			foreach ($lista_asiento as $key => $item) {

				$i 								=   $i + 1;

				$saldo = 0.0000;
				if($cuenta_referencia=='42'){

					$CAN_DEBE_MN = $item->CAN_DEBE_MN+$item->CAN_HABER_MN;
					$CAN_HABER_MN = 0.0000;
					$CAN_DEBE_ME = $item->CAN_DEBE_ME+$item->CAN_HABER_ME;
					$CAN_HABER_ME = 0.0000;

					if($moneda_id == 'MON0000000000001'){
						$saldo 				= 	$CAN_DEBE_MN;
						$montome_md			=	$montome_md + $CAN_DEBE_MN;
					}else{
						$saldo 				= 	$CAN_DEBE_ME;
						$montome_md			=	$montome_md + ($CAN_DEBE_ME*$tipocambio);


					}

			    	$montomn							=	$montomn+$CAN_DEBE_MN;
				    $montome							=	$montome+$CAN_DEBE_ME; 
		    		$montome_mn							=	$montome_mn+$CAN_DEBE_MN;

				}else{

					$CAN_DEBE_MN = 0.0000;
					$CAN_HABER_MN = $item->CAN_DEBE_MN+$item->CAN_HABER_MN;
					$CAN_DEBE_ME = 0.0000;
					$CAN_HABER_ME = $item->CAN_DEBE_ME+$item->CAN_HABER_ME;

					if($moneda_id == 'MON0000000000001'){
						$saldo 				= 	$CAN_HABER_MN;
						$montome_md			=	$montome_md + $CAN_HABER_MN;
					}else{
						$saldo 				= 	$CAN_HABER_ME;
						$montome_md			=	$montome_md + ($CAN_HABER_ME*$tipocambio);
					}

		    		$montomn							=	$montomn+$CAN_HABER_MN;
				    $montome							=	$montome+$CAN_HABER_ME; 
		    		$montome_mn							=	$montome_mn+$CAN_HABER_MN;


				}





				$IND_TIPO_OPERACION = 'I';
				$COD_ASIENTO_MOVIMIENTO = '';
				$COD_EMPR = Session::get('empresas_meta')->COD_EMPR;
				$COD_CENTRO = 'CEN0000000000001';
				$COD_ASIENTO = $asientocontable;

				$COD_CUENTA_CONTABLE = $item->id;
				$TXT_CUENTA_CONTABLE = $item->nro_cuenta;

				$TXT_GLOSA = 'ABONO DOC : '. $item->TXT_CATEGORIA_TIPO_DOCUMENTO.' '.$item->NRO_SERIE.' '.$item->NRO_DOC.' // '.$item->TXT_EMPR_CLI;
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


	    		$frmasiento 							= 	WEBAsiento::where('COD_ASIENTO','=',$item->COD_ASIENTO)->first();

	    		$frmasiento->COD_ASIENTO_PAGO_COBRO 	=   $COD_ASIENTO;
	    		$frmasiento->SALDO 						=   $saldo;
				$frmasiento->FEC_USUARIO_MODIF_AUD 	 	=   $this->fechaactual;
				$frmasiento->COD_USUARIO_MODIF_AUD 		=   Session::get('usuario_meta')->id;
				$frmasiento->save();



			}


		    $cuentacontable 	= 	WEBCuentaContable::where('id','=',$cuenta_id)
									->first();


			if($cuenta_referencia=='42'){

				$CAN_DEBE_MN = 0.0000;
				$CAN_HABER_MN = $montomn;
				$CAN_DEBE_ME = 0.0000;
				$CAN_HABER_ME = $montome;

				if($moneda_id == 'MON0000000000002'){
					$CAN_DEBE_MN = 0.0000;
					$CAN_HABER_MN = $montome_md;
					$CAN_DEBE_ME = 0.0000;
					$CAN_HABER_ME = $montome;
				}

			}else{

				$CAN_DEBE_MN = $montomn;
				$CAN_HABER_MN = 0.0000;
				$CAN_DEBE_ME = $montome;
				$CAN_HABER_ME = 0.0000;

				if($moneda_id == 'MON0000000000002'){
					$CAN_DEBE_MN = $montome_md;
					$CAN_HABER_MN = 0.0000;
					$CAN_DEBE_ME = $montome;
					$CAN_HABER_ME = 0.0000;
				}


			}



			$IND_TIPO_OPERACION = 'I';
			$COD_ASIENTO_MOVIMIENTO = '';
			$COD_EMPR = Session::get('empresas_meta')->COD_EMPR;
			$COD_CENTRO = 'CEN0000000000001';
			$COD_ASIENTO = $asientocontable;

			$COD_CUENTA_CONTABLE = $cuentacontable->id;
			$TXT_CUENTA_CONTABLE = $cuentacontable->nro_cuenta;
			$TXT_GLOSA = 'CANCELADO : '. $glosa;
			$NRO_LINEA = $i+1;
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


    		//perdida o  ganancia $montome_mn

    		if($montome_mn <> $montome_md){


    			if($montome_mn>$montome_md){

				    $cuentacontable 	= 	WEBCuentaContable::where('nro_cuenta','=','776101')
				    						->where('anio','=',$anio)
				    						->where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
											->first();

					if($cuenta_referencia=='42'){



						$CAN_DEBE_MN = 0.0000;
						$CAN_HABER_MN = $montome_mn - $montome_md;
						$CAN_DEBE_ME = 0.0000;
						$CAN_HABER_ME = 0.0000;

					}else{

						$CAN_DEBE_MN = $montome_mn - $montome_md;
						$CAN_HABER_MN = 0.0000;
						$CAN_DEBE_ME = 0.0000;
						$CAN_HABER_ME = 0.0000;

					}


    			}else{

				    $cuentacontable 	= 	WEBCuentaContable::where('nro_cuenta','=','676101')
				    						->where('anio','=',$anio)
				    						->where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
											->first();

					if($cuenta_referencia=='42'){

						$CAN_DEBE_MN = 0.0000;
						$CAN_HABER_MN = $montome_md - $montome_mn;
						$CAN_DEBE_ME = 0.0000;
						$CAN_HABER_ME = 0.0000;

					}else{

						$CAN_DEBE_MN = $montome_md - $montome_mn;
						$CAN_HABER_MN = 0.0000;
						$CAN_DEBE_ME = 0.0000;
						$CAN_HABER_ME = 0.0000;

					}

    			}


				$IND_TIPO_OPERACION = 'I';
				$COD_ASIENTO_MOVIMIENTO = '';
				$COD_EMPR = Session::get('empresas_meta')->COD_EMPR;
				$COD_CENTRO = 'CEN0000000000001';
				$COD_ASIENTO = $asientocontable;

				$COD_CUENTA_CONTABLE = $cuentacontable->id;
				$TXT_CUENTA_CONTABLE = $cuentacontable->nro_cuenta;
				$TXT_GLOSA = $cuentacontable->nombre;
				$NRO_LINEA = $i+2;
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



			return Redirect::to('/gestion-pago-cobro/'.$idopcion)->with('bienhecho', 'Asiento '.$glosa.' registrado con exito');


		}else{


			if(Session::has('asiento_id_session')){
				$ocultarbuscar          =   'ocultar';
				$ocultarasignar         =   '';
				$asiento_id 			=	Session::get('asiento_id_session');
			}else{
				$asiento_id 			=	'';

				$ocultarbuscar          =   '';
				$ocultarasignar         =   'ocultar';

			}

			$asiento				=	WEBAsiento::where('COD_ASIENTO','=',$asiento_id)
										->first();	
	    	$detalle_asiento 		= 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento_id)
	    								->get();

	    	$combo_cuenta_referencia = $this->cb_combo_cuenta_referencia();
			$combo_moneda 			= 	$this->gn_generacion_combo_categoria('MONEDA','Seleccione moneda','');
			$combo_tipo_documento	= 	$this->gn_generacion_combo_tabla_osiris('STD.TIPO_DOCUMENTO','COD_TIPO_DOCUMENTO','TXT_TIPO_DOCUMENTO','Seleccione tipo documento','');

	    	$combo_tipo_asiento 	= 	$this->gn_generacion_combo_pago_cobro('TIPO_ASIENTO','Seleccione tipo asiento','');
		
	    	if(count($asiento)>0){
				$defecto_moneda			= 	$asiento->COD_CATEGORIA_MONEDA;
				$tipo_cambio 			=	$this->gn_tipo_cambio(date_format(date_create($asiento->FEC_ASIENTO), 'd-m-Y'));
				$fecha 					=	date_format(date_create($asiento->FEC_ASIENTO), 'd-m-Y');
				$defecto_tipo_documento	= 	$asiento->COD_CATEGORIA_TIPO_DOCUMENTO;

				$serie 					=	$asiento->NRO_SERIE;
				$nrocomprobante 		=	$asiento->NRO_DOC;

	    	}else{
				$defecto_moneda			= 	'';
				$tipo_cambio 			=	$this->gn_tipo_cambio($this->fin);
				$fecha 					=	$this->fin;
				$defecto_tipo_documento	= 	'-1';
				$serie 					=	'';
				$nrocomprobante 		=	'';

	    	}
	    	$sel_tipo_asiento 			=	'';
	    	$lista_asiento              =  	array();
	    	$combo_cuenta_corriente 	= 	array(''=>'Seleccione cuenta corriente');
	    	$sel_cuenta_corriente 		=	'';

		    $anio  					=   $this->anio;
	        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
			$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
			$array_cp 				=  	array();




			return View::make('asiento/agregarpagocobro',
						[
							'combo_tipo_documento'  	=> $combo_tipo_documento,
							'combo_cuenta_referencia'  	=> $combo_cuenta_referencia,
							'combo_moneda'  			=> $combo_moneda,
							'combo_tipo_asiento'  		=> $combo_tipo_asiento,
							'combo_cuenta_corriente'  	=> $combo_cuenta_corriente,
							'sel_cuenta_corriente'  	=> $sel_cuenta_corriente,
							'defecto_moneda'  			=> $defecto_moneda,
							'defecto_tipo_documento'  	=> $defecto_tipo_documento,
							'sel_tipo_asiento'  		=> $sel_tipo_asiento,
							'tipo_cambio'  				=> $tipo_cambio,
							'serie'  					=> $serie,
							'nrocomprobante'  			=> $nrocomprobante,
							'fecha'						=> $fecha,
							'asiento_id'				=> $asiento_id,
							'lista_asiento'				=> $lista_asiento,
							'array_cp'					=> $array_cp,
							'anio'						=> $anio,
							'combo_anio_pc'				=> $combo_anio_pc,
						  	'idopcion'  				=> $idopcion,
							'ocultarbuscar'				=> $ocultarbuscar,
							'ocultarasignar'			=> $ocultarasignar,

						]);
		}
	}

	public function actionAjaxComboCuentaPagoCobro(Request $request)
	{


		$tipo_asiento_id 	    	=   $request['tipo_asiento_id'];
		$anio 	    				=   $request['anio'];


    	$sel_cuenta_corriente 		=	'';
    	if($tipo_asiento_id=='TAS0000000000007'){
    		$array_cuenta 	    	= 	$this->pc_array_nro_cuentas_nombre_xnivel(Session::get('empresas_meta')->COD_EMPR,6,$anio);
			$combo_cuenta_corriente = 	$this->gn_generacion_combo_array('Seleccione cuenta contable', '' , $array_cuenta);
    	}else{

	    	if($tipo_asiento_id=='TAS0000000000001'){
	    		$array_cuenta 	    	= 	$this->pc_array_nro_cuentas_nombre_xnivel_caja(Session::get('empresas_meta')->COD_EMPR,6,$anio);
				$combo_cuenta_corriente = 	$this->gn_generacion_combo_array('Seleccione cuenta contable', '' , $array_cuenta);
	    	}else{


		    	if($tipo_asiento_id=='TAS0000000000002'){
		    		$array_cuenta 	    	= 	$this->pc_array_nro_cuentas_nombre_xnivel_banco(Session::get('empresas_meta')->COD_EMPR,6,$anio);
					$combo_cuenta_corriente = 	$this->gn_generacion_combo_array('Seleccione cuenta contable', '' , $array_cuenta);
		    	}else{


		    		$combo_cuenta_corriente 	= 	array(''=>'Seleccione cuenta corriente');

	    		}

    		}
    	}


		return View::make('asiento/ajax/acuentapagocobro',
						 [

						 	'combo_cuenta_corriente'=> $combo_cuenta_corriente,
						 	'sel_cuenta_corriente'	 => $sel_cuenta_corriente,					 	
						 	'ajax' 					=> true,						 	
						 ]);
	}


	public function actionAjaxBuscarAsientoPagoCobro(Request $request)
	{

		$tipo_documento 					= 	$request['tipo_documento'];
		$serie 								= 	$request['serie'];
		$asiento_id 						= 	$request['asiento_id'];
		$nrocomprobante 					= 	$request['nrocomprobante'];
		$array_cp 							=   json_decode($request['array_cp'], true);
		$arrayasiento 						=	array($asiento_id);

	    $lista_asiento 						= 	$this->as_lista_asiento_pago_cobro($asiento_id);

		return View::make('asiento/ajax/aasientopagocobro',
					[
						'lista_asiento'  	=> $lista_asiento,
						'array_cp'  		=> $arrayasiento,
					]);
	}

	public function actionAjaxBuscarAsientoPagoCobroClienteProveedor(Request $request)
	{

		$data_archivo 						=   json_decode($request['datastring'], true);
		$cuenta_referencia 					=   $request['cuenta_referencia'];
		$array_cp 							=   json_decode($request['array_cp'], true);



		$arrayasiento 						=	array();


		foreach($data_archivo as $key => $obj){
			$cod_asiento 		=   $obj['cod_asiento'];
			array_push($arrayasiento, $cod_asiento);
		}

		foreach($array_cp as $key => $obj){
			$cod_asiento 		=   $obj;

			array_push($arrayasiento, $cod_asiento);
		}


		if($cuenta_referencia=='42'){
	    	$lista_asiento 						= 	$this->as_lista_asiento_pago_cobro_array_compra($arrayasiento);
		}else{
			$lista_asiento 						= 	$this->as_lista_asiento_pago_cobro_array_venta($arrayasiento);
		}

		//dd($lista_asiento);

		return View::make('asiento/ajax/aasientopagocobro',
					[
						'lista_asiento'  	=> $lista_asiento,
						'array_cp'  		=> $arrayasiento,
					]);
	}



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


				$nrocomprobante = '';
				if($request['nrocomprobante'] !=''){
					$nrocomprobante = str_pad($request['nrocomprobante'], 7, "0", STR_PAD_LEFT);
				}
				$nrocomprobantereferencia = '';
				if($request['nrocomprobantereferencia'] !=''){
					$nrocomprobantereferencia = str_pad($request['nrocomprobantereferencia'], 7, "0", STR_PAD_LEFT);
				}

				$anio 	 		 			= 	$request['anio'];
				$periodo_id 	 		 	= 	$request['periodo_id'];
				$tipo_asiento_id 	 		= 	$request['tipo_asiento_id'];
				$moneda_id 	 		 		= 	$request['moneda_id'];
				$tipocambio 	 		 	= 	$request['tipocambio'];
				$pagocobro 	 		 		= 	$request['pagocobro'];


				$tipo_documento 	 		= 	$request['tipo_documento'];
				$serie 	 		 			= 	$request['serie'];

				$nrocomprobante 	 		= 	$nrocomprobante;


				$fechavencimiento 	 		= 	$request['fechavencimiento'];
				$fechadocumento 	 		= 	$request['fechadocumento'];
				$cliente_id 	 			= 	$request['cliente_id'];


				$tipo_documento_referencia 	= 	$request['tipo_documento_referencia'];
				$seriereferencia 	 		= 	$request['seriereferencia'];

				$nrocomprobantereferencia 	= 	$nrocomprobantereferencia;

				$fechareferencia 	 		= 	$request['fechareferencia'];
				$glosa 	 		 			= 	$request['glosa'];
				$empresa_id 				=	Session::get('empresas_meta')->COD_EMPR;
				$centro_id 					=	'CEN0000000000001';
				$periodo 					= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
				$tipo_asiento 				= 	CMPCategoria::where('COD_CATEGORIA','=',$tipo_asiento_id)->first();
				$moneda 					= 	CMPCategoria::where('COD_CATEGORIA','=',$moneda_id)->first();
				$tipodocumento 				= 	STDTipoDocumento::where('COD_TIPO_DOCUMENTO','=',$tipo_documento)->first();
				$tipodocumentoreferencia 	= 	STDTipoDocumento::where('COD_TIPO_DOCUMENTO','=',$tipo_documento_referencia)->first();
				$cliente 					= 	STDEmpresa::where('COD_EMPR','=',$cliente_id)->first();


				$cod_cliente 				=	'';
				$nom_cliente 				=	'';
				if(count($cliente)>0){
					$cod_cliente 				=	$cliente->COD_EMPR;
					$nom_cliente 				=	$cliente->NOM_EMPR;
				}else{
					$cod_cliente 				=	'';
					$nom_cliente 				=	'';
				}


				if(count($tipodocumento)>0){
					$TXT_CATEGORIA_TIPO_DOCUMENTO = $tipodocumento->TXT_TIPO_DOCUMENTO;
				}else{
					$TXT_CATEGORIA_TIPO_DOCUMENTO = '';
				}
				$glosa  =	$tipo_asiento->NOM_CATEGORIA.' : '.trim($TXT_CATEGORIA_TIPO_DOCUMENTO).' '.$serie.' '.$nrocomprobante.' // '.$nom_cliente.' // '.$glosa;


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
				$TXT_GLOSA = $glosa;
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
				$COD_EMPR_CLI = $cod_cliente;
				$TXT_EMPR_CLI = $nom_cliente;

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
					$CAN_DEBE_ME = $item['montod']/$tipocambio;
					$CAN_HABER_ME = $item['montoh']/$tipocambio;
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



	        $stmt 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.APLICAR_ASIENTO_DESTINO 
												@COD_ASIENTO = ?,
												@anio = ?');

	        $stmt->bindParam(1, $COD_ASIENTO ,PDO::PARAM_STR);                   
	        $stmt->bindParam(2, $anio ,PDO::PARAM_STR);
	        $stmt->execute();

	        if($pagocobro=='1'){

	        	Session::flash('asiento_id_session', $COD_ASIENTO);
	        	return Redirect::to('/gestion-pago-cobro/'.$idopcion)->with('bienhecho', 'Asiento '.$glosa.' registrado con exito');

	        }else{
	        	return Redirect::to('/gestion-asiento/'.$idopcion)->with('bienhecho', 'Asiento '.$glosa.' registrado con exito');
	        }


		}else{

			$combo_tipo_asiento 	= 	$this->gn_generacion_combo_categoria('TIPO_ASIENTO','Seleccione tipo asiento','');
			$combo_moneda 			= 	$this->gn_generacion_combo_categoria('MONEDA','Seleccione moneda','');
			$combo_tipo_documento	= 	$this->gn_generacion_combo_tabla_osiris('STD.TIPO_DOCUMENTO','COD_TIPO_DOCUMENTO','TXT_TIPO_DOCUMENTO','Seleccione tipo documento','');

			$combo_tipo_documento_re	= 	array('' => 'Seleccione tipo documento');


		    $sel_periodo 			=	'';
		    // $anio  					=   $this->anio;
		    $anio  					=   '2022';
	        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
			$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    	$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');

	    	$combo_empresa 			= 	$this->gn_combo_empresa('Seleccione periodo','');

			$defecto_tipo_asiento 	= 	'';
			$defecto_moneda			= 	'';
			$defecto_empresa		= 	'';			

			$defecto_tipo_documento	= 	'-1';

			$tipo_cambio 			=	$this->gn_tipo_cambio($this->fin);
			$array_detalle_asiento  = 	array();

			return View::make('asiento/agregarasiento',
						[
							'combo_tipo_asiento'  		=> $combo_tipo_asiento,
							'combo_moneda'  			=> $combo_moneda,
							'combo_tipo_documento'  	=> $combo_tipo_documento,
							'combo_tipo_documento_re'  	=> $combo_tipo_documento_re,
							'combo_empresa'  			=> $combo_empresa,

							'sel_periodo'  				=> $sel_periodo,
							'anio'  					=> $anio,
							'combo_anio_pc'  			=> $combo_anio_pc,
							'combo_periodo'  			=> $combo_periodo,
							'tipo_cambio'  				=> $tipo_cambio,
							'fecha'						=> $this->fin,
							'defecto_tipo_documento'  	=> $defecto_tipo_documento,
							'defecto_tipo_asiento'  	=> $defecto_tipo_asiento,
							'defecto_moneda'  			=> $defecto_moneda,
							'defecto_empresa'  			=> $defecto_empresa,
							'array_detalle_asiento'  	=> $array_detalle_asiento,
						  	'idopcion'  				=> $idopcion
						]);
		}
	}


	public function actionAjaxModalProveedorCliente(Request $request)
	{
		
		$funcion 				= 	$this;
		$cuenta_referencia  	=   $request['cuenta_referencia'];
		$tipo_documento  		=   $request['tipo_documento'];
		$serie  				=   $request['serie'];
		$nrocomprobante  		=   $request['nrocomprobante'];
		$nombreruc  			=   $request['nombreruc'];

		if($cuenta_referencia=='12'){//venta

			$listaasiento 		= 	WEBAsiento::where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
			    							->where('COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000003')
			    							->where('COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
			    							->where(function ($query) {
											    $query->where('saldo', '<=', 0)
											          ->orwhereNull('saldo');
											})
			    							->TipoDocumento($tipo_documento)
			    							->NroSerie($serie)
			    							->NroDocumento($nrocomprobante)
			    							->RazonSocial($nombreruc)
			    							->orderby('WEB.asientos.FEC_ASIENTO','asc')
			    							->get();

		}else{

			$listaasiento 		= 	WEBAsiento::where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
			    							->where('COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000004')
			    							->where('COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
			    							->where(function ($query) {
											    $query->where('saldo', '<=', 0)
											          ->orwhereNull('saldo');
											})
			    							->TipoDocumento($tipo_documento)
			    							->NroSerie($serie)
			    							->NroDocumento($nrocomprobante)
			    							->RazonSocial($nombreruc)
			    							->orderby('WEB.asientos.FEC_ASIENTO','asc')
			    							->get();

		}

		return View::make('asiento/modal/ajax/mlistaproveedorcliente',
						 [		 	

						 	'cuenta_referencia' 	=> $cuenta_referencia,
						 	'listaasiento' 			=> $listaasiento,
						 	'funcion' 				=> $funcion,
						 	'ajax' 					=> true,						 	
						 ]);
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


	public function actionAjaxModalConfirmacionGuardar(Request $request)
	{
		
		$funcion 				= 	$this;


		return View::make('asiento/modal/ajax/mconfirmacionguardar',
						 [		 	
						 	'funcion' 				=> $funcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}




}
