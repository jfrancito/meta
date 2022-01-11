<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Biblioteca\Funcion;

use DateTime;

class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public $funciones;
	public $inicio;
	public $fin;
	public $anio;
	public $prefijomaestro;
	public $fechaActual;

	public function __construct()
	{
	    $this->funciones = new Funcion();
		$fecha = new DateTime();
		$fecha->modify('first day of this month');
		$anio = date("Y");

		$this->fechaactual 				= date('d-m-Y H:i:s');
		$this->inicio 					= date_format(date_create($fecha->format('Y-m-d')), 'd-m-Y');
		$this->fin 						= date_format(date_create(date('Y-m-d')), 'd-m-Y');
		$this->anio 					= $anio;
		$this->prefijomaestro			= $this->funciones->prefijomaestra();

	}




}
