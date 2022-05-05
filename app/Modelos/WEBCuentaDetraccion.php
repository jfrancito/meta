<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBCuentaDetraccion extends Model
{
    protected $table = 'WEB.cuentadetracciones';
    public $timestamps=false;

    protected $primaryKey = 'DOCUMENTO';
    public $incrementing = false;
    public $keyType = 'string';

}