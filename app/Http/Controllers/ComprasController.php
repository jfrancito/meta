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
use App\Traits\ComprasTraits;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;



use Illuminate\Support\Facades\Storage;

class ComprasController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use ComprasTraits;

	public function actionListarCompras($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Listado de compras');


	    $sel_periodo 			=	'';
	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
	    $listacompras 			= 	array();
		$funcion 				= 	$this;
		
		return View::make('compras/listacompras',
						 [
						 	'listacompras' 			=> $listacompras,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_periodo'	 		=> $sel_periodo,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxListarCompras(Request $request)
	{

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$serie 					=   $request['serie'];
		$documento 				=   $request['documento'];


        $listacompras     		= 	$this->co_lista_compras_migrar($anio,$periodo_id,Session::get('empresas_meta')->COD_EMPR,$serie,$documento);
		$funcion 				= 	$this;
		
		return View::make('compras/ajax/alistacompras',
						 [

						 	'listacompras'			=> $listacompras,
						 	'funcion'				=> $funcion,			 	
						 	'ajax' 					=> true,						 	
						 ]);
	}

	public function actionAjaxBuscarCompraseleccionada(Request $request)
	{

		$documento_ctble_id 	=   $request['documento_ctble_id'];
		$compra     			= 	$this->co_documento_compra($documento_ctble_id);
		$listadetallecompra     = 	$this->co_detalle_compra($documento_ctble_id);

		$tipo_asiento_id 		= 	'TAS0000000000004';
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;
		$cod_moneda 			= 	$compra->COD_MONEDA;
		$cod_proveedor 			= 	$compra->COD_PROVEEDOR;
		$proveedor     			= 	$this->gn_data_empresa($cod_proveedor);
		$relacionado 			=   $proveedor->IND_RELACIONADO;

		$iconotipocliente     	= 	$this->co_asiento_modelo($tipo_asiento_id,$empresa_id,$relacionado,$cod_moneda,'tipo_cliente');
		$iconomoneda     		= 	$this->co_asiento_modelo($tipo_asiento_id,$empresa_id,$relacionado,$cod_moneda,'moneda_id');



		$funcion 				= 	$this;
		
		return View::make('compras/ajax/aasignarasiento',
						 [

						 	'compra'				=> $compra,
						 	'listadetallecompra'	=> $listadetallecompra,
						 	'iconotipocliente'		=> $iconotipocliente,
						 	'iconomoneda'			=> $iconomoneda,
						 	'funcion'				=> $funcion,			 	
						 	'ajax' 					=> true,						 	
						 ]);
	}




}
