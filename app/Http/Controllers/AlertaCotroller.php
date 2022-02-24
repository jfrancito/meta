<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\User,App\Modelos\WEBGrupoopcion,App\Modelos\WEBRol,App\Modelos\WEBRolOpcion,App\Modelos\WEBOpcion,App\Modelos\WEBListaPersonal,App\Modelos\WEBPedido,App\Modelos\WEBDetallePedido;
use App\Modelos\ALMCentro,App\Modelos\STDEmpresa,App\Modelos\WEBUserEmpresaCentro;
use View;
use Session;
use Hashids;


use App\Traits\MigrarVentaTraits;
use App\Traits\AlertaTraits;

class AlertaCotroller extends Controller
{

	use MigrarVentaTraits;
	use AlertaTraits;

	public function actionAjaxModalDetalleDocumentoSinEnviarSunat(Request $request)
	{


		$empresa_id  	 				= 	$request['empresa_id'];
		$empresa 						= 	STDEmpresa::where('COD_EMPR','=',$empresa_id)->first();

		$lista_documento_sin_enviar 	= 	$this->al_lista_documentos_sin_enviar_detallado($empresa_id);



		return View::make('alerta.modal.ajax.mlistadetalledocumentosinenviarsunat',
						 [
						 	'empresa' => $empresa,
						 	'lista_documento_sin_enviar' => $lista_documento_sin_enviar,
						 ]);
	}


}
