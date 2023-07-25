<?php

namespace App\Http\Controllers;

use App\Modelos\WEBActivoFijo;
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\WEBDepreciacionActivoFijo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;


class ImportadorAsientosActivosFijos extends Controller
{
    //

    public function index()
    {
        $asientos_af = $this->leerCSV();

        $estructura_asientos_af =  $this->generaEstructura($asientos_af);

        foreach ($estructura_asientos_af  as $nrodoc => $linea_asiento) {

            $this->generaAsiento($nrodoc,$linea_asiento);
            
        }

    }

    public function leerCSV(){

        $asientos = [];

        if (($archivo = fopen(storage_path() . "/asientos_activos_fijos.csv", "r")) !== FALSE) {

            while (($data = fgetcsv($archivo)) !== FALSE) {
                $asientos[] = $data;
            }

            fclose($archivo);
        }

        return $asientos;
    }

    public function generaEstructura($asientos){

        $estructura_asientos = [];

        foreach ($asientos as $linea_asiento) {
            $linea_asiento_det = explode(";",$linea_asiento[0]);
            $estructura_asientos[$linea_asiento_det[7]][0] = $linea_asiento_det[0];
            $estructura_asientos[$linea_asiento_det[7]][1] = $linea_asiento_det[1]; 
            $estructura_asientos[$linea_asiento_det[7]][2] = $linea_asiento_det[2]; 
            $estructura_asientos[$linea_asiento_det[7]][3] = $linea_asiento_det[3]; 
            $estructura_asientos[$linea_asiento_det[7]][4] = $linea_asiento_det[4]; 
            $estructura_asientos[$linea_asiento_det[7]][5] = $linea_asiento_det[6]; 
            $estructura_asientos[$linea_asiento_det[7]][6] = $linea_asiento_det[7];
            $estructura_asientos[$linea_asiento_det[7]][7] = $linea_asiento_det[8]; 
            $estructura_asientos[$linea_asiento_det[7]][8] = $linea_asiento_det[9]; 
            $estructura_asientos[$linea_asiento_det[7]][9] = $linea_asiento_det[10]; 
            
            $indice = isset($estructura_asientos[$linea_asiento_det[7]][10]) ? count($estructura_asientos[$linea_asiento_det[7]][10]) : 0;
            
            $estructura_asientos[$linea_asiento_det[7]][10][$indice][] = $linea_asiento_det[5]; 
            $estructura_asientos[$linea_asiento_det[7]][10][$indice][] = $linea_asiento_det[11]; 
            $estructura_asientos[$linea_asiento_det[7]][10][$indice][] = $linea_asiento_det[12]; 
            $estructura_asientos[$linea_asiento_det[7]][10][$indice][] = $linea_asiento_det[13]; 

            $estructura_asientos[$linea_asiento_det[7]][11] = isset($estructura_asientos[$linea_asiento_det[7]][11]) ? $estructura_asientos[$linea_asiento_det[7]][11] + $linea_asiento_det[12] : $linea_asiento_det[12]; 
            $estructura_asientos[$linea_asiento_det[7]][12] = isset($estructura_asientos[$linea_asiento_det[7]][12]) ? $estructura_asientos[$linea_asiento_det[7]][12] + $linea_asiento_det[13] : $linea_asiento_det[13]; 

        }

        return $estructura_asientos;

    }

    public function buscarPeriodo($empresa_id, $anio, $mes){
        $periodo = DB::table('CON.PERIODO')
                       ->select('COD_PERIODO')
                       ->where('CON.PERIODO.COD_MES','=', $mes) // TEST date("m")
                       ->where('CON.PERIODO.COD_ANIO','=', $anio)
                       ->where('CON.PERIODO.COD_EMPR','=', $empresa_id)
                       //->where('CON.PERIODO.COD_CENTRO','=', $centro_id)
                       ->first();        
        return $periodo->COD_PERIODO;
    }

