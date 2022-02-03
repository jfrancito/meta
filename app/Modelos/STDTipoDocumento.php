<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class ALMCentro extends Model
{
    protected $table = 'STD.TIPO_DOCUMENTO';
    public $timestamps=false;
    protected $primaryKey = 'COD_TIPO_DOCUMENTO';
	public $incrementing = false;
	public $keyType = 'string';

}





