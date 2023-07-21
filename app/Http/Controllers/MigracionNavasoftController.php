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
use App\Modelos\CMPCategoria;



use App\Traits\GeneralesTraits;
use App\Traits\PlanContableTraits;
use App\Traits\MigracionNavasoftTraits;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use ZipArchive;
use Maatwebsite\Excel\Facades\Excel;


class MigracionNavasoftController extends Controller
{

	use GeneralesTraits;
	use PlanContableTraits;
	use MigracionNavasoftTraits;

	public function actionGestionMigracionNavasoft($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Migracion a Navasoft');
	    $sel_tipo_asiento 		=	'';
	    $sel_periodo 			=	'';

	    $array_id_tipo_asiento  =    ['TAS0000000000003','TAS0000000000004'];

	    $anio  					=   $this->anio;
        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
	    $combo_tipo_asiento 	= 	$this->gn_generacion_combo_categoria_xarrayid('TIPO_ASIENTO','Seleccione tipo asiento','',$array_id_tipo_asiento);
	    $combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$funcion 				= 	$this;
		$combo_tran_gratuita 	= 	$this->gn_combo_transferencia_gratuita();
		$combo_estado_migracion = 	$this->gn_generacion_combo_categoria('CONTABILIDAD_ESM','Seleccione estado migración','TODO');
	    $sel_estado_migracion 	=	'TODO';

		$lista_asiento          =   array();

		return View::make('navasoft/migracionnavasoft',
						 [
						 	'combo_tipo_asiento'	=> $combo_tipo_asiento,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'combo_tran_gratuita'	=> $combo_tran_gratuita,
						 	'combo_estado_migracion'=> $combo_estado_migracion,

						 	'anio'					=> $anio,
						 	'sel_tipo_asiento'	 	=> $sel_tipo_asiento,
						 	'sel_periodo'	 		=> $sel_periodo,
						 	'sel_estado_migracion'	=> $sel_estado_migracion,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'lista_asiento' 		=> $lista_asiento,						 	
						 ]);
	}




