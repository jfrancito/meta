<?php

namespace App\Http\Controllers;

use App\Modelos\WEBActivoFijo;
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\WEBAsientoCompraActivoFijo;
use App\Modelos\WEBAsientoCompraActivoFijoMovimiento;
use App\Modelos\WEBCategoriaActivoFijo;
use App\Modelos\WEBDepreciacionActivoFijo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;


class DepreciacionActivoFijoController extends Controller
{
    //
    public function index(Request $request)
    {
	    $empresa_id = Session::get('empresas_meta')->COD_EMPR;
        
        if($request->isMethod('post')){
            $resultado_depreciacion = array();
            $resultado_asientos = array();

            if($request['todos'] != 'on'){
                $activofijo =  WEBActivoFijo::find($request['activofijo']);            
                $resultado_depreciacion = $this->depreciar($request, $activofijo);                
                return view('logistica/depreciacionactivosfijos', ['resultado_depreciacion'=>$resultado_depreciacion["resultado"], 'combo_mes'=>$resultado_depreciacion["combo_mes"], 'combo_activo_fijo'=>$resultado_depreciacion["combo_activo_fijo"], "mensaje"=>$resultado_depreciacion["mensaje"], "alerta"=>$resultado_depreciacion["alerta"]]);
            } else {
                $activosfijos =  WEBActivoFijo::where('estado', '<>', 'BAJA')->where('estado_depreciacion', '<>', 'DEPRECIADO')->where('tipo_activo', '<>', 'COMPUESTO')->where('WEB.activosfijos.cod_empresa','=',$empresa_id)->get();
                $j = 0;
                $depreciacion_total = 0;
                foreach ($activosfijos as $activofijo) {
                    $activofijo_res = $this->depreciar($request, $activofijo);
                    //dd($activofijo_res);
                    $depreciacion_total += $activofijo_res['resultado'];                         
                    $j++;
                }
                $resultado_depreciacion = array(
                    "nombre" => "Todos", "cuenta_activo" => "Todas", "tasa_depreciacion" => "", "fecha_inicio_depreciacion" => "",
                    "monto_depreciar" => $depreciacion_total, "depreciacion_acumulada_actualizada" => "", "mes" => $request['mes']
                );

                $resultado_asientos_af = array();
                
                if($request['asientos'] == 'on'){
                    $categoriasFijos =  WEBCategoriaActivoFijo::get()->toArray();
                    //dd($categoriasFijos);
                    foreach ($categoriasFijos as $categoriaFijo) {
                        $resultado_asientos_af[] = $this->generarAsientosAF($categoriaFijo["id"]);
                    }
                    if(!$this->existe_asientos_apertura_anio()){ // Test date
                        $this->generarAsientosCompraAF('APERTURA');
                        $this->generarAsientosCompraAF('SALDO');
                     }
                    /*if(!$this->existe_asientos_adquisicion_anio()){ // Test date
                        $this->generarAsientosCompraAF('ADQUISICION');
                    }*/
                    $resultado_asientos = $this->generarAsientosCompraAF('MOVIMIENTO');
                    //dd($resultado_asientos);
                    //dd($j);
                }
                return view('logistica/depreciacionactivosfijos', ['resultado_depreciacion'=>$resultado_depreciacion, 'combo_mes'=>$activofijo_res["combo_mes"], 'combo_activo_fijo'=>$activofijo_res["combo_activo_fijo"], "mensaje"=>$activofijo_res["mensaje"], "alerta"=>$activofijo_res["alerta"], "resultado_asientos"=>$resultado_asientos_af]);
            }
        } else {
            $combo_mes = $this->funciones->combo_mes();
            $combo_activo_fijo = $this->funciones->combo_activo_fijo();
            return view('logistica/depreciacionactivosfijos', ['combo_mes'=>$combo_mes, 'combo_activo_fijo'=>$combo_activo_fijo]);            
        }
    }

