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

use App\Traits\PlanContableTraits;

class PlanContableController extends Controller
{
	use PlanContableTraits;
	use GeneralesTraits;

	public function actionListarPlanContable($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Plan Contable');
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$anio  					=   $this->anio;
		if(Session::has('anio_pc')){$anio = Session::get('anio_pc');}
	    $listacuentacontable 	= 	$this->pc_lista_cuentas_contable(Session::get('empresas_meta')->COD_EMPR,$anio);
		$funcion 				= 	$this;
		return View::make('plancontable/listaplancontable',
						 [
						 	'listacuentacontable' 	=> $listacuentacontable,
						 	'combo_anio_pc'	 		=> $combo_anio_pc,
						 	'anio'	 				=> $anio,						 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxListarPlanContable(Request $request)
	{
		$anio 					=   $request['anio'];
		$idopcion 				=   $request['idopcion'];
	    $listacuentacontable 	= 	$this->pc_lista_cuentas_contable(Session::get('empresas_meta')->COD_EMPR,$anio);
		$funcion 				= 	$this;
		return View::make('plancontable/ajax/alistaplancontable',
						 [
						 	'listacuentacontable' 	=> $listacuentacontable,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}




	public function actionAjaxConfiguracionPlanContable(Request $request)
	{
		$cuenta_contable_id 	=   $request['cuenta_contable_id'];
		$idopcion 				=   $request['idopcion'];
		$anio 					=   $request['anio'];

		$cuenta_contable 	    = 	WEBCuentaContable::where('id','=',$cuenta_contable_id)->first();


		$combo_clase 	    	= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_CLASE','Seleccione clase','');
		$combo_tipo_saldo    	= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_TIPO_SALDO','Seleccione tipo de saldo','');
		$combo_tipo_cuenta    	= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_TIPO_CUENTA','Seleccione tipo de cuenta','');

		$array_cuenta_pc     	= 	$this->pc_array_nro_cuenta_nro_cuenta(Session::get('empresas_meta')->COD_EMPR,$anio);

		$combo_cuenta_tran_debe01		= 	$this->gn_generacion_combo_array('Seleccione cuenta contable transferencia debe 01', '' , $array_cuenta_pc);
		$combo_cuenta_tran_debe02		= 	$this->gn_generacion_combo_array('Seleccione cuenta contable transferencia debe 02', '' , $array_cuenta_pc);
		$combo_cuenta_tran_haber		= 	$this->gn_generacion_combo_array('Seleccione cuenta contable transferencia haber', '' , $array_cuenta_pc);


		$funcion 				= 	$this;

		return View::make('plancontable/modal/ajax/maconfiguracionplancontable',
						 [		 	
						 	'anio' 					=> $anio,
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'cuenta_contable' 		=> $cuenta_contable,
						 	'combo_clase' 			=> $combo_clase,
						 	'combo_tipo_saldo' 		=> $combo_tipo_saldo,
						 	'combo_tipo_cuenta' 	=> $combo_tipo_cuenta,
						 	'combo_cuenta_tran_debe01' 	=> $combo_cuenta_tran_debe01,
						 	'combo_cuenta_tran_debe02' 	=> $combo_cuenta_tran_debe02,
						 	'combo_cuenta_tran_haber' 	=> $combo_cuenta_tran_haber,
						 	'ajax' 					=> true,						 	
						 ]);
	}


	public function actionGuardarConfiguracionPlanContable($idopcion,Request $request)
	{


		if($_POST)
		{
			$cuenta_contable_id = $request['cuenta_contable_id'];
			$anio 				= $request['anio'];
			$clase_id 			= $request['clase_id'];
			$tiposaldo_id 		= $request['tiposaldo_id'];
			$tipocuenta_id 		= $request['tipocuenta_id'];
			$codigo_migracion 	= $request['codigo_migracion'];


			$cuenta_contable 	= WEBCuentaContable::where('id','=',$cuenta_contable_id)->first();

			$cuenta_contable->clase_categoria_id  		 	=	$clase_id;
			$cuenta_contable->tipo_saldo_categoria_id 		=	$tiposaldo_id;
			$cuenta_contable->tipo_cuenta_categoria_id 		=	$tipocuenta_id;
			$cuenta_contable->codigo_migracion 				=	$codigo_migracion;

			$cuenta_contable->fecha_mod 	 				=   $this->fechaactual;
			$cuenta_contable->usuario_mod 					=   Session::get('usuario_meta')->id;
			$cuenta_contable->save();

			Session::flash('anio_pc', $anio);
			return Redirect::to('/gestion-plan-contable/'.$idopcion)->with('bienhecho', 'Cuenta Contable '.$cuenta_contable->nombre.' modificada con exito');

		}


	}

	public function actionGuardarComprasPlanContable($idopcion,Request $request)
	{


		if($_POST)
		{
			$cuenta_contable_id = $request['cuenta_contable_id'];
			$anio 				= $request['anio'];

			$cuenta_contable_tran_debe01_id 	= $request['cuenta_contable_tran_debe01_id'];
			$cuenta_contable_por_debe01_id 		= $request['cuenta_contable_por_debe01_id'];
			$cuenta_contable_tran_debe02_id 	= $request['cuenta_contable_tran_debe02_id'];
			$cuenta_contable_por_debe02_id 		= $request['cuenta_contable_por_debe02_id'];
			$cuenta_contable_tran_haber_id 		= $request['cuenta_contable_tran_haber_id'];

			if(is_null($cuenta_contable_tran_debe01_id)){
				$cuenta_contable_tran_debe01_id ='';
			}
			if(is_null($cuenta_contable_tran_debe02_id)){
				$cuenta_contable_tran_debe02_id ='';
			}
			if(is_null($cuenta_contable_tran_haber_id)){
				$cuenta_contable_tran_haber_id ='';
			}

			if($cuenta_contable_por_debe01_id == ''){
				$cuenta_contable_por_debe01_id = 0;
			}
			if($cuenta_contable_por_debe02_id == ''){
				$cuenta_contable_por_debe01_id = 0;
			}

			$cuenta_contable 	= WEBCuentaContable::where('id','=',$cuenta_contable_id)->first();

			$cuenta_contable->cuenta_contable_transferencia_debe  		 	=	$cuenta_contable_tran_debe01_id;
			$cuenta_contable->transferencia_debe_porcentaje 				=	$cuenta_contable_por_debe01_id;
			$cuenta_contable->cuenta_contable_transferencia_debe02 			=	$cuenta_contable_tran_debe02_id;
			$cuenta_contable->transferencia_debe02_porcentaje 				=	$cuenta_contable_por_debe02_id;
			$cuenta_contable->cuenta_contable_transferencia_haber 			=	$cuenta_contable_tran_haber_id;
			$cuenta_contable->fecha_mod 	 				=   $this->fechaactual;
			$cuenta_contable->usuario_mod 					=   Session::get('usuario_meta')->id;
			$cuenta_contable->save();

			Session::flash('anio_pc', $anio);
			return Redirect::to('/gestion-plan-contable/'.$idopcion)->with('bienhecho', 'Cuenta Contable '.$cuenta_contable->nombre.' modificada con exito');

		}


	}


	public function actionAjaxComboCuentaContableNivel(Request $request)
	{

		$nivel 					=   $request['nivel'];
		$anio  					=   $this->anio;
		$array_cuenta 	    	= 	$this->pc_array_nro_cuentas_nombre_xnivel(Session::get('empresas_meta')->COD_EMPR,$nivel,$anio);
		$combo_cuenta  			= 	$this->gn_generacion_combo_array('Seleccione cuenta contable', '' , $array_cuenta);
		$defecto_cuenta			= 	'';

		return View::make('plancontable/combo/cnrocuentanombre',
						 [		 	
						 	'combo_cuenta' 			=> $combo_cuenta,
						 	'defecto_cuenta' 		=> $defecto_cuenta,
						 	'ajax' 					=> true,						 	
						 ]);
	}




}
