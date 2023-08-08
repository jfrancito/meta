<?php

namespace App\Http\Controllers;

use App\Modelos\CONItemMovimiento;
use App\Modelos\CONPeriodo;
use App\Traits\AsientoModeloTraits;
use App\Traits\GeneralesTraits;
use App\Traits\ItfTraits;
use App\Traits\PlanContableTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;

class ItfController extends Controller
{
    //
    use GeneralesTraits;
    use AsientoModeloTraits;
    use PlanContableTraits;
    use ItfTraits;

    public function actionGuardarItfCuentaContable($idopcion, Request $request)
    {

        $asientos = json_decode($request['asientos_itf'], false);
        foreach ($asientos as $index_asiento => $asiento) {

            $cabecera = $asiento->cabecera;
            $detalle = $asiento->detalle;
            $periodo_id = $request['periodog_id'];
            $empresa_id = Session::get('empresas_meta')->COD_EMPR;
            $centro_id = 'CEN0000000000001';
            $periodo = CONPeriodo::where('COD_PERIODO', '=', $periodo_id)->first();
            $tipo_asiento_id = 'TAS0000000000007';
            $tipo_referencia = 'TAS0000000000007';

            //CABECERA
            foreach ($cabecera as $index => $item) {

                $IND_TIPO_OPERACION = 'I';
                $COD_ASIENTO = '';
                $COD_EMPR = $empresa_id;
                $COD_CENTRO = $centro_id;
                $COD_PERIODO = $periodo->COD_PERIODO;
                $COD_CATEGORIA_TIPO_ASIENTO = 'TAS0000000000007'; //'TAS0000000000002';
                $TXT_CATEGORIA_TIPO_ASIENTO = 'DIARIO'; //'BANCOS';
                $NRO_ASIENTO = '';
                $FEC_ASIENTO = substr($periodo->FEC_FIN, 0, 10);
                //dd($FEC_ASIENTO);
                $TXT_GLOSA = $item->glosa;

                $COD_CATEGORIA_ESTADO_ASIENTO = 'IACHTE0000000025'; //'IACHTE0000000025';
                $TXT_CATEGORIA_ESTADO_ASIENTO = 'CONFIRMADO'; //'CONFIRMADO';
                $COD_CATEGORIA_MONEDA = $item->moneda_id;
                $TXT_CATEGORIA_MONEDA = $item->moneda;
                $CAN_TIPO_CAMBIO = $item->tipo_cambio;
                $CAN_TOTAL_DEBE = $item->total_debe;
                $CAN_TOTAL_HABER = $item->total_haber;
                $COD_ASIENTO_EXTORNO = '';
                $COD_ASIENTO_EXTORNADO = '';
                $IND_EXTORNO = '0';

                $COD_ASIENTO_MODELO = '';
                $TXT_TIPO_REFERENCIA = 'ITF'; // $item->tipo_referencia;
                $TXT_REFERENCIA = $index_asiento;
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

                $asiento_id = $this->gn_encontrar_cod_asiento($empresa_id, $centro_id,
                    $periodo_id, $tipo_asiento_id, $item->tipo_referencia);

                $anular_asiento = $this->itf_anular_asiento($asiento_id,
                    Session::get('usuario_meta')->name, $this->fechaactual);

                $asientocontable = $this->gn_crear_asiento_contable($IND_TIPO_OPERACION,
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

            foreach ($detalle as $index => $item) {

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

                $detalle = $this->gn_crear_detalle_asiento_contable($IND_TIPO_OPERACION,
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

        }
        Session::flash('periodo_id_confirmar', $periodo->COD_PERIODO);
        return Redirect::to('/gestion-pago-itf/' . $idopcion)->with('bienhecho', 'Registro cuenta contable exitoso');

    }

    public function actionAjaxModalConfiguracionItfCuentaContable(Request $request)
    {

        $datastring = $request['datastring'];
        $data_tabla = $request['data_tabla'];
        $periodo_registrado = $request['periodo_registrado'];
        $periodo = CONPeriodo::where('COD_PERIODO', '=', $periodo_registrado)->first();
        $empresa_id = Session::get('empresas_meta')->COD_EMPR;
        $data_archivo = $request['data_archivo'];
        $idopcion = $request['idopcion'];

        $movimiento = CONItemMovimiento::where('COD_EMPR', $empresa_id)->where('TXT_NOMBRE', 'PAGO DE ITF')->first();
        $movimiento_id = $movimiento['COD_ITEM_MOVIMIENTO'];

        $lista_itf = $this->lista_itf($empresa_id, $periodo_registrado, $movimiento_id);

        $glosa = '';
        $tipo_referencia = '';

        $asientos_itf = array();

        foreach ($request['datastring'] as $registro) {

            $monto_total = $this->itf_monto_total_asiento($lista_itf, $registro['documento_id']); //, $array_asoc

            if ($data_archivo == 'agregaritf') {
                $glosa = 'EGRESO DIRECTO POR: PAGO DE ITF' . ' / ' . $registro['cuenta_bancaria'];
                $tipo_referencia = 'ITF';
            }

            $moneda_id = 'MON0000000000001';
            $moneda = 'SOLES';



            $fecha_cambio = date_format(date_create(substr($periodo->FEC_FIN, 0, 10)), 'Ymd');
            $tipo_cambio = $this->gn_tipo_cambio($fecha_cambio);

            $array_asiento_modelo = $this->itf_asiento_modelo($data_archivo);


            $cabecera = $this->itf_cabecera_asiento($periodo, $empresa_id, $monto_total, $glosa, $moneda_id, $moneda, $tipo_cambio, $tipo_referencia);            
            $detalle = $this->itf_detalle_asiento($array_asiento_modelo, $periodo, $empresa_id, $moneda_id, $moneda, $monto_total, $tipo_cambio);



            $asientos_itf[$registro['documento_id']]['cabecera'] = $cabecera;
            $asientos_itf[$registro['documento_id']]['detalle'] = $detalle;

        }



        $funcion = $this;

        return View::make('itf/modal/ajax/amitfcc',
            [
                'asientos_itf' => $asientos_itf,
                'periodo' => $periodo,
                'funcion' => $funcion,
                'idopcion' => $idopcion,
                'ajax' => true,
            ]);
    }

    public function actionListarItf($idopcion)
    {
        $validarurl = $this->funciones->getUrl($idopcion, 'Ver');
        if ($validarurl != 'true') {return $validarurl;}

        View::share('titulo', 'Lista de Pagos ITF');

        $empresa_id = Session::get('empresas_meta')->COD_EMPR;

        if (Session::has('periodo_id_confirmar')) {
            $sel_periodo = Session::get('periodo_id_confirmar');
        } else {
            $sel_periodo = '';
        }

        $movimiento = CONItemMovimiento::where('COD_EMPR', $empresa_id)->where('TXT_NOMBRE', 'PAGO DE ITF')->first();
        $movimiento_id = $movimiento['COD_ITEM_MOVIMIENTO'];

        $lista_itf = $this->lista_itf($empresa_id, $sel_periodo, $movimiento_id);

        $anio = $this->anio;
        $array_anio_pc = $this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
        $combo_anio_pc = $this->gn_generacion_combo_array('Seleccione año', '', $array_anio_pc);
        $combo_periodo = $this->gn_combo_periodo_xanio_xempresa($anio, Session::get('empresas_meta')->COD_EMPR, '', 'Seleccione periodo');
        $funcion = $this;

        return View::make('itf/listaitf',
            [
                'lista_itf' => $lista_itf,
                'combo_anio_pc' => $combo_anio_pc,
                'combo_periodo' => $combo_periodo,
                'anio' => $anio,
                'sel_periodo' => $sel_periodo,
                'periodo_id' => $sel_periodo,
                'idopcion' => $idopcion,
                'funcion' => $funcion,
            ]);
    }

    public function actionAjaxRegistroItf(Request $request)
    {

        $anio = $request['anio'];
        $periodo_id = $request['periodo_id'];
        $empresa_id = Session::get('empresas_meta')->COD_EMPR;

        $movimiento = CONItemMovimiento::where('COD_EMPR', $empresa_id)->where('TXT_NOMBRE', 'PAGO DE ITF')->first();
        $movimiento_id = $movimiento['COD_ITEM_MOVIMIENTO'];

        $lista_itf = $this->lista_itf($empresa_id, $periodo_id, $movimiento_id);

        $funcion = $this;

        return View::make('itf/ajax/alistaitf',
            [
                'lista_itf' => $lista_itf,
                'funcion' => $funcion,
                'periodo_id' => $periodo_id,
                'ajax' => true,
            ]);
    }
}