    public function depreciar($request, $activofijo)
    {
        $mes = $request['mes'];
        $calculo = $request['calculo'];
        $todos = $request['todos'];
        $asientos = $request['asientos'];
        $activo = $activofijo->id;
        $fecha = $request['fecha'];
        $condicion = $request['condicion'];
        $dias_mes = $this->funciones->dias_mes($mes);
        $anio = date("Y"); //parametro anio
        $tasa_depreciacion = $activofijo->categoria->tasa_depreciacion;
        $cuenta_activo = $activofijo->categoria->cuenta_activo;
        $base_de_calculo = $activofijo->base_de_calculo;
        $tipo_activo = $activofijo->tipo_activo;
        $fecha_inicio_depreciacion = $activofijo->fecha_inicio_depreciacion;
        $anio_inicio_depreciacion = date("Y", strtotime($fecha_inicio_depreciacion));
        $mes_inicio_depreciacion = date("m", strtotime($fecha_inicio_depreciacion));
        $depreciacion_acumulada = $activofijo->depreciacion_acumulada;
        $ultima_fecha_depreciacion = $activofijo->ultima_fecha_depreciacion;
        $estado_depreciacion = $activofijo->estado_depreciacion;
        $estado_activo = $activofijo->estado;
        $saldo_inicio_depreciacion_acumulada = $activofijo->saldo_inicio_depreciacion_acumulada;

        //dd($fecha_inicio_depreciacion, $ultima_fecha_depreciacion, $saldo_inicio_depreciacion_acumulada);

        if ($calculo == 'unico') { //periodo unico

            $fecha_inicio_periodo = date("Y-" . $mes . "-01"); //parametro anio
            $fecha_fin_periodo = date("Y-" . $mes . "-t", strtotime($anio . "-" . $mes . "-01")); //parametro anio
            
            $validacion_periodo_unico = $this->validarPeriodo($activo, $mes, $anio, $tipo_activo, $anio_inicio_depreciacion, $mes_inicio_depreciacion);

            if($validacion_periodo_unico['error'] && $todos != 'on'){
                return $validacion_periodo_unico;
            }

            if ($mes_inicio_depreciacion == $mes) {
                $diferencia_dias = $this->funciones->compararFechas($fecha_inicio_periodo, $fecha_inicio_depreciacion);
                if ($diferencia_dias > 0) {
                    $dias_mes = $this->funciones->compararFechas($fecha_inicio_depreciacion, $fecha_fin_periodo) + 1;
                    $fecha_inicio_periodo = $fecha_inicio_depreciacion;
                }
            }

            //refactorizar
            $dias_anio = $this->funciones->diasAnio($anio);
            $monto_depreciar = (($tasa_depreciacion / $dias_anio) * $base_de_calculo * $dias_mes) / 100;
            $depreciacion_acumulada_actualizada = $depreciacion_acumulada + $monto_depreciar;
            $estado_depreciacion = "DEPRECIANDOSE";

            if ($depreciacion_acumulada_actualizada >= $base_de_calculo) {
                $monto_depreciar = $base_de_calculo -  $depreciacion_acumulada;
                $depreciacion_acumulada_actualizada = $base_de_calculo;
                $estado_depreciacion = "DEPRECIADO";
            }

            if ($condicion == "procesado" && !($validacion_periodo_unico['error'])) {
                $depreciacion = new WEBDepreciacionActivoFijo();
                $depreciacion_id = $this->funciones->getCreateIdDepreciacionActivoFijo('WEB.depreciacionesactivosfijos');
                $depreciacion->id = $depreciacion_id;
                $depreciacion->activo_fijo_id = $activo;
                $depreciacion->mes = $mes;
                $depreciacion->anio = date("Y");
                $depreciacion->tasa_depreciacion = $tasa_depreciacion;
                $depreciacion->monto  = $monto_depreciar;
                $depreciacion->fecha_inicio = date("Y-m-d", strtotime($fecha_inicio_periodo));
                $depreciacion->fecha_fin = date("Y-m-d", strtotime($fecha_fin_periodo));
                $depreciacion->calculo = $calculo;
                $depreciacion->condicion = $condicion;
                $depreciacion->usuario_id = Session::get('usuario_meta')->id;
                $depreciacion->save();

                $activofijo->estado_depreciacion = $estado_depreciacion;
                $activofijo->depreciacion_acumulada = $depreciacion_acumulada_actualizada;
                $activofijo->ultima_fecha_depreciacion = date("Y-m-d", strtotime($fecha_fin_periodo));
                $activofijo->save();
            }

            if ($todos == 'on') {
                if($validacion_periodo_unico['error']){
                    $resultado = 0;
                } else {
                    $resultado = $monto_depreciar;
                    //echo $activo.':'.$resultado.'-';
                }
            } else {
                $resultado = array(
                    "nombre" => $activofijo->nombre, "cuenta_activo" => $cuenta_activo, "tasa_depreciacion" => $tasa_depreciacion, "fecha_inicio_depreciacion" => $fecha_inicio_depreciacion,
                    "monto_depreciar" => $monto_depreciar, "depreciacion_acumulada_actualizada" => $depreciacion_acumulada_actualizada, "mes" => $mes
                );
            }

            $combo_mes = $this->funciones->combo_mes();
            $combo_activo_fijo = $this->funciones->combo_activo_fijo();
            return ['resultado' => $resultado, 'combo_mes' => $combo_mes, 'combo_activo_fijo' => $combo_activo_fijo, 'mensaje' => 'El registro de depreciación ha sido ' . $condicion . '.', "alerta" => "alert-success"];
            //refactorizar

        } else {  //desde ultima fecha de depreciacion

            $fecha_fin_periodo = date("Y-" . $mes . "-t", strtotime($anio . "-" . $mes . "-01")); //parametro anio
            if (!empty($ultima_fecha_depreciacion) && !is_null($ultima_fecha_depreciacion)) {
                $fecha_inicio_depreciacion = date("Y-m-d", strtotime('+1 day', strtotime(date("Y-m-d", strtotime($ultima_fecha_depreciacion)))));
            }
            $anio_inicio_depreciacion = date("Y", strtotime($fecha_inicio_depreciacion));
            $mes_inicio_depreciacion = date("m", strtotime($fecha_inicio_depreciacion));

            $error_validacion_periodo = false;

            //dd($mes_inicio_depreciacion, $anio_inicio_depreciacion);

            /*if ($anio > $anio_inicio_depreciacion) { //contar meses entre
                $mes_inicio_depreciacion = 1;
                $fecha_inicio_depreciacion = date("Y-01-01");
            } */
            
            /* elseif ($anio < $anio_inicio_depreciacion) {
                $error_en_anio = true;
            }

            if ($todos != 'on' && $error_en_anio) {
                $combo_mes = $this->funciones->combo_mes();
                $combo_activo_fijo = $this->funciones->combo_activo_fijo();
                return ['mensaje' => 'El año de inicio que corresponde depreciar es mayor al periodo actual.', 'combo_mes' => $combo_mes, 'combo_activo_fijo' => $combo_activo_fijo, "resultado" => '', "alerta" => "alert-danger"];
            }

            if (($anio == $anio_inicio_depreciacion) && ($mes_inicio_depreciacion > $mes)) {
                $combo_mes = $this->funciones->combo_mes();
                $combo_activo_fijo = $this->funciones->combo_activo_fijo();
                return ['mensaje' => 'El mes de inicio que corresponde depreciar es mayor al mes a procesar.', 'combo_mes' => $combo_mes, 'combo_activo_fijo' => $combo_activo_fijo, "resultado" => '', "alerta" => "alert-danger"];
            } */

            $validacion_periodo = $this->validarPeriodo($activo, $mes, $anio, $tipo_activo, $anio_inicio_depreciacion, $mes_inicio_depreciacion);

            if($validacion_periodo['error'] && $todos != 'on'){
                return $validacion_periodo;
            } 

            

            $depreciacion_acumulada_actualizada = $depreciacion_acumulada; //inicializa actualizacion de depreciacion acumulada
            $monto_total_depreciar = 0;
            $meses = '';
            

            //dd($anio_inicio_depreciacion, $anio, $activo);

            for($a = $anio_inicio_depreciacion; $a <= $anio; $a++){            
                if($a < $anio){
                    $mes_calculo = 12;
                } else {
                    $mes_calculo = $mes;
                }

                if($a > $anio_inicio_depreciacion){
                    $mes_inicio_depreciacion = 1;    
                }
                
                for ($m = $mes_inicio_depreciacion; $m <= $mes_calculo; $m++) {

                    $estado_actual = WEBActivoFijo::where('id', $activo)->select('estado_depreciacion', 'depreciacion_acumulada')->first();
                    $estado_depreciacion = $estado_actual->estado_depreciacion;
                    $depreciacion_acumulada = $estado_actual->depreciacion_acumulada;
                    
                    $validacion_periodo = $this->validarPeriodo($activo, $m, $anio, $tipo_activo, $a, $mes_inicio_depreciacion);

                    if ($estado_depreciacion != "DEPRECIADO" && !($validacion_periodo['error'])) {
                        //dd($a);
                        $fecha_inicio_periodo = date($a."-" . $m . "-01");
                        $fecha_fin_periodo = date($a."-" . $m . "-t", strtotime($a . "-" . $m . "-01"));
                        //dd($fecha_inicio_periodo,$fecha_fin_periodo, $mes_inicio_depreciacion);
                        if ($m == $mes_inicio_depreciacion) {
                            $diferencia_dias = $this->funciones->compararFechas($fecha_inicio_periodo, $fecha_inicio_depreciacion);
                            if ($diferencia_dias > 0) {
                                $dias_mes = $this->funciones->compararFechas($fecha_inicio_depreciacion, $fecha_fin_periodo) + 1;
                                $fecha_inicio_periodo = $fecha_inicio_depreciacion;
                            } else {
                                $dias_mes = $this->funciones->dias_mes($m);
                            }
                            $meses = $meses . $m;
                        } else {
                            $dias_mes = $this->funciones->dias_mes($m);
                            $meses = $meses . ' - ' . $m;
                        }

                        //dd($dias_mes, $meses);

                        $dias_anio = $this->funciones->diasAnio($a);
                        $monto_depreciar = (($tasa_depreciacion / $dias_anio) * $base_de_calculo * $dias_mes) / 100;
                        $depreciacion_acumulada_actualizada += $monto_depreciar;
                        $monto_total_depreciar += $monto_depreciar;
                        $estado_depreciacion = "DEPRECIANDOSE";

                        //dd($dias_anio, $monto_depreciar, $depreciacion_acumulada, $depreciacion_acumulada_actualizada, $monto_total_depreciar);

                        if ($depreciacion_acumulada_actualizada >= $base_de_calculo) {
                            $monto_depreciar = $base_de_calculo -  $depreciacion_acumulada;
                            $depreciacion_acumulada_actualizada = $base_de_calculo;
                            $estado_depreciacion = "DEPRECIADO";
                        }

                        if ($condicion == "procesado") {
                            $depreciacion = new WEBDepreciacionActivoFijo();
                            $depreciacion_id = $this->funciones->getCreateIdDepreciacionActivoFijo('WEB.depreciacionesactivosfijos');
                            $depreciacion->id = $depreciacion_id;
                            $depreciacion->activo_fijo_id = $activo;
                            $depreciacion->mes = $m;
                            $depreciacion->anio = $a; // date("Y");
                            $depreciacion->tasa_depreciacion = $tasa_depreciacion;
                            $depreciacion->monto  = $monto_depreciar;
                            $depreciacion->fecha_inicio = date("Y-m-d", strtotime($fecha_inicio_periodo));
                            $depreciacion->fecha_fin = date("Y-m-d", strtotime($fecha_fin_periodo));
                            $depreciacion->calculo = $calculo;
                            $depreciacion->condicion = $condicion;
                            $depreciacion->usuario_id = Session::get('usuario_meta')->id;
                            $depreciacion->save();

                            $activofijo->estado_depreciacion = $estado_depreciacion;
                            $activofijo->depreciacion_acumulada = $depreciacion_acumulada_actualizada;
                            $activofijo->ultima_fecha_depreciacion = date("Y-m-d", strtotime($fecha_fin_periodo));
                            $activofijo->save();

                            //dd($depreciacion, $activofijo);
                        }
                    }
                }
            }

            if ($todos == 'on') {
                if($validacion_periodo['error']){
                    $resultado = 0;
                } else {
                    $resultado = $monto_depreciar;
                    //echo $activo.':'.$resultado.'-';
                }
                /* $resultado = array(
                    "nombre" => "Todos", "cuenta_activo" => "Todas", "tasa_depreciacion" => "Todas ", "fecha_inicio_depreciacion" => "",
                    "monto_depreciar" => $monto_total_depreciar, "depreciacion_acumulada_actualizada" => $depreciacion_acumulada_actualizada, "mes" => $meses
                ); */
            } else {
                $resultado = array(
                    "nombre" => $activofijo->nombre, "cuenta_activo" => $cuenta_activo, "tasa_depreciacion" => $tasa_depreciacion, "fecha_inicio_depreciacion" => $activofijo->fecha_inicio_depreciacion,
                    "monto_depreciar" => $monto_total_depreciar, "depreciacion_acumulada_actualizada" => $depreciacion_acumulada_actualizada, "mes" => $meses
                );
            }

            $combo_mes = $this->funciones->combo_mes();
            $combo_activo_fijo = $this->funciones->combo_activo_fijo();

            return ['resultado' => $resultado, 'combo_mes' => $combo_mes, 'combo_activo_fijo' => $combo_activo_fijo, 'mensaje' => 'El registro de depreciación ha sido ' . $condicion . '.', "alerta" => "alert-success"];

        }

    }

