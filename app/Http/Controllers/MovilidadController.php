<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;


use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;
use App\Traits\MovilidadTraits;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;



use Illuminate\Support\Facades\Storage;

class MovilidadController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use MovilidadTraits;

	public function actionListarMovilidad($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Lista de registro de movilidad');

	    $sel_periodo 			=	'';
	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
	    $listamovilidad 		= 	array();
		$funcion 				= 	$this;
		
		return View::make('movilidad/listamovilidad',
						 [
						 	'listamovilidad' 		=> $listamovilidad,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_periodo'	 		=> $sel_periodo,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}



	public function actionAjaxRegistroMovilidad(Request $request)
	{


		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$empresa_id 			=   Session::get('empresas_meta')->COD_EMPR;

        $listamovilidad     	= 	$this->movilidad_lista_movilidad('LISTA_MOVILIDAD',$empresa_id,$periodo_id);

		$funcion 				= 	$this;
		
		return View::make('movilidad/ajax/alistamovilidad',
						 [

						 	'listamovilidad'		=> $listamovilidad,
						 	'funcion'				=> $funcion,			 	
						 	'ajax' 					=> true,						 	
						 ]);
	}




}
