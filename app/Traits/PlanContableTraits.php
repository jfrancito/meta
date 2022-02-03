<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


trait PlanContableTraits
{
	private function pc_lista_cuentas_contable($empresa_id, $anio)
	{
	    $listacuentacontable 	= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
									->where('anio','=',$anio)
									->where('activo','=',1)
									->orderBy('orden', 'asc')
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


	public function pc_color_fila($data_plancontable)
	{
		$background = 'primary';
	    if($data_plancontable->nivel == 6){
	    	$background = '';
	    }
		return $background;
	}


}