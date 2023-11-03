<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\User,App\Modelos\WEBGrupoopcion,App\Modelos\WEBRol,App\Modelos\WEBRolOpcion,App\Modelos\WEBOpcion,App\Modelos\WEBListaPersonal,App\Modelos\WEBPedido,App\Modelos\WEBDetallePedido;
use App\Modelos\ALMCentro,App\Modelos\STDEmpresa,App\Modelos\WEBUserEmpresaCentro;
use View;
use Session;
use Hashids;

use App\Traits\GeneralesTraits;
use App\Traits\MigrarVentaTraits;
use App\Traits\AlertaTraits;
use App\Traits\PlanContableTraits;

class UserController extends Controller
{

	use MigrarVentaTraits;
	use AlertaTraits;
	use GeneralesTraits;
	use PlanContableTraits;

    public function actionLogin(Request $request){

		if($_POST)
		{
			/**** Validaciones laravel ****/
			$this->validate($request, [
	            'name' => 'required',
	            'password' => 'required',

			], [
            	'name.required' => 'El campo Usuario es obligatorio',
            	'password.required' => 'El campo Clave es obligatorio',
        	]);

			/**********************************************************/
			
			$usuario 	 				 = strtoupper($request['name']);
			$clave   	 				 = strtoupper($request['password']);
			$local_id  	 				 = $request['local_id'];

			$tusuario    				 = 	User::whereRaw('UPPER(name)=?',[$usuario])
											//->where('activo','=',1)
											->first();

			if(count($tusuario)>0)
			{
				$clavedesifrada 		 = 	strtoupper(Crypt::decrypt($tusuario->password));

				if($clavedesifrada == $clave)
				{

					$listamenu    		 = 	WEBGrupoopcion::join('web.opciones', 'web.opciones.grupoopcion_id', '=', 'web.grupoopciones.id')
											->join('web.rolopciones', 'web.rolopciones.opcion_id', '=', 'web.opciones.id')
											->where('web.grupoopciones.activo', '=', 1)
											->where('web.rolopciones.rol_id', '=', $tusuario->rol_id)
											->where('web.rolopciones.ver', '=', 1)
											->where('web.opciones.ind_meta', '=', 1)
											->groupBy('web.grupoopciones.id')
											->groupBy('web.grupoopciones.nombre')
											->groupBy('web.grupoopciones.icono')
											->groupBy('web.grupoopciones.orden')
											->select('web.grupoopciones.id','web.grupoopciones.nombre','web.grupoopciones.icono','web.grupoopciones.orden')
											->orderBy('web.grupoopciones.orden', 'asc')
											->get();

											

					$listaopciones    	= 	WEBRolOpcion::where('rol_id', '=', $tusuario->rol_id)
											->where('ver', '=', 1)
											->orderBy('orden', 'asc')
											->pluck('opcion_id')
											->toArray();


					Session::put('usuario_meta', $tusuario);
					Session::put('listamenu_meta', $listamenu);
					Session::put('listaopciones_meta', $listaopciones);

					return Redirect::to('acceso');
					
						
				}else{
					return Redirect::back()->withInput()->with('errorbd', 'Usuario o clave incorrecto');
				}	
			}else{
				return Redirect::back()->withInput()->with('errorbd', 'Usuario o clave incorrecto');
			}						    

		}else{

			return view('usuario.login');
		}
    }


	public function actionAcceso()
	{

		$accesos  	= 	WEBUserEmpresaCentro::where('activo','=',1)
						->where('usuario_id','=',Session::get('usuario_meta')->id)
						->select(DB::raw('empresa_id'))
						->groupBy('empresa_id')
						->get();

		$funcion 	=   $this;

		return View::make('acceso',
						 [
						 	'accesos' => $accesos,
						 	'funcion' => $funcion,
						 ]);

	}

