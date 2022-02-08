<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBHistorialMigrar extends Model
{
    protected $table = 'WEB.historialmigrar';
    public $timestamps=false;

    protected $primaryKey = 'COD_REFERENCIA';
    public $incrementing = false;
    public $keyType = 'string';

   
    public function periodo()
    {
        return $this->belongsTo('App\Modelos\CONPeriodo', 'COD_PERIODO', 'COD_PERIODO');
    }

    public function documento_ctble()
    {
        return $this->belongsTo('App\Modelos\CMPDocumentoCtble', 'COD_REFERENCIA', 'COD_DOCUMENTO_CTBLE');
    }

}
