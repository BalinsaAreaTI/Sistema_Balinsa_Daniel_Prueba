<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('acta_conformidad')) {
            Schema::create('acta_conformidad', function (Blueprint $table) {
                $table->id();
                $table->string('idActa');
                $table->string('tipo_conformidad');
                $table->string('ruc_acta')->nullable();
                $table->string('razon_social_acta')->nullable();
                $table->string('direccion_acta')->nullable();
                $table->date('fecha_acta')->nullable();
                $table->date('fecha_final_acta')->nullable();
                $table->string('lugar_atencion')->nullable();
                $table->string('trabajo_conformidad')->nullable();
                $table->string('tecnico')->nullable();
                $table->string('registro_user')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acta_conformidad');
    }
};
