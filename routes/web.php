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
Route::get('/migrar-compras', 'MigrarCompraController@actionMigrarCompras');


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
	Route::any('/guardar-compras-cuenta-contable/{idopcion}', 'PlanContableController@actionGuardarComprasPlanContable');



	Route::any('/gestion-asiento-modelo/{idopcion}', 'AsientoModeloController@actionListarAsientoModelo');
	Route::any('/ajax-asiento-modelo', 'AsientoModeloController@actionAjaxListarAsientoModelo');
	Route::any('/agregar-asiento-modelo/{idopcion}', 'AsientoModeloController@actionAgregarAsientoModelo');
	Route::any('/modificar-asiento-modelo/{idopcion}/{idasientomodelo}', 'AsientoModeloController@actionModificarAsientoModelo');
	Route::any('/configurar-asiento-modelo/{idopcion}/{idasientomodelo}', 'AsientoModeloController@actionConfigurarAsientoModelo');
	Route::any('/ajax-modal-configuracion-asiento-modelo-detalle', 'AsientoModeloController@actionAjaxModalConfiguracionAsientoModelo');
	Route::any('/ajax-modal-modificar-configuracion-asiento-modelo-detalle', 'AsientoModeloController@actionAjaxModalModificarConfiguracionAsientoModelo');

	Route::any('/gestion-configuracion-producto/{idopcion}/{tipo_asiento_id}', 'ConfiguracioProductoController@actionListarConfiguracionProducto');
	Route::any('/ajax-configuracion-producto', 'ConfiguracioProductoController@actionAjaxConfiguracionProducto');
	Route::any('/ajax-modal-configuracion-producto-cuenta-contable', 'ConfiguracioProductoController@actionAjaxModalConfiguracionProductoCuentaContable');
	Route::any('/ajax-guardar-cuenta-contable', 'ConfiguracioProductoController@actionAjaxGuardarCuentaContable');


	Route::any('/gestion-registro-diario/{idopcion}', 'RegistroDiarioController@actionListarRegistroDiario');
	Route::any('/ajax-combo-periodo-xanio-xempresa', 'RegistroDiarioController@actionAjaxComboPeriodoAnioEmpresa');
	Route::any('/ajax-registro-diario', 'RegistroDiarioController@actionAjaxRegistroDiario');
	Route::any('/ajax-modal-detalle-asiento', 'RegistroDiarioController@actionAjaxModalDetalleAsiento');




	Route::any('/gestion-observacion-documentos/{tipo_asiento_id}', 'MigrarVentaController@actionListarObservacionDocumentos');
	Route::any('/ajax-modal-detalle-producto-migracion-ventas', 'MigrarVentaController@actionAjaxModalDetalleProductoMigracionVentas');
	Route::any('/generar-asiento-contables-xdocumentos', 'MigrarVentaController@actionGenerarAsientoContablesXDocumentos');
	Route::any('/ajax-observacion-documentos-ventas', 'MigrarVentaController@actionAjaxObeservacionDocumentosVentas');

	Route::any('/gestion-libros-electronico-ple/{idopcion}', 'ArchivoController@actionGestionLibrosElectronicoPle');
	Route::any('/descargar-archivo-ple', 'ArchivoController@actionDescargarArchivoPle');
	Route::any('/registro-ventas-txt', 'ArchivoController@actionRegistroVentasTxt');
	Route::any('/archivo-ple-excel/{anio}/{tipo_asiento_id}/{periodo_id}/{documento}', 'ArchivoController@actionDescargarArchivoPleExcel');
	Route::any('/ajax-buscar-lista-ple', 'ArchivoController@actionAjaxBuscarListaPle');


	Route::any('/gestion-listado-compras/{idopcion}', 'ComprasController@actionListarCompras');
	Route::any('/ajax-combo-periodo-xanio-xempresa', 'RegistroDiarioController@actionAjaxComboPeriodoAnioEmpresa');
	Route::any('/ajax-listado-compras', 'ComprasController@actionAjaxListarCompras');
	Route::any('/ajax-buscar-compra-seleccionada', 'ComprasController@actionAjaxBuscarCompraseleccionada');
	Route::any('/asiento-contables-confirmado-xdocumentos', 'ComprasController@actionGonfirmarAsientoContablesXDocumentos');
	Route::any('/ajax-modal-detalle-asiento-confirmar', 'ComprasController@actionAjaxModalDetalleAsientoConfirmar');
	Route::any('/asiento-contables-confirmado-configuracion-xdocumentos/{idopcion}/{idasiento}', 'ComprasController@actionGonfirmarConfiguracionAsientoContablesXDocumentos');


	Route::any('/gestion-deposito-masivo-detraccion/{idopcion}', 'ComprasController@actionListarDepositoMasivoDetraccion');
	Route::any('/ajax-listado-deposito-masivo-detraccion', 'ComprasController@actionAjaxListarDepositoMasivoDetraccion');
	Route::any('/descargar-archivo-detraccion', 'ComprasController@actionDescargarArchivoDetraccion');

	Route::any('/gestion-configuracion-cuenta-detraccion/{idopcion}', 'ComprasController@actionConfiguracionCuentaDetraccion');
	Route::any('/agregar-cuenta-detraccion/{idopcion}', 'ComprasController@actionAgregarCuentaDetraccion');
	Route::any('/modificar-cuenta-detraccion/{idopcion}/{documento}', 'ComprasController@actionModificarCuentaDetraccion');


	Route::any('/ajax-modal-detalle-documento-sin-enviar-sunat', 'AlertaCotroller@actionAjaxModalDetalleDocumentoSinEnviarSunat');
	Route::any('/ajax-modal-detalle-documento-correlativos', 'AlertaCotroller@actionAjaxModalDetalleDocumentoCorrelativos');


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


   	Route::get('buscardetraccion', function (Illuminate\Http\Request  $request) {


        $term = $request->term ?: '';

	   	$tags = App\Modelos\STDEmpresa::where('COD_ESTADO','=',1)
	   									->where('IND_PROVEEDOR','=',1)
	   									->select(DB::raw(" (NRO_DOCUMENTO + ' - ' + NOM_EMPR) as NOM_EMPR , NRO_DOCUMENTO"))
	   									->where('NOM_EMPR', 'like', '%'.$term.'%')
	   									->orWhere('NRO_DOCUMENTO', 'like', '%'.$term.'%')
		        						->take(100)
										->pluck('NOM_EMPR','NRO_DOCUMENTO');
        $valid_tags = [];
        foreach ($tags as $id => $tag) {
            $valid_tags[] = ['id' => $id, 'text' => $tag];
        }
        return \Response::json($valid_tags);
    });


});

