<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Modelos\WEBActivoFijo;
use Session;

class ActivoFijoController extends Controller
{
    
    public function registrarActivoFijo($idproducto, $iddocumento, Request $request)
    {
        if($_POST) {
            $this->almacenarActivoFijo($request, 'transferencia');
            return Redirect::to('/gestion-almacen-activos-transferidos/0v')->with('mensaje', 'Activo transferido con éxito.');
        }
        else
        {
            $webactivofijo = new WEBActivoFijo();
            $idalmacen = $webactivofijo->obtenerAlmacen();
            $producto = $webactivofijo->obtenerActivoFijoAlmacen($idproducto, $idalmacen, $iddocumento);
            $cantidad_productos = $producto[0]->CAN_PRODUCTO;

            $combo_categoria_activo_fijo = $this->funciones->combo_categoria_activo_fijo();
            $combo_obra = $this->funciones->combo_obra();
            $item_ple = $this->funciones->getCreateItemPle("WEB.activosfijos", $cantidad_productos);
            //dd($item_ple);
            $combo_tipo_activo_fijo = $this->funciones->combo_tipo_activo_fijo();
            $combo_estado_activo_fijo = $this->funciones->combo_estado_activo_fijo();
            $combo_estado_conservacion_activo_fijo = $this->funciones->combo_estado_conservacion_activo_fijo();
            return view('logistica/registraractivosfijos', ['producto' => $producto[0],
                                                            'combo_categoria_activo_fijo' => $combo_categoria_activo_fijo,
                                                            'combo_obra' => $combo_obra,
                                                            'combo_tipo_activo_fijo' => $combo_tipo_activo_fijo,                                                            
                                                            'combo_estado_activo_fijo' => $combo_estado_activo_fijo,
                                                            'combo_estado_conservacion_activo_fijo' => $combo_estado_conservacion_activo_fijo,
                                                            'cantidad_productos' => $cantidad_productos,
                                                            "item_ple" => $item_ple
                                                            ]);
        }
    }

