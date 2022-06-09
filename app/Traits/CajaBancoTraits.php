<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use App\Modelos\TESOperacionCaja;
use App\Modelos\TESCajaBanco;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


trait CajaBancoTraits
{

	private function cb_combo_cuenta_referencia()
	{
		$combo_cuenta_referencia  	= 	array('12' => 'Clientes' , '42' => 'Proveedores');
	    return $combo_cuenta_referencia;
	}

	private function cb_buscar_tipo_asiento($movimiento)
	{

		$tipo_asiento = '';
		if($movimiento->cajabanco->IND_BANCO == 1){
			$tipo_asiento = 'TAS0000000000002';
		}
		if($movimiento->cajabanco->IND_CAJA == 1){
			$tipo_asiento = 'TAS0000000000001';
		}
		return $tipo_asiento;
	}

	private function cb_lista_banco_caja()
	{

		$listabancocaja = 	TESCajaBanco::where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
							->where('COD_ESTADO','=',1)
							->get();
		return $listabancocaja;
	}


	private function cb_operacion_caja_movimientos($cuenta_referencia,$nrooperacion)
	{

		$cod_operacion_caja 		=  	$this->cb_cod_egreso_ingreso($cuenta_referencia);

		$listamovimientos 			=  	TESOperacionCaja::where('NRO_VOUCHER', 'Like', '%'.$nrooperacion.'%')
										->where('COD_CATEGORIA_OPERACION_CAJA','=',$cod_operacion_caja)
										->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
										->where('IND_EXTORNO','=',0)
										->select(DB::raw('COD_CAJA_BANCO,
															NRO_CUENTA_BANCARIA,
															NRO_VOUCHER,
															TXT_CATEGORIA_OPERACION_CAJA,
															TXT_CATEGORIA_MONEDA,
															FEC_MOVIMIENTO_CAJABANCO,
															SUM(CAN_DEBE_MN) AS CAN_DEBE_MN,
															SUM(CAN_DEBE_ME) AS CAN_DEBE_ME,
															SUM(CAN_HABER_MN) AS CAN_HABER_MN,
															SUM(CAN_HABER_ME) AS CAN_HABER_ME,
															CAN_TIPO_CAMBIO'))
										->groupBy('COD_CAJA_BANCO')
										->groupBy('NRO_CUENTA_BANCARIA')
										->groupBy('NRO_VOUCHER')
										->groupBy('TXT_CATEGORIA_OPERACION_CAJA')
										->groupBy('TXT_CATEGORIA_MONEDA')
										->groupBy('CAN_TIPO_CAMBIO')
										->groupBy('FEC_MOVIMIENTO_CAJABANCO')
										->get();
	    return $listamovimientos;

	}


	private function cb_operacion_caja_movimientos_first($cuenta_referencia,$nrooperacion,$cod_caja_banco,$fec_movimiento_caja)
	{

		$cod_operacion_caja 		=  	$this->cb_cod_egreso_ingreso($cuenta_referencia);

		$listamovimientos 			=  	TESOperacionCaja::where('NRO_VOUCHER', 'Like', '%'.$nrooperacion.'%')
										->where('COD_CATEGORIA_OPERACION_CAJA','=',$cod_operacion_caja)
										->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
										->where('COD_CAJA_BANCO', '=', $cod_caja_banco)
										->where('FEC_MOVIMIENTO_CAJABANCO', '=', $fec_movimiento_caja)
										->where('IND_EXTORNO','=',0)
										->select(DB::raw('COD_CAJA_BANCO,
															NRO_CUENTA_BANCARIA,
															NRO_VOUCHER,
															TXT_CATEGORIA_OPERACION_CAJA,
															MAX(COD_PERIODO_CONCILIA) AS COD_PERIODO_CONCILIA,
															MAX(COD_CENTRO) AS COD_CENTRO,
															COD_CATEGORIA_MONEDA,
															TXT_CATEGORIA_MONEDA,
															FEC_MOVIMIENTO_CAJABANCO,
															FEC_OPERACION,
															MAX(COD_EMPR_AFECTA) AS COD_EMPR_AFECTA,
															SUM(CAN_DEBE_MN) AS CAN_DEBE_MN,
															SUM(CAN_DEBE_ME) AS CAN_DEBE_ME,
															SUM(CAN_HABER_MN) AS CAN_HABER_MN,
															SUM(CAN_HABER_ME) AS CAN_HABER_ME,
															CAN_TIPO_CAMBIO'))
										->groupBy('COD_CAJA_BANCO')
										->groupBy('NRO_CUENTA_BANCARIA')
										->groupBy('NRO_VOUCHER')
										->groupBy('TXT_CATEGORIA_OPERACION_CAJA')
										->groupBy('COD_CATEGORIA_MONEDA')
										->groupBy('TXT_CATEGORIA_MONEDA')
										->groupBy('CAN_TIPO_CAMBIO')
										->groupBy('FEC_MOVIMIENTO_CAJABANCO')
										->groupBy('FEC_OPERACION')
										->first();
	    return $listamovimientos;

	}



	private function cb_detalle_operacion_caja_movimientos($cod_caja_banco,$nrooperacion,$nro_referencia,$fec_movimiento_caja)
	{

		$cod_operacion_caja 		=  	$this->cb_cod_egreso_ingreso($nro_referencia);


		$listadetallemovimientos 	=  	TESOperacionCaja::join('TES.CAJA_BANCO', 'TES.CAJA_BANCO.COD_CAJA_BANCO', '=', 'TES.OPERACION_CAJA.COD_CAJA_BANCO')
										->join('CON.ASIENTO', 'CON.ASIENTO.COD_ASIENTO', '=', 'TES.OPERACION_CAJA.COD_ASIENTO')
										->join('CON.ASIENTO_MOVIMIENTO', 'CON.ASIENTO_MOVIMIENTO.COD_ASIENTO', '=', 'CON.ASIENTO.COD_ASIENTO')
										->join('CMP.DOCUMENTO_CTBLE', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', '=', 'CON.ASIENTO_MOVIMIENTO.TXT_REFERENCIA')
										->where('TES.OPERACION_CAJA.COD_CAJA_BANCO', '=', $cod_caja_banco)
										->where('TES.OPERACION_CAJA.NRO_VOUCHER', '=', $nrooperacion)
										->where('TES.OPERACION_CAJA.COD_CATEGORIA_OPERACION_CAJA','=',$cod_operacion_caja)
										->where('TES.OPERACION_CAJA.FEC_MOVIMIENTO_CAJABANCO', '=', $fec_movimiento_caja)
										->where('TES.OPERACION_CAJA.IND_EXTORNO','=',0)
										->where('TES.OPERACION_CAJA.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
										->whereNotIn('CON.ASIENTO_MOVIMIENTO.TXT_TIPO_REFERENCIA',['TES.OPERACION_CAJA'])
										->select(DB::raw('	TES.CAJA_BANCO.TXT_BANCO,
															TES.CAJA_BANCO.TXT_NRO_CCI,
															TES.OPERACION_CAJA.FEC_OPERACION,
															TES.OPERACION_CAJA.FEC_MOVIMIENTO_CAJABANCO,
															TES.OPERACION_CAJA.COD_EMPR_AFECTA,
															TES.OPERACION_CAJA.TXT_EMPR_AFECTA,
															TES.OPERACION_CAJA.COD_CONTRATO_AFECTA,
															TES.OPERACION_CAJA.TXT_FLUJO_CAJA,
															TES.OPERACION_CAJA.NRO_VOUCHER,
															CON.ASIENTO_MOVIMIENTO.TXT_TIPO_REFERENCIA,
															CON.ASIENTO_MOVIMIENTO.TXT_REFERENCIA,
															CON.ASIENTO_MOVIMIENTO.TXT_GLOSA,
															CON.ASIENTO_MOVIMIENTO.CAN_DEBE_MN,
															CON.ASIENTO_MOVIMIENTO.CAN_DEBE_ME,
															CON.ASIENTO_MOVIMIENTO.CAN_HABER_MN,
															CON.ASIENTO_MOVIMIENTO.CAN_HABER_ME,
															CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE,
															CMP.DOCUMENTO_CTBLE.COD_CATEGORIA_TIPO_DOC,
															CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_TIPO_DOC,
															CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_MONEDA,
															CMP.DOCUMENTO_CTBLE.NRO_SERIE,
															CMP.DOCUMENTO_CTBLE.FEC_EMISION,
															CMP.DOCUMENTO_CTBLE.CAN_TOTAL,
															(CMP.DOCUMENTO_CTBLE.CAN_TOTAL*CMP.DOCUMENTO_CTBLE.CAN_TIPO_CAMBIO) as CAN_TOTAL_SOLES,
															CMP.DOCUMENTO_CTBLE.CAN_TIPO_CAMBIO,
															CMP.DOCUMENTO_CTBLE.NRO_DOC'))
										->get();
	    return $listadetallemovimientos;

	}



	private function cb_cod_egreso_ingreso($cuenta_referencia)
	{
		$cod_operacion_caja = '';
		//12: Cliente(ingreso)
		if($cuenta_referencia == '12'){
			$cod_operacion_caja = 'OPC0000000000001';
		}
		//42: Proveedor(egreso)
		if($cuenta_referencia == '42'){
			$cod_operacion_caja = 'OPC0000000000002';
		}
	    return $cod_operacion_caja;
	}


	private function cb_cod_pago_cobro($cuenta_referencia)
	{
		$cod_pago_cobro = '';
		//12: Cliente(ingreso)
		if($cuenta_referencia == '12'){
			$cod_pago_cobro = 'EPC0000000000002';
		}
		//42: Proveedor(egreso)
		if($cuenta_referencia == '42'){
			$cod_pago_cobro = 'EPC0000000000001';
		}
	    return $cod_pago_cobro;
	}

}