<?php

namespace App\Traits;

use App\Modelos\CONPeriodo;
use App\Modelos\TESOperacionCaja;
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\WEBCuentaContable;
use Illuminate\Support\Facades\DB;
use ZipArchive;

trait InteresesPrestamoTraits
{

    private function intereses_prestamo_anular_asiento($asiento_id, $usuario_id, $fecha)
    {

        WEBAsiento::where('COD_ASIENTO', '=', $asiento_id)
            ->update(['COD_CATEGORIA_ESTADO_ASIENTO' => 'IACHTE0000000024',
                'TXT_CATEGORIA_ESTADO_ASIENTO' => 'EXTORNADO',
                'COD_USUARIO_MODIF_AUD' => $usuario_id,
                'FEC_USUARIO_MODIF_AUD' => $fecha,
                'COD_ESTADO' => '0',
            ]);

        WEBAsientoMovimiento::where('COD_ASIENTO', '=', $asiento_id)
            ->update(['COD_USUARIO_MODIF_AUD' => $usuario_id,
                'FEC_USUARIO_MODIF_AUD' => $fecha,
                'COD_ESTADO' => '0',
            ]);

        return true;

    }

    private function lista_intereses_prestamo($empresa_id, $periodo_id, $movimiento_id, $banco_id = '')
    {
        $periodo_anio = CONPeriodo::where('COD_PERIODO', '=', $periodo_id)->select('COD_ANIO')->get()->toArray();

        $periodo_mes = CONPeriodo::where('COD_PERIODO', '=', $periodo_id)->select('COD_MES')->get()->toArray();

        $asientos_registrados = WEBAsiento::where('COD_CATEGORIA_TIPO_ASIENTO', 'TAS0000000000007')
            ->where('COD_CATEGORIA_ESTADO_ASIENTO', 'IACHTE0000000025') //TAS0000000000002
            ->where('COD_PERIODO', $periodo_id)
            ->pluck('TXT_REFERENCIA')
            ->toArray();

        $anio = isset($periodo_anio[0]['COD_ANIO']) ? $periodo_anio[0]['COD_ANIO'] : '';
        $mes = isset($periodo_mes[0]['COD_MES']) ? $periodo_mes[0]['COD_MES'] : '';

        $intereses_prestamo = TESOperacionCaja::where('TES.OPERACION_CAJA.COD_EMPR', $empresa_id)
        ->whereNotIn('TES.OPERACION_CAJA.COD_CAJA_BANCO', $asientos_registrados)
            ->join('TES.CAJA_BANCO', 'TES.OPERACION_CAJA.COD_CAJA_BANCO', '=', 'TES.CAJA_BANCO.COD_CAJA_BANCO')
            ->join('CON.PAGARE', 'TES.OPERACION_CAJA.TXT_REFERENCIA', '=', 'CON.PAGARE.COD_PAGARE')
            ->join('CON.PAGARE_DETALLE', 'CON.PAGARE.COD_PAGARE', '=', 'CON.PAGARE_DETALLE.COD_PAGARE')
            ->where('CON.PAGARE.COD_EMPR_BANCO', 'LIKE', '%' . $banco_id . '%')
            ->where('CON.PAGARE.COD_CATEGORIA_TIPO_PAGARE', '=', 'TPG0000000000001')
            ->where('CON.PAGARE.COD_GASTO_FUNCION', '<>', '')
            ->where(DB::raw("YEAR(CON.PAGARE_DETALLE.FEC_VENCIMIENTO)"), 'LIKE', '%' . $anio . '%')
            ->where(DB::raw("MONTH(CON.PAGARE_DETALLE.FEC_VENCIMIENTO)"), 'LIKE', '%' . $mes . '%')
            ->orderBy('TES.OPERACION_CAJA.FEC_OPERACION')
            ->get();

        return $intereses_prestamo;
    }

