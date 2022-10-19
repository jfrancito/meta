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


}