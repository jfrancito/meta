<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBViewMigrarVentaCabecera extends Model
{
    protected $table = 'WEB.viewmigrarventascabecera';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

}
