<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


trait AsientoModeloTraits
{
	private function am_lista_asiento_modelo($empresa_id, $tipo_asiento_id,$anio)
	{


	    $listaasientomodelo 	= 	WEBAsientoModelo::where('empresa_id','=',$empresa_id)
	    							->where('anio','=',$anio)
	    							->tipoasiento($tipo_asiento_id)
									->orderBy('id', 'asc')
			    					->get();

		return $listaasientomodelo;

	}

	public function am_pertenece_debe_haber_rno_cuenta($asienti_modelo_detalle_id,$valor)
	{

		$nro_cuenta				= 	'';
	    $asientomodelodetalle 	= 	WEBAsientoModeloDetalle::where('id','=',$asienti_modelo_detalle_id)->first();
	    $cuentacontable 		= 	WEBCuentaContable::where('id','=',$asientomodelodetalle->cuenta_contable_id)->first();

	    //DEBE
	    if($asientomodelodetalle->partida_id == 'COP0000000000001' and $valor == 'DEBE'){
	    	$nro_cuenta			= 	$cuentacontable->nro_cuenta;
	    }
	    //HABER
	    if($asientomodelodetalle->partida_id == 'COP0000000000002' and $valor == 'HABER'){
	    	$nro_cuenta			= 	$cuentacontable->nro_cuenta;
	    }

		return $nro_cuenta;

	}

	private function am_array_asiento_modelo_referencia_xreferencia($referencia,$asiento_modelo_id) {
		
		$array 						= 	WEBAsientoModeloReferencia::where('activo','=',1)
        								->where('referencia','=',$referencia)
        								->where('asiento_modelo_id','=',$asiento_modelo_id)
		        						->pluck('documento_id')
										->toArray();
	 	return  $array;					 			
	}

	private function am_agregar_modificar_asiento_modelo_referencia($array,$referencia,$asiento_modelo_id,$fechaactual) {
		

			//limpiar todo en referencia
			WEBAsientoModeloReferencia::where('referencia','=', $referencia)
										->where('asiento_modelo_id','=', $asiento_modelo_id)
										->update([	'activo' 		=> 	0,
													'fecha_mod'		=> 	$fechaactual,
													'usuario_mod'	=>	Session::get('usuario_meta')->id
												]);

			foreach($array as $item=>$id)
			{

				$cabecera						=  	WEBAsientoModeloReferencia::where('referencia','=', 'TIPO_DOCUMENTO')
													->where('asiento_modelo_id','=', $asiento_modelo_id)
													->where('documento_id','=', $id)
													->first();
                if (count($cabecera)<=0) {

					$idasientomodeloreferencia 	=   $this->funciones->getCreateIdMaestra('web.asientomodeloreferencias');
					$cabecera            	 	=	new WEBAsientoModeloReferencia;
					$cabecera->id 	     	 	=   $idasientomodeloreferencia;
					$cabecera->asiento_modelo_id=   $asiento_modelo_id;
					$cabecera->documento_id 	=   $id;
					$cabecera->referencia 		=   'TIPO_DOCUMENTO';
					$cabecera->empresa_id 	 	=   Session::get('empresas_meta')->COD_EMPR;
					$cabecera->fecha_crea 	 	=   $fechaactual;
					$cabecera->usuario_crea 	=   Session::get('usuario_meta')->id;
					$cabecera->save();

                }else{

					$cabecera->activo 			=  1;
					$cabecera->save();	

                }
			}

			return 1;
	
	}


}