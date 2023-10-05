<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use App\Modelos\CONPeriodo;
use App\Modelos\CMPDetalleProducto;
use App\Modelos\STDEmpresa;
use App\Modelos\WEBProductoEmpresa;
use App\Modelos\WEBCuentaDetraccion;
use App\Modelos\CMPTipoCambio;
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\TESCajaBanco;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;



trait GeneralesTraits
{

	private function indicadores_asientos_contables($empresa_id, $tiponotificacion)
	{
        $stmt 		= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.notificacion_asientos 
							@empresa_id = ?,
							@tiponotificacion = ?');

        $stmt->bindParam(1, $empresa_id ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $tiponotificacion  ,PDO::PARAM_STR);
        $stmt->execute();
		return $stmt;
	}

	private function gn_array_bancos($empresa_id)
	{
	    $array_banco_pc 		= 	TESCajaBanco::where('COD_EMPR','=',$empresa_id)
									->where('COD_ESTADO','=',1)
									->where('COD_BANCO','!=','')
									->groupBy('COD_BANCO','TXT_BANCO')
									->pluck('TXT_BANCO','COD_BANCO')
									->toArray();

		return $array_banco_pc;

	}

	public function gr_is_connected($url='www.google.com',$port=80)
	{
		$connected = @fsockopen($url, $port); 
		//website, port  (try 80 or 443)
		if ($connected){
			$is_conn = true; //action when connected
			fclose($connected);
		}else{
			$is_conn = false; //action in connection failure
		}
		return $is_conn;
	}



	public function gn_generar_total_asientos($COD_ASIENTO)
	{

		$asiento 									=	WEBAsiento::where('COD_ASIENTO','=',$COD_ASIENTO)->first();


		$total_debe 								=	0;
		$total_haber 								=	0;
		//actualizar totales
		$listaasientomovimiento 					=	WEBAsientoMovimiento::where('COD_ASIENTO','=',$COD_ASIENTO)
														->where('COD_ESTADO','=','1')
														->where('IND_PRODUCTO','<>','2')
														->orderBy('NRO_LINEA','ASC')
														->get();

		foreach($listaasientomovimiento as $key => $item){

			//if($asiento->COD_CATEGORIA_MONEDA=='MON0000000000001'){//soles
				$total_debe 	=	$total_debe + $item->CAN_DEBE_MN;
				$total_haber 	=	$total_haber + $item->CAN_HABER_MN;
			//}else{//DOLARES
				// $total_debe 	=	$total_debe + $item->CAN_DEBE_ME;
				// $total_haber 	=	$total_haber + $item->CAN_HABER_ME;
			//}

		}

		//ACTUALIZAR TOTAL
		$asiento->CAN_TOTAL_DEBE 			= 	$total_debe;				
		$asiento->CAN_TOTAL_HABER 			= 	$total_haber;
		$asiento->FEC_USUARIO_MODIF_AUD 	=   $this->fechaactual;
		$asiento->COD_USUARIO_MODIF_AUD 	=   Session::get('usuario_meta')->name;
		$asiento->save();


    }





	public function gn_crear_asiento_destino($cod_asiento,$cod_asientomovimiento,$anio)
	{

		$empresa 				=   Session::get('empresas_meta')->COD_EMPR;
		$asiento 				= 	WEBAsiento::where('COD_ASIENTO','=',$cod_asiento)->first();
		$listaasientomovimiento 	= 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$cod_asiento)
										->where('COD_ASIENTO_MOVIMIENTO','=',$cod_asientomovimiento)
										->get();


		foreach($listaasientomovimiento as $key => $item){

			$ind_alimentacion = 0;

			$asientomovimiento 		= 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$cod_asiento)
										->where('COD_ESTADO','=',1)
										->get();

			$cont_detalle_asiento 	=	count($asientomovimiento)+1;						
			$COD_CUENTA_CONTABLE 	= 	$item->COD_CUENTA_CONTABLE;
			$nro_cuenta_alimentacion =  $item->TXT_CUENTA_CONTABLE;

			if($nro_cuenta_alimentacion=='631401' and Ltrim(Rtrim($nro_cuenta_alimentacion)) == '91'){
				$ind_alimentacion = 1;
			}

			/////////////////////////////////////////DEBE 01///////////////////////////////////////////
			$dcc 					=	WEBCuentaContable::where('id','=',$COD_CUENTA_CONTABLE)
										->where('cuenta_contable_transferencia_debe','<>','')->get();

