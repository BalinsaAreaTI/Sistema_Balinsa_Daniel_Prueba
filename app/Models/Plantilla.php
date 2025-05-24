<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plantilla extends Model
{
    use HasFactory;
    protected $table = 'plantillas';

    protected $primaryKey = 'id';

    protected $fillable = ['descripcion', 'informacion'];

    public function cServicios()
    {
        return $this->hasMany(C_Servicio::class);
    }
}