    public function generarAsientosAF($idcategoria)
    {
        $resultado = array();
        //dd($activosFijos);
        $empresa_id = Session::get('empresas_meta')->COD_EMPR;
        //$empresa_id = 'EMP0000000000007';
        $centro_id = 'CEN0000000000001';
        //$centro_id = 'CEN0000000000001';
        $usuario_id = $this->obtenerCuentaUsuario(Session::get('usuario_meta')->id);        
        $cod_periodo = $this->obtenerPeriodoActual($empresa_id, $centro_id);
        $tipo_documento = '';
        $txt_tipo_documento = '';
        $tipo_asiento = 'TAS0000000000007';
        $txt_tipo_asiento = 'DIARIO';
        $nro_asiento = '';
        $fec_asiento = date("Ymd");
        $fec_creacion = date("Ymd H:i:s");
        $cod_categoria_estado_asiento = 'IACHTE0000000025'; //'IACHTE0000000010';
        $txt_categoria_estado_asiento = 'CONFIRMADO'; //'CUADRADO';
        $cod_moneda = 'MON0000000000001';
        $txt_categoria_moneda = 'SOLES';
        $tipo_cambio = $this->obtenerTipoCambio();
        $total_debe = '';
        $total_haber = '';
        $ind_extorno = 0;
        $ind_anulado = 0;
        $txt_tipo_referencia = 'DEPRECIACION';
        $cod_estado = 1;
        $cod_categoria_tipo_documento_ref = '';
        $nro_serie_Ref = '';
        $nro_doc_ref = '';
        $fec_vencimiento = '';
        $ind_afecto = 0;            

             $activosFijos =  DB::table('WEB.activosfijos')
                                ->join('WEB.depreciacionesactivosfijos','WEB.activosfijos.id','=','WEB.depreciacionesactivosfijos.activo_fijo_id')
                                ->join('WEB.categoriasactivosfijos','WEB.activosfijos.categoria_activo_fijo_id','=','WEB.categoriasactivosfijos.id')
                                //->select(DB::raw('SUM(WEB.depreciacionesactivosfijos.monto) as monto'), 'WEB.activosfijos.id', 'WEB.activosfijos.nombre','WEB.categoriasactivosfijos.id as catid', 'WEB.categoriasactivosfijos.nombre as catnombre', 'WEB.categoriasactivosfijos.cuenta_debe', 'WEB.categoriasactivosfijos.cuenta_haber')
                                ->select(DB::raw('SUM(WEB.depreciacionesactivosfijos.monto) as monto'),'WEB.categoriasactivosfijos.id as catid', 'WEB.categoriasactivosfijos.nombre as catnombre', 'WEB.categoriasactivosfijos.cuenta_debe', 'WEB.categoriasactivosfijos.cuenta_haber')
                                ->where('WEB.depreciacionesactivosfijos.mes','=',date("m"))
                                ->where('WEB.depreciacionesactivosfijos.anio','=',date("Y"))                        
                                ->where('WEB.categoriasactivosfijos.id','=',$idcategoria)                        
                                ->groupBy( 'WEB.categoriasactivosfijos.id', 'WEB.categoriasactivosfijos.nombre',    'WEB.categoriasactivosfijos.cuenta_debe', 'WEB.categoriasactivosfijos.cuenta_haber')
                                ->get();

        //dd($activosFijos);

        $i = 0;
//        dd($categoriasFijos);


        $asientos_desactivacion = WEBAsiento::where('COD_ESTADO','=','1')->where('COD_PERIODO','=',$cod_periodo)->where('TXT_TIPO_REFERENCIA','=','DEPRECIACION')->update(['COD_ESTADO' => '0']);
        //$asientos_movimientos_desactivacion = WEBAsientoMovimiento::where('COD_ESTADO','=','1')->where('COD_PERIODO','=',$cod_periodo)->where('TXT_TIPO_REFERENCIA','=','DEPRECIACION')->update(['COD_ESTADO' => '0']);
        //dd($asientos_desactivacion);

        foreach ($activosFijos as $activoFijo) {            
            // CHANGE Agregar anio en busqueda de cuenta
            $datos_cuenta_debe = DB::table('WEB.cuentacontables')->select('id', 'nro_cuenta', 'cuenta_contable_transferencia_debe', 'cuenta_contable_transferencia_haber')->where('nro_cuenta','=',$activoFijo->cuenta_debe)->where('empresa_id','=',$empresa_id)->first();
            $datos_cuenta_haber = DB::table('WEB.cuentacontables')->select('id', 'nro_cuenta')->where('nro_cuenta','=',$activoFijo->cuenta_haber)->where('empresa_id','=',$empresa_id)->first();
            $datos_cuenta_transferencia_debe = DB::table('WEB.cuentacontables')->select('id', 'nro_cuenta')->where('nro_cuenta','=',$datos_cuenta_debe->cuenta_contable_transferencia_debe)->where('empresa_id','=',$empresa_id)->first();
            $datos_cuenta_transferencia_haber = DB::table('WEB.cuentacontables')->select('id', 'nro_cuenta')->where('nro_cuenta','=',$datos_cuenta_debe->cuenta_contable_transferencia_haber)->where('empresa_id','=',$empresa_id)->first();
            
            $txt_glosa = 'ASIENTO DEPRECIACIÓN '.$activoFijo->catnombre;
            $nro_asiento = $this->funciones->getCreateINumeroAsiento('WEB.asientos');
            //$nro_asiento = $this->funciones->getCreateINumeroAsiento('WEB.asientoscompraactivosfijos');
            
            $asiento_id = $this->funciones->getCreateIdAsientoContable('WEB.asientos');
            //$asiento_id = $this->funciones->getCreateIdAsientoContable('WEB.asientoscompraactivosfijos');
            $asiento = new WEBAsiento(); //WEBAsientoCompraActivoFijo();
            $asiento->COD_ASIENTO = $asiento_id;
            $asiento->COD_EMPR = $empresa_id;
            $asiento->COD_CENTRO = $centro_id;
            $asiento->COD_PERIODO = $cod_periodo;
            $asiento->COD_CATEGORIA_TIPO_ASIENTO = $tipo_asiento;
            $asiento->TXT_CATEGORIA_TIPO_ASIENTO = $txt_tipo_asiento;
            $asiento->NRO_ASIENTO = $nro_asiento;
            $asiento->FEC_ASIENTO = $fec_asiento;        
            $asiento->TXT_GLOSA = $txt_glosa;
            $asiento->COD_CATEGORIA_ESTADO_ASIENTO = $cod_categoria_estado_asiento;
            $asiento->TXT_CATEGORIA_ESTADO_ASIENTO = $txt_categoria_estado_asiento;
            $asiento->COD_CATEGORIA_MONEDA = $cod_moneda;
            $asiento->TXT_CATEGORIA_MONEDA = $txt_categoria_moneda;
            $asiento->CAN_TIPO_CAMBIO = $tipo_cambio;
            $asiento->CAN_TOTAL_DEBE = $activoFijo->monto;
            $asiento->CAN_TOTAL_HABER = $activoFijo->monto;
            $asiento->IND_EXTORNO = $ind_extorno;
            $asiento->IND_ANULADO = $ind_anulado;
            $asiento->TXT_TIPO_REFERENCIA = $txt_tipo_referencia;
            $asiento->COD_USUARIO_CREA_AUD = $usuario_id;
            $asiento->FEC_USUARIO_CREA_AUD = $fec_creacion;
            $asiento->COD_USUARIO_MODIF_AUD = $usuario_id;
            $asiento->FEC_USUARIO_MODIF_AUD = $fec_creacion;
            $asiento->COD_ESTADO = $cod_estado;
            $asiento->IND_AFECTO = $ind_afecto;
            $asiento->COND_ASIENTO = "DEPRECIACIÓN";
            //$asiento->COD_ACTIVO_FIJO = $activoFijo->id;
            $asiento->save();

            $resultado[$i]['fecha'] = date("d-m-Y"); //$fec_asiento;
            $resultado[$i]['glosa'] = $txt_glosa;
            $resultado[$i]['debe'] =  $activoFijo->monto;
            $resultado[$i]['haber'] =  $activoFijo->monto;

            $asientoMovimiento = new WEBAsientoMovimiento(); //WEBAsientoCompraActivoFijoMovimiento();
            $asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientomovimientos');
            //$asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientoscompraactivosfijosmovimientos');            
            $asientoMovimiento->COD_EMPR = $empresa_id;
            $asientoMovimiento->COD_CENTRO = $centro_id;                
            $asientoMovimiento->COD_ASIENTO = $asiento_id;
            $asientoMovimiento->COD_CUENTA_CONTABLE = $datos_cuenta_debe->id;
            $asientoMovimiento->TXT_CUENTA_CONTABLE = $datos_cuenta_debe->nro_cuenta;            
            $asientoMovimiento->TXT_GLOSA = $txt_glosa;   
            $asientoMovimiento->CAN_DEBE_MN = $activoFijo->monto;            
            $asientoMovimiento->CAN_HABER_MN = 0; 
            $asientoMovimiento->CAN_DEBE_ME = $activoFijo->monto/$tipo_cambio; 
            $asientoMovimiento->CAN_HABER_ME = 0; 
            $asientoMovimiento->NRO_LINEA = 1; 
            $asientoMovimiento->IND_EXTORNO = 0; 
            $asientoMovimiento->COD_USUARIO_CREA_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_CREA_AUD = $fec_asiento;
            $asientoMovimiento->COD_USUARIO_MODIF_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_MODIF_AUD = $fec_asiento;
            $asientoMovimiento->COD_ESTADO = $cod_estado;
            $asientoMovimiento->TXT_TIPO_REFERENCIA = $txt_tipo_referencia;
            $asientoMovimiento->save();

            $resultado[$i]['detalle'][0] = array('cuenta' => $datos_cuenta_debe->nro_cuenta, 'glosa' => $txt_glosa, 'debe' => $activoFijo->monto, 'haber' => 0);

            $asientoMovimiento = new WEBAsientoMovimiento(); //WEBAsientoCompraActivoFijoMovimiento();
            $asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientomovimientos');
            //$asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientoscompraactivosfijosmovimientos');            
            $asientoMovimiento->COD_EMPR = $empresa_id;
            $asientoMovimiento->COD_CENTRO = $centro_id;                
            $asientoMovimiento->COD_ASIENTO = $asiento_id;
            $asientoMovimiento->COD_CUENTA_CONTABLE = $datos_cuenta_haber->id;
            $asientoMovimiento->TXT_CUENTA_CONTABLE = $datos_cuenta_haber->nro_cuenta;            
            $asientoMovimiento->TXT_GLOSA = $txt_glosa;   
            $asientoMovimiento->CAN_DEBE_MN = 0;            
            $asientoMovimiento->CAN_HABER_MN = $activoFijo->monto; 
            $asientoMovimiento->CAN_DEBE_ME = 0; 
            $asientoMovimiento->CAN_HABER_ME = $activoFijo->monto/$tipo_cambio; 
            $asientoMovimiento->NRO_LINEA = 2; 
            $asientoMovimiento->IND_EXTORNO = 0; 
            $asientoMovimiento->COD_USUARIO_CREA_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_CREA_AUD = $fec_asiento;
            $asientoMovimiento->COD_USUARIO_MODIF_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_MODIF_AUD = $fec_asiento;
            $asientoMovimiento->COD_ESTADO = $cod_estado;
            $asientoMovimiento->TXT_TIPO_REFERENCIA = $txt_tipo_referencia;
            $asientoMovimiento->save();

            $resultado[$i]['detalle'][1] = array('cuenta' => $datos_cuenta_haber->nro_cuenta, 'glosa' => $txt_glosa, 'debe' => 0, 'haber' =>  $activoFijo->monto);

            $asientoMovimiento = new WEBAsientoMovimiento(); //WEBAsientoCompraActivoFijoMovimiento();
            $asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientomovimientos');
            //$asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientoscompraactivosfijosmovimientos');            
            $asientoMovimiento->COD_EMPR = $empresa_id;
            $asientoMovimiento->COD_CENTRO = $centro_id;                
            $asientoMovimiento->COD_ASIENTO = $asiento_id;
            $asientoMovimiento->COD_CUENTA_CONTABLE = $datos_cuenta_transferencia_debe->id;
            $asientoMovimiento->TXT_CUENTA_CONTABLE = $datos_cuenta_transferencia_debe->nro_cuenta;            
            $asientoMovimiento->TXT_GLOSA = $txt_glosa;   
            $asientoMovimiento->CAN_DEBE_MN = $activoFijo->monto;            
            $asientoMovimiento->CAN_HABER_MN = 0; 
            $asientoMovimiento->CAN_DEBE_ME = $activoFijo->monto/$tipo_cambio; 
            $asientoMovimiento->CAN_HABER_ME = 0; 
            $asientoMovimiento->NRO_LINEA = 3; 
            $asientoMovimiento->IND_EXTORNO = 0; 
            $asientoMovimiento->COD_USUARIO_CREA_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_CREA_AUD = $fec_asiento;
            $asientoMovimiento->COD_USUARIO_MODIF_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_MODIF_AUD = $fec_asiento;
            $asientoMovimiento->COD_ESTADO = $cod_estado;
            $asientoMovimiento->TXT_TIPO_REFERENCIA = $txt_tipo_referencia;
            $asientoMovimiento->save();

            $resultado[$i]['detalle'][2] = array('cuenta' => $datos_cuenta_transferencia_debe->nro_cuenta, 'glosa' => $txt_glosa, 'debe' => $activoFijo->monto, 'haber' => 0);

            $asientoMovimiento = new WEBAsientoMovimiento(); //WEBAsientoCompraActivoFijoMovimiento();
            $asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientomovimientos');
            //$asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientoscompraactivosfijosmovimientos');            
            $asientoMovimiento->COD_EMPR = $empresa_id;
            $asientoMovimiento->COD_CENTRO = $centro_id;                
            $asientoMovimiento->COD_ASIENTO = $asiento_id;
            $asientoMovimiento->COD_CUENTA_CONTABLE = $datos_cuenta_transferencia_haber->id;
            $asientoMovimiento->TXT_CUENTA_CONTABLE = $datos_cuenta_transferencia_haber->nro_cuenta;            
            $asientoMovimiento->TXT_GLOSA = $txt_glosa;   
            $asientoMovimiento->CAN_DEBE_MN = 0;            
            $asientoMovimiento->CAN_HABER_MN = $activoFijo->monto; 
            $asientoMovimiento->CAN_DEBE_ME = 0; 
            $asientoMovimiento->CAN_HABER_ME = $activoFijo->monto/$tipo_cambio; 
            $asientoMovimiento->NRO_LINEA = 4; 
            $asientoMovimiento->IND_EXTORNO = 0; 
            $asientoMovimiento->COD_USUARIO_CREA_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_CREA_AUD = $fec_asiento;
            $asientoMovimiento->COD_USUARIO_MODIF_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_MODIF_AUD = $fec_asiento;
            $asientoMovimiento->COD_ESTADO = $cod_estado;
            $asientoMovimiento->TXT_TIPO_REFERENCIA = $txt_tipo_referencia;
            $asientoMovimiento->save();

            $resultado[$i]['detalle'][3] = array('cuenta' => $datos_cuenta_transferencia_haber->nro_cuenta, 'glosa' => $txt_glosa, 'debe' => 0, 'haber' => $activoFijo->monto);

            $i++;
            //dd($resultado);            
        }
        return $resultado;
    }
    
