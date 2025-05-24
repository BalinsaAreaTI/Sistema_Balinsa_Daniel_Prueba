<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActaConformidad extends Model
{
    use HasFactory;

    protected $table = 'acta_conformidad';

    protected $fillable =[
        'idActa',
        'tipo_acta_conformidad',
        'tipo_conformidad',
        'ruc_acta',
        'razon_social_acta',
        'direccion_acta',
        'fecha_acta',
        'fecha_registro',
        'fecha_conformidad',
        'fecha_final_acta',
        'lugar_atencion',
        'trabajo_conformidad',
        'registro_user',
        'tecnico'
    ];
}
