<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EliminarPagoCliente extends Model
{
    use HasFactory;
    protected $table = 'tb_caja_chica'; // Nombre de tu tabla
    protected $primaryKey = 'idCaja'; // Clave primaria
    public $timestamps = false;

    protected $fillable = [
        'idCaja',
        'estadoPago',
    ];

}