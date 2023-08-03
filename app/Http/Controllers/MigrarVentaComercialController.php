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

class MigrarVentaComercialController extends Controller
{

	use GeneralesTraits;
	use PlanContableTraits;
	use ConfiguracionProductoTraits;
	use MigrarVentaComercialTraits;


	public function actionMigrarVentasComercial()
	{
		set_time_limit(0);
		$tipo_asiento 						=	'TAS0000000000003';	
		//buscar asiento 
		$lista_ventas_migrar_emitido 		= 	$this->mv_lista_ventas_migrar_agrupado_emitido_comercial();
		//dd($lista_ventas_migrar_emitido);
		$lista_ventas_migrar_anulado 		= 	$this->mv_lista_ventas_migrar_agrupado_anulado_comercial();
		//dd($lista_ventas_migrar_emitido);
		//dd($lista_ventas_migrar_anulado);
		$lista_ventas_migrar_anulado_nuevo  = 	$this->mv_lista_ventas_migrar_agrupado_anulado_nuevo_comercial();

		//dd($lista_ventas_migrar_anulado);

		$this->mv_agregar_historial_ventas_comercial($lista_ventas_migrar_emitido,$lista_ventas_migrar_anulado,$tipo_asiento);
		//dd('llego');
		
		foreach($lista_ventas_migrar_emitido as $index => $item){
			$respuesta = $this->mv_update_historial_ventas_comercial($item->COD_DOCUMENTO_CTBLE,$tipo_asiento);
		}
		
		foreach($lista_ventas_migrar_anulado as $index => $item){
			$respuesta = $this->mv_update_historial_ventas_comercial($item->COD_DOCUMENTO_CTBLE,$tipo_asiento);
		}	
		
		foreach($lista_ventas_migrar_anulado_nuevo as $index => $item){
			$respuesta = $this->mv_update_historial_ventas_comercial($item->COD_DOCUMENTO_CTBLE,$tipo_asiento);
		}	

		//asignar asiento
		$lista_ventas 				= 	$this->mv_lista_ventas_asignar_comercial($tipo_asiento);

		//dd($lista_ventas);

		
		foreach($lista_ventas as $index => $item){
			$respuesta2 = $this->mv_asignar_asiento_modelo_comercial($item,$tipo_asiento);
		}
		//$this->mv_asignar_totales_ceros();

		//print_r("se realizo con exito");
		return Redirect::to('/bienvenido');

	}



}
