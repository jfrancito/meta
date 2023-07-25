<?php

namespace App\Http\Controllers;

use App\Modelos\WEBActivoFijo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use View;
use Session;
use Hashids;

class AlmacenActivoFijoTransferidoController extends Controller
{
    //
    public function actionListarActivosFijosTransferidos()
    {
        //$centro_id 	= Session::get('centros')->COD_CENTRO;
		$empresa_id = Session::get('empresas_meta')->COD_EMPR;
        $id_almacen = DB::table('ALM.ALMACEN')->select('COD_ALMACEN')
                                              ->where('COD_EMPR','=',$empresa_id)
                                              //->where('COD_CENTRO','=',$centro_id)
                                              ->where('COD_ESTADO','=','1')
                                              ->where('COD_ACTIVO','=','1')
                                              ->where('NOM_ALMACEN','LIKE','%FIJO%')
                                              ->first();        
        //$productos = DB::select('EXEC WEB.LISTAR_ACTIVOS_FIJOS_TRANSFERIDOS " ","'.$id_almacen->COD_ALMACEN.'"');        

        $productos = WEBActivoFijo::where('cod_empresa','=',$empresa_id)
                                    //->where('cod_centro','=',$centro_id)
                                    ->where('modalidad_adquisicion','=','COMPRA')
                                    ->get();
        $obras = $this->listarObras();
        return view('logistica/almacenactivosfijostransferidos')->with(['productos' => $productos, 'obras' => $obras]);
    }

    public function listarObras()
    {
        $empresa_id = Session::get('empresas_meta')->COD_EMPR;
	    $centro_id = 'CEN0000000000001';//Session::get('centros')->COD_CENTRO;
        
        $activos_obras = WEBActivoFijo::where('modalidad_adquisicion','=','OBRA')
                                        ->where('cod_empresa','=',$empresa_id)
                                        ->where('cod_centro','=',$centro_id)
                                        ->get();
        return $activos_obras;
    }
}