	public function actionAccesoBienvenido($idempresa)
	{
		
		$empresas 	= 	STDEmpresa::where('COD_EMPR','=',$idempresa)
						->where('COD_ESTADO','=','1')->where('IND_SISTEMA','=','1')->first(); 
		$color 		=   $this->funciones->color_empresa($empresas->COD_EMPR);

		Session::put('color_meta', $color);
		Session::put('empresas_meta', $empresas);


		return Redirect::to('bienvenido');

	}



	public function actionAjaxListarObservacionesAsiento(Request $request)
	{

		$anio 							=   $request['anio'];

	    $tipo_asiento_venta 			=	'TAS0000000000003';
	    $tipo_asiento_compra 			=	'TAS0000000000004';

		$lista_ventas 					= 	$this->mv_lista_ventas_observadas($tipo_asiento_venta,Session::get('empresas_meta')->COD_EMPR,$anio);
		$lista_productos_sc 		 	= 	$this->mv_lista_productos_sin_configuracion($tipo_asiento_venta,Session::get('empresas_meta')->COD_EMPR,$anio);

		$lista_compras 					= 	$this->mv_lista_ventas_observadas($tipo_asiento_compra,Session::get('empresas_meta')->COD_EMPR,$anio);
		$lista_productos_sc_comp 		= 	$this->mv_lista_productos_sin_configuracion($tipo_asiento_compra,Session::get('empresas_meta')->COD_EMPR,$anio);


		$funcion 						= 	$this;

		return View::make('usuario/ajax/alistaobservacionesasiento',
						 [
						 	'lista_ventas' 		 			=> $lista_ventas,
						 	'lista_productos_sc' 			=> $lista_productos_sc,
						 	'lista_compras' 	 			=> $lista_compras,
						 	'lista_productos_sc_comp' 		=> $lista_productos_sc_comp,
						 	'anio' 				 			=> $anio,
						 	'funcion' 						=> $funcion,
						 	'ajax' 							=> true,						 	
						 ]);
	}

	public function actionBienvenido()
	{

		View::share('titulo','Bienvenido Sistema Contable "☯ META"');

	    $anio  							=   $this->anio;

	    $tipo_asiento_venta 			=	'TAS0000000000003';
	    $tipo_asiento_compra 			=	'TAS0000000000004';
	    $empresa_id 					= 	Session::get('empresas_meta')->COD_EMPR;


		$lista_ventas 					= 	$this->mv_lista_ventas_observadas($tipo_asiento_venta,Session::get('empresas_meta')->COD_EMPR,$anio);
		$lista_productos_sc 		 	= 	$this->mv_lista_productos_sin_configuracion($tipo_asiento_venta,Session::get('empresas_meta')->COD_EMPR,$anio);

		$lista_documento_sin_enviar 	= 	$this->al_lista_documentos_sin_enviar_agrupado(Session::get('empresas_meta')->COD_EMPR);
		$lista_documento_correlativo 	= 	$this->al_lista_documentos_correlativos_faltante_agrupado(Session::get('empresas_meta')->COD_EMPR);

		$lista_compras 					= 	$this->mv_lista_ventas_observadas($tipo_asiento_compra,Session::get('empresas_meta')->COD_EMPR,$anio);
		$lista_productos_sc_comp 		= 	$this->mv_lista_productos_sin_configuracion($tipo_asiento_compra,Session::get('empresas_meta')->COD_EMPR,$anio);

		//dd($lista_productos_sc_comp);

        $array_anio_pc     				= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  				= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);

		//indicadores
		$diferencimontos  				= 	$this->indicadores_asientos_contables($empresa_id, 'DIFERENCIA_MONTOS');
		$documentositemcero  			= 	$this->indicadores_asientos_contables($empresa_id, 'DOCUMENTO_ITEM_CERO');
		$documentosdiferenciadestino    = 	$this->indicadores_asientos_contables($empresa_id, 'DIFERENCIAS_DESTINO');

		$empresa_id 					= 	Session::get('empresas_meta')->COD_EMPR;

