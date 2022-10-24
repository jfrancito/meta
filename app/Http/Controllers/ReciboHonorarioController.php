<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;

use App\Modelos\WEBCuentaContable;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\WEBAsiento;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\WEBCuentaDetraccion;
use App\Modelos\CONPeriodo;
use App\Modelos\STDEmpresa;
use App\Modelos\WEBHistorialMigrar;


use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;
use App\Traits\ComprasTraits;
use App\Traits\MovilidadTraits;
use App\Traits\MigrarCompraTraits;

use App\Traits\ReciboHonorarioTraits;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;


use Illuminate\Support\Facades\Storage;

class ReciboHonorarioController extends Controller
{

	use GeneralesTraits;
	use AsientoModeloTraits;
	use PlanContableTraits;
	use ComprasTraits;
	use ReciboHonorarioTraits;


	use MovilidadTraits;
	use MigrarCompraTraits;


	public function actionListarReciboHonorario($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Gestionar asiento de compras');
	    $empresa_id = Session::get('empresas_meta')->COD_EMPR;

		if(Session::has('periodo_id_confirmar')){
			$sel_periodo 			=	Session::get('periodo_id_confirmar');
			$sel_serie 				=	Session::get('nro_serie_confirmar');
			$sel_nrodoc 			=	Session::get('nro_doc_confirmar');
			$anio 					=	Session::get('anio_confirmar');





        	$listacompras     		= 	$this->rh_lista_compras_asiento($anio,$sel_periodo,Session::get('empresas_meta')->COD_EMPR,$sel_serie,$sel_nrodoc);

        	$listacomprasterminado  = 	$this->rh_lista_compras_terminado_asiento($anio,$sel_periodo,Session::get('empresas_meta')->COD_EMPR,$sel_serie,$sel_nrodoc);




		}else{
			$sel_periodo 			=	'';
			$sel_serie 				=	'';
			$sel_nrodoc 			=	'';
			$anio  					=   $this->anio;
	    	$listacompras 			= 	array();
	    	$listacomprasterminado 	= 	array();



		}

        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
		$combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');

		$funcion 				= 	$this;
		
		return View::make('recibohonorario/listarecibohonorario',
						 [
						 	'listacompras' 			=> $listacompras,
						 	'listacomprasterminado' => $listacomprasterminado,
						 	'combo_anio_pc'			=> $combo_anio_pc,
						 	'combo_periodo'			=> $combo_periodo,
						 	'anio'					=> $anio,
						 	'sel_periodo'	 		=> $sel_periodo,
						 	'sel_serie'	 			=> $sel_serie,
						 	'sel_nrodoc'	 		=> $sel_nrodoc,				 	
						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxListarReciboHonorario(Request $request)
	{

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$serie 					=   $request['serie'];
		$documento 				=   $request['documento'];
		$empresa_id 			= 	Session::get('empresas_meta')->COD_EMPR;



        $listacompras     		= 	$this->rh_lista_compras_asiento($anio,$periodo_id,Session::get('empresas_meta')->COD_EMPR,$serie,$documento);

        $listacomprasterminado  = 	$this->rh_lista_compras_terminado_asiento($anio,$periodo_id,Session::get('empresas_meta')->COD_EMPR,$serie,$documento);


		$funcion 				= 	$this;
		
		return View::make('recibohonorario/ajax/alistarecibohonorario',
						 [
						 	'listacompras'			=> $listacompras,
						 	'listacomprasterminado'	=> $listacomprasterminado,
						 	'funcion'				=> $funcion,			 	
						 	'ajax' 					=> true,						 	
						 ]);
	}




	public function actionAjaxModalDetalleAsientoRHConfirmar(Request $request)
	{


		$asiento_id 			=   $request['asiento_id'];
		$idopcion 				=   $request['idopcion'];

		$anio 					=   $request['anio'];
		$periodo_id 			=   $request['periodo_id'];
		$serie 					=   $request['serie'];
		$documento 				=   $request['documento'];


	    $asiento 				= 	WEBAsiento::where('COD_ASIENTO','=',$asiento_id)->first();
	    $listaasientomovimiento = 	WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento_id)->orderBy('NRO_LINEA', 'asc')->get();

        $array_anio_pc     		= 	$this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
	    $anio  					=   $this->anio;
	    $combo_anio_pc  		= 	$this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
		$combo_periodo 			= 	$this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
		$sel_periodo 			=	'';

		$orden					=	$this->co_orden_xdocumento_contable($asiento->TXT_REFERENCIA);
		$sel_tipo_descuento		=	$this->co_orden_compra_tipo_descuento($orden);
		$combo_descuento 		= 	$this->co_generacion_combo_detraccion('DESCUENTO','Seleccione tipo descuento','');
		$funcion 				= 	$this;
		

		return View::make('recibohonorario/modal/ajax/mdetalleasientoconfirmar',
						 [
						 	'asiento'					=> $asiento,
						 	'listaasientomovimiento'	=> $listaasientomovimiento,
						 	'combo_periodo'				=> $combo_periodo,
						 	'combo_anio_pc'				=> $combo_anio_pc,
						 	'anio'						=> $anio,
						 	'sel_periodo'				=> $sel_periodo,

						 	'sel_tipo_descuento'		=> $sel_tipo_descuento,
						 	'combo_descuento'			=> $combo_descuento,
						 	'orden'						=> $orden,
						 	'idopcion'					=> $idopcion,

						 	'anio'						=> $anio,
						 	'periodo_id'				=> $periodo_id,
						 	'serie'						=> $serie,
						 	'documento'					=> $documento,


						 	'ajax' 						=> true,						 	
						 ]);
	}




	public function actionGonfirmarConfiguracionAsientoRHContablesXDocumentos($idopcion,$idasiento,Request $request)
	{

		if($_POST)
		{


			$anio_asiento 					= $request['anio_asiento'];
			$periodo_asiento_id 			= $request['periodo_asiento_id'];
			$tipo_descuento 				= $request['tipo_descuento'];
			$porcentaje_detraccion 			= $request['porcentaje_detraccion'];
			$total_detraccion 				= $request['total_detraccion'];

			$anio_confirmar 				= $request['anio_configuracion'];
			$periodo_id_confirmar 			= $request['periodo_id_configuracion'];
			$nro_serie_confirmar 			= $request['serie_configuracion'];
			$nro_doc_confirmar 				= $request['documento_configuracion'];

			$asiento 								= 	WEBAsiento::where('COD_ASIENTO','=',$idasiento)->first();

			$asiento->COD_CATEGORIA_ESTADO_ASIENTO 	=   'IACHTE0000000025';
			$asiento->TXT_CATEGORIA_ESTADO_ASIENTO 	=   'CONFIRMADO';
			$asiento->FEC_USUARIO_MODIF_AUD 		=   $this->fechaactual;
			$asiento->COD_USUARIO_MODIF_AUD 		=   Session::get('usuario_meta')->id;
			$asiento->COD_PERIODO 					=   $periodo_asiento_id;
			$asiento->COD_CATEGORIA_TIPO_DETRACCION =   $tipo_descuento;
			$asiento->CAN_DESCUENTO_DETRACCION 		=   $porcentaje_detraccion;
			$asiento->CAN_TOTAL_DETRACCION 			=   $total_detraccion;
			$asiento->save();

			sleep(1);
			$reversion = $this->co_reversion_compra($idasiento);
			sleep(4);


			Session::flash('periodo_id_confirmar', $periodo_id_confirmar);
			Session::flash('nro_serie_confirmar', $nro_serie_confirmar);
			Session::flash('nro_doc_confirmar', $nro_doc_confirmar);
			Session::flash('anio_confirmar', $anio_confirmar);

 		 	return Redirect::to('/gestion-recibo-honorario/'.$idopcion)->with('bienhecho', 'Asiento Modelo '.$asiento->NRO_SERIE.'-'.$asiento->NRO_DOC.' confirmado con exito');
		
		}


	}


    public function actionAjaxModalDetalleAsientoRHTransicion(Request $request)
    {


        $asiento_id             =   $request['asiento_id'];
        $idopcion               =   $request['idopcion'];

        $anio                   =   $request['anio'];
        $periodo_id             =   $request['periodo_id'];
        $serie                  =   $request['serie'];
        $documento              =   $request['documento'];


        $asiento                =   WEBAsiento::where('COD_ASIENTO','=',$asiento_id)->first();
        $listaasientomovimiento =   WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento_id)->orderBy('NRO_LINEA', 'asc')->get();

        $array_anio_pc          =   $this->pc_array_anio_cuentas_contable(Session::get('empresas_meta')->COD_EMPR);
        $anio                   =   $this->anio;
        $combo_anio_pc          =   $this->gn_generacion_combo_array('Seleccione año', '' , $array_anio_pc);
        $combo_periodo          =   $this->gn_combo_periodo_xanio_xempresa($anio,Session::get('empresas_meta')->COD_EMPR,'','Seleccione periodo');
        $sel_periodo            =   '';

        $orden                  =   $this->co_orden_xdocumento_contable($asiento->TXT_REFERENCIA);
        $sel_tipo_descuento     =   $this->co_orden_compra_tipo_descuento($orden);
        $combo_descuento        =   $this->co_generacion_combo_detraccion('DESCUENTO','Seleccione tipo descuento','');
        $funcion                =   $this;
        

        return View::make('recibohonorario/modal/ajax/mdetalleasientotransicion',
                         [
                            'asiento'                   => $asiento,
                            'listaasientomovimiento'    => $listaasientomovimiento,
                            'combo_periodo'             => $combo_periodo,
                            'combo_anio_pc'             => $combo_anio_pc,
                            'anio'                      => $anio,
                            'sel_periodo'               => $sel_periodo,
                            'sel_tipo_descuento'        => $sel_tipo_descuento,
                            'combo_descuento'           => $combo_descuento,
                            'orden'                     => $orden,
                            'idopcion'                  => $idopcion,
                            'anio'                      => $anio,
                            'periodo_id'                => $periodo_id,
                            'serie'                     => $serie,
                            'documento'                 => $documento,
                            'ajax'                      => true,                            
                         ]);
    }


	public function actionTransicionConfiguracionAsientoContablesRHXDocumentos($idopcion,$idasiento,Request $request)
	{

		if($_POST)
		{


			$anio_asiento 					= $request['anio_asiento'];


			$anio_confirmar 				= $request['anio_configuracion'];
			$periodo_id_confirmar 			= $request['periodo_id_configuracion'];
			$nro_serie_confirmar 			= $request['serie_configuracion'];
			$nro_doc_confirmar 				= $request['documento_configuracion'];

			$asiento 								= 	WEBAsiento::where('COD_ASIENTO','=',$idasiento)->first();

			$asiento->COD_CATEGORIA_ESTADO_ASIENTO 	=   'IACHTE0000000032';
			$asiento->TXT_CATEGORIA_ESTADO_ASIENTO 	=   'TRANSICION';
			$asiento->FEC_USUARIO_MODIF_AUD 		=   $this->fechaactual;
			$asiento->COD_USUARIO_MODIF_AUD 		=   Session::get('usuario_meta')->id;
			$asiento->save();


			Session::flash('periodo_id_confirmar', $periodo_id_confirmar);
			Session::flash('nro_serie_confirmar', $nro_serie_confirmar);
			Session::flash('nro_doc_confirmar', $nro_doc_confirmar);
			Session::flash('anio_confirmar', $anio_confirmar);

 		 	return Redirect::to('/gestion-recibo-honorario/'.$idopcion)->with('bienhecho', 'Asiento Modelo '.$asiento->NRO_SERIE.'-'.$asiento->NRO_DOC.' transicion con exito');
		
		}


	}

}
