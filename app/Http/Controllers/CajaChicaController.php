<?php

namespace App\Http\Controllers;

use App\Models\CajaChica;
use App\Models\EditarCajaChica;
use App\Models\EliminarPagoCliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CajaChicaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('CajaChica/Index');
    }

    public function registrarDatosCajaChica (Request $request){
        $fechaAgregarPagoCaja = $request->input('fechaAgregarPagoCaja');
        $horaAgregarPagoCaja = $request->input('horaAgregarPagoCaja');
        $acreedorCaja = $request->input('acreedorCaja');
        $servicioCaja = $request->input('servicioCaja');
        $descripcionCaja = $request->input('descripcionCaja');
        $razonSocialCaja = $request->input('razonSocialCaja');
        $sustentoCaja = $request->input('sustentoCaja');
        $nTransaccionCaja = $request->input('nTransaccionCaja');
        $sustentoGastoCaja = $request->input('sustentoGastoCaja');
        $ingresoCaja = $request->input('ingresoCaja');
        $salidaCaja = $request->input('salidaCaja');
        $linkCaja = $request->input('linkCaja');

        $agregarDatosCajaChica = new CajaChica;
        $agregarDatosCajaChica->fechaCaja = $fechaAgregarPagoCaja;
        $agregarDatosCajaChica->horaCaja = $horaAgregarPagoCaja;
        $agregarDatosCajaChica->acreedorCaja = $acreedorCaja;
        $agregarDatosCajaChica->servicioCaja = $servicioCaja;
        $agregarDatosCajaChica->descripcionCaja = $descripcionCaja;
        $agregarDatosCajaChica->razonSocialCaja = $razonSocialCaja;
        $agregarDatosCajaChica->sustentoCaja = $sustentoCaja;
        $agregarDatosCajaChica->nTransaccionCaja = $nTransaccionCaja;
        $agregarDatosCajaChica->sustentoGastoCaja = $sustentoGastoCaja;
        $agregarDatosCajaChica->ingresoCaja = $ingresoCaja;
        $agregarDatosCajaChica->salidaCaja = $salidaCaja;
        $agregarDatosCajaChica->linkCaja = $linkCaja;
        $agregarDatosCajaChica->save();

        return response()->json(['success' => true], 200);
    }

    public function obtenerDatosCajaChica(Request $request) {
        $fechaDesde = $request->input('fechaDesde');
        $fechaHasta = $request->input('fechaHasta');
    
        $listarCajaChica = DB::table('tb_caja_chica')
            ->select(
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
            )
            ->when($fechaDesde, function ($query) use ($fechaDesde) {
                return $query->where('fechaCaja', '>=', $fechaDesde);
            })
            ->when($fechaHasta, function ($query) use ($fechaHasta) {
                return $query->where('fechaCaja', '<=', $fechaHasta);
            })
            ->where('estadoPago' , '1')
            ->orderBy('idCaja', 'DESC')
            ->get();
    
        return response()->json($listarCajaChica);
    }

    public function consulta_AgregarDetalleEgresoEditar(Request $request)
    {
        $idCaja = $request->input('idCaja');
        if (!$idCaja) {
            return response()->json(['error' => 'ID de caja no encontrado'], 400);
        }

        if (Auth::check()) {
            EditarCajaChica::where('idCaja', $idCaja)->update([
                'fechaCaja' => $request->input('fechaCaja'),
                'horaCaja' => $request->input('horaCaja'),
                'acreedorCaja' => $request->input('acreedorCaja'),
                'servicioCaja' => $request->input('servicioCaja'),
                'descripcionCaja' => $request->input('descripcionCaja'),
                'razonSocialCaja' => $request->input('razonSocialCaja'),
                'sustentoCaja' => $request->input('sustentoCaja'),
                'nTransaccionCaja' => $request->input('nTransaccionCaja'),
                'sustentoGastoCaja' => $request->input('sustentoGastoCaja'),
                'ingresoCaja' => $request->input('ingresoCaja'),
                'salidaCaja' => $request->input('salidaCaja'),
                'linkCaja' => $request->input('linkCaja'),
                'updated_at' => Carbon::now()->setTimezone('America/Lima')->toDateTimeString(),
            ]);

            return response()->json(['success' => true], 200);
        }

        return response()->json(['error' => 'Usuario no autenticado'], 401);
    }

    public function consulta_EliminarPago(Request $request)
    {
        $codigoPago = $request->input('codigoPago');

        if (Auth::check()) {
            $EliminarPagoCliente = new EliminarPagoCliente;
            $EliminarPagoCliente->where('idCaja', $codigoPago)
                ->update([
                    'estadoPago' => 0,
                ]);
            
            return response()->json(['success' => true], 200);
        }

        // Si el usuario no está autenticado, puedes devolver un error o redirigirlo
        return response()->json(['error' => 'Usuario no autenticado'], 401);
    }
}
