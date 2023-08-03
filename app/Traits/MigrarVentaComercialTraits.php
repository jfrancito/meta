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

trait MigrarVentaComercialTraits
{
	
	public function mv_array_empresa_venta_comercial(){
        $array_empresas  		    = 		['IACHEM0000007086'];
        return $array_empresas;
    }
	
	private function mv_lista_ventas_migrar_agrupado_emitido_comercial()
	{
		
		$array_empresas  		    = 		$this->mv_array_empresa_venta_comercial();

		$array_periodo				=		CONPeriodo::where('COD_ANIO','>=',2023)
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
											//->where('WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE','=','ISBEFC0000028081')//quitar
											->where('WEB.viewmigrarventas.NOM_ESTADO','=','EMITIDO')
											->select(DB::raw('WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE'))
											->groupBy('WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE')
											->get();

		return $lista_migrar_ventas;

	}


	private function mv_lista_ventas_migrar_agrupado_anulado_comercial()
	{
		
		$array_empresas  		    = 		$this->mv_array_empresa_venta_comercial();

		$array_periodo				=		CONPeriodo::where('COD_ANIO','>=',2023)
											->whereIn('COD_EMPR',$array_empresas)
											->where('COD_ESTADO','=','1')
											->pluck('COD_PERIODO')
											->toArray();

		$lista_migrar_ventas		=		WEBViewMigrarVenta::leftJoin('WEB.historialmigrar', function ($join) {
									            $join->on('WEB.historialmigrar.COD_REFERENCIA', '=', 'WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE')
									                 ->where('WEB.historialmigrar.IND_ANULADO', '=', 1);
									        })
											->join("WEB.asientos","WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE", "=", "WEB.asientos.TXT_REFERENCIA")
											->where('WEB.asientos.IND_EXTORNO','<>','1')
											->whereIn('WEB.asientos.COD_EMPR',$array_empresas)

											//->whereNull('WEB.historialmigrar.COD_REFERENCIA')
											->whereIn('WEB.viewmigrarventas.COD_PERIODO',$array_periodo)
											->whereIn('WEB.viewmigrarventas.COD_EMPR',$array_empresas)
											//->where('WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE','=','ISBEBL0000036961')
											->where('WEB.viewmigrarventas.NOM_ESTADO','=','ANULADO')
											->select(DB::raw('WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE'))
											->groupBy('WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE')
											->get();

		return $lista_migrar_ventas;

	}


	private function mv_lista_ventas_migrar_agrupado_anulado_nuevo_comercial()
	{
		
		$array_empresas  		    = 		$this->mv_array_empresa_venta_comercial();

		$array_periodo				=		CONPeriodo::where('COD_ANIO','>=',2023)
											->whereIn('COD_EMPR',$array_empresas)
											->where('COD_ESTADO','=','1')
											->pluck('COD_PERIODO')
											->toArray();

		$lista_migrar_ventas		=		WEBViewMigrarVenta::leftJoin('WEB.historialmigrar', function ($join) {
									            $join->on('WEB.historialmigrar.COD_REFERENCIA', '=', 'WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE')
									                 ->where('WEB.historialmigrar.IND_ANULADO', '=', 1);
									        })
											->where('WEB.historialmigrar.IND_ERROR','=','1')
											->whereIn('WEB.viewmigrarventas.COD_PERIODO',$array_periodo)
											->whereIn('WEB.viewmigrarventas.COD_EMPR',$array_empresas)
											//->where('WEB.viewmigrarventas.FEC_EMISION','=','2022-01-07')
											->where('WEB.viewmigrarventas.NOM_ESTADO','=','ANULADO')
											->select(DB::raw('WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE'))
											->groupBy('WEB.viewmigrarventas.COD_DOCUMENTO_CTBLE')
											->get();


		return $lista_migrar_ventas;

	}


	private function mv_agregar_historial_ventas_comercial($lista_ventas_migrar_emitida,$lista_ventas_migrar_anulada,$tipo_asiento)
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




	private function mv_update_historial_ventas_comercial($documento_ctble_cod,$tipo_asiento)
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

        $stmt 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.BUSCAR_ASIENTO_MODELO_COMERCIAL 
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


	private function mv_update_historial_segundaventas_internacional($asiento,$tipo_asiento)
	{
	

		$documento_ctble 			= 		CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$asiento->TXT_REFERENCIA)->first();
		$periodo 					= 		CONPeriodo::where('COD_PERIODO','=',$asiento->COD_PERIODO)->first();
		$anio 						= 		$periodo->COD_ANIO;
		$empresa 					= 		$documento_ctble->COD_EMPR;
		$cod_contable 				= 		$documento_ctble->COD_DOCUMENTO_CTBLE;
		$tipo_igv_id_sv				=		'CTV0000000000002';
		$documento_anulado 			=   	1;
		if($documento_ctble->COD_CATEGORIA_ESTADO_DOC_CTBLE == 'EDC0000000000002'){
			$documento_anulado 		=   	0;
		}
        $stmt 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.BUSCAR_ASIENTO_MODELO_COMERCIAL 
											@anio = ?,
											@empresa = ?,
											@cod_contable = ?,
											@cod_tipo_asiento = ?,
											@ind_anulado = ?,
											@tipo_igv_id_sv = ?');

