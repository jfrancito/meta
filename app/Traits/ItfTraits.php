<?php

namespace App\Traits;

use App\Modelos\TESOperacionCaja;
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\WEBCuentaContable;
use ZipArchive;

trait ItfTraits
{

    private function itf_anular_asiento($asiento_id, $usuario_id, $fecha)
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

    private function lista_itf($empresa_id, $periodo_id, $movimiento_id)
    {

        $asientos_registrados = WEBAsiento::where('COD_CATEGORIA_TIPO_ASIENTO', 'TAS0000000000007')
            ->where('COD_CATEGORIA_ESTADO_ASIENTO', 'IACHTE0000000025') //TAS0000000000002
            ->where('COD_PERIODO', $periodo_id)
            ->pluck('TXT_REFERENCIA')
            ->toArray();

        $itf = TESOperacionCaja::where('TES.OPERACION_CAJA.COD_ITEM_MOVIMIENTO', $movimiento_id) //'IICHIM0000000020')
            ->where('TES.OPERACION_CAJA.COD_EMPR', $empresa_id)
            ->where('TES.OPERACION_CAJA.COD_PERIODO_CONCILIA', $periodo_id)
            ->whereNotIn('TES.OPERACION_CAJA.COD_OPERACION_CAJA', $asientos_registrados)
            ->join('TES.CAJA_BANCO', 'TES.OPERACION_CAJA.COD_CAJA_BANCO', '=', 'TES.CAJA_BANCO.COD_CAJA_BANCO')
            ->orderBy('TES.OPERACION_CAJA.FEC_OPERACION')
            ->get();

        return $itf;
    }

    public function itf_monto_total_asiento($lista_itf, $seleccionados)
    {

        $monto = 0.0000;

        foreach ($lista_itf as $index => $item) {
            if ($item['COD_OPERACION_CAJA'] == $seleccionados) {
                $monto = $monto + $item['CAN_HABER_MN'];
            }
        }

        return number_format($monto, 4, '.', '');

    }

    public function itf_asiento_modelo($data_archivo)
    {

        $array_detalle_asiento = array();

        if ($data_archivo == 'agregaritf') {

            $array_nuevo_asiento = array(
                "cuenta_nrocuenta" => '679403',
                "d_h" => 'D',
                "linea" => '1',
            );
            array_push($array_detalle_asiento, $array_nuevo_asiento);

            $array_nuevo_asiento = array(
                "cuenta_nrocuenta" => '421203',
                "d_h" => 'H',
                "linea" => '2',
            );
            array_push($array_detalle_asiento, $array_nuevo_asiento);

            $array_nuevo_asiento = array(
                "cuenta_nrocuenta" => '979402',
                "d_h" => 'D',
                "linea" => '3',
            );
            array_push($array_detalle_asiento, $array_nuevo_asiento);

            $array_nuevo_asiento = array(
                "cuenta_nrocuenta" => '791101',
                "d_h" => 'H',
                "linea" => '4',
            );
            array_push($array_detalle_asiento, $array_nuevo_asiento);

        }

        return $array_detalle_asiento;

    }

    public function itf_cabecera_asiento($periodo, $empresa_id, $monto_total, $glosa, $moneda_id, $moneda, $tipo_cambio, $tipo_referencia)
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
            "tipo_cambio" => $tipo_cambio->CAN_COMPRA_SBS,
            "moneda_id" => $moneda_id,
            "moneda" => $moneda,
            "total_debe" => $monto_total,
            "total_haber" => $monto_total,
        );

        array_push($array_detalle_asiento, $array_nuevo_asiento);
        return $array_detalle_asiento;

    }

    public function itf_detalle_asiento($array_asiento_modelo, $periodo, $empresa_id, $moneda_id, $moneda, $monto_total, $tipo_cambio)
    {

        $array_detalle_asiento = array();

        foreach ($array_asiento_modelo as $index => $item) {

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

}
