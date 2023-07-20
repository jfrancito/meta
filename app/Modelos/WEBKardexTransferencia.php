<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class WEBKardexTransferencia extends Model
{
    protected $table = 'WEB.kardextransferencias';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

}