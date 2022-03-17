<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBViewMigrarCompras extends Model
{
    protected $table = 'WEB.viewmigrarcompras';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';


    public function scopeNroSerie($query,$serie){
        if(trim($serie) != ''){
            $query->where('NRO_SERIE', 'like', '%'.$serie.'%');
        }
    }

    public function scopeNroDocumento($query,$documento){
        if(trim($documento) != ''){
            $query->where('NRO_DOC', 'like', '%'.$documento.'%');
        }
    }





}
