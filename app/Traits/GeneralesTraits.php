<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use App\Modelos\CONPeriodo;
use App\Modelos\CMPDetalleProducto;
use App\Modelos\STDEmpresa;
use App\Modelos\WEBProductoEmpresa;
use App\Modelos\WEBCuentaDetraccion;
use App\Modelos\CMPTipoCambio;



use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

trait GeneralesTraits
{


	public function gn_tipo_cambio($fecha)
	{
		$tipo_cambio   				=   CMPTipoCambio::where('FEC_CAMBIO','<=',$fecha)
										->orderBy('FEC_CAMBIO', 'DESC')
										->first();
	    return $tipo_cambio;
	}

	public function gn_array_meses()
	{
		$meses 		= 	array('1' => 'ENERO','2' => 'FEBRERO','3' => 'MARZO','4' => 'ABRIL','5' => 'MAYO','6' => 'JUNIO','7' => 'JULIO','8' => 'AGOSTO','9' => 'SETIEMBRE','10' => 'OCTUBRE','11' => 'NOVIEMBRE','12' => 'DICIEMBRE');
	 	return  $meses;
	}


	public function gn_data_empresa($cod_empresa_id)
	{
		$empresa 		= 	STDEmpresa::where('COD_EMPR','=',$cod_empresa_id)->first();
	 	return  $empresa;
	}

	private function gn_generacion_combo_array($titulo, $todo , $array)
	{
		if($todo=='TODO'){
			$combo_anio_pc  		= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo_anio_pc  		= 	array('' => $titulo) + $array;
		}
	    return $combo_anio_pc;
	}

	private function gn_generacion_combo_meses_array($titulo, $todo)
	{

		$meses   					=   $this->gn_array_meses();
		$combo_meses 				= 	array('' => $titulo) + $meses;
	    return $combo_meses;
	}


	private function gn_genecombo_cuenta_contable_xnrocuentapadre($nro_cuenta,$anio)
	{

		$cuentacontable 			= 	WEBCuentaContable::where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
										->where('nro_cuenta','=',$nro_cuenta)
										->where('anio','=',$anio)
										->where('activo','=',1)
										->first();

		$array 						= 	DB::table('WEB.cuentacontables')
        								->where('activo','=',1)
        								->where('empresa_id','=',Session::get('empresas_meta')->COD_EMPR)
        								->where('cuenta_contable_superior_id','=',$cuentacontable->id)
        								->select(DB::raw(" (nro_cuenta+'-'+nombre) as nro_cuenta,id "))
		        						->pluck('nro_cuenta','id')
										->toArray();

		$combo  					= 	array('' => 'Seleccione una cuenta') + $array;

	    return $combo;
	}

	private function gn_generacion_combo($tabla,$atributo1,$atributo2,$titulo,$todo) {
		
		$array 						= 	DB::table($tabla)
        								->where('activo','=',1)
		        						->pluck($atributo2,$atributo1)
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;					 			
	}

	private function gn_generacion_combo_tabla_osiris($tabla,$atributo1,$atributo2,$titulo,$todo) {
		
		$array 							= 	DB::table($tabla)
        									->where('COD_ESTADO','=',1)
		        							->pluck($atributo2,$atributo1)
											->toArray();
		if($titulo==''){
			$combo  					= 	$array;
		}else{
			if($todo=='TODO'){
				$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
			}else{
				$combo  				= 	array('' => $titulo) + $array;
			}
		}

	 	return  $combo;					 			
	}

	private function gn_generacion_combo_categoria($txt_grupo,$titulo,$todo) {
		
		$array 						= 	DB::table('CMP.CATEGORIA')
        								->where('COD_ESTADO','=',1)
        								->where('TXT_GRUPO','=',$txt_grupo)
		        						->pluck('NOM_CATEGORIA','COD_CATEGORIA')
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;					 			
	}

	private function gn_generacion_combo_categoria_xarrayid($txt_grupo,$titulo,$todo,$array_ids) {
		
		$array 						= 	DB::table('CMP.CATEGORIA')
        								->where('COD_ESTADO','=',1)
        								->where('TXT_GRUPO','=',$txt_grupo)
        								->whereIn('COD_CATEGORIA', $array_ids)
		        						->pluck('NOM_CATEGORIA','COD_CATEGORIA')
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;					 			
	}


	public function gn_background_fila_activo($activo)
	{
		$background =	'';
		if($activo == 0){
			$background = 'fila-desactivada';
		}
	    return $background;
	}