    public function almacenarActivoFijo($request, $movimiento)
    {
        if($request["canproducto"] > 1 && (trim($request['tipoactivo']) != 'COMPUESTO')){
            for ($i=0; $i < $request["canproducto"]; $i++) { 
                $id_activo_fijo = $this->funciones->getCreateIdActivoFijo('WEB.activosfijos');
                $cabecera =	new WEBActivoFijo;
                $cabecera->id = $id_activo_fijo;
                $cabecera->item_ple = trim($request['itemples'][$i]);
                $cabecera->nombre = trim($request['nomproducto']);
                $cabecera->observacion = trim($request['observacion']);
                $cabecera->estado = trim($request['estado']);
                $cabecera->origen = 'MANUAL';
                $cabecera->tipo_activo = trim($request['tipoactivo']);
                $cabecera->estado_conservacion = trim($request['estadoconservacion']);
                $cabecera->base_de_calculo = trim($request['basedecalculo']);
                $cabecera->saldo_inicio_depreciacion_acumulada = $request['saldoiniciodepreciacionacumulada']; // > 0 ? trim($request['saldoiniciodepreciacionacumulada']) : trim($request['basedecalculo']);        
                $cabecera->depreciacion_acumulada = $request['saldoiniciodepreciacionacumulada'] > 0 ? trim($request['saldoiniciodepreciacionacumulada']) : 0; //trim($request['basedecalculo']);        
                $cabecera->fecha_inicio_depreciacion = trim($request['fechainiciodepreciacion']);
                $cabecera->categoria_activo_fijo_id = trim($request['categoria']);
                $cabecera->usuario_id = Session::get('usuario')->id;
                $cabecera->fecha_registro = date("d-m-Y H:i:s");
                $cabecera->fecha_modificacion = date("d-m-Y H:i:s");
                $cabecera->cod_empresa = Session::get('empresas_meta')->COD_EMPR;
                $cabecera->cod_centro = Session::get('centros')->COD_CENTRO;
                if($request['saldoiniciodepreciacionacumulada'] > 0) {
                    $cabecera->estado_depreciacion = "DEPRECIANDOSE";
                } else {
                    $cabecera->estado_depreciacion = "SIN DEPRECIAR";
                }
                if($movimiento == 'transferencia') {
                    $cabecera->marca = trim($request['marca'][$i]);
                    $cabecera->modelo = trim($request['modelo'][$i]);
                    $cabecera->numero_serie = trim($request['numero_serie'][$i]);
                    $cabecera->modalidad_adquisicion = 'COMPRA';
                    $cabecera->cod_producto = trim($request['codproducto']);
                    $cabecera->cod_documento_ctble = trim($request['coddocumentoctble']);
                    $cabecera->cod_tabla = trim($request['codtabla']);
                    $cabecera->cod_tabla_asoc = trim($request['codtablaasoc']);
                    $cabecera->cantidad = 1;
                } else {
                    $cabecera->modalidad_adquisicion = 'OBRA';
                    $cabecera->cantidad = 1;
                    $cabecera->tipo_activo = 'PRINCIPAL';
                }
                $cabecera->save();
            }
        } else {
            
            $id_activo_fijo = $this->funciones->getCreateIdActivoFijo('WEB.activosfijos');
            $cabecera =	new WEBActivoFijo;
            $cabecera->id = $id_activo_fijo;
            $cabecera->item_ple = trim($request['itemple']);
            $cabecera->nombre = trim($request['nomproducto']);
            $cabecera->observacion = trim($request['observacion']);
            $cabecera->estado = trim($request['estado']);
            $cabecera->origen = 'MANUAL';
            $cabecera->tipo_activo = trim($request['tipoactivo']);
            if(trim($request['tipoactivo']) == 'COMPUESTO') {
                $cabecera->activo_principal = trim($request['activoprincipal']);
                $cabecera->base_de_calculo = trim($request['basedecalculocompuesto']);
                $cabecera->saldo_inicio_depreciacion_acumulada = $request['saldoiniciodepreciacionacumulada']; // > 0 ? trim($request['saldoiniciodepreciacionacumulada']) : trim($request['basedecalculocompuesto']);        
            } else {
                $cabecera->base_de_calculo = trim($request['basedecalculo']);
                $cabecera->saldo_inicio_depreciacion_acumulada = $request['saldoiniciodepreciacionacumulada']; // > 0 ? trim($request['saldoiniciodepreciacionacumulada']) : trim($request['basedecalculo']);        
            }
            $cabecera->cod_empresa = Session::get('empresas_meta')->COD_EMPR;
            $cabecera->cod_centro = 'CEN0000000000001';//Session::get('centros')->COD_CENTRO;
            $cabecera->estado_conservacion = trim($request['estadoconservacion']);
            $cabecera->depreciacion_acumulada = trim($request['saldoiniciodepreciacionacumulada']);        
            $cabecera->fecha_inicio_depreciacion = trim($request['fechainiciodepreciacion']);
            $cabecera->categoria_activo_fijo_id = trim($request['categoria']);
            $cabecera->usuario_id = Session::get('usuario_meta')->id;
            $cabecera->fecha_registro = date("d-m-Y H:i:s");
            $cabecera->fecha_modificacion = date("d-m-Y H:i:s");
            if(trim($request['tipoactivo']) == 'COMPUESTO') {
                $cabecera->activo_principal = trim($request['activoprincipal']);
            }
            if($request['saldoiniciodepreciacionacumulada'] > 0) {
                $cabecera->estado_depreciacion = "DEPRECIANDOSE";
            } else {
                $cabecera->estado_depreciacion = "SIN DEPRECIAR";
            }
            if($movimiento == 'transferencia') {
                if((trim($request['tipoactivo']) != 'COMPUESTO')){
                    $cabecera->marca = trim($request['marca']);
                    $cabecera->modelo = trim($request['modelo']);
                    $cabecera->numero_serie = trim($request['numero_serie']);
                }
                $cabecera->modalidad_adquisicion = 'COMPRA';
                $cabecera->cod_producto = trim($request['codproducto']);
                $cabecera->cod_documento_ctble = trim($request['coddocumentoctble']);
                $cabecera->cod_tabla = trim($request['codtabla']);
                $cabecera->cod_tabla_asoc = trim($request['codtablaasoc']);
            } else {
                $cabecera->modalidad_adquisicion = 'OBRA';
            }
            if(trim($request['tipoactivo']) == 'COMPUESTO'){
                $cabecera->cantidad = trim($request['canproducto']);;
            } else {
                $cabecera->cantidad = 1;
            }
            $cabecera->save();        
        }
    }

