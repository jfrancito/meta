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
use App\Modelos\WEBViewMigrarVentaCabecera;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;



trait AlertaTraits
{
	


	private function al_lista_documentos_sin_enviar_agrupado($empresa_id)
	{
		
        $lista_documento    		=   	CMPDocumentoCtble::leftJoin('SGD.USUARIO','SGD.USUARIO.COD_USUARIO','=','CMP.DOCUMENTO_CTBLE.COD_USUARIO_CREA_AUD')
                                			->whereRaw("LEFT(CMP.DOCUMENTO_CTBLE.NRO_SERIE ,1) in ('F','B')")
                                			->whereRaw("YEAR(CMP.DOCUMENTO_CTBLE.FEC_EMISION)>2021")
                                			->whereRaw("CMP.DOCUMENTO_CTBLE.COD_CATEGORIA_TIPO_DOC IN ('TDO0000000000001','TDO0000000000003','TDO0000000000007','TDO0000000000008')")
                                            ->whereIn('CMP.DOCUMENTO_CTBLE.ESTADO_ELEC', ['C',''])
                                			->where('CMP.DOCUMENTO_CTBLE.COD_ESTADO','=',1)
                                			->where('CMP.DOCUMENTO_CTBLE.IND_COMPRA_VENTA','=','V')
                                			->select(DB::raw('CMP.DOCUMENTO_CTBLE.COD_EMPR_EMISOR,CMP.DOCUMENTO_CTBLE.TXT_EMPR_EMISOR , count(CMP.DOCUMENTO_CTBLE.TXT_EMPR_EMISOR) as cantidad'))
                                			->groupBy('CMP.DOCUMENTO_CTBLE.TXT_EMPR_EMISOR')
                                			->groupBy('CMP.DOCUMENTO_CTBLE.COD_EMPR_EMISOR')
			                                ->orderBy('CMP.DOCUMENTO_CTBLE.TXT_EMPR_EMISOR', 'ASC')
			                                ->get();

		return $lista_documento;

	}


	private function al_lista_documentos_sin_enviar_detallado($empresa_id)
	{
		

        $lista_documento    	=   CMPDocumentoCtble::leftJoin('SGD.USUARIO','SGD.USUARIO.COD_USUARIO','=','CMP.DOCUMENTO_CTBLE.COD_USUARIO_CREA_AUD')
                                    ->whereRaw("LEFT(CMP.DOCUMENTO_CTBLE.NRO_SERIE ,1) in ('F','B')")
                                    ->whereRaw("YEAR(CMP.DOCUMENTO_CTBLE.FEC_EMISION)>2021")
                                    ->whereRaw("CMP.DOCUMENTO_CTBLE.COD_CATEGORIA_TIPO_DOC IN ('TDO0000000000001','TDO0000000000003','TDO0000000000007','TDO0000000000008')")
                                    ->whereIn('CMP.DOCUMENTO_CTBLE.ESTADO_ELEC', ['C',''])
                                    ->where('CMP.DOCUMENTO_CTBLE.COD_ESTADO','=',1)
                                    ->where('CMP.DOCUMENTO_CTBLE.IND_COMPRA_VENTA','=','V')
                                    ->where('CMP.DOCUMENTO_CTBLE.COD_EMPR_EMISOR','=',$empresa_id)
                                    ->select(DB::raw('CMP.DOCUMENTO_CTBLE.TXT_EMPR_EMISOR as EMPR_EMISOR,CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE,CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_TIPO_DOC AS TIPO_DOC,CMP.DOCUMENTO_CTBLE.NRO_SERIE,CMP.DOCUMENTO_CTBLE.NRO_DOC,TXT_EMPR_RECEPTOR as CLIENTE,CMP.DOCUMENTO_CTBLE.FEC_EMISION,CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_ESTADO_DOC_CTBLE as ESTADO_DOC_CTBLE,SGD.USUARIO.NOM_TRABAJADOR'))
                                    ->orderBy('CMP.DOCUMENTO_CTBLE.FEC_EMISION', 'ASC')
                                    ->orderBy('CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', 'ASC')
                                    ->orderBy('CMP.DOCUMENTO_CTBLE.NRO_SERIE', 'ASC')
                                    ->orderBy('CMP.DOCUMENTO_CTBLE.NRO_DOC', 'ASC')
                                    ->get();

		return $lista_documento;

	}

    private function al_lista_documentos_correlativos_faltante_agrupado($empresa_id)
    {
        

        $lista_documento            =       DB::select("SELECT 
                                            DC.TXT_EMPR_EMISOR
                                            ,DC.COD_EMPR
                                            ,TD.NOM_CATEGORIA
                                            ,TD.COD_CATEGORIA
                                            ,DC.NRO_SERIE
                                            ,MIN(DC.NRO_DOC) AS MINDOC
                                            ,MAX(DC.NRO_DOC) AS MANDOC
                                            ,(CAST(MAX(DC.NRO_DOC) AS INT) - CAST(MIN(DC.NRO_DOC) AS INT) + 1) as CANT
                                            ,COUNT(DC.NRO_DOC) AS CANTDOC
                                            ,((CAST(MAX(DC.NRO_DOC) AS INT) - CAST(MIN(DC.NRO_DOC) AS INT) + 1) -  COUNT(DC.NRO_DOC)) AS DIFERENCIA
                                            FROM  CMP.DOCUMENTO_CTBLE(NOLOCK) DC
                                            LEFT JOIN ALM.CENTRO(NOLOCK) C ON DC.COD_CENTRO=C.COD_CENTRO
                                            LEFT JOIN CMP.CATEGORIA(NOLOCK) TD ON DC.COD_CATEGORIA_TIPO_DOC = TD.COD_CATEGORIA
                                            LEFT JOIN STD.TIPO_DOCUMENTO(NOLOCK) TIPO_DOC ON DC.COD_CATEGORIA_TIPO_DOC = TIPO_DOC.COD_TIPO_DOCUMENTO
                                            LEFT JOIN STD.EMPRESA(NOLOCK) EM ON DC.COD_EMPR_RECEPTOR = EM.COD_EMPR
                                            LEFT JOIN STD.EMPRESA(NOLOCK) E_I ON DC.COD_EMPR_IMPRESION = E_I.COD_EMPR
                                            LEFT JOIN CON.PERIODO(NOLOCK) PE  ON DC.COD_PERIODO = PE.COD_PERIODO
                                            LEFT JOIN CON.CATALOGO_CODIGO_SUNAT (NOLOCK) CCS ON CCS.COD_TABLA_SUNAT = '013' AND CCS.COD_ELEMENTO COLLATE Modern_Spanish_CI_AS= DC.ESTADO_ELEC COLLATE Modern_Spanish_CI_AS 
                                            WHERE DC.COD_ESTADO = 1 
                                            AND TIPO_DOC.TXT_INDICADOR=1 
                                            AND DC.COD_CATEGORIA_TIPO_DOC <> 'TDO0000000000009'
                                            AND DC.IND_COMPRA_VENTA='V'
                                            AND YEAR(DC.FEC_EMISION) >= 2022

                                            GROUP by DC.TXT_EMPR_EMISOR,DC.COD_EMPR,TD.NOM_CATEGORIA,TD.COD_CATEGORIA,DC.NRO_SERIE
                                            HAVING ((CAST(MAX(DC.NRO_DOC) AS INT) - CAST(MIN(DC.NRO_DOC) AS INT) + 1) -  COUNT(DC.NRO_DOC)) >0");

        return $lista_documento;

    }


    private function al_lista_documentos_correlativo_detallado($empresa,$categoria,$serie)
    {
        


        $min                        =        WEBViewMigrarVentaCabecera::where('COD_EMPR','=',$empresa)
                                            ->where('COD_CATEGORIA','=',$categoria)
                                            ->where('NRO_SERIE','=',$serie)
                                            ->where(DB::raw("YEAR(FEC_EMISION) >= 2022"))
                                            //->whereIn('COD_PERIODO',$array_periodo)
                                            ->min('NRO_CORRELATIVO');

        $max                        =        WEBViewMigrarVentaCabecera::where('COD_EMPR','=',$empresa)
                                            ->where('COD_CATEGORIA','=',$categoria)
                                            ->where('NRO_SERIE','=',$serie)
                                            ->where(DB::raw("YEAR(FEC_EMISION) >= 2022"))
                                            //->whereIn('COD_PERIODO',$array_periodo)
                                            ->max('NRO_CORRELATIVO');

        $min_int                    =       (int)$min;
        $max_int                    =       (int)$max;
        $total                      =       array();

        for ($i = $min_int; $i <= $max_int; $i++) {
            array_push($total, str_pad($i, 8, "0", STR_PAD_LEFT));
        }


        $lista_documento            =       WEBViewMigrarVentaCabecera::where('COD_EMPR','=',$empresa)
                                            ->where('COD_CATEGORIA','=',$categoria)
                                            ->where('NRO_SERIE','=',$serie)
                                            ->where(DB::raw("YEAR(FEC_EMISION) >= 2022"))
                                            //->whereIn('COD_PERIODO',$array_periodo)
                                            ->pluck('NRO_CORRELATIVO')
                                            ->toArray();

        $resultado = array_values(array_diff($total, $lista_documento));


        return $resultado;

    }


}