    public function generarAsientosCompraAF($condicion_asiento)
    {
        $resultado = array();
        //dd($activosFijos);
        $empresa_id = Session::get('empresas_meta')->COD_EMPR;
        //$empresa_id = 'EMP0000000000007';
        $centro_id = 'CEN0000000000001';
        //$centro_id = 'CEN0000000000001';
        $usuario_id = $this->obtenerCuentaUsuario(Session::get('usuario_meta')->id);        
        $cod_periodo = $this->obtenerPeriodoActual($empresa_id, $centro_id);
        $tipo_documento = '';
        $txt_tipo_documento = '';
        $tipo_asiento = 'TAS0000000000007';
        $txt_tipo_asiento = 'DIARIO';
        $nro_asiento = '';
        $fec_asiento = date("Ymd");
        $fec_creacion = date("Ymd H:i:s");
        $cod_categoria_estado_asiento = 'IACHTE0000000025'; //'IACHTE0000000010';
        $txt_categoria_estado_asiento = 'CONFIRMADO'; //'CUADRADO';
        $cod_moneda = 'MON0000000000001';
        $txt_categoria_moneda = 'SOLES';
        $tipo_cambio = $this->obtenerTipoCambio();
        $total_debe = '';
        $total_haber = '';
        $ind_extorno = 0;
        $ind_anulado = 0;
        $txt_tipo_referencia = 'DEPRECIACION';
        $cod_estado = 1;
        $cod_categoria_tipo_documento_ref = '';
        $nro_serie_Ref = '';
        $nro_doc_ref = '';
        $fec_vencimiento = '';
        $ind_afecto = 0;            

        if(1 == 1){
            if($condicion_asiento == 'MOVIMIENTO'){
                
                $activosFijos =  DB::table('WEB.activosfijos')
                                    ->join('WEB.depreciacionesactivosfijos','WEB.activosfijos.id','=','WEB.depreciacionesactivosfijos.activo_fijo_id')
                                    ->join('WEB.categoriasactivosfijos','WEB.activosfijos.categoria_activo_fijo_id','=','WEB.categoriasactivosfijos.id')
                                    ->select('WEB.activosfijos.id', 'WEB.activosfijos.nombre', 'WEB.categoriasactivosfijos.cuenta_debe', 'WEB.categoriasactivosfijos.cuenta_haber', DB::raw('SUM(WEB.depreciacionesactivosfijos.monto) as monto'))
                                    ->where('WEB.depreciacionesactivosfijos.mes','=',date("m"))
                                    ->where('WEB.depreciacionesactivosfijos.anio','=',date("Y"))                        
                                    ->groupBy('WEB.activosfijos.id', 'WEB.activosfijos.nombre', 'WEB.categoriasactivosfijos.cuenta_debe', 'WEB.categoriasactivosfijos.cuenta_haber')
                                    ->get();

            }
            if($condicion_asiento == 'APERTURA'){
                
                $activosFijos =  DB::table('WEB.activosfijos')
                                    ->join('WEB.depreciacionesactivosfijos','WEB.activosfijos.id','=','WEB.depreciacionesactivosfijos.activo_fijo_id')
                                    ->join('WEB.categoriasactivosfijos','WEB.activosfijos.categoria_activo_fijo_id','=','WEB.categoriasactivosfijos.id')
                                    ->join('CMP.DOCUMENTO_CTBLE','WEB.activosfijos.cod_documento_ctble','=','CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE')
                                    ->select('WEB.activosfijos.id', 'WEB.activosfijos.nombre', 'WEB.activosfijos.base_de_calculo as monto', 'WEB.categoriasactivosfijos.cuenta_debe', 'WEB.categoriasactivosfijos.cuenta_haber')                                 
                                    ->where('CMP.DOCUMENTO_CTBLE.FEC_EMISION','<',date("Y").'-01-01')                        
                                    ->groupBy('WEB.activosfijos.id', 'WEB.activosfijos.nombre', 'WEB.activosfijos.base_de_calculo', 'WEB.categoriasactivosfijos.cuenta_debe', 'WEB.categoriasactivosfijos.cuenta_haber')
                                    ->get();
                //dd($activosFijos);
            }
            if($condicion_asiento == 'SALDO'){
                
                $activosFijos =  DB::table('WEB.activosfijos')
                                    ->join('WEB.depreciacionesactivosfijos','WEB.activosfijos.id','=','WEB.depreciacionesactivosfijos.activo_fijo_id')
                                    ->join('WEB.categoriasactivosfijos','WEB.activosfijos.categoria_activo_fijo_id','=','WEB.categoriasactivosfijos.id')
                                    ->join('CMP.DOCUMENTO_CTBLE','WEB.activosfijos.cod_documento_ctble','=','CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE')
                                    ->select('WEB.activosfijos.id', 'WEB.activosfijos.nombre', DB::raw('SUM(WEB.depreciacionesactivosfijos.monto) as monto'), 'WEB.categoriasactivosfijos.cuenta_debe', 'WEB.categoriasactivosfijos.cuenta_haber')                                 
                                    ->where('CMP.DOCUMENTO_CTBLE.FEC_EMISION','<',date("Y").'-01-01')  
                                    ->where('WEB.depreciacionesactivosfijos.anio','=',date("Y")-1)                                                                               
                                    ->groupBy('WEB.activosfijos.id', 'WEB.activosfijos.nombre', 'WEB.categoriasactivosfijos.cuenta_debe', 'WEB.categoriasactivosfijos.cuenta_haber')
                                    ->get();
                //dd($activosFijos);
            }

        } else {
            $activosFijos =  DB::table('WEB.activosfijos')
                                ->join('WEB.depreciacionesactivosfijos','WEB.activosfijos.id','=','WEB.depreciacionesactivosfijos.activo_fijo_id')
                                ->join('WEB.categoriasactivosfijos','WEB.activosfijos.categoria_activo_fijo_id','=','WEB.categoriasactivosfijos.id')
                                ->select('WEB.categoriasactivosfijos.id', 'WEB.categoriasactivosfijos.nombre', 'WEB.categoriasactivosfijos.cuenta_debe', 'WEB.categoriasactivosfijos.cuenta_haber', DB::raw('SUM(WEB.depreciacionesactivosfijos.monto) as monto'))
                                ->where('WEB.depreciacionesactivosfijos.mes','=',date("m"))
                                ->where('WEB.depreciacionesactivosfijos.anio','=',date("Y"))                        
                                ->groupBy('WEB.categoriasactivosfijos.id', 'WEB.categoriasactivosfijos.nombre', 'WEB.categoriasactivosfijos.cuenta_debe', 'WEB.categoriasactivosfijos.cuenta_haber')
                                ->get();
        }
    
        $i = 0;
        //dd($activosFijos);

        foreach ($activosFijos as $activoFijo) {            
            // CHANGE Agregar anio en busqueda de cuenta
            $datos_cuenta_debe = DB::table('WEB.cuentacontables')->select('id', 'nro_cuenta', 'cuenta_contable_transferencia_debe', 'cuenta_contable_transferencia_haber')->where('nro_cuenta','=',$activoFijo->cuenta_debe)->where('empresa_id','=',$empresa_id)->first();
            $datos_cuenta_haber = DB::table('WEB.cuentacontables')->select('id', 'nro_cuenta')->where('nro_cuenta','=',$activoFijo->cuenta_haber)->where('empresa_id','=',$empresa_id)->first();
            $datos_cuenta_transferencia_debe = DB::table('WEB.cuentacontables')->select('id', 'nro_cuenta')->where('nro_cuenta','=',$datos_cuenta_debe->cuenta_contable_transferencia_debe)->where('empresa_id','=',$empresa_id)->first();
            $datos_cuenta_transferencia_haber = DB::table('WEB.cuentacontables')->select('id', 'nro_cuenta')->where('nro_cuenta','=',$datos_cuenta_debe->cuenta_contable_transferencia_haber)->where('empresa_id','=',$empresa_id)->first();
            
            $txt_glosa = 'ASIENTO DEPRECIACIÓN '.$condicion_asiento.' ';
            $txt_glosa .= $activoFijo->nombre; 
            //$nro_asiento = $this->funciones->getCreateINumeroAsiento('WEB.asientos');
            $nro_asiento = $this->funciones->getCreateINumeroAsiento('WEB.asientoscompraactivosfijos');
            
            //$asiento_id = $this->funciones->getCreateIdAsientoContable('WEB.asientos');
            $asiento_id = $this->funciones->getCreateIdAsientoContable('WEB.asientoscompraactivosfijos');
            $asiento = new WEBAsientoCompraActivoFijo(); //WEBAsiento();
            $asiento->COD_ASIENTO = $asiento_id;
            $asiento->COD_EMPR = $empresa_id;
            $asiento->COD_CENTRO = $centro_id;
            $asiento->COD_PERIODO = $cod_periodo;
            $asiento->COD_CATEGORIA_TIPO_ASIENTO = $tipo_asiento;
            $asiento->TXT_CATEGORIA_TIPO_ASIENTO = $txt_tipo_asiento;
            $asiento->NRO_ASIENTO = $nro_asiento;
            $asiento->FEC_ASIENTO = $fec_asiento;        
            $asiento->TXT_GLOSA = $txt_glosa;
            $asiento->COD_CATEGORIA_ESTADO_ASIENTO = $cod_categoria_estado_asiento;
            $asiento->TXT_CATEGORIA_ESTADO_ASIENTO = $txt_categoria_estado_asiento;
            $asiento->COD_CATEGORIA_MONEDA = $cod_moneda;
            $asiento->TXT_CATEGORIA_MONEDA = $txt_categoria_moneda;
            $asiento->CAN_TIPO_CAMBIO = $tipo_cambio;
            $asiento->CAN_TOTAL_DEBE = $activoFijo->monto;
            $asiento->CAN_TOTAL_HABER = $activoFijo->monto;
            $asiento->IND_EXTORNO = $ind_extorno;
            $asiento->IND_ANULADO = $ind_anulado;
            $asiento->TXT_TIPO_REFERENCIA = $txt_tipo_referencia;
            $asiento->COD_USUARIO_CREA_AUD = $usuario_id;
            $asiento->FEC_USUARIO_CREA_AUD = $fec_creacion;
            $asiento->COD_USUARIO_MODIF_AUD = $usuario_id;
            $asiento->FEC_USUARIO_MODIF_AUD = $fec_creacion;
            $asiento->COD_ESTADO = $cod_estado;
            $asiento->IND_AFECTO = $ind_afecto;
            $asiento->COND_ASIENTO = $condicion_asiento;
            $asiento->COD_ACTIVO_FIJO = $activoFijo->id;
            $asiento->save();

            $resultado[$i]['fecha'] = date("d-m-Y"); //$fec_asiento;
            $resultado[$i]['glosa'] = $txt_glosa;
            $resultado[$i]['debe'] =  $activoFijo->monto;
            $resultado[$i]['haber'] =  $activoFijo->monto;

            $asientoMovimiento = new WEBAsientoCompraActivoFijoMovimiento(); //WEBAsientoMovimiento();
            //$asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientomovimientos');
            $asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientoscompraactivosfijosmovimientos');            
            $asientoMovimiento->COD_EMPR = $empresa_id;
            $asientoMovimiento->COD_CENTRO = $centro_id;                
            $asientoMovimiento->COD_ASIENTO = $asiento_id;
            $asientoMovimiento->COD_CUENTA_CONTABLE = $datos_cuenta_debe->id;
            $asientoMovimiento->TXT_CUENTA_CONTABLE = $datos_cuenta_debe->nro_cuenta;            
            $asientoMovimiento->TXT_GLOSA = $txt_glosa;   
            $asientoMovimiento->CAN_DEBE_MN = $activoFijo->monto;            
            $asientoMovimiento->CAN_HABER_MN = 0; 
            $asientoMovimiento->CAN_DEBE_ME = $activoFijo->monto/$tipo_cambio; 
            $asientoMovimiento->CAN_HABER_ME = 0; 
            $asientoMovimiento->NRO_LINEA = 1; 
            $asientoMovimiento->IND_EXTORNO = 0; 
            $asientoMovimiento->COD_USUARIO_CREA_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_CREA_AUD = $fec_asiento;
            $asientoMovimiento->COD_USUARIO_MODIF_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_MODIF_AUD = $fec_asiento;
            $asientoMovimiento->COD_ESTADO = $cod_estado;
            $asientoMovimiento->save();

            $resultado[$i]['detalle'][0] = array('cuenta' => $datos_cuenta_debe->nro_cuenta, 'glosa' => $txt_glosa, 'debe' => $activoFijo->monto, 'haber' => 0);

            $asientoMovimiento = new WEBAsientoCompraActivoFijoMovimiento(); //WEBAsientoMovimiento();
            //$asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientomovimientos');
            $asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientoscompraactivosfijosmovimientos');            
            $asientoMovimiento->COD_EMPR = $empresa_id;
            $asientoMovimiento->COD_CENTRO = $centro_id;                
            $asientoMovimiento->COD_ASIENTO = $asiento_id;
            $asientoMovimiento->COD_CUENTA_CONTABLE = $datos_cuenta_haber->id;
            $asientoMovimiento->TXT_CUENTA_CONTABLE = $datos_cuenta_haber->nro_cuenta;            
            $asientoMovimiento->TXT_GLOSA = $txt_glosa;   
            $asientoMovimiento->CAN_DEBE_MN = 0;            
            $asientoMovimiento->CAN_HABER_MN = $activoFijo->monto; 
            $asientoMovimiento->CAN_DEBE_ME = 0; 
            $asientoMovimiento->CAN_HABER_ME = $activoFijo->monto/$tipo_cambio; 
            $asientoMovimiento->NRO_LINEA = 2; 
            $asientoMovimiento->IND_EXTORNO = 0; 
            $asientoMovimiento->COD_USUARIO_CREA_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_CREA_AUD = $fec_asiento;
            $asientoMovimiento->COD_USUARIO_MODIF_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_MODIF_AUD = $fec_asiento;
            $asientoMovimiento->COD_ESTADO = $cod_estado;
            $asientoMovimiento->save();

            $resultado[$i]['detalle'][1] = array('cuenta' => $datos_cuenta_haber->nro_cuenta, 'glosa' => $txt_glosa, 'debe' => 0, 'haber' =>  $activoFijo->monto);

            $asientoMovimiento = new WEBAsientoCompraActivoFijoMovimiento(); //WEBAsientoMovimiento();
            //$asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientomovimientos');
            $asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientoscompraactivosfijosmovimientos');            
            $asientoMovimiento->COD_EMPR = $empresa_id;
            $asientoMovimiento->COD_CENTRO = $centro_id;                
            $asientoMovimiento->COD_ASIENTO = $asiento_id;
            $asientoMovimiento->COD_CUENTA_CONTABLE = $datos_cuenta_transferencia_debe->id;
            $asientoMovimiento->TXT_CUENTA_CONTABLE = $datos_cuenta_transferencia_debe->nro_cuenta;            
            $asientoMovimiento->TXT_GLOSA = $txt_glosa;   
            $asientoMovimiento->CAN_DEBE_MN = $activoFijo->monto;            
            $asientoMovimiento->CAN_HABER_MN = 0; 
            $asientoMovimiento->CAN_DEBE_ME = $activoFijo->monto/$tipo_cambio; 
            $asientoMovimiento->CAN_HABER_ME = 0; 
            $asientoMovimiento->NRO_LINEA = 3; 
            $asientoMovimiento->IND_EXTORNO = 0; 
            $asientoMovimiento->COD_USUARIO_CREA_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_CREA_AUD = $fec_asiento;
            $asientoMovimiento->COD_USUARIO_MODIF_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_MODIF_AUD = $fec_asiento;
            $asientoMovimiento->COD_ESTADO = $cod_estado;
            $asientoMovimiento->save();

            $resultado[$i]['detalle'][2] = array('cuenta' => $datos_cuenta_transferencia_debe->nro_cuenta, 'glosa' => $txt_glosa, 'debe' => $activoFijo->monto, 'haber' => 0);

            $asientoMovimiento = new WEBAsientoCompraActivoFijoMovimiento(); //WEBAsientoMovimiento();
            //$asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientomovimientos');
            $asientoMovimiento->COD_ASIENTO_MOVIMIENTO = $this->funciones->getCreateIdAsientoContableMovimiento('WEB.asientoscompraactivosfijosmovimientos');            
            $asientoMovimiento->COD_EMPR = $empresa_id;
            $asientoMovimiento->COD_CENTRO = $centro_id;                
            $asientoMovimiento->COD_ASIENTO = $asiento_id;
            $asientoMovimiento->COD_CUENTA_CONTABLE = $datos_cuenta_transferencia_haber->id;
            $asientoMovimiento->TXT_CUENTA_CONTABLE = $datos_cuenta_transferencia_haber->nro_cuenta;            
            $asientoMovimiento->TXT_GLOSA = $txt_glosa;   
            $asientoMovimiento->CAN_DEBE_MN = 0;            
            $asientoMovimiento->CAN_HABER_MN = $activoFijo->monto; 
            $asientoMovimiento->CAN_DEBE_ME = 0; 
            $asientoMovimiento->CAN_HABER_ME = $activoFijo->monto/$tipo_cambio; 
            $asientoMovimiento->NRO_LINEA = 4; 
            $asientoMovimiento->IND_EXTORNO = 0; 
            $asientoMovimiento->COD_USUARIO_CREA_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_CREA_AUD = $fec_asiento;
            $asientoMovimiento->COD_USUARIO_MODIF_AUD = $usuario_id;
            $asientoMovimiento->FEC_USUARIO_MODIF_AUD = $fec_asiento;
            $asientoMovimiento->COD_ESTADO = $cod_estado;
            $asientoMovimiento->save();

            $resultado[$i]['detalle'][3] = array('cuenta' => $datos_cuenta_transferencia_haber->nro_cuenta, 'glosa' => $txt_glosa, 'debe' => 0, 'haber' => $activoFijo->monto);

            $i++;
            //dd($resultado);            
        }
        return $resultado;
    }

