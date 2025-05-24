<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaChica extends Model
{
    use HasFactory;

    protected $table = 'tb_caja_chica';

    protected $fillable =[
        'idCaja',
        'fechaCaja',
        'horaCaja',
        'acreedorCaja',
        'servicioCaja',
        'descripcionCaja',
        'razonSocialCaja',
        'sustentoCaja',
        'nTransaccionCaja',
        'sustentoGastoCaja',
        'ingresoCaja',
        'salidaCaja',
        'linkCaja'
    ];

    public $timestamps = true;
}
