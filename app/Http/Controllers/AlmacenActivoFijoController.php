<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use View;
use Session;
use Hashids;
//use PDO;


class AlmacenActivoFijoController extends Controller
{
    //
    public function actionListarActivosFijos()
    {
        //$centro_id 	= Session::get('centros')->COD_CENTRO;
		    $empresa_id = Session::get('empresas_meta')->COD_EMPR;


        $id_almacen = DB::table('ALM.ALMACEN')->select('COD_ALMACEN')
                                              ->where('COD_EMPR','=',$empresa_id)
                                              //->      where('COD_CENTRO','=',$centro_id)
                                              ->where('COD_ESTADO','=','1')
                                              ->where('COD_ACTIVO','=','1')
                                              ->where('NOM_ALMACEN','LIKE','%FIJO%')
                                              ->first();
        $productos = DB::select('EXEC WEB.LISTAR_ACTIVOS_FIJOS " ","'.$id_almacen->COD_ALMACEN.'"');        
        return view('logistica/almacenactivosfijos')->with(['productos' => $productos]);
    }
}
