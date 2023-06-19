<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Traits\GeneralesTraits;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

use App\Traits\SegundaVentaTraits;
use App\Traits\PlanContableTraits;


class SegundaVentaController extends Controller
{
	use SegundaVentaTraits;
	use GeneralesTraits;
	use PlanContableTraits;

	public function actionListarSegundaVenta($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Gestion Segunda Venta');
	    $empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$anio  					=   $this->anio;
		if(Session::has('anio_pc')){$anio = Session::get('anio_pc');}
		
		$listaperiodo 			=	$this->gn_lista_periodo($anio,$empresa_id);

	    $listasegundaventa 		= 	$this->sv_lista_inventario($empresa_id,$anio);
		$funcion 				= 	$this;
		return View::make('venta/listasegundaventa',
						 [
						 	'listasegundaventa' 	=> $listasegundaventa,
						 	'combo_anio_pc'	 		=> $combo_anio_pc,
						 	'anio'	 				=> $anio,						 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}




}