    public function intereses_prestamo_monto_total_asiento($lista_intereses_prestamo, $cod_pagare, $cod_detalle_pagare)
    {

        $monto = 0.0000;
        foreach ($lista_intereses_prestamo as $index => $item) {
            if ($item['COD_PAGARE'] == $cod_pagare && $item['COD_DETALLE'] == $cod_detalle_pagare) {
                $pagare = DB::table('CON.PAGARE')->where('COD_PAGARE', '=', $cod_pagare)->select('CAN_CUOTAS')->first();
                $pagare_detalle = DB::table('CON.PAGARE_DETALLE')->where('COD_PAGARE', '=', $cod_pagare)->where('COD_DETALLE', '=', $cod_detalle_pagare)->first();
                $mes = date("m", strtotime($pagare_detalle->FEC_VENCIMIENTO));
                $anio = date("Y", strtotime($pagare_detalle->FEC_VENCIMIENTO));
                $dia_vencimiento = date("d", strtotime($pagare_detalle->FEC_VENCIMIENTO));
                $cuota_actual = substr($pagare_detalle->COD_DETALLE, -3, 3);
                $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, date("Y"));
                $mes_siguiente = $mes == 12 ? 1 : $mes + 1;
                $anio = $mes == 12 ? $anio + 1 : $anio;
                if ((int) $cuota_actual < (int) $pagare->CAN_CUOTAS) {
                    $pagare_detalle_siguiente = DB::table('CON.PAGARE_DETALLE')->where('COD_PAGARE', '=', $cod_pagare)->where(DB::raw("MONTH(CON.PAGARE_DETALLE.FEC_VENCIMIENTO)"), 'LIKE', '%' . $mes_siguiente . '%')->where(DB::raw("YEAR(CON.PAGARE_DETALLE.FEC_VENCIMIENTO)"), 'LIKE', '%' . $anio . '%')->first();
                    $dias_mes_siguiente = cal_days_in_month(CAL_GREGORIAN, $mes_siguiente, date("Y"));
                    $dia_vencimiento_siguiente = date("d", strtotime($pagare_detalle_siguiente->FEC_VENCIMIENTO));
                }
                if ((int) $cuota_actual == 1) { //$pagare_detalle->COD_DETALLE == 'CTA0000000000001'
                    $interes_mes_actual = $pagare_detalle->CAN_INTERES_MN;
                } else {
                    $interes_mes_actual = ($pagare_detalle->CAN_INTERES_MN / $dias_mes) * ($dias_mes - $dia_vencimiento + 1);
                }
                if ($pagare->CAN_CUOTAS == $cuota_actual) {
                    $interes_mes_siguiente = 0;
                } else {
                    $interes_mes_siguiente = ($pagare_detalle_siguiente->CAN_INTERES_MN / $dias_mes_siguiente) * ($dia_vencimiento_siguiente - 1);
                }
                $interes_calculado = $interes_mes_actual + $interes_mes_siguiente;
                $monto = $monto + $interes_calculado;
            }
        }