    public function obtenerPeriodoActual($empresa_id, $centro_id){
        $periodo = DB::table('CON.PERIODO')
                       ->select('COD_PERIODO')
                       ->where('CON.PERIODO.COD_MES','=', '1') // TEST date("m")
                       ->where('CON.PERIODO.COD_ANIO','=', date("Y"))
                       ->where('CON.PERIODO.COD_EMPR','=', $empresa_id)
                       //->where('CON.PERIODO.COD_CENTRO','=', $centro_id)
                       ->first();        
        return $periodo->COD_PERIODO;
    }

    public function obtenerTipoCambio(){
        $tipo_cambio = DB::table('CMP.TIPO_CAMBIO')
                        ->select('CAN_VENTA')
                        //->where('CMP.TIPO_CAMBIO.FEC_CAMBIO','=', $fec_creacion) // TEST 
                        ->where('CMP.TIPO_CAMBIO.COD_CATEGORIA_MONEDA_ORIG','=', 'MON0000000000001')
                        ->where('CMP.TIPO_CAMBIO.COD_CATEGORIA_MONEDA_DEST','=', 'MON0000000000002')
                        ->first();                          
        return $tipo_cambio->CAN_VENTA;
    }

    public function obtenerCuentaUsuario($usuario_id){
        return $this->funciones->data_usuario($usuario_id)->name;
    }