    public function generaAsiento($nrodoc,$linea_asiento){

        $empresa_id = Session::get('empresas')->COD_EMPR;
        $centro_id = Session::get('centros')->COD_CENTRO;
        $txt_glosa = 'ASIENTO DEPRECIACIÓN '.$nrodoc;
        $cod_periodo = $this->buscarPeriodo($empresa_id, $linea_asiento[0], $linea_asiento[1]);
        $tipo_asiento = 'TAS0000000000007';
        $txt_tipo_asiento = 'DIARIO';
        $cod_categoria_estado_asiento = 'IACHTE0000000025'; //'IACHTE0000000010';
        $txt_categoria_estado_asiento = 'CONFIRMADO'; //'CUADRADO';
        $cod_moneda = 'MON0000000000001';
        $txt_categoria_moneda = 'SOLES';
        $tipo_cambio = $this->obtenerTipoCambio();
        $total_debe = '';
        $total_haber = '';
        $ind_extorno = 0;
        $ind_anulado = 0;
        $txt_tipo_referencia = 'DEPRECIACION';
        $cod_estado = 1;
        $cod_categoria_tipo_documento_ref = '';
        $nro_serie_Ref = '';
        $nro_doc_ref = '';
        $fec_vencimiento = '';
        $ind_afecto = 0;            
        $fec_asiento = date("Ymd");
        $fec_creacion = date("Ymd H:i:s");        

        $fec_asiento = date("Ymd",strtotime((int)$linea_asiento[0].'-'.(int)$linea_asiento[1].'-'.(int)$linea_asiento[3]));

        $nro_asiento = $this->funciones->getCreateINumeroAsiento('WEB.asientos');

        $asiento_id = $this->funciones->getCreateIdAsientoContable('WEB.asientos');
        
        $asiento = new WEBAsiento();
        $asiento->COD_ASIENTO = $asiento_id;
        $asiento->COD_EMPR = $empresa_id;
        $asiento->COD_CENTRO = $centro_id;
        $asiento->COD_PERIODO = $cod_periodo;
        $asiento->COD_CATEGORIA_TIPO_ASIENTO = $tipo_asiento;
        $asiento->TXT_CATEGORIA_TIPO_ASIENTO = $txt_tipo_asiento;
        $asiento->NRO_ASIENTO = $nro_asiento;
        $asiento->FEC_ASIENTO = $fec_asiento;        
        $asiento->TXT_GLOSA = $txt_glosa;
        $asiento->COD_CATEGORIA_ESTADO_ASIENTO = $cod_categoria_estado_asiento;
        $asiento->TXT_CATEGORIA_ESTADO_ASIENTO = $txt_categoria_estado_asiento;
        $asiento->COD_CATEGORIA_MONEDA = $cod_moneda;
        $asiento->TXT_CATEGORIA_MONEDA = $txt_categoria_moneda;
        $asiento->CAN_TIPO_CAMBIO = $tipo_cambio;
        $asiento->CAN_TOTAL_DEBE = $linea_asiento[11];
        $asiento->CAN_TOTAL_HABER = $linea_asiento[12];
        $asiento->IND_EXTORNO = $ind_extorno;
        $asiento->IND_ANULADO = $ind_anulado;
        $asiento->TXT_TIPO_REFERENCIA = $txt_tipo_referencia;
        $asiento->COD_USUARIO_CREA_AUD = '1CIX00000001';
        $asiento->FEC_USUARIO_CREA_AUD = $fec_creacion;
        $asiento->COD_USUARIO_MODIF_AUD = '1CIX00000001';
        $asiento->FEC_USUARIO_MODIF_AUD = $fec_creacion;
        $asiento->COD_ESTADO = $cod_estado;
        $asiento->IND_AFECTO = $ind_afecto;
        $asiento->COND_ASIENTO = 'IMPORTACION';
        $asiento->save();        
        
        foreach ($linea_asiento[10] as $iddetalle => $detalle) {

            $datos_cuenta = DB::table('WEB.cuentacontables')->select('id', 'nro_cuenta', 'cuenta_contable_transferencia_debe', 'cuenta_contable_transferencia_haber')->where('nro_cuenta','=',$detalle[0])->where('empresa_id','=','EMP0000000000007')->where('anio','=',$linea_asiento[0])->first(); //->where('empresa_id','=',$empresa_id)  // CHANGE Dejar la empresa de la sesion
            
            $asientoMovimiento = new WEBAsientoMovimiento();
            $asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientomovimientos');
            $asientoMovimiento->COD_EMPR = $empresa_id;
            $asientoMovimiento->COD_CENTRO = $centro_id;                
            $asientoMovimiento->COD_ASIENTO = $asiento_id;
            $asientoMovimiento->COD_CUENTA_CONTABLE = $datos_cuenta->id;
            $asientoMovimiento->TXT_CUENTA_CONTABLE = $datos_cuenta->nro_cuenta;            
            $asientoMovimiento->TXT_GLOSA = $detalle[1];   
            $asientoMovimiento->CAN_DEBE_MN = $detalle[2];            
            $asientoMovimiento->CAN_HABER_MN = $detalle[3]; 
            $asientoMovimiento->CAN_DEBE_ME = $detalle[2]/$tipo_cambio; 
            $asientoMovimiento->CAN_HABER_ME = $detalle[3]/$tipo_cambio; 
            $asientoMovimiento->NRO_LINEA = $iddetalle + 1; 
            $asientoMovimiento->IND_EXTORNO = 0; 
            $asientoMovimiento->COD_USUARIO_CREA_AUD = '1CIX00000001';
            $asientoMovimiento->FEC_USUARIO_CREA_AUD = $fec_creacion;
            $asientoMovimiento->COD_USUARIO_MODIF_AUD = '1CIX00000001';
            $asientoMovimiento->FEC_USUARIO_MODIF_AUD = $fec_creacion;
            $asientoMovimiento->COD_ESTADO = $cod_estado;
            $asientoMovimiento->save();
    
        }

        //dd($asiento);

    }

    public function obtenerTipoCambio(){
        $tipo_cambio = DB::table('CMP.TIPO_CAMBIO')
                        ->select('CAN_VENTA')
                        //->where('CMP.TIPO_CAMBIO.FEC_CAMBIO','=', $fec_creacion) // TEST 
                        ->where('CMP.TIPO_CAMBIO.COD_CATEGORIA_MONEDA_ORIG','=', 'MON0000000000001')
                        ->where('CMP.TIPO_CAMBIO.COD_CATEGORIA_MONEDA_DEST','=', 'MON0000000000002')
                        ->first();                          
        return $tipo_cambio->CAN_VENTA;
    }
}
