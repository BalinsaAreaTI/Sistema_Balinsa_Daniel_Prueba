<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ActaConformidad;
use App\Http\Requests\ActaConformidadRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ActaConformidadController extends Controller
{
    public function index()
    {
        return inertia('ActasConformidad/Index');
    }

    public function obtenerListadeActaClientes(){

        $listaractas = DB::table('acta_conformidad')
                    ->select(
                        'id',
                        'idActa',
                        'tipo_acta_conformidad',
                        'tipo_conformidad',
                        'ruc_acta',
                        'razon_social_acta',
                        'direccion_acta',
                        'fecha_acta',
                        'fecha_final_acta',
                        'lugar_atencion',
                        'trabajo_conformidad',
                        'tecnico',
                        'registro_user',
                    )
                    ->orderBy('id', 'DESC')
                    ->get();

        return response()->json($listaractas);
    }

    public function create()
    {
        return inertia('ActasConformidad/Create');
    }

    public function obtenerDatosdeActaClientes(){

        $clientesactas = DB::table('clientes')
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
                        'clientes.tbprovincia_id',
                        'tbprovincias.prov_nombre'
                    )
                    ->get();

        return response()->json($clientesactas);
    }

    public function obtenerListadeActaClientesPdf(Request $request)
    {
        $id = $request->query('id');

        if (!$id) {
            return response()->json(['error' => 'El ID es obligatorio'], 400);
        }

        $listaractas = DB::table('acta_conformidad')
            ->select(
                'id',
                'idActa',
                'tipo_acta_conformidad',
                'tipo_conformidad',
                'ruc_acta',
                'razon_social_acta',
                'direccion_acta',
                'fecha_registro',
                'fecha_conformidad',
                'fecha_acta',
                'fecha_final_acta',
                'lugar_atencion',
                'trabajo_conformidad',
                'tecnico',
                'registro_user',
            )
            ->where('id', $id)
            ->get();

        return response()->json($listaractas);
    }

    public function obtenerListadeTecnicosActa(){

        $tecnicoactas = DB::table('users')
                    ->select('id','name','apellidopat','apellidomat')
                    ->whereNull('deleted_at')
                    ->get();

        return response()->json($tecnicoactas);
    }

    public function store(ActaConformidadRequest $request)
    {
        $data=$request->validated();
        $data['fecha_acta'] = Carbon::parse($data['fecha_acta'])->format('Y-m-d H:i:s');
        $data['fecha_final_acta'] = Carbon::parse($data['fecha_final_acta'])->format('Y-m-d H:i:s');
        $data['fecha_registro'] = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        $data['fecha_conformidad'] = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        $data['created_at'] = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        $data['updated_at'] = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        $data['registro_user'] = Auth::user()->name;
        ActaConformidad::create($data);
        return redirect()->route('actasConformidad.index');
    }

    public function destroy(string $id)
    {
        $actaconformidad = ActaConformidad::find($id);

        if ($actaconformidad) {
            $actaconformidad->delete();
            return response()->json(['success' => true, 'message' => 'Acta de conformidad eliminada correctamente.']);
        }

        return response()->json(['success' => false, 'message' => 'No se encontró la entrada.'], 404);
    }
}
