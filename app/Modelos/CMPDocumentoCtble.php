<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class CMPDocumentoCtble extends Model
{
    protected $table = 'CMP.DOCUMENTO_CTBLE';
    public $timestamps=false;

    protected $primaryKey = 'COD_DOCUMENTO_CTBLE';
	public $incrementing = false;
    public $keyType = 'string';
    
}
