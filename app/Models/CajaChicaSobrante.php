<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaChicaSobrante extends Model
{
    use HasFactory;

    protected $table = 'caja_chica_sobrantes';

    protected $fillable = [
        'fecha',
        'monto',
        'estado',
    ];

    // Establece el estado automáticamente al guardar
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->estado = $model->determinarEstado();
        });

        static::updating(function ($model) {
            $model->estado = $model->determinarEstado();
        });
    }

    public function determinarEstado()
    {
        if ($this->monto > 0) return 'sobrante';
        if ($this->monto < 0) return 'deuda';
        return 'nulo';
    }
}
