<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use App\Modelos\CONPeriodo;
use App\Modelos\WEBViewMigrarVenta;
use App\Modelos\CMPDocumentoCtble;
use App\Modelos\WEBHistorialMigrar;
use App\Modelos\CMPDetalleProducto;
use App\Modelos\WEBProductoEmpresa;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait MigrarVentaTraits
{
	
	public function mv_array_empresa_venta(){
        $array_empresas  		    = 		['IACHEM0000001339','EMP0000000000007'];
        return $array_empresas;
    }
	
	private function mv_lista_ventas_migrar_agrupado_emitido()
	{
		
		$array_empresas  		    = 		$this->mv_array_empresa_venta();

		$array_periodo				=		CONPeriodo::where('COD_ANIO','>=',2022)
											->whereIn('COD_EMPR',$array_empresas)
											->where('COD_ESTADO','=','1')
											->pluck('COD_PERIODO')
											->toArray();

		$lista_migrar_ventas		=		WEBViewMigrarVenta::leftJoin('WEB.historialmigrar', function ($join) {
									            $join->on('WEB.historialmigrar.COD_REFERENCIA', '=', 'WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE')
									                 ->where('WEB.historialmigrar.IND_ASIENTO_MODELO', '=', 1);
									        })
											->whereNull('WEB.historialmigrar.COD_REFERENCIA')
											->whereIn('WEB.viewmigrarventas.COD_PERIODO',$array_periodo)
											->whereIn('WEB.viewmigrarventas.COD_EMPR',$array_empresas)
											//->where('WEB.viewmigrarventas.FEC_EMISION','=','2022-01-07')
											->where('WEB.viewmigrarventas.NOM_ESTADO','=','EMITIDO')
											->select(DB::raw('WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE'))
											->groupBy('WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE')
											->get();

		return $lista_migrar_ventas;

	}

	private function mv_lista_ventas_migrar_agrupado_anulado()
	{
		
		$array_empresas  		    = 		$this->mv_array_empresa_venta();

		$array_periodo				=		CONPeriodo::where('COD_ANIO','>=',2022)
											->whereIn('COD_EMPR',$array_empresas)
											->where('COD_ESTADO','=','1')
											->pluck('COD_PERIODO')
											->toArray();

		$lista_migrar_ventas		=		WEBViewMigrarVenta::leftJoin('WEB.historialmigrar', function ($join) {
									            $join->on('WEB.historialmigrar.COD_REFERENCIA', '=', 'WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE')
									                 ->where('WEB.historialmigrar.IND_ANULADO', '=', 1);
									        })
											->whereNull('WEB.historialmigrar.COD_REFERENCIA')
											->whereIn('WEB.viewmigrarventas.COD_PERIODO',$array_periodo)
											->whereIn('WEB.viewmigrarventas.COD_EMPR',$array_empresas)
											//->where('WEB.viewmigrarventas.FEC_EMISION','=','2022-01-07')
											->where('WEB.viewmigrarventas.NOM_ESTADO','=','ANULADO')
											->select(DB::raw('WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE'))
											->groupBy('WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE')
											->get();


		return $lista_migrar_ventas;

	}




	private function mv_agregar_historial_ventas($lista_ventas_migrar_emitida,$lista_ventas_migrar_anulada,$tipo_asiento)
	{
	
		//ver e insertar uno a uno los documentos
		foreach($lista_ventas_migrar_emitida as $index => $item){
			

			$documento_anulado 							=   1;
			$documento_ctble 							= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$item->COD_DOCUMENTO_CTBLE)->first();
			$historialmigrar 							=   WEBHistorialMigrar::where('COD_REFERENCIA','=',$item->COD_DOCUMENTO_CTBLE)
															->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)->first();

			if($documento_ctble->COD_CATEGORIA_ESTADO_DOC_CTBLE == 'EDC0000000000002'){
				$documento_anulado 						=   0;
			}

			if(count($historialmigrar)<=0){
				$cabecera            	 				=	new WEBHistorialMigrar;
				$cabecera->COD_REFERENCIA 				=   $documento_ctble->COD_DOCUMENTO_CTBLE;
				$cabecera->TXT_TIPO_REFERENCIA			=   'CMP_DOCUMENTO_CTBLE';
				$cabecera->COD_CATEGORIA_TIPO_ASIENTO 	=   'TAS0000000000003';
				$cabecera->TXT_CATEGORIA_TIPO_ASIENTO 	=   'VENTAS';
				$cabecera->COD_EMPR 					=   $documento_ctble->COD_EMPR;
				$cabecera->COD_PERIODO 					=   $documento_ctble->COD_PERIODO;
				$cabecera->IND_ERROR 					=   -1;
				$cabecera->IND_ASIENTO_MODELO 			=   -1;
				$cabecera->COD_ASIENTO_MODELO 			=   '';
				$cabecera->TXT_ERROR 					=   '';
				$cabecera->IND_CORREO 					=   -1;
				$cabecera->IND_ANULADO 					=   $documento_anulado;
				$cabecera->save();
			}else{

				$historialmigrar->IND_ERROR 			=   -1;
				$historialmigrar->IND_ASIENTO_MODELO 	=   -1;
				$historialmigrar->COD_ASIENTO_MODELO 	=   '';
				$historialmigrar->TXT_ERROR 			=   '';
				$historialmigrar->IND_CORREO 			=   -1;
				$historialmigrar->IND_ANULADO 			=   $documento_anulado;
				$historialmigrar->save();

			}

		}

		//ver e insertar uno a uno los documentos
		foreach($lista_ventas_migrar_anulada as $index => $item){
			

			$documento_anulado 							=   0;
			$documento_ctble 							= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$item->COD_DOCUMENTO_CTBLE)->first();
			$historialmigrar 							=   WEBHistorialMigrar::where('COD_REFERENCIA','=',$item->COD_DOCUMENTO_CTBLE)
															->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)->first();


			if(count($historialmigrar)<=0){
				$cabecera            	 				=	new WEBHistorialMigrar;
				$cabecera->COD_REFERENCIA 				=   $documento_ctble->COD_DOCUMENTO_CTBLE;
				$cabecera->TXT_TIPO_REFERENCIA			=   'CMP_DOCUMENTO_CTBLE';
				$cabecera->COD_CATEGORIA_TIPO_ASIENTO 	=   'TAS0000000000003';
				$cabecera->TXT_CATEGORIA_TIPO_ASIENTO 	=   'VENTAS';
				$cabecera->COD_EMPR 					=   $documento_ctble->COD_EMPR;
				$cabecera->COD_PERIODO 					=   $documento_ctble->COD_PERIODO;
				$cabecera->IND_ERROR 					=   -1;
				$cabecera->IND_ASIENTO_MODELO 			=   -1;
				$cabecera->COD_ASIENTO_MODELO 			=   '';
				$cabecera->TXT_ERROR 					=   '';
				$cabecera->IND_CORREO 					=   -1;
				$cabecera->IND_ANULADO 					=   $documento_anulado;
				$cabecera->save();
			}else{

				$historialmigrar->IND_ERROR 			=   -1;
				$historialmigrar->IND_ASIENTO_MODELO 	=   -1;
				$historialmigrar->COD_ASIENTO_MODELO 	=   '';
				$historialmigrar->TXT_ERROR 			=   '';
				$historialmigrar->IND_CORREO 			=   -1;
				$historialmigrar->IND_ANULADO 			=   $documento_anulado;
				$historialmigrar->save();

			}

		}



	}



	private function mv_lista_ventas_asignar($tipo_asiento)
	{
		
		$array_empresas  		    = 		$this->mv_array_empresa_venta();
		$lista_ventas				=		WEBHistorialMigrar::whereIn('COD_EMPR',$array_empresas)
											->where('IND_ASIENTO_MODELO','=',0)
											->where('IND_ERROR','<>',1)
											->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)
											->get();
		return $lista_ventas;

	}

	private function mv_lista_ventas_asignar_xdocumento($documento_id,$tipo_asiento)
	{
		
		$array_empresas  		    = 		$this->mv_array_empresa_venta();
		$lista_ventas				=		WEBHistorialMigrar::whereIn('COD_EMPR',$array_empresas)
											->where('IND_ASIENTO_MODELO','=',0)
											->where('COD_REFERENCIA','=',$documento_id)
											->where('IND_ERROR','<>',1)
											->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)
											->get();
		return $lista_ventas;

	}


	private function mv_lista_ventas_observadas($tipo_asiento,$empresa_id)
	{
		

		$array_empresas  		    = 		$this->mv_array_empresa_venta();
		$lista_ventas				=		WEBHistorialMigrar::whereIn('COD_EMPR',$array_empresas)
											->where('IND_ASIENTO_MODELO','=',-1)
											->where('IND_ERROR','=',1)
											->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)
											->where('COD_EMPR','=',$empresa_id)
											->orderby('COD_REFERENCIA','asc')
											->get();
		return $lista_ventas;

	}

	private function mv_lista_ventas_observadas_xperiodo($tipo_asiento,$empresa_id,$periodo_id)
	{
		

		$array_empresas  		    = 		$this->mv_array_empresa_venta();
		$lista_ventas				=		WEBHistorialMigrar::whereIn('COD_EMPR',$array_empresas)
											->where('IND_ASIENTO_MODELO','=',-1)
											->where('IND_ERROR','=',1)
											->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)
											->where('COD_PERIODO','=',$periodo_id)
											->where('COD_EMPR','=',$empresa_id)
											->orderby('COD_REFERENCIA','asc')
											->get();
		return $lista_ventas;

	}



	private function mv_lista_productos_sin_configuracion($tipo_asiento,$empresa_id,$anio)
	{
		
		$array_ventas_con_error		=		WEBHistorialMigrar::where('COD_EMPR','=',$empresa_id)
											->where('IND_ASIENTO_MODELO','=',-1)
											->where('IND_ERROR','=',1)
											->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)
											->where('TXT_ERROR','=','Hay productos que no tienen asociados cuenta contable')
											->pluck('COD_REFERENCIA')
											->toArray();


		if($tipo_asiento=='TAS0000000000003'){

			$array_producto_empresas 	=  		WEBProductoEmpresa::where('empresa_id','=',$empresa_id)
												->where('activo','=',1)
												->where('anio','=',$anio)
												->where('cuenta_contable_venta_relacionada_id','<>','')
												->where('cuenta_contable_venta_tercero_id','<>','')
												->pluck('producto_id')
												->toArray();

		}else{
			$array_producto_empresas 	=  		WEBProductoEmpresa::where('empresa_id','=',$empresa_id)
												->where('activo','=',1)
												->where('anio','=',$anio)
												->where('cuenta_contable_compra_id','<>','')
												->pluck('producto_id')
												->toArray();
		}


		$lista_productos_sc			=		CMPDetalleProducto::whereIn('COD_TABLA',$array_ventas_con_error)
											->whereNotIn('COD_PRODUCTO',$array_producto_empresas)
											->groupBy('COD_PRODUCTO')
											->pluck('COD_PRODUCTO')
											->toArray();

		return $lista_productos_sc;

	}


	private function mv_asignar_asiento_modelo($historialmigrar,$tipo_asiento)
	{
	
		$documento_ctble 			= 		CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$historialmigrar->COD_REFERENCIA)->first();
		$periodo 					= 		CONPeriodo::where('COD_PERIODO','=',$documento_ctble->COD_PERIODO)->first();
		$anio 						= 		$periodo->COD_ANIO;
		$empresa 					= 		$documento_ctble->COD_EMPR;
		$cod_contable 				= 		$documento_ctble->COD_DOCUMENTO_CTBLE;
		$asiento_modelo_id 			= 		trim($historialmigrar->COD_ASIENTO_MODELO);
		$anulado 					= 		$historialmigrar->IND_ANULADO;


        $stmt 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.APLICAR_ASIENTO_MODELO 
											@anio = ?,
											@empresa = ?,
											@cod_contable = ?,
											@cod_tipo_asiento = ?,
											@asiento_modelo_id = ?,
											@ind_anulado = ?');

        $stmt->bindParam(1, $anio ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $empresa  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $cod_contable  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $tipo_asiento  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $asiento_modelo_id  ,PDO::PARAM_STR);
        $stmt->bindParam(6, $anulado  ,PDO::PARAM_STR);
        $stmt->execute();

		$historialmigrar->IND_ASIENTO_MODELO 	=   1;
		$historialmigrar->IND_CORREO 			=   -1;
		$historialmigrar->save();


	}



	private function mv_update_historial_ventas($documento_ctble_cod,$tipo_asiento)
	{
	

		$documento_ctble 			= 		CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$documento_ctble_cod)->first();
		$periodo 					= 		CONPeriodo::where('COD_PERIODO','=',$documento_ctble->COD_PERIODO)->first();
		$anio 						= 		$periodo->COD_ANIO;
		$empresa 					= 		$documento_ctble->COD_EMPR;
		$cod_contable 				= 		$documento_ctble->COD_DOCUMENTO_CTBLE;

		$documento_anulado 			=   	1;
		if($documento_ctble->COD_CATEGORIA_ESTADO_DOC_CTBLE == 'EDC0000000000002'){
			$documento_anulado 		=   	0;
		}

        $stmt 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.BUSCAR_ASIENTO_MODELO 
											@anio = ?,
											@empresa = ?,
											@cod_contable = ?,
											@cod_tipo_asiento = ?,
											@ind_anulado = ?');

        $stmt->bindParam(1, $anio ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $empresa  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $cod_contable  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $tipo_asiento  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $documento_anulado  ,PDO::PARAM_STR);
        $stmt->execute();

      	while ($row = $stmt->fetch()){
      		$codigo = $row['codigo'];
      		$mensaje = $row['mensaje'];
      		$asiento_modelo_id = $row['asiento_modelo_id'];

      		$historialmigrar 						=   WEBHistorialMigrar::where('COD_REFERENCIA','=',$documento_ctble_cod)
      													->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)->first();
			
      		if($codigo=='1'){

				$historialmigrar->IND_ERROR 			=   -1;
				$historialmigrar->IND_ASIENTO_MODELO 	=   0;
				$historialmigrar->COD_ASIENTO_MODELO 	=   $asiento_modelo_id;
				$historialmigrar->IND_CORREO 			=   -1;
				$historialmigrar->save();

      		}else{

      			$historialmigrar->IND_ERROR 			=   1;
				$historialmigrar->IND_ASIENTO_MODELO 	=   -1;
				$historialmigrar->COD_ASIENTO_MODELO 	=   '';
				$historialmigrar->TXT_ERROR 			=   $mensaje;
				$historialmigrar->IND_CORREO 			=   0;
				$historialmigrar->save();

      		}

      	}

      	return "se realizo con exito";

	}

}