    public function existe_asientos_en_periodo($cod_periodo)
    {
        $asientos_en_periodo = DB::table('WEB.ASIENTOS')
                            ->select('COD_ASIENTO')
                            ->where('COD_PERIODO','=', $cod_periodo)
                            ->where('TXT_TIPO_REFERENCIA','=', 'DEPRECIACION')
                            ->first();      

        isset($asientos_en_periodo->COD_ASIENTO) ? $existe_asientos = true : $existe_asientos = false;
        
        return $existe_asientos;
    }

    public function existe_asientos_apertura_anio()
    {
        $fecha_inicio = date("Y").'-01-01';
        $fecha_fin = date("Y").'-12-31';
        
        $asientos_apertura_en_periodo = DB::table('WEB.ASIENTOS')
                            ->select('COD_ASIENTO')
                            ->where('FEC_ASIENTO','>=', $fecha_inicio)
                            ->where('FEC_ASIENTO','<=', $fecha_fin)
                            ->where('TXT_TIPO_REFERENCIA','=', 'DEPRECIACION')
                            ->where('COND_ASIENTO','=', 'APERTURA')
                            ->first();      

        isset($asientos_apertura_en_periodo->COD_ASIENTO) ? $existe_asientos_apertura = true : $existe_asientos_apertura = false;
        
        return $existe_asientos_apertura;
    }

    public function existe_asientos_adquisicion_anio()
    {
        $fecha_inicio = date("Y").'-01-01';
        $fecha_fin = date("Y").'-12-31';
        
        $asientos_adquisicion_en_periodo = DB::table('WEB.ASIENTOS')
                            ->select('COD_ASIENTO')
                            ->where('FEC_ASIENTO','>=', $fecha_inicio)
                            ->where('FEC_ASIENTO','<=', $fecha_fin)
                            ->where('TXT_TIPO_REFERENCIA','=', 'DEPRECIACION')
                            ->where('COND_ASIENTO','=', 'ADQUISICION')
                            ->first();      

        isset($asientos_adquisicion_en_periodo->COD_ASIENTO) ? $existe_asientos_apertura = true : $existe_asientos_apertura = false;
        
        return $existe_asientos_apertura;
    }

    public function anula_asientos_depreciacion_periodo($cod_periodo)
    {
        $asientos_en_periodo = DB::table('WEB.ASIENTOS')
                                ->select('COD_ASIENTO')
                                ->where('COD_PERIODO','=', $cod_periodo)
                                ->where('TXT_TIPO_REFERENCIA','=', 'DEPRECIACION')
                                ->get();
        foreach($asientos_en_periodo as $asiento_en_periodo){
            $asientos_actualizados = DB::table('WEB.ASIENTOS')
                                        ->where('COD_ASIENTO','=', $asiento_en_periodo->COD_ASIENTO)
                                        ->update(array('COD_ESTADO' => 0, 'COD_CATEGORIA_ESTADO_ASIENTO' => 'IACHTE0000000007', 'TXT_CATEGORIA_ESTADO_ASIENTO' => 'CANCELADO'));                                         
        }
        foreach($asientos_en_periodo as $asiento_en_periodo){
            $asientos_movimientos_actualizados = DB::table('WEB.ASIENTOMOVIMIENTOS')
                                                    ->where('COD_ASIENTO','=', $asiento_en_periodo->COD_ASIENTO)
                                                    ->update(array('COD_ESTADO' => 0));    
        }       
    }

    public function validarPeriodo($activo, $mes, $anio, $tipo_activo, $anio_inicio_depreciacion, $mes_inicio_depreciacion)
    {        
        $combo_mes = $this->funciones->combo_mes();
        $combo_activo_fijo = $this->funciones->combo_activo_fijo();
        
        $existe_depreciacion = WEBDepreciacionActivoFijo::where('activo_fijo_id', $activo)->where('mes', $mes)->where('anio', $anio)->first();

        $validacion = array('error' => 0);

        if (isset($existe_depreciacion)) {
            $validacion = ['error' => 1, 'mensaje' => 'El proceso de depreciación ya existe para el periodo elegido.', 'combo_mes' => $combo_mes, 'combo_activo_fijo' => $combo_activo_fijo, "resultado" => '', "alerta" => 'alert-danger'];
        }
        if ($tipo_activo == 'COMPUESTO') {
            $validacion = ['error' => 1, 'mensaje' => 'El activo no puede ser depreciado.', 'combo_mes' => $combo_mes, 'combo_activo_fijo' => $combo_activo_fijo, "resultado" => '', "alerta" => 'alert-danger'];
        }
        if ($anio_inicio_depreciacion > $anio) {
            $validacion = ['error' => 1, 'mensaje' => 'El año de inicio que corresponde depreciar es mayor al año a procesar.', 'combo_mes' => $combo_mes, 'combo_activo_fijo' => $combo_activo_fijo, "resultado" => '', "alerta" => 'alert-danger'];
        } 
        if (($anio_inicio_depreciacion == $anio) && ($mes_inicio_depreciacion > $mes)) {
            $validacion = ['error' => 1, 'mensaje' => 'El mes de inicio que corresponde depreciar es mayor al periodo a procesar.', 'combo_mes' => $combo_mes, 'combo_activo_fijo' => $combo_activo_fijo, "resultado" => '', "alerta" => "alert-danger"];
        }        

        return $validacion;
    }
    /* public function validarPeriodoConjunto($activo, $mes, $anio, $tipo_activo, $anio_inicio_depreciacion, $mes_inicio_depreciacion)
    {
        $combo_mes = $this->funciones->combo_mes();
        $combo_activo_fijo = $this->funciones->combo_activo_fijo();
            
        if ($anio < $anio_inicio_depreciacion) {
            return ['mensaje' => 'El año de inicio que corresponde depreciar es mayor al periodo actual.', 'combo_mes' => $combo_mes, 'combo_activo_fijo' => $combo_activo_fijo, "resultado" => '', "alerta" => "alert-danger"];
        }

        if (($anio == $anio_inicio_depreciacion) && ($mes_inicio_depreciacion > $mes)) {
            return ['mensaje' => 'El mes de inicio que corresponde depreciar es mayor al mes a procesar.', 'combo_mes' => $combo_mes, 'combo_activo_fijo' => $combo_activo_fijo, "resultado" => '', "alerta" => "alert-danger"];
        }
    } */
}
