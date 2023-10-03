<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use App\Modelos\TESOperacionCaja;
use App\Modelos\TESCajaBanco;
use App\Modelos\WEBKardexProducto;
use App\Modelos\WEBAsiento;
use App\Modelos\CONPeriodo;
use App\Modelos\WEBListaCVProductoKardex;
use App\Modelos\CMPTipoCambio;
use App\Modelos\STDEmpresa;
use App\Modelos\CMPOrden;
use App\Modelos\WEBKardexTransferencia;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait KardexTraits
{

	public function kd_archivo_ple_kardex($anio,$mes,$listasaldoinicial,$nombre,$path,$tipo_producto_id){


	    if (file_exists($path)) {
	        unlink("storage/kardex/ple/".$nombre);
	    } 
		$datos = fopen("storage/kardex/ple/".$nombre, "a");
		$empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
		$periodosel 			=	$anio.$mes;

		//llenado de datalle
		$array_detalle_asiento 		=	array();


		foreach($listasaldoinicial as $index => $item){

        	$saldoinicial 			= 	$this->kd_saldo_inicial_producto_id($empresa_id,$tipo_producto_id,$item->producto_id);
	    	$listadetalleproducto 	= 	$this->kd_lista_producto_periodo_view($empresa_id, 
	    																 $anio, 
	    																 '',
	    																 $item->producto_id,
	    																 '');
			$producto 				= 	ALMProducto::where('COD_PRODUCTO','=',$item->producto_id)->first();
			$periodo_enero 			= 	CONPeriodo::where('COD_ANIO','=',$anio)
										->where('COD_MES','=',1)
										->where('COD_EMPR','=',$empresa_id)
										->first();

		    $listakardexif 			= 	$this->kd_lista_kardex_inventario_final($empresa_id, 
		    																		$saldoinicial,
		    																		$listadetalleproducto,
		    																		$producto,
		    																		$periodo_enero);

		    foreach($listakardexif as $index => $item){

		    	$fecha 					=	$item["fecha"];
		    	$periodo                = 	$anio.substr($fecha, 5, 2);

		    	if($periodosel == $periodo){

			    	$periodo_01  			= 	$periodo."00";
			    	$cu  					= 	"";//falta
			    	$correlativo_02  		= 	str_pad($index+1, 9, "0", STR_PAD_LEFT);
			    	$codigo_03  			= 	'M'.$correlativo_02;
			    	$codigo_estable_04  	= 	'0000001';
			    	$codigo_catalogo_05  	= 	'9';
			    	$tipo_existencia_06  	= 	'01';
			    	$codigo_existencia_07  	= 	'';//falta
			    	$codigo_catalogo_08  	= 	'';
			    	$codigo_cubso_09  		= 	'';

			    	$comp_fecha_10  		= 	date_format(date_create($item["fecha"]), 'd/m/Y');
			    	$comp_tipo_11  			= 	'';//falta
			    	$comp_serie_12  		= 	$item['serie'];
			    	$comp_numero_13  		= 	$item['correlativo'];

			    	$tipo_op_efectu_14  	= 	'';//falta

			    	$exis_descrip_15  		= 	$item['nombre_producto'];
			    	$unidad_medida_16  		= 	'NIU';
			    	$metodo_valuacion_17  	= 	'1';

			    	$entrada_cantidad  		= 	number_format($item['entrada_cantidad'], 2, '.','');
			    	$entrada_cu  			= 	number_format($item['entrada_cu'], 2, '.', '');
			    	$entrada_importe  		= 	number_format($item['entrada_importe'], 2, '.', '');

			    	$salida_cantidad  		= 	number_format($item['salida_cantidad'], 2, '.','');
			    	$salida_cu  			= 	number_format($item['salida_cu'], 2, '.', '');
			    	$salida_importe  		= 	number_format($item['salida_importe'], 2, '.', '');

			    	$saldo_cantidad  		= 	number_format($item['saldo_cantidad'], 2, '.','');
			    	$saldo_cu  				= 	number_format($item['saldo_cu'], 2, '.', '');
			    	$saldo_importe  		= 	number_format($item['saldo_importe'], 2, '.', '');

			    	$estado  				= 	'1';
			    	$libre  				= 	'-';

			      	fwrite($datos, $periodo_01."|");
			      	fwrite($datos, $cu."|");
			      	fwrite($datos, $codigo_03."|");
			      	fwrite($datos, $codigo_estable_04."|");
			      	fwrite($datos, $codigo_catalogo_05."|");

			      	fwrite($datos, $tipo_existencia_06."|");
			      	fwrite($datos, $codigo_existencia_07."|");
			      	fwrite($datos, $codigo_catalogo_08."|");
			      	fwrite($datos, $codigo_cubso_09."|");
			      	fwrite($datos, $comp_fecha_10."|");

			      	fwrite($datos, $comp_tipo_11."|");
			      	fwrite($datos, $comp_serie_12."|");
			      	fwrite($datos, $comp_numero_13."|");
			      	fwrite($datos, $tipo_op_efectu_14."|");
			      	fwrite($datos, $exis_descrip_15."|");
			      	fwrite($datos, $unidad_medida_16."|");
			      	fwrite($datos, $metodo_valuacion_17."|");


			      	fwrite($datos, $entrada_cantidad."|");
			      	fwrite($datos, $entrada_cu."|");
			      	fwrite($datos, $entrada_importe."|");

			      	fwrite($datos, $salida_cantidad."|");
			      	fwrite($datos, $salida_cu."|");
			      	fwrite($datos, $salida_importe."|");

			      	fwrite($datos, $saldo_cantidad."|");
			      	fwrite($datos, $saldo_cu."|");
			      	fwrite($datos, $saldo_importe."|");

			      	fwrite($datos, $estado."|");
			      	fwrite($datos, $libre.PHP_EOL);

			    	$array_nuevo_asiento 	=	array();
					$array_nuevo_asiento    =	array(
						"periodo_01" 					=> $periodo_01,
						"cu" 							=> $cu,
						"codigo_03" 					=> $codigo_03,
						"codigo_estable_04" 			=> $codigo_estable_04,

						"codigo_catalogo_05" 			=> $codigo_catalogo_05,
						"tipo_existencia_06" 			=> $tipo_existencia_06,
			            "codigo_existencia_07" 			=> $codigo_existencia_07,
			            "codigo_catalogo_08" 			=> $codigo_catalogo_08,
			            "codigo_cubso_09" 				=> $codigo_cubso_09,
			            "comp_fecha_10" 				=> $comp_fecha_10,
			            "comp_tipo_11" 					=> $comp_tipo_11,
			            "comp_serie_12" 				=> $comp_serie_12,
			            "comp_numero_13"				=> $comp_numero_13,
			            "tipo_op_efectu_14" 			=> $tipo_op_efectu_14,
			            "exis_descrip_15" 				=> $exis_descrip_15,
			            "unidad_medida_16" 				=> $unidad_medida_16,
			            "metodo_valuacion_17" 			=> $metodo_valuacion_17,
			            
			            "entrada_cantidad" 				=> $entrada_cantidad,
			            "entrada_cu" 					=> $entrada_cu,
			            "entrada_importe" 				=> $entrada_importe,
			            "salida_cantidad" 				=> $salida_cantidad,
			            "salida_cu" 					=> $salida_cu,
			            "salida_importe" 				=> $salida_importe,
			            "saldo_cantidad" 				=> $saldo_cantidad,
			            "saldo_cu" 						=> $saldo_cu,
			            "saldo_importe" 				=> $saldo_importe,
			            "estado"     					=> $estado,
			            "libre"     					=> $libre
					);
					array_push($array_detalle_asiento,$array_nuevo_asiento);
		    	}



		    }
		}
	    fclose($datos);
	    return $array_detalle_asiento;
    }







	public function ar_identificador_fijo(){
        $identificador_fijo  		    = 		'LE';
        return $identificador_fijo;
    }

	public function kd_crear_nombre_venta($anio,$mes){

		$identificador 					=       $this->ar_identificador_fijo();
		$ruc 							=       Session::get('empresas_meta')->NRO_DOCUMENTO;
		$dd 							= 		'00';
		$identificador_libro 			= 		'130100';
		$cc 							= 		'00';
		$identificador_operaciones 		= 		'1';
		$i 								= 		'1';
		$m 								= 		'1';
		$g 								= 		'1';
        $nombre_archivo  		    	= 		$identificador.$ruc.$anio.$mes.$dd.$identificador_libro.$cc.$identificador_operaciones.$i.$m.$g;

        return $nombre_archivo;
    }


	public function kd_insertar_envases_saldoinicial_kardex($empresa_id,$tipo_producto_id)
	{

	    $listaproductoenv 	= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
	    						->join('ALM.PRODUCTO', 'WEB.asientomovimientos.COD_PRODUCTO', '=', 'ALM.PRODUCTO.COD_PRODUCTO')
	    						->where('WEB.asientos.COD_EMPR','=',$empresa_id)
	    						->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    						->whereIn('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO',['TAS0000000000003','TAS0000000000004'])
	    						->where('WEB.asientomovimientos.IND_PRODUCTO','=','1')
	    						->where('ALM.PRODUCTO.NOM_PRODUCTO','like','%ENVASE%')
	    						->select(DB::raw("ALM.PRODUCTO.COD_PRODUCTO,
												ALM.PRODUCTO.NOM_PRODUCTO,
												MAX(YEAR(WEB.asientos.FEC_ASIENTO)) ANIO"))
	    						->groupBy('ALM.PRODUCTO.COD_PRODUCTO')
	    						->groupBy('ALM.PRODUCTO.NOM_PRODUCTO')
								->get();

		foreach($listaproductoenv as $index => $item){
		    $kardexproducto 	= 	WEBKardexProducto::where('empresa_id','=',$empresa_id)
		    							->where('tipo_producto_id','=',$tipo_producto_id)
		    							->where('producto_id','=',$item->COD_PRODUCTO)
				    					->first();
			if(count($kardexproducto)<=0){

				$idkardex 			=   $this->funciones->getCreateIdMaestra('WEB.kardexproductos');
				$fecha_saldo  		=   $item->ANIO.'-01-01';

				$cabecera            	 	=	new WEBKardexProducto;
				$cabecera->id 	     	 	=   $idkardex;
				$cabecera->unidades 	   	=   0;
				$cabecera->cu_soles 	   	=   0;
				$cabecera->inicial_soles 	=   0;
				$cabecera->fecha_saldo_inicial 	=   $fecha_saldo;
				$cabecera->fecha_crea 	 	=   date('Ymd H:i:s');
				$cabecera->usuario_crea 	=   Session::get('usuario_meta')->id;
				$cabecera->producto_id 		=   $item->COD_PRODUCTO;
				$cabecera->tipo_producto_id =   $tipo_producto_id;
				$cabecera->empresa_id 	 	=   Session::get('empresas_meta')->COD_EMPR;
				$cabecera->serieingreso 	=   '';
				$cabecera->seriesalida 	   	=   '';
				$cabecera->correlativoingreso 	=   '0000000';
				$cabecera->correlativosalida 	=   '0000000';
				$cabecera->ruc 	=   '';
				$cabecera->save();

			}
		}

	}




	public function kd_monto_producto_material_auxiliar($listarequerimiento,$periodo,$producto_id)
	{
		$monto 				= 	0;


		$fecha_inicio			= 	date_format(date_create(substr($periodo->FEC_INICIO, 0, 10)), 'Y-m-d');
		$fecha_fin				= 	date_format(date_create(substr($periodo->FEC_FIN, 0, 10)), 'Y-m-d');
		$fecha_inicio 			= 	date($fecha_inicio);
		$fecha_fin 				= 	date($fecha_fin);

		$monto 					=	$listarequerimiento->where('COD_PRODUCTO','=',$producto_id)
	    							->where('FEC_ORDEN','>=',$fecha_inicio)
	    							->where('FEC_ORDEN','<=',$fecha_fin)
									->sum('CAN_PRODUCTO');

		return $monto;
	}


	public function kd_lista_requerimiento($empresa_id,$fecha_inicio,$fecha_fin)
	{


	    $listarequerimiento = 	CMPOrden::join('CMP.DETALLE_PRODUCTO', 'CMP.ORDEN.COD_ORDEN', '=', 'CMP.DETALLE_PRODUCTO.COD_TABLA')
	    						->where('CMP.ORDEN.COD_EMPR','=',$empresa_id)
	    						->where('CMP.ORDEN.COD_CATEGORIA_TIPO_ORDEN','=','TOR0000000000003')
	    						->where('CMP.ORDEN.COD_CATEGORIA_MOVIMIENTO_INVENTARIO','=','MIN0000000000024')
	    						->where('CMP.ORDEN.COD_CATEGORIA_ESTADO_ORDEN','=','EOR0000000000003')
	    						->where('CMP.ORDEN.COD_ESTADO','=',1)
	    						->where('CMP.DETALLE_PRODUCTO.COD_ESTADO','=',1)
	    						->where('CMP.ORDEN.FEC_ORDEN','>=',$fecha_inicio)
	    						->where('CMP.ORDEN.FEC_ORDEN','<=',$fecha_fin)
	    						->select('COD_PRODUCTO','CAN_PRODUCTO','COD_ORDEN','FEC_ORDEN')
	    						->get()->toArray();

		return $listarequerimiento;

	}


	public function kd_tipo_cambio($fecha)
	{
		$tipo_cambio   				=   CMPTipoCambio::where('FEC_CAMBIO','<=',$fecha)
										->orderBy('FEC_CAMBIO', 'DESC')
										->first();
	    return $tipo_cambio;
	}


	public function kd_monto_producto_venta_costo($listakardexif,$tioo_venta_compra,$periodo_id)
	{

		$monto 				= 	0;
		foreach($listakardexif as $index => $item){

			if($item['servicio'] == $tioo_venta_compra && $item['periodo_id'] == $periodo_id){
				if($tioo_venta_compra == 'VENTAS'){
					$monto = $monto + $item['salida_importe'];
				}
				if($tioo_venta_compra == 'COMPRAS'){
					$monto = $monto + $item['entrada_importe'];
				}
			}

			if($item['periodo_id'] == $periodo_id && $tioo_venta_compra == 'SALDOS' && $item['servicio'] <> 'Apertura'){
				$monto = $item['saldo_importe'];
			}

		}
		return $monto;
	}

	public function kd_cantidad_producto_venta_costo($data_producto_id,$data_anio,$tipo_producto_id)
	{


		$data_producto_id 		=   $data_producto_id;
		$data_periodo_id 		=   '';
		$data_anio 				=   $data_anio;
		$data_tipo_asiento_id 	=   '';
		$tipo_producto_id 		=   $tipo_producto_id;


		$producto 				= 	ALMProducto::where('COD_PRODUCTO','=',$data_producto_id)->first();
		$periodo_enero 			= 	CONPeriodo::where('COD_ANIO','=',$data_anio)
									->where('COD_MES','=',1)
									->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
									->first();


	    $saldoinicial 			= 	$this->kd_saldo_inicial_producto_id(Session::get('empresas_meta')->COD_EMPR,
	    																	$tipo_producto_id,
	    																	$data_producto_id);

	    $listadetalleproducto 	= 	$this->kd_lista_producto_periodo_view(Session::get('empresas_meta')->COD_EMPR, 
			    															$data_anio, 
			    															$data_tipo_asiento_id,
			    															$data_producto_id,
			    															$data_periodo_id);

	    $listakardexif 			= 	$this->kd_lista_kardex_inventario_final(Session::get('empresas_meta')->COD_EMPR, 
	    																	$saldoinicial,
	    																	$listadetalleproducto,
	    																	$producto,
	    																	$periodo_enero);

		return $listakardexif;
	}

	public function kd_cantidad_producto_venta($listamovimiento,$producto_id ,$periodo_id)
	{

		$monto 				= 	0;

	    $montomovi 			= 	$listamovimiento->where('COD_PRODUCTO','=',$producto_id)
	    						->where('COD_PERIODO','=',$periodo_id)
	    						->first();

	    if(count($montomovi)>0){
			$monto 			= 	$montomovi->CAN_PRODUCTO;
	    }


		return $monto;

	}

	private function kd_lista_saldo_inicial($empresa_id, $tipo_producto_id)
	{

	    $listasaldoincial 	= 	WEBKardexProducto::where('empresa_id','=',$empresa_id)
	    							->where('tipo_producto_id','=',$tipo_producto_id)
	    							->where('activo','=',1)
	    							//->where('id','=','1CIX00000126')
	    							//->take(51)
									->orderBy('id', 'asc')
			    					->get();

		return $listasaldoincial;

	}


	private function kd_saldo_inicial_producto_id($empresa_id, $tipo_producto_id,$producto_id)
	{

	    $listasaldoincial 	= 	WEBKardexProducto::where('empresa_id','=',$empresa_id)
	    							->where('tipo_producto_id','=',$tipo_producto_id)
	    							->where('producto_id','=',$producto_id)
	    							->where('activo','=',1)
			    					->first();

		return $listasaldoincial;

	}

	private function kd_lista_movimiento($empresa_id, $anio, $tipo_producto_id, $tipo_asiento_id)
	{


	    $listamovimiento 	= 	WEBAsiento::join('CMP.DETALLE_PRODUCTO', 'WEB.asientos.TXT_REFERENCIA', '=', 'CMP.DETALLE_PRODUCTO.COD_TABLA')
	    						->join('WEB.kardexproductos', 'WEB.kardexproductos.producto_id', '=', 'CMP.DETALLE_PRODUCTO.COD_PRODUCTO')
	    						->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    						//->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
	    						->TipoAsiento($tipo_asiento_id)
	    						->where('WEB.asientos.COD_EMPR','=',$empresa_id)

	    						->where('WEB.kardexproductos.empresa_id','=',$empresa_id)

	    						->where('WEB.kardexproductos.tipo_producto_id','=',$tipo_producto_id)
	    						->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    						->where('CON.PERIODO.COD_ANIO','=',$anio)
	    						->where('WEB.kardexproductos.activo','=',1)
	    						->where('CMP.DETALLE_PRODUCTO.COD_ESTADO','=',1)
	    						->select(DB::raw("CMP.DETALLE_PRODUCTO.COD_PRODUCTO,
												TXT_NOMBRE_PRODUCTO,
												sum(CAN_PRODUCTO) CAN_PRODUCTO,
												CON.PERIODO.COD_ANIO AS ANIO,
												CON.PERIODO.COD_PERIODO,
												CON.PERIODO.COD_MES,
												CON.PERIODO.TXT_NOMBRE AS MES"))
	    						->groupBy('CMP.DETALLE_PRODUCTO.COD_PRODUCTO')
	    						->groupBy('CMP.DETALLE_PRODUCTO.TXT_NOMBRE_PRODUCTO')
	    						->groupBy('CON.PERIODO.COD_ANIO')
	    						->groupBy('CON.PERIODO.COD_PERIODO')
	    						->groupBy('CON.PERIODO.COD_MES')
	    						->groupBy('CON.PERIODO.TXT_NOMBRE')->get();

		return $listamovimiento;

	}


	private function kd_lista_materialesauxiliares($empresa_id, $anio, $tipo_producto_id, $tipo_asiento_id,$cod_almacen)
	{


        $stmt 		= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.listamaproductokardex 
							@empresa_id = ?,
							@anio = ?,
							@tipo_asiento_id = ?,
							@cod_almacen = ?');

        $stmt->bindParam(1, $empresa_id ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $anio  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $tipo_asiento_id  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $cod_almacen  ,PDO::PARAM_STR);
        $stmt->execute();


		return $stmt;

	}




	public function kd_array_materialesauxiliares($empresa_id, $anio, $tipo_producto_id, $tipo_asiento_id,$cod_almacen,$listamovimientocommpra){

		$array_detalle_asiento 		=	array();

	    while ($row = $listamovimientocommpra->fetch()){

	    	$empresa 				= 	STDEmpresa::where('COD_EMPR','=', $row['COD_EMPR_CLI'])->first();
	    	$tipo_cambio_cp   		=   $this->kd_tipo_cambio(date_format(date_create(substr($row['FEC_ASIENTO'], 0, 10)), 'd-m-Y'));


	    	if($row['COD_CATEGORIA_MONEDA'] == 'MON0000000000002'){
	    		$entrada                = 	$row['CAN_PRODUCTO']*$row['PRECIO_SIGV']*$tipo_cambio_cp->CAN_VENTA_SBS;
	    	}else{
	    		$entrada                = 	$row['CAN_PRODUCTO']*$row['PRECIO_SIGV'];
	    	}

	    	$costounitario 				= 	$entrada/$row['CAN_PRODUCTO'];
	    	$costounitario 				=	number_format($costounitario, 4, '.', '');
	    	$entrada 					=	number_format($entrada, 4, '.', '');

	    	$array_nuevo_asiento 		=	array();
			$array_nuevo_asiento    	=	array(

				"FECHA" 					=> $row['FEC_ASIENTO'],
				"COD_PRODUCTO" 				=> $row['COD_PRODUCTO'],
				"TIPODOCUMENTO" 			=> $row['TXT_CATEGORIA_TIPO_DOCUMENTO'],
				"NRODOCUMENTO" 				=> $row['NRO_SERIE'].'-'.$row['NRO_DOC'],
				"NOMREF" 					=> $empresa->TXT_EMPR_CLI,
				"RUC" 						=> $empresa->NRO_DOCUMENTO,
				"DESCRIPCION" 				=> $row['TXT_NOMBRE_PRODUCTO'],
				"CANTIDAD" 					=> $row['CAN_PRODUCTO'],
				"COSTOUNITARIO" 			=> $costounitario,
				"ENTRADA" 					=> $entrada,

			);


			array_push($array_detalle_asiento,$array_nuevo_asiento);
	    }


	    return $array_detalle_asiento;

    }


	private function kd_lista_producto_periodo($empresa_id, $anio, $tipo_asiento_id,$producto_id,$periodo_id)
	{



	    $listaproducto 	= 	WEBAsiento::join('CMP.DETALLE_PRODUCTO', 'WEB.asientos.TXT_REFERENCIA', '=', 'CMP.DETALLE_PRODUCTO.COD_TABLA')
	    						->join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.asientos.COD_PERIODO')
	    						->join('STD.EMPRESA', 'STD.EMPRESA.COD_EMPR', '=', 'WEB.asientos.COD_EMPR_CLI')
	    						->where('WEB.asientos.COD_EMPR','=',$empresa_id)
	    						->where('CON.PERIODO.COD_ANIO','=',$anio)
	    						->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    						->Periodo($periodo_id)
	    						->TipoAsiento($tipo_asiento_id)
	    						->where('CMP.DETALLE_PRODUCTO.COD_PRODUCTO','=',$producto_id)
	    						->where('CMP.DETALLE_PRODUCTO.COD_ESTADO','=',1)
	    						->select(DB::raw("
	    										CON.PERIODO.COD_PERIODO
	    										,CON.PERIODO.TXT_NOMBRE AS NOMBRE_PERIODO
												,WEB.asientos.TXT_CATEGORIA_TIPO_DOCUMENTO
												,WEB.asientos.FEC_ASIENTO
												,WEB.asientos.NRO_SERIE
												,WEB.asientos.NRO_DOC

												,WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO
												,WEB.asientos.TXT_CATEGORIA_TIPO_ASIENTO

												,WEB.asientos.COD_EMPR_CLI
												,WEB.asientos.TXT_EMPR_CLI
												,STD.EMPRESA.NRO_DOCUMENTO

												,CMP.DETALLE_PRODUCTO.COD_PRODUCTO
												,CMP.DETALLE_PRODUCTO.TXT_NOMBRE_PRODUCTO
												,CMP.DETALLE_PRODUCTO.CAN_PRODUCTO
												"))
	    						->orderBy('WEB.asientos.FEC_ASIENTO', 'asc')->get();




		return $listaproducto;

	}


	private function kd_lista_producto_periodo_view($empresa_id, $anio, $tipo_asiento_id,$producto_id,$periodo_id)
	{


        $stmt 		= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.listacvproductokardex 
							@empresa_id = ?,
							@anio = ?,
							@producto_id = ?');

        $stmt->bindParam(1, $empresa_id ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $anio  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $producto_id  ,PDO::PARAM_STR);
        $stmt->execute();


		return $stmt;

	}

	public function kd_cantidad_producto_venta_totales($listamovimiento ,$periodo_id)
	{

		$monto 				= 	0;

	    $monto 				= 	$listamovimiento->where('COD_PERIODO','=',$periodo_id)
	    						->sum('CAN_PRODUCTO');


		return $monto;

	}
	public function kd_cantidad_producto_if($listamovimientocompra,$listamovimientoventa,$cantii,$producto_id ,$mes)
	{

		$mes 				= 	(int)$mes;




	    $montomovikac 		= 	WEBKardexTransferencia::join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.kardextransferencias.COD_PERIODO')
	    						->where('WEB.kardextransferencias.empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
	    						->where('WEB.kardextransferencias.producto_id','=',$producto_id)
	    						->where('WEB.kardextransferencias.activo','=',1)
	    						->where('CON.PERIODO.COD_MES','<=',$mes)
	    						->where('WEB.kardextransferencias.ingreso_salida','=','INGRESO')
	    						->sum('cantidad');


	    $montomovikav 		= 	WEBKardexTransferencia::join('CON.PERIODO', 'CON.PERIODO.COD_PERIODO', '=', 'WEB.kardextransferencias.COD_PERIODO')
	    						->where('WEB.kardextransferencias.empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
	    						->where('WEB.kardextransferencias.producto_id','=',$producto_id)
	    						->where('WEB.kardextransferencias.activo','=',1)
	    						->where('CON.PERIODO.COD_MES','<=',$mes)
	    						->where('WEB.kardextransferencias.ingreso_salida','=','SALIDA')
	    						->sum('cantidad');

		//compra
		$montocom 			= 	0;
	    $montomovic 		= 	$listamovimientocompra->where('COD_PRODUCTO','=',$producto_id)
	    						->where('COD_MES','<=',$mes)
	    						->sum('CAN_PRODUCTO');


		//venta
		$montoven 			= 	0;
	    $montomoviv 		= 	$listamovimientoventa->where('COD_PRODUCTO','=',$producto_id)
	    						->where('COD_MES','<=',$mes)
	    						->sum('CAN_PRODUCTO');


	    $monto 				= 	0;
		$monto 				= 	(float)$cantii+($montomovic-$montomoviv)+($montomovikac-$montomovikav);


		return $monto;

	}


	public function kd_lista_kardex_inventario_final($empresa_id, 
	    											$saldoinicial,
	    											$listadetalleproducto,
	    											$producto,
	    											$periodo_enero){

		$array_detalle_asiento 		=	array();

		$cantidad_antes    			= 	$saldoinicial->unidades;
		$cu_antes    				= 	$saldoinicial->cu_soles;
		$importe_antes    			= 	$saldoinicial->inicial_soles;

    	$array_nuevo_asiento 		=	array();
		$array_nuevo_asiento    	=	array(

			"periodo_id" 				=> $periodo_enero->COD_PERIODO,
			"nombre_periodo" 			=> $periodo_enero->TXT_NOMBRE,
			"fecha" 					=> substr($periodo_enero->FEC_INICIO, 0, 10),

			"servicio" 					=> 'Apertura',
			"producto_id" 				=> $producto->COD_PRODUCTO,
			"nombre_producto" 			=> $producto->NOM_PRODUCTO,

			"serie" 					=> '',
			"correlativo" 				=> '',
			"ruc" 						=> '',

			"cliente_id" 				=> '',
			"nombre_cliente" 			=> '',

			"entrada_cantidad" 			=> 0,
			"entrada_cu" 				=> 0,
			"entrada_importe" 			=> 0,

			"salida_cantidad" 			=> 0,
			"salida_cu" 				=> 0,
			"salida_importe" 			=> 0,

			"saldo_cantidad" 			=> $saldoinicial->unidades,
			"saldo_cu" 					=> $saldoinicial->cu_soles,
			"saldo_importe" 			=> $saldoinicial->inicial_soles

		);

		array_push($array_detalle_asiento,$array_nuevo_asiento);


	    while ($row = $listadetalleproducto->fetch()){
	    	//{{$row['COD_CARRO_INGRESO_SALIDA']}}

			$periodo 					= 	CONPeriodo::where('COD_PERIODO','=',$row['COD_PERIODO'])->first();

			$monedo_conversion          =   '';

			$entrada_cantidad           =   0;
			$entrada_cu           		=   0;
			$entrada_importe           	=   0;

			$salida_cantidad           	=   0;
			$salida_cu           		=   0;
			$salida_importe           	=   0;

			$saldo_cantidad           	=   0;
			$saldo_cu           		=   0;
			$saldo_importe           	=   0;

			if($row['COD_CATEGORIA_TIPO_ASIENTO'] == 'TAS0000000000004'){

				$tipo_cambio_cp   		=   $this->kd_tipo_cambio(date_format(date_create(substr($row['FEC_ASIENTO'], 0, 10)), 'd-m-Y'));

				$entrada_cantidad       =   $row['CAN_PRODUCTO'];
				$monedo_conversion      =   $row['COD_CATEGORIA_MONEDA_CONVERSION'];
				if($monedo_conversion == 'MON0000000000002'){
					$entrada_importe        =   $row['CAN_VALOR_VENTA_IGV']*$tipo_cambio_cp->CAN_VENTA_SBS;
				}else{
					$entrada_importe        =   $row['CAN_VALOR_VENTA_IGV'];
				}
				$entrada_cu           	=   $entrada_importe/$entrada_cantidad;

				if($row['TXT_CATEGORIA_TIPO_ASIENTO']=='INGRESO TRANSFERENCIA'){
					$entrada_cantidad   =   $row['CAN_PRODUCTO'];
					$entrada_cu    		=   $row['cu'];
					$entrada_importe    =   $row['CAN_VALOR_VENTA_IGV'];					
				}

				$saldo_cantidad         =   $cantidad_antes+$entrada_cantidad-$salida_cantidad;
				$saldo_importe          =   $importe_antes+$entrada_importe-$salida_importe;
				if($saldo_cantidad==0){
					$saldo_cu           	=   0;
				}else{
					$saldo_cu           	=   $saldo_importe/$saldo_cantidad;
				}

				$cantidad_antes    		= 	$saldo_cantidad;
				$cu_antes    			= 	$saldo_cu;
				$importe_antes    		= 	$saldo_importe;


			}else{

				$salida_cantidad        =   $row['CAN_PRODUCTO'];
				$salida_cu           	=   $cu_antes;
				$salida_importe         =   $salida_cantidad*$salida_cu;

				if($row['TXT_CATEGORIA_TIPO_ASIENTO']=='SALIDA TRANSFERENCIA'){
					$salida_cantidad        =   $row['CAN_PRODUCTO'];
					$salida_cu           	=   $row['cu'];
					$salida_importe         =   $row['CAN_VALOR_VENTA_IGV'];					
				}

				//NOTA DE CREDITO
				if($row['TXT_CATEGORIA_TIPO_ASIENTO']=='NOTA DE CREDITO'){
					$tipo_cambio_cp   		=   $this->kd_tipo_cambio(date_format(date_create(substr($row['FECHA_REFERENCIA'], 0, 10)), 'd-m-Y'));
					$salida_cantidad       	=   $row['CAN_PRODUCTO'];
					$monedo_conversion      =   $row['COD_CATEGORIA_MONEDA_CONVERSION'];
					if($monedo_conversion == 'MON0000000000002'){
						$salida_importe     =   $row['CAN_VALOR_VENTA_IGV_REF']*$tipo_cambio_cp->CAN_VENTA_SBS;
					}else{
						$salida_importe     =   $row['CAN_VALOR_VENTA_IGV_REF'];
					}
					$salida_cu           	=   $salida_importe/$salida_cantidad;					
				}

				

				$saldo_cantidad         =   $cantidad_antes+$entrada_cantidad-$salida_cantidad;
				$saldo_importe          =   $importe_antes+$entrada_importe-$salida_importe;
				if($saldo_cantidad==0){
					$saldo_cu           	=   0;
				}else{
					$saldo_cu           	=   $saldo_importe/$saldo_cantidad;
				}


				$cantidad_antes    		= 	$saldo_cantidad;
				$cu_antes    			= 	$saldo_cu;
				$importe_antes    		= 	$saldo_importe;



			}
									
	    	$array_nuevo_asiento 		=	array();
			$array_nuevo_asiento    	=	array(
				"periodo_id" 				=> $periodo->COD_PERIODO,
				"nombre_periodo" 			=> $periodo->TXT_NOMBRE,
				"fecha" 					=> substr($row['FEC_ASIENTO'], 0, 10),
				"servicio" 					=> $row['TXT_CATEGORIA_TIPO_ASIENTO'],
				"producto_id" 				=> $row['COD_PRODUCTO'],
				"nombre_producto" 			=> $row['TXT_NOMBRE_PRODUCTO'],
				"serie" 					=> $row['NRO_SERIE'],
				"correlativo" 				=> $row['NRO_DOC'],
				"ruc" 						=> $row['NRO_DOCUMENTO'],
				"cliente_id" 				=> $row['COD_EMPR_CLI'],
				"nombre_cliente" 			=> $row['TXT_EMPR_CLI'],

				"entrada_cantidad" 			=> $entrada_cantidad,
				"entrada_cu" 				=> $entrada_cu,
				"entrada_importe" 			=> $entrada_importe,

				"salida_cantidad" 			=> $salida_cantidad,
				"salida_cu" 				=> $salida_cu,
				"salida_importe" 			=> $salida_importe,

				"saldo_cantidad" 			=> $saldo_cantidad,
				"saldo_cu" 					=> $saldo_cu,
				"saldo_importe" 			=> $saldo_importe

			);
			array_push($array_detalle_asiento,$array_nuevo_asiento);


	    }


	    return $array_detalle_asiento;

    }







	public function kd_cabecera_asiento($periodo,$empresa_id,$monto_total,$tipoproducto,$tipo_referencia,$tipo_cambio){

		$array_detalle_asiento 		=	array();

		$glosa 						= 	"";
		if($tipoproducto->COD_CATEGORIA =='TPK0000000000001'){
			$glosa 						= 	"ENVASES : COSTO DE ENVASES / ".$periodo->TXT_NOMBRE;
		}

		if($tipoproducto->COD_CATEGORIA =='TPK0000000000002'){
			$glosa 						= 	"CONSUMO MATERIALES AUXILIARES";
		}

    	$array_nuevo_asiento 		=	array();
		$array_nuevo_asiento    	=	array(

			"periodo_id" 				=> $periodo->COD_PERIODO,
			"nombre_periodo" 			=> $periodo->TXT_NOMBRE,
			"fecha" 					=> substr($periodo->FEC_FIN, 0, 10),
			"empresa_id" 				=> $empresa_id,
            "tipo_referencia"           => $tipo_referencia,
			"glosa" 					=> $glosa,
            "tipo_cambio"               => $tipo_cambio->CAN_COMPRA_SBS,
			"moneda_id" 				=> "MON0000000000001",
			"moneda" 					=> "SOLES",

			"total_debe" 				=> $monto_total,
			"total_haber" 				=> $monto_total,

		);
		array_push($array_detalle_asiento,$array_nuevo_asiento);
	    return $array_detalle_asiento;

    }


	public function kd_detalle_asiento($periodo,$empresa_id,$monto_total,$data_anio,$tipoproducto,$tipo_cambio){

		$array_detalle_asiento 		=	array();


        $monto_total_dolar_debe = $monto_total/$tipo_cambio->CAN_COMPRA_SBS;

		//ENVASES
		if($tipoproducto->COD_CATEGORIA =='TPK0000000000001'){

		    $cuentacontable 			= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
											->where('anio','=',$data_anio)
											->where('nro_cuenta','=','691111')
											->where('activo','=',1)
					    					->first();
	    	$array_nuevo_asiento 		=	array();
			$array_nuevo_asiento    	=	array(
				"linea" 					=> 1,
				"cuenta_id" 				=> $cuentacontable->id,
				"cuenta_nrocuenta" 			=> $cuentacontable->nro_cuenta,
				"glosa" 					=> $cuentacontable->nombre,
				"fecha" 					=> substr($periodo->FEC_FIN, 0, 10),

				"empresa_id" 				=> $empresa_id,
				"moneda_id" 				=> "MON0000000000001",
				"moneda" 					=> "SOLES",

				"total_debe" 				=> $monto_total,
				"total_haber" 				=> 0,

                "total_debe_dolar"          => $monto_total_dolar_debe,
                "total_haber_dolar"         => 0


			);

			array_push($array_detalle_asiento,$array_nuevo_asiento);


		    $cuentacontable 			= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
											->where('anio','=',$data_anio)
											->where('nro_cuenta','=','201111')
											->where('activo','=',1)
					    					->first();
	    	$array_nuevo_asiento 		=	array();
			$array_nuevo_asiento    	=	array(
				"linea" 					=> 2,
				"cuenta_id" 				=> $cuentacontable->id,
				"cuenta_nrocuenta" 			=> $cuentacontable->nro_cuenta,
				"glosa" 					=> $cuentacontable->nombre,
				"fecha" 					=> substr($periodo->FEC_FIN, 0, 10),
				"empresa_id" 				=> $empresa_id,
				"moneda_id" 				=> "MON0000000000001",
				"moneda" 					=> "SOLES",
				"total_debe" 				=> 0,
				"total_haber" 				=> $monto_total,
                "total_debe_dolar"          => 0,
                "total_haber_dolar"         => $monto_total_dolar_debe


			);

			array_push($array_detalle_asiento,$array_nuevo_asiento);
		}

		//BOBINAS
		if($tipoproducto->COD_CATEGORIA =='TPK0000000000002'){
		    $cuentacontable 			= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
											->where('anio','=',$data_anio)
											->where('nro_cuenta','=','613102')
											->where('activo','=',1)
					    					->first();
	    	$array_nuevo_asiento 		=	array();
			$array_nuevo_asiento    	=	array(
				"linea" 					=> 1,
				"cuenta_id" 				=> $cuentacontable->id,
				"cuenta_nrocuenta" 			=> $cuentacontable->nro_cuenta,
				"glosa" 					=> $cuentacontable->nombre,
				"fecha" 					=> substr($periodo->FEC_FIN, 0, 10),
				"empresa_id" 				=> $empresa_id,
				"moneda_id" 				=> "MON0000000000001",
				"moneda" 					=> "SOLES",
				"total_debe" 				=> $monto_total,
				"total_haber" 				=> 0,

			);

			array_push($array_detalle_asiento,$array_nuevo_asiento);


		    $cuentacontable 			= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
											->where('anio','=',$data_anio)
											->where('nro_cuenta','=','251102')
											->where('activo','=',1)
					    					->first();
	    	$array_nuevo_asiento 		=	array();
			$array_nuevo_asiento    	=	array(
				"linea" 					=> 2,
				"cuenta_id" 				=> $cuentacontable->id,
				"cuenta_nrocuenta" 			=> $cuentacontable->nro_cuenta,
				"glosa" 					=> $cuentacontable->nombre,
				"fecha" 					=> substr($periodo->FEC_FIN, 0, 10),
				"empresa_id" 				=> $empresa_id,
				"moneda_id" 				=> "MON0000000000001",
				"moneda" 					=> "SOLES",
				"total_debe" 				=> 0,
				"total_haber" 				=> $monto_total,

			);

			array_push($array_detalle_asiento,$array_nuevo_asiento);


		    $cuentacontable 			= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
											->where('anio','=',$data_anio)
											->where('nro_cuenta','=','911213')
											->where('activo','=',1)
					    					->first();
	    	$array_nuevo_asiento 		=	array();
			$array_nuevo_asiento    	=	array(
				"linea" 					=> 1,
				"cuenta_id" 				=> $cuentacontable->id,
				"cuenta_nrocuenta" 			=> $cuentacontable->nro_cuenta,
				"glosa" 					=> $cuentacontable->nombre,
				"fecha" 					=> substr($periodo->FEC_FIN, 0, 10),
				"empresa_id" 				=> $empresa_id,
				"moneda_id" 				=> "MON0000000000001",
				"moneda" 					=> "SOLES",
				"total_debe" 				=> $monto_total,
				"total_haber" 				=> 0,

			);

			array_push($array_detalle_asiento,$array_nuevo_asiento);


		    $cuentacontable 			= 	WEBCuentaContable::where('empresa_id','=',$empresa_id)
											->where('anio','=',$data_anio)
											->where('nro_cuenta','=','791101')
											->where('activo','=',1)
					    					->first();
	    	$array_nuevo_asiento 		=	array();
			$array_nuevo_asiento    	=	array(
				"linea" 					=> 2,
				"cuenta_id" 				=> $cuentacontable->id,
				"cuenta_nrocuenta" 			=> $cuentacontable->nro_cuenta,
				"glosa" 					=> $cuentacontable->nombre,
				"fecha" 					=> substr($periodo->FEC_FIN, 0, 10),
				"empresa_id" 				=> $empresa_id,
				"moneda_id" 				=> "MON0000000000001",
				"moneda" 					=> "SOLES",
				"total_debe" 				=> 0,
				"total_haber" 				=> $monto_total,

			);

			array_push($array_detalle_asiento,$array_nuevo_asiento);


		}






	    return $array_detalle_asiento;

    }

}