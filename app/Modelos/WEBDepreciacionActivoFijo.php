<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBDepreciacionActivoFijo extends Model
{
    protected $table = 'WEB.depreciacionesactivosfijos';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    public function activo()
    {
        return $this->belongsTo(WEBActivoFijo::class, 'id', 'id');
    }
}
