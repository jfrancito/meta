<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBViewMigrarCompras;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


trait ComprasTraits
{
	private function co_lista_compras_migrar($anio,$periodo_id,$empresa_id,$serie,$documento)
	{

		$lista_compras		=		WEBViewMigrarCompras::where('WEB.viewmigrarcompras.COD_PERIODO','=',$periodo_id)
									->where('WEB.viewmigrarcompras.COD_EMPR','=',$empresa_id)
									->NroSerie($serie)
									->NroDocumento($documento)
									->select(DB::raw('WEB.viewmigrarcompras.NRO_SERIE,
													  WEB.viewmigrarcompras.NRO_DOC,
													  WEB.viewmigrarcompras.FEC_EMISION,
													  WEB.viewmigrarcompras.NOM_TIPO_DOC,
													  WEB.viewmigrarcompras.NOM_PROVEEDOR,
													  WEB.viewmigrarcompras.NOM_MONEDA,
													  sum(WEB.viewmigrarcompras.CAN_SUB_TOTAL) as CAN_SUB_TOTAL,
													  sum(WEB.viewmigrarcompras.CAN_IMPUESTO_VTA) as CAN_IMPUESTO_VTA,
													  sum(WEB.viewmigrarcompras.CAN_TOTAL) as CAN_TOTAL,
													  WEB.viewmigrarcompras.NOM_ESTADO'))
									->groupBy('WEB.viewmigrarcompras.NRO_SERIE')
									->groupBy('WEB.viewmigrarcompras.NRO_DOC')
									->groupBy('WEB.viewmigrarcompras.FEC_EMISION')
									->groupBy('WEB.viewmigrarcompras.NOM_TIPO_DOC')
									->groupBy('WEB.viewmigrarcompras.NOM_PROVEEDOR')
									->groupBy('WEB.viewmigrarcompras.NOM_MONEDA')
									->groupBy('WEB.viewmigrarcompras.NOM_ESTADO')
									->get();

		return $lista_compras;

	}


}