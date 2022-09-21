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
	

	public function ms_lista_migracion_navasoft($listaasiento,$anio){


		//llenado de datalle
		$array_detalle_asiento 		=	array();


	    foreach($listaasiento as $index => $item){

	    	$lista_producto         =   CMPDetalleProducto::where('COD_TABLA','=',$item->TXT_REFERENCIA)->get();
	   		$empresa 				= 	STDEmpresa::where('COD_EMPR','=',$item->COD_EMPR_CLI)->first();
	   		$categoria 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_TIPO_DOCUMENTO)->first();
	   		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$item->COD_PERIODO)->first();
	   		$moneda 				= 	CMPCategoria::where('COD_CATEGORIA','=',$item->COD_CATEGORIA_MONEDA)->first();

    		$fecha_emision  		= 	date_format(date_create($item->FEC_ASIENTO), 'd/m/Y');
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
	    	foreach($lista_producto as $indexp => $itemp){

	    		$can_valor 			=	$itemp->CAN_VALOR_VTA;


	    		if($moneda->TXT_REFERENCIA == 's'){

	    			$asientomovimientos		=	WEBAsientoMovimiento::where('COD_ASIENTO','=',$item->COD_ASIENTO)
	    										->whereRaw('(CAN_DEBE_MN + CAN_HABER_MN) = ?', [$can_valor])
	    										->where('IND_PRODUCTO','=',1)
	    										->first();
	    		}else{
	    			$asientomovimientos		=	WEBAsientoMovimiento::where('COD_ASIENTO','=',$item->COD_ASIENTO)
	    										->where('IND_PRODUCTO','=',1)
	    										->whereRaw('(CAN_DEBE_ME + CAN_HABER_ME) = ?', [$can_valor])
	    										->first();
	    		}

	    		$codi  						= 	'';	
	    		if(count($asientomovimientos)>0){
	    			$cc 					= 	WEBCuentaContable::where('id','=',$asientomovimientos->COD_CUENTA_CONTABLE)
	    										->first();
							
	    			$codi  					= 	$cc->codigo_migracion;							
	    		}


	    		// $productoempresa    = 	WEBProductoEmpresa::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
	    		// 						->where('producto_id','=',$itemp->COD_PRODUCTO)
	    		// 						->where('anio','=',$anio)
	    		// 						->first();

	    		// if(count($productoempresa)>0){
	    		// 		$codi  					= 	$productoempresa->codigo_migracion;
	    		// }else{
	    		// 		$codi  					= 	'';	    			
	    		// }


	    		$CANT 					=	$itemp->CAN_PRODUCTO;
	    		$PREU 					= 	$itemp->CAN_PRECIO_UNIT;

	    		$TOTA 					= 	$itemp->CAN_VALOR_VTA;
	      		$TOTN 					= 	$itemp->CAN_VALOR_VENTA_IGV;
	    		$TOTI 					= 	$TOTN-$TOTA;
	    		$TOTIVA 				= 	'-';

	    		if($itemp->IND_IGV == 1){
	    			$aigv 					= 	"'S";
	    		}else{
	    			$aigv 					= 	"'N";
	    		}
	      		
	    		if($itemp->IND_MATERIAL_SERVICIO == 'M'){
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
					"nombre_cliente" 			=> $nombre_cliente,
					"ruc" 						=> $ruc,

					"codi" 						=> $codi,
		            "MONE" 						=> $MONE,
		            "TCAM" 						=> $TCAM,
		            "CANT" 						=> $CANT,
		            "PREU" 						=> $PREU,

					"TOTA" 						=> $TOTA,
		            "TOTI" 						=> $TOTI,
		            "TOTIVA" 					=> $TOTIVA,
		            "TOTN" 						=> $TOTN,
		            "aigv" 						=> $aigv,

		            "codalm" 					=> $codalm,
		            "codvta" 					=> $codvta,
		            "CODSCC" 					=> $CODSCC,

				);
				array_push($array_detalle_asiento,$array_nuevo_asiento);


	    	}

	    }


	    return $array_detalle_asiento;

    }


}