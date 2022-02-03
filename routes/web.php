<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/********************** USUARIOS *************************/
// header('Access-Control-Allow-Origin:  *');
// header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers: *');

Route::group(['middleware' => ['guestaw']], function () {

	Route::any('/', 'UserController@actionLogin');
	Route::any('/login', 'UserController@actionLogin');
	Route::any('/acceso', 'UserController@actionAcceso');
	Route::any('/accesobienvenido/{idempresa}', 'UserController@actionAccesoBienvenido');
	
}); 

Route::get('/cerrarsession', 'UserController@actionCerrarSesion');
Route::get('/cambiarperfil', 'UserController@actionCambiarPerfil');
Route::get('/migrar-ventas', 'MigrarVentaController@actionMigrarVentas');

Route::group(['middleware' => ['authaw']], function () {

	Route::get('/bienvenido', 'UserController@actionBienvenido');

	Route::any('/gestion-de-usuarios/{idopcion}', 'UserController@actionListarUsuarios');
	Route::any('/agregar-usuario/{idopcion}', 'UserController@actionAgregarUsuario');
	Route::any('/modificar-usuario/{idopcion}/{idusuario}', 'UserController@actionModificarUsuario');
	Route::any('/ajax-activar-perfiles', 'UserController@actionAjaxActivarPerfiles');

	Route::any('/gestion-de-roles/{idopcion}', 'UserController@actionListarRoles');
	Route::any('/agregar-rol/{idopcion}', 'UserController@actionAgregarRol');
	Route::any('/modificar-rol/{idopcion}/{idrol}', 'UserController@actionModificarRol');

	Route::any('/gestion-de-permisos/{idopcion}', 'UserController@actionListarPermisos');
	Route::any('/ajax-listado-de-opciones', 'UserController@actionAjaxListarOpciones');
	Route::any('/ajax-activar-permisos', 'UserController@actionAjaxActivarPermisos');

	Route::any('/gestion-plan-contable/{idopcion}', 'PlanContableController@actionListarPlanContable');
	Route::any('/ajax-plan-contable', 'PlanContableController@actionAjaxListarPlanContable');
	Route::any('/ajax-modal-configuracion-plan-contable', 'PlanContableController@actionAjaxConfiguracionPlanContable');
	Route::any('/guardar-configuracion-cuenta-contable/{idopcion}', 'PlanContableController@actionGuardarConfiguracionPlanContable');
	Route::any('/ajax-combo-cuentacontable-xnivel', 'PlanContableController@actionAjaxComboCuentaContableNivel');


	Route::any('/gestion-asiento-modelo/{idopcion}', 'AsientoModeloController@actionListarAsientoModelo');
	Route::any('/ajax-asiento-modelo', 'AsientoModeloController@actionAjaxListarAsientoModelo');
	Route::any('/agregar-asiento-modelo/{idopcion}', 'AsientoModeloController@actionAgregarAsientoModelo');
	Route::any('/modificar-asiento-modelo/{idopcion}/{idasientomodelo}', 'AsientoModeloController@actionModificarAsientoModelo');
	Route::any('/configurar-asiento-modelo/{idopcion}/{idasientomodelo}', 'AsientoModeloController@actionConfigurarAsientoModelo');
	Route::any('/ajax-modal-configuracion-asiento-modelo-detalle', 'AsientoModeloController@actionAjaxModalConfiguracionAsientoModelo');
	Route::any('/ajax-modal-modificar-configuracion-asiento-modelo-detalle', 'AsientoModeloController@actionAjaxModalModificarConfiguracionAsientoModelo');

	Route::any('/gestion-configuracion-producto/{idopcion}', 'ConfiguracioProductoController@actionListarConfiguracionProducto');
	Route::any('/ajax-configuracion-producto', 'ConfiguracioProductoController@actionAjaxConfiguracionProducto');
	Route::any('/ajax-modal-configuracion-producto-cuenta-contable', 'ConfiguracioProductoController@actionAjaxModalConfiguracionProductoCuentaContable');
	Route::any('/ajax-guardar-cuenta-contable', 'ConfiguracioProductoController@actionAjaxGuardarCuentaContable');

	Route::any('/gestion-registro-diario/{idopcion}', 'RegistroDiarioController@actionListarRegistroDiario');
	Route::any('/ajax-combo-periodo-xanio-xempresa', 'RegistroDiarioController@actionAjaxComboPeriodoAnioEmpresa');
	Route::any('/ajax-registro-diario', 'RegistroDiarioController@actionAjaxRegistroDiario');
	Route::any('/ajax-modal-detalle-asiento', 'RegistroDiarioController@actionAjaxModalDetalleAsiento');


   	Route::any('/enviocorreos', 'CorreoController@enviocorreo');
   	Route::any('/pruebaquery', 'PruebaController@pruebas');
   	Route::any('/power-bi', 'PruebaController@indicadoresISL');


   	Route::get('buscarproducto', function (Illuminate\Http\Request  $request) {
        $term = $request->term ?: '';

        $tags = App\Modelos\ALMProducto::where('COD_ESTADO','=',1)
        								->where('NOM_PRODUCTO', 'like', '%'.$term.'%')
        								->whereIn('IND_MATERIAL_SERVICIO', ['M','S'])
										->take(100)
										->pluck('NOM_PRODUCTO','COD_PRODUCTO');

        $valid_tags = [];
        foreach ($tags as $id => $tag) {
            $valid_tags[] = ['id' => $id, 'text' => $tag];
        }
        return \Response::json($valid_tags);
    });


});

