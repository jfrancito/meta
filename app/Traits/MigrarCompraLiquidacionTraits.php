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
use App\Modelos\WEBViewMigrarVenta;
use App\Modelos\CMPDocumentoCtble;
use App\Modelos\WEBHistorialMigrar;
use App\Modelos\CMPDetalleProducto;
use App\Modelos\WEBProductoEmpresa;
use App\Modelos\WEBViewMigrarCompras;
use App\Modelos\WEBViewMigrarLiquidacionCompra;
use App\Modelos\WEBAsientoMovimiento;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait MigrarCompraLiquidacionTraits
{
	

	private function mvl_lista_compras_liquidacion_migrar_agrupado()
	{

		$lista_migrar_compras_liq		=	WEBViewMigrarLiquidacionCompra::select(DB::raw('COD_DOC_FORMAL,
											COD_PERIODO,
											NRO_SERIE_FORMAL,
											NRO_DOC_FORMAL,
											FEC_EMISION,
											COD_EMPRESA,
											TXT_EMPRESA,
											COD_EMPR,
											COD_CENTRO,
											COD_CATEGORIA_MONEDA,
											TXT_CATEGORIA_MONEDA,
											CAN_TIPO_CAMBIO,
											SUM(CAN_TOTAL) AS CAN_TOTAL'))
											->groupBy('COD_DOC_FORMAL')
											->groupBy('COD_PERIODO')
											->groupBy('NRO_SERIE_FORMAL')
											->groupBy('NRO_DOC_FORMAL')
											->groupBy('FEC_EMISION')
											->groupBy('COD_EMPRESA')
											->groupBy('TXT_EMPRESA')
											->groupBy('COD_EMPR')
											->groupBy('COD_CENTRO')
											->groupBy('COD_CATEGORIA_MONEDA')
											->groupBy('TXT_CATEGORIA_MONEDA')
											->groupBy('CAN_TIPO_CAMBIO')
											->get();
		return $lista_migrar_compras_liq;
	}

	private function mvl_asignar_asiento_liquidacion_compra($doc,$tipo_asiento)
	{
	
		$anio 							=	substr($doc->FEC_EMISION, 0, 4);
		//cantidad de detalle
		$count_detalle					=	WEBViewMigrarLiquidacionCompra::select(DB::raw('*'))
											->where('COD_DOC_FORMAL','=',$doc->COD_DOC_FORMAL)
											->get();
		//cantidad detalle configuracion
		$detalle						=	WEBViewMigrarLiquidacionCompra::join('WEB.productoempresas', 'WEB.productoempresas.producto_id', '=', 'WEB.viewmigrarliquidacioncompras.COD_PRODUCTO')
											->where('COD_DOC_FORMAL','=',$doc->COD_DOC_FORMAL)
											->where('WEB.productoempresas.cuenta_contable_compra_id','<>','')
											->where('WEB.productoempresas.anio','=',$anio)
											->where('WEB.productoempresas.empresa_id','=',$doc->COD_EMPR)
											->get();

		if(count($detalle) == count($count_detalle)){

			$periodo 	= 	CONPeriodo::where('COD_PERIODO','=',$doc->COD_PERIODO)->first();
			$glosa 		=	'COMPRA : LIQUIDACION DE COMPRA '.$doc->NRO_SERIE_FORMAL.' '.$doc->NRO_DOC_FORMAL.' // '.$doc->TXT_EMPRESA;

			$NRODOC     =	substr($doc->NRO_DOC_FORMAL, -7);
			$NRO_DOC 	= 	str_pad($NRODOC, 7, "0", STR_PAD_LEFT);


			$IND_TIPO_OPERACION = 'I';
			$COD_ASIENTO = '';
			$COD_EMPR = $doc->COD_EMPR;
			$COD_CENTRO = $doc->COD_CENTRO;
			$COD_PERIODO = $periodo->COD_PERIODO;
			$COD_CATEGORIA_TIPO_ASIENTO = $tipo_asiento;
			$TXT_CATEGORIA_TIPO_ASIENTO = 'COMPRAS';
			$NRO_ASIENTO = '';
			$FEC_ASIENTO = substr($doc->FEC_EMISION, 0, 10);
			$TXT_GLOSA = $glosa;
			$COD_CATEGORIA_ESTADO_ASIENTO = 'IACHTE0000000025';
			$TXT_CATEGORIA_ESTADO_ASIENTO = 'CONFIRMADO';
			$COD_CATEGORIA_MONEDA = $doc->COD_CATEGORIA_MONEDA;
			$TXT_CATEGORIA_MONEDA = $doc->TXT_CATEGORIA_MONEDA;
			$CAN_TIPO_CAMBIO = $doc->CAN_TIPO_CAMBIO;
			$CAN_TOTAL_DEBE = $doc->CAN_TOTAL;
			$CAN_TOTAL_HABER = $doc->CAN_TOTAL;

			$COD_ASIENTO_EXTORNO = '';
			$COD_ASIENTO_EXTORNADO = '';
			$IND_EXTORNO = '0';

			$COD_ASIENTO_MODELO = '';
			$TXT_TIPO_REFERENCIA = 'CMP_DOCUMENTO_CTBLE';
			$TXT_REFERENCIA = $doc->COD_DOC_FORMAL;
			$COD_ESTADO = '1';
			$COD_USUARIO_REGISTRO = 'ADMIN';
			$COD_MOTIVO_EXTORNO = '';
			$GLOSA_EXTORNO = '';
			$COD_EMPR_CLI = $doc->COD_EMPRESA;
			$TXT_EMPR_CLI = $doc->TXT_EMPRESA;

			$COD_CATEGORIA_TIPO_DOCUMENTO = 'TDO0000000000004';

			$TXT_CATEGORIA_TIPO_DOCUMENTO = 'LIQUIDACION COMPRA';
			$NRO_SERIE = $doc->NRO_SERIE_FORMAL;
			$NRO_DOC = $NRO_DOC;
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


    		$asientocontable     	= 	$this->mvl_crear_asiento_contable($IND_TIPO_OPERACION,
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

    		$linea 	=	1;
    		$suma_de_60 = 0;
    		$sobrante = 0;


			foreach($detalle as $index => $item){

				$cuenta_contable 	=	WEBCuentaContable::where('id','=',$item->cuenta_contable_compra_id)->first();

				$IND_TIPO_OPERACION = 'I';
				$COD_ASIENTO_MOVIMIENTO = '';
				$COD_EMPR = $doc->COD_EMPR;
				$COD_CENTRO = $doc->COD_CENTRO;
				$COD_ASIENTO = $asientocontable;
				$COD_CUENTA_CONTABLE = $cuenta_contable->id;
				$TXT_CUENTA_CONTABLE = $cuenta_contable->nro_cuenta;
				$TXT_GLOSA = $item->TXT_NOMBRE_PRODUCTO;
				$CAN_DEBE_MN = $item->CAN_IMPORTE;
				$CAN_HABER_MN = '0.0000';
				$CAN_DEBE_ME = $item->CAN_IMPORTE/$CAN_TIPO_CAMBIO;
				$CAN_HABER_ME = '0.0000';

				$NRO_LINEA = $linea;
				$COD_CUO = '';
				$IND_EXTORNO = '0';
				$TXT_TIPO_REFERENCIA = '';
				$TXT_REFERENCIA = '';
				$COD_ESTADO = '1';
				$COD_USUARIO_REGISTRO = 'ADMIN';
				$COD_DOC_CTBLE_REF = '';
				$COD_ORDEN_REF = '';

				$IND_PRODUCTO = '1';
				$COD_PRODUCTO =  $item->COD_PRODUCTO;
				$TXT_NOMBRE_PRODUCTO =  $item->TXT_NOMBRE_PRODUCTO;
				$COD_LOTE =  $item->COD_LOTE;
				$NRO_LINEA_PRODUCTO =  $item->NRO_LINEA;

				$suma_de_60 = $suma_de_60 + $item->CAN_IMPORTE;


	    		$detalle     	= 	$this->mcl_crear_detalle_asiento_contable(	$IND_TIPO_OPERACION,
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

															$IND_PRODUCTO,
															$COD_PRODUCTO,
															$TXT_NOMBRE_PRODUCTO,
															$COD_LOTE,
															$NRO_LINEA_PRODUCTO

														);

	    		$linea = $linea + 1;
			}	


			//completar los centimos

			$sobrante = $doc->CAN_TOTAL - $suma_de_60;
			$valor_total = 0;

			if($sobrante>0){

				$asientomovimientocompletar 	= 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$COD_ASIENTO)
													->where('IND_PRODUCTO','=',1)
													->first();
				$valor_total = $asientomovimientocompletar->CAN_DEBE_MN + $sobrante;
				$asientomovimientocompletar->CAN_DEBE_MN = $valor_total;
				$asientomovimientocompletar->CAN_DEBE_ME = $valor_total/$CAN_TIPO_CAMBIO;
				$asientomovimientocompletar->save(); 

			}

			//CUENTA 42
			$cuenta_contable 	=	WEBCuentaContable::where('nro_cuenta','=','421203')->where('anio','=',$anio)
									->where('empresa_id','=',$doc->COD_EMPR)
									->first();
			$IND_TIPO_OPERACION = 'I';
			$COD_ASIENTO_MOVIMIENTO = '';
			$COD_EMPR = $doc->COD_EMPR;;
			$COD_CENTRO = $doc->COD_CENTRO;;
			$COD_ASIENTO = $asientocontable;
			$COD_CUENTA_CONTABLE = $cuenta_contable->id;
			$TXT_CUENTA_CONTABLE = $cuenta_contable->nro_cuenta;
			$TXT_GLOSA = $cuenta_contable->nombre;
			$CAN_DEBE_MN = '0.0000';
			$CAN_HABER_MN = $doc->CAN_TOTAL;

			$CAN_DEBE_ME = '0.000';
			$CAN_HABER_ME = $doc->CAN_TOTAL/$CAN_TIPO_CAMBIO;

			$NRO_LINEA = $linea;
			$COD_CUO = '';
			$IND_EXTORNO = '0';
			$TXT_TIPO_REFERENCIA = '';
			$TXT_REFERENCIA = '';
			$COD_ESTADO = '1';
			$COD_USUARIO_REGISTRO = 'ADMIN';
			$COD_DOC_CTBLE_REF = '';
			$COD_ORDEN_REF = '';

			$IND_PRODUCTO = '0';
			$COD_PRODUCTO =  '';
			$TXT_NOMBRE_PRODUCTO =  '';
			$COD_LOTE =  '';
			$NRO_LINEA_PRODUCTO =  '';				

    		$detalle     	= 	$this->mcl_crear_detalle_asiento_contable(	$IND_TIPO_OPERACION,
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

														$IND_PRODUCTO,
														$COD_PRODUCTO,
														$TXT_NOMBRE_PRODUCTO,
														$COD_LOTE,
														$NRO_LINEA_PRODUCTO

													);




		    $listaasientomovimientodes 	= 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asientocontable)
		    								->where('COD_ESTADO','=','1')
		    								->where('IND_PRODUCTO','<>','2')
		    								->orderBy('NRO_LINEA', 'asc')
		    								->get();

			foreach($listaasientomovimientodes as $index => $item){
				$this->gn_crear_asiento_destino($asientocontable,$item->COD_ASIENTO_MOVIMIENTO,$anio);
			}





		}



	}



	public function mcl_crear_detalle_asiento_contable(	$IND_TIPO_OPERACION,
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

														$IND_PRODUCTO,
														$COD_PRODUCTO,
														$TXT_NOMBRE_PRODUCTO,
														$COD_LOTE,
														$NRO_LINEA_PRODUCTO
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

							@IND_PRODUCTO = ?,
							@COD_PRODUCTO = ?,
							@TXT_NOMBRE_PRODUCTO = ?,
							@COD_LOTE = ?,
							@NRO_LINEA_PRODUCTO = ?

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
        $stmt->bindParam(23, $COD_PRODUCTO ,PDO::PARAM_STR); 
        $stmt->bindParam(24, $TXT_NOMBRE_PRODUCTO ,PDO::PARAM_STR); 
        $stmt->bindParam(25, $COD_LOTE ,PDO::PARAM_STR); 
        $stmt->bindParam(26, $NRO_LINEA_PRODUCTO ,PDO::PARAM_STR); 


        $stmt->execute();

        $cod = $stmt->fetch();
        $codorden = $cod[0];

		return $codorden;
		
	}


	public function mvl_crear_asiento_contable(  $IND_TIPO_OPERACION,
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
												$IND_AFECTO
												)
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

}