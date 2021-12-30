<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class ALMCentro extends Model
{
    protected $table = 'ALM.CENTRO';
    public $timestamps=false;
    protected $primaryKey = 'COD_CENTRO';
	public $incrementing = false;
	public $keyType = 'string';

	public function userempresacentro()
    {
        return $this->hasMany('App\Modelos\WEBUserEmpresaCentro', 'centro_id', 'id');
    }


}



