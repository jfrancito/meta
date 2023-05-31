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




	public function actionListarConfiguracionProductoMenu($idopcion)
	{
		$tipo_asiento = '3';
		$anio  		  =  $this->anio;
		return Redirect::to('/gestion-configuracion-producto/'.$idopcion.'/'.$tipo_asiento.'/'.$anio);
	}

	public function actionListarConfiguracionProducto($idopcion,$tipo_asiento,$anio)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Configuración Producto');


	    $nro_asiento 	 =  $tipo_asiento;
	    $nombre_asiento  =  '';
	    if($tipo_asiento == '3'){$tipo_asiento = 'TAS0000000000003'; $nombre_asiento  =  'Productos de Ventas por configurar';}
	    if($tipo_asiento == '4'){$tipo_asiento = 'TAS0000000000004'; $nombre_asiento  =  'Productos de Compras por configurar';}

	    $combo_producto  				= 	$this->gn_generacion_combo_productos('Seleccione producto', '');
		$funcion 						= 	$this;
	    $anio  							=   $anio;
        $array_anio_pc     				= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  				= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$empresa_id 					=   Session::get('empresas_meta')->COD_EMPR;


		$lista_productos_sc 		 	= 	$this->mv_lista_productos_sin_configuracion($tipo_asiento,$empresa_id,$anio);

		if(count($lista_productos_sc)<=0){
			$lista_productos_sc 		 	= 	$this->mv_lista_productos_configurados($tipo_asiento,$empresa_id,$anio);
		}

		//dd($lista_productos_sc);

		$array_productos_empresa    	=	ALMProducto::whereIn('COD_PRODUCTO',$lista_productos_sc)
											->pluck('COD_PRODUCTO')
											->toArray();

		$producto_id 					=	'';									
		$servicio_id 					=	'';
		$material_id 					=	'';	
		$serviciomaterial 				=	'';	


		$lista_configuracion_producto 	= 	$this->cp_lista_productos_configuracion($empresa_id, $anio,$array_productos_empresa,$producto_id,$servicio_id,$material_id,$serviciomaterial);



		$defecto_producto				= 	'';

		$sel_categoria_producto 		=	'';
	    $combo_categoria_producto 		= 	$this->gn_generacion_combo_categoria('CATEGORIA_PRODUCTO','Seleccione categoria producto','');

		$sel_sub_categoria_id 			=	'';
	    $combo_sub_categoria 			= 	array();


		return View::make('configuracionproducto/listaconfiguracionproducto',
						 [
						 	'combo_producto' 					=> $combo_producto,						 	
						 	'idopcion' 							=> $idopcion,
						 	'funcion' 							=> $funcion,	
						 	'defecto_producto' 					=> $defecto_producto,
						 	'anio' 								=> $anio,	
						 	'combo_anio_pc' 					=> $combo_anio_pc,
						 	'sel_categoria_producto' 			=> $sel_categoria_producto,	
						 	'combo_categoria_producto' 			=> $combo_categoria_producto,
						 	'sel_sub_categoria_id' 				=> $sel_sub_categoria_id,	
						 	'combo_sub_categoria' 				=> $combo_sub_categoria,
						 	'lista_configuracion_producto' 		=> $lista_configuracion_producto,	
						 	'nombre_asiento' 					=> $nombre_asiento,
						 	'nro_asiento' 						=> $nro_asiento,
						 	'empresa_id' 						=> $empresa_id,

						 ]);
	}


	public function actionAjaxConfiguracionProducto(Request $request)
	{
		$producto_id 					=   $request['producto_id'];
		$anio 							=   $request['anio'];
		$idopcion 						=   $request['idopcion'];
		$categoria_producto_id 			=   $request['categoria_producto_id'];
		$sub_categoria_id 				=   $request['sub_categoria_id'];
		$nro_asiento 					=   $request['nro_asiento'];


		$material_id					=	'';
		$servicio_id					=	'';

		$serviciomaterial				=	'';

		if($categoria_producto_id=='CTP0000000000001'){
			$servicio_id				=	$sub_categoria_id;
			$serviciomaterial			=	'S';
		}
		if($categoria_producto_id=='CTP0000000000002'){
			$material_id				=	$sub_categoria_id;
			$serviciomaterial			=	'M';

		}

		$productoempresa 				= 	WEBProductoEmpresa::where('producto_id','=',$producto_id)->first();

		// $array_productos_empresa    	=	ALMProducto::CodProducto($producto_id)
		// 									->CodServicio($servicio_id)
		// 									->CodMaterial($material_id)
		// 									->where('IND_MATERIAL_SERVICIO','=',$serviciomaterial)
		// 									->where('COD_ESTADO','=',1)
		// 									->pluck('COD_PRODUCTO')
		// 									->toArray();

		$array_productos_empresa  		=	array();

		//dd($array_productos_empresa);
		$empresa_id 					=   Session::get('empresas_meta')->COD_EMPR;

		$lista_configuracion_producto 	= 	$this->cp_lista_productos_configuracion($empresa_id, $anio,$array_productos_empresa,$producto_id,$servicio_id,$material_id,$serviciomaterial);

		$funcion 						= 	$this;
	    $nombre_asiento  =  '';


		return View::make('configuracionproducto/ajax/alistaconfiguracionproducto',
						 [
						 	'lista_configuracion_producto' 	=> $lista_configuracion_producto,					 	
						 	'idopcion' 						=> $idopcion,
						 	'funcion' 						=> $funcion,
						 	'nombre_asiento' 				=> $nombre_asiento,
						 	'nro_asiento' 					=> $nro_asiento,
						 	'ajax' 							=> true,						 	
						 ]);
	}


	public function actionAjaxComboServicioMaterial(Request $request)
	{
		$categoria_producto_id 			=   $request['categoria_producto_id'];

		//SERVICIO
		if($categoria_producto_id=='CTP0000000000001'){

			$sel_sub_categoria_id = '';
			$array_sub_categoria  = ALMProducto::join('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_SERVICIO')
									->where('ALM.PRODUCTO.COD_CATEGORIA_SERVICIO','<>','')
									->where('ALM.PRODUCTO.COD_ESTADO','=',1)
									->where('ALM.PRODUCTO.IND_MATERIAL_SERVICIO','=','S')
									->groupBy('CMP.CATEGORIA.COD_CATEGORIA')
									->groupBy('CMP.CATEGORIA.NOM_CATEGORIA')
        							->select(DB::raw("CMP.CATEGORIA.COD_CATEGORIA,CMP.CATEGORIA.NOM_CATEGORIA"))
		        					->pluck('NOM_CATEGORIA','COD_CATEGORIA')
									->toArray();

			$combo_sub_categoria  = 	array('' => 'Seleccione una sub categoria') + $array_sub_categoria;

		}else{


		//MATERIAL
			$sel_sub_categoria_id = '';
			$array_sub_categoria  = ALMProducto::join('CMP.CATEGORIA', 'CMP.CATEGORIA.COD_CATEGORIA', '=', 'ALM.PRODUCTO.COD_CATEGORIA_SUB_FAMILIA')
									->where('ALM.PRODUCTO.COD_CATEGORIA_SUB_FAMILIA','<>','')
									->where('ALM.PRODUCTO.COD_ESTADO','=',1)
									->where('ALM.PRODUCTO.IND_MATERIAL_SERVICIO','=','M')
									->groupBy('CMP.CATEGORIA.COD_CATEGORIA')
									->groupBy('CMP.CATEGORIA.NOM_CATEGORIA')
        							->select(DB::raw("CMP.CATEGORIA.COD_CATEGORIA,CMP.CATEGORIA.NOM_CATEGORIA"))
		        					->pluck('NOM_CATEGORIA','COD_CATEGORIA')
									->toArray();

			$combo_sub_categoria  = 	array('' => 'Seleccione una sub categoria') + $array_sub_categoria;

		}


		return View::make('general/combo/ccategoriams',
						 [
					 	
						 	'sel_sub_categoria_id' 			=> $sel_sub_categoria_id,
						 	'combo_sub_categoria' 			=> $combo_sub_categoria,
						 	'ajax' 							=> true,						 	
						 ]);
	}



	public function actionAjaxModalConfiguracionProductoCuentaContable(Request $request)
	{
		
		$array_productos 		=   json_encode($request['array_productos'],false);
		$nro_asiento 			=   $request['nro_asiento'];


		$anio  					=   $this->anio;

		$array_cuenta_pc     	= 	$this->pc_array_nro_cuentas_nombre(Session::get('empresas_meta')->COD_EMPR,$anio);

		$combo_cuenta_rel		= 	$this->gn_generacion_combo_array('Seleccione cuenta contable relacionada', '' , $array_cuenta_pc);
		$combo_cuenta_ter		= 	$this->gn_generacion_combo_array('Seleccione cuenta contable tercero', '' , $array_cuenta_pc);

		$combo_cuenta_rel_sv	= 	$this->gn_generacion_combo_array('Seleccione cuenta contable relacionada sv', '' , $array_cuenta_pc);
		$combo_cuenta_ter_sv	= 	$this->gn_generacion_combo_array('Seleccione cuenta contable tercero sv', '' , $array_cuenta_pc);


		$combo_cuenta_com		= 	$this->gn_generacion_combo_array('Seleccione cuenta contable compra', '' , $array_cuenta_pc);

		$defecto_cuenta_rel		= 	'';
		$defecto_cuenta_ter		= 	'';
		$defecto_cuenta_rel_sv	= 	'';
		$defecto_cuenta_ter_sv	= 	'';

		$defecto_cuenta_com		= 	'';


		$ocultar_venta 			=	'';
		$ocultar_compra 		=	'';
		if($nro_asiento=='4'){$ocultar_venta = 'ocultar';}
		if($nro_asiento=='3'){$ocultar_compra = 'ocultar';}
		$empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;

		$funcion 				= 	$this;

		return View::make('configuracionproducto/modal/ajax/mcuentacontable',
						 [		 	
						 	'combo_cuenta_rel' 		=> $combo_cuenta_rel,
						 	'combo_cuenta_ter' 		=> $combo_cuenta_ter,
						 	'combo_cuenta_rel_sv' 	=> $combo_cuenta_rel_sv,
						 	'combo_cuenta_ter_sv' 	=> $combo_cuenta_ter_sv,

						 	'combo_cuenta_com' 		=> $combo_cuenta_com,

						 	'defecto_cuenta_rel' 	=> $defecto_cuenta_rel,
						 	'defecto_cuenta_ter' 	=> $defecto_cuenta_ter,

						 	'defecto_cuenta_rel_sv' => $defecto_cuenta_rel_sv,
						 	'defecto_cuenta_ter_sv' => $defecto_cuenta_ter_sv,

						 	'empresa_id' 			=> $empresa_id,

						 	'defecto_cuenta_com' 	=> $defecto_cuenta_com,
						 	'array_productos' 		=> $array_productos,
						 	'nro_asiento' 			=> $nro_asiento,
						 	'ocultar_venta' 		=> $ocultar_venta,
						 	'ocultar_compra' 		=> $ocultar_compra,
						 	'funcion' 				=> $funcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}


	public function actionAjaxModalConfiguracionProductoCodigoMigracion(Request $request)
	{
		
		$array_productos 		=   json_encode($request['array_productos'],false);
		$anio  					=   $this->anio;


		$funcion 				= 	$this;

		return View::make('configuracionproducto/modal/ajax/mcodigomigracion',
						 [		 	
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

		$cuenta_contable_rel_sv_id 	=   $request['cuenta_contable_rel_sv_id'];
		$cuenta_contable_ter_sv_id 	=   $request['cuenta_contable_ter_sv_id'];


		$cuenta_contable_compra_id 	=   $request['cuenta_contable_compra_id'];
		$ind_venta_compra 			=   $request['ind_venta_compra'];
		$anio  						=   $request['anio'];;

		//dd($anio);

		foreach ($array_productos as $key => $item) {

			$cabecera 			= 	WEBProductoEmpresa::where('producto_id','=',$item['producto_id'])
									->where('anio','=',$anio)
									->where('WEB.productoempresas.empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
									->first();

            if (count($cabecera)<=0) {

				$idproductoempresa 								=   $this->funciones->getCreateIdMaestra('web.productoempresas');
				$cabecera            	 						=	new WEBProductoEmpresa;
				$cabecera->id 	     	 						=   $idproductoempresa;

				if($ind_venta_compra == '1'){
					$cabecera->cuenta_contable_venta_relacionada_id 	=   $cuenta_contable_rel_id;
					$cabecera->cuenta_contable_venta_tercero_id 		=   $cuenta_contable_ter_id;
					$cabecera->cuenta_contable_venta_segunda_relacionada_id 	=   $cuenta_contable_rel_sv_id;
					$cabecera->cuenta_contable_venta_segunda_tercero_id 		=   $cuenta_contable_ter_sv_id;

					$cabecera->cuenta_contable_compra_id 				=   '';
				}else{
					$cabecera->cuenta_contable_venta_relacionada_id 	=   '';
					$cabecera->cuenta_contable_venta_tercero_id 		=   '';
					$cabecera->cuenta_contable_venta_segunda_relacionada_id 	=   '';
					$cabecera->cuenta_contable_venta_segunda_tercero_id 		=   '';

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
					$cabecera->cuenta_contable_venta_segunda_relacionada_id 	=   $cuenta_contable_rel_sv_id;
					$cabecera->cuenta_contable_venta_segunda_tercero_id 		=   $cuenta_contable_ter_sv_id;

				}else{
					$cabecera->cuenta_contable_compra_id 				=   $cuenta_contable_compra_id;
				}
				$cabecera->activo 								=   1;
				$cabecera->empresa_id 	 						=   Session::get('empresas_meta')->COD_EMPR;
				$cabecera->fecha_mod 	 						=   $this->fechaactual;
				$cabecera->usuario_mod 							=   Session::get('usuario_meta')->id;
				$cabecera->save();	

            }

		}
		echo('Registro de cuenta contable modificada con exito');

	}

	public function actionAjaxGuardarCodigoMigracion(Request $request)
	{
		
		$array_productos 			=   json_decode($request['array_productos'],true);
		$codigo_migracion 			=   $request['codigo_migracion'];
		$anio  						=   $request['anio'];



		foreach ($array_productos as $key => $item) {

			$cabecera 			= 	WEBProductoEmpresa::where('producto_id','=',$item['producto_id'])
									->where('anio','=',$anio)
									->where('WEB.productoempresas.empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
									->first();

            if (count($cabecera)<=0) {

				$idproductoempresa 								=   $this->funciones->getCreateIdMaestra('web.productoempresas');
				$cabecera            	 						=	new WEBProductoEmpresa;
				$cabecera->id 	     	 						=   $idproductoempresa;

				$cabecera->cuenta_contable_venta_relacionada_id =   '';
				$cabecera->cuenta_contable_venta_tercero_id 	=   '';
				$cabecera->cuenta_contable_compra_id 			=   '';
				$cabecera->codigo_migracion 					=   $codigo_migracion;
				$cabecera->anio 								=  	$anio;
				$cabecera->producto_id 							=   $item['producto_id'];
				$cabecera->empresa_id 	 						=   Session::get('empresas_meta')->COD_EMPR;
				$cabecera->fecha_crea 	 						=   $this->fechaactual;
				$cabecera->usuario_crea 						=   Session::get('usuario_meta')->id;
				$cabecera->save();

            }else{
            	
				$cabecera->codigo_migracion 					=   $codigo_migracion;
				$cabecera->activo 								=  1;
				$cabecera->empresa_id 	 						=   Session::get('empresas_meta')->COD_EMPR;
				$cabecera->fecha_mod 	 						=   $this->fechaactual;
				$cabecera->usuario_mod 							=   Session::get('usuario_meta')->id;
				$cabecera->save();	

            }

		}
		echo('Registro de codigo migracion modificada con exito');


	}

}
