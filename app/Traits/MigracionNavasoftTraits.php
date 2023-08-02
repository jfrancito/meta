<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;


use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use App\Modelos\CONPeriodo;
use App\Modelos\WEBViewMigrarVenta;
use App\Modelos\CMPDocumentoCtble;
use App\Modelos\WEBHistorialMigrar;
use App\Modelos\CMPDetalleProducto;
use App\Modelos\WEBProductoEmpresa;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\CMPReferecenciaAsoc;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\CMPCategoria;

use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\WEBAsiento;
use App\Modelos\STDEmpresa;





use ZipArchive;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait MigracionNavasoftTraits
{
	


	public function ms_lista_migracion_navasoft_comerciales($listaasiento,$anio,$migracion = '0',$excel = '0'){


		//llenado de datalle
		$array_detalle_asiento 		=	array();

	    foreach($listaasiento as $index => $item){

	    	$colormigracion 		=	'';
	    	if($item->IND_MIGRACION_NAVASOFT==1){
	    		$colormigracion 		=	'color_migracion';
	    	}
	    	if($excel==1){
	    		$colormigracion 		=	'';
	    	}
	    	if($migracion=='1'){
	    		$this->ms_marcar_migracion_navasoft($item);
	    	}

	   		$categoria 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_DOCUMENTO)->first();
	   		$empresa 				= 	STDEmpresa::where('COD_EMPR','=',$item->COD_EMPR_CLI)->first();
    		$nombre_cliente  		= 	$empresa->NOM_EMPR;
    		$fecha_emision  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');
    		$tipo_documento  		= 	$categoria->CODIGO_SUNAT;
    		$ndoc  					= 	$item->NRO_SERIE.'-'.$item->NRO_DOC;

    		$ruc  					= 	$empresa->NRO_DOCUMENTO;

    		$codi  					= 	$item->codigo_migracion;
	   		$moneda 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_MONEDA)->first();
    		$MONE                   = 	$moneda->TXT_REFERENCIA;
    		$TCAM 					=   $item->CAN_TIPO_CAMBIO;

    		$CANT 					=	'';
    		$PREU 					= 	'';

    		$TOTA 					= 	0;
    		$TOTI 					= 	0;
    		$TOTIVA 				= 	0;
      		$TOTN 					= 	0;
      		$aigv 					= 	'';

      		$codalm 				= 	'';      		
      		$codvta 				= 	'';
      		$CODSCC 				= 	'';

    		$producto 					=  	CMPDetalleProducto::where('COD_TABLA','=',$item->COD_DOCUMENTO_CTBLE)
    										->where('COD_PRODUCTO','=',$item->COD_PRODUCTO)
    										->where('NRO_LINEA','=',$item->NRO_LINEA_PRODUCTO)
    										->where('COD_LOTE','=',$item->COD_LOTE)
    										->first();

      		$can_valor 				=	0;

    		//anticipo es 2
    		if($item->IND_ANTICIPO == 2){

    			// $producto_anticipo 		=	$this->ms_producto_ind_anticipo($item,$producto);

	    		$producto_anticipo 		=  	CMPDetalleProducto::where('COD_TABLA','=',$item->COD_DOCUMENTO_CTBLE)
	    									->where('COD_PRODUCTO','=',$item->COD_PRODUCTO)
	    									->first();

	    		$PREU 					= 	$producto_anticipo->CAN_PRECIO_UNIT;
	    		if($item->tipo_ivap_id == 'CTV0000000000001'){
	    			$TOTA 					= 	($item->CAN_DEBE_MN + $item->CAN_HABER_MN)*1.04;
	    		}else{
	    			$TOTA 					= 	($item->CAN_DEBE_MN + $item->CAN_HABER_MN);
	    		}
	    		$TOTA 					=	number_format($TOTA, 2, '.', '');


	    		$CANT 					=	number_format($TOTA/$PREU, 2, '.', '');
	      		$TOTN 					= 	$TOTA;
	    		$TOTI 					= 	$TOTN-$TOTA;
	    		$TOTIVA 				= 	'-';

    		}else{

	    		$CANT 					=	$producto->CAN_PRODUCTO;
	    		$PREU 					= 	$producto->CAN_PRECIO_UNIT;
	    		$TOTA 					= 	$producto->CAN_VALOR_VTA;
	      		$TOTN 					= 	$producto->CAN_VALOR_VENTA_IGV;
	    		$TOTI 					= 	$TOTN-$TOTA;
	    		$TOTIVA 				= 	0;

    		}

    		if($producto->COD_OPERACION_AUX == 1){
	    		$TOTN 					= 	number_format($CANT*$PREU, 2, '.', '');
    		}


    		if($producto->IND_IGV == 1){
    			$aigv 					= 	"'S";
    		}else{
    			$aigv 					= 	"'N";
    		}
    		if($producto->IND_MATERIAL_SERVICIO == 'M'){
    			$codvta 				= 	'01';
    		}else{
    			$codvta 				= 	'02';
    		}     
    		
    		if($item->tipo_ivap_id == 'CTV0000000000001'){

	    		$TOTA 					= 	number_format($TOTN/1.04, 2, '.', '');
	    		$TOTIVA 				= 	number_format($TOTA*0.04, 2, '.', '');
    			$aigv 					= 	"'S";

    		}





    		if($item->IND_EXTORNO_ANULADO == 1){

	    		$CANT 					=	0;
	    		$PREU 					= 	0;
	    		$TOTA 					= 	0;
	      		$TOTN 					= 	0;
	    		$TOTI 					= 	0;
	    		$TOTIVA 				= 	0;

    		}

		



      		$codalm 				= 	'01';      		
      		$CODSCC 				= 	"'";

	    	$array_nuevo_asiento 	=	array();
			$array_nuevo_asiento    =	array(
				"fecha_emision" 			=> $fecha_emision,
				"tipo_documento" 			=> $tipo_documento,
				"ndoc" 						=> $ndoc,
				"nombre_cliente" 			=> "'".$nombre_cliente,
				"ruc" 						=> "'".$ruc,

				"codi" 						=> $codi,
	            "MONE" 						=> "'".strtoupper($MONE),

	            "TCAM" 						=> $TCAM,
	            "CANT" 						=> $CANT,
	            "PREU" 						=> $PREU,

				"TOTA" 						=> $TOTA,
	            "TOTI" 						=> $TOTI,
	            "TOTIVA" 					=> $TOTIVA,
	            "TOTN" 						=> $TOTN,
	            "aigv" 						=> $aigv,

	            "codalm" 					=> "'".$codalm,
	            "codvta" 					=> "'".$codvta,
	            "CODSCC" 					=> $CODSCC,
	           	"cm" 						=> $colormigracion,

			);
			array_push($array_detalle_asiento,$array_nuevo_asiento);

		}

	    return $array_detalle_asiento;

    }




	public function ms_marcar_migracion_navasoft($asiento){

		$asiento->IND_MIGRACION_NAVASOFT=1;
		$asiento->save();

        $referencia     =       CMPReferecenciaAsoc::where('COD_TABLA_ASOC','=',$asiento->COD_CATEGORIA_TIPO_ASIENTO)
                                ->where('COD_TABLA','=',$asiento->COD_ASIENTO)
                                ->where('TXT_DESCRIPCION','=','MIGRACION')
                                ->first();

        if(count($referencia)>0){

        	$referencia->COD_ESTADO = 1;
        	$referencia->FEC_USUARIO_MODIF_AUD  =   date('d-m-Y H:i:s');
        	$referencia->COD_USUARIO_MODIF_AUD  =   Session::get('usuario_meta')->name;
        	$referencia->save();
        	
        }else{

	        $referenciaasoc = new CMPReferecenciaAsoc;
	        $referenciaasoc->COD_TABLA        		=   $asiento->COD_ASIENTO;
	        $referenciaasoc->COD_TABLA_ASOC   		=   $asiento->COD_CATEGORIA_TIPO_ASIENTO;
	        $referenciaasoc->TXT_TABLA        		=   'WEB.asientos';
	        $referenciaasoc->TXT_TABLA_ASOC   		=   'CMP.CATEGORIA';
	        $referenciaasoc->CAN_AUX1         		=   0;
	        $referenciaasoc->COD_USUARIO_CREA_AUD  	=   Session::get('usuario_meta')->name;
	        $referenciaasoc->FEC_USUARIO_CREA_AUD  	=   date('d-m-Y H:i:s');
	        $referenciaasoc->FEC_USUARIO_MODIF_AUD  =   date('d-m-Y H:i:s');
	        $referenciaasoc->TXT_TIPO_REFERENCIA   	=   '';
	        $referenciaasoc->TXT_REFERENCIA    		=   '';
	        $referenciaasoc->TXT_DESCRIPCION    	=   'MIGRACION';
	        $referenciaasoc->COD_ESTADO        		=   1;
	        $referenciaasoc->save();

        }

	}



	public function ms_lista_migracion_navasoft_compras($listaasiento,$anio,$migracion = '0',$excel = '0'){


		//llenado de datalle
		$array_detalle_asiento 		=	array();


	    foreach($listaasiento as $index => $item){

	    	if($migracion=='1'){
	    		$this->ms_marcar_migracion_navasoft($item);
	    	}

	    	$colormigracion 		=	'';
	    	if($item->IND_MIGRACION_NAVASOFT==1){
	    		$colormigracion 		=	'color_migracion';
	    	}
	    	if($excel==1){
	    		$colormigracion 		=	'';
	    	}

	    	$lista_producto         =   CMPDetalleProducto::where('COD_TABLA','=',$item->TXT_REFERENCIA)->get();
	   		$empresa 				= 	STDEmpresa::where('COD_EMPR','=',$item->COD_EMPR_CLI)->first();
	   		$categoria 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_DOCUMENTO)->first();
	   		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$item->COD_PERIODO)->first();
	   		$moneda 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_MONEDA)->first();
	    	$documento         		=   CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$item->TXT_REFERENCIA)->first();

	    	$listaasientomoviento   =   WEBAsientoMovimiento::where('COD_ASIENTO','=',$item->COD_ASIENTO)
	    								->where('IND_PRODUCTO','=',1)
	    								->orderby('NRO_LINEA_PRODUCTO','asc')
	    								->get();

    		$fecha_emision  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');
    		$tipo_documento  		= 	"'".$categoria->CODIGO_SUNAT;

    		if($item->COD_CATEGORIA_TIPO_ASIENTO == 'TAS0000000000004'){
    			$ndoc  				= 	$item->NRO_SERIE.'-'.substr($item->NRO_DOC, -7);
    		}else{
    			$ndoc  				= 	$item->NRO_SERIE.'-'.$item->NRO_DOC;
    		}

    		$nombre_cliente  		= 	$empresa->NOM_EMPR;
    		$ruc  					= 	str_pad($empresa->NRO_DOCUMENTO ,11, "0", STR_PAD_LEFT);

    		$codi  					= 	'';
    		$MONE                   = 	$moneda->TXT_REFERENCIA;
    		$TCAM 					=   $item->CAN_TIPO_CAMBIO;
    		$CANT 					=	'';
    		$PREU 					= 	'';

    		$TOTA 					= 	0;
    		$TOTI 					= 	0;
    		$TOTIVA 				= 	0;
      		$TOTN 					= 	0;
      		$aigv 					= 	'';

      		$codalm 				= 	'';      		
      		$codvta 				= 	'';
      		$CODSCC 				= 	'';


      		$can_valor 				=	0;


	    	foreach($listaasientomoviento as $indexp => $itemp){

	    		$cuentacontable 			=	WEBCuentaContable::where('id','=',$itemp->COD_CUENTA_CONTABLE)->first();
	    		$producto 					=  	CMPDetalleProducto::where('COD_TABLA','=',$item->TXT_REFERENCIA)
	    										->where('COD_PRODUCTO','=',$itemp->COD_PRODUCTO)
	    										->where('NRO_LINEA','=',$itemp->NRO_LINEA_PRODUCTO)
	    										->where('COD_LOTE','=',$itemp->COD_LOTE)
	    										->first();

	    		$productoempresa    		= 	WEBProductoEmpresa::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
	    										->where('producto_id','=',$itemp->COD_PRODUCTO)
	    										->where('anio','=',$anio)
	    										->first();

	    		//VENTA
	    		if($item->COD_CATEGORIA_TIPO_ASIENTO =='TAS0000000000003'){
		    		if(count($productoempresa)>0){
		    				$codi  					= 	$productoempresa->codigo_migracion;
		    		}else{
		    				$codi  					= 	'';	    			
		    		}
	    		}else{
	    			//COMPRA
	    			if($item->COD_CATEGORIA_TIPO_ASIENTO =='TAS0000000000004'){
	    				$codi  						= 	$cuentacontable->codigo_migracion;	
		    		}

	    		}




	    		//anticipo es 2
	    		if($documento->IND_ANTICIPO == 2){

	    			$producto_anticipo 		=	$this->ms_producto_ind_anticipo($item,$producto);

		    		$CANT 					=	$producto_anticipo->CAN_PRODUCTO;
		    		$PREU 					= 	$producto_anticipo->CAN_PRECIO_UNIT;
		    		$TOTA 					= 	$producto_anticipo->CAN_VALOR_VTA;
		      		$TOTN 					= 	$producto_anticipo->CAN_VALOR_VENTA_IGV;
		    		$TOTI 					= 	$TOTN-$TOTA;
		    		$TOTIVA 				= 	'-';

	    		}else{

		    		$CANT 					=	$producto->CAN_PRODUCTO;
		    		$PREU 					= 	$producto->CAN_PRECIO_UNIT;
		    		$TOTA 					= 	$producto->CAN_VALOR_VTA;
		      		$TOTN 					= 	$producto->CAN_VALOR_VENTA_IGV;
		    		$TOTI 					= 	$TOTN-$TOTA;
		    		$TOTIVA 				= 	'-';

	    		}


	    		if($item->IND_EXTORNO == 1){

		    		$CANT 					=	0;
		    		$PREU 					= 	0;
		    		$TOTA 					= 	0;
		      		$TOTN 					= 	0;
		    		$TOTI 					= 	0;
		    		$TOTIVA 				= 	0;

	    		}




	    		if($producto->IND_IGV == 1){
	    			$aigv 					= 	"'S";
	    			$codisunat 				= 	"'113";
	    		}else{
	    			$aigv 					= 	"'N";
	    			$codisunat 				= 	"'120";
	    		}
	    		if($producto->IND_MATERIAL_SERVICIO == 'M'){
	    			$codvta 				= 	'01';
	    		}else{
	    			$codvta 				= 	'02';
	    		}     		





	      		$codalm 				= 	'01';      		
	      		$CODSCC 				= 	"'";

		    	$array_nuevo_asiento 	=	array();
				$array_nuevo_asiento    =	array(
					"fecha_emision" 			=> $fecha_emision,
					"tipo_documento" 			=> $tipo_documento,
					"ndoc" 						=> $ndoc,
					"nombre_cliente" 			=> "'".$nombre_cliente,
					"ruc" 						=> "'".$ruc,
					
					"codi" 						=> $codi,
		            "MONE" 						=> $MONE,
		            "TCAM" 						=> $TCAM,
		            "CANT" 						=> number_format($CANT, 2, '.', ''),
		            "PREU" 						=> number_format($PREU, 2, '.', ''),

					"TOTA" 						=> number_format($TOTA, 2, '.', ''),
		            "TOTI" 						=> number_format($TOTI, 2, '.', ''),
		            "TOTN" 						=> number_format($TOTN, 2, '.', ''),
		            "aigv" 						=> $aigv,
		            "codalm" 					=> "'".$codalm,
		            
		            "codvta" 					=> "'".$codvta,
		            "CODSUN" 					=> $codisunat,
		            "CODSCC" 					=> $CODSCC,
		            "cm" 						=> $colormigracion,
				);
				array_push($array_detalle_asiento,$array_nuevo_asiento);


	    	}


	   //  	foreach($lista_producto as $indexp => $itemp){

	   //  		$can_valor 			=	$itemp->CAN_VALOR_VTA;


	   //  		if($moneda->TXT_REFERENCIA == 's'){

	   //  			$asientomovimientos		=	WEBAsientoMovimiento::where('COD_ASIENTO','=',$item->COD_ASIENTO)
	   //  										->whereRaw('(CAN_DEBE_MN + CAN_HABER_MN) = ?', [$can_valor])
	   //  										->where('IND_PRODUCTO','=',1)
	   //  										->first();
	   //  		}else{
	   //  			$asientomovimientos		=	WEBAsientoMovimiento::where('COD_ASIENTO','=',$item->COD_ASIENTO)
	   //  										->where('IND_PRODUCTO','=',1)
	   //  										->whereRaw('(CAN_DEBE_ME + CAN_HABER_ME) = ?', [$can_valor])
	   //  										->first();
	   //  		}

	   //  		$codi  						= 	'';	
	   //  		// if(count($asientomovimientos)>0){
	   //  		// 	$cc 					= 	WEBCuentaContable::where('id','=',$asientomovimientos->COD_CUENTA_CONTABLE)
	   //  		// 								->first();
							
	   //  		// 	$codi  					= 	$cc->codigo_migracion;							
	   //  		// }


	   //  		$productoempresa    		= 	WEBProductoEmpresa::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
	   //  										->where('producto_id','=',$itemp->COD_PRODUCTO)
	   //  										->where('anio','=',$anio)
	   //  										->first();

	   //  		if(count($productoempresa)>0){
	   //  				$codi  					= 	$productoempresa->codigo_migracion;
	   //  		}else{
	   //  				$codi  					= 	'';	    			
	   //  		}


	   //  		$CANT 					=	$itemp->CAN_PRODUCTO;
	   //  		$PREU 					= 	$itemp->CAN_PRECIO_UNIT;

	   //  		$TOTA 					= 	$itemp->CAN_VALOR_VTA;
	   //    		$TOTN 					= 	$itemp->CAN_VALOR_VENTA_IGV;
	   //  		$TOTI 					= 	$TOTN-$TOTA;


	   //  		$TOTIVA 				= 	'-';

	   //  		if($itemp->IND_IGV == 1){
	   //  			$aigv 					= 	"'S";
	   //  		}else{
	   //  			$aigv 					= 	"'N";
	   //  		}
	      		
	   //  		if($itemp->IND_MATERIAL_SERVICIO == 'M'){
	   //  			$codvta 				= 	'01';
	   //  		}else{
	   //  			$codvta 				= 	'02';
	   //  		}	      		


	   //    		$codalm 				= 	'01';      		
	   //    		$CODSCC 				= 	"'";

		  //   	$array_nuevo_asiento 	=	array();
				// $array_nuevo_asiento    =	array(
				// 	"fecha_emision" 			=> $fecha_emision,
				// 	"tipo_documento" 			=> $tipo_documento,
				// 	"ndoc" 						=> $ndoc,
				// 	"nombre_cliente" 			=> $nombre_cliente,
				// 	"ruc" 						=> $ruc,

				// 	"codi" 						=> $codi,
		  //           "MONE" 						=> $MONE,
		  //           "TCAM" 						=> $TCAM,
		  //           "CANT" 						=> $CANT,
		  //           "PREU" 						=> $PREU,

				// 	"TOTA" 						=> $TOTA,
		  //           "TOTI" 						=> $TOTI,
		  //           "TOTIVA" 					=> $TOTIVA,
		  //           "TOTN" 						=> $TOTN,
		  //           "aigv" 						=> $aigv,

		  //           "codalm" 					=> $codalm,
		  //           "codvta" 					=> $codvta,
		  //           "CODSCC" 					=> $CODSCC,

				// );
				// array_push($array_detalle_asiento,$array_nuevo_asiento);
	   //  	}

	    }


	    return $array_detalle_asiento;

    }




	public function ms_producto_ind_anticipo($asiento,$producto){

		$periodo 			=	CONPeriodo::where('COD_PERIODO','=',$asiento->COD_PERIODO)->first();



		$producto_anticipo 	= 	CMPReferecenciaAsoc::join('CMP.ORDEN', 'CMP.REFERENCIA_ASOC.COD_TABLA_ASOC', '=', 'CMP.ORDEN.COD_ORDEN')
								->join('CMP.DETALLE_PRODUCTO', 'CMP.DETALLE_PRODUCTO.COD_TABLA', '=', 'CMP.ORDEN.COD_ORDEN')
								->where('CMP.REFERENCIA_ASOC.COD_TABLA','=',$asiento->TXT_REFERENCIA)
								->where('CMP.ORDEN.FEC_ORDEN','>=',$periodo->FEC_INICIO)
								->where('CMP.ORDEN.FEC_ORDEN','<=',$periodo->FEC_FIN)
								->where('CMP.DETALLE_PRODUCTO.TXT_NOMBRE_PRODUCTO','=',$producto->TXT_NOMBRE_PRODUCTO)
								->select(DB::raw('sum(CAN_PRODUCTO) CAN_PRODUCTO,
												sum(CAN_VALOR_VTA)/sum(CAN_PRODUCTO) CAN_PRECIO_UNIT,
												sum(CAN_VALOR_VTA) CAN_VALOR_VTA,
												sum(CAN_VALOR_VENTA_IGV) CAN_VALOR_VENTA_IGV'))
								->first();

		return $producto_anticipo;

	}


	public function ms_lista_migracion_navasoft($listaasiento,$anio,$migracion = '0',$excel = '0'){


		//llenado de datalle
		$array_detalle_asiento 		=	array();


	    foreach($listaasiento as $index => $item){


	    	if($migracion=='1'){
	    		$this->ms_marcar_migracion_navasoft($item);
	    	}

	    	$colormigracion 		=	'';
	    	if($item->IND_MIGRACION_NAVASOFT==1){
	    		$colormigracion 		=	'color_migracion';
	    	}
	    	if($excel==1){
	    		$colormigracion 		=	'';
	    	}

	    	$lista_producto         =   CMPDetalleProducto::where('COD_TABLA','=',$item->TXT_REFERENCIA)->get();
	   		$empresa 				= 	STDEmpresa::where('COD_EMPR','=',$item->COD_EMPR_CLI)->first();
	   		$categoria 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_DOCUMENTO)->first();
	   		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$item->COD_PERIODO)->first();
	   		$moneda 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_MONEDA)->first();
	    	$documento         		=   CMPDocumentoCtble::where('COD_DOCUMENTO_CTBLE','=',$item->TXT_REFERENCIA)->first();

	    	$listaasientomoviento   =   WEBAsientoMovimiento::where('COD_ASIENTO','=',$item->COD_ASIENTO)
	    								->where('IND_PRODUCTO','=',1)
	    								->orderby('NRO_LINEA_PRODUCTO','asc')
	    								->get();


    		$tipo_documento  		= 	$categoria->CODIGO_SUNAT;

    		if($item->COD_CATEGORIA_TIPO_ASIENTO == 'TAS0000000000004'){
    			$ndoc  				= 	$item->NRO_SERIE.'-'.substr($item->NRO_DOC, -7);
    		}else{
    			$ndoc  				= 	$item->NRO_SERIE.'-'.$item->NRO_DOC;
    		}

    		$nombre_cliente  		= 	$empresa->NOM_EMPR;
    		$ruc  					= 	$empresa->NRO_DOCUMENTO;

    		$codi  					= 	'';
    		$MONE                   = 	$moneda->TXT_REFERENCIA;
    		$TCAM 					=   $item->CAN_TIPO_CAMBIO;
    		$CANT 					=	'';
    		$PREU 					= 	'';

    		$TOTA 					= 	0;
    		$TOTI 					= 	0;
    		$TOTIVA 				= 	0;
      		$TOTN 					= 	0;
      		$aigv 					= 	'';

      		$codalm 				= 	'';      		
      		$codvta 				= 	'';
      		$CODSCC 				= 	'';


      		$can_valor 				=	0;


	    	foreach($listaasientomoviento as $indexp => $itemp){

	    		$cuentacontable 			=	WEBCuentaContable::where('id','=',$itemp->COD_CUENTA_CONTABLE)->first();
	    		$producto 					=  	CMPDetalleProducto::where('COD_TABLA','=',$item->TXT_REFERENCIA)
	    										->where('COD_PRODUCTO','=',$itemp->COD_PRODUCTO)
	    										->where('NRO_LINEA','=',$itemp->NRO_LINEA_PRODUCTO)
	    										->where('COD_LOTE','=',$itemp->COD_LOTE)
	    										->first();

	    		$productoempresa    		= 	WEBProductoEmpresa::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
	    										->where('producto_id','=',$itemp->COD_PRODUCTO)
	    										->where('anio','=',$anio)
	    										->first();

	    		//VENTA
	    		if($item->COD_CATEGORIA_TIPO_ASIENTO =='TAS0000000000003'){
		    		if(count($productoempresa)>0){
		    				$codi  					= 	$productoempresa->codigo_migracion;
		    		}else{
		    				$codi  					= 	'';	    			
		    		}
	    		}else{
	    			//COMPRA
	    			if($item->COD_CATEGORIA_TIPO_ASIENTO =='TAS0000000000004'){
	    				$codi  						= 	$cuentacontable->codigo_migracion;	
		    		}

	    		}


	    		//dd($itemp);

	    		//anticipo es 2
	    		if($documento->IND_ANTICIPO == 2){

	    			$producto_anticipo 		=	$this->ms_producto_ind_anticipo($item,$producto);

		    		$CANT 					=	$producto_anticipo->CAN_PRODUCTO;
		    		$PREU 					= 	$producto_anticipo->CAN_PRECIO_UNIT;
		    		$TOTA 					= 	$producto_anticipo->CAN_VALOR_VTA;
		      		$TOTN 					= 	$producto_anticipo->CAN_VALOR_VENTA_IGV;
		    		$TOTI 					= 	$TOTN-$TOTA;
		    		$TOTIVA 				= 	'-';

	    		}else{

		    		$CANT 					=	$producto->CAN_PRODUCTO;
		    		$PREU 					= 	$producto->CAN_PRECIO_UNIT;
		    		$TOTA 					= 	$producto->CAN_VALOR_VTA;
		      		$TOTN 					= 	$producto->CAN_VALOR_VENTA_IGV;
		    		$TOTI 					= 	$TOTN-$TOTA;
		    		$TOTIVA 				= 	'-';

	    		}


	    		if($item->IND_EXTORNO == 1){

		    		$CANT 					=	0;
		    		$PREU 					= 	0;
		    		$TOTA 					= 	0;
		      		$TOTN 					= 	0;
		    		$TOTI 					= 	0;
		    		$TOTIVA 				= 	'-';

	    		}




	    		if($producto->IND_IGV == 1){
	    			$aigv 					= 	"'S";
	    		}else{
	    			$aigv 					= 	"'N";
	    		}
	    		if($producto->IND_MATERIAL_SERVICIO == 'M'){
	    			$codvta 				= 	'01';
	    		}else{
	    			$codvta 				= 	'02';
	    		}     		



	      		$codalm 				= 	'01';      		
	      		$CODSCC 				= 	"'";

    			$fecha_emision  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');


		    	$array_nuevo_asiento 	=	array();
				$array_nuevo_asiento    =	array(
					"fecha_emision" 			=> $fecha_emision,
					"tipo_documento" 			=> $tipo_documento,
					"ndoc" 						=> $ndoc,
					"nombre_cliente" 			=> "'".$nombre_cliente,
					"ruc" 						=> "'".$ruc,

					"codi" 						=> $codi,
		            "MONE" 						=> "'".strtoupper($MONE),
		            "TCAM" 						=> $TCAM,
		            "CANT" 						=> $CANT,
		            "PREU" 						=> $PREU,

					"TOTA" 						=> $TOTA,
		            "TOTI" 						=> $TOTI,
		            "TOTIVA" 					=> $TOTIVA,
		            "TOTN" 						=> $TOTN,
		            "aigv" 						=> $aigv,

		            "codalm" 					=> "'".$codalm,
		            "codvta" 					=> "'".$codvta,
		            "CODSCC" 					=> $CODSCC,
		           	"cm" 						=> $colormigracion,

				);
				array_push($array_detalle_asiento,$array_nuevo_asiento);


	    	}


	   //  	foreach($lista_producto as $indexp => $itemp){

	   //  		$can_valor 			=	$itemp->CAN_VALOR_VTA;


	   //  		if($moneda->TXT_REFERENCIA == 's'){

	   //  			$asientomovimientos		=	WEBAsientoMovimiento::where('COD_ASIENTO','=',$item->COD_ASIENTO)
	   //  										->whereRaw('(CAN_DEBE_MN + CAN_HABER_MN) = ?', [$can_valor])
	   //  										->where('IND_PRODUCTO','=',1)
	   //  										->first();
	   //  		}else{
	   //  			$asientomovimientos		=	WEBAsientoMovimiento::where('COD_ASIENTO','=',$item->COD_ASIENTO)
	   //  										->where('IND_PRODUCTO','=',1)
	   //  										->whereRaw('(CAN_DEBE_ME + CAN_HABER_ME) = ?', [$can_valor])
	   //  										->first();
	   //  		}

	   //  		$codi  						= 	'';	
	   //  		// if(count($asientomovimientos)>0){
	   //  		// 	$cc 					= 	WEBCuentaContable::where('id','=',$asientomovimientos->COD_CUENTA_CONTABLE)
	   //  		// 								->first();
							
	   //  		// 	$codi  					= 	$cc->codigo_migracion;							
	   //  		// }


	   //  		$productoempresa    		= 	WEBProductoEmpresa::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
	   //  										->where('producto_id','=',$itemp->COD_PRODUCTO)
	   //  										->where('anio','=',$anio)
	   //  										->first();

	   //  		if(count($productoempresa)>0){
	   //  				$codi  					= 	$productoempresa->codigo_migracion;
	   //  		}else{
	   //  				$codi  					= 	'';	    			
	   //  		}


	   //  		$CANT 					=	$itemp->CAN_PRODUCTO;
	   //  		$PREU 					= 	$itemp->CAN_PRECIO_UNIT;

	   //  		$TOTA 					= 	$itemp->CAN_VALOR_VTA;
	   //    		$TOTN 					= 	$itemp->CAN_VALOR_VENTA_IGV;
	   //  		$TOTI 					= 	$TOTN-$TOTA;


	   //  		$TOTIVA 				= 	'-';

	   //  		if($itemp->IND_IGV == 1){
	   //  			$aigv 					= 	"'S";
	   //  		}else{
	   //  			$aigv 					= 	"'N";
	   //  		}
	      		
	   //  		if($itemp->IND_MATERIAL_SERVICIO == 'M'){
	   //  			$codvta 				= 	'01';
	   //  		}else{
	   //  			$codvta 				= 	'02';
	   //  		}	      		


	   //    		$codalm 				= 	'01';      		
	   //    		$CODSCC 				= 	"'";

		  //   	$array_nuevo_asiento 	=	array();
				// $array_nuevo_asiento    =	array(
				// 	"fecha_emision" 			=> $fecha_emision,
				// 	"tipo_documento" 			=> $tipo_documento,
				// 	"ndoc" 						=> $ndoc,
				// 	"nombre_cliente" 			=> $nombre_cliente,
				// 	"ruc" 						=> $ruc,

				// 	"codi" 						=> $codi,
		  //           "MONE" 						=> $MONE,
		  //           "TCAM" 						=> $TCAM,
		  //           "CANT" 						=> $CANT,
		  //           "PREU" 						=> $PREU,

				// 	"TOTA" 						=> $TOTA,
		  //           "TOTI" 						=> $TOTI,
		  //           "TOTIVA" 					=> $TOTIVA,
		  //           "TOTN" 						=> $TOTN,
		  //           "aigv" 						=> $aigv,

		  //           "codalm" 					=> $codalm,
		  //           "codvta" 					=> $codvta,
		  //           "CODSCC" 					=> $CODSCC,

				// );
				// array_push($array_detalle_asiento,$array_nuevo_asiento);
	   //  	}

	    }


	    return $array_detalle_asiento;

    }


}