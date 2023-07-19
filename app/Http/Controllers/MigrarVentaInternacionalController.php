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
use App\Traits\MigrarVentaComercialTraits;
use App\Traits\MigrarVentaInternacionalTraits;


class MigrarVentaInternacionalController extends Controller
{

	use GeneralesTraits;
	use PlanContableTraits;
	use ConfiguracionProductoTraits;
	use MigrarVentaComercialTraits;
	use MigrarVentaInternacionalTraits;

	public function actionMigrarVentasInternacional()
	{
		set_time_limit(0);
		$tipo_asiento 						=	'TAS0000000000003';	
		//buscar asiento 
		$lista_ventas_migrar_emitido 		= 	$this->mvi_lista_ventas_migrar_agrupado_emitido_internacional();
		//dd($lista_ventas_migrar_emitido);
		$lista_ventas_migrar_anulado 		= 	$this->mvi_lista_ventas_migrar_agrupado_anulado_internacional();

		$this->mv_agregar_historial_ventas_internacional($lista_ventas_migrar_emitido,$lista_ventas_migrar_anulado,$tipo_asiento);



		foreach($lista_ventas_migrar_emitido as $index => $item){
			$respuesta = $this->mv_update_historial_ventas_internacional($item->COD_DOCUMENTO_CTBLE,$tipo_asiento);
		}
		foreach($lista_ventas_migrar_anulado as $index => $item){
			$respuesta = $this->mv_update_historial_ventas_internacional($item->COD_DOCUMENTO_CTBLE,$tipo_asiento);
		}	

		//asignar asiento
		$lista_ventas 				= 	$this->mv_lista_ventas_asignar_internacional($tipo_asiento);
		//dd($lista_ventas);
		foreach($lista_ventas as $index => $item){
			$respuesta2 = $this->mv_asignar_asiento_modelo_internacional($item,$tipo_asiento);
		}

		//$this->mv_asignar_totales_ceros();
		//print_r("se realizo con exito");
		return Redirect::to('/bienvenido');

	}



}
