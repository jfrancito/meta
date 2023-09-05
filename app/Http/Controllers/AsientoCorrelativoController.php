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
use App\Modelos\CMPTipoCambio;


use App\Traits\GeneralesTraits;
use App\Traits\AsientoModeloTraits;
use App\Traits\PlanContableTraits;
use App\Traits\ArchivoTraits;
use App\Traits\ReporteTraits;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use ZipArchive;
use Maatwebsite\Excel\Facades\Excel;
use GuzzleHttp\Client;
use PDO;

class AsientoCorrelativoController extends Controller
{

	use GeneralesTraits;

	public function actionActualizarCorrelativo()
	{

        $stmt2 						= 		DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC WEB.COMPLETAR_CORRELATIVO');
        $stmt2->execute();	
        dd("completado");


	}


}