		return View::make('bienvenido',
						 [
						 	'lista_ventas' 		 			=> $lista_ventas,
						 	'lista_productos_sc' 			=> $lista_productos_sc,
						 	'lista_documento_sin_enviar' 	=> $lista_documento_sin_enviar,
						 	'lista_documento_correlativo' 	=> $lista_documento_correlativo,
						 	'lista_compras' 	 			=> $lista_compras,
						 	'lista_productos_sc_comp' 		=> $lista_productos_sc_comp,
						 	'anio' 				 			=> $anio,
						 	'combo_anio_pc' 				=> $combo_anio_pc,
						 	'empresa_id' 					=> $empresa_id,
						 	'diferencimontos' 				=> $diferencimontos,
						 	'documentositemcero' 			=> $documentositemcero,
						 	'documentosdiferenciadestino' 	=> $documentosdiferenciadestino,

						 ]);
	}

	public function actionCerrarSesion()
	{

		Session::forget('usuario_meta');
		Session::forget('listamenu_meta');
		Session::forget('empresas_meta');
		Session::forget('centros_meta');
		Session::forget('listaopciones_meta');
		Session::forget('color_meta');

		return Redirect::to('/login');
	}

	public function actionCambiarPerfil()
	{

		Session::forget('empresas');
		Session::forget('centros');
		return Redirect::to('/acceso');
	}


	public function actionListarUsuarios($idopcion)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Lista de usuarios');
        $array_rols    = WEBRol::where('ind_meta','=',1)
                         ->pluck('id')
                         ->toArray();
                 
	    $listausuarios = User::where('id','<>',$this->prefijomaestro.'00000001')
	    				->whereIn('rol_id',$array_rols)->orderBy('id', 'asc')->get();

		return View::make('usuario/listausuarios',
						 [
						 	'listausuarios' => $listausuarios,
						 	'idopcion' => $idopcion,
						 ]);
	}


	public function actionAgregarUsuario($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
		View::share('titulo','Agregar Usuario');
		if($_POST)
		{


			$personal_id 	 		 	= 	$request['personal'];
			$personal     				=   WEBListaPersonal::where('id', '=', $personal_id)->first();
			$idusers 				 	=   $this->funciones->getCreateIdMaestra('users');
			
			$cabecera            	 	=	new User;
			$cabecera->id 	     	 	=   $idusers;
			$cabecera->nombre 	     	=   $personal->nombres;
			$cabecera->name  		 	=	$request['name'];
			$cabecera->passwordmobil  	=	$request['password'];
			$cabecera->fecha_crea 	   	=  	$this->fechaactual;
			$cabecera->password 	 	= 	Crypt::encrypt($request['password']);
			$cabecera->rol_id 	 		= 	$request['rol_id'];
			$cabecera->usuarioosiris_id	= 	$personal->id;
			$cabecera->save();
 
 
 			return Redirect::to('/gestion-de-usuarios/'.$idopcion)->with('bienhecho', 'Usuario '.$personal->nombres.' registrado con exito');

		}else{

			$listapersonal 				= 	DB::table('WEB.LISTAPERSONAL')
	    									->leftJoin('users', 'WEB.LISTAPERSONAL.id', '=', 'users.usuarioosiris_id')
	    									->whereNull('users.usuarioosiris_id')
	    									->select('WEB.LISTAPERSONAL.id','WEB.LISTAPERSONAL.nombres')
	    									->get();

			$rol 						= 	DB::table('WEB.Rols')->where('id','<>',$this->prefijomaestro.'00000001')->pluck('nombre','id')->toArray();
			$comborol  					= 	array('' => "Seleccione Rol") + $rol;
		
			return View::make('usuario/agregarusuario',
						[
							'comborol'  		=> $comborol,
							'listapersonal'  	=> $listapersonal,					
						  	'idopcion'  		=> $idopcion
						]);
		}
	}


	public function actionModificarUsuario($idopcion,$idusuario,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idusuario = $this->funciones->decodificarmaestra($idusuario);
	    View::share('titulo','Modificar Usuario');
		if($_POST)
		{

			$cabecera            	 =	User::find($idusuario);			
			$cabecera->name  		 =	$request['name'];
			$cabecera->passwordmobil =	$request['password'];
			$cabecera->fecha_mod 	 =  $this->fechaactual;
			$cabecera->password 	 = 	Crypt::encrypt($request['password']);
			$cabecera->activo 	 	 =  $request['activo'];			
			$cabecera->rol_id 	 	 = 	$request['rol_id']; 
			$cabecera->save();


 			return Redirect::to('/gestion-de-usuarios/'.$idopcion)->with('bienhecho', 'Usuario '.$request['nombre'].' modificado con exito');


		}else{


				$usuario 	= 	User::where('id', $idusuario)->first();  
				$rol 		= 	DB::table('WEB.Rols')->where('id','<>',$this->prefijomaestro.'00000001')->pluck('nombre','id')->toArray();
				$comborol  	= 	array($usuario->rol_id => $usuario->rol->nombre) + $rol;
				$centros 	= 	ALMCentro::where('COD_ESTADO','=','1')->get(); 
				$empresas 	= 	STDEmpresa::where('COD_ESTADO','=','1')->where('IND_SISTEMA','=','1')->get(); 
				$funcion 	= 	$this;	

		        return View::make('usuario/modificarusuario', 
		        				[
		        					'usuario'  		=> $usuario,
									'comborol' 		=> $comborol,
						  			'idopcion' 		=> $idopcion,
									'centros' 		=> $centros,
									'empresas' 		=> $empresas,
									'funcion' 		=> $funcion,
		        				]);
		}
	}




	public function actionListarRoles($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Lista de Roles');
	    $listaroles = WEBRol::where('id','<>',$this->prefijomaestro.'00000001')
	    				->where('ind_meta','=',1)->orderBy('id', 'asc')->get();

		return View::make('usuario/listaroles',
						 [
						 	'listaroles' => $listaroles,
						 	'idopcion' => $idopcion,
						 ]);

	}


	public function actionAgregarRol($idopcion,Request $request)
	{
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Anadir');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Agregar Rol');
		if($_POST)
		{

			/**** Validaciones laravel ****/
			
			$this->validate($request, [
			    'nombre' => 'unico:WEB,rols',
			], [
            	'nombre.unico' => 'Rol ya registrado',
        	]);

			/******************************/
			$idrol 					 = $this->funciones->getCreateIdMaestra('WEB.rols');

			$cabecera            	 =	new WEBRol;
			$cabecera->id 	     	 =  $idrol;
			$cabecera->ind_meta 	 =  1;
			$cabecera->fecha_crea 	 =  $this->fechaactual;
			$cabecera->nombre 	     =  $request['nombre'];
			$cabecera->save();

			$listaopcion 			 = 	WEBOpcion::where('ind_meta','=',1)->orderBy('id', 'asc')->get();
			$count = 1;

			foreach($listaopcion as $item){

				$idrolopciones 		= $this->funciones->getCreateIdMaestra('WEB.rolopciones');

			    $detalle            =	new WEBRolOpcion;
			    $detalle->id 	    =  	$idrolopciones;
				$detalle->opcion_id = 	$item->id;
				$detalle->fecha_crea =  $this->fechaactual;
				$detalle->rol_id    =  	$idrol;
				$detalle->orden     =  	$count;
				$detalle->ver       =  	0;
				$detalle->anadir    =  	0;
				$detalle->modificar =  	0;
				$detalle->eliminar  =  	0;
				$detalle->todas     = 	0;
				$detalle->save();

				$count 				= 	$count +1;
			}

 			return Redirect::to('/gestion-de-roles/'.$idopcion)->with('bienhecho', 'Rol '.$request['nombre'].' registrado con exito');
		}else{

		
			return View::make('usuario/agregarrol',
						[
						  	'idopcion' => $idopcion
						]);

		}
	}


	public function actionModificarRol($idopcion,$idrol,Request $request)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Modificar');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    $idrol = $this->funciones->decodificarmaestra($idrol);
	    View::share('titulo','Modificar Rol');


		if($_POST)
		{

			/**** Validaciones laravel ****/
			$this->validate($request, [
				'nombre' => 'unico_menos:WEB,rols,id,'.$idrol,
			], [
            	'nombre.unico_menos' => 'Rol ya registrado',
        	]);
			/******************************/

			$cabecera            	 =	WEBRol::find($idrol);
			$cabecera->nombre 	     =  $request['nombre'];
			$cabecera->fecha_mod 	 =  $this->fechaactual;
			$cabecera->activo 	 	 =  $request['activo'];			
			$cabecera->save();
 
 			return Redirect::to('/gestion-de-roles/'.$idopcion)->with('bienhecho', 'Rol '.$request['nombre'].' modificado con éxito');

		}else{
				$rol = WEBRol::where('id', $idrol)->first();

		        return View::make('usuario/modificarrol', 
		        				[
		        					'rol'  		=> $rol,
						  			'idopcion' 	=> $idopcion
		        				]);
		}
	}



	public function actionListarPermisos($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	     View::share('titulo','Lista Permisos');
	    $listaroles = WEBRol::where('id','<>',$this->prefijomaestro.'00000001')
	    				->where('ind_meta','=',1)->orderBy('id', 'asc')->get();

		return View::make('usuario/listapermisos',
						 [
						 	'listaroles' => $listaroles,
						 	'idopcion' => $idopcion,
						 ]);
	}


	public function actionAjaxListarOpciones(Request $request)
	{
		$idrol =  $request['idrol'];
		$idrol = $this->funciones->decodificarmaestra($idrol);

		$listaopciones = WEBRolOpcion::where('rol_id','=',$idrol)->get();

		return View::make('usuario/ajax/listaopciones',
						 [
							 'listaopciones'   => $listaopciones
						 ]);
	}

	public function actionAjaxActivarPermisos(Request $request)
	{

		$idrolopcion =  $request['idrolopcion'];
		$idrolopcion = $this->funciones->decodificarmaestra($idrolopcion);

		$cabecera            	 =	WEBRolOpcion::find($idrolopcion);
		$cabecera->ver 	     	 =  $request['ver'];
		$cabecera->anadir 	 	 =  $request['anadir'];
		$cabecera->fecha_mod 	 =  $this->fechaactual;
		$cabecera->modificar 	 =  $request['modificar'];
		$cabecera->todas 	 	 =  $request['todas'];	
		$cabecera->save();

		echo("gmail");

	}
	
	public function actionAjaxActivarPerfiles(Request $request)
	{

		$idempresa =  $request['idempresa'];
		$idcentro =  $request['idcentro'];
		$idusuario =  $request['idusuario'];
		$check =  $request['check'];	

		$perfiles = WEBUserEmpresaCentro::where('empresa_id','=',$idempresa)
										  ->where('centro_id','=',$idcentro)
										  ->where('usuario_id','=',$idusuario)
										  ->first();

		if(count($perfiles)>0){

			$cabecera            	 =	WEBUserEmpresaCentro::find($perfiles->id);
			$cabecera->fecha_mod 	 = 	$this->fechaactual;
			$cabecera->activo 	     =  $check;	
			$cabecera->save();	
			
		}else{

			$id 					= 	$this->funciones->getCreateIdMaestra('WEB.userempresacentros');
		    $detalle            	=	new WEBUserEmpresaCentro;
		    $detalle->id 	    	=  	$id;
			$detalle->empresa_id 	= 	$idempresa;
			$detalle->centro_id    	=  	$idcentro;
			$detalle->fecha_crea 	 = 	$this->fechaactual;
			$detalle->usuario_id    =  	$idusuario;
			$detalle->save();

		}

		echo("gmail");

	}





}
