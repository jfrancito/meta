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

use App\Modelos\WEBMovimientoEfectivo;

use ZipArchive;
use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait PagoCobroEfectivo
{
	

    private function pce_lista_movimiento($periodo_id,$caja_id)
    {

        $listamovimientos       =   WEBMovimientoEfectivo::where('COD_PERIODO','=',$periodo_id)
       	 							->where('COD_CAJA_BANCO','=',$caja_id)
                                    ->orderby('FEC_MOVIMIENTO_CAJABANCO','ASC')
                                    ->orderby('COD_OPERACION','ASC')
                                    ->get();
        return  $listamovimientos;          

    }


}