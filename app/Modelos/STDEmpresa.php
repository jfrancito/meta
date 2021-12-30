<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class STDEmpresa extends Model
{
    protected $table = 'STD.EMPRESA';
    public $timestamps=false;
    protected $primaryKey = 'COD_EMPR';
    public $incrementing = false;
    public $keyType = 'string';

    public function userempresacentro()
    {
        return $this->hasMany('App\Modelos\WEBUserEmpresaCentro', 'empresa_id', 'id');
    }



}
