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
use App\Traits\MigrarReciboHonorarioTraits;

class MigrarReciboHonorarioController extends Controller
{

	use GeneralesTraits;
	use PlanContableTraits;
	use ConfiguracionProductoTraits;
	use MigrarReciboHonorarioTraits;
	public $tipo_asiento = 'TAS0000000000007';

	public function actionMigrarReciboHonorario()
	{

		set_time_limit(0);

		//buscar asiento 
		$lista_compras_migrar_emitido 		= 	$this->mrh_lista_compras_migrar_agrupado_emitido($this->array_empresas,$this->anio_inicio);
		$lista_compras_migrar_anulado 		= 	array();
		$this->mrh_agregar_historial_compras($lista_compras_migrar_emitido,$lista_compras_migrar_anulado,$this->tipo_asiento);
		foreach($lista_compras_migrar_emitido as $index => $item){
			$respuesta = $this->mrh_update_historial_compras($item->COD_DOCUMENTO_CTBLE,$this->tipo_asiento);
		}
		//asignar asiento
		$lista_compras 				= 	$this->mrh_lista_compras_asignar($this->array_empresas,$this->tipo_asiento);
		foreach($lista_compras as $index => $item){
			$respuesta2 = $this->mrh_asignar_asiento_modelo($item,$this->tipo_asiento);
		}
		print_r("se realizo con exito");
		

	}




}
