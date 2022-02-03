<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBAsientoModeloReferencia extends Model
{
    protected $table = 'WEB.asientomodeloreferencias';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';


}
