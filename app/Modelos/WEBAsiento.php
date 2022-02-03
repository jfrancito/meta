<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBAsiento extends Model
{
    protected $table = 'WEB.asientos';
    public $timestamps=false;
    protected $primaryKey = 'COD_ASIENTO';
	public $incrementing = false;
	public $keyType = 'string';


    public function periodo()
    {
        return $this->belongsTo('App\Modelos\CONPeriodo', 'COD_PERIODO', 'COD_PERIODO');
    }

}
