<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBListaCVProductoKardex extends Model
{
    protected $table = 'WEB.listacvproductokardex';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

}
