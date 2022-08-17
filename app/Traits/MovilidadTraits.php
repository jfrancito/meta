<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;


use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use App\Modelos\CONPeriodo;
use App\Modelos\WEBViewMigrarVenta;
use App\Modelos\CMPDocumentoCtble;
use App\Modelos\WEBHistorialMigrar;
use App\Modelos\CMPDetalleProducto;
use App\Modelos\WEBProductoEmpresa;
use App\Modelos\WEBAsientoMovimiento;
use App\Modelos\CMPReferecenciaAsoc;
use App\Modelos\WEBAsientoModelo;
use App\Modelos\CMPCategoria;

use App\Modelos\WEBAsientoModeloDetalle;
use App\Modelos\WEBAsientoModeloReferencia;
use App\Modelos\WEBAsiento;
use App\Modelos\STDEmpresa;



use ZipArchive;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait MovilidadTraits
{
	
	private function movilidad_lista_movilidad($tipo_funcion, $empresa_id, $periodo_id)
	{


        $stmt 		= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.listaprovisiones 
							@tipo_funcion = ?,
							@empresa_id = ?,
							@periodo_id = ?');

        $stmt->bindParam(1, $tipo_funcion ,PDO::PARAM_STR);                   
        $stmt->bindParam(2, $empresa_id  ,PDO::PARAM_STR);
        $stmt->bindParam(3, $periodo_id  ,PDO::PARAM_STR);
        $stmt->execute();


		return $stmt;

	}

}