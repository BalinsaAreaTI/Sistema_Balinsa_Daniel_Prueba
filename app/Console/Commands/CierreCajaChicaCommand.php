<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CajaChicaSobrante;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CierreCajaChicaCommand extends Command
{
    protected $signature = 'caja-chica:cierre-automatico';
    protected $description = 'Realiza el cierre automático de caja chica y registra el sobrante';

    public function handle()
    {
        // Supongamos que obtienes el monto actual desde una tabla llamada "caja_chica_movimientos"
        $total = DB::table('caja_chica_movimientos')
            ->whereDate('created_at', Carbon::today())
            ->sum('monto');

        CajaChicaSobrante::create([
            'fecha' => Carbon::today(),
            'monto' => $total, // puede ser positivo, 0 o negativo
        ]);

        $this->info('Cierre de caja chica ejecutado correctamente a las ' . now());
    }
}
