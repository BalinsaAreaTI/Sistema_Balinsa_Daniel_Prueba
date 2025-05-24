<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendientesTecnicos extends Model
{
    use HasFactory;

    protected $table = 'tb_pendientes_tecnicos';

    protected $fillable =[
        'cliente_id',
        'descripcion',
        'responsable',
        'hora_prevista',
        'hora_llegada',
        'hora_limite',
        'hora_termino',
        'usuario'
    ];
}
