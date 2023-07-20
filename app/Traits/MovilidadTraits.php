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
	
    private function movilidad_anular_asiento($asiento_id,$usuario_id,$fecha)
    {

        WEBAsiento::where('COD_ASIENTO','=',$asiento_id)
                    ->update([  'COD_CATEGORIA_ESTADO_ASIENTO' => 'IACHTE0000000024',
                                'TXT_CATEGORIA_ESTADO_ASIENTO' => 'EXTORNADO',
                                'COD_USUARIO_MODIF_AUD' => $usuario_id,
                                'FEC_USUARIO_MODIF_AUD' => $fecha,
                                'COD_ESTADO' => '0'
                             ]);

        WEBAsientoMovimiento::where('COD_ASIENTO','=',$asiento_id)
                    ->update([  'COD_USUARIO_MODIF_AUD' => $usuario_id,
                                'FEC_USUARIO_MODIF_AUD' => $fecha,
                                'COD_ESTADO' => '0'
                             ]);



        return true;

    }


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


	private function movilidad_guardar_asociacion($campo1, $campo2, $campo3, $campo4, $campo5, $campo6, $campo7, $campo8, $campo9, $campo10, $campo11, $campo12, $campo13, $campo14)
	{


           $stmt = DB::connection('sqlsrv')->getPdo()->prepare('SET NOCOUNT ON;EXEC CMP.REFERENCIA_ASOC_IUD ?,?,?,?,?,?,?,?,?,?,?,?,?,?');
            $stmt->bindParam(1, $campo1 ,PDO::PARAM_STR);                                   //@IND_TIPO_OPERACION='I',
            $stmt->bindParam(2, $campo2  ,PDO::PARAM_STR);                         //@COD_TABLA='IILMNC0000000495',
            $stmt->bindParam(3, $campo3 ,PDO::PARAM_STR);                 //@COD_TABLA_ASOC='IILMFC0000005728',
            $stmt->bindParam(4, $campo4 ,PDO::PARAM_STR);                                //@TXT_TABLA='CMP.DOCUMENTO_CTBLE', 
            $stmt->bindParam(5, $campo5 ,PDO::PARAM_STR);                                //@TXT_TABLA_ASOC='CMP.DOCUMENTO_CTBLE', 
            $stmt->bindParam(6, $campo6  ,PDO::PARAM_STR);                               //@TXT_GLOSA='NOTA DE CREDITO F005-00000420 / ',
            $stmt->bindParam(7, $campo7  ,PDO::PARAM_STR);                                   //@TXT_TIPO_REFERENCIA='',
            $stmt->bindParam(8, $campo8  ,PDO::PARAM_STR);                                   //@TXT_REFERENCIA='',
            $stmt->bindParam(9, $campo9  ,PDO::PARAM_STR);                              //@COD_ESTADO=1,
            $stmt->bindParam(10, $campo10  ,PDO::PARAM_STR);                   //@COD_USUARIO_REGISTRO='PHORNALL',
            $stmt->bindParam(11, $campo11  ,PDO::PARAM_STR);                                  //@TXT_DESCRIPCION='',
            $stmt->bindParam(12, $campo12  ,PDO::PARAM_STR);                             //@CAN_AUX1=0,
            $stmt->bindParam(13, $campo13  ,PDO::PARAM_STR);                             //@CAN_AUX2=0,
            $stmt->bindParam(14, $campo14  ,PDO::PARAM_STR);                             //@CAN_AUX3=0,
            $stmt->execute();

           return "guardado";

	}

	private function movilidad_array_asociacion_proviciones($descripcion, $periodo_id, $empresa_id,$referencia)
	{

        $array_asociacion          =    CMPReferecenciaAsoc::where('TXT_TABLA','=',$empresa_id)
                                        ->where('COD_TABLA','=',$periodo_id)
                                        ->where('TXT_DESCRIPCION','=',$descripcion)
                                        ->Referencia($referencia)
                                        ->where('COD_ESTADO','=',1)
                                        ->whereIn('TXT_TABLA_ASOC', ['IACHTE0000000032'])
                                        ->pluck('COD_TABLA_ASOC')
                                        ->toArray();
        return  $array_asociacion;          

	}


	private function movilidad_extornar_asociacion_proviciones($descripcion, $periodo_id, $empresa_id,$asoci_id)
	{

        $aso_refe          		=    	CMPReferecenciaAsoc::where('TXT_TABLA','=',$empresa_id)
                                        ->where('COD_TABLA','=',$periodo_id)
                                        ->where('TXT_DESCRIPCION','=',$descripcion)
                                        ->where('COD_TABLA_ASOC','=',$asoci_id)
                                        ->first();



        if(count($aso_refe)>0){

        	$aso_refe->TXT_TABLA_ASOC = 'IACHTE0000000024';
        	$aso_refe->COD_ESTADO = 0;
        	$aso_refe->save();
        }

        return  $aso_refe;          

	}

    private function movilidad_existe_asociacion_proviciones($descripcion, $periodo_id, $empresa_id,$asoci_id)
    {

        $aso_refe               =       CMPReferecenciaAsoc::where('TXT_TABLA','=',$empresa_id)
                                        ->where('COD_TABLA','=',$periodo_id)
                                        ->where('TXT_DESCRIPCION','=',$descripcion)
                                        ->where('COD_TABLA_ASOC','=',$asoci_id)
                                        ->first();


        return  $aso_refe;          

    }


    public function movilidad_monto_total_asiento($listamovilidad,$array_asoc){

        $monto      =  0.0000;

        foreach($listamovilidad as $index => $item){
            if(in_array($item['COD_DOCUMENTO_CTBLE_MOVILIDAD'], $array_asoc)){
                $monto      =  $monto + $item['IMPORTE'];
            }
        }
    
        return number_format($monto, 4, '.', '');

    }



    public function movilidad_array_asiento_modelo($data_archivo){

        $array_detalle_asiento      =   array();

        if($data_archivo=='agregarmobilidadgeneral'){

            $array_nuevo_asiento        =   array(
                "cuenta_nrocuenta"          => '659303',
                "d_h"                       => 'D',
                "linea"                     => '1',
            );
            array_push($array_detalle_asiento,$array_nuevo_asiento);


            $array_nuevo_asiento        =   array(
                "cuenta_nrocuenta"          => '421203',
                "d_h"                       => 'H',
                "linea"                     => '2',
            );
            array_push($array_detalle_asiento,$array_nuevo_asiento);


            $array_nuevo_asiento        =   array(
                "cuenta_nrocuenta"          => '941473',
                "d_h"                       => 'D',
                "linea"                     => '3',
            );
            array_push($array_detalle_asiento,$array_nuevo_asiento);


            $array_nuevo_asiento        =   array(
                "cuenta_nrocuenta"          => '791101',
                "d_h"                       => 'H',
                "linea"                     => '4',
            );
            array_push($array_detalle_asiento,$array_nuevo_asiento);



        }else{

            $array_nuevo_asiento        =   array(
                "cuenta_nrocuenta"          => '659307',
                "d_h"                       => 'D',
                "linea"                     => '1',
            );
            array_push($array_detalle_asiento,$array_nuevo_asiento);


            $array_nuevo_asiento        =   array(
                "cuenta_nrocuenta"          => '421203',
                "d_h"                       => 'H',
                "linea"                     => '2',
            );
            array_push($array_detalle_asiento,$array_nuevo_asiento);


            $array_nuevo_asiento        =   array(
                "cuenta_nrocuenta"          => '941477',
                "d_h"                       => 'D',
                "linea"                     => '3',
            );
            array_push($array_detalle_asiento,$array_nuevo_asiento);


            $array_nuevo_asiento        =   array(
                "cuenta_nrocuenta"          => '791101',
                "d_h"                       => 'H',
                "linea"                     => '4',
            );
            array_push($array_detalle_asiento,$array_nuevo_asiento);
        }

        return $array_detalle_asiento;

    }

    public function movilidad_cabecera_asiento($periodo,$empresa_id,$monto_total,$glosa,$moneda_id,$moneda,$tipo_cambio,$tipo_referencia){

        $array_detalle_asiento      =   array();

        $array_nuevo_asiento        =   array();
        $array_nuevo_asiento        =   array(
            "periodo_id"                => $periodo->COD_PERIODO,
            "nombre_periodo"            => $periodo->TXT_NOMBRE,
            "fecha"                     => substr($periodo->FEC_FIN, 0, 10),
            "empresa_id"                => $empresa_id,
            "glosa"                     => $glosa,
            "tipo_referencia"           => $tipo_referencia,
            "tipo_cambio"               => $tipo_cambio->CAN_COMPRA_SBS,
            "moneda_id"                 => $moneda_id,
            "moneda"                    => $moneda,
            "total_debe"                => $monto_total,
            "total_haber"               => $monto_total,
        );

        array_push($array_detalle_asiento,$array_nuevo_asiento);
        return $array_detalle_asiento;

    }



    public function movilidad_detalle_asiento($array_asiento_modelo,$periodo,$empresa_id,$moneda_id,$moneda,$monto_total,$tipo_cambio){

        $array_detalle_asiento      =   array();

        foreach($array_asiento_modelo as $index => $item){

            $cuentacontable             =   WEBCuentaContable::where('empresa_id','=',$empresa_id)
                                            ->where('anio','=',$periodo->COD_ANIO)
                                            ->where('nro_cuenta','=',$item['cuenta_nrocuenta'])
                                            ->where('activo','=',1)
                                            ->first();

            if($item['d_h'] == 'D'){
                $monto_total_debe = $monto_total;
                $monto_total_haber = 0;

                $monto_total_dolar_debe = $monto_total*$tipo_cambio->CAN_COMPRA_SBS;
                $monto_total_dola_haber = 0;

            }else{
                $monto_total_debe = 0;
                $monto_total_haber = $monto_total;

                $monto_total_dolar_debe = 0;
                $monto_total_dola_haber = $monto_total*$tipo_cambio->CAN_COMPRA_SBS;

            }

            $array_nuevo_asiento        =   array();
            $array_nuevo_asiento        =   array(
                "linea"                     => $item['linea'],
                "cuenta_id"                 => $cuentacontable->id,
                "cuenta_nrocuenta"          => $cuentacontable->nro_cuenta,
                "glosa"                     => $cuentacontable->nombre,

                "fecha"                     => substr($periodo->FEC_FIN, 0, 10),
                "empresa_id"                => $empresa_id,
                "moneda_id"                 => $moneda_id,
                "moneda"                    => $moneda,
                
                "total_debe"                => $monto_total_debe,
                "total_haber"               => $monto_total_haber,
                "total_debe_dolar"          => $monto_total_dolar_debe,
                "total_haber_dolar"         => $monto_total_dola_haber


            );

            array_push($array_detalle_asiento,$array_nuevo_asiento);


        }
                   
        return $array_detalle_asiento;

    }


}