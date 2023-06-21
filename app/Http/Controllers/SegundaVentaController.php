<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBAsiento;
use App\Modelos\ALMProducto;
use App\Modelos\CONPeriodo;
use App\Modelos\WEBInventarioSegundaVenta;
use App\Modelos\WEBDetalleSegundaVenta;


use App\Traits\GeneralesTraits;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

use App\Traits\SegundaVentaTraits;
use App\Traits\PlanContableTraits;
use App\Traits\MigrarVentaComercialTraits;


class SegundaVentaController extends Controller
{
	use SegundaVentaTraits;
	use GeneralesTraits;
	use PlanContableTraits;
	use MigrarVentaComercialTraits;

	public function actionGuardarConfiguracionSV($idopcion,Request $request)
	{

		$data_archivo 			=   json_decode($request['data_archivo'], true);
		$producto_id 			=   $request['producto_id'];
		$periodo_id 			=   $request['periodo_id'];
		$agregarinventario 		=   floatval(str_replace(",","",$request['agregarinventario']));
		$cantidad_documento 	=   floatval($request['cantidad_documento']);
		$anio 					=   $request['anio'];

		$producto 				=   WEBInventarioSegundaVenta::where('producto_id','=',$producto_id)->first();
		$inventariosegunda 		=   WEBInventarioSegundaVenta::where('producto_id','=',$producto_id)
									->where('periodo_id','=',$periodo_id)
									->first();

		if(count($inventariosegunda)>0){

			$inventariosegunda->cantidad_compra =   $inventariosegunda->cantidad_compra + $agregarinventario;
			$inventariosegunda->ucantidad_agregada =   $agregarinventario;
			$inventariosegunda->cantidad_documento =  $inventariosegunda->cantidad_documento+ $cantidad_documento;
			$inventariosegunda->fecha_mod 	 	=   $this->fechaactual;
			$inventariosegunda->usuario_mod 	=   Session::get('usuario_meta')->id;
			$inventariosegunda->save();

		}else{

			$id 						=   $this->funciones->getCreateIdMaestra('WEB.inventariosegundaventas');
			$anio  						=   $this->anio;
			
			$cabecera            	 	=	new WEBInventarioSegundaVenta;
			$cabecera->id 	     	 	=   $id;
			$cabecera->producto_id 	   	=   $producto_id;
			$cabecera->filtro 			=   $producto->filtro;
			$cabecera->periodo_id 	   	=   $periodo_id;
			$cabecera->cantidad_compra 	=   $agregarinventario;
			$cabecera->ucantidad_agregada 	=   $agregarinventario;
			$cabecera->cantidad_documento 	=   $cantidad_documento;
			$cabecera->anio 				=   $anio;
			$cabecera->empresa_id 	 	=   Session::get('empresas_meta')->COD_EMPR;
			$cabecera->fecha_crea 	 	=   $this->fechaactual;
			$cabecera->usuario_crea 	=   Session::get('usuario_meta')->id;
			$cabecera->save();
		}

		$inventariosegunda 		=   WEBInventarioSegundaVenta::where('producto_id','=',$producto_id)
									->where('periodo_id','=',$periodo_id)
									->first();
		$tipo_asiento 						=	'TAS0000000000003';	
		//dd($data_archivo);
		foreach($data_archivo as $key => $obj){
			$asiento_id 		=   $obj['asiento_id'];

			$asiento            =	WEBAsiento::where('COD_ASIENTO','=',$asiento_id)->first();
			$respuesta 			= 	$this->mv_update_historial_segundaventas_comercial($asiento,$tipo_asiento);
			//eliminar asiento
			$asiento->COD_CATEGORIA_ESTADO_ASIENTO = 'IACHTE0000000024';
			$asiento->TXT_CATEGORIA_ESTADO_ASIENTO = 'EXTORNADO';
			$asiento->save();
			$respuesta2 		= 	$this->mv_asignar_asiento_modelo_comercial_sv($asiento,$tipo_asiento);
			$asiento_nuevo      =	WEBAsiento::where('TXT_REFERENCIA','=',$asiento->TXT_REFERENCIA)
									->where('COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
									->first();

			$cantidad 			=   $obj['cantidad'];
			$id_det 					=   $this->funciones->getCreateIdMaestra('WEB.detallesegundaventas');
			$anio  						=   $this->anio;
			$detalle            	 	=	new WEBDetalleSegundaVenta;
			$detalle->id 	     	 	=   $id_det;
			$detalle->inventariosegundaventa_id 	   	=   $inventariosegunda->id;
			$detalle->asiento_id 		=   $asiento_nuevo->COD_ASIENTO;
			$detalle->cantidad_descargo=   $cantidad;
			$detalle->empresa_id 	 	=   Session::get('empresas_meta')->COD_EMPR;
			$detalle->fecha_crea 	 	=   $this->fechaactual;
			$detalle->usuario_crea 	=   Session::get('usuario_meta')->id;
			$detalle->save();



		}	
		return Redirect::to('/gestion-segunda-ventas/'.$idopcion)->with('bienhecho', 'Asociacion exitosa');
	}


	public function actionListarSegundaVenta($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Gestion Segunda Venta');
	    $empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$anio  					=   $this->anio;
		if(Session::has('anio_pc')){$anio = Session::get('anio_pc');}
		
		$listaperiodo 			=	$this->gn_lista_periodo($anio,$empresa_id);

	    $listasegundaventa 		= 	$this->sv_lista_inventario($empresa_id,$anio,$listaperiodo);
	    //dd($listasegundaventa );

		$funcion 				= 	$this;
		return View::make('venta/listasegundaventa',
						 [
						 	'listaperiodo' 			=> $listaperiodo,
						 	'listasegundaventa' 	=> $listasegundaventa,
						 	'combo_anio_pc'	 		=> $combo_anio_pc,
						 	'anio'	 				=> $anio,						 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}

	public function actionAjaxSegundaVenta(Request $request)
	{
		$anio 					=   $request['anio'];
		$idopcion 				=   $request['idopcion'];
	    $empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
		$listaperiodo 			=	$this->gn_lista_periodo($anio,$empresa_id);
	    $listasegundaventa 		= 	$this->sv_lista_inventario($empresa_id,$anio,$listaperiodo);
		$funcion 				= 	$this;
		return View::make('venta/ajax/alistasegundaventa',
						 [
						 	'listasegundaventa' 	=> $listasegundaventa,
						 	'listaperiodo' 			=> $listaperiodo,				 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'ajax' 					=> true,						 	
						 ]);
	}



	public function actionAjaxModalConfiguracionSV(Request $request)
	{
		$data_producto 			=   $request['data_producto'];
		$data_periodo 			=   $request['data_periodo'];
		$data_filtro 			=   $request['data_filtro'];
		$data_anio 				=   $request['data_anio'];
		$data_asociada 			=   $request['data_asociada'];
		$idopcion 				=   $request['idopcion'];
		$tipo_asiento_id 		=   'TAS0000000000003';
		$data_monto 			=   $request['data_monto'];
	    $empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;

		$monto_por_asociar		=   floatval($data_monto) - floatval($data_asociada);


		$listaasociada 			=   WEBInventarioSegundaVenta::join('WEB.detallesegundaventas','WEB.detallesegundaventas.inventariosegundaventa_id','=','WEB.inventariosegundaventas.id')
									->join('WEB.asientos','WEB.asientos.COD_ASIENTO','=','WEB.detallesegundaventas.asiento_id')
									->where('WEB.inventariosegundaventas.producto_id','=',$data_producto)
									->where('WEB.inventariosegundaventas.periodo_id','=',$data_periodo)
									->select('WEB.asientos.*','WEB.detallesegundaventas.cantidad_descargo')
									->get();

		$array_asientos 		= 	WEBInventarioSegundaVenta::join('WEB.detallesegundaventas','WEB.detallesegundaventas.inventariosegundaventa_id','=','WEB.inventariosegundaventas.id')
									->where('WEB.inventariosegundaventas.producto_id','=',$data_producto)
									->where('WEB.inventariosegundaventas.periodo_id','=',$data_periodo)
    								->pluck('asiento_id')
									->toArray();

	    $listaasiento 			= 	WEBAsiento::join(
										        DB::raw("
										            (	
														SELECT das.COD_ASIENTO,COUNT(das.COD_ASIENTO) CANTIDAD,SUM(det.CAN_PESO_PRODUCTO*det.CAN_PRODUCTO) CD 
														FROM WEB.asientomovimientos das
														inner join WEB.asientos asi on das.COD_ASIENTO = asi.COD_ASIENTO
														inner join  CMP.DETALLE_PRODUCTO det on det.COD_TABLA = asi.TXT_REFERENCIA 
														and det.COD_PRODUCTO = das.COD_PRODUCTO
														and det.COD_LOTE = das.COD_LOTE 
														and det.NRO_LINEA = das.NRO_LINEA_PRODUCTO
														 where das.IND_PRODUCTO=1 
																and das.COD_ASIENTO IN (
																select COD_ASIENTO from WEB.asientomovimientos where IND_PRODUCTO = 1
																and TXT_GLOSA like '%".$data_filtro."%'
																and COD_EMPR = '".$empresa_id."'
																GROUP BY COD_ASIENTO
																) GROUP BY das.COD_ASIENTO
														HAVING COUNT(das.COD_ASIENTO) = 1
														AND SUM(det.CAN_PRODUCTO) <= ".$data_monto."
													) tt
										        "), 'WEB.asientos.COD_ASIENTO', '=', 'tt.COD_ASIENTO'
									    		)
	    							->where('WEB.asientos.COD_PERIODO','=',$data_periodo)
	    							->where('WEB.asientos.COD_EMPR','=',$empresa_id)
	    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
	    							->whereNotIn('WEB.asientos.COD_ASIENTO',$array_asientos)
	    							->select('WEB.asientos.*','tt.CD')
	    							->get();

	    $producto 				=	ALMProducto::where('COD_PRODUCTO','=',$data_producto)->first();
	    $periodo 				=	CONPeriodo::where('COD_PERIODO','=',$data_periodo)->first();


		$funcion 				= 	$this;

		return View::make('venta/modal/ajax/maconfiguracioninventario',
						 [		 	
						 	'anio' 					=> $data_anio,
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'listaasiento' 			=> $listaasiento,
						 	'listaasociada' 		=> $listaasociada,
						 	'producto' 				=> $producto,
						 	'periodo' 				=> $periodo,
						 	'data_monto' 			=> $data_monto,
						 	'data_asociada' 		=> $data_asociada,
						 	'monto_por_asociar' 	=> $monto_por_asociar,
						 	'ajax' 					=> true,						 	
						 ]);
	}


}
