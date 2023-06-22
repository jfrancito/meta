<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBAsiento;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


trait PlanContableTraits
{



	public function sumar_total_resultado_naturaleza($tipo_cuenta_naturaleza_id, $periodo_array,$array_cuenta,$anio)
	{

		$total                  =   0.00;

	    $cuentas 				= 	WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
									->where('anio','=',$anio)
									->whereIn('nro_cuenta', $array_cuenta)
									->where('tipo_cuenta_naturaleza_id','=',$tipo_cuenta_naturaleza_id)
									->where('activo','=',1)
									->get();

	    $sumat 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    							->join('WEB.cuentacontables', 'WEB.cuentacontables.id', '=', 'WEB.asientomovimientos.COD_CUENTA_CONTABLE')
									->join('CMP.CATEGORIA AS TC', 'TC.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_categoria_id')
									->join('CMP.CATEGORIA AS FTC', 'FTC.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_naturaleza_id')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('FTC.COD_CATEGORIA','=',$tipo_cuenta_naturaleza_id)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array)
	    							->selectRaw('WEB.asientomovimientos.*,
	    										 WEB.asientos.*,
	    										 CON.PERIODO.*,
	    										 SUBSTRING(WEB.asientomovimientos.TXT_CUENTA_CONTABLE, 1, 2) as GRUPO,
	    										 TC.NOM_CATEGORIA AS tipo_cuenta,
	    										 FTC.NOM_CATEGORIA AS tipo_cuenta_funcion,
	    										 TC.COD_CATEGORIA AS tipo_cuenta_id,
	    										 FTC.COD_CATEGORIA AS tipo_cuenta_naturaleza_id')
	    							->get();


	    $sfd = 0;
	    $sfa = 0;							
    	foreach($cuentas as $item){
	        $sum_debe  =   $sumat->where('TXT_CUENTA_CONTABLE','=',$item->nro_cuenta)->sum('CAN_DEBE_MN');
	        $sum_haber =   $sumat->where('TXT_CUENTA_CONTABLE','=',$item->nro_cuenta)->sum('CAN_HABER_MN');
	      	if($sum_debe > $sum_haber){
	          $sfd       =   $sfd + ($sum_debe - $sum_haber);
	      	}
	      	if($sum_haber > $sum_debe){
	          $sfa       =   $sfa + ($sum_haber - $sum_debe);
	      	}
    	}

    	$total = $sfd + $sfa;

		return $total;

	}


