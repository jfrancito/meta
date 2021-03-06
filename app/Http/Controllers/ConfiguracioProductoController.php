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

class ConfiguracioProductoController extends Controller
{

	use GeneralesTraits;
	use PlanContableTraits;
	use ConfiguracionProductoTraits;
	use MigrarVentaTraits;

	public function actionListarConfiguracionProducto($idopcion,$tipo_asiento)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Configuración Producto');

	    if($tipo_asiento == '3'){$tipo_asiento = 'TAS0000000000003';}
	    if($tipo_asiento == '4'){$tipo_asiento = 'TAS0000000000004';}

	    $combo_producto  				= 	$this->gn_generacion_combo_productos('Seleccione producto', '');
		$funcion 						= 	$this;
	    $anio  							=   $this->anio;
        $array_anio_pc     				= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  				= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$empresa_id 					=   Session::get('empresas_meta')->COD_EMPR;
		$lista_productos_sc 		 	= 	$this->mv_lista_productos_sin_configuracion($tipo_asiento,$empresa_id,$anio);
		$array_productos_empresa    	=	ALMProducto::whereIn('COD_PRODUCTO',$lista_productos_sc)
											->pluck('COD_PRODUCTO')
											->toArray();
		$lista_configuracion_producto 	= 	$this->cp_lista_productos_configuracion($empresa_id, $anio,$array_productos_empresa);
		$defecto_producto				= 	'';

		//dd($lista_configuracion_producto);

		return View::make('configuracionproducto/listaconfiguracionproducto',
						 [
						 	'combo_producto' 					=> $combo_producto,						 	
						 	'idopcion' 							=> $idopcion,
						 	'funcion' 							=> $funcion,	
						 	'defecto_producto' 					=> $defecto_producto,
						 	'anio' 								=> $anio,	
						 	'combo_anio_pc' 					=> $combo_anio_pc,
						 	'lista_configuracion_producto' 		=> $lista_configuracion_producto,				 	
						 ]);
	}


	public function actionAjaxConfiguracionProducto(Request $request)
	{
		$producto_id 					=   $request['producto_id'];
		$anio 							=   $request['anio'];
		$idopcion 						=   $request['idopcion'];

		$productoempresa 				= 	WEBProductoEmpresa::where('producto_id','=',$producto_id)->first();

		$array_productos_empresa    	=	ALMProducto::where('COD_PRODUCTO','=',$producto_id)
											->pluck('COD_PRODUCTO')
											->toArray();
		$empresa_id 					=   Session::get('empresas_meta')->COD_EMPR;

		$lista_configuracion_producto 	= 	$this->cp_lista_productos_configuracion($empresa_id, $anio,$array_productos_empresa);

		$funcion 						= 	$this;

		return View::make('configuracionproducto/ajax/alistaconfiguracionproducto',
						 [
						 	'lista_configuracion_producto' 	=> $lista_configuracion_producto,					 	
						 	'idopcion' 						=> $idopcion,
						 	'funcion' 						=> $funcion,
						 	'ajax' 							=> true,						 	
						 ]);
	}




	public function actionAjaxModalConfiguracionProductoCuentaContable(Request $request)
	{
		
		$array_productos 		=   json_encode($request['array_productos'],false);
		$anio  					=   $this->anio;

		$array_cuenta_pc     	= 	$this->pc_array_nro_cuentas_nombre(Session::get('empresas_meta')->COD_EMPR,$anio);

		$combo_cuenta_rel		= 	$this->gn_generacion_combo_array('Seleccione cuenta contable relacionada', '' , $array_cuenta_pc);
		$combo_cuenta_ter		= 	$this->gn_generacion_combo_array('Seleccione cuenta contable tercero', '' , $array_cuenta_pc);
		$combo_cuenta_com		= 	$this->gn_generacion_combo_array('Seleccione cuenta contable compra', '' , $array_cuenta_pc);

		$defecto_cuenta_rel		= 	'';
		$defecto_cuenta_ter		= 	'';
		$defecto_cuenta_com		= 	'';

		$funcion 				= 	$this;

		return View::make('configuracionproducto/modal/ajax/mcuentacontable',
						 [		 	
						 	'combo_cuenta_rel' 		=> $combo_cuenta_rel,
						 	'combo_cuenta_ter' 		=> $combo_cuenta_ter,
						 	'combo_cuenta_com' 		=> $combo_cuenta_com,
						 	'defecto_cuenta_rel' 	=> $defecto_cuenta_rel,
						 	'defecto_cuenta_ter' 	=> $defecto_cuenta_ter,
						 	'defecto_cuenta_com' 	=> $defecto_cuenta_com,
						 	'array_productos' 		=> $array_productos,
						 	'funcion' 				=> $funcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}



	public function actionAjaxGuardarCuentaContable(Request $request)
	{
		
		$array_productos 			=   json_decode($request['array_productos'],true);
		$cuenta_contable_rel_id 	=   $request['cuenta_contable_rel_id'];
		$cuenta_contable_ter_id 	=   $request['cuenta_contable_ter_id'];
		$cuenta_contable_compra_id 	=   $request['cuenta_contable_compra_id'];
		$ind_venta_compra 			=   $request['ind_venta_compra'];
		$anio  						=   $this->anio;



		foreach ($array_productos as $key => $item) {

			$cabecera 			= 	WEBProductoEmpresa::where('producto_id','=',$item['producto_id'])
									->where('WEB.productoempresas.empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
									->first();

            if (count($cabecera)<=0) {

				$idproductoempresa 								=   $this->funciones->getCreateIdMaestra('web.productoempresas');
				$cabecera            	 						=	new WEBProductoEmpresa;
				$cabecera->id 	     	 						=   $idproductoempresa;
				if($ind_venta_compra == '1'){
					$cabecera->cuenta_contable_venta_relacionada_id 	=   $cuenta_contable_rel_id;
					$cabecera->cuenta_contable_venta_tercero_id 		=   $cuenta_contable_ter_id;
					$cabecera->cuenta_contable_compra_id 				=   '';
				}else{
					$cabecera->cuenta_contable_venta_relacionada_id 	=   '';
					$cabecera->cuenta_contable_venta_tercero_id 		=   '';
					$cabecera->cuenta_contable_compra_id 				=   $cuenta_contable_compra_id;
				}
				$cabecera->anio 								=  	$anio;
				$cabecera->producto_id 							=   $item['producto_id'];
				$cabecera->empresa_id 	 						=   Session::get('empresas_meta')->COD_EMPR;
				$cabecera->fecha_crea 	 						=   $this->fechaactual;
				$cabecera->usuario_crea 						=   Session::get('usuario_meta')->id;
				$cabecera->save();

            }else{
            	
				if($ind_venta_compra == '1'){
					$cabecera->cuenta_contable_venta_relacionada_id 	=   $cuenta_contable_rel_id;
					$cabecera->cuenta_contable_venta_tercero_id 		=   $cuenta_contable_ter_id;
				}else{
					$cabecera->cuenta_contable_compra_id 				=   $cuenta_contable_compra_id;
				}
				$cabecera->activo 								=  1;
				$cabecera->empresa_id 	 						=   Session::get('empresas_meta')->COD_EMPR;
				$cabecera->fecha_mod 	 						=   $this->fechaactual;
				$cabecera->usuario_mod 							=   Session::get('usuario_meta')->id;
				$cabecera->save();	

            }

		}
		echo('Registro de cuenta contable modificada con exito');


	}



}
