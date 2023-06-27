<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;


use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\CMPDocumentoCtble;
use App\Modelos\CONPeriodo;
use App\Modelos\WEBInventarioSegundaVenta;


use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;
use App\Traits\ReporteVentaTraits;



use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use ZipArchive;
use Maatwebsite\Excel\Facades\Excel;


class ReporteVentasController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use ReporteVentaTraits;





	public function actionGestionReporteRegistroVenta($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Registro Venta');
	    $sel_tipo_asiento 		=	'';
	    $sel_periodo 			=	'';
	    $array_id_tipo_asiento  =    ['TAS0000000000003'];

	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_tipo_asiento 	= 	$this->gn_generacion_combo_categoria_xarrayid('TIPO_ASIENTO','Seleccione tipo asiento','',$array_id_tipo_asiento);
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');

		$funcion 				= 	$this;
		$combo_tran_gratuita 	= 	$this->gn_combo_transferencia_gratuita();
		$lista_asiento          =   array();

		return View::make('reporteventa/registroventa',
						 [
						 	'combo_tipo_asiento'	=> $combo_tipo_asiento,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'combo_tran_gratuita'	=> $combo_tran_gratuita,
						 	'anio'					=> $anio,
						 	'sel_tipo_asiento'	 	=> $sel_tipo_asiento,
						 	'sel_periodo'	 		=> $sel_periodo,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'lista_asiento' 		=> $lista_asiento,						 	
						 ]);
	}



	public function actionAjaxBuscarReporteRegistroVenta(Request $request)
	{

		$anio 					=   $request['anio'];
		$tipo_asiento_id 		=   $request['tipo_asiento_id'];
		$periodo_id 			=   $request['periodo_id'];
		$idopcion 				=   $request['idopcion'];
	    $empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
		$tipo_asiento_id 		=   'TAS0000000000003';

		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
	   	$mes 					= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT);

	    if($tipo_asiento_id == 'TAS0000000000003'){

		    $listaasiento 			= 	WEBAsiento::join('CMP.DOCUMENTO_CTBLE', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', '=', 'WEB.asientos.TXT_REFERENCIA')
		    							->join('WEB.historialmigrar', 'WEB.historialmigrar.COD_REFERENCIA', '=', 'WEB.asientos.TXT_REFERENCIA')
		    							->join('WEB.asientomodelos', 'WEB.asientomodelos.id', '=', 'WEB.historialmigrar.COD_ASIENTO_MODELO')
										->leftJoin(
											        DB::raw("
											            (select COD_ASIENTO, (SUM(CAN_DEBE_MN) + SUM(CAN_HABER_MN)) SIETE_CAN_HABER_MN  
															from WEB.asientomovimientos where IND_PRODUCTO = 1 
															GROUP BY COD_ASIENTO) siete
											        "), 'WEB.asientos.COD_ASIENTO', '=', 'siete.COD_ASIENTO'
										    		)
										->leftJoin(
											        DB::raw("
											            (select COD_ASIENTO, (SUM(CAN_DEBE_MN) + SUM(CAN_HABER_MN)) IVAP_CAN_HABER_MN  
															from WEB.asientomovimientos where TXT_CUENTA_CONTABLE like '4%'
															GROUP BY COD_ASIENTO) IVAP
											        "), 'WEB.asientos.COD_ASIENTO', '=', 'IVAP.COD_ASIENTO'
										    		)

										->leftJoin(
											        DB::raw("
											            (
															select count(dd.COD_TABLA) ind_boni,dd.COD_TABLA from WEB.asientos asi
															inner join CMP.DETALLE_PRODUCTO dd on asi.TXT_REFERENCIA = dd.COD_TABLA
															where asi.COD_EMPR = '".$empresa_id."'
															and asi.COD_CATEGORIA_TIPO_ASIENTO = '".$tipo_asiento_id."'
															and asi.COD_CATEGORIA_ESTADO_ASIENTO = 'IACHTE0000000025'
															and asi.COD_PERIODO = '".$periodo_id."'
															and dd.COD_OPERACION_AUX = 1
															group by dd.COD_TABLA
														) boni
											        "), 'WEB.asientos.TXT_REFERENCIA', '=', 'boni.COD_TABLA'
										    		)
		    							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
		    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
		    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
		    							//->where('COD_ASIENTO','=','ICCHAC0000005534')
		    							->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
		    							->select('WEB.asientos.*','WEB.asientomodelos.tipo_ivap_id',
		    										'WEB.asientomodelos.alias',
		    										'siete.SIETE_CAN_HABER_MN',
		    										'IVAP.IVAP_CAN_HABER_MN',
		    										'CMP.DOCUMENTO_CTBLE.IND_GRATUITO',
		    										'CMP.DOCUMENTO_CTBLE.IND_ANTICIPO',
		    										'boni.ind_boni')
		    							//->with('asientomovimiento:COD_ASIENTO,CAN_DEBE_MN,CAN_HABER_MN')
		    							//->where('WEB.asientos.COD_ASIENTO','=','ISRJAC0000000001')
		    							//->orderby('WEB.asientos.FEC_ASIENTO','asc')
		    							->get();


		   	//dd($listaasiento);

		    // $detallelistaasiento 	= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
		    // 							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
		    // 							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
		    // 							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
		    // 							->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
		    // 							//->select('WEB.asientomovimientos.*','')
		    // 							->select(DB::raw("WEB.asientomovimientos.* , 
		    // 												(CAN_DEBE_MN + CAN_HABER_MN) as CAN_DB_T,
						// 									CASE WHEN TXT_CUENTA_CONTABLE LIKE '4%' THEN '1' 
						// 									     ELSE '0' 
						// 									     END 
						// 									AS ind_ivap
		    // 												"))
		    // 							->get()
		    // 							->toArray();

		    $detallelistaasiento 	=	array();
		  	$detcoleccion 			= 	collect($detallelistaasiento);
		    //dd($listaasiento);
			// $detcoleccion 			= 	$detcoleccion->where('COD_ASIENTO', 'ISLMAC0000016754')
			// 					 		->where('COD_ESTADO','=',1)
			// 					 		->where('IND_PRODUCTO', '=', 1)->sum('CAN_DB_T');
			// dd($unique);
		 	// dd(collect($detallelistaasiento));
		    // dd($detallelistaasiento->where('COD_ESTADO','=',1)
						// 				->where('IND_PRODUCTO', '=', 1)
						// 				->where('COD_ASIENTO','=','ISLMAC0000016754')
						// 				->sum('CAN_DB_T'));

	    	$lista_asiento = $this->rv_reporte_registro_venta($anio,$mes,$listaasiento,$detcoleccion);

	    	$funcion 				= 	$this;

			return View::make('reporteventa/ajax/alistaregisroventas',
							 [
							 	'lista_asiento'			=> $lista_asiento,					 	
							 	'idopcion' 				=> $idopcion,
							 	'funcion' 				=> $funcion,
							 	'ajax' 					=> true,
							 	'pantalla' 				=> true,					 	
							 ]);



	    }

	}


	public function actionDescargarRegistroVentaExcel(Request $request)
	{


		set_time_limit(0);

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$tipo_asiento_id 		=   $request['tipo_asiento_id'];
		$data_archivo 			=   $request['data_archivo'];

	    $empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
		$tipo_asiento_id 		=   'TAS0000000000003';

		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();
	   	$mes 					= 	str_pad($periodo->COD_MES, 2, "0", STR_PAD_LEFT); 

	    //ventas 
	    if($tipo_asiento_id == 'TAS0000000000003'){


	    	//ARCHIVO PLE

		    $listaasiento 			= 	WEBAsiento::join('CMP.DOCUMENTO_CTBLE', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', '=', 'WEB.asientos.TXT_REFERENCIA')
		    							->join('WEB.historialmigrar', 'WEB.historialmigrar.COD_REFERENCIA', '=', 'WEB.asientos.TXT_REFERENCIA')
		    							->join('WEB.asientomodelos', 'WEB.asientomodelos.id', '=', 'WEB.historialmigrar.COD_ASIENTO_MODELO')
										->leftJoin(
											        DB::raw("
											            (select COD_ASIENTO, (SUM(CAN_DEBE_MN) + SUM(CAN_HABER_MN)) SIETE_CAN_HABER_MN  
															from WEB.asientomovimientos where IND_PRODUCTO = 1 
															GROUP BY COD_ASIENTO) siete
											        "), 'WEB.asientos.COD_ASIENTO', '=', 'siete.COD_ASIENTO'
										    		)
										->leftJoin(
											        DB::raw("
											            (select COD_ASIENTO, (SUM(CAN_DEBE_MN) + SUM(CAN_HABER_MN)) IVAP_CAN_HABER_MN  
															from WEB.asientomovimientos where TXT_CUENTA_CONTABLE like '4%'
															GROUP BY COD_ASIENTO) IVAP
											        "), 'WEB.asientos.COD_ASIENTO', '=', 'IVAP.COD_ASIENTO'
										    		)

										->leftJoin(
											        DB::raw("
											            (
															select count(dd.COD_TABLA) ind_boni,dd.COD_TABLA from WEB.asientos asi
															inner join CMP.DETALLE_PRODUCTO dd on asi.TXT_REFERENCIA = dd.COD_TABLA
															where asi.COD_EMPR = '".$empresa_id."'
															and asi.COD_CATEGORIA_TIPO_ASIENTO = '".$tipo_asiento_id."'
															and asi.COD_CATEGORIA_ESTADO_ASIENTO = 'IACHTE0000000025'
															and asi.COD_PERIODO = '".$periodo_id."'
															and dd.COD_OPERACION_AUX = 1
															group by dd.COD_TABLA
														) boni
											        "), 'WEB.asientos.TXT_REFERENCIA', '=', 'boni.COD_TABLA'
										    		)
		    							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
		    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
		    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
		    							//->where('COD_ASIENTO','=','ICCHAC0000005534')
		    							->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
		    							->select('WEB.asientos.*','WEB.asientomodelos.tipo_ivap_id',
		    										'WEB.asientomodelos.alias',
		    										'siete.SIETE_CAN_HABER_MN',
		    										'IVAP.IVAP_CAN_HABER_MN',
		    										'CMP.DOCUMENTO_CTBLE.IND_GRATUITO',
		    										'CMP.DOCUMENTO_CTBLE.IND_ANTICIPO',
		    										'boni.ind_boni')
		    							//->with('asientomovimiento:COD_ASIENTO,CAN_DEBE_MN,CAN_HABER_MN')
		    							//->where('WEB.asientos.COD_ASIENTO','=','ISRJAC0000000001')
		    							//->orderby('WEB.asientos.FEC_ASIENTO','asc')
		    							->get();

		    $detallelistaasiento 	=	array();

		    // $detallelistaasiento 	= 	WEBAsiento::join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
		    // 							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
		    // 							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
		    // 							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
		    // 							->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
		    // 							->select('WEB.asientomovimientos.*','')
		    // 							->select(DB::raw("WEB.asientomovimientos.* , 
		    // 												(CAN_DEBE_MN + CAN_HABER_MN) as CAN_DB_T,
						// 									CASE WHEN TXT_CUENTA_CONTABLE LIKE '4%' THEN '1' 
						// 									     ELSE '0' 
						// 									     END 
						// 									AS ind_ivap
		    // 												"))
		    // 							->get()
		    // 							->toArray();

		  	$detcoleccion 			= 	collect($detallelistaasiento);

	    	$lista_asiento = $this->rv_reporte_registro_venta($anio,$mes,$listaasiento,$detcoleccion);

			$titulo 		=   'REGISTRO-VENTA-'.$periodo->TXT_CODIGO;

		    Excel::create($titulo, function($excel) use ($lista_asiento,$periodo) {
		        $excel->sheet($periodo->TXT_CODIGO, function($sheet) use ($lista_asiento,$periodo) {

		        	$sheet->mergeCells('A1:A2');
                	$sheet->setCellValue('A1', "Tipo de venta");
		        	$sheet->mergeCells('B1:B2');
                	$sheet->setCellValue('B1', "Periodo");
                	$sheet->mergeCells('C1:C2');
                	$sheet->setCellValue('C1', "Codigo unico de la operación");
                	$sheet->mergeCells('D1:D2');
                	$sheet->setCellValue('D1', "Correlativo");
                	$sheet->mergeCells('E1:E2');
                	$sheet->setCellValue('E1', "Fecha de emisión");
                	$sheet->mergeCells('F1:F2');
                	$sheet->setCellValue('F1', "Fecha de vencimiento");

                	$sheet->mergeCells('G1:I1');
                	$sheet->setCellValue('G1', "COMPROBANTE DE PAGO");
					$sheet->setCellValue('G2', "Tipo");
					$sheet->setCellValue('H2', "Nro serie");
					$sheet->setCellValue('I2', "Nro comprobante");


                	$sheet->mergeCells('J1:L1');
                	$sheet->setCellValue('J1', "INFORMACIÓN DEL CLIENTE");
					$sheet->setCellValue('J2', "Tipo de documento identidad");
					$sheet->setCellValue('K2', "Documento identidad");
					$sheet->setCellValue('L2', "Nombre del cliente");

		        	$sheet->mergeCells('M1:M2');
                	$sheet->setCellValue('M1', "Valor facturado de la exportación");
		        	$sheet->mergeCells('N1:N2');
                	$sheet->setCellValue('N1', "Base imponible de la operación gravada");
		        	$sheet->mergeCells('O1:O2');
                	$sheet->setCellValue('O1', "Descuento de la base gravada");
		        	$sheet->mergeCells('P1:P2');
                	$sheet->setCellValue('P1', "Impuesto general a la ventas");

                	$sheet->mergeCells('Q1:R1');
                	$sheet->setCellValue('Q1', "IMPORTE TOTAL DE LA OPERACIÓN EXONERADA O INAFECTA");
					$sheet->setCellValue('Q2', "Exonerada");
					$sheet->setCellValue('R2', "Inafecta");

		        	$sheet->mergeCells('S1:S2');
                	$sheet->setCellValue('S1', "Base imponible de la operacion gravada");
		        	$sheet->mergeCells('T1:T2');
                	$sheet->setCellValue('T1', "IVAP");
		        	$sheet->mergeCells('U1:U2');
                	$sheet->setCellValue('U1', "Importe total del comprobante de pago");
		        	$sheet->mergeCells('V1:V2');
                	$sheet->setCellValue('V1', "Tipo de cambio");


                	$sheet->mergeCells('W1:Z1');
                	$sheet->setCellValue('W1', "REFERENCIA DEL COMPROBANTE DE PAGO O DOCUMENTO ORIGINAL QUE SE MODIFICA");
					$sheet->setCellValue('W2', "Fecha");
					$sheet->setCellValue('X2', "Tipo");
					$sheet->setCellValue('Y2', "Serie");
					$sheet->setCellValue('Z2', "Nro comprobante");


					$sheet->cell('A1:AA1',function($cell){
						$cell->setAlignment('center');
						$cell->setFontWeight('bold');
					});

		            $sheet->loadView('reporteventa/ajax/alistaregisroventas')->with('lista_asiento',$lista_asiento)
		            														 ->with('pantalla',false);         
		        });
		    })->export('xls');
	    }
	}



}
