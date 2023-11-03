<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;

use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\ALMProducto;
use App\Modelos\CONPeriodo;
use App\Modelos\CMPCategoria;
use App\Modelos\WEBKardexTransferencia;
use App\Modelos\WEBKardexProducto;


use App\Traits\PlanContableTraits;
use App\Traits\GeneralesTraits;
use App\Traits\KardexTraits;
use App\Traits\MovilidadTraits;


use Maatwebsite\Excel\Facades\Excel;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


class KardexProductoTerminadoController extends Controller
{

	use GeneralesTraits;
	use KardexTraits;
	use PlanContableTraits;
	use MovilidadTraits;

	public function actionListarKardexProductoTerminado($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Movimiento del kardex Producto Terminado');

	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	   	$sel_tipo_movimiento 	=	'';
	   	$sel_tipo_producto 		=	'TPK0000000000005';



	    $combo_tipo_movimiento 	= 	$this->gn_generacion_combo_categoria('TIPO_MOVIMIENTO_KARDEX','Seleccione movimiento','');
	    $combo_tipo_producto 	= 	$this->gn_generacion_combo_categoria('TIPO_PRODUCTO_KARDEX','Seleccione tipo producto','');

	    $listamovimiento 		= 	array();
	    $listasaldoinicial      = 	array();
	    $listaperido            = 	array();
		$funcion 				= 	$this;
	
	   	//dd($combo_tipo_producto);

		return View::make('kardex/listakardexproductoterminado',
						 [
						 	'listamovimiento' 		=> $listamovimiento,
						 	'listasaldoinicial' 	=> $listasaldoinicial,
						 	'listaperido' 			=> $listaperido,
						 	'combo_tipo_movimiento'	=> $combo_tipo_movimiento,
						 	'sel_tipo_movimiento'	=> $sel_tipo_movimiento,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_tipo_producto'	=> $combo_tipo_producto,
						 	'sel_tipo_producto'	 	=> $sel_tipo_producto,	
						 	'anio'					=> $anio,
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}



	public function actionDescargarExcelKardexCProductoTerminado(Request $request)
	{


		set_time_limit(0);


		//CASACARA
		$tipo_producto_id 		=   'TPK0000000000004';
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;
	    $saldoincial 			= 	WEBKardexProducto::where('empresa_id','=',$empresa_id)
	    							->where('tipo_producto_id','=',$tipo_producto_id)
	    							->where('activo','=',1)
									->orderBy('fecha_saldo_inicial', 'asc')
			    					->first();
		$anio 					= 	substr($saldoincial->fecha_saldo_inicial, 0, 4);

		$tipo_producto_id 		=   'TPK0000000000004';
		$tipoproducto 			= 	CMPCategoria::where('COD_CATEGORIA','=',$tipo_producto_id)->first();
		$idopcion 				=   $request['idopcion'];
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;
		$funcion 				= 	$this;
	    $listasaldoinicial 		= 	 $this->kd_lista_saldo_inicial(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id);
		$titulo 				=   'KARDEX-'.$tipoproducto->NOM_CATEGORIA.'-'.Session::get('empresas_meta')->NOM_EMPR;
		$array_detalle_asiento 	=	array();

		//CASCARA
        foreach($listasaldoinicial as $index => $item){

        	$tipo_producto_id 		=   'TPK0000000000004';
	        $saldoinicial 			= 	$funcion->kd_saldo_inicial_producto_id($empresa_id,$tipo_producto_id,$item->producto_id);
	    	$listadetalleproducto 	= 	$funcion->kd_lista_producto_periodo_cascara_view($empresa_id, 
	    																 $anio, 
	    																 '',
	    																 $item->producto_id,
	    																 '');
			$producto 				= 	ALMProducto::where('COD_PRODUCTO','=',$item->producto_id)->first();
			$periodo_enero 			= 	CONPeriodo::where('COD_ANIO','=',$anio)
										->where('COD_MES','=',1)
										->where('COD_EMPR','=',$empresa_id)
										->first();

		    $listakardexif 			= 	$funcion->kd_lista_kardex_inventario_cascara_final($empresa_id, 
		    																		$saldoinicial,
		    																		$listadetalleproducto,
		    																		$producto,
		    																		$periodo_enero);

	    	foreach($listakardexif as $index => $item){

	    		$servicio 					=	$item['servicio'];

	    		if($servicio == 'Salida a Produccion'){

			    	$array_nuevo_asiento 		=	array();
					$array_nuevo_asiento    	=	array(
						"periodo_id" 				=> $item['periodo_id'],
						"nombre_periodo" 			=> $item['nombre_periodo'],

						"fecha" 					=> $item['fecha'],
						"servicio" 					=> $item['servicio'],
						"producto_id" 				=> $item['producto_id'],
						"nombre_producto" 			=> $item['nombre_producto'],
						"serie" 					=> $item['serie'],
						"correlativo" 				=> $item['correlativo'],
						"ruc" 						=> $item['ruc'],
						"cliente_id" 				=> $item['cliente_id'],
						"nombre_cliente" 			=> $item['nombre_cliente'],

						"entrada_cantidad" 			=> $item['entrada_cantidad'],
						"entrada_cu" 				=> $item['entrada_cu'],
						"entrada_importe" 			=> $item['entrada_importe'],

						"salida_cantidad" 			=> $item['salida_cantidad'],
						"salida_cu" 				=> $item['salida_cu'],
						"salida_importe" 			=> $item['salida_importe'],

						"saldo_cantidad" 			=> $item['saldo_cantidad'],
						"saldo_cu" 					=> $item['saldo_cu'],
						"saldo_importe" 			=> $item['saldo_importe'],
						"tipo" 						=> 'MP'

					);
					array_push($array_detalle_asiento,$array_nuevo_asiento);
	    		}
	    	}

        }
		$tipo_producto_id 		=   'TPK0000000000004';
	    $listasaldoinicial 		= 	 $this->kd_lista_saldo_inicial_producto(Session::get('empresas_meta')->COD_EMPR,$tipo_producto_id);
	    $listaperido 			= 	 $this->gn_lista_periodo_asc($anio,Session::get('empresas_meta')->COD_EMPR);	

	    //Costos Vinculados
	    $tipo_categoria 		=	'TIPO_COSTO_VINCULADO';
		$listacostovinculado 	= 	CMPCategoria::where('TXT_GRUPO','=',$tipo_categoria)->where('TXT_REFERENCIA','=',$empresa_id)->get();
    	$listadetalleproductocv = 	$funcion->kd_lista_costo_producto_terminado($empresa_id, 
    																 $anio, 
    																 $tipo_categoria);

	    while ($row = $listadetalleproductocv->fetch()){
	    	$monto 						=	$row['CAN_DEBE_MN']+$row['CAN_HABER_MN']+$row['IGV'];
	    	$array_nuevo_asiento 		=	array();
			$array_nuevo_asiento    	=	array(
				"periodo_id" 				=> $row['COD_PERIODO'],
				"nombre_periodo" 			=> $row['TXT_NOMBRE'],
				"fecha" 					=> substr($row['FEC_ASIENTO'], 0, 10),
				"servicio" 					=> '',
				"producto_id" 				=> $row['TXT_CUENTA_CONTABLE'],
				"nombre_producto" 			=> $row['TXT_GLOSA'],
				"serie" 					=> $row['NRO_SERIE'],
				"correlativo" 				=> $row['NRO_DOC'],
				"ruc" 						=> '',
				"cliente_id" 				=> $row['COD_EMPR_CLI'],
				"nombre_cliente" 			=> $row['TXT_EMPR_CLI'],
				"entrada_cantidad" 			=> 0,
				"entrada_cu" 				=> 0,
				"entrada_importe" 			=> 0,
				"salida_cantidad" 			=> 0,
				"salida_cu" 				=> 0,
				"salida_importe" 			=> $monto,
				"saldo_cantidad" 			=> 0,
				"saldo_cu" 					=> 0,
				"saldo_importe" 			=> 0,
				"tipo" 						=> 'CV'

			);
			array_push($array_detalle_asiento,$array_nuevo_asiento);
	    }



	    //Produccion encargada de tercera
	    $tipo_categoria 		=	'TIPO_PRODUCTO_TERCEROS';
		$listaproduccionet 		= 	CMPCategoria::where('TXT_GRUPO','=',$tipo_categoria)->where('TXT_REFERENCIA','=',$empresa_id)->get();
    	$listadetalleproductocv = 	$funcion->kd_lista_costo_producto_terminado($empresa_id, 
    																 $anio, 
    																 $tipo_categoria);

	    while ($row = $listadetalleproductocv->fetch()){
	    	$monto 						=	$row['CAN_DEBE_MN']+$row['CAN_HABER_MN']+$row['IGV'];
	    	$array_nuevo_asiento 		=	array();
			$array_nuevo_asiento    	=	array(
				"periodo_id" 				=> $row['COD_PERIODO'],
				"nombre_periodo" 			=> $row['TXT_NOMBRE'],
				"fecha" 					=> substr($row['FEC_ASIENTO'], 0, 10),
				"servicio" 					=> '',
				"producto_id" 				=> $row['TXT_CUENTA_CONTABLE'],
				"nombre_producto" 			=> $row['TXT_GLOSA'],
				"serie" 					=> $row['NRO_SERIE'],
				"correlativo" 				=> $row['NRO_DOC'],
				"ruc" 						=> '',
				"cliente_id" 				=> $row['COD_EMPR_CLI'],
				"nombre_cliente" 			=> $row['TXT_EMPR_CLI'],
				"entrada_cantidad" 			=> 0,
				"entrada_cu" 				=> 0,
				"entrada_importe" 			=> 0,
				"salida_cantidad" 			=> 0,
				"salida_cu" 				=> 0,
				"salida_importe" 			=> $monto,
				"saldo_cantidad" 			=> 0,
				"saldo_cu" 					=> 0,
				"saldo_importe" 			=> 0,
				"tipo" 						=> 'PE'

			);
			array_push($array_detalle_asiento,$array_nuevo_asiento);
	    }


		$coleccionarrozcascara = collect($array_detalle_asiento);
		//dd($coleccionarrozcascara->where('periodo_id','=','ISCHPE0000000085')->sum('salida_importe'));
	    //TITULO DEL EXCEL
		$tipo_producto_id 		=   'TPK0000000000005';
		$tipoproducto 			= 	CMPCategoria::where('COD_CATEGORIA','=',$tipo_producto_id)->first();
		$titulo 				=   'KARDEX-'.$tipoproducto->NOM_CATEGORIA.'-'.Session::get('empresas_meta')->NOM_EMPR;


	    //Costo envase
	    $tipo_categoria 		=	'TIPO_COSTO_ENVASES';
		$listacostoenvases 		= 	CMPCategoria::where('TXT_GRUPO','=',$tipo_categoria)->where('TXT_REFERENCIA','=',$empresa_id)->get();
    	$listadetalleproductocv = 	$funcion->kd_lista_costo_producto_terminado($empresa_id, 
    																 $anio, 
    																 $tipo_categoria);

	    while ($row = $listadetalleproductocv->fetch()){
	    	$monto 						=	$row['CAN_DEBE_MN']+$row['CAN_HABER_MN']+$row['IGV'];
	    	$array_nuevo_asiento 		=	array();
			$array_nuevo_asiento    	=	array(
				"periodo_id" 				=> $row['COD_PERIODO'],
				"nombre_periodo" 			=> $row['TXT_NOMBRE'],
				"fecha" 					=> substr($row['FEC_ASIENTO'], 0, 10),
				"servicio" 					=> '',
				"producto_id" 				=> $row['TXT_CUENTA_CONTABLE'],
				"nombre_producto" 			=> $row['TXT_GLOSA'],
				"serie" 					=> $row['NRO_SERIE'],
				"correlativo" 				=> $row['NRO_DOC'],
				"ruc" 						=> '',
				"cliente_id" 				=> $row['COD_EMPR_CLI'],
				"nombre_cliente" 			=> $row['TXT_EMPR_CLI'],
				"entrada_cantidad" 			=> 0,
				"entrada_cu" 				=> 0,
				"entrada_importe" 			=> 0,
				"salida_cantidad" 			=> 0,
				"salida_cu" 				=> 0,
				"salida_importe" 			=> $monto,
				"saldo_cantidad" 			=> 0,
				"saldo_cu" 					=> 0,
				"saldo_importe" 			=> 0,
				"tipo" 						=> 'CE'

			);
			array_push($array_detalle_asiento,$array_nuevo_asiento);
	    }


		$coleccionarrozcascara = collect($array_detalle_asiento);
		//dd($coleccionarrozcascara->where('periodo_id','=','ISCHPE0000000085')->sum('salida_importe'));
	    //TITULO DEL EXCEL
		$tipo_producto_id 		=   'TPK0000000000005';
		$tipoproducto 			= 	CMPCategoria::where('COD_CATEGORIA','=',$tipo_producto_id)->first();
		$titulo 				=   'KARDEX-'.$tipoproducto->NOM_CATEGORIA.'-'.Session::get('empresas_meta')->NOM_EMPR;





		//dd($listasaldoinicial);
	    Excel::create($titulo, function($excel) use ($listasaldoinicial,$listaperido,$funcion,$anio,$empresa_id,$tipo_producto_id,$coleccionarrozcascara,$listacostovinculado,$listaproduccionet,$listacostoenvases) {

	        $excel->sheet('Costo Produccion', function($sheet) use ($listasaldoinicial,$listaperido,$funcion,$anio,$empresa_id,$tipo_producto_id,$coleccionarrozcascara,$listacostovinculado,$listaproduccionet,$listacostoenvases){
	            $sheet->loadView('kardex/excel/ecostoproduccion')->with('listasaldoinicial',$listasaldoinicial)
	            												 ->with('listaperido',$listaperido)
	            												 ->with('anio',$anio)
	            												 ->with('tipo_producto_id',$tipo_producto_id)
	            												 ->with('coleccionarrozcascara',$coleccionarrozcascara)
	            												 ->with('listacostovinculado',$listacostovinculado)
	            												 ->with('listaproduccionet',$listaproduccionet)
	            												 ->with('listacostoenvases',$listacostoenvases)
	            												 ->with('funcion',$funcion);         
	        });

	    })->export('xls');

	}



}
