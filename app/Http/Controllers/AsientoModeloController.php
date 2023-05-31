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



use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


class AsientoModeloController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;

	public function actionListarAsientoModelo($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Modelo Asiento');
	    $sel_tipo_asiento 		=	'TODO';

	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_tipo_asiento 	= 	$this->gn_generacion_combo_categoria('TIPO_ASIENTO','Seleccione tipo asiento','TODO');
	    $listamodeloasiento 	= 	$this->am_lista_asiento_modelo(Session::get('empresas_meta')->COD_EMPR,$sel_tipo_asiento,$anio);

		$funcion 				= 	$this;
		
		return View::make('asientomodelo/listaasientomodelo',
						 [
						 	'listamodeloasiento' 	=> $listamodeloasiento,
						 	'combo_tipo_asiento'	=> $combo_tipo_asiento,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'anio'					=> $anio,
						 	'sel_tipo_asiento'	 	=> $sel_tipo_asiento,						 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}

	public function actionAjaxListarAsientoModelo(Request $request)
	{
		$tipo_asiento_id 		=   $request['tipo_asiento_id'];
		$anio 					=   $request['anio'];
		$idopcion 				=   $request['idopcion'];
	    $listamodeloasiento 	= 	$this->am_lista_asiento_modelo(Session::get('empresas_meta')->COD_EMPR,$tipo_asiento_id,$anio);
		$funcion 				= 	$this;
		return View::make('asientomodelo/ajax/alistaasientomodelo',
						 [
						 	'listamodeloasiento' 	=> $listamodeloasiento,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}




	public function actionAgregarAsientoModelo($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar Asiento Modelo');
		if($_POST)
		{


			$nombre 	 		 		= 	$request['nombre'];
			$tipo_asiento_id 	 		= 	$request['tipo_asiento_id'];
			$moneda_id 	 		 		= 	$request['moneda_id'];
			$tipo_cliente 	 		 	= 	$request['tipo_cliente'];
			$tipo_igv_id 	 		 	= 	$request['tipo_igv_id'];
			$tipo_ivap_id 	 		 	= 	$request['tipo_ivap_id'];

			$pago_cobro_id 	 		 	= 	$request['pago_cobro_id'];			
			$idasientomodelo 			=   $this->funciones->getCreateIdMaestra('web.asientomodelos');
			$anio  						=   $this->anio;
			
			$cabecera            	 	=	new WEBAsientoModelo;
			$cabecera->id 	     	 	=   $idasientomodelo;
			$cabecera->nombre 	   		=   $nombre;
			$cabecera->tipo_asiento_id 	=   $tipo_asiento_id;
			$cabecera->tipo_cliente 	=   $tipo_cliente;
			$cabecera->moneda_id 	   	=   $moneda_id;
			$cabecera->tipo_igv_id 	   	=   $tipo_igv_id;
			$cabecera->tipo_ivap_id 	=   $tipo_ivap_id;
			$cabecera->pago_cobro_id 	=   $pago_cobro_id;
			$cabecera->anio 	   		=   $anio;
			$cabecera->empresa_id 	 	=   Session::get('empresas_meta')->COD_EMPR;
			$cabecera->fecha_crea 	 	=   $this->fechaactual;
			$cabecera->usuario_crea 	=   Session::get('usuario_meta')->id;
			$cabecera->save();
 
			//tipo documento
			$tipo_documento 	= $request['tipo_documento'];
			$this->am_agregar_modificar_asiento_modelo_referencia($tipo_documento,'TIPO_DOCUMENTO',$idasientomodelo,$this->fechaactual);

 		 	return Redirect::to('/gestion-asiento-modelo/'.$idopcion)->with('bienhecho', 'Asiento Modelo '.$nombre.' registrado con exito');

		}else{

			$combo_moneda 			= 	$this->gn_generacion_combo_categoria('MONEDA','Seleccione moneda','');
			$combo_tipo_asiento 	= 	$this->gn_generacion_combo_categoria('TIPO_ASIENTO','Seleccione tipo asiento','');
			$combo_tipo_cliente 	= 	$this->gn_combo_tipo_cliente();
			$combo_tipo_documento	= 	$this->gn_generacion_combo_tabla_osiris('STD.TIPO_DOCUMENTO','COD_TIPO_DOCUMENTO','TXT_TIPO_DOCUMENTO','','');

			$combo_tipo_igv 		= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_IGV','Seleccione tipo igv','');
			$combo_tipo_ivap 		= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_IVAP','Seleccione tipo ivap','');

			$combo_pago_cobro 		= 	$this->gn_generacion_combo_categoria('ENTIDAD_PAGO_COBRO','Seleccione pago o cobro','');
			$defecto_tipo_asiento 	= 	'';
			$defecto_moneda			= 	'';
			$defecto_tipo_cliente	= 	'';
			$defecto_tipo_igv		= 	'';
			$defecto_tipo_ivap		= 	'';

			$defecto_tipo_documento	= 	'-1';
			$defecto_pago_cobro		= 	'';


			return View::make('asientomodelo/agregarasientomodelo',
						[
							'combo_moneda'  		=> $combo_moneda,
							'combo_tipo_asiento'  	=> $combo_tipo_asiento,
							'combo_tipo_cliente'  	=> $combo_tipo_cliente,
							'combo_tipo_documento'  => $combo_tipo_documento,
							'combo_tipo_igv'  		=> $combo_tipo_igv,
							'combo_tipo_ivap'  		=> $combo_tipo_ivap,
							'combo_pago_cobro'  	=> $combo_pago_cobro,
							'defecto_tipo_asiento'  => $defecto_tipo_asiento,
		        			'defecto_moneda'  		=> $defecto_moneda,
		        			'defecto_tipo_cliente'  => $defecto_tipo_cliente,
		        			'defecto_tipo_documento'=> $defecto_tipo_documento,	
		        			'defecto_tipo_igv'		=> $defecto_tipo_igv,
		        			'defecto_tipo_ivap'		=> $defecto_tipo_ivap,
		        			'defecto_pago_cobro'	=> $defecto_pago_cobro,	
						  	'idopcion'  			=> $idopcion
						]);
		}
	}


	public function actionModificarAsientoModelo($idopcion,$idasientomodelo,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idasientomodelo = $this->funciones->decodificarmaestra($idasientomodelo);
	    View::share('titulo','Modificar Asiento modelo');

		if($_POST)
		{

			$nombre 	 		 	 	= 	$request['nombre'];
			$tipo_asiento_id 	 	 	= 	$request['tipo_asiento_id'];
			$moneda_id 	 		 	 	= 	$request['moneda_id'];
			$activo 	 		 	 	= 	$request['activo'];
			$tipo_cliente 	 		 	= 	$request['tipo_cliente'];
			$tipo_igv_id 	 		 	= 	$request['tipo_igv_id'];
			$pago_cobro_id 	 		 	= 	$request['pago_cobro_id'];
			$tipo_ivap_id 	 		 	= 	$request['tipo_ivap_id'];


			$cabecera            	 	=	WEBAsientoModelo::find($idasientomodelo);
			$cabecera->nombre 	   		=   $nombre;
			$cabecera->tipo_asiento_id 	=   $tipo_asiento_id;
			$cabecera->moneda_id 	   	=   $moneda_id;
			$cabecera->tipo_cliente 	=   $tipo_cliente;
			$cabecera->tipo_igv_id 	   	=   $tipo_igv_id;
			$cabecera->tipo_ivap_id 	=   $tipo_ivap_id;
			$cabecera->pago_cobro_id 	=   $pago_cobro_id;
			$cabecera->activo 	 	 	=   $activo;
			$cabecera->fecha_mod 	 	=   $this->fechaactual;
			$cabecera->usuario_mod 		=   Session::get('usuario_meta')->id;
			$cabecera->save();
 
 			//tipo documento
			$tipo_documento 			= 	$request['tipo_documento'];
			$this->am_agregar_modificar_asiento_modelo_referencia($tipo_documento,'TIPO_DOCUMENTO',$idasientomodelo,$this->fechaactual);



 			return Redirect::to('/gestion-asiento-modelo/'.$idopcion)->with('bienhecho', 'Asiento Modelo '.$nombre.' modificado con éxito');

		}else{

			$combo_moneda 				= 	$this->gn_generacion_combo_categoria('MONEDA','Seleccione moneda','');
			$combo_tipo_asiento 		= 	$this->gn_generacion_combo_categoria('TIPO_ASIENTO','Seleccione tipo asiento','');
			$combo_tipo_cliente 		= 	$this->gn_combo_tipo_cliente();
			$combo_tipo_documento		= 	$this->gn_generacion_combo_tabla_osiris('STD.TIPO_DOCUMENTO','COD_TIPO_DOCUMENTO','TXT_TIPO_DOCUMENTO','','');
			$combo_tipo_igv 			= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_IGV','Seleccione tipo igv','');
			$combo_pago_cobro 			= 	$this->gn_generacion_combo_categoria('ENTIDAD_PAGO_COBRO','Seleccione pago o cobro','');
			$combo_tipo_ivap 			= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_IVAP','Seleccione tipo ivap','');


			$asientomodelo 				= 	WEBAsientoModelo::where('id', $idasientomodelo)->first();
			$defecto_tipo_asiento 		= 	$asientomodelo->tipo_asiento_id;
			$defecto_moneda				= 	$asientomodelo->moneda_id;
			$defecto_tipo_cliente		= 	$asientomodelo->tipo_cliente;
			$array_tipo_documento 		=   $this->am_array_asiento_modelo_referencia_xreferencia('TIPO_DOCUMENTO',$idasientomodelo);
			$defecto_tipo_documento		= 	$array_tipo_documento;
			$defecto_tipo_igv			= 	$asientomodelo->tipo_igv_id;
			$defecto_pago_cobro			= 	$asientomodelo->pago_cobro_id;
			$defecto_tipo_ivap			= 	$asientomodelo->tipo_ivap_id;

	        return View::make('asientomodelo/modificarasientomodelo', 
	        				[
	        					'combo_moneda'  		=> $combo_moneda,
	        					'combo_tipo_asiento'  	=> $combo_tipo_asiento,
	        					'combo_tipo_cliente'  	=> $combo_tipo_cliente,
								'combo_tipo_documento'  => $combo_tipo_documento,
								'combo_tipo_igv'  		=> $combo_tipo_igv,
								'combo_tipo_ivap'  		=> $combo_tipo_ivap,
								'combo_pago_cobro'  	=> $combo_pago_cobro,
	        					'asientomodelo'  		=> $asientomodelo,
	        					'defecto_tipo_asiento'  => $defecto_tipo_asiento,
	        					'defecto_moneda'  		=> $defecto_moneda,
	        					'defecto_tipo_cliente'  => $defecto_tipo_cliente,
		        				'defecto_tipo_documento'=> $defecto_tipo_documento,	
		        				'defecto_tipo_igv' 		=> $defecto_tipo_igv,
		        				'defecto_tipo_ivap' 	=> $defecto_tipo_ivap,

		        				'defecto_pago_cobro' 	=> $defecto_pago_cobro,		
					  			'idopcion' 				=> $idopcion
	        				]);
		}
	}

	public function actionConfigurarAsientoModelo($idopcion,$idasientomodelo,Request $request)
	{

		$sdidasientomodelo = $idasientomodelo;
	    $idasientomodelo = $this->funciones->decodificarmaestra($idasientomodelo);
	    View::share('titulo','Configurar Asiento modelo');
		$asientomodelo 						= 	WEBAsientoModelo::where('id', $idasientomodelo)->first();

		if($_POST)
		{


			$activo 	 		 	 		= 	$request['activo'];
			$partida_id 	 		 		= 	$request['partida_id'];
			$orden 	 	 					= 	$request['orden'];
			$cuenta_contable_id 	 		= 	$request['cuenta_contable_id'];
			$asiento_modelo_detalle_id 	 	= 	$request['asiento_modelo_detalle_id'];
			$cuentacontable 				= 	WEBCuentaContable::where('id', $cuenta_contable_id)->first();

			//agregar cuenta contable
			if(trim($asiento_modelo_detalle_id)==''){
				
				$idasientomodelodetalle 					=   $this->funciones->getCreateIdMaestra('web.asientomodelodetalles');
				$cabecera            	 					=	new WEBAsientoModeloDetalle;
				$cabecera->id 	     	 					=   $idasientomodelodetalle;
				$cabecera->orden 	   						=   $orden;
				$cabecera->moneda_id 						=   $asientomodelo->moneda_id;
				$cabecera->cuenta_contable_id 				=   $cuenta_contable_id;
				$cabecera->asiento_modelo_id 				=   $idasientomodelo;
				$cabecera->activo 	   						=   $activo;
				$cabecera->partida_id 	   					=   $partida_id;
				$cabecera->empresa_id 	 					=   Session::get('empresas_meta')->COD_EMPR;
				$cabecera->fecha_crea 	 					=   $this->fechaactual;
				$cabecera->usuario_crea 					=   Session::get('usuario_meta')->id;
				$cabecera->save();

			}else{
				//modificar cuenta contable
				$asientomodelodetalle						= 	WEBAsientoModeloDetalle::where('id', $asiento_modelo_detalle_id)->first();
				$asientomodelodetalle->orden 	   			=   $orden;
				$asientomodelodetalle->moneda_id 			=   $asientomodelo->moneda_id;
				$asientomodelodetalle->cuenta_contable_id 	=   $cuenta_contable_id;
				$asientomodelodetalle->partida_id 	   		=   $partida_id;
				$asientomodelodetalle->activo 	   			=   $activo;
				$asientomodelodetalle->fecha_mod 	 		=   $this->fechaactual;
				$asientomodelodetalle->usuario_mod 			=   Session::get('usuario_meta')->id;
				$asientomodelodetalle->save();

			}


 			return Redirect::to('/configurar-asiento-modelo/'.$idopcion.'/'.$sdidasientomodelo)->with('bienhecho', 'Cuenta contable '.$cuentacontable->nombre.' agregada con éxito');

		}else{

			$listaasientomodelodetalle 	= 	WEBAsientoModeloDetalle::where('asiento_modelo_id', $idasientomodelo)
											->where('activo','=',1)
											->orderBy('orden', 'asc')
											->get();
			$funcion 					= 	$this;

	        return View::make('asientomodelo/configurarasientomodelo', 
	        				[
	        					'asientomodelo'  			 => $asientomodelo,
	        					'listaasientomodelodetalle'  => $listaasientomodelodetalle,
	        					'funcion'  					 => $funcion,
					  			'idopcion' 					 => $idopcion
	        				]);
		}
	}


	public function actionAjaxModalConfiguracionAsientoModelo(Request $request)
	{
		$asiento_contable_id 	=   $request['asiento_contable_id'];
		$idopcion 				=   $request['idopcion'];
		$anio  					=   $this->anio;

		$asientomodelo 			= 	WEBAsientoModelo::where('id', $asiento_contable_id)->first();
        $array_nivel_pc     	= 	$this->pc_array_nivel_cuentas_contable(Session::get('empresas_meta')->COD_EMPR,$anio);
		$combo_nivel_pc  		= 	$this->gn_generacion_combo_array('Seleccione nivel', '' , $array_nivel_pc);
		$combo_cuenta  			= 	$this->gn_generacion_combo_array('Seleccione cuenta contable', '' , array());
		$max_nro_asientomodelo	=	WEBAsientoModeloDetalle::where('asiento_modelo_id', $asiento_contable_id)
									->where('activo','=',1)
									->max('orden');
		$max_nro_asientomodelo 	=   $max_nro_asientomodelo + 1;
		$combo_partida 			= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_PARTIDA','Seleccione partida','');
		$funcion 				= 	$this;
		$defecto_nivel 			= 	'';
		$defecto_cuenta			= 	'';
		$defecto_partida		= 	'';
		$asiento_modelo_detalle_id = '';

		return View::make('asientomodelo/modal/ajax/mconfiguracionasientomodelo',
						 [		 	
						 	'asientomodelo' 		=> $asientomodelo,
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'combo_nivel_pc' 		=> $combo_nivel_pc,
						 	'combo_cuenta' 			=> $combo_cuenta,
						 	'combo_partida' 		=> $combo_partida,
						 	'defecto_nivel' 		=> $defecto_nivel,
						 	'defecto_cuenta' 		=> $defecto_cuenta,
						 	'defecto_partida' 		=> $defecto_partida,
						 	'max_nro_asientomodelo' => $max_nro_asientomodelo,
						 	'asiento_modelo_detalle_id' => $asiento_modelo_detalle_id,
						 	'ajax' 					=> true,						 	
						 ]);
	}

	public function actionAjaxModalModificarConfiguracionAsientoModelo(Request $request)
	{
		$asiento_contable_id 		=   $request['asiento_modelo_id'];
		$asiento_modelo_detalle_id 	=   $request['asiento_modelo_detalle_id'];
		$idopcion 					=   $request['idopcion'];
		$anio  						=   $this->anio;

		$asientomodelodetalle	=	WEBAsientoModeloDetalle::where('id', $asiento_modelo_detalle_id)->first();
		$asientomodelo 			= 	WEBAsientoModelo::where('id', $asiento_contable_id)->first();
        $array_nivel_pc     	= 	$this->pc_array_nivel_cuentas_contable(Session::get('empresas_meta')->COD_EMPR,$anio);
		$combo_nivel_pc  		= 	$this->gn_generacion_combo_array('Seleccione nivel', '' , $array_nivel_pc);

		$array_cuenta 	    	= 	$this->pc_array_nro_cuentas_nombre_xnivel(Session::get('empresas_meta')->COD_EMPR,$asientomodelodetalle->cuentacontable->nivel,$anio);
		$combo_cuenta  			= 	$this->gn_generacion_combo_array('Seleccione cuenta contable', '' , $array_cuenta);

		$combo_partida 			= 	$this->gn_generacion_combo_categoria('CONTABILIDAD_PARTIDA','Seleccione partida','');
		$funcion 				= 	$this;

		$defecto_nivel 			= 	$asientomodelodetalle->cuentacontable->nivel;
		$defecto_cuenta			= 	trim($asientomodelodetalle->cuenta_contable_id);
		$defecto_partida		= 	$asientomodelodetalle->partida_id;
		$max_nro_asientomodelo 	=   $asientomodelodetalle->orden;


		return View::make('asientomodelo/modal/ajax/mconfiguracionasientomodelo',
						 [		 	
						 	'asientomodelo' 		=> $asientomodelo,
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'combo_nivel_pc' 		=> $combo_nivel_pc,
						 	'combo_cuenta' 			=> $combo_cuenta,
						 	'combo_partida' 		=> $combo_partida,
						 	'defecto_nivel' 		=> $defecto_nivel,
						 	'defecto_cuenta' 		=> $defecto_cuenta,
						 	'defecto_partida' 		=> $defecto_partida,
						 	'max_nro_asientomodelo' => $max_nro_asientomodelo,
						 	'asientomodelodetalle' 	=> $asientomodelodetalle,
						 	'asiento_modelo_detalle_id' => $asiento_modelo_detalle_id,
						 	'ajax' 					=> true,						 	
						 ]);
	}





}
