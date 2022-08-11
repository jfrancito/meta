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
		$lista_asiento          =   array();

		return View::make('navasoft/migracionnavasoft',
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




	public function actionAjaxBuscarListaNavasoft(Request $request)
	{

		$anio 					=   $request['anio'];
		$tipo_asiento_id 		=   $request['tipo_asiento_id'];
		$periodo_id 			=   $request['periodo_id'];
		$idopcion 				=   $request['idopcion'];

	    $listaasiento 			= 	WEBAsiento::where('COD_PERIODO','=',$periodo_id)
	    							->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
	    							->where('COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->orderby('FEC_ASIENTO','asc')
	    							->get();

		$lista_migracion 		= 	$this->ms_lista_migracion_navasoft($listaasiento,$anio);


    	$funcion 				= 	$this;

		return View::make('navasoft/ajax/alistamigracionnavasoft',
						 [
						 	'lista_migracion'			=> $lista_migracion,					 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,
						 	'ajax' 					=> true,					 	
						 ]);
	}



	public function actionDescargarArchivoMigrarNavasoft(Request $request)
	{


		set_time_limit(0);

		$anio 					=   $request['anio'];
		$tipo_asiento_id 		=   $request['tipo_asiento_id'];
		$periodo_id 			=   $request['periodo_id'];
		$idopcion 				=   $request['idopcion'];

		$tipoasiento 			= 	CMPCategoria::where('COD_CATEGORIA','=',$tipo_asiento_id)->first();
		$periodo 				= 	CONPeriodo::where('COD_PERIODO','=',$periodo_id)->first();


	    $listaasiento 			= 	WEBAsiento::where('COD_PERIODO','=',$periodo_id)
	    							->where('COD_EMPR','=',Session::get('empresas_meta')->COD_EMPR)
	    							->where('COD_CATEGORIA_TIPO_ASIENTO','=',$tipo_asiento_id)
	    							->where('COD_CATEGORIA_ESTADO_ASIENTO','=','IACHTE0000000025')
	    							->orderby('FEC_ASIENTO','asc')
	    							->get();

		$lista_migracion 		= 	$this->ms_lista_migracion_navasoft($listaasiento,$anio);

		$titulo 				=   'MstImp-'.Session::get('empresas_meta')->NOM_EMPR.'-'.$tipoasiento->NOM_CATEGORIA.'-'.$periodo->TXT_CODIGO;

	    Excel::create($titulo, function($excel) use ($lista_migracion) {
	        $excel->sheet('Hoja1', function($sheet) use ($lista_migracion) {
	            $sheet->loadView('navasoft/excel/elistamigracionnavasoft')->with('lista_migracion',$lista_migracion);         
	        });
	    })->export('xls');

	}

}
