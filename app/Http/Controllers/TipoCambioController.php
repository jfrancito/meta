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


class TipoCambioController extends Controller
{

	use GeneralesTraits;


	public function actionActualizarTipoCambio()
	{

    	if($this->gr_is_connected()){


    		$anio  					=   $this->anio;
    		$mes  					=   $this->mes;

			$listatipocambio 		=   CMPTipoCambio::whereRaw('YEAR(FEC_CAMBIO) = ?', [$anio])
										->whereRaw('MONTH(FEC_CAMBIO) <= ?', [$mes])
										->whereRaw('FEC_CAMBIO < ?', [date('Ymd')])
										->take(7)
										->orderBy('FEC_CAMBIO', 'desc')
										->get();


			foreach($listatipocambio as $index => $item){

				//dd($item->FEC_CAMBIO);
				$fecha_normal 		=	date_format(date_create($item->FEC_CAMBIO), 'Ymd');
				$fecha_normal_dos 	=	date_format(date_create($item->FEC_CAMBIO), 'Y-m-d');

				$tcnormal 			=   CMPTipoCambio::where('FEC_CAMBIO','=',$fecha_normal)
										->first();

				$fechamdos 			= 	$fecha_normal_dos;
				$nuevafechad 		= 	strtotime ('+1 day' , strtotime($fechamdos));
				$nuevafechad		= 	date('Y-m-d' , $nuevafechad);

				$fechamenosuno 		=	date_format(date_create($nuevafechad), 'Y-m-d');
				$fechamenosuno 		= 	strtotime($fechamenosuno);
				$dia_f 				=  	date("d", $fechamenosuno);
				$mes_f 				=  	date("m", $fechamenosuno);
				$anio_f 			=  	date("Y", $fechamenosuno);

				$ff 				=	$anio_f.'-'.$mes_f.'-'.$dia_f;


				if(count($tcnormal)>0){


					$client = new Client([
					    // Base URI is used with relative requests
					    'base_uri' => 'https://api.apis.net.pe',
					    // You can set any number of default request options.
					    'timeout'  => 2.0,
					]);

					$response 		= 	$client->request('GET', 'v1/tipo-cambio-sunat?fecha='.$ff);
					$arraytc 		= 	json_decode($response->getBody()->getContents(),true);


					$fechamenosuno 	=	date_format(date_create($fecha_normal), 'Y-m-d');
					$fechamenosuno 	= 	strtotime($fechamenosuno);
					$dia 						=  	date("d", $fechamenosuno);
					$mes 						=  	date("m", $fechamenosuno);
					$anio 						=  	date("Y", $fechamenosuno);

					DB::connection('sqlsrv')->table('CMP.TIPO_CAMBIO')->whereRaw('day(FEC_CAMBIO) = ?', [$dia])
											->whereRaw('MONTH(FEC_CAMBIO) = ?', [$mes])
											->whereRaw('YEAR(FEC_CAMBIO) = ?', [$anio])
            		->update(['CAN_COMPRA_SBS' => $arraytc['compra'],'CAN_VENTA_SBS' => $arraytc['venta']]);


				}
			} 

    	}
    	else{
   		 	$arraytc 		=	[];	
    	}

	}



}
