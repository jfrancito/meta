<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBInventarioSegundaVenta;
use App\Modelos\WEBAsiento;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


trait SegundaVentaTraits
{

	private function sv_lista_inventario($empresa_id, $anio)
	{
	    $listasegundaventa 	= 	WEBInventarioSegundaVenta::where('empresa_id','=',$empresa_id)
								->where('anio','=',$anio)
								->where('activo','=',1)
			    				->get();

		return $listasegundaventa;

	}


}