        $stmt->bindParam(1, $anio ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $empresa  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $cod_contable  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $tipo_asiento  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $documento_anulado  ,PDO::PARAM_STR);
        $stmt->bindParam(6, $tipo_igv_id_sv  ,PDO::PARAM_STR);
        $stmt->execute();

      	while ($row = $stmt->fetch()){
      		$codigo = $row['codigo'];
      		$mensaje = $row['mensaje'];
      		$asiento_modelo_id = $row['asiento_modelo_id'];

      		$historialmigrar 						=   WEBHistorialMigrar::where('COD_REFERENCIA','=',$asiento->TXT_REFERENCIA)
      													->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)->first();
			
      		if($codigo=='1'){
				$historialmigrar->COD_ASIENTO_MODELO 	=   $asiento_modelo_id;
				$historialmigrar->save();

      		}

      	}

      	return "se realizo con exito";

	}


	private function mv_update_historial_segundaventas_comercial($asiento,$tipo_asiento)
	{
	



		$documento_ctble 			= 		CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$asiento->TXT_REFERENCIA)->first();
		$periodo 					= 		CONPeriodo::where('COD_PERIODO','=',$asiento->COD_PERIODO)->first();
		$anio 						= 		$periodo->COD_ANIO;
		$empresa 					= 		$documento_ctble->COD_EMPR;
		$cod_contable 				= 		$documento_ctble->COD_DOCUMENTO_CTBLE;
		$tipo_igv_id_sv				=		'CTV0000000000002';
		$documento_anulado 			=   	1;
		if($documento_ctble->COD_CATEGORIA_ESTADO_DOC_CTBLE == 'EDC0000000000002'){
			$documento_anulado 		=   	0;
		}
        $stmt 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.BUSCAR_ASIENTO_MODELO_COMERCIAL 
											@anio = ?,
											@empresa = ?,
											@cod_contable = ?,
											@cod_tipo_asiento = ?,
											@ind_anulado = ?,
											@tipo_igv_id_sv = ?');

        $stmt->bindParam(1, $anio ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $empresa  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $cod_contable  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $tipo_asiento  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $documento_anulado  ,PDO::PARAM_STR);
        $stmt->bindParam(6, $tipo_igv_id_sv  ,PDO::PARAM_STR);
        $stmt->execute();

      	while ($row = $stmt->fetch()){
      		$codigo = $row['codigo'];
      		$mensaje = $row['mensaje'];
      		$asiento_modelo_id = $row['asiento_modelo_id'];

      		$historialmigrar 						=   WEBHistorialMigrar::where('COD_REFERENCIA','=',$asiento->TXT_REFERENCIA)
      													->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)->first();
			
      		if($codigo=='1'){
				$historialmigrar->COD_ASIENTO_MODELO 	=   $asiento_modelo_id;
				$historialmigrar->save();

      		}

      	}

      	return "se realizo con exito";

	}


	private function mv_lista_ventas_asignar_comercial($tipo_asiento)
	{
		
		$array_empresas  		    = 		$this->mv_array_empresa_venta_comercial();
		$lista_ventas				=		WEBHistorialMigrar::whereIn('COD_EMPR',$array_empresas)
											->where('IND_ASIENTO_MODELO','=',0)
											->where('IND_ERROR','<>',1)
											//->where('COD_REFERENCIA','=','ISBEFC0000028081')//quitar
											->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento)
											->get();
		return $lista_ventas;

	}




	private function mv_asignar_asiento_modelo_comercial($historialmigrar,$tipo_asiento)
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


        $stmt 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.APLICAR_ASIENTO_MODELO_COMERCIAL 
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


	private function mv_asignar_asiento_modelo_comercial_sv($asiento,$tipo_asiento,$nro_cuenta_sv)
	{
	
		$documento_ctble 			= 		CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$asiento->TXT_REFERENCIA)->first();
		$periodo 					= 		CONPeriodo::where('COD_PERIODO','=',$asiento->COD_PERIODO)->first();
		$anio 						= 		$periodo->COD_ANIO;
		$empresa 					= 		$documento_ctble->COD_EMPR;
		$cod_contable 				= 		$documento_ctble->COD_DOCUMENTO_CTBLE;

		$historialmigrar			=		WEBHistorialMigrar::where('COD_REFERENCIA','=',$asiento->TXT_REFERENCIA)
											->first();
		$asiento_modelo_id 			= 		trim($historialmigrar->COD_ASIENTO_MODELO);

		if($documento_ctble->ESTADO_ELEC == 'R'){
		    $anulado 				= 		0;
		}else{
			$anulado 				= 		$historialmigrar->IND_ANULADO;	
		}


        $stmt 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.APLICAR_ASIENTO_MODELO_COMERCIAL 
											@anio = ?,
											@empresa = ?,
											@cod_contable = ?,
											@cod_tipo_asiento = ?,
											@asiento_modelo_id = ?,
											@ind_anulado = ?,
											@nro_cuenta_sv = ?');

        $stmt->bindParam(1, $anio ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $empresa  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $cod_contable  ,PDO::PARAM_STR);
        $stmt->bindParam(4, $tipo_asiento  ,PDO::PARAM_STR);
        $stmt->bindParam(5, $asiento_modelo_id  ,PDO::PARAM_STR);
        $stmt->bindParam(6, $anulado  ,PDO::PARAM_STR);
        $stmt->bindParam(7, $nro_cuenta_sv  ,PDO::PARAM_STR);
        $stmt->execute();

	}



}