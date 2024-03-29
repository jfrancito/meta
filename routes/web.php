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
Route::get('/migrar-ventas-comercial', 'MigrarVentaComercialController@actionMigrarVentasComercial');
Route::get('/migrar-ventas-internacional', 'MigrarVentaInternacionalController@actionMigrarVentasInternacional');
Route::get('/migrar-compras', 'MigrarCompraController@actionMigrarCompras');
Route::get('/migrar-liquidacion-compras', 'MigrarCompraController@actionMigrarLiquidacionCompras');
Route::get('/migrar-recibo-honorario', 'MigrarReciboHonorarioController@actionMigrarReciboHonorario');
Route::get('/migrar-total-ceros', 'MigrarVentaController@actionMigrarTotalCeros');
Route::get('/actualizar-tipo-cambio-sbs', 'TipoCambioController@actionActualizarTipoCambio');
Route::get('/actualizar-correlativo-asiento', 'AsientoCorrelativoController@actionActualizarCorrelativo');
Route::get('/actualizar-tipo-cambio', 'TipoCambioController@actionActualizarTipoCambioNormal');

Route::group(['middleware' => ['authaw']], function () {

	Route::get('/bienvenido', 'UserController@actionBienvenido');
	Route::any('/ajax-observaciones-asiento', 'UserController@actionAjaxListarObservacionesAsiento');

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

	Route::any('/gestion-segunda-ventas/{idopcion}', 'SegundaVentaController@actionListarSegundaVenta');
	Route::any('/ajax-segunda-ventas', 'SegundaVentaController@actionAjaxSegundaVenta');
	Route::any('/ajax-modal-configuracion-segunda-venta', 'SegundaVentaController@actionAjaxModalConfiguracionSV');
	Route::any('/guardar-configuracio-segunda-venta/{idopcion}', 'SegundaVentaController@actionGuardarConfiguracionSV');

	Route::any('/gestion-asiento-modelo/{idopcion}', 'AsientoModeloController@actionListarAsientoModelo');
	Route::any('/ajax-asiento-modelo', 'AsientoModeloController@actionAjaxListarAsientoModelo');
	Route::any('/agregar-asiento-modelo/{idopcion}', 'AsientoModeloController@actionAgregarAsientoModelo');
	Route::any('/modificar-asiento-modelo/{idopcion}/{idasientomodelo}', 'AsientoModeloController@actionModificarAsientoModelo');
	Route::any('/configurar-asiento-modelo/{idopcion}/{idasientomodelo}', 'AsientoModeloController@actionConfigurarAsientoModelo');
	Route::any('/ajax-modal-configuracion-asiento-modelo-detalle', 'AsientoModeloController@actionAjaxModalConfiguracionAsientoModelo');
	Route::any('/ajax-modal-modificar-configuracion-asiento-modelo-detalle', 'AsientoModeloController@actionAjaxModalModificarConfiguracionAsientoModelo');

	Route::any('/gestion-configuracion-producto/{idopcion}/{tipo_asiento_id}/{anio}', 'ConfiguracioProductoController@actionListarConfiguracionProducto');
	Route::any('/gestion-configuracion-producto/{idopcion}', 'ConfiguracioProductoController@actionListarConfiguracionProductoMenu');

	//transfrencia entre productos

	Route::any('/gestion-transferencia-cantidades-productos/{idopcion}', 'KardexController@actionTransferenciaCantidadesProductos');
	Route::any('/ajax-modal-configuracion-transferencia-producto', 'KardexController@actionAjaxModalConfiguracionTranferenciaPorducto');
	Route::any('/configurar-transferencia-producto/{idopcion}', 'KardexController@actionConfigurarTransferenciaProducto');
	Route::any('/gestion-eliminar-item-kardex/{idopcion}/{codigo}', 'KardexController@actionConfigurarELiminarItemKardex');



	Route::any('/ajax-configuracion-producto', 'ConfiguracioProductoController@actionAjaxConfiguracionProducto');
	Route::any('/ajax-modal-configuracion-producto-cuenta-contable', 'ConfiguracioProductoController@actionAjaxModalConfiguracionProductoCuentaContable');
	Route::any('/ajax-guardar-cuenta-contable', 'ConfiguracioProductoController@actionAjaxGuardarCuentaContable');
	Route::any('/ajax-combo-servicio-material-xcategoria-producto', 'ConfiguracioProductoController@actionAjaxComboServicioMaterial');
	Route::any('/ajax-modal-configuracion-producto-codigo-migracion', 'ConfiguracioProductoController@actionAjaxModalConfiguracionProductoCodigoMigracion');
	Route::any('/ajax-guardar-codigo-migracion', 'ConfiguracioProductoController@actionAjaxGuardarCodigoMigracion');
	Route::any('/ajax-guardar-cuenta-contable-inter', 'ConfiguracioProductoController@actionAjaxGuardarCuentaContableInter');

	Route::any('/gestion-configuracion-tipo-cambio/{idopcion}', 'ConfiguracioTipoCambioController@actionListarTipoCambio');
	Route::any('/ajax-tipo-cambio', 'ConfiguracioTipoCambioController@actionAjaxListarTipoCambio');
	Route::any('/ajax-guardar-configuracion-tipo-cambio', 'ConfiguracioTipoCambioController@actionAjaxGuardarTipoCambio');

	Route::any('/gestion-registro-diario/{idopcion}', 'RegistroDiarioController@actionListarRegistroDiario');
	Route::any('/ajax-combo-periodo-xanio-xempresa', 'RegistroDiarioController@actionAjaxComboPeriodoAnioEmpresa');
	Route::any('/ajax-registro-diario', 'RegistroDiarioController@actionAjaxRegistroDiario');
	Route::any('/ajax-modal-detalle-asiento-rd', 'RegistroDiarioController@actionAjaxModalDetalleAsiento');
	Route::any('/descargar-asientos-contable-excel', 'RegistroDiarioController@actionAjaxDescargarAsientoContable');
	Route::any('/descargar-asientos-contable-excel-rd', 'RegistroDiarioController@actionAjaxDescargarAsientoContableRD');

	Route::any('/asiento-contable-excel-xasiento/{cod_asiento}', 'RegistroDiarioController@actionAsientoContableExcelXAsiento');

	Route::any('/gestion-estado-comprobantes-compras/{idopcion}', 'ReporteComprasController@actionListarEstadoComprobanteCompra');
	Route::any('/ajax-estado-comprobantes-compras', 'ReporteComprasController@actionAjaxListarEstadoComprobanteCompra');
	Route::any('/descargar-estado-comprobante-excel', 'ReporteComprasController@actionAjaxDescargarAsientoContable');

	Route::any('/gestion-asiento-alterado-compras/{idopcion}', 'ReporteComprasController@actionListarAsientoAlteradoCompra');
	Route::any('/ajax-asiento-alterado-compras', 'ReporteComprasController@actionAjaxListarAsientoAlteradoCompra');
	Route::any('/descargar-asiento-alterado-excel', 'ReporteComprasController@actionAjaxDescargarAsientoAlterado');

	Route::any('/ajax-combo-periodo-xanio-xempresa-gc', 'RegistroDiarioController@actionAjaxComboPeriodoAnioEmpresaGC');
	Route::any('/gestion-observacion-documentos/{tipo_asiento_id}/{anio}', 'MigrarVentaController@actionListarObservacionDocumentos');
	Route::any('/ajax-modal-detalle-producto-migracion-ventas', 'MigrarVentaController@actionAjaxModalDetalleProductoMigracionVentas');
	Route::any('/generar-asiento-contables-xdocumentos', 'MigrarVentaController@actionGenerarAsientoContablesXDocumentos');
	Route::any('/ajax-observacion-documentos-ventas', 'MigrarVentaController@actionAjaxObeservacionDocumentosVentas');

	Route::any('/gestion-libros-electronico-ple/{idopcion}', 'ArchivoController@actionGestionLibrosElectronicoPle');
	Route::any('/descargar-archivo-ple', 'ArchivoController@actionDescargarArchivoPle');
	Route::any('/registro-ventas-txt', 'ArchivoController@actionRegistroVentasTxt');
	Route::any('/archivo-ple-excel/{anio}/{tipo_asiento_id}/{periodo_id}/{documento}', 'ArchivoController@actionDescargarArchivoPleExcel');
	Route::any('/ajax-buscar-lista-ple', 'ArchivoController@actionAjaxBuscarListaPle');


	Route::any('/gestion-ple-kardex-envases/{idopcion}', 'KardexController@actionGestionKardexEnvasesPle');
	Route::any('/ajax-buscar-lista-ple-kardex', 'KardexController@actionAjaxBuscarListaPleKardex');
	Route::any('/descargar-archivo-ple-kardex', 'KardexController@actionDescargarArchivoPle');




	Route::any('/gestion-reporte-registro-ventas/{idopcion}', 'ReporteVentasController@actionGestionReporteRegistroVenta');
	Route::any('/ajax-buscar-reporte-registro-venta', 'ReporteVentasController@actionAjaxBuscarReporteRegistroVenta');
	Route::any('/descargar-registro-venta-excel', 'ReporteVentasController@actionDescargarRegistroVentaExcel');

	Route::any('/gestion-migracion-erp-navasoft/{idopcion}', 'MigracionNavasoftController@actionGestionMigracionNavasoft');
	Route::any('/descargar-archivo-migrar-navasoft', 'MigracionNavasoftController@actionDescargarArchivoMigrarNavasoft');
	Route::any('/ajax-buscar-lista-navasotf', 'MigracionNavasoftController@actionAjaxBuscarListaNavasoft');

	Route::any('/gestion-recibo-honorario/{idopcion}', 'ReciboHonorarioController@actionListarReciboHonorario');
	Route::any('/ajax-listado-recibo-honorario', 'ReciboHonorarioController@actionAjaxListarReciboHonorario');
	Route::any('/ajax-modal-detalle-asiento-rh-confirmar', 'ReciboHonorarioController@actionAjaxModalDetalleAsientoRHConfirmar');
	Route::any('/asiento-contables-confirmado-rh-configuracion-xdocumentos/{idopcion}/{idasiento}', 'ReciboHonorarioController@actionGonfirmarConfiguracionAsientoRHContablesXDocumentos');
	Route::any('/ajax-modal-detalle-rh-asiento-transicion', 'ReciboHonorarioController@actionAjaxModalDetalleAsientoRHTransicion');
	Route::any('/asiento-contables-transicion-configuracion-rh-xdocumentos/{idopcion}/{idasiento}', 'ReciboHonorarioController@actionTransicionConfiguracionAsientoContablesRHXDocumentos');
	Route::any('/ajax-editar-asiento-contable-movimiento-rh', 'ReciboHonorarioController@actionAjaxEditarAsientoContableMovimientoRH');



	Route::any('/gestion-listado-compras/{idopcion}', 'ComprasController@actionListarCompras');
	Route::any('/ajax-combo-periodo-xanio-xempresa', 'RegistroDiarioController@actionAjaxComboPeriodoAnioEmpresa');
	Route::any('/ajax-listado-compras', 'ComprasController@actionAjaxListarCompras');
	Route::any('/ajax-buscar-compra-seleccionada', 'ComprasController@actionAjaxBuscarCompraseleccionada');
	Route::any('/asiento-contables-confirmado-xdocumentos', 'ComprasController@actionGonfirmarAsientoContablesXDocumentos');
	Route::any('/ajax-modal-detalle-asiento-confirmar', 'ComprasController@actionAjaxModalDetalleAsientoConfirmar');
	Route::any('/ajax-modal-cambiar-asiento-fechaemision', 'ComprasController@actionAjaxModalCambiarAsientoFechaEmision');
	Route::any('/asiento-contables-confirmado-configuracion-xdocumentos/{idopcion}/{idasiento}', 'ComprasController@actionGonfirmarConfiguracionAsientoContablesXDocumentos');

	Route::any('/ajax-modal-detalle-asiento-transicion', 'ComprasController@actionAjaxModalDetalleAsientoTransicion');	
	Route::any('/asiento-contables-transicion-configuracion-xdocumentos/{idopcion}/{idasiento}', 'ComprasController@actionTransicionConfiguracionAsientoContablesXDocumentos');

	Route::any('/ajax-modal-detalle-asiento-diario-compra', 'ComprasController@actionAjaxModalDetalleAsientoDiarioCompra');
	Route::any('/ajax-modal-detalle-asiento-diario-reversion', 'ComprasController@actionAjaxModalDetalleAsientoDiarioReversion');
	Route::any('/ajax-modal-crear-detalle-asiento-diario', 'ComprasController@actionAjaxModalCrearDetalleAsientoDiario');
	Route::any('/ajax-editar-asiento-contable-movimiento', 'ComprasController@actionAjaxEditarAsientoContableMovimiento');

	Route::any('/popup-detalle-asiento-diario-compra/{anio}/{asiento_compra_id}', 'ComprasController@actionPopUpDetalleAsientoDiarioCompra');



	Route::any('/gestion-deposito-masivo-detraccion/{idopcion}', 'ComprasController@actionListarDepositoMasivoDetraccion');
	Route::any('/ajax-listado-deposito-masivo-detraccion', 'ComprasController@actionAjaxListarDepositoMasivoDetraccion');
	Route::any('/descargar-archivo-detraccion', 'ComprasController@actionDescargarArchivoDetraccion');

	Route::any('/gestion-configuracion-cuenta-detraccion/{idopcion}', 'ComprasController@actionConfiguracionCuentaDetraccion');
	Route::any('/agregar-cuenta-detraccion/{idopcion}', 'ComprasController@actionAgregarCuentaDetraccion');
	Route::any('/modificar-cuenta-detraccion/{idopcion}/{documento}', 'ComprasController@actionModificarCuentaDetraccion');
	Route::any('/diario-reversion-guardar-data/{idopcion}', 'ComprasController@actionGuardarDiarioReversionCuentaContable');
	Route::any('/ajax-reversion-diario', 'ComprasController@actionAjaxReversionDiario');


	Route::any('/ajax-modal-detalle-documento-sin-enviar-sunat', 'AlertaCotroller@actionAjaxModalDetalleDocumentoSinEnviarSunat');
	Route::any('/ajax-modal-detalle-documento-correlativos', 'AlertaCotroller@actionAjaxModalDetalleDocumentoCorrelativos');


	Route::any('/gestion-cancelar-documentos/{idopcion}', 'CajaBancoController@actionCancelarDocumentoClienteProveedor');
	Route::any('/ajax-modal-lista-movimiento-caja-banco', 'CajaBancoController@actionAjaxModalListaMovimientoCajaBanco');
	Route::any('/ajax-lista-movimiento-caja-banco', 'CajaBancoController@actionAjaxListaMovimientoCajaBanco');


	Route::any('/gestion-guardar-asiento-caja-banco/{idopcion}', 'CajaBancoController@actionGuardarAsientoCajaBanco');


	Route::any('/gestion-asociar-banco-caja/{idopcion}', 'CajaBancoController@actionListarBancoCaja');
	Route::any('/ajax-modal-asociar-banco-caja', 'CajaBancoController@actionAjaxModalAsociarBancoCaja');
	Route::any('/guardar-asociacion-banco-caja/{idopcion}', 'CajaBancoController@actionGuardarAsociacionCajaBanco');



	Route::any('/gestion-kardex-cascara/{idopcion}', 'KardexCascaraController@actionListarKardexCascara');
	Route::any('/descargar-excel-kardex-cascara', 'KardexCascaraController@actionDescargarExcelKardexCascara');
	Route::any('/ajax-movimiento-kardex-cascara', 'KardexCascaraController@actionAjaxListarMovimientoKardex');
	Route::any('/ajax-modal-detalle-producto-total-kardex-cascara', 'KardexCascaraController@actionAjaxModalDetalleTotalKardex');



	Route::any('/gestion-kardex-producto-terminado/{idopcion}', 'KardexProductoTerminadoController@actionListarKardexProductoTerminado');
	Route::any('/descargar-excel-kardex-producto-terminado', 'KardexProductoTerminadoController@actionDescargarExcelKardexCProductoTerminado');
	// Route::any('/ajax-movimiento-kardex-cascara', 'KardexCascaraController@actionAjaxListarMovimientoKardex');
	// Route::any('/ajax-modal-detalle-producto-total-kardex-cascara', 'KardexCascaraController@actionAjaxModalDetalleTotalKardex');




	Route::any('/gestion-saldos-inicial/{idopcion}', 'KardexController@actionListarSaldoInicial');
	Route::any('/ajax-saldo-inicial', 'KardexController@actionAjaxListarSaldoInicial');

	Route::any('/gestion-movimiento-kardex/{idopcion}', 'KardexController@actionListarMovimientoKardex');
	Route::any('/ajax-movimiento-kardex', 'KardexController@actionAjaxListarMovimientoKardex');
	Route::any('/descargar-excel-kardex', 'KardexController@actionDescargarExcelKardex');
	
	Route::any('/ajax-modal-detalle-producto-kardex', 'KardexController@actionAjaxModalDetalleKardex');
	Route::any('/ajax-modal-detalle-producto-total-kardex', 'KardexController@actionAjaxModalDetalleTotalKardex');
	Route::any('/ajax-modal-asiento-contable-kardex', 'KardexController@actionAjaxModalAsientoContableKardex');

	Route::any('/kardex-guardar-data/{idopcion}', 'KardexController@actionGuardarKardexCuentaContable');
	Route::any('/ajax-calcular-ultimo-cu', 'KardexController@actionAjaxCalcularUltimoCU');


	Route::any('/gestion-pago-cobro-efectivo/{idopcion}', 'PagoCobroEfectivoController@actionListarMovimiento');
	Route::any('/ajax-registro-movimiento-efectivo', 'PagoCobroEfectivoController@actionAjaxRegistroMovimientoEfectivo');
	Route::any('/ajax-modal-configuracion-movimiento-efectivo', 'PagoCobroEfectivoController@actionAjaxModalConfiguracionMovimientoEfectivo');
	

	Route::any('/gestion-planilla-movilidad/{idopcion}', 'MovilidadController@actionListarMovilidad');
	Route::any('/ajax-registro-movilidad', 'MovilidadController@actionAjaxRegistroMovilidad');
	Route::any('/mobilidad-guardar-data', 'MovilidadController@actionMobilidadGuardarData');
	Route::any('/ajax-modal-configuracion-movilidad-cuenta-contable', 'MovilidadController@actionAjaxModalConfiguracionMovilidadCuentaContable');
	Route::any('/mobilidad-guardar-data/{idopcion}', 'MovilidadController@actionGuardarMovilidadCuentaContable');

	Route::any('/gestion-reversion-igv/{idopcion}', 'ComprasController@actionListarReversionIgv');
	Route::any('/ajax-modal-reversion-cuenta-contable', 'ComprasController@actionAjaxModalReversionCuentaContable');
	Route::any('/reversion-guardar-data', 'ComprasController@actionReversionGuardarData');
	Route::any('/reversion-guardar-data/{idopcion}', 'ComprasController@actionGuardarReversionCuentaContable');


	Route::any('/gestion-asiento/{idopcion}', 'AsientoController@actionGestionarAsiento');
	Route::any('/ajax-modal-detalle-asiento-configuracion', 'AsientoController@actionAjaxModalDetalleAsiento');
	Route::any('/ajax-agregar-detalle-asiento', 'AsientoController@actionAjaxAgregarDetalleAsiento');
	Route::any('/ajax-eliminar-detalle-asiento', 'AsientoController@actionAjaxEliminarDetalleAsiento');
	Route::any('/ajax-input-tipo-cambio', 'AsientoController@actionAjaxInputTipoCambio');
	Route::any('/ajax-modal-confirmacion-guardar', 'AsientoController@actionAjaxModalConfirmacionGuardar');
	
	Route::any('/gestion-pago-cobro/{idopcion}', 'AsientoController@actionGestionarPagoCobro');
	Route::any('/ajax-buscar-asiento-pago-cobro', 'AsientoController@actionAjaxBuscarAsientoPagoCobro');
	Route::any('/ajax-buscar-asiento-pago-cobro-cliente-proveedor', 'AsientoController@actionAjaxBuscarAsientoPagoCobroClienteProveedor');



	Route::any('/ajax-combo-cuentapagocobro-xtipoasiento', 'AsientoController@actionAjaxComboCuentaPagoCobro');
	Route::any('/ajax-combo-tipo-documento-referencia-xtipodocumento', 'AsientoController@actionAjaxComboDocumentoReferencia');
	Route::any('/ajax-modal-asientos-proveedor-cliente', 'AsientoController@actionAjaxModalProveedorCliente');

	Route::any('/libro-mayor-diario/{idopcion}', 'ReporteController@actionGestionLibrosMayorDiario');
	Route::any('/descargar-archivo-diario-mayor', 'ReporteController@actionDescargarArchivoDiarioMayor');
	Route::any('/ajax-buscar-lista-ple-diario', 'ReporteController@actionAjaxBuscarListaPleDiario');

	Route::any('/gestion-balance-comprobacion/{idopcion}', 'ReporteController@actionGestionBalanceComprobacion');
	Route::any('/ajax-buscar-balance-comprobacion', 'ReporteController@actionAjaxBuscarBalanceComprobacion');
	Route::any('/descargar-balance-comprobacion-excel', 'ReporteController@actionDescargarBalanceComprobacionExcel');
	Route::any('/ajax-combo-periodo-xanio-titulo', 'ReporteController@actionAjaxComboPeriodoAnioEmpresa');
	Route::any('/ajax-combo-cuentas-xanio-titulo', 'ReporteController@actionAjaxComboCuentasAnioEmpresa');

	Route::any('/gestion-situacion-financiera/{idopcion}', 'ReporteController@actionGestionSituacionFinanciera');
	Route::any('/ajax-buscar-situacion-financiera', 'ReporteController@actionAjaxBuscarSituacionFinanciera');
	Route::any('/descargar-situacion-financiera-excel', 'ReporteController@actionSituacionFinancieraExcel');

	Route::any('/gestion-resultado-funcion/{idopcion}', 'ReporteController@actionGestionResultadoFuncion');
	Route::any('/ajax-buscar-resultado-funcion', 'ReporteController@actionAjaxBuscarResultadoFuncion');
	Route::any('/descargar-resultado-funcion-excel', 'ReporteController@actionResutadoFuncionExcel');

	Route::any('/consulta-ingresos-mensuales/{idopcion}', 'ReporteController@actionGestionIngresosMensuales');
	Route::any('/ajax-buscar-ingresos-mensuales', 'ReporteController@actionAjaxBuscarIngresosMensuales');
	Route::any('/descargar-ingresos-mensuales-excel', 'ReporteController@actionIngresoMensualExcel');

	Route::any('/gestion-almacen-activos/{idopcion}', 'AlmacenActivoFijoController@actionListarActivosFijos');
	Route::any('/registrar-activo-fijo/{idproducto}/{iddocumento}', 'ActivoFijoController@registrarActivoFijo');
	Route::any('/gestion-almacen-activos-transferidos/{idopcion}', 'AlmacenActivoFijoTransferidoController@actionListarActivosFijosTransferidos');
	Route::any('/modificar-activo-fijo/{idactivofijo}', 'ActivoFijoController@modificarActivoFijo');
	Route::any('/modificar-obra/{idactivofijo}', 'ActivoFijoController@modificarObra');
	Route::any('/depreciacion-activo-fijo/{idopcion}', 'DepreciacionActivoFijoController@index');
	Route::any('/catalogo-activos-fijos/{idopcion}', 'ActivoFijoController@catalogoActivosFijos');	
	Route::any('/registrar-obra-activo-fijo', 'ActivoFijoController@registrarObraActivoFijo');	
	Route::any('/reporte-formato-contable/{idopcion}', 'ExportarFormatoIatrController@reporteFormatoIATR');	 
	Route::any('/reporte-formato-iach/{idopcion}', 'ExportarFormatoIachController@reporteFormatoIACH');	 
	Route::any('/exportar-formato-iatr', 'ExportarFormatoIatrController@exportarFormatoIATR');	 
	Route::any('/exportar-formato-iach', 'ExportarFormatoIachController@exportarFormatoIACH');	 

	Route::any('/importar-asientos-activos-fijos', 'ImportadorAsientosActivosFijos@index');	 


	Route::any('/gestion-resultado-naturaleza/{idopcion}', 'ReporteController@actionGestionResultadoNaturaleza');
	Route::any('/ajax-buscar-resultado-naturaleza', 'ReporteController@actionAjaxBuscarResultadoNaturaleza');
	Route::any('/descargar-resultado-naturaleza-excel', 'ReporteController@actionResutadoNaturalezaExcel');

	Route::any('/gestion-pago-itf/{idopcion}', 'ItfController@actionListarItf');
	Route::any('/ajax-registro-itf', 'ItfController@actionAjaxRegistroItf');
	Route::any('/ajax-modal-configuracion-itf-cuenta-contable', 'ItfController@actionAjaxModalConfiguracionItfCuentaContable');
	Route::any('/itf-guardar-data', 'ItfController@actionItfGuardarData');
	Route::any('/itf-guardar-data/{idopcion}', 'ItfController@actionGuardarItfCuentaContable');

	Route::any('/gestion-pago-comisiones/{idopcion}', 'ComisionesController@actionListarComisiones');
	Route::any('/ajax-registro-comisiones', 'ComisionesController@actionAjaxRegistroComisiones');
	Route::any('/ajax-modal-configuracion-comisiones-cuenta-contable', 'ComisionesController@actionAjaxModalConfiguracionComisionesCuentaContable');
	Route::any('/comisiones-guardar-data', 'ComisionesController@actionComisionesGuardarData');
	Route::any('/comisiones-guardar-data/{idopcion}', 'ComisionesController@actionGuardarComisionesCuentaContable');

	Route::any('/gestion-pago-intereses-prestamo/{idopcion}', 'InteresesPrestamoController@actionListarInteresesPrestamo');
	Route::any('/ajax-registro-intereses-prestamo', 'InteresesPrestamoController@actionAjaxRegistroInteresesPrestamo');
	Route::any('/ajax-modal-configuracion-intereses-prestamo-cuenta-contable', 'InteresesPrestamoController@actionAjaxModalConfiguracionInteresesPrestamoCuentaContable');
	Route::any('/intereses-prestamo-guardar-data', 'InteresesPrestamoController@actionInteresesPrestamoGuardarData');
	Route::any('/intereses-prestamo-guardar-data/{idopcion}', 'InteresesPrestamoController@actionGuardarInteresesPrestamoCuentaContable');
	Route::any('/detalle-intereses-prestamo/{idopcion}', 'InteresesPrestamoController@actionDetalleInteresesPrestamo');


	Route::any('/gestion-pago-leasing/{idopcion}', 'LeasingController@actionListarLeasing');
	Route::any('/ajax-registro-leasing', 'LeasingController@actionAjaxRegistroLeasing');
	Route::any('/ajax-modal-configuracion-leasing-cuenta-contable', 'LeasingController@actionAjaxModalConfiguracionLeasingCuentaContable');
	Route::any('/leasing-guardar-data', 'LeasingController@actionLeasingGuardarData');
	Route::any('/leasing-guardar-data/{idopcion}', 'LeasingController@actionGuardarLeasingCuentaContable');

	Route::any('/gestion-pago-multa-sunat/{idopcion}', 'MultaSunatController@actionListarMultaSunat');
	Route::any('/ajax-registro-multa-sunat', 'MultaSunatController@actionAjaxRegistroMultaSunat');
	Route::any('/ajax-modal-configuracion-multa-sunat-cuenta-contable', 'MultaSunatController@actionAjaxModalConfiguracionMultaSunatCuentaContable');
	Route::any('/multa-sunat-guardar-data', 'MultaSunatController@actionMultaSunatGuardarData');
	Route::any('/multa-sunat-guardar-data/{idopcion}', 'MultaSunatController@actionGuardarMultaSunatCuentaContable');



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