        return number_format($monto, 4, '.', '');

    }

    public function intereses_prestamo_asiento_modelo($data_archivo, $nro_linea, $cod_banco, $empresa_id)
    {

        $array_detalle_asiento = array();

        if ($data_archivo == 'agregar-intereses-prestamo') {

            switch ($cod_banco) {
                case 'IACHEM0000001037':
                    $cuenta = '673111';
                    break;
                case 'IACHEM0000001039':
                    $cuenta = '673112';
                    break;
                case 'IACHEM0000001038':
                    $cuenta = '673113';
                    break;
                case 'IACHEM0000001041':
                    $cuenta = '673114';
                    break;
                case 'IACHEM0000001043':
                    $cuenta = '673115';
                    break;
                case 'IACHEM0000001040':
                    $cuenta = '673116';
                    break;

                default:
                    $cuenta = '';
                    break;
            }

            $array_nuevo_asiento = array(
                "cuenta_nrocuenta" => $cuenta,
                "d_h" => 'D',
                "linea" => $nro_linea,
            );
            array_push($array_detalle_asiento, $array_nuevo_asiento);

            switch ($cod_banco) {
                case 'IACHEM0000001037':
                    $cuenta = '455111';
                    break;
                case 'IACHEM0000001039':
                    $cuenta = '455112';
                    break;
                case 'IACHEM0000001038':
                    $cuenta = '455113';
                    break;
                case 'IACHEM0000001041':
                    $cuenta = '455114';
                    break;
                case 'IACHEM0000001043':
                    $cuenta = '455115';
                    break;
                case 'IACHEM0000001040':
                    $cuenta = '455116';
                    break;

                default:
                    $cuenta = '';
                    break;
            }

            $array_nuevo_asiento = array(
                "cuenta_nrocuenta" => $cuenta,
                "d_h" => 'H',
                "linea" => $nro_linea + 1,
            );
            array_push($array_detalle_asiento, $array_nuevo_asiento);

            $array_nuevo_asiento = array(
                "cuenta_nrocuenta" => '971311',
                "d_h" => 'D',
                "linea" => $nro_linea + 2,
            );
            array_push($array_detalle_asiento, $array_nuevo_asiento);

            $array_nuevo_asiento = array(
                "cuenta_nrocuenta" => '791101',
                "d_h" => 'H',
                "linea" => $nro_linea + 3,
            );
            array_push($array_detalle_asiento, $array_nuevo_asiento);

        }

        return $array_detalle_asiento;

    }

    public function intereses_prestamo_cabecera_asiento($periodo, $empresa_id, $monto_total, $glosa, $moneda_id, $moneda, $tipo_cambio, $tipo_referencia)
    {

        $array_detalle_asiento = array();

        $array_nuevo_asiento = array();
        $array_nuevo_asiento = array(
            "periodo_id" => $periodo->COD_PERIODO,
            "nombre_periodo" => $periodo->TXT_NOMBRE,
            "fecha" => substr($periodo->FEC_FIN, 0, 10),
            "empresa_id" => $empresa_id,
            "glosa" => $glosa,
            "tipo_referencia" => $tipo_referencia,
            "tipo_cambio" => $tipo_cambio->CAN_COMPRA, //CAN_COMPRA_SBS
            "moneda_id" => $moneda_id,
            "moneda" => $moneda,
            "total_debe" => $monto_total,
            "total_haber" => $monto_total,
        );
        array_push($array_detalle_asiento, $array_nuevo_asiento);
        return $array_detalle_asiento;

    }

    public function intereses_prestamo_detalle_asiento($array_asiento_modelo, $periodo, $empresa_id, $moneda_id, $moneda, $monto_total, $tipo_cambio)
    {

        $array_detalle_asiento = array();

        foreach ($array_asiento_modelo as $index => $item) {
            // Del
            $empresa_id = 'IACHEM0000001339';
            // Del
            $cuentacontable = WEBCuentaContable::where('empresa_id', '=', $empresa_id)
                ->where('anio', '=', $periodo->COD_ANIO)
                ->where('nro_cuenta', '=', $item['cuenta_nrocuenta'])
                ->where('activo', '=', 1)
                ->first();

            if ($item['d_h'] == 'D') {
                $monto_total_debe = $monto_total;
                $monto_total_haber = 0;

                $monto_total_dolar_debe = $monto_total / $tipo_cambio->CAN_COMPRA; //CAN_COMPRA_SBS
                $monto_total_dola_haber = 0;

            } else {
                $monto_total_debe = 0;
                $monto_total_haber = $monto_total;

                $monto_total_dolar_debe = 0;
                $monto_total_dola_haber = $monto_total / $tipo_cambio->CAN_COMPRA; //CAN_COMPRA_SBS

            }

            $array_nuevo_asiento = array();
            $array_nuevo_asiento = array(
                "linea" => $item['linea'],
                "cuenta_id" => $cuentacontable->id,
                "cuenta_nrocuenta" => $cuentacontable->nro_cuenta,
                "glosa" => $cuentacontable->nombre,
                "fecha" => substr($periodo->FEC_FIN, 0, 10),
                "empresa_id" => $empresa_id,
                "moneda_id" => $moneda_id,
                "moneda" => $moneda,
                "total_debe" => $monto_total_debe,
                "total_haber" => $monto_total_haber,
                "total_debe_dolar" => $monto_total_dolar_debe,
                "total_haber_dolar" => $monto_total_dola_haber,

            );

            array_push($array_detalle_asiento, $array_nuevo_asiento);

        }

        return $array_detalle_asiento;

    }

    public function lista_detalle_prestamo($cod_pagare)
    {

        $detalle_prestamo = DB::table('CON.PAGARE')
            ->join('CON.PAGARE_DETALLE', 'CON.PAGARE.COD_PAGARE', '=', 'CON.PAGARE_DETALLE.COD_PAGARE')
            ->where('CON.PAGARE.COD_PAGARE', '=', $cod_pagare)
            ->select('CON.PAGARE.COD_PAGARE', 'CON.PAGARE.NRO_PAGARE', 'CON.PAGARE.TXT_EMPR_BANCO', 'CON.PAGARE_DETALLE.FEC_CUOTA',
                'CON.PAGARE_DETALLE.FEC_VENCIMIENTO', 'CON.PAGARE.CAN_MONTO_MN', 'CON.PAGARE_DETALLE.CAN_CUOTA_MN',
                'CON.PAGARE_DETALLE.CAN_INT_MENSUAL_MN', 'CON.PAGARE_DETALLE.COD_DETALLE')
            ->get()
            ->ToArray();
        $lista_detalle = array();
        $i = 0;
        foreach ($detalle_prestamo as $detalle) {
            $lista_detalle[$i]['NRO_PAGARE'] = $detalle->NRO_PAGARE;
            $lista_detalle[$i]['TXT_EMPR_BANCO'] = $detalle->TXT_EMPR_BANCO;
            $lista_detalle[$i]['FEC_CUOTA'] = $detalle->FEC_CUOTA;
            $lista_detalle[$i]['FEC_VENCIMIENTO'] = $detalle->FEC_VENCIMIENTO;
            $lista_detalle[$i]['CAN_MONTO_MN'] = $detalle->CAN_MONTO_MN;
            $lista_detalle[$i]['CAN_CUOTA_MN'] = $detalle->CAN_CUOTA_MN;
            $lista_detalle[$i]['CAN_INT_MENSUAL_MN'] = $detalle->CAN_INT_MENSUAL_MN;
            $interes = $this->interes_a_pagar($detalle->COD_PAGARE, $detalle->COD_DETALLE, $i + 1);
            $lista_detalle[$i]['INTERES_MES_SIGUIENTE'] = $interes['interes_mes_siguiente'];
            $lista_detalle[$i]['INTERES_MES_ACTUAL'] = $interes['interes_mes_actual'];
            $lista_detalle[$i]['INTERES_A_PAGAR'] = $interes['monto'];
            $i++;
        }
        return $lista_detalle;
    }

    public function interes_a_pagar($cod_pagare, $cod_detalle_pagare, $cuota_actual)
    {
        $pagare = DB::table('CON.PAGARE')->where('COD_PAGARE', '=', $cod_pagare)->select('CAN_CUOTAS')->first();
        $pagare_detalle = DB::table('CON.PAGARE_DETALLE')->where('COD_PAGARE', '=', $cod_pagare)->where('COD_DETALLE', '=', $cod_detalle_pagare)->first();
        $mes = date("m", strtotime($pagare_detalle->FEC_VENCIMIENTO));
        $anio = date("Y", strtotime($pagare_detalle->FEC_VENCIMIENTO));
        $dia_vencimiento = date("d", strtotime($pagare_detalle->FEC_VENCIMIENTO));
        $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, date("Y"));
        $monto = 0;

        $mes_siguiente = $mes == 12 ? 1 : $mes + 1;
        $anio = $mes == 12 ? $anio + 1 : $anio;
        if ((int) $cuota_actual < (int) $pagare->CAN_CUOTAS) {
            $pagare_detalle_siguiente = DB::table('CON.PAGARE_DETALLE')->where('COD_PAGARE', '=', $cod_pagare)->where(DB::raw("MONTH(CON.PAGARE_DETALLE.FEC_VENCIMIENTO)"), 'LIKE', '%' . $mes_siguiente . '%')->where(DB::raw("YEAR(CON.PAGARE_DETALLE.FEC_VENCIMIENTO)"), 'LIKE', '%' . $anio . '%')->first();
            $dias_mes_siguiente = cal_days_in_month(CAL_GREGORIAN, $mes_siguiente, date("Y"));
            $dia_vencimiento_siguiente = date("d", strtotime($pagare_detalle_siguiente->FEC_VENCIMIENTO));
        }
        if ((int) $cuota_actual == 1) { //$pagare_detalle->COD_DETALLE == 'CTA0000000000001'
            $interes_mes_actual = $pagare_detalle->CAN_INTERES_MN;
        } else {
            $interes_mes_actual = ($pagare_detalle->CAN_INTERES_MN / $dias_mes) * ($dias_mes - $dia_vencimiento + 1);
        }
        if ($pagare->CAN_CUOTAS == $cuota_actual) {
            $interes_mes_siguiente = 0;
        } else {
            $interes_mes_siguiente = ($pagare_detalle_siguiente->CAN_INTERES_MN / $dias_mes_siguiente) * ($dia_vencimiento_siguiente - 1);
        }
        $interes_calculado = $interes_mes_actual + $interes_mes_siguiente;
        $monto = $monto + $interes_calculado;

        $res['interes_mes_siguiente'] = $interes_mes_siguiente;
        $res['interes_mes_actual'] = $interes_mes_actual;
        $res['monto'] = $monto;

        return $res;
    }
}