	public function actionAjaxBuscarListaNavasoft(Request $request)
	{

		$anio 					=   $request['anio'];
		$tipo_asiento_id 		=   $request['tipo_asiento_id'];
		$periodo_id 			=   $request['periodo_id'];
		$idopcion 				=   $request['idopcion'];
		$estado_migracion_id 	=   $request['estado_migracion_id'];
		$ind_migracion 			= 	-1;

		if($estado_migracion_id == 'CEM0000000000001'){
			$ind_migracion 			= 	1;
		}else{
			if($estado_migracion_id == 'CEM0000000000002'){
				$ind_migracion 			= 	0;
			}
		}




    	$funcion 				= 	$this;

	    if($tipo_asiento_id == 'TAS0000000000004'){

	    $listaasiento 			= 	WEBAsiento::where('COD_PERIODO','=',$periodo_id)
	    							->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
	    							->where('COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							//->where('COD_ASIENTO','=','ITRJAC0000000414')
	    							->MigracionNava($ind_migracion)
	    							->orderby('FEC_USUARIO_MODIF_AUD','desc')
	    							->get();



	    	$lista_migracion 		= 	$this->ms_lista_migracion_navasoft_compras($listaasiento,$anio);


			return View::make('navasoft/ajax/alistamigracionnavasoftcompra',
							 [
							 	'lista_migracion'			=> $lista_migracion,					 	
							 	'idopcion' 				=> $idopcion,
							 	'funcion' 				=> $funcion,
							 	'ajax' 					=> true,					 	
							 ]);

	    }else{



	    	$empresa_id = Session::get('empresas_meta')->COD_EMPR;

		    $listaasiento 			= 	WEBAsiento::join('CMP.DOCUMENTO_CTBLE', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', '=', 'WEB.asientos.TXT_REFERENCIA')
		    							->join('WEB.historialmigrar', 'WEB.historialmigrar.COD_REFERENCIA', '=', 'WEB.asientos.TXT_REFERENCIA')
		    							->join('WEB.asientomodelos', 'WEB.asientomodelos.id', '=', 'WEB.historialmigrar.COD_ASIENTO_MODELO')
		    							->join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
		    							->leftJoin('WEB.productoempresas', function($join) use ($anio, $empresa_id)
				                         {
				                             $join->on('WEB.productoempresas.producto_id', '=', 'WEB.asientomovimientos.COD_PRODUCTO')
				                             ->where('WEB.productoempresas.anio','=',$anio)
				                              ->where('WEB.productoempresas.empresa_id','=',$empresa_id);
				                         })
			                            ->where('WEB.productoempresas.anio','=',$anio)
			                             ->where('WEB.productoempresas.empresa_id','=',$empresa_id)
		    							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
		    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
		    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
		    							->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
		    							->where('WEB.asientomovimientos.IND_PRODUCTO','=',1)
		    							->MigracionNava($ind_migracion)
		    							->select('WEB.asientos.*','WEB.asientomodelos.tipo_ivap_id',
		    										'WEB.asientomodelos.alias',
		    										'WEB.asientomodelos.tipo_ivap_id',
		    										'CMP.DOCUMENTO_CTBLE.IND_GRATUITO',
		    										'CMP.DOCUMENTO_CTBLE.IND_ANTICIPO',
		    										'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE',
		    										'WEB.productoempresas.codigo_migracion',
		    										'WEB.asientomovimientos.*')
		    							->orderby('WEB.asientos.COD_ASIENTO','ASC')
		    							->get();



	    	$nombre_excel 			= 	'Ventas';

	    	if( $empresa_id == 'EMP0000000000007'){
	    		$lista_migracion 		= 	$this->ms_lista_migracion_navasoft($listaasiento,$anio);
	    	}else{
	    		$lista_migracion 		= 	$this->ms_lista_migracion_navasoft_comerciales($listaasiento,$anio);
	    	}





			return View::make('navasoft/ajax/alistamigracionnavasoft',
							 [
							 	'lista_migracion'			=> $lista_migracion,					 	
							 	'idopcion' 				=> $idopcion,
							 	'funcion' 				=> $funcion,
							 	'ajax' 					=> true,					 	
							 ]);

	    }





	}



	public function actionDescargarArchivoMigrarNavasoft(Request $request)
	{


		set_time_limit(0);

		$anio 					=   $request['anio'];
		$tipo_asiento_id 		=   $request['tipo_asiento_id'];

		$periodo_id 			=   $request['periodo_id'];
		$idopcion 				=   $request['idopcion'];
		$migrado 				=   $request['migrado'];
		$excel 					=   '1';
	    $empresa_id 			=	Session::get('empresas_meta')->COD_EMPR;
		$tipoasiento 			= 	CMPCategoria::where('COD_CATEGORIA','=',$tipo_asiento_id)->first();
		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();

		$estado_migracion_id 	=   $request['estado_migracion_id'];
		$ind_migracion 			= 	-1;

		if($estado_migracion_id == 'CEM0000000000001'){
			$ind_migracion 			= 	1;
		}else{
			if($estado_migracion_id == 'CEM0000000000002'){
				$ind_migracion 			= 	0;
			}
		}




	    if($tipo_asiento_id == 'TAS0000000000004'){


		    $listaasiento 			= 	WEBAsiento::where('COD_PERIODO','=',$periodo_id)
		    							->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
		    							->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
		    							->where('COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
		    							->orderby('FEC_ASIENTO','asc')
		    							//->where('COD_ASIENTO','=','ITBEAC0000001101')
		    							->MigracionNava($ind_migracion)
		    							->orderby('FEC_USUARIO_MODIF_AUD','desc')
		    							->get();


	    	$nombre_excel 			= 	'Compras';
	    	$lista_migracion 		= 	$this->ms_lista_migracion_navasoft_compras($listaasiento,$anio,$migrado,$excel);

			$titulo 				=   'MstImp_'.$nombre_excel;
			
		    Excel::create($titulo, function($excel) use ($lista_migracion) {
		        $excel->sheet('Hoja1', function($sheet) use ($lista_migracion) {
		            $sheet->loadView('navasoft/excel/elistamigracionnavasoftcompra')->with('lista_migracion',$lista_migracion);         
		        });
		    })->export('xls');


	    }else{

	    	$empresa_id = Session::get('empresas_meta')->COD_EMPR;

		    $listaasiento 			= 	WEBAsiento::join('CMP.DOCUMENTO_CTBLE', 'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE', '=', 'WEB.asientos.TXT_REFERENCIA')
		    							->join('WEB.historialmigrar', 'WEB.historialmigrar.COD_REFERENCIA', '=', 'WEB.asientos.TXT_REFERENCIA')
		    							->join('WEB.asientomodelos', 'WEB.asientomodelos.id', '=', 'WEB.historialmigrar.COD_ASIENTO_MODELO')
		    							->join('WEB.asientomovimientos', 'WEB.asientomovimientos.COD_ASIENTO', '=', 'WEB.asientos.COD_ASIENTO')
		    							->leftJoin('WEB.productoempresas', function($join) use ($anio, $empresa_id)
				                         {
				                             $join->on('WEB.productoempresas.producto_id', '=', 'WEB.asientomovimientos.COD_PRODUCTO')
				                             ->where('WEB.productoempresas.anio','=',$anio)
				                              ->where('WEB.productoempresas.empresa_id','=',$empresa_id);
				                         })
			                            ->where('WEB.productoempresas.anio','=',$anio)
			                             ->where('WEB.productoempresas.empresa_id','=',$empresa_id)
		    							->where('WEB.asientos.COD_PERIODO','=',$periodo_id)
		    							->where('WEB.asientos.COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
		    							->where('WEB.asientos.COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
		    							->where('WEB.asientos.COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
		    							->where('WEB.asientomovimientos.IND_PRODUCTO','=',1)
		    							->MigracionNava($ind_migracion)
		    							->select('WEB.asientos.*','WEB.asientomodelos.tipo_ivap_id',
		    										'WEB.asientomodelos.alias',
		    										'WEB.asientomodelos.tipo_ivap_id',
		    										'CMP.DOCUMENTO_CTBLE.IND_GRATUITO',
		    										'CMP.DOCUMENTO_CTBLE.IND_ANTICIPO',
		    										'CMP.DOCUMENTO_CTBLE.COD_DOCUMENTO_CTBLE',
		    										'WEB.productoempresas.codigo_migracion',
		    										'WEB.asientomovimientos.*')
		    							->orderby('WEB.asientos.COD_ASIENTO','ASC')
		    							->get();



	    	$nombre_excel 			= 	'Ventas';

	    	if( $empresa_id == 'EMP0000000000007'){
	    		$lista_migracion 		= 	$this->ms_lista_migracion_navasoft($listaasiento,$anio,$migrado,$excel);
	    	}else{
	    		$lista_migracion 		= 	$this->ms_lista_migracion_navasoft_comerciales($listaasiento,$anio,$migrado,$excel);
	    	}


			$titulo 				=   'MstImp_'.$nombre_excel;
			
		    Excel::create($titulo, function($excel) use ($lista_migracion) {
		        $excel->sheet('Hoja1', function($sheet) use ($lista_migracion) {
		            $sheet->loadView('navasoft/excel/elistamigracionnavasoft')->with('lista_migracion',$lista_migracion);         
		        });
		    })->export('xls');

	    }


	}

}