			if(count($dcc)>0 and $ind_alimentacion <=0){

				$cctd = WEBCuentaContable::where('id','=',$COD_CUENTA_CONTABLE)->first();
				$nro_cuenta_rel =	Ltrim(Rtrim($cctd->cuenta_contable_transferencia_debe));

				$cc = WEBCuentaContable::where('nro_cuenta','=',$nro_cuenta_rel)->where('empresa_id','=',$empresa)->where('anio','=',$anio)->first();				
				$COD_CUENTA_CONTABLE 	= 	$cc->id;
				$TXT_GLOSA_PRODUCTO 	= 	$cc->nombre;
				$cctn = WEBCuentaContable::where('id','=',$COD_CUENTA_CONTABLE)->first();
				$porcentaje_debe	= 	$cctn->transferencia_debe_porcentaje/100;
				$CAN_DEBE_MN = 0.000;
				$CAN_HABER_MN  = 0.000;
				$CAN_DEBE_ME  = 0.000;
				$CAN_HABER_ME  = 0.000;
				$CAN_VALOR  = 0.000;

				if($asiento->COD_CATEGORIA_MONEDA == 'MON0000000000001'){
					$CAN_VALOR =  $item->CAN_DEBE_MN+$item->CAN_HABER_MN;
				}
				else{
					$CAN_VALOR =  $item->CAN_DEBE_ME+$item->CAN_HABER_ME;
				}


				$contadorpro = $cont_detalle_asiento;
				$partida_id = 'COP0000000000001';

				if($asiento->COD_CATEGORIA_TIPO_DOCUMENTO=='TDO0000000000007'){
					$partida_id = 'COP0000000000002';			
				}




				if($partida_id == 'COP0000000000001')
					{
						if($asiento->COD_CATEGORIA_MONEDA == 'MON0000000000001')
							{
								$CAN_DEBE_MN = $CAN_VALOR;
								$CAN_DEBE_ME = $CAN_VALOR/$asiento->CAN_TIPO_CAMBIO;
							}
						else
							{
								$CAN_DEBE_MN = $CAN_VALOR*$asiento->CAN_TIPO_CAMBIO;
								$CAN_DEBE_ME = $CAN_VALOR;
							}
					}
				else
					{
						if($asiento->COD_CATEGORIA_MONEDA == 'MON0000000000001')
							{
								$CAN_HABER_MN = $CAN_VALOR;
								$CAN_HABER_ME = $CAN_VALOR/$asiento->CAN_TIPO_CAMBIO;
							}
						else
							{
								$CAN_HABER_MN = $CAN_VALOR*$asiento->CAN_TIPO_CAMBIO;
								$CAN_HABER_ME = $CAN_VALOR;
							}
					}


					$CAN_DEBE_MN = $CAN_DEBE_MN*$porcentaje_debe;
					$CAN_HABER_MN = $CAN_HABER_MN*$porcentaje_debe;
					$CAN_DEBE_ME = $CAN_DEBE_ME*$porcentaje_debe;
					$CAN_HABER_ME = $CAN_HABER_ME*$porcentaje_debe;

					$IND_TIPO_OPERACION = 'I';
					$COD_ASIENTO_MOVIMIENTO = '';
					$COD_EMPR = $asiento->COD_EMPR;
					$COD_CENTRO = $asiento->COD_CENTRO;
					$COD_ASIENTO = $asiento->COD_ASIENTO;

					$COD_CUO = '';
					$IND_EXTORNO = '0';
					$TXT_TIPO_REFERENCIA = 'WEB.asientomovimientos';
					$TXT_REFERENCIA = $cod_asientomovimiento;
					$COD_ESTADO = '1';
					$COD_USUARIO_REGISTRO = Session::get('usuario_meta')->nombre;
					$COD_DOC_CTBLE_REF = '';
					$COD_ORDEN_REF = '';

		    		$detalle     	= 	$this->gn_crear_detalle_asiento_contable($IND_TIPO_OPERACION,
											$COD_ASIENTO_MOVIMIENTO,
											$COD_EMPR,
											$COD_CENTRO,
											$COD_ASIENTO,
											$COD_CUENTA_CONTABLE,
											$nro_cuenta_rel,
											$TXT_GLOSA_PRODUCTO,
											$CAN_DEBE_MN,
											$CAN_HABER_MN,

											$CAN_DEBE_ME,
											$CAN_HABER_ME,

											$contadorpro,

											$COD_CUO,
											$IND_EXTORNO,
											$TXT_TIPO_REFERENCIA,
											$TXT_REFERENCIA,
											$COD_ESTADO,
											$COD_USUARIO_REGISTRO,
											$COD_DOC_CTBLE_REF,
											$COD_ORDEN_REF);



					$asmo 			= 		WEBAsientoMovimiento::where('COD_ASIENTO_MOVIMIENTO','=',$detalle)->first();
					$asmo->IND_PRODUCTO = 2;
					$asmo->save();


					$asientomovimiento 		= 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$cod_asiento)
												->where('COD_ESTADO','=',1)
												->get();

			}

			/////////////////////////////////////////DEBE 02/////////////////////////////////////////// 

			$asientomovimiento 		= 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$cod_asiento)
										->where('COD_ESTADO','=',1)
										->get();
			$cont_detalle_asiento 	=	count($asientomovimiento)+1;	
			$COD_CUENTA_CONTABLE 	= 	$item->COD_CUENTA_CONTABLE;
			$dcc 					=	WEBCuentaContable::where('id','=',$COD_CUENTA_CONTABLE)
										->where('cuenta_contable_transferencia_debe02','<>','')->get();


			if(count($dcc)>0 and $ind_alimentacion <=0){

				$cctd = WEBCuentaContable::where('id','=',$COD_CUENTA_CONTABLE)->first();
				$nro_cuenta_rel =	Ltrim(Rtrim($cctd->cuenta_contable_transferencia_debe02));

				$cc = WEBCuentaContable::where('nro_cuenta','=',$nro_cuenta_rel)->where('empresa_id','=',$empresa)->where('anio','=',$anio)->first();				
				$COD_CUENTA_CONTABLE 	= 	$cc->id;
				$TXT_GLOSA_PRODUCTO 	= 	$cc->nombre;
				$cctn = WEBCuentaContable::where('id','=',$COD_CUENTA_CONTABLE)->first();
				$porcentaje_debe	= 	$cctn->transferencia_debe02_porcentaje/100;
				$CAN_DEBE_MN = 0.000;
				$CAN_HABER_MN  = 0.000;
				$CAN_DEBE_ME  = 0.000;
				$CAN_HABER_ME  = 0.000;
				$CAN_VALOR  = 0.000;

				if($asiento->COD_CATEGORIA_MONEDA == 'MON0000000000001'){
					$CAN_VALOR =  $item->CAN_DEBE_MN+$item->CAN_HABER_MN;
				}
				else{
					$CAN_VALOR =  $item->CAN_DEBE_ME+$item->CAN_HABER_ME;
				}


				$contadorpro = $cont_detalle_asiento;
				$partida_id = 'COP0000000000001';

				if($asiento->COD_CATEGORIA_TIPO_DOCUMENTO=='TDO0000000000007'){
					$partida_id = 'COP0000000000002';			
				}

				if($partida_id == 'COP0000000000001')
					{
						if($asiento->COD_CATEGORIA_MONEDA == 'MON0000000000001')
							{
								$CAN_DEBE_MN = $CAN_VALOR;
								$CAN_DEBE_ME = $CAN_VALOR/$asiento->CAN_TIPO_CAMBIO;
							}
						else
							{
								$CAN_DEBE_MN = $CAN_VALOR*$asiento->CAN_TIPO_CAMBIO;
								$CAN_DEBE_ME = $CAN_VALOR;
							}
					}
				else
					{
						if($asiento->COD_CATEGORIA_MONEDA == 'MON0000000000001')
							{
								$CAN_HABER_MN = $CAN_VALOR;
								$CAN_HABER_ME = $CAN_VALOR/$asiento->CAN_TIPO_CAMBIO;
							}
						else
							{
								$CAN_HABER_MN = $CAN_VALOR*$asiento->CAN_TIPO_CAMBIO;
								$CAN_HABER_ME = $CAN_VALOR;
							}
					}


					$CAN_DEBE_MN = $CAN_DEBE_MN*$porcentaje_debe;
					$CAN_HABER_MN = $CAN_HABER_MN*$porcentaje_debe;
					$CAN_DEBE_ME = $CAN_DEBE_ME*$porcentaje_debe;
					$CAN_HABER_ME = $CAN_HABER_ME*$porcentaje_debe;

					$IND_TIPO_OPERACION = 'I';
					$COD_ASIENTO_MOVIMIENTO = '';
					$COD_EMPR = $asiento->COD_EMPR;
					$COD_CENTRO = $asiento->COD_CENTRO;
					$COD_ASIENTO = $asiento->COD_ASIENTO;

					$COD_CUO = '';
					$IND_EXTORNO = '0';
					$TXT_TIPO_REFERENCIA = 'WEB.asientomovimientos';
					$TXT_REFERENCIA = $cod_asientomovimiento;
					$COD_ESTADO = '1';
					$COD_USUARIO_REGISTRO = Session::get('usuario_meta')->nombre;
					$COD_DOC_CTBLE_REF = '';
					$COD_ORDEN_REF = '';

		    		$detalle     	= 	$this->gn_crear_detalle_asiento_contable($IND_TIPO_OPERACION,
											$COD_ASIENTO_MOVIMIENTO,
											$COD_EMPR,
											$COD_CENTRO,
											$COD_ASIENTO,
											$COD_CUENTA_CONTABLE,
											$nro_cuenta_rel,
											$TXT_GLOSA_PRODUCTO,
											$CAN_DEBE_MN,
											$CAN_HABER_MN,

											$CAN_DEBE_ME,
											$CAN_HABER_ME,

											$contadorpro,

											$COD_CUO,
											$IND_EXTORNO,
											$TXT_TIPO_REFERENCIA,
											$TXT_REFERENCIA,
											$COD_ESTADO,
											$COD_USUARIO_REGISTRO,
											$COD_DOC_CTBLE_REF,
											$COD_ORDEN_REF);



					$asmo 			= 		WEBAsientoMovimiento::where('COD_ASIENTO_MOVIMIENTO','=',$detalle)->first();
					$asmo->IND_PRODUCTO = 2;
					$asmo->save();


			}


			/////////////////////////////////////////HABER 01/////////////////////////////////////////// 

			$asientomovimiento 		= 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$cod_asiento)
										->where('COD_ESTADO','=',1)
										->get();
			$cont_detalle_asiento 	=	count($asientomovimiento)+1;	
			$COD_CUENTA_CONTABLE 	= 	$item->COD_CUENTA_CONTABLE;
			$dcc 					=	WEBCuentaContable::where('id','=',$COD_CUENTA_CONTABLE)
										->where('cuenta_contable_transferencia_haber','<>','')->get();


			if(count($dcc)>0 and $ind_alimentacion <=0){

				$cctd = WEBCuentaContable::where('id','=',$COD_CUENTA_CONTABLE)->first();
				$nro_cuenta_rel =	Ltrim(Rtrim($cctd->cuenta_contable_transferencia_haber));

				$cc = WEBCuentaContable::where('nro_cuenta','=',$nro_cuenta_rel)->where('empresa_id','=',$empresa)->where('anio','=',$anio)->first();				
				$COD_CUENTA_CONTABLE 	= 	$cc->id;
				$TXT_GLOSA_PRODUCTO 	= 	$cc->nombre;




				$CAN_DEBE_MN = 0.000;
				$CAN_HABER_MN  = 0.000;
				$CAN_DEBE_ME  = 0.000;
				$CAN_HABER_ME  = 0.000;
				$CAN_VALOR  = 0.000;

				if($asiento->COD_CATEGORIA_MONEDA == 'MON0000000000001'){
					$CAN_VALOR =  $item->CAN_DEBE_MN+$item->CAN_HABER_MN;
				}
				else{
					$CAN_VALOR =  $item->CAN_DEBE_ME+$item->CAN_HABER_ME;
				}


				$contadorpro = $cont_detalle_asiento;
				$partida_id = 'COP0000000000002';
				if($asiento->COD_CATEGORIA_TIPO_DOCUMENTO=='TDO0000000000007'){
					$partida_id = 'COP0000000000001';			
				}


				if($partida_id == 'COP0000000000001')
					{
						if($asiento->COD_CATEGORIA_MONEDA == 'MON0000000000001')
							{
								$CAN_DEBE_MN = $CAN_VALOR;
								$CAN_DEBE_ME = $CAN_VALOR/$asiento->CAN_TIPO_CAMBIO;
							}
						else
							{
								$CAN_DEBE_MN = $CAN_VALOR*$asiento->CAN_TIPO_CAMBIO;
								$CAN_DEBE_ME = $CAN_VALOR;
							}
					}
				else
					{
						if($asiento->COD_CATEGORIA_MONEDA == 'MON0000000000001')
							{
								$CAN_HABER_MN = $CAN_VALOR;
								$CAN_HABER_ME = $CAN_VALOR/$asiento->CAN_TIPO_CAMBIO;
							}
						else
							{
								$CAN_HABER_MN = $CAN_VALOR*$asiento->CAN_TIPO_CAMBIO;
								$CAN_HABER_ME = $CAN_VALOR;
							}
					}


					$IND_TIPO_OPERACION = 'I';
					$COD_ASIENTO_MOVIMIENTO = '';
					$COD_EMPR = $asiento->COD_EMPR;
					$COD_CENTRO = $asiento->COD_CENTRO;
					$COD_ASIENTO = $asiento->COD_ASIENTO;

					$COD_CUO = '';
					$IND_EXTORNO = '0';
					$TXT_TIPO_REFERENCIA = 'WEB.asientomovimientos';
					$TXT_REFERENCIA = $cod_asientomovimiento;
					$COD_ESTADO = '1';
					$COD_USUARIO_REGISTRO = Session::get('usuario_meta')->nombre;
					$COD_DOC_CTBLE_REF = '';
					$COD_ORDEN_REF = '';

		    		$detalle     	= 	$this->gn_crear_detalle_asiento_contable($IND_TIPO_OPERACION,
											$COD_ASIENTO_MOVIMIENTO,
											$COD_EMPR,
											$COD_CENTRO,
											$COD_ASIENTO,
											$COD_CUENTA_CONTABLE,
											$nro_cuenta_rel,
											$TXT_GLOSA_PRODUCTO,
											$CAN_DEBE_MN,
											$CAN_HABER_MN,

											$CAN_DEBE_ME,
											$CAN_HABER_ME,

											$contadorpro,

											$COD_CUO,
											$IND_EXTORNO,
											$TXT_TIPO_REFERENCIA,
											$TXT_REFERENCIA,
											$COD_ESTADO,
											$COD_USUARIO_REGISTRO,
											$COD_DOC_CTBLE_REF,
											$COD_ORDEN_REF);



					$asmo 			= 		WEBAsientoMovimiento::where('COD_ASIENTO_MOVIMIENTO','=',$detalle)->first();
					$asmo->IND_PRODUCTO = 2;
					$asmo->save();


			}



		}


	 	return  'exito';	
	}

	public function gn_suma_debe_haber_balance_comprobacion($debe_haber,$cuentacontable,$periodoinicio_id,$periodofin_id)
	{


		$periodoinicio   	=   CONPeriodo::where('COD_PERIODO','=',$periodoinicio_id)->first();
		$periodofin   		=   CONPeriodo::where('COD_PERIODO','=',$periodofin_id)->first();

	    $suma 				= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    						->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    						->where('WEB.asientomovimientos.TXT_CUENTA_CONTABLE','=',$cuentacontable)
	    						->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    						->where('WEB.asientos.COD_ESTADO','=','1')
	    						->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    						->where('CON.PERIODO.COD_MES','>=',$periodoinicio->COD_MES)
	    						->where('CON.PERIODO.COD_MES','<=',$periodofin->COD_MES)
	    						->sum('WEB.asientomovimientos.CAN_DEBE_MN');

	 	return  $suma;	
	}


	private function gn_encontrar_cod_asiento($empresa_id, $centro_id, $periodo_id, $tipo_asiento_id,$tipo_referencia)
	{
		$cod_asiento 	= 	'';

		$asiento   		=   WEBAsiento::where('COD_EMPR','=',$empresa_id)
							->where('COD_CENTRO','=',$centro_id)
							->where('COD_PERIODO','=',$periodo_id)
							->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
							->where('TXT_TIPO_REFERENCIA','=',$tipo_referencia)
							->where('COD_ESTADO','=',1)
							->orderBy('FEC_USUARIO_CREA_AUD', 'DESC')
							->first();

		if(count($asiento)>0){
			$cod_asiento 	= 	$asiento->COD_ASIENTO;
		}

	    return $cod_asiento;
	}


	public function gn_tipo_cambio($fecha)
	{
		$tipo_cambio   				=   CMPTipoCambio::where('FEC_CAMBIO','<=',$fecha)
										->orderBy('FEC_CAMBIO', 'DESC')
										->first();
	    return $tipo_cambio;
	}

	public function gn_array_meses()
	{
		$meses 		= 	array('1' => 'ENERO','2' => 'FEBRERO','3' => 'MARZO','4' => 'ABRIL','5' => 'MAYO','6' => 'JUNIO','7' => 'JULIO','8' => 'AGOSTO','9' => 'SETIEMBRE','10' => 'OCTUBRE','11' => 'NOVIEMBRE','12' => 'DICIEMBRE');
	 	return  $meses;
	}


	public function gn_data_empresa($cod_empresa_id)
	{
		$empresa 		= 	STDEmpresa::where('COD_EMPR','=',$cod_empresa_id)->first();
	 	return  $empresa;
	}

	private function gn_generacion_combo_array($titulo, $todo , $array)
	{
		if($todo=='TODO'){
			$combo_anio_pc  		= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo_anio_pc  		= 	array('' => $titulo) + $array;
		}
	    return $combo_anio_pc;
	}

	private function gn_generacion_combo_meses_array($titulo, $todo)
	{

		$meses   					=   $this->gn_array_meses();
		$combo_meses 				= 	array('' => $titulo) + $meses;
	    return $combo_meses;
	}


	private function gn_genecombo_cuenta_contable_xnrocuentapadre($nro_cuenta,$anio)
	{

		$cuentacontable 			= 	WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
										->where('nro_cuenta','=',$nro_cuenta)
										->where('anio','=',$anio)
										->where('activo','=',1)
										->first();

			//dd($cuentacontable->id);								

		$array 						= 	DB::table('WEB.cuentacontables')
        								->where('activo','=',1)
        								->where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
        								->where('cuenta_contable_superior_id','=',$cuentacontable->id)
        								->select(DB::raw(" (nro_cuenta+'-'+nombre) as nro_cuenta,id "))
		        						->pluck('nro_cuenta','id')
										->toArray();

		$combo  					= 	array('' => 'Seleccione una cuenta') + $array;

	    return $combo;
	}

	private function gn_generacion_combo($tabla,$atributo1,$atributo2,$titulo,$todo) {
		
		$array 						= 	DB::table($tabla)
        								->where('activo','=',1)
		        						->pluck($atributo2,$atributo1)
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;					 			
	}

	private function gn_generacion_combo_tabla_osiris($tabla,$atributo1,$atributo2,$titulo,$todo) {
		
		$array 							= 	DB::table($tabla)
        									->where('COD_ESTADO','=',1)
		        							->pluck($atributo2,$atributo1)
											->toArray();
		if($titulo==''){
			$combo  					= 	$array;
		}else{
			if($todo=='TODO'){
				$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
			}else{
				$combo  				= 	array('' => $titulo) + $array;
			}
		}

	 	return  $combo;					 			
	}


	private function gn_generacion_combo_tabla_osiris_referencial($tabla,$atributo1,$atributo2,$titulo,$todo) {
		
		$array 							= 	DB::table($tabla)
        									->where('COD_ESTADO','=',1)
        									->whereIn('COD_TIPO_DOCUMENTO', array('TDO0000000000001','TDO0000000000003'))
		        							->pluck($atributo2,$atributo1)
											->toArray();
		if($titulo==''){
			$combo  					= 	$array;
		}else{
			if($todo=='TODO'){
				$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
			}else{
				$combo  				= 	array('' => $titulo) + $array;
			}
		}

	 	return  $combo;					 			
	}


	private function gn_generacion_combo_producto_kardex($titulo,$todo) {
		
		$empresa_id 				=   Session::get('empresas_meta')->COD_EMPR;

		$array 						= 	DB::table('WEB.kardexproductos')
										->join('ALM.PRODUCTO', 'ALM.PRODUCTO.COD_PRODUCTO', '=', 'WEB.kardexproductos.producto_id')
        								->where('WEB.kardexproductos.empresa_id','=',$empresa_id)
        								->where('WEB.kardexproductos.activo','=',1)
		        						->pluck('ALM.PRODUCTO.NOM_PRODUCTO','ALM.PRODUCTO.COD_PRODUCTO')
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;					 			
	}

	private function gn_generacion_combo_categoria($txt_grupo,$titulo,$todo) {
		
		$array 						= 	DB::table('CMP.CATEGORIA')
        								->where('COD_ESTADO','=',1)
        								->where('TXT_GRUPO','=',$txt_grupo)
		        						->pluck('NOM_CATEGORIA','COD_CATEGORIA')
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;					 			
	}


	public function gn_combo_caja_banco_efectivo($todo,$titulo)
	{
		$array 						= 	TESCajaBanco::whereIn('COD_CAJA_BANCO', array('ITRJCB0000000011','ITRJCB0000000008','ITRJCB0000000006'))
		        						->pluck('TXT_CAJA_BANCO','COD_CAJA_BANCO')
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;	


	}


	private function gn_generacion_combo_pago_cobro($txt_grupo,$titulo,$todo) {
		
		$array 						= 	DB::table('CMP.CATEGORIA')
        								->where('COD_ESTADO','=',1)
        								->whereIn('COD_CATEGORIA', array('TAS0000000000001','TAS0000000000007','TAS0000000000002'))
        								->where('TXT_GRUPO','=',$txt_grupo)
		        						->pluck('NOM_CATEGORIA','COD_CATEGORIA')
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;					 			
	}

	private function gn_generacion_combo_libro($titulo) {
		
		$combo  				= 	array('' => $titulo , 'LD' => 'LIBRO DIARIO', 'LM' => 'LIBRO MAYOR' ,'PC' => 'PLAN CONTABLE');

	 	return  $combo;					 			
	}



	private function gn_generacion_combo_categoria_xarrayid($txt_grupo,$titulo,$todo,$array_ids) {
		
		$array 						= 	DB::table('CMP.CATEGORIA')
        								->where('COD_ESTADO','=',1)
        								->where('TXT_GRUPO','=',$txt_grupo)
        								->whereIn('COD_CATEGORIA', $array_ids)
		        						->pluck('NOM_CATEGORIA','COD_CATEGORIA')
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;					 			
	}


	public function gn_background_fila_activo($activo)
	{
		$background =	'';
		if($activo == 0){
			$background = 'fila-desactivada';
		}
	    return $background;
	}


	public function gn_background_fila_ind_extorno($activo)
	{
		$background =	'';
		if($activo == 1){
			$background = 'fila-desactivada';
		}
	    return $background;
	}

	public function gn_combo_tipo_cliente()
	{
		$combo  	= 	array('' => 'Seleccione tipo de cliente' , '0' => 'Tercero', '1' => 'Relacionada');
	    return $combo;
	}

	public function gn_combo_transferencia_gratuita()
	{
		$combo  	= 	array('TODOS' => 'TODOS' , '1' => 'Tranferencia gratuita');
	    return $combo;
	}



	private function gn_generacion_combo_cuenta_detraccion($titulo,$todo)
	{

	   	$array   					=   STDEmpresa::where('COD_ESTADO','=',1)
	   									->where('IND_PROVEEDOR','=',1)
	   									->select(DB::raw(" (NRO_DOCUMENTO + ' - ' + NOM_EMPR) as NOM_EMPR , COD_EMPR"))
	   									->pluck('NOM_EMPR','NRO_DOCUMENTO')
		        						->take(10)
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;	
	}


	private function gn_generacion_combo_productos($titulo,$todo)
	{


		$array 						= 	ALMProducto::where('COD_ESTADO','=',1)
										->whereIn('IND_MATERIAL_SERVICIO', ['M','S'])
		        						->pluck('NOM_PRODUCTO','COD_PRODUCTO')
		        						->take(10)
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;	
	}


	public function gn_combo_periodo_xanio_xempresa($anio,$cod_empresa,$todo,$titulo)
	{
		$array 						= 	CONPeriodo::where('COD_ESTADO','=',1)
										->where('COD_ANIO','=',$anio)
										->where('COD_EMPR','=',$cod_empresa)
										->orderBy('COD_MES','DESC')
		        						->pluck('TXT_NOMBRE','COD_PERIODO')
		        						
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;	


	}


	public function gn_combo_empresa($titulo,$todo)
	{

		$array 						= 	STDEmpresa::where('COD_ESTADO','=',1)
										->where('IND_CLIENTE','=',1)
										->where('IND_PROVEEDOR','=',1)
										->select(DB::raw("NRO_DOCUMENTO + ' ' + NOM_EMPR as NOM_EMPR, COD_EMPR"))
		        						->pluck('NOM_EMPR','COD_EMPR')
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;	

	}


	public function gn_lista_periodo($anio,$cod_empresa)
	{
		
		$listaperiodo 				= 	CONPeriodo::where('COD_ESTADO','=',1)
										->where('COD_ANIO','=',$anio)
										->where('COD_EMPR','=',$cod_empresa)
										->orderby('COD_MES','desc')
										->get();

	 	return  $listaperiodo;	


	}

	public function gn_periodo_xanio_xmes($anio,$mes,$cod_empresa)
	{
		
		$periodo 					= 	CONPeriodo::where('COD_ESTADO','=',1)
										->where('COD_ANIO','=',$anio)
										->where('COD_MES','=',$mes)
										->where('COD_EMPR','=',$cod_empresa)
										->first();

	 	return  $periodo;	


	}


	private function gn_detalle_producto_xcoddocumento($cod_documento)
	{

		$listadetalleproducto 		= 	CMPDetalleProducto::where('COD_ESTADO','=',1)
										->where('COD_TABLA','=',$cod_documento)
		        						->get();

	 	return  $listadetalleproducto;	
	}

	public function gn_cliente_relacionado_tercero_xempresa($cod_empresa)
	{

		$tipo_cliente   =   "TERCERO";
		$empresa 		= 	STDEmpresa::where('COD_EMPR','=',$cod_empresa)
		        			->first();
		if($empresa->IND_RELACIONADO==1){
			$tipo_cliente   =   "RELACIONADO";
		}
	 	return  $tipo_cliente;	
	}


	private function gn_ind_relacionado_tercero_xempresa($cod_empresa)
	{
		$empresa 		= 	STDEmpresa::where('COD_EMPR','=',$cod_empresa)
		        			->first();

	 	return  $empresa->IND_RELACIONADO;	
	}


	public function gn_cuenta_contable_xproducto_xempresa_xanio($cod_producto,$cod_empresa,$ind_cliente,$anio_documento,$tipo_asiento)
	{

		$cuenta_contable= 	'';

		$empresa 		= 	WEBProductoEmpresa::where('producto_id','=',$cod_producto)
							->where('empresa_id','=',$cod_empresa)
							->where('anio','=',$anio_documento)
							//->where('cod_categoria_tipo_asiento','=',$tipo_asiento)
		        			->first();

		if(count($empresa)>0){

			//ventas
			if($tipo_asiento == 'TAS0000000000003'){
				if($ind_cliente == 0){
					if(trim($empresa->cuenta_contable_venta_tercero_id) != ''){
						$cuenta_contable = 	$empresa->cuentacontabletercero->nro_cuenta .' '.$empresa->cuentacontabletercero->nombre;
					}
				}else{
					if(trim($empresa->cuenta_contable_venta_relacionada_id) !=  ''){
						$cuenta_contable = 	$empresa->cuentacontablerelacionada->nro_cuenta .' '.$empresa->cuentacontablerelacionada->nombre;
					}
				}	
			}
			//compras
			if($tipo_asiento == 'TAS0000000000004'){
				if(trim($empresa->cuenta_contable_compra_id) != ''){
					$cuenta_contable = 	$empresa->cuentacontablecompra->nro_cuenta .' '.$empresa->cuentacontablecompra->nombre;
				}
			}

		}
	 	return  $cuenta_contable;	
	}



	public function gn_crear_asiento_contable(  $IND_TIPO_OPERACION,
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
												$IND_AFECTO)
	{


        $stmt 		= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.asientos_IUD
							@IND_TIPO_OPERACION = ?,
							@COD_ASIENTO = ?,
							@COD_EMPR = ?,
							@COD_CENTRO = ?,
							@COD_PERIODO = ?,
							@COD_CATEGORIA_TIPO_ASIENTO = ?,
							@TXT_CATEGORIA_TIPO_ASIENTO = ?,
							@NRO_ASIENTO = ?,
							@FEC_ASIENTO = ?,
							@TXT_GLOSA = ?,

							@COD_CATEGORIA_ESTADO_ASIENTO = ?,
							@TXT_CATEGORIA_ESTADO_ASIENTO = ?,
							@COD_CATEGORIA_MONEDA = ?,
							@TXT_CATEGORIA_MONEDA = ?,
							@CAN_TIPO_CAMBIO = ?,
							@CAN_TOTAL_DEBE = ?,
							@CAN_TOTAL_HABER = ?,
							@COD_ASIENTO_EXTORNO = ?,
							@COD_ASIENTO_EXTORNADO = ?,
							@IND_EXTORNO = ?,

							@COD_ASIENTO_MODELO = ?,
							@TXT_TIPO_REFERENCIA = ?,
							@TXT_REFERENCIA = ?,
							@COD_ESTADO = ?,
							@COD_USUARIO_REGISTRO = ?,
							@COD_MOTIVO_EXTORNO = ?,
							@GLOSA_EXTORNO = ?,
							@COD_EMPR_CLI = ?,
							@TXT_EMPR_CLI = ?,
							@COD_CATEGORIA_TIPO_DOCUMENTO = ?,

							@TXT_CATEGORIA_TIPO_DOCUMENTO = ?,
							@NRO_SERIE = ?,
							@NRO_DOC = ?,
							@FEC_DETRACCION = ?,
							@NRO_DETRACCION = ?,
							@CAN_DESCUENTO_DETRACCION = ?,
							@CAN_TOTAL_DETRACCION = ?,
							@COD_CATEGORIA_TIPO_DOCUMENTO_REF = ?,
							@TXT_CATEGORIA_TIPO_DOCUMENTO_REF = ?,
							@NRO_SERIE_REF = ?,

							@NRO_DOC_REF = ?,
							@FEC_VENCIMIENTO = ?,
							@IND_AFECTO = ?

							');

        $stmt->bindParam(1, $IND_TIPO_OPERACION ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $COD_ASIENTO  ,PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 16);
        $stmt->bindParam(3, $COD_EMPR  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $COD_CENTRO  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $COD_PERIODO ,PDO::PARAM_STR);                   
        $stmt->bindParam(6, $COD_CATEGORIA_TIPO_ASIENTO  ,PDO::PARAM_STR);
        $stmt->bindParam(7, $TXT_CATEGORIA_TIPO_ASIENTO  ,PDO::PARAM_STR);
        $stmt->bindParam(8, $NRO_ASIENTO  ,PDO::PARAM_STR);
        $stmt->bindParam(9, $FEC_ASIENTO  ,PDO::PARAM_STR);
        $stmt->bindParam(10, $TXT_GLOSA  ,PDO::PARAM_STR);

        $stmt->bindParam(11, $COD_CATEGORIA_ESTADO_ASIENTO ,PDO::PARAM_STR);                   
        $stmt->bindParam(12, $TXT_CATEGORIA_ESTADO_ASIENTO  ,PDO::PARAM_STR);
        $stmt->bindParam(13, $COD_CATEGORIA_MONEDA  ,PDO::PARAM_STR);
        $stmt->bindParam(14, $TXT_CATEGORIA_MONEDA  ,PDO::PARAM_STR);
        $stmt->bindParam(15, $CAN_TIPO_CAMBIO ,PDO::PARAM_STR);                   
        $stmt->bindParam(16, $CAN_TOTAL_DEBE  ,PDO::PARAM_STR);
        $stmt->bindParam(17, $CAN_TOTAL_HABER  ,PDO::PARAM_STR);
        $stmt->bindParam(18, $COD_ASIENTO_EXTORNO  ,PDO::PARAM_STR);
        $stmt->bindParam(19, $COD_ASIENTO_EXTORNADO  ,PDO::PARAM_STR);
        $stmt->bindParam(20, $IND_EXTORNO  ,PDO::PARAM_STR);

        $stmt->bindParam(21, $COD_ASIENTO_MODELO ,PDO::PARAM_STR);                   
        $stmt->bindParam(22, $TXT_TIPO_REFERENCIA  ,PDO::PARAM_STR);
        $stmt->bindParam(23, $TXT_REFERENCIA  ,PDO::PARAM_STR);
        $stmt->bindParam(24, $COD_ESTADO  ,PDO::PARAM_STR);
        $stmt->bindParam(25, $COD_USUARIO_REGISTRO ,PDO::PARAM_STR);                   
        $stmt->bindParam(26, $COD_MOTIVO_EXTORNO  ,PDO::PARAM_STR);
        $stmt->bindParam(27, $GLOSA_EXTORNO  ,PDO::PARAM_STR);
        $stmt->bindParam(28, $COD_EMPR_CLI  ,PDO::PARAM_STR);
        $stmt->bindParam(29, $TXT_EMPR_CLI  ,PDO::PARAM_STR);
        $stmt->bindParam(30, $COD_CATEGORIA_TIPO_DOCUMENTO  ,PDO::PARAM_STR);

        $stmt->bindParam(31, $TXT_CATEGORIA_TIPO_DOCUMENTO ,PDO::PARAM_STR);                   
        $stmt->bindParam(32, $NRO_SERIE  ,PDO::PARAM_STR);
        $stmt->bindParam(33, $NRO_DOC  ,PDO::PARAM_STR);
        $stmt->bindParam(34, $FEC_DETRACCION  ,PDO::PARAM_STR);
        $stmt->bindParam(35, $NRO_DETRACCION ,PDO::PARAM_STR);                   
        $stmt->bindParam(36, $CAN_DESCUENTO_DETRACCION  ,PDO::PARAM_STR);
        $stmt->bindParam(37, $CAN_TOTAL_DETRACCION  ,PDO::PARAM_STR);
        $stmt->bindParam(38, $COD_CATEGORIA_TIPO_DOCUMENTO_REF  ,PDO::PARAM_STR);
        $stmt->bindParam(39, $TXT_CATEGORIA_TIPO_DOCUMENTO_REF  ,PDO::PARAM_STR);
        $stmt->bindParam(40, $NRO_SERIE_REF  ,PDO::PARAM_STR);

        $stmt->bindParam(41, $NRO_DOC_REF  ,PDO::PARAM_STR);
        $stmt->bindParam(42, $FEC_VENCIMIENTO  ,PDO::PARAM_STR);
        $stmt->bindParam(43, $IND_AFECTO  ,PDO::PARAM_STR);
        $stmt->execute();
        $cod = $stmt->fetch();
        $codorden = $cod[0];

		return $codorden;
		
	}




	public function gn_crear_detalle_asiento_contable(	$IND_TIPO_OPERACION,
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


														$COD_EMPR_CLI_REF='',
														$TXT_EMPR_CLI_REF='',
														$DOCUMENTO_REF=''

													)
	{


        $stmt 		= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.ASIENTO_MOVIMIENTOS_IUD
							@IND_TIPO_OPERACION = ?,
							@COD_ASIENTO_MOVIMIENTO = ?,
							@COD_EMPR = ?,
							@COD_CENTRO = ?,
							@COD_ASIENTO = ?,
							@COD_CUENTA_CONTABLE = ?,
							@TXT_CUENTA_CONTABLE = ?,
							@TXT_GLOSA = ?,
							@CAN_DEBE_MN = ?,
							@CAN_HABER_MN = ?,

							@CAN_DEBE_ME = ?,
							@CAN_HABER_ME = ?,
							@NRO_LINEA = ?,
							@COD_CUO = ?,
							@IND_EXTORNO = ?,
							@TXT_TIPO_REFERENCIA = ?,
							@TXT_REFERENCIA = ?,
							@COD_ESTADO = ?,


							@COD_USUARIO_REGISTRO = ?,
							@COD_DOC_CTBLE_REF = ?,
							@COD_ORDEN_REF = ?,


							@COD_EMPR_CLI_REF = ?,
							@TXT_EMPR_CLI_REF = ?,
							@DOCUMENTO_REF = ?


							');

        $stmt->bindParam(1, $IND_TIPO_OPERACION ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $COD_ASIENTO_MOVIMIENTO  ,PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 16);
        $stmt->bindParam(3, $COD_EMPR  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $COD_CENTRO  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $COD_ASIENTO ,PDO::PARAM_STR);                   
        $stmt->bindParam(6, $COD_CUENTA_CONTABLE  ,PDO::PARAM_STR);
        $stmt->bindParam(7, $TXT_CUENTA_CONTABLE  ,PDO::PARAM_STR);
        $stmt->bindParam(8, $TXT_GLOSA  ,PDO::PARAM_STR);
        $stmt->bindParam(9, $CAN_DEBE_MN  ,PDO::PARAM_STR);
        $stmt->bindParam(10, $CAN_HABER_MN  ,PDO::PARAM_STR);

        $stmt->bindParam(11, $CAN_DEBE_ME ,PDO::PARAM_STR);                   
        $stmt->bindParam(12, $CAN_HABER_ME  ,PDO::PARAM_STR);
        $stmt->bindParam(13, $NRO_LINEA  ,PDO::PARAM_STR);
        $stmt->bindParam(14, $COD_CUO  ,PDO::PARAM_STR);
        $stmt->bindParam(15, $IND_EXTORNO ,PDO::PARAM_STR);                   
        $stmt->bindParam(16, $TXT_TIPO_REFERENCIA  ,PDO::PARAM_STR);
        $stmt->bindParam(17, $TXT_REFERENCIA  ,PDO::PARAM_STR);
        $stmt->bindParam(18, $COD_ESTADO  ,PDO::PARAM_STR);
        $stmt->bindParam(19, $COD_USUARIO_REGISTRO  ,PDO::PARAM_STR);
        $stmt->bindParam(20, $COD_DOC_CTBLE_REF  ,PDO::PARAM_STR);
        $stmt->bindParam(21, $COD_ORDEN_REF ,PDO::PARAM_STR);  

        $stmt->bindParam(22, $COD_EMPR_CLI_REF  ,PDO::PARAM_STR);
        $stmt->bindParam(23, $TXT_EMPR_CLI_REF  ,PDO::PARAM_STR);
        $stmt->bindParam(24, $DOCUMENTO_REF ,PDO::PARAM_STR); 


        $stmt->execute();

        $cod = $stmt->fetch();
        $codorden = $cod[0];

		return $codorden;
		
	}





	public function gn_crear_detalle_asiento_contable_movimiento(	$IND_TIPO_OPERACION,
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
														$IND_PRODUCTO)
	{


        $stmt 		= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.ASIENTO_MOVIMIENTOS_IUD
							@IND_TIPO_OPERACION = ?,
							@COD_ASIENTO_MOVIMIENTO = ?,
							@COD_EMPR = ?,
							@COD_CENTRO = ?,
							@COD_ASIENTO = ?,
							@COD_CUENTA_CONTABLE = ?,
							@TXT_CUENTA_CONTABLE = ?,
							@TXT_GLOSA = ?,
							@CAN_DEBE_MN = ?,
							@CAN_HABER_MN = ?,

							@CAN_DEBE_ME = ?,
							@CAN_HABER_ME = ?,
							@NRO_LINEA = ?,
							@COD_CUO = ?,
							@IND_EXTORNO = ?,
							@TXT_TIPO_REFERENCIA = ?,
							@TXT_REFERENCIA = ?,
							@COD_ESTADO = ?,
							@COD_USUARIO_REGISTRO = ?,
							@COD_DOC_CTBLE_REF = ?,

							@COD_ORDEN_REF = ?,
							@IND_PRODUCTO = ?

							');

        $stmt->bindParam(1, $IND_TIPO_OPERACION ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $COD_ASIENTO_MOVIMIENTO  ,PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 16);
        $stmt->bindParam(3, $COD_EMPR  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $COD_CENTRO  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $COD_ASIENTO ,PDO::PARAM_STR);                   
        $stmt->bindParam(6, $COD_CUENTA_CONTABLE  ,PDO::PARAM_STR);
        $stmt->bindParam(7, $TXT_CUENTA_CONTABLE  ,PDO::PARAM_STR);
        $stmt->bindParam(8, $TXT_GLOSA  ,PDO::PARAM_STR);
        $stmt->bindParam(9, $CAN_DEBE_MN  ,PDO::PARAM_STR);
        $stmt->bindParam(10, $CAN_HABER_MN  ,PDO::PARAM_STR);

        $stmt->bindParam(11, $CAN_DEBE_ME ,PDO::PARAM_STR);                   
        $stmt->bindParam(12, $CAN_HABER_ME  ,PDO::PARAM_STR);
        $stmt->bindParam(13, $NRO_LINEA  ,PDO::PARAM_STR);
        $stmt->bindParam(14, $COD_CUO  ,PDO::PARAM_STR);
        $stmt->bindParam(15, $IND_EXTORNO ,PDO::PARAM_STR);                   
        $stmt->bindParam(16, $TXT_TIPO_REFERENCIA  ,PDO::PARAM_STR);
        $stmt->bindParam(17, $TXT_REFERENCIA  ,PDO::PARAM_STR);
        $stmt->bindParam(18, $COD_ESTADO  ,PDO::PARAM_STR);
        $stmt->bindParam(19, $COD_USUARIO_REGISTRO  ,PDO::PARAM_STR);
        $stmt->bindParam(20, $COD_DOC_CTBLE_REF  ,PDO::PARAM_STR);

        $stmt->bindParam(21, $COD_ORDEN_REF ,PDO::PARAM_STR);   
        $stmt->bindParam(22, $IND_PRODUCTO ,PDO::PARAM_STR);                 
        $stmt->execute();


		return true;
		
	}



}