    public function modificarActivoFijo($id_activofijo, Request $request)
    {
        $mensaje = '';
        $alerta = '';
        $activofijo = WEBActivoFijo::find($id_activofijo);   
        if($_POST) {
            //dd($request);
            $activofijo = WEBActivoFijo::find($id_activofijo);
            $activofijo->item_ple = trim($request['itemple']);
            $activofijo->nombre = trim($request['nomproducto']);
            $activofijo->observacion = trim($request['observacion']);
            $activofijo->estado = trim($request['estado']);   
            $activofijo->estado_conservacion = trim($request['estadoconservacion']);
            $activofijo->categoria_activo_fijo_id = trim($request['categoria']);        
            $activofijo->fecha_modificacion = date("d-m-Y H:i:s");
            if ($activofijo->estado_depreciacion == 'SIN DEPRECIAR') {           
                $activofijo->fecha_inicio_depreciacion = trim($request['fechainiciodepreciacion']);
            }
            if(trim($request['estado']) == 'BAJA') {
                $activofijo->fecha_baja = date("d-m-Y H:i:s");                   
            } else {
                $activofijo->tipo_activo = trim($request['tipoactivo']);
                if(trim($request['tipoactivo']) == 'COMPUESTO') {
                    $activofijo->activo_principal = trim($request['activoprincipal']);
                } else{
                    $activofijo->activo_principal = "";
                }   
            }                     
            if(trim($request['tipoactivo']) != 'Principal') {
                $activofijo->marca = trim($request['marca']);
                $activofijo->modelo = trim($request['modelo']);
                $activofijo->numero_serie = trim($request['numero_serie']);
            }
            $activofijo->save();            
            $mensaje = 'El activo ha sido modificado.';
            $alerta = 'alert-success';
        } 

        $combo_categoria_activo_fijo = $this->funciones->combo_categoria_activo_fijo($activofijo->categoria->id);
        $combo_obra = $this->funciones->combo_obra(trim($activofijo->activo_principal));
        $combo_tipo_activo_fijo = $this->funciones->combo_tipo_activo_fijo($activofijo->tipo_activo);
        $combo_estado_activo_fijo = $this->funciones->combo_estado_activo_fijo($activofijo->estado);
        $combo_estado_conservacion_activo_fijo = $this->funciones->combo_estado_conservacion_activo_fijo($activofijo->estado_conservacion);        
        return view('logistica/modificaractivosfijos', ['activofijo' => $activofijo,
                                                        'combo_categoria_activo_fijo' => $combo_categoria_activo_fijo,
                                                        'combo_obra' => $combo_obra,
                                                        'combo_tipo_activo_fijo' => $combo_tipo_activo_fijo,                                                            
                                                        'combo_estado_activo_fijo' => $combo_estado_activo_fijo,
                                                        'combo_estado_conservacion_activo_fijo' => $combo_estado_conservacion_activo_fijo,
                                                        'mensaje' => $mensaje,
                                                        'alerta' => $alerta
                                                        ]);        
    }

    public function modificarObra($id_activofijo, Request $request)
    {
        $mensaje = '';
        $alerta = '';
        $activofijo = WEBActivoFijo::find($id_activofijo);   
        if($_POST) {
            $activofijo = WEBActivoFijo::find($id_activofijo);
            $activofijo->item_ple = trim($request['itemple']);
            $activofijo->nombre = trim($request['nomproducto']);
            $activofijo->observacion = trim($request['observacion']);
            $activofijo->estado = trim($request['estado']);   
            $activofijo->tipo_activo = trim($request['tipoactivo']);
            $activofijo->estado_conservacion = trim($request['estadoconservacion']);
            $activofijo->categoria_activo_fijo_id = trim($request['categoria']);        
            $activofijo->fecha_modificacion = date("d-m-Y H:i:s");
            if ($activofijo->estado_depreciacion == 'SIN DEPRECIAR') {           
                $activofijo->fecha_inicio_depreciacion = trim($request['fechainiciodepreciacion']);
            }
            if(trim($request['estado']) == 'BAJA') {
                $activofijo->fecha_baja = date("d-m-Y H:i:s");                   
            }
            if(trim($request['tipoactivo']) == 'COMPUESTO') {
                $activofijo->activo_principal = trim($request['activoprincipal']);
            }         
            if(trim($request['tipoactivo']) != 'Principal') {
                $activofijo->marca = trim($request['marca']);
                $activofijo->modelo = trim($request['modelo']);
                $activofijo->numero_serie = trim($request['numero_serie']);
            }
            $activofijo->save();            
            $mensaje = 'El activo ha sido modificado.';
            $alerta = 'alert-success';
        } 

        $activos_obra = DB::table('WEB.activosfijos')->where('activo_principal', $id_activofijo)->where('estado', "OPERATIVO")->get();
        $bajas_obra = DB::table('WEB.activosfijos')->where('activo_principal', $id_activofijo)->where('estado', "BAJA")->get();
        $total_activos_obra = DB::table('WEB.activosfijos')->where('activo_principal', $id_activofijo)->where('estado', "OPERATIVO")->sum('base_de_calculo');
        $total_bajas_obra = DB::table('WEB.activosfijos')->where('activo_principal', $id_activofijo)->where('estado', "BAJA")->sum('base_de_calculo');

        $combo_categoria_activo_fijo = $this->funciones->combo_categoria_activo_fijo($activofijo->categoria->id);
        $combo_obra = $this->funciones->combo_obra(trim($activofijo->activo_principal));
        $combo_tipo_activo_fijo = $this->funciones->combo_tipo_activo_fijo($activofijo->tipo_activo);
        $combo_estado_activo_fijo = $this->funciones->combo_estado_activo_fijo($activofijo->estado);
        $combo_estado_conservacion_activo_fijo = $this->funciones->combo_estado_conservacion_activo_fijo($activofijo->estado_conservacion);        
        return view('logistica/modificarobra', ['activofijo' => $activofijo,
                                                        'combo_categoria_activo_fijo' => $combo_categoria_activo_fijo,
                                                        'combo_obra' => $combo_obra,
                                                        'combo_tipo_activo_fijo' => $combo_tipo_activo_fijo,                                                            
                                                        'combo_estado_activo_fijo' => $combo_estado_activo_fijo,
                                                        'combo_estado_conservacion_activo_fijo' => $combo_estado_conservacion_activo_fijo,
                                                        'mensaje' => $mensaje,
                                                        'alerta' => $alerta,
                                                        'activos_obra' => $activos_obra,
                                                        'bajas_obra' => $bajas_obra,
                                                        'total_activos_obra' => $total_activos_obra,
                                                        'total_bajas_obra' => $total_bajas_obra
                                                        ]);        
    }

