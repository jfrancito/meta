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

	private function sv_lista_inventario($empresa_id, $anio, $listaperiodo)
	{

		$array_detalle_asiento 		=	array();

	    $listaproductosagrupados 	= 	WEBInventarioSegundaVenta::where('empresa_id','=',$empresa_id)
										->where('anio','=',$anio)
										->where('activo','=',1)
										->select('producto_id','filtro')
										->groupBy('producto_id')
										->groupBy('filtro')
			    						->get();

	    foreach($listaproductosagrupados as $index => $item){

	    	$array_nuevo_asiento 	=	array();
			$array_nuevo_asiento    =	array(
				"anio" 						=> $anio,
				"producto_id" 				=> $item->producto_id,
				"filtro" 					=> $item->filtro,
				"producto_nombre" 			=> $item->producto->NOM_PRODUCTO
			);
		    foreach($listaperiodo as $indexp => $itemp){

		    	$cantidad_compra		=	0.0000;
		    	$cantidad_asociada		=	0.0000;
			    $inventariosegunda 		= 	WEBInventarioSegundaVenta::where('empresa_id','=',$empresa_id)
											->where('anio','=',$anio)
											->where('producto_id','=',$item->producto_id)
											->where('periodo_id','=',$itemp->COD_PERIODO)
					    					->first();

				if(count($inventariosegunda)>0){
					$cantidad_compra		=	$inventariosegunda->cantidad_compra;
					$cantidad_asociada		=	$inventariosegunda->cantidad_documento;
				}

		    	$array_nuevo_asiento    = $array_nuevo_asiento + array(
					"mes".$indexp			=> $itemp->TXT_NOMBRE,
					"monto".$indexp			=> $cantidad_compra,
					"asociada".$indexp		=> $cantidad_asociada,
				);
		    }
			array_push($array_detalle_asiento,$array_nuevo_asiento);
	    }

		return $array_detalle_asiento;

	}


}