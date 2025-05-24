<?php

namespace App\Http\Controllers;

use App\Http\Requests\hojaservicioRequest;
use App\Models\Cuadropendientes;
use App\Models\Servicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PendientesController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->input('fecha', Carbon::now()->setTimezone('America/Lima')->toDateString()); 

        $tb_pendientes = DB::table('tb_pendientes')
            ->join('clientes', 'clientes.id', '=', 'tb_pendientes.cliente_id')
            ->whereDate('tb_pendientes.fecha', $fecha)
            ->orderBy('tb_pendientes.created_at', 'desc')
            ->select(
                'tb_pendientes.id',
                'tb_pendientes.cliente_id',
                'tb_pendientes.cantidad',
                'tb_pendientes.descripcion',
                'tb_pendientes.fecha',
                'tb_pendientes.hora',
                'tb_pendientes.lugar',
                'tb_pendientes.contacto',
                'tb_pendientes.nro_contacto',
                'tb_pendientes.usuario',
                'clientes.razonSocial AS razon_social'
            )
            ->get();

        return Inertia::render('CuadroPendientes/Index', compact('tb_pendientes', 'fecha'));
    }  

    public function store(hojaservicioRequest $request){
        $usuario = Auth::user()->name;
        $data=$request->validated();
        $data['usuario'] = $usuario;
        $data['created_at'] = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        $data['updated_at'] = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        Cuadropendientes::create($data);
        return redirect()->back();
    }

    public function obtenerDatosdeClientesparaPendientes(){

        $clientesDatosPendientes = DB::table('clientes')
                    ->join('tbprovincias', 'tbprovincias.id', '=', 'clientes.tbprovincia_id')
                    ->select(
                        'clientes.id',
                        'clientes.numeroDocumento',
                        'clientes.razonSocial',
                        'clientes.direccion',
                        'clientes.distrito',
                        'clientes.provincia',
                        'clientes.departamento',
                        'clientes.estado',
                        'clientes.ctg',
                        'clientes.asesor',
                        'clientes.fechafactura',
                        'clientes.codigofactura',
                        'clientes.cli_direccion2',
                        'clientes.cli_observacion',
                        'tbprovincias.prov_nombre AS prov_nombre'
                    )
                    ->get();

        return response()->json($clientesDatosPendientes);
    }

    public function update(hojaservicioRequest $request , $id){
        $data=$request->validated();
        $cuadropendientes=Cuadropendientes::find($id);
        $cuadropendientes->update($data);
    }

    public function destroy($id)
    {
        $cuadropendientes = Cuadropendientes::find($id);
        $cuadropendientes->delete();
        return redirect()->back();
    }
}
