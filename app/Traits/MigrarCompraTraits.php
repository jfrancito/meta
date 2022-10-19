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
use App\Modelos\WEBViewMigrarCompras;



use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait MigrarCompraTraits
{
	
	private function mv_lista_compras_migrar_agrupado_emitido($array_empresas,$anio)
	{

		$array_empresas  		    = 		$array_empresas;
		$anio  		    			= 		$anio;		

		$array_periodo				=		CONPeriodo::where('COD_ANIO','>=',$anio)
											->whereIn('COD_EMPR',$array_empresas)
											->where('COD_ESTADO','=','1')
											->pluck('COD_PERIODO')
											->toArray();

		$lista_migrar_compras		=		WEBViewMigrarCompras::leftJoin('WEB.historialmigrar', function ($join) {
									            $join->on('WEB.historialmigrar.COD_REFERENCIA', '=', 'WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE')
									                 ->where('WEB.historialmigrar.IND_ASIENTO_MODELO', '=', 1);
									        })
											->whereNull('WEB.historialmigrar.COD_REFERENCIA')
											->whereIn('WEB.viewmigrarcompras.COD_PERIODO',$array_periodo)
											->whereIn('WEB.viewmigrarcompras.COD_EMPR',$array_empresas)
											//->where('WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE','=','ICCHFC0000047269')
											->where('WEB.viewmigrarcompras.NOM_ESTADO','=','ENVIADO')
											->select(DB::raw('WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE'))
											->groupBy('WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE')
											->get();



		return $lista_migrar_compras;

	}


	private function mv_lista_compras_migrar_agrupado_emitidoxdocumento($array_empresas,$anio,$cod_documento)
	{

		$array_empresas  		    = 		$array_empresas;
		$anio  		    			= 		$anio;		

		$array_periodo				=		CONPeriodo::where('COD_ANIO','>=',$anio)
											->whereIn('COD_EMPR',$array_empresas)
											->where('COD_ESTADO','=','1')
											->pluck('COD_PERIODO')
											->toArray();

		$lista_migrar_compras		=		WEBViewMigrarCompras::leftJoin('WEB.historialmigrar', function ($join) {
									            $join->on('WEB.historialmigrar.COD_REFERENCIA', '=', 'WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE')
									                 ->where('WEB.historialmigrar.IND_ASIENTO_MODELO', '=', 1);
									        })
											->whereNull('WEB.historialmigrar.COD_REFERENCIA')
											->whereIn('WEB.viewmigrarcompras.COD_PERIODO',$array_periodo)
											->whereIn('WEB.viewmigrarcompras.COD_EMPR',$array_empresas)
											->where('WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE','=',$cod_documento)
											->where('WEB.viewmigrarcompras.NOM_ESTADO','=','ENVIADO')
											->select(DB::raw('WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE'))
											->groupBy('WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE')
											->get();



		return $lista_migrar_compras;

	}



	private function mv_lista_compras_migrar_agrupado_anulado($array_empresas,$anio)
	{
		
		$array_empresas  		    = 		$array_empresas;
		$anio  		    			= 		$anio;

		$array_periodo				=		CONPeriodo::where('COD_ANIO','>=',$anio)
											->whereIn('COD_EMPR',$array_empresas)
											->where('COD_ESTADO','=','1')
											->pluck('COD_PERIODO')
											->toArray();

		$lista_migrar_compras		=		WEBViewMigrarCompras::leftJoin('WEB.historialmigrar', function ($join) {
									            $join->on('WEB.historialmigrar.COD_REFERENCIA', '=', 'WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE')
									                 ->where('WEB.historialmigrar.IND_ANULADO', '=', 1);
									        })
											->whereNull('WEB.historialmigrar.COD_REFERENCIA')
											->whereIn('WEB.viewmigrarcompras.COD_PERIODO',$array_periodo)
											->whereIn('WEB.viewmigrarcompras.COD_EMPR',$array_empresas)
											//->where('WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE','=','ICCHFC0000047269')
											->where('WEB.viewmigrarcompras.NOM_ESTADO','=','EXTORNADO')
											->select(DB::raw('WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE'))
											->groupBy('WEB.viewmigrarcompras.COD_DOCUMENTO_CTBLE')
											->get();


		return $lista_migrar_compras;

	}





	private function mv_agregar_historial_compras($lista_compras_migrar_emitido,$lista_compras_migrar_anulado,$tipo_asiento)
	{
	
		//ver e insertar uno a uno los documentos
		foreach($lista_compras_migrar_emitido as $index => $item){
			

			$documento_anulado 							=   1;
			$documento_ctble 							= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$item->COD_DOCUMENTO_CTBLE)->first();
			$historialmigrar 							=   WEBHistorialMigrar::where('COD_REFERENCIA','=',$item->COD_DOCUMENTO_CTBLE)
															->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)->first();

			if($documento_ctble->COD_CATEGORIA_ESTADO_DOC_CTBLE == 'EDC0000000000012'){
				$documento_anulado 						=   0;
			}

			if(count($historialmigrar)<=0){
				$cabecera            	 				=	new WEBHistorialMigrar;
				$cabecera->COD_REFERENCIA 				=   $documento_ctble->COD_DOCUMENTO_CTBLE;
				$cabecera->TXT_TIPO_REFERENCIA			=   'CMP_DOCUMENTO_CTBLE';
				$cabecera->COD_CATEGORIA_TIPO_ASIENTO 	=   $tipo_asiento;
				$cabecera->TXT_CATEGORIA_TIPO_ASIENTO 	=   'COMPRAS';
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
		foreach($lista_compras_migrar_anulado as $index => $item){
			

			$documento_anulado 							=   0;
			$documento_ctble 							= 	CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$item->COD_DOCUMENTO_CTBLE)->first();
			$historialmigrar 							=   WEBHistorialMigrar::where('COD_REFERENCIA','=',$item->COD_DOCUMENTO_CTBLE)
															->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)->first();

			if(count($historialmigrar)<=0){
				$cabecera            	 				=	new WEBHistorialMigrar;
				$cabecera->COD_REFERENCIA 				=   $documento_ctble->COD_DOCUMENTO_CTBLE;
				$cabecera->TXT_TIPO_REFERENCIA			=   'CMP_DOCUMENTO_CTBLE';
				$cabecera->COD_CATEGORIA_TIPO_ASIENTO 	=   $tipo_asiento;
				$cabecera->TXT_CATEGORIA_TIPO_ASIENTO 	=   'COMPRAS';
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


	private function mv_update_historial_compras($documento_ctble_cod,$tipo_asiento)
	{
	

		$documento_ctble 			= 		CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$documento_ctble_cod)->first();
		$periodo 					= 		CONPeriodo::where('COD_PERIODO','=',$documento_ctble->COD_PERIODO)->first();
		$anio 						= 		$periodo->COD_ANIO;
		$empresa 					= 		$documento_ctble->COD_EMPR;
		$cod_contable 				= 		$documento_ctble->COD_DOCUMENTO_CTBLE;

		$documento_anulado 			=   	1;
		if($documento_ctble->COD_CATEGORIA_ESTADO_DOC_CTBLE == 'EDC0000000000012'){
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


	private function mv_lista_compras_asignar($array_empresas,$tipo_asiento)
	{
		
		$lista_ventas				=		WEBHistorialMigrar::whereIn('COD_EMPR',$array_empresas)
											->where('IND_ASIENTO_MODELO','=',0)
											->where('IND_ERROR','<>',1)
											->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)
											->get();
		return $lista_ventas;

	}

	private function mv_lista_compras_asignarxdocumento($array_empresas,$tipo_asiento,$documento)
	{
		
		$lista_ventas				=		WEBHistorialMigrar::whereIn('COD_EMPR',$array_empresas)
											->where('IND_ASIENTO_MODELO','=',0)
											->where('COD_REFERENCIA','=',$documento)
											->where('IND_ERROR','<>',1)
											->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)
											->get();
		return $lista_ventas;

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

	private function mv_asignar_asiento_modelo_x_fechaemision($historialmigrar,$tipo_asiento,$fechaemision)
	{
	
		$documento_ctble 			= 		CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$historialmigrar->COD_REFERENCIA)->first();
		$periodo 					= 		CONPeriodo::where('COD_PERIODO','=',$documento_ctble->COD_PERIODO)->first();
		$anio 						= 		$periodo->COD_ANIO;
		$empresa 					= 		$documento_ctble->COD_EMPR;
		$cod_contable 				= 		$documento_ctble->COD_DOCUMENTO_CTBLE;
		$asiento_modelo_id 			= 		trim($historialmigrar->COD_ASIENTO_MODELO);

		if($documento_ctble->ESTADO_ELEC == 'R'){
		    $anulado 				= 		0;
		}else{
			$anulado 				= 		$historialmigrar->IND_ANULADO;	
		}


        $stmt 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.APLICAR_ASIENTO_MODELO 
											@anio = ?,
											@empresa = ?,
											@cod_contable = ?,
											@cod_tipo_asiento = ?,
											@asiento_modelo_id = ?,
											@ind_anulado = ?,
											@fechaemision = ?');

        $stmt->bindParam(1, $anio ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $empresa  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $cod_contable  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $tipo_asiento  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $asiento_modelo_id  ,PDO::PARAM_STR);
        $stmt->bindParam(6, $anulado  ,PDO::PARAM_STR);
        $stmt->bindParam(7, $fechaemision  ,PDO::PARAM_STR);
        $stmt->execute();

		$historialmigrar->IND_ASIENTO_MODELO 	=   1;
		$historialmigrar->IND_CORREO 			=   -1;
		$historialmigrar->save();


	}

}