	public function sumar_total_resultado_funcion($tipo_cuenta_funcion_id, $periodo_array,$array_cuenta,$anio)
	{

		$total                  =   0.00;

	    $cuentas 				= 	WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
									->where('anio','=',$anio)
									->whereIn('nro_cuenta', $array_cuenta)
									->where('tipo_cuenta_funcion_id','=',$tipo_cuenta_funcion_id)
									->where('activo','=',1)
									->get();

	    $sumat 					= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    							->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    							->join('WEB.cuentacontables', 'WEB.cuentacontables.id', '=', 'WEB.asientomovimientos.COD_CUENTA_CONTABLE')
									->join('CMP.CATEGORIA AS TC', 'TC.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_categoria_id')
									->join('CMP.CATEGORIA AS FTC', 'FTC.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_funcion_id')
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_ESTADO','=','1')
	    							->where('WEB.asientomovimientos.COD_ESTADO ','=','1')
	    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('FTC.COD_CATEGORIA','=',$tipo_cuenta_funcion_id)
	    							->whereIn('CON.PERIODO.COD_PERIODO', $periodo_array)
	    							->selectRaw('WEB.asientomovimientos.*,
	    										 WEB.asientos.*,
	    										 CON.PERIODO.*,
	    										 SUBSTRING(WEB.asientomovimientos.TXT_CUENTA_CONTABLE, 1, 2) as GRUPO,
	    										 TC.NOM_CATEGORIA AS tipo_cuenta,
	    										 FTC.NOM_CATEGORIA AS tipo_cuenta_funcion,
	    										 TC.COD_CATEGORIA AS tipo_cuenta_id,
	    										 FTC.COD_CATEGORIA AS tipo_cuenta_funcion_id')
	    							->get();


	    $sfd = 0;
	    $sfa = 0;							
    	foreach($cuentas as $item){
	        $sum_debe  =   $sumat->where('TXT_CUENTA_CONTABLE','=',$item->nro_cuenta)->sum('CAN_DEBE_MN');
	        $sum_haber =   $sumat->where('TXT_CUENTA_CONTABLE','=',$item->nro_cuenta)->sum('CAN_HABER_MN');
	      	if($sum_debe > $sum_haber){
	          $sfd       =   $sfd + ($sum_debe - $sum_haber);
	      	}
	      	if($sum_haber > $sum_debe){
	          $sfa       =   $sfa + ($sum_haber - $sum_debe);
	      	}
    	}

    	$total = $sfd + $sfa;

		return $total;

	}



	private function pc_lista_cuentas_contable_funcion_detallado_array_cuenta($empresa_id, $anio, $array_cuenta)
	{



	    $array_cuenta_dos 		= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
									->where('anio','=',$anio)
									->whereIn('nro_cuenta', $array_cuenta)
									->where('activo','=',1)
									->select(DB::raw('SUBSTRING(nro_cuenta, 1, 2) as nro_cuenta_dos'))
									->pluck('nro_cuenta_dos')->toArray();

	    $array_id_dos 			= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
									->where('anio','=',$anio)
									->whereIn('nro_cuenta', $array_cuenta_dos)
									->where('activo','=',1)
									->pluck('nro_cuenta')->toArray();

		$resultado 				= 	array_merge($array_cuenta, $array_id_dos);


	    $listacuentacontable 	= 	WEBCuentaContable::leftJoin('CMP.CATEGORIA AS TC', 'TC.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_categoria_id')
									->leftJoin('CMP.CATEGORIA AS TCF', 'TCF.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_funcion_id')
	    							->where('empresa_id','=',$empresa_id)
									->where('anio','=',$anio)
									->whereIn('nro_cuenta', $resultado)
									->whereIn('nivel', ['2','6'])
									->where('WEB.cuentacontables.tipo_cuenta_funcion_id','<>','')
									->select('TCF.NOM_CATEGORIA',
											 'TCF.COD_CATEGORIA',
											 'TCF.NRO_ORDEN',
											 'TCF.CODIGO_SUNAT',
											 'TCF.TXT_REFERENCIA',
											 'TCF.IND_GEN_ASIENTO')
									->where('activo','=',1)
									->groupBy('TCF.COD_CATEGORIA')
									->groupBy('TCF.NOM_CATEGORIA')
									->groupBy('TCF.NRO_ORDEN')
									->groupBy('TCF.CODIGO_SUNAT')
									->groupBy('TCF.TXT_REFERENCIA')
									->groupBy('TCF.IND_GEN_ASIENTO')
									->orderBy('TCF.NRO_ORDEN', 'asc')
			    					->get();

		return $listacuentacontable;

	}


	private function pc_lista_cuentas_contable_naturaleza_detallado_array_cuenta($empresa_id, $anio, $array_cuenta)
	{



	    $array_cuenta_dos 		= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
									->where('anio','=',$anio)
									->whereIn('nro_cuenta', $array_cuenta)
									->where('activo','=',1)
									->select(DB::raw('SUBSTRING(nro_cuenta, 1, 2) as nro_cuenta_dos'))
									->pluck('nro_cuenta_dos')->toArray();

	    $array_id_dos 			= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
									->where('anio','=',$anio)
									->whereIn('nro_cuenta', $array_cuenta_dos)
									->where('activo','=',1)
									->pluck('nro_cuenta')->toArray();

		$resultado 				= 	array_merge($array_cuenta, $array_id_dos);


	    $listacuentacontable 	= 	WEBCuentaContable::leftJoin('CMP.CATEGORIA AS TC', 'TC.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_categoria_id')
									->leftJoin('CMP.CATEGORIA AS TCF', 'TCF.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_naturaleza_id')
	    							->where('empresa_id','=',$empresa_id)
									->where('anio','=',$anio)
									->whereIn('nro_cuenta', $resultado)
									->whereIn('nivel', ['2','6'])
									->where('WEB.cuentacontables.tipo_cuenta_naturaleza_id','<>','')
									->select('TCF.NOM_CATEGORIA',
											 'TCF.COD_CATEGORIA',
											 'TCF.NRO_ORDEN',
											 'TCF.CODIGO_SUNAT',
											 'TCF.TXT_REFERENCIA',
											 'TCF.IND_GEN_ASIENTO')
									->where('activo','=',1)
									->groupBy('TCF.COD_CATEGORIA')
									->groupBy('TCF.NOM_CATEGORIA')
									->groupBy('TCF.NRO_ORDEN')
									->groupBy('TCF.CODIGO_SUNAT')
									->groupBy('TCF.TXT_REFERENCIA')
									->groupBy('TCF.IND_GEN_ASIENTO')
									->orderBy('TCF.NRO_ORDEN', 'asc')
			    					->get();

		return $listacuentacontable;

	}


	private function pc_lista_cuentas_contable_balance_detallado_array_cuenta($empresa_id, $anio, $array_cuenta)
	{



	    $array_cuenta_dos 		= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
									->where('anio','=',$anio)
									->whereIn('nro_cuenta', $array_cuenta)
									->where('activo','=',1)
									->select(DB::raw('SUBSTRING(nro_cuenta, 1, 2) as nro_cuenta_dos'))
									->pluck('nro_cuenta_dos')->toArray();

	    $array_id_dos 			= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
									->where('anio','=',$anio)
									->whereIn('nro_cuenta', $array_cuenta_dos)
									->where('activo','=',1)
									->pluck('nro_cuenta')->toArray();

		$resultado 				= 	array_merge($array_cuenta, $array_id_dos);



	    $listacuentacontable 	= 	WEBCuentaContable::leftJoin('CMP.CATEGORIA AS TC', 'TC.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_categoria_id')
									->leftJoin('CMP.CATEGORIA AS BTC', 'BTC.COD_CATEGORIA', '=', 'WEB.cuentacontables.tipo_cuenta_balance_id')
	    							->where('empresa_id','=',$empresa_id)
									->where('anio','=',$anio)
									->whereIn('nro_cuenta', $resultado)
									->whereIn('nivel', ['2','6'])
									->where('WEB.cuentacontables.tipo_cuenta_balance_id','<>','')
									->select('WEB.cuentacontables.*',
											 'TC.NOM_CATEGORIA AS tipo_cuenta',
											 'BTC.NOM_CATEGORIA AS tipo_cuenta_balance',
											 'TC.COD_CATEGORIA AS tipo_cuenta_id',
											 'BTC.COD_CATEGORIA AS tipo_cuenta_balance_id')
									->where('activo','=',1)
									->orderBy('orden', 'asc')
			    					->get();

		return $listacuentacontable;

	}


	private function pc_lista_cuentas_contable_array_cuenta($empresa_id, $anio, $array_cuenta)
	{
	    $listacuentacontable 	= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
									->where('anio','=',$anio)
									->whereIn('nro_cuenta', $array_cuenta)
									->where('activo','=',1)
									->orderBy('orden', 'asc')
			    					->get();

		return $listacuentacontable;

	}

	private function pc_lista_cuentas_contable($empresa_id, $anio)
	{

	    $listacuentacontable 	= 	WEBCuentaContable::leftJoin('CMP.CATEGORIA as clase','clase.COD_CATEGORIA','=','WEB.cuentacontables.clase_categoria_id')
	    							->leftJoin('CMP.CATEGORIA as tiposaldo','tiposaldo.COD_CATEGORIA','=','WEB.cuentacontables.tipo_saldo_categoria_id')
	    							->leftJoin('CMP.CATEGORIA as tipocuenta','tipocuenta.COD_CATEGORIA','=','WEB.cuentacontables.tipo_cuenta_categoria_id')
	    							->where('WEB.cuentacontables.empresa_id','=',$empresa_id)
									->where('WEB.cuentacontables.anio','=',$anio)
									->where('WEB.cuentacontables.activo','=',1)
									->select('WEB.cuentacontables.*','clase.NOM_CATEGORIA as nclase',
											'tiposaldo.NOM_CATEGORIA as ntiposaldo',
											'tipocuenta.NOM_CATEGORIA as ntipocuenta')
									->orderBy('WEB.cuentacontables.orden', 'asc')
			    					->get();

		return $listacuentacontable;

	}

	private function pc_array_anio_cuentas_contable($empresa_id)
	{
	    $array_anio_pc 			= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
									->where('activo','=',1)
									->groupBy('anio')
									->pluck('anio','anio')
									->toArray();

		return $array_anio_pc;

	}

	private function pc_array_nivel_cuentas_contable($empresa_id, $anio)
	{

	    $array_nivel_pc 			= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
										->where('activo','=',1)
										->orderBy('WEB.cuentacontables.nivel', 'asc')
										->groupBy('nivel')
										->pluck('nivel','nivel')									
										->toArray();

		return $array_nivel_pc;

	}

	private function pc_array_nro_cuentas_nombre_xnivel($empresa_id,$nivel, $anio)
	{

	    $array_nro_cuenta_pc 		= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
	    								->where('anio','=',$anio)
	    								->where('nivel','=',$nivel)
										->where('activo','=',1)
										->orderBy('id', 'asc')
										->select(DB::raw("nro_cuenta + ' ' + nombre as nro_cuenta_nombre, id"))
										->pluck('nro_cuenta_nombre','id')									
										->toArray();

		return $array_nro_cuenta_pc;

	}


	private function pc_array_nro_cuentas_nombre_xnivel_caja($empresa_id,$nivel, $anio)
	{

	    $array_nro_cuenta_pc 		= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
	    								->where('anio','=',$anio)
	    								->where('nivel','=',$nivel)
	    								->where('nro_cuenta','=','101101')
										->where('activo','=',1)
										->orderBy('id', 'asc')
										->select(DB::raw("nro_cuenta + ' ' + nombre as nro_cuenta_nombre, id"))
										->pluck('nro_cuenta_nombre','id')									
										->toArray();

		return $array_nro_cuenta_pc;

	}


	private function pc_array_nro_cuentas_nombre_xnivel_banco($empresa_id,$nivel, $anio)
	{

	    $array_nro_cuenta_pc 		= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
	    								->where('anio','=',$anio)
	    								->where('nivel','=',$nivel)
	    								->where('nro_cuenta','like','1041%')
										->where('activo','=',1)
										->orderBy('id', 'asc')
										->select(DB::raw("nro_cuenta + ' ' + nombre as nro_cuenta_nombre, id"))
										->pluck('nro_cuenta_nombre','id')									
										->toArray();

		return $array_nro_cuenta_pc;

	}

	private function pc_array_nro_cuentas_nombre($empresa_id, $anio)
	{

	    $array_nro_cuenta_pc 		= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
	    								->where('anio','=',$anio)
										->where('activo','=',1)
										->orderBy('id', 'asc')
										->select(DB::raw("nro_cuenta + ' ' + nombre as nro_cuenta_nombre, id"))
										->pluck('nro_cuenta_nombre','id')									
										->toArray();

		return $array_nro_cuenta_pc;

	}

	private function pc_array_nro_cuenta_nro_cuenta($empresa_id, $anio)
	{

	    $array_nro_cuenta_pc 		= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
	    								->where('anio','=',$anio)
										->where('activo','=',1)
										->orderBy('orden', 'asc')
										->select(DB::raw("nro_cuenta + ' ' + nombre as nro_cuenta_nombre, nro_cuenta"))
										->pluck('nro_cuenta_nombre','nro_cuenta')									
										->toArray();

		return $array_nro_cuenta_pc;

	}


	public function pc_color_fila($data_plancontable)
	{
		$background = 'primary';
	    if($data_plancontable->nivel == 6){
	    	$background = '';
	    }
		return $background;
	}


}