	public function gn_background_fila_ind_extorno($activo)
	{
		$background =	'';
		if($activo == 1){
			$background = 'fila-desactivada';
		}
	    return $background;
	}

	public function gn_combo_tipo_cliente()
	{
		$combo  	= 	array('' => 'Seleccione tipo de cliente' , '0' => 'Tercero', '1' => 'Relacionada');
	    return $combo;
	}

	public function gn_combo_transferencia_gratuita()
	{
		$combo  	= 	array('TODOS' => 'TODOS' , '1' => 'Tranferencia gratuita');
	    return $combo;
	}



	private function gn_generacion_combo_cuenta_detraccion($titulo,$todo)
	{

	   	$array   					=   STDEmpresa::where('COD_ESTADO','=',1)
	   									->where('IND_PROVEEDOR','=',1)
	   									->select(DB::raw(" (NRO_DOCUMENTO + ' - ' + NOM_EMPR) as NOM_EMPR , COD_EMPR"))
	   									->pluck('NOM_EMPR','NRO_DOCUMENTO')
		        						->take(10)
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;	
	}


	private function gn_generacion_combo_productos($titulo,$todo)
	{


		$array 						= 	ALMProducto::where('COD_ESTADO','=',1)
										->whereIn('IND_MATERIAL_SERVICIO', ['M','S'])
		        						->pluck('NOM_PRODUCTO','COD_PRODUCTO')
		        						->take(10)
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;	
	}


	public function gn_combo_periodo_xanio_xempresa($anio,$cod_empresa,$todo,$titulo)
	{
		$array 						= 	CONPeriodo::where('COD_ESTADO','=',1)
										->where('COD_ANIO','=',$anio)
										->where('COD_EMPR','=',$cod_empresa)
		        						->pluck('TXT_NOMBRE','COD_PERIODO')
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;	


	}



	private function gn_detalle_producto_xcoddocumento($cod_documento)
	{

		$listadetalleproducto 		= 	CMPDetalleProducto::where('COD_ESTADO','=',1)
										->where('COD_TABLA','=',$cod_documento)
		        						->get();

	 	return  $listadetalleproducto;	
	}

	public function gn_cliente_relacionado_tercero_xempresa($cod_empresa)
	{

		$tipo_cliente   =   "TERCERO";
		$empresa 		= 	STDEmpresa::where('COD_EMPR','=',$cod_empresa)
		        			->first();
		if($empresa->IND_RELACIONADO==1){
			$tipo_cliente   =   "RELACIONADO";
		}
	 	return  $tipo_cliente;	
	}


	private function gn_ind_relacionado_tercero_xempresa($cod_empresa)
	{
		$empresa 		= 	STDEmpresa::where('COD_EMPR','=',$cod_empresa)
		        			->first();

	 	return  $empresa->IND_RELACIONADO;	
	}


	public function gn_cuenta_contable_xproducto_xempresa_xanio($cod_producto,$cod_empresa,$ind_cliente,$anio_documento,$tipo_asiento)
	{

		$cuenta_contable= 	'';

		$empresa 		= 	WEBProductoEmpresa::where('producto_id','=',$cod_producto)
							->where('empresa_id','=',$cod_empresa)
							->where('anio','=',$anio_documento)
							//->where('cod_categoria_tipo_asiento','=',$tipo_asiento)
		        			->first();

		if(count($empresa)>0){

			//ventas
			if($tipo_asiento == 'TAS0000000000003'){
				if($ind_cliente == 0){
					if(trim($empresa->cuenta_contable_venta_tercero_id) != ''){
						$cuenta_contable = 	$empresa->cuentacontabletercero->nro_cuenta .' '.$empresa->cuentacontabletercero->nombre;
					}
				}else{
					if(trim($empresa->cuenta_contable_venta_relacionada_id) !=  ''){
						$cuenta_contable = 	$empresa->cuentacontablerelacionada->nro_cuenta .' '.$empresa->cuentacontablerelacionada->nombre;
					}
				}	
			}
			//compras
			if($tipo_asiento == 'TAS0000000000004'){
				if(trim($empresa->cuenta_contable_compra_id) != ''){
					$cuenta_contable = 	$empresa->cuentacontablecompra->nro_cuenta .' '.$empresa->cuentacontablecompra->nombre;
				}
			}

		}
	 	return  $cuenta_contable;	
	}

}