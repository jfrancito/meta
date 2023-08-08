<?php
namespace App\Biblioteca;

use Illuminate\Support\Facades\DB;
use Hashids,Session,Redirect,table;
use App\Modelos\WEBRolOpcion,App\Modelos\WEBUserEmpresaCentro;
use App\User;
use App\Modelos\WEBCategoriaActivoFijo;
use App\Modelos\WEBActivoFijo;
use Keygen;
use PDO;

class Funcion{
  public function getCreateIdDepreciacionActivoFijo($tabla)
  {
	$id="";
	$id = DB::table($tabla)
			  ->select(DB::raw('max(SUBSTRING(id,5,8)) as id'))
			  ->get();
	$idsuma = (int)$id[0]->id + 1;
	$idopcioncompleta = str_pad($idsuma, 8, "0", STR_PAD_LEFT);
	$prefijo = 'DEPR';
	$idopcioncompleta = $prefijo.$idopcioncompleta;
	return $idopcioncompleta;	
  }

  function diasAnio($year){
	if(date('L',mktime(1,1,1,1,1,$year))){
		$dias_anio = 366;
	} else {
		$dias_anio = 365;
	}
	return $dias_anio;
  }
  public function compararFechas($fecha_inicio, $fecha_fin){
	$fechai=date_create($fecha_inicio);
	$fechaf=date_create($fecha_fin);
	$diff=date_diff($fechai,$fechaf);
	return $diff->format("%R%a");	
  }
  public function combo_mes() 
  {
	$meses = array(1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto',
					9=>'Setiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre');
	return $meses;
  }
  public function dias_mes($mes){
	return cal_days_in_month(CAL_GREGORIAN, $mes, date("Y"));
  }

  public function getCreateIdActivoFijo($tabla) {
	$id="";
	$id = DB::table($tabla)
			  ->select(DB::raw('max(SUBSTRING(id,5,8)) as id'))
			  ->get();
	$idsuma = (int)$id[0]->id + 1;
	$idopcioncompleta = str_pad($idsuma, 8, "0", STR_PAD_LEFT);
	$prefijo = 'ACTF';
	$idopcioncompleta = $prefijo.$idopcioncompleta;
	return $idopcioncompleta;	
  }

  public function combo_estado_conservacion_activo_fijo($estado_conservacion = '')
  {
	$estados = array('BUENO' => 'BUENO', 'REGULAR' => 'REGULAR', 'MALO' => 'MALO');
	if($estado_conservacion != ''){
		$estado_conservacion_sel = array($estado_conservacion => $estado_conservacion);
		$estados = $estado_conservacion_sel + $estados;
	}
	return $estados;
  }

  public function combo_estado_activo_fijo($estado = '')
  {
	$estados = array('OPERATIVO' => 'OPERATIVO', 'BAJA' => 'BAJA');
	if($estado != ''){
		$estado_sel = array($estado => $estado);
		$estados = $estado_sel + $estados;
	}
	return $estados;
  }

  public function combo_tipo_activo_fijo($tipo='')
  {
	$estados = array('INDIVIDUAL' => 'INDIVIDUAL', 'COMPUESTO' => 'COMPUESTO');
	if($tipo != ''){
		$tipo_sel = array($tipo => $tipo);
		$estados = $tipo_sel + $estados;
	}
	return $estados;
  }

  public function getCreateItemPle($tabla, $cantidad=1)
  {
	$iditemplearr = array();
	$id="";
	$id = DB::table($tabla)
			  ->select(DB::raw('max(SUBSTRING(item_ple,3,7)) as item_ple'))
			  ->get();
	for ($i=1; $i <= $cantidad; $i++) { 
		$idsuma = (int)$id[0]->item_ple + $i;
		$iditemple = str_pad($idsuma, 7, "0", STR_PAD_LEFT);
		$prefijo = 'IM';
		$iditemplearr[] = $prefijo.$iditemple;
	}
	return $iditemplearr;	
  }  

  public function combo_categoria_activo_fijo($id_categoria='')
  {
	$combo_categorias_activos_fijos = WEBCategoriaActivoFijo::all()->pluck('nombre','id');
	if($id_categoria!=''){
		$categoria_sel = WEBCategoriaActivoFijo::find($id_categoria);
		$combo_categorias_activos_fijos = array($categoria_sel->id => $categoria_sel->nombre) + $combo_categorias_activos_fijos->toArray();
	}
	return $combo_categorias_activos_fijos;
  }
  
  public function combo_producto($id_producto='') 
  {
	$productos = ALMProducto::all()->pluck('NOM_PRODUCTO','COD_PRODUCTO');
	if($id_producto!=''){
		$producto_sel = ALMProducto::find($id_producto);
		$productos = array($producto_sel->COD_PRODUCTO => $producto_sel->NOM_PRODUCTO) + $productos->toArray();
	}
	return $productos;
  }

  public function combo_obra($id_obra='') 
  {
	$empresa_id = Session::get('empresas_meta')->COD_EMPR;
	//$centro_id = Session::get('centros')->COD_CENTRO;

	$obras = WEBActivoFijo::where('modalidad_adquisicion','=','OBRA')
							->where('cod_empresa','=',$empresa_id)
							//->where('cod_centro','=',$centro_id)
							->pluck('nombre','id');
	if($id_obra!=''){
		$obra_sel = WEBActivoFijo::find($id_obra);
		$obras = array($obra_sel->id => $obra_sel->nombre) + $obras->toArray();
	}
	return $obras;
  }

