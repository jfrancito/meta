<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBViewMigrarCompras;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;

use App\Modelos\CMPReferecenciaAsoc;
use App\Modelos\CMPOrden;
use App\Modelos\STDEmpresa;
use App\Modelos\WEBCuentaDetraccion;
use App\Modelos\CMPCategoria;
use App\Modelos\WEBCuentaContable;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait ReciboHonorarioTraits
{


    private function rh_lista_compras_asiento($anio,$periodo_id,$empresa_id,$serie,$documento)
    {

        $lista_compras          =   WEBAsiento::join('CMP.DOCUMENTO_CTBLE', function ($join) use ($periodo_id,$empresa_id){
                                        $join->on('WEB.asientos.TXT_REFERENCIA', '=', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE');
                                    })
                                    ->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
                                    ->where('WEB.asientos.COD_EMPR','=',$empresa_id)
                                    ->where('WEB.asientos.COD_OBJETO_ORIGEN','=','DIARIO-RECIBOHONORARIO')
                                    ->NroSerie($serie)
                                    ->NroDocumento($documento)
                                    ->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000007')
                                    ->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000032')
                                    ->select(DB::raw('WEB.asientos.*'))
                                    ->orderby('CMP.DOCUMENTO_CTBLE.FEC_EMISION','asc')
                                    ->get();

        return $lista_compras;

    }

    private function rh_lista_compras_terminado_asiento($anio,$periodo_id,$empresa_id,$serie,$documento)
    {

        $lista_compras      =   WEBAsiento::join('CMP.DOCUMENTO_CTBLE', function ($join) use ($periodo_id,$empresa_id){
                                        $join->on('WEB.asientos.TXT_REFERENCIA', '=', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE');
                                    })
                                    ->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
                                    ->where('WEB.asientos.COD_EMPR','=',$empresa_id)
                                    ->where('WEB.asientos.COD_OBJETO_ORIGEN','=','DIARIO-RECIBOHONORARIO')
                                    ->NroSerie($serie)
                                    ->NroDocumento($documento)
                                    ->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=','TAS0000000000007')
                                    ->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
                                    ->select(DB::raw('WEB.asientos.*'))
                                    ->orderby('FEC_USUARIO_MODIF_AUD','asc')
                                    ->get();

                

        return $lista_compras;

    }





}