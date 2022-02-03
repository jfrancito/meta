<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBProductoEmpresa extends Model
{
    protected $table = 'WEB.productoempresas';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    
}
