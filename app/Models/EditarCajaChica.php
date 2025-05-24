<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditarCajaChica extends Model
{
    use HasFactory;
    protected $table = 'tb_caja_chica'; // Nombre de tu tabla
    protected $primaryKey = 'idCaja'; // Clave primaria
    public $timestamps = false;

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

}