  public function combo_activo_fijo() 
  {
	$empresa_id = Session::get('empresas_meta')->COD_EMPR;
	$activosfijos =  DB::table('WEB.activosfijos')
						  ->where('WEB.activosfijos.estado_depreciacion','<>','DEPRECIADO')
						  ->where('WEB.activosfijos.estado','<>','BAJA')
						  ->where('WEB.activosfijos.tipo_activo','<>','COMPUESTO')
						  ->where('WEB.activosfijos.cod_empresa','=',$empresa_id)
                          ->select('id', DB::raw("CONCAT(item_ple, ' - ', nombre) AS nom_activo"))
                          ->get()
						  ->pluck('nom_activo','id')
						  ->toArray();
	return $activosfijos;
  }


	public function prefijomaestra() {
		$prefijo = '1CIX';
	  	return $prefijo;
	}

	public function generar_codigo($basedatos,$cantidad) {

	  		// maximo valor de la tabla referente
			$tabla = DB::table($basedatos)
            ->select(DB::raw('max(codigo) as codigo'))
            ->get();

            //conversion a string y suma uno para el siguiente id
            $idsuma = (int)$tabla[0]->codigo + 1;

		  	//concatenar con ceros
		  	$correlativocompleta = str_pad($idsuma, $cantidad, "0", STR_PAD_LEFT); 

	  		return $correlativocompleta;

	}

	public function generar_codigo_transferencia($codigo,$cantidad) {

            //conversion a string y suma uno para el siguiente id
            $idsuma = (int)$codigo + 1;
		  	//concatenar con ceros
		  	$correlativocompleta = str_pad($idsuma, $cantidad, "0", STR_PAD_LEFT); 

	  		return $correlativocompleta;

	}


	public function color_empresa($empresa_id) {

		$color 		= '';
		if($empresa_id == 'IACHEM0000010394'){
			$color 		= 'color-iin';
		}

		if($empresa_id == 'IACHEM0000007086'){
			$color 		= 'color-ico';
		}
		if($empresa_id == 'EMP0000000000007'){
			$color 		= 'color-itr';
		}

		if($empresa_id == 'IACHEM0000001339'){
			$color 		= 'color-ich';
		}

		if($empresa_id == 'EMP0000000000001'){
			$color 		= 'color-iaa';
		}
		return $color;
	}


	public function getUrl($idopcion,$accion) {

	  	//decodificar variable
	  	$decidopcion = Hashids::decode($idopcion);
	  	//ver si viene con letras la cadena codificada
	  	if(count($decidopcion)==0){ 
	  		return Redirect::back()->withInput()->with('errorurl', 'Indices de la url con errores'); 
	  	}

	  	//concatenar con ceros
	  	$idopcioncompleta = str_pad($decidopcion[0], 8, "0", STR_PAD_LEFT); 
	  	//concatenar prefijo

	  	// hemos hecho eso porque ahora el prefijo va hacer fijo en todas las empresas que 1CIX
		//$prefijo = Local::where('activo', '=', 1)->first();
		//$idopcioncompleta = $prefijo->prefijoLocal.$idopcioncompleta;
		$idopcioncompleta = '1CIX'.$idopcioncompleta;

	  	// ver si la opcion existe
	  	$opcion =  WEBRolOpcion::where('opcion_id', '=',$idopcioncompleta)
	  			   ->where('rol_id', '=',Session::get('usuario_meta')->rol_id)
	  			   ->where($accion, '=',1)
	  			   ->first();

	  	if(count($opcion)<=0){
	  		return Redirect::back()->withInput()->with('errorurl', 'No tiene autorización para '.$accion.' aquí');
	  	}
	  	return 'true';

	 }


	public function getCreateIdMaestra($tabla) {

  		$id="";

  		// maximo valor de la tabla referente
		$id = DB::table($tabla)
        ->select(DB::raw('max(SUBSTRING(id,5,8)) as id'))
        ->get();

        //conversion a string y suma uno para el siguiente id
        $idsuma = (int)$id[0]->id + 1;

	  	//concatenar con ceros
	  	$idopcioncompleta = str_pad($idsuma, 8, "0", STR_PAD_LEFT);

	  	//concatenar prefijo
		$prefijo = $this->prefijomaestra();

		$idopcioncompleta = $prefijo.$idopcioncompleta;

  		return $idopcioncompleta;	

	}

	public function decodificarmaestra($id) {

	  	//decodificar variable
	  	$iddeco = Hashids::decode($id);
	  	//ver si viene con letras la cadena codificada
	  	if(count($iddeco)==0){ 
	  		return ''; 
	  	}
	  	//concatenar con ceros
	  	$idopcioncompleta = str_pad($iddeco[0], 8, "0", STR_PAD_LEFT); 
	  	//concatenar prefijo

		//$prefijo = Local::where('activo', '=', 1)->first();

		// apunta ahi en tu cuaderno porque esto solo va a permitir decodifcar  cuando sea el contrato del locl en donde estas del resto no 
		//¿cuando sea el contrato del local?
		$prefijo = $this->prefijomaestra();
		$idopcioncompleta = $prefijo.$idopcioncompleta;
	  	return $idopcioncompleta;

	}


	public function tiene_perfil($empresa_id,$centro_id,$usuario_id) {

		$perfiles 		=   WEBUserEmpresaCentro::where('empresa_id','=',$empresa_id)
							->where('centro_id','=',$centro_id)
							->where('usuario_id','=',$usuario_id)
							->where('activo','=','1')
							->first();

		if(count($perfiles)>0){
			return true;
		}else{
			return false;
		}	

	}


}

