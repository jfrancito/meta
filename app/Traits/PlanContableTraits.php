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
									->orderBy('id', 'asc')
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


	public function pc_color_fila($data_plancontable)
	{
		$background = 'primary';
	    if($data_plancontable->nivel == 6){
	    	$background = '';
	    }
		return $background;
	}


}