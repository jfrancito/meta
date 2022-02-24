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
                                			->where('CMP.DOCUMENTO_CTBLE.ESTADO_ELEC','=','C')
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
                                    ->where('CMP.DOCUMENTO_CTBLE.ESTADO_ELEC','=','C')
                                    ->where('CMP.DOCUMENTO_CTBLE.COD_ESTADO','=',1)
                                    ->where('CMP.DOCUMENTO_CTBLE.IND_COMPRA_VENTA','=','V')
                                    ->where('CMP.DOCUMENTO_CTBLE.COD_EMPR_EMISOR','=',$empresa_id)
                                    ->select(DB::raw('CMP.DOCUMENTO_CTBLE.TXT_EMPR_EMISOR as EMPR_EMISOR,CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE,CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_TIPO_DOC AS TIPO_DOC,CMP.DOCUMENTO_CTBLE.NRO_SERIE,CMP.DOCUMENTO_CTBLE.NRO_DOC,TXT_EMPR_RECEPTOR as CLIENTE,CMP.DOCUMENTO_CTBLE.FEC_EMISION,CMP.DOCUMENTO_CTBLE.TXT_CATEGORIA_ESTADO_DOC_CTBLE as ESTADO_DOC_CTBLE,SGD.USUARIO.NOM_TRABAJADOR'))
                                    ->orderBy('CMP.DOCUMENTO_CTBLE.FEC_EMISION', 'ASC')
                                    ->orderBy('CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', 'desc')
                                    ->orderBy('CMP.DOCUMENTO_CTBLE.NRO_SERIE', 'desc')
                                    ->orderBy('CMP.DOCUMENTO_CTBLE.NRO_DOC', 'desc')
                                    ->get();

		return $lista_documento;

	}


}