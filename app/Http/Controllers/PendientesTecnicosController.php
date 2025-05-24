<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendientesTecnicosRequest;
use App\Models\PendientesTecnicos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PendientesTecnicosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('PendientesTecnicos/Index');
    }

    public function obtenerIndexListadePendientes()
    {
        // Establecer la zona horaria de Piura
        $hoy = Carbon::now('America/Lima')->startOfDay();

        $listarpendientes = DB::table('tb_pendientes')
            ->join('clientes', 'clientes.id', '=', 'tb_pendientes.cliente_id')
            ->select(
                'tb_pendientes.id',
                'clientes.razonSocial as razonSocial',
                'tb_pendientes.cantidad',
                'tb_pendientes.descripcion',
                'tb_pendientes.fecha',
                'tb_pendientes.hora',
                'tb_pendientes.lugar',
                'tb_pendientes.contacto',
                'tb_pendientes.nro_contacto',
                'tb_pendientes.usuario'
            )
            ->whereDate('tb_pendientes.fecha', $hoy)
            ->get();

        return response()->json($listarpendientes);
    }

    public function guardarPendientes(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'data' => '',
            'fechaHora' => '',
        ]);

        // Convertir el array recibido en JSON
        $data = json_encode($request->input('data'), JSON_PRETTY_PRINT);
        $fechaHora = $request->input('fechaHora');

        // Crear el nombre del archivo con fecha y hora actuales
        $filename = 'pendientesTecnicos_' . $fechaHora . '.json';

        // Definir la ruta temporal donde se almacenará el archivo temporalmente
        $tempPath = storage_path('app/temp_' . $filename);

        try {
            // Guardar el archivo temporalmente
            File::put($tempPath, $data);

            // Mover el archivo a la carpeta 'public/pendientesTec'
            $publicPath = public_path('pendientesTec/' . $filename);
            File::move($tempPath, $publicPath);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar el archivo: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Pendientes guardados exitosamente.']);
    }

    public function listarArchivos()
    {
        $path = public_path('pendientesTec');
        
        if (!is_dir($path)) {
            return response()->json(['error' => 'Directorio no encontrado'], 404);
        }

        // Obtener la lista de archivos en el directorio
        $files = array_filter(scandir($path), function ($file) use ($path) {
            return is_file($path . DIRECTORY_SEPARATOR . $file);
        });

        // Agregar ruta completa para acceso desde el navegador
        $files = array_map(function ($file) {
            return url('pendientesTec/' . $file);
        }, $files);

        return response()->json(array_values($files)); // Asegurarse de que no queden claves no deseadas
    }

    public function store(PendientesTecnicosRequest $request)
    {
        $data=$request->validated();
        $data['created_at'] = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        $data['updated_at'] = Carbon::now()->setTimezone('America/Lima')->toDateTimeString();
        $data['usuario'] = Auth::user()->name;
        PendientesTecnicos::create($data);
        return redirect()->back();
    }

}
