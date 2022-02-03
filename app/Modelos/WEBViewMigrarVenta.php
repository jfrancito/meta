<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBViewMigrarVenta extends Model
{
    protected $table = 'WEB.viewmigrarventas';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

}
