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
use App\Modelos\STDEmpresa;
use App\Modelos\CMPCategoria;
use App\Modelos\TESCajaBanco;
use App\Modelos\CONPeriodo;

use App\Modelos\WEBAsiento;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;


trait AsientoTraits
{


	public function llenar_array_detalle_asiento($nivel,$partida_id,$cuenta_contable_id,$nro_cuenta,$nombre,$montod,$montoh,$nombre_partida,$ultimalinea){


		return						array(
											"nivel" 					=> $nivel,
											"partida_id" 				=> $partida_id,
											"cuenta_contable_id" 		=> $cuenta_contable_id,
											"nro_cuenta" 				=> $nro_cuenta,
											"nombre" 					=> $nombre,
											"montod" 					=> $montod,
											"montoh" 					=> $montoh,
											"nombre_partida" 			=> $nombre_partida,
											"ultimalinea" 				=> $ultimalinea,
								        );



	}


	public function as_lista_asiento_pago_cobro($asiento_id){


		$lista_asiento 						=	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientos.COD_ASIENTO', '=', 'WEB.asientomovimientos.COD_ASIENTO')
												->join('WEB.cuentacontables', 'WEB.asientomovimientos.COD_CUENTA_CONTABLE', '=', 'WEB.cuentacontables.id')
												->where('WEB.asientos.COD_ASIENTO','=',$asiento_id)
												->where(function ($query){
										            $query->where('WEB.cuentacontables.nro_cuenta','Like','42%')
										            ->orWhere('WEB.cuentacontables.nro_cuenta','Like','43%');
												})
												//->where('WEB.cuentacontables.nro_cuenta','Like','42%')
	    										->get();

	    return $lista_asiento;

	}

	public function as_lista_asiento_pago_cobro_array_compra($asiento_id){


		$lista_asiento 						=	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientos.COD_ASIENTO', '=', 'WEB.asientomovimientos.COD_ASIENTO')
												->join('WEB.cuentacontables', 'WEB.asientomovimientos.COD_CUENTA_CONTABLE', '=', 'WEB.cuentacontables.id')
												->whereIn('WEB.asientos.COD_ASIENTO',$asiento_id)
												->where(function ($query){
										            $query->where('WEB.cuentacontables.nro_cuenta','Like','42%')
										            ->orWhere('WEB.cuentacontables.nro_cuenta','Like','43%');
												})
	    										->get();

	    return $lista_asiento;

	}

	public function as_lista_asiento_pago_cobro_array_venta($asiento_id){


		$lista_asiento 						=	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientos.COD_ASIENTO', '=', 'WEB.asientomovimientos.COD_ASIENTO')
												->join('WEB.cuentacontables', 'WEB.asientomovimientos.COD_CUENTA_CONTABLE', '=', 'WEB.cuentacontables.id')
												->whereIn('WEB.asientos.COD_ASIENTO',$asiento_id)
												->where(function ($query){
										            $query->where('WEB.cuentacontables.nro_cuenta','Like','1%')
										            ->orWhere('WEB.cuentacontables.nro_cuenta','Like','1%');
												})
	    										->get();

	    return $lista_asiento;

	}

	



}