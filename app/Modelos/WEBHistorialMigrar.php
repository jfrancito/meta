<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBHistorialMigrar extends Model
{
    protected $table = 'WEB.historialmigrar';
    public $timestamps=false;

    protected $primaryKey = 'COD_REFERENCIA';
    public $incrementing = false;
    public $keyType = 'string';

   
}
