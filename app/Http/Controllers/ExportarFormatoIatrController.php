<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

use View;
use Session;
use Hashids;

class ExportarFormatoIatrController extends Controller
{
    //

    public function reporteFormatoIATR(){

        /******************* validar url **********************/
		/* $validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;} */
	    /******************************************************/

        
        
		$combo_anios = DB::table('WEB.depreciacionesactivosfijos')->select("anio")->distinct("anio")->orderByDesc("anio")->pluck("anio","anio");
	 
		return View::make('logistica/reporte/iatr',
						 [
							'combo_anios' 			=> $combo_anios
						 ]);

    }

    public function exportarFormatoIATR(Request $request)
	{
		set_time_limit(0);		

		$funcion = $this;	

        $anio = $request["anio"];

		$empresa = Session::get('empresas_meta')->NOM_EMPR;
		$centro = 'CEN0000000000001';								

        $formato = $request["formato"];
        $extension = $request["extension"];

        if($formato == 'iatr' || $formato == 'iach'){
            
        $activosfijos =  DB::table('WEB.activosfijos')
                             ->join('WEB.depreciacionesactivosfijos', function($join) use ($anio){
                                    $empresa_id = Session::get('empresas_meta')->COD_EMPR;
                                    $join->on('WEB.activosfijos.id', '=', 'WEB.depreciacionesactivosfijos.activo_fijo_id')   
                                    ->where('WEB.depreciacionesactivosfijos.anio','=',$anio)
                                    ->where('WEB.activosfijos.cod_empresa','=',$empresa_id);
                                    })
							 ->join('WEB.categoriasactivosfijos', function($join){
                                    $join->on('WEB.activosfijos.categoria_activo_fijo_id', '=', 'WEB.categoriasactivosfijos.id');
                                    })			
							 ->leftJoin('CMP.DOCUMENTO_CTBLE', function($join){
                                    $join->on('WEB.activosfijos.cod_documento_ctble', '=', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE');
                                    })
							 ->leftJoin('STD.EMPRESA', function($join){
                                    $join->on('CMP.DOCUMENTO_CTBLE.COD_EMPR_EMISOR', '=', 'STD.EMPRESA.COD_EMPR');
                                    })
                                    ->select('WEB.activosfijos.*', 'WEB.categoriasactivosfijos.nombre as categoria', 'WEB.categoriasactivosfijos.cuenta_activo', 'WEB.depreciacionesactivosfijos.mes', 'WEB.depreciacionesactivosfijos.anio', 'WEB.depreciacionesactivosfijos.tasa_depreciacion', 'WEB.depreciacionesactivosfijos.monto', 'CMP.DOCUMENTO_CTBLE.NRO_SERIE', 'CMP.DOCUMENTO_CTBLE.NRO_DOC', 'CMP.DOCUMENTO_CTBLE.FEC_EMISION', 'CMP.DOCUMENTO_CTBLE.CAN_TOTAL', 'STD.EMPRESA.NRO_DOCUMENTO', 'STD.EMPRESA.NOM_EMPR')
                             ->get();

        } else {

        $activosfijos =  DB::table('WEB.activosfijos')
                            /*  ->join('WEB.depreciacionesactivosfijos', function($join) use ($anio){
                                    $empresa_id = Session::get('empresas_meta')->COD_EMPR;
                                    $join->on('WEB.activosfijos.id', '=', 'WEB.depreciacionesactivosfijos.activo_fijo_id')   
                                    ->where('WEB.depreciacionesactivosfijos.anio','=',$anio)
                                    ->where('WEB.activosfijos.cod_empresa','=',$empresa_id);
                                    }) */
							 ->join('WEB.categoriasactivosfijos', function($join){
                                    $join->on('WEB.activosfijos.categoria_activo_fijo_id', '=', 'WEB.categoriasactivosfijos.id');
                                    })
                             ->join('WEB.asientoscompraactivosfijos', function($join){
                                    $join->on('WEB.activosfijos.id', '=', 'WEB.asientoscompraactivosfijos.COD_ACTIVO_FIJO')
                                    ->where('WEB.asientoscompraactivosfijos.FEC_ASIENTO','>=',date("Y").'-01-01')
                                    ->where('WEB.asientoscompraactivosfijos.FEC_ASIENTO','<=',date("Y").'-12-31'); //TEST
                                    })                                    			
							 ->leftJoin('CMP.DOCUMENTO_CTBLE', function($join){
                                    $join->on('WEB.activosfijos.cod_documento_ctble', '=', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE');
                                    })
							 ->leftJoin('STD.EMPRESA', function($join){
                                    $join->on('CMP.DOCUMENTO_CTBLE.COD_EMPR_EMISOR', '=', 'STD.EMPRESA.COD_EMPR');
                                    })
                             //->select('WEB.activosfijos.*', 'WEB.categoriasactivosfijos.nombre as categoria', 'WEB.categoriasactivosfijos.cuenta_activo', 'WEB.depreciacionesactivosfijos.mes', 'WEB.depreciacionesactivosfijos.anio', 'WEB.depreciacionesactivosfijos.tasa_depreciacion', 'WEB.depreciacionesactivosfijos.monto', 'CMP.DOCUMENTO_CTBLE.NRO_SERIE', 'CMP.DOCUMENTO_CTBLE.NRO_DOC', 'CMP.DOCUMENTO_CTBLE.FEC_EMISION', 'CMP.DOCUMENTO_CTBLE.CAN_TOTAL', 'STD.EMPRESA.NRO_DOCUMENTO', 'STD.EMPRESA.NOM_EMPR', 'WEB.asientos.*')                             
                             ->select('WEB.activosfijos.*', 'WEB.categoriasactivosfijos.nombre as categoria', 'WEB.categoriasactivosfijos.cuenta_activo', 'WEB.categoriasactivosfijos.tasa_depreciacion', 'CMP.DOCUMENTO_CTBLE.NRO_SERIE', 'CMP.DOCUMENTO_CTBLE.NRO_DOC', 'CMP.DOCUMENTO_CTBLE.FEC_EMISION', 'CMP.DOCUMENTO_CTBLE.CAN_TOTAL', 'STD.EMPRESA.NRO_DOCUMENTO', 'STD.EMPRESA.NOM_EMPR', 'WEB.asientoscompraactivosfijos.*')
                             ->get();                             
        }
            //dd($activosfijos);

        //$mes =  DB::table('WEB.depreciacionesactivosfijos')->max('mes');   

        $mes = 0;

        $meses_esp = array(1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto',
        9=>'Setiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre');
        
        $catalogo = array();
        $i = 0;
        //dd($activosfijos);
        foreach ($activosfijos as $item) {	
            
			$depreciacion_acumulada_anio_anterior = DB::table('WEB.depreciacionesactivosfijos')
                                                          ->select(DB::raw('SUM(WEB.depreciacionesactivosfijos.monto) as monto'))
                                                          ->where('activo_fijo_id','=', $item->id)                                                          
                                                          ->where('anio','<', $anio)
                                                          ->first()
                                                          ->monto;
			$depreciacion_acumulada_total = DB::table('WEB.depreciacionesactivosfijos')
                                                          ->select(DB::raw('SUM(WEB.depreciacionesactivosfijos.monto) as monto'))
                                                          ->where('activo_fijo_id','=', $item->id)                                                          
                                                          ->where('anio','<=', $anio)
                                                          ->first()
                                                          ->monto;														  
			$depreciacion_anio = DB::table('WEB.depreciacionesactivosfijos')
                                                          ->select(DB::raw('SUM(WEB.depreciacionesactivosfijos.monto) as monto'))
                                                          ->where('activo_fijo_id','=', $item->id)                                                          
                                                          ->where('anio','=', $anio)
                                                          ->first()
                                                          ->monto;														  														  
            
          
            if($formato == 'iatr' || $formato == 'iach'){

                if($item->mes > $mes){
                    $mes = $item->mes;
                }
                
                $catalogo[$item->id]["fecha_registro"] = $item->fecha_registro;
                $catalogo[$item->id]["cuenta_activo"] = $item->cuenta_activo;			
                $catalogo[$item->id]["item_ple"] = $item->item_ple;
                $catalogo[$item->id]["tipo_activo"] = "";
                $catalogo[$item->id]["saldo_inicial"] = $item->saldo_inicio_depreciacion_acumulada > 0 ? $item->saldo_inicio_depreciacion_acumulada : $item->base_de_calculo;
                $catalogo[$item->id]["nombre"] = $item->nombre;
                $catalogo[$item->id]["factura"] = $item->NRO_SERIE . "-" . $item->NRO_DOC;
                $catalogo[$item->id]["empresa"] = $item->NOM_EMPR;
                $catalogo[$item->id]["ruc"] = $item->NRO_DOCUMENTO;
                $catalogo[$item->id]["modelo"] = $item->modelo;
                $catalogo[$item->id]["marca"] = $item->marca;
                $catalogo[$item->id]["numero_serie"] = $item->numero_serie;            
                $catalogo[$item->id]["categoria"] = $item->categoria;
                setlocale(LC_TIME,"es_ES");
                $catalogo[$item->id]["fecha_adquisicion"] = isset($item->FEC_EMISION) ? $item->FEC_EMISION : '';
                $catalogo[$item->id]["mes_adquisicion"] = isset($item->FEC_EMISION) ? date("n", strtotime($item->FEC_EMISION)) : '';
                $catalogo[$item->id]["adquisicion"] = $item->CAN_TOTAL;
                $catalogo[$item->id]["fecha_baja"] = $item->fecha_baja;
                $catalogo[$item->id]["base_de_calculo"] = $item->base_de_calculo;
                $catalogo[$item->id]["fecha_inicio_depreciacion"] = date("d/m/Y",strtotime($item->fecha_inicio_depreciacion));
                $catalogo[$item->id]["tasa_depreciacion"] = $item->tasa_depreciacion;
                $catalogo[$item->id]["depreciacion_acumulada_anio_anterior"] = $depreciacion_acumulada_anio_anterior;
                $catalogo[$item->id]["dias"] = $this->funciones->dias_mes(date("m",strtotime($item->fecha_inicio_depreciacion))) - date("d",strtotime($item->fecha_inicio_depreciacion)) + 1;
                $catalogo[$item->id]["por_depreciar"] = $item->base_de_calculo - $depreciacion_acumulada_anio_anterior;
                $catalogo[$item->id]["condicion"] = $item->estado_depreciacion;
                $catalogo[$item->id]["meses"][$item->mes] = $item->monto;
                $catalogo[$item->id]["depreciacion_acumulada_total"] = $depreciacion_acumulada_total;		
                $catalogo[$item->id]["depreciacion_anio"] = $depreciacion_anio;
                $catalogo[$item->id]["saldo_final_depreciacion"] = $depreciacion_acumulada_total;
                $catalogo[$item->id]["saldo_a_depreciar"] = $item->base_de_calculo - $depreciacion_acumulada_total;	
                $catalogo[$item->id]["mes"] = array(1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto',
                9=>'Setiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre');   
                /* $catalogo[$item->id]["cod_periodo"] = $item->COD_PERIODO;
                $catalogo[$item->id]["nro_asiento"] = $item->NRO_ASIENTO;
                $catalogo[$item->id]["cond_asiento"] = $item->COND_ASIENTO; */
            
            } else {
                
                $catalogo[$i]["fecha_registro"] = $item->fecha_registro;
                $catalogo[$i]["cuenta_activo"] = $item->cuenta_activo;			
                $catalogo[$i]["item_ple"] = $item->item_ple;
                $catalogo[$i]["tipo_activo"] = "";
                $catalogo[$i]["saldo_inicial"] = $item->saldo_inicio_depreciacion_acumulada > 0 ? $item->saldo_inicio_depreciacion_acumulada : $item->base_de_calculo;
                $catalogo[$i]["nombre"] = $item->nombre;
                $catalogo[$i]["factura"] = $item->NRO_SERIE . "-" . $item->NRO_DOC;
                $catalogo[$i]["empresa"] = $item->NOM_EMPR;
                $catalogo[$i]["ruc"] = $item->NRO_DOCUMENTO;
                $catalogo[$i]["modelo"] = $item->modelo;
                $catalogo[$i]["marca"] = $item->marca;
                $catalogo[$i]["numero_serie"] = $item->numero_serie;            
                $catalogo[$i]["categoria"] = $item->categoria;
                setlocale(LC_TIME,"es_ES");
                $catalogo[$i]["fecha_adquisicion"] = isset($item->FEC_EMISION) ? $item->FEC_EMISION : '';
                $catalogo[$i]["mes_adquisicion"] = isset($item->FEC_EMISION) ? date("n", strtotime($item->FEC_EMISION)) : '';
                $catalogo[$i]["adquisicion"] = $item->CAN_TOTAL;
                $catalogo[$i]["fecha_baja"] = $item->fecha_baja;
                $catalogo[$i]["base_de_calculo"] = $item->base_de_calculo;
                $catalogo[$i]["fecha_inicio_depreciacion"] = date("d/m/Y",strtotime($item->fecha_inicio_depreciacion));
                $catalogo[$i]["tasa_depreciacion"] = $item->tasa_depreciacion;
                $catalogo[$i]["depreciacion_acumulada_anio_anterior"] = $depreciacion_acumulada_anio_anterior;
                $catalogo[$i]["dias"] = $this->funciones->dias_mes(date("m",strtotime($item->fecha_inicio_depreciacion))) - date("d",strtotime($item->fecha_inicio_depreciacion)) + 1;
                $catalogo[$i]["por_depreciar"] = $item->base_de_calculo - $depreciacion_acumulada_anio_anterior;
                $catalogo[$i]["condicion"] = $item->estado_depreciacion;
                $catalogo[$i]["depreciacion_acumulada_total"] = $depreciacion_acumulada_total;		
                $catalogo[$i]["depreciacion_anio"] = $depreciacion_anio;
                $catalogo[$i]["saldo_final_depreciacion"] = $depreciacion_acumulada_total;
                $catalogo[$i]["saldo_a_depreciar"] = $item->base_de_calculo - $depreciacion_acumulada_total;	
                $catalogo[$i]["mes"] = array(1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto',
                9=>'Setiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre');   
                $catalogo[$i]["cod_periodo"] = $item->COD_PERIODO;
                $catalogo[$i]["nro_asiento"] = $item->NRO_ASIENTO;
                $catalogo[$i]["cond_asiento"] = $item->COND_ASIENTO;

                $i++;
            }
        }
		//dd($catalogo);		

        switch ($formato) {
            case 'iatr':
                $view = 'logistica/excel/formatoiatr';
                $titulo = 'Formato Contable IATR';
                break;
            case 'iach':
                $view = 'logistica/excel/formatoiach';
                $titulo = 'Formato Contable IACH';
                break;
            case 'pleuno':
                $view = 'logistica/excel/formatopleuno';
                $titulo = 'Formato PLE 7.1';
                break;
            case 'plecuatro':
                $view = 'logistica/excel/formatoplecuatro';
                $titulo = 'Formato PLE 7.4';
                break;            
            default:
                $view = 'logistica/excel/formatoiatr';
                $titulo = 'Formato Contable IATR';
                break;
        }

        $ext = $extension == 'excel' ? 'xls' : 'csv';

	    Excel::create($titulo, function($excel) use ($catalogo,$titulo,$funcion,$empresa,$centro,$mes,$meses_esp,$anio,$view) {

	        $excel->sheet('Formato IATR', function($sheet) use ($catalogo,$titulo,$funcion,$empresa,$centro,$mes,$meses_esp,$anio,$view) {

	            $sheet->loadView($view)->with('catalogo',$catalogo)
	                                         		 ->with('titulo',$titulo)
	                                         		 ->with('empresa',$empresa)
	                                         		 ->with('centro',$centro)	                                         		 
	                                         		 ->with('funcion',$funcion)
	                                         		 ->with('mes',$mes)
                                                     ->with('meses_esp',$meses_esp)
                                                     ->with('anio_actual',$anio);	                                         		 
	        });
	    })->export($ext);

	}
}
