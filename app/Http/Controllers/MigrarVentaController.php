<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBProductoEmpresa;
use App\Modelos\ALMProducto;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

use App\Traits\GeneralesTraits;
use App\Traits\PlanContableTraits;
use App\Traits\ConfiguracionProductoTraits;
use App\Traits\MigrarVentaTraits;




class MigrarVentaController extends Controller
{

	use GeneralesTraits;
	use PlanContableTraits;
	use ConfiguracionProductoTraits;
	use MigrarVentaTraits;
	

	public function actionMigrarVentas()
	{

		//buscar asiento 
		$lista_ventas_migrar_emitido 		= 	$this->mv_lista_ventas_migrar_agrupado_emitido();
		$lista_ventas_migrar_anulado 		= 	$this->mv_lista_ventas_migrar_agrupado_anulado();
		$this->mv_agregar_historial_ventas($lista_ventas_migrar_emitido,$lista_ventas_migrar_anulado);

		foreach($lista_ventas_migrar_emitido as $index => $item){
			$respuesta = $this->mv_update_historial_ventas($item->COD_DOCUMENTO_CTBLE);
		}

		foreach($lista_ventas_migrar_anulado as $index => $item){
			$respuesta = $this->mv_update_historial_ventas($item->COD_DOCUMENTO_CTBLE);
		}	

		//asignar asiento
		$lista_ventas 				= 	$this->mv_lista_ventas_asignar();

		//dd($lista_ventas);

		foreach($lista_ventas as $index => $item){
			$respuesta2 = $this->mv_asignar_asiento_modelo($item);
		}
		print_r("se realizo con exito");


	}


}
