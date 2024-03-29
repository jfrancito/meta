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
	public $mes;
	public $dia;

	public $prefijomaestro;
	public $fechaActual;
	public $array_empresas;
	public $anio_inicio;

	public function __construct()
	{
	    $this->funciones = new Funcion();
		$fecha = new DateTime();
		$fecha->modify('first day of this month');
		$anio = date("Y");
		$mes = date("n");
		$dia = date("d");

		$this->fechaactual 				= date('d-m-Y H:i:s');
		$this->inicio 					= date_format(date_create($fecha->format('Y-m-d')), 'd-m-Y');
		$this->fin 						= date_format(date_create(date('Y-m-d')), 'd-m-Y');
		$this->anio 					= $anio;
		$this->mes 						= $mes;
		$this->dia 						= $dia;		
		$this->prefijomaestro			= $this->funciones->prefijomaestra();

		//$this->array_empresas			= ['EMP0000000000007','IACHEM0000007086'];
		$this->array_empresas			= ['IACHEM0000007086','IACHEM0000010394'];
		
		$this->anio_inicio				= 2022;


	}




}