    public function catalogoActivosFijos($idopcion, Request $request)
    {

        $anio = isset($request['anio']) ? $request['anio'] : date("Y");

        $activosfijos =  DB::table('WEB.depreciacionesactivosfijos')
                             ->join('WEB.activosfijos', function($join) use ($anio){
                                    $empresa_id = Session::get('empresas_meta')->COD_EMPR;
                                    $join->on('WEB.depreciacionesactivosfijos.activo_fijo_id', '=', 'WEB.activosfijos.id')   
                                    ->where('WEB.depreciacionesactivosfijos.anio','=', $anio)
                                    ->where('WEB.activosfijos.cod_empresa','=',$empresa_id);
                                    })
                             ->select('WEB.activosfijos.id', 'WEB.activosfijos.item_ple', 'WEB.activosfijos.nombre', 'WEB.activosfijos.fecha_inicio_depreciacion', 'WEB.depreciacionesactivosfijos.mes', 'WEB.depreciacionesactivosfijos.anio', 'WEB.depreciacionesactivosfijos.tasa_depreciacion', 'WEB.depreciacionesactivosfijos.monto')
                             ->get();
        $catalogo = array();
        foreach ($activosfijos as $item) {
            $catalogo[$item->id]["fecha_inicio_depreciacion"] = $item->fecha_inicio_depreciacion;
            $catalogo[$item->id]["item_ple"] = $item->item_ple;
            $catalogo[$item->id]["nombre"] = $item->nombre;
            $catalogo[$item->id][$item->mes] = $item->monto;
            $catalogo[$item->id]["anio"] = $item->anio;
            $catalogo[$item->id]["tasa_depreciacion"] = $item->tasa_depreciacion;
            $catalogo[$item->id]["monto"] = $item->monto;            
            $catalogo[$item->id]["monto_acumulado"] = DB::table('WEB.depreciacionesactivosfijos')
                                                          ->select(DB::raw('SUM(WEB.depreciacionesactivosfijos.monto) as monto'))
                                                          ->where('activo_fijo_id','=', $item->id)                                                          
                                                          ->where('anio','<', date("Y"))
                                                          ->first()
                                                          ->monto;
        }
        
        $combo_anios = DB::table('WEB.depreciacionesactivosfijos')->select("anio")->distinct("anio")->orderByDesc("anio")->pluck("anio","anio");
	 
        return view('logistica.catalogodepreciacionactivofijo', ['catalogo'=>$catalogo, 'combo_anios' => $combo_anios, 'idopcion' => $idopcion]);
    
    }

    public function registrarObraActivoFijo(Request $request)
    {
        if($_POST){
            $this->almacenarActivoFijo($request, 'obra');
            return Redirect::to('/gestion-almacen-activos-transferidos/0v')->with('mensaje', 'Activo registrado con éxito.');
        }
        else
        {
            $combo_categoria_activo_fijo = $this->funciones->combo_categoria_activo_fijo();
            $combo_obra = $this->funciones->combo_obra();            
            $combo_tipo_activo_fijo = $this->funciones->combo_tipo_activo_fijo();
            $combo_estado_activo_fijo = $this->funciones->combo_estado_activo_fijo();
            $combo_estado_conservacion_activo_fijo = $this->funciones->combo_estado_conservacion_activo_fijo();

            return view('logistica/registrarobra', ['combo_categoria_activo_fijo' => $combo_categoria_activo_fijo,
                                                            'combo_obra' => $combo_obra,
                                                            'combo_tipo_activo_fijo' => $combo_tipo_activo_fijo,                                                            
                                                            'combo_estado_activo_fijo' => $combo_estado_activo_fijo,
                                                            'combo_estado_conservacion_activo_fijo' => $combo_estado_conservacion_activo_fijo
                                                            ]);
        }
    }    
}
