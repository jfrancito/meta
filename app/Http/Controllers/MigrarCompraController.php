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
use App\Modelos\WEBHistorialMigrar;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

use App\Traits\GeneralesTraits;
use App\Traits\PlanContableTraits;
use App\Traits\ConfiguracionProductoTraits;
use App\Traits\MigrarCompraTraits;

class MigrarCompraController extends Controller
{

	use GeneralesTraits;
	use PlanContableTraits;
	use ConfiguracionProductoTraits;
	use MigrarCompraTraits;
	public $tipo_asiento = 'TAS0000000000004';

	public function actionMigrarCompras()
	{

		set_time_limit(0);
				
		//buscar asiento 
		$lista_compras_migrar_emitido 		= 	$this->mv_lista_compras_migrar_agrupado_emitido($this->array_empresas,$this->anio_inicio);
		$lista_compras_migrar_anulado 		= 	$this->mv_lista_compras_migrar_agrupado_anulado($this->array_empresas,$this->anio_inicio);
		$this->mv_agregar_historial_compras($lista_compras_migrar_emitido,$lista_compras_migrar_anulado,$this->tipo_asiento);

		foreach($lista_compras_migrar_emitido as $index => $item){
			$respuesta = $this->mv_update_historial_compras($item->COD_DOCUMENTO_CTBLE,$this->tipo_asiento);
		}
		foreach($lista_compras_migrar_anulado as $index => $item){
			$respuesta = $this->mv_update_historial_compras($item->COD_DOCUMENTO_CTBLE,$this->tipo_asiento);
		}
		
		//asignar asiento
		$lista_compras 				= 	$this->mv_lista_compras_asignar($this->array_empresas,$this->tipo_asiento);

		foreach($lista_compras as $index => $item){
			$respuesta2 = $this->mv_asignar_asiento_modelo($item,$this->tipo_asiento);
		}
		print_r("se realizo con exito");
		

	}




}
