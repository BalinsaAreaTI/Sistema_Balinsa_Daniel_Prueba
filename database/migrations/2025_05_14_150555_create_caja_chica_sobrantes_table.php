<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_chica_sobrantes', function (Blueprint $table) {
            $table->id();
            $table->date('fecha'); // Fecha del corte o cierre
            $table->decimal('monto', 10, 2); // Puede ser negativo, cero o positivo
            $table->string('estado'); // sobrante, nulo o deuda
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_chica_sobrantes');
    }
};
