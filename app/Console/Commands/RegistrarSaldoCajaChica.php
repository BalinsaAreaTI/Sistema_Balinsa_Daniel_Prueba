<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CajaChica;
use Carbon\Carbon;

class RegistrarSaldoCajaChica extends Command
{
    protected $signature = 'caja_chica:registrar_saldo';
    protected $description = 'Registra el saldo de caja chica al final del día para el siguiente día hábil';

    public function handle()
    {
        $hoy = Carbon::now()->toDateString(); // Fecha de hoy
        $diaSemana = Carbon::now()->dayOfWeek; // 0 = Domingo, 6 = Sábado

        // Determinar el próximo día hábil
        if ($diaSemana == 6) { // Si es sábado
            $proximoDiaHabil = Carbon::now()->addDays(2)->toDateString(); // Lunes
        } elseif ($diaSemana == 0) { // Si es domingo (por seguridad)
            $proximoDiaHabil = Carbon::now()->addDay()->toDateString(); // Lunes
        } else {
            $proximoDiaHabil = Carbon::tomorrow()->toDateString(); // Día siguiente normal
        }

        // Calcular saldo del día
        $totalIngreso = CajaChica::whereDate('fechaCaja', $hoy)->sum('ingresoCaja');
        $totalEgreso = CajaChica::whereDate('fechaCaja', $hoy)->sum('salidaCaja');
        $saldo = $totalIngreso - $totalEgreso;

        if ($saldo > 0) { // Solo registrar si el saldo es positivo
            CajaChica::create([
                'fechaCaja' => $proximoDiaHabil,
                'horaCaja' => '00:00:00',
                'acreedorCaja' => 'Sistema',
                'servicioCaja' => 'Saldo anterior',
                'descripcionCaja' => "SALDO DEL DIA" . Carbon::now()->format('d/m/Y'),
                'razonSocialCaja' => '-',
                'sustentoCaja' => '-',
                'nTransaccionCaja' => '-',
                'sustentoGastoCaja' => '-',
                'ingresoCaja' => $saldo,
                'salidaCaja' => 0,
                'linkCaja' => null,
            ]);

            $this->info("Saldo de $saldo registrado para el día $proximoDiaHabil.");
        } else {
            $this->info("No se registró saldo porque es 0 o negativo ($saldo).");
        }
    }
}
