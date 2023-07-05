<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBViewMigrarLiquidacionCompra extends Model
{
    protected $table = 'WEB.viewmigrarliquidacioncompras';
    public $timestamps=false;
    protected $primaryKey = 'COD_DOC_FORMAL';
    public $incrementing = false;
    public $keyType = 'string';
}
