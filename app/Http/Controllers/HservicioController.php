<?php

namespace App\Http\Controllers;

use App\Http\Requests\HservicioRequest;
use App\Http\Requests\InformeTecnicoCli;
use App\Http\Requests\UpdateHservicioRequest;
use App\Models\Cliente;
use App\Models\Hservicio;
use App\Models\InformesClientes;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HservicioController extends Controller
{
    public function index(Request $request){
        $hservicios = DB::table('hservicios')
                        ->join('clientes', 'hservicios.cliente_id', '=', 'clientes.id')
                        ->select(
                            'clientes.razonSocial as razonSocial',
                            'hservicios.id',
                            'hservicios.fecha',
                        );
        return Inertia::render('Hojaservico/Index',compact('hservicios'));
    }

    public function obtenerHojasServicioDiarias(Request $request)
    {
        $servicios = DB::table('servicios')
            ->join('clientes', 'servicios.cliente_id', '=', 'clientes.id')
            ->join('hservicios', 'servicios.id', '=', 'hservicios.n_servicio')
            ->select(
                'clientes.razonSocial as razonSocial',
                'servicios.id',
                'servicios.fecha',
                DB::raw('SUM(CASE WHEN hservicios.instrumento = "1" THEN 1 ELSE 0 END) as balanzas'),
                DB::raw('SUM(CASE WHEN hservicios.instrumento = "2" THEN 1 ELSE 0 END) as termometros'),
                DB::raw('SUM(CASE WHEN hservicios.instrumento = "3" THEN 1 ELSE 0 END) as pesas'),
                'hservicios.n_servicio as n_informe'
            )
            ->groupBy('servicios.id', 'clientes.razonSocial', 'servicios.fecha', 'hservicios.n_servicio')
            ->orderBy("servicios.fecha", "DESC")
            ->get();

        return response()->json($servicios);
    }

    public function consultarDatosDiagnosticosServ(Request $request)
    {
        $tbservicioId = $request->input('id');

        $hservicios = DB::select("
            SELECT
                hservicios.id AS hservicio_id,
                hservicios.n_servicio AS hservicio_n_servicio,
                hservicios.hmarca_id,
                hservicios.instrumento,
                hservicios.rango,
                hservicios.medida_bastago,
                hservicios.medida_bastago2,
                hservicios.codigo,
                hservicios.material,
                hservicios.modelo,
                hservicios.serie,
                hservicios.division,
                hservicios.medida_division,
                hservicios.capacidad,
                hservicios.medida_capacidad,
                hservicios.cliente_id AS hservicio_cliente_id,
                hservicios.plataforma,
                hservicios.medida_plataforma,
                hservicios.fecha AS hservicio_fecha,
                hservicios.requiere,
                hservicios.diagnostico,
                hservicios.trabajos,
                hservicios.tecnico,
                hservicios.encargado,
                hservicios.foto,
                hservicios.foto2,
                hservicios.foto3,
                clientes.razonSocial,
                servicios.id AS servicio_id,
                servicios.fecha AS informetec_fecha
            FROM hservicios
            JOIN clientes ON hservicios.cliente_id = clientes.id
            JOIN servicios ON hservicios.n_servicio = servicios.id
            WHERE hservicios.id = ?
        ", [$tbservicioId]);

        return response()->json([
            'hservicios' => $hservicios,
            'tbservicioId' => $tbservicioId,
        ]);
    }

    public function obtenerFechaHojaServicio(Request $request)
    {
        $tbIdClientaso = $request->input('cliente_id');
        $tbHojaServicioId = $request->input('fecha');
        
        // Busca el registro por fecha
        $servicio = DB::table('servicios')
            ->where('fecha', '=', $tbHojaServicioId)
            ->where('cliente_id', '=', $tbIdClientaso)
            ->first();

        // Si no existe, crearlo
        if (!$servicio) {
            $id = DB::table('servicios')->insertGetId([
                'fecha' => $tbHojaServicioId,
                'cliente_id' => $tbIdClientaso,
            ]);

            // Retornar el nuevo registro creado
            $servicio = DB::table('servicios')->where('id', $id)->first();
        }

        return response()->json($servicio);
    }

    public function create(){
        $clientes = Cliente::select('id', 'razonSocial', 'direccion', 'numeroDocumento')->get();
        $hservicios = Hservicio::orderBy('id', 'desc')->get();
        return Inertia::render('Hojaservico/Create',compact('clientes', 'hservicios'));
    }

    public function store(HservicioRequest $request)
    {
        $tecnico = Auth::user()->name;

        $validatedData = $request->except(['foto', 'foto2', 'foto3']);

        $validatedData['tecnico'] = $tecnico;

        foreach (['foto', 'foto2', 'foto3'] as $foto) {
            if ($request->hasFile($foto)) {
                $file = $request->file($foto);
                $routeName = $file->store('', ['disk' => 'hservicio_img']);
                $validatedData[$foto] = $routeName;
            }
        }

        Hservicio::create($validatedData);
        return redirect()->back();
    }

    public function obtenerHojasServicioAnteriores(Request $request){
        $servicio = $request->input('servicio');
        $fecha = $request->input('fecha');

        $obtenerdatos = DB::select('
            SELECT 
                hservicios.id,
                hservicios.hmarca_id,
                hservicios.instrumento,
                hservicios.rango,
                hservicios.medida_bastago,
                hservicios.medida_bastago2,
                hservicios.codigo,
                hservicios.material,
                hservicios.modelo,
                hservicios.serie,
                hservicios.division,
                hservicios.medida_division,
                hservicios.capacidad,
                hservicios.medida_capacidad,
                hservicios.cliente_id,
                hservicios.n_servicio,
                hservicios.plataforma,
                hservicios.medida_plataforma,
                hservicios.fecha,
                hservicios.requiere,
                hservicios.diagnostico,
                hservicios.trabajos,
                hservicios.tecnico,
                hservicios.encargado,
                hservicios.foto,
                hservicios.foto2,
                hservicios.foto3,
                clientes.razonSocial AS cliente_razonSocial
            FROM hservicios
            INNER JOIN clientes ON hservicios.cliente_id = clientes.id
            INNER JOIN servicios ON hservicios.n_servicio = servicios.id
            WHERE hservicios.n_servicio = ?
            AND hservicios.fecha = ?',
            [$servicio ,$fecha]
        );

        return response()->json($obtenerdatos);
    }

    public function obtenerHojasServicio(Request $request){
        $cliente_id = $request->input('cliente_id');
        $fecha = $request->input('fecha');

        $obtenerdatos = DB::select('
            SELECT 
                hservicios.id,
                hservicios.hmarca_id,
                hservicios.instrumento,
                hservicios.rango,
                hservicios.medida_bastago,
                hservicios.medida_bastago2,
                hservicios.codigo,
                hservicios.material,
                hservicios.modelo,
                hservicios.serie,
                hservicios.division,
                hservicios.medida_division,
                hservicios.capacidad,
                hservicios.medida_capacidad,
                hservicios.cliente_id,
                hservicios.plataforma,
                hservicios.medida_plataforma,
                hservicios.fecha,
                hservicios.requiere,
                hservicios.diagnostico,
                hservicios.trabajos,
                hservicios.tecnico,
                hservicios.encargado,
                hservicios.n_servicio,
                hservicios.foto,
                hservicios.foto2,
                hservicios.foto3,
                clientes.razonSocial AS cliente_razonSocial
            FROM hservicios
            INNER JOIN clientes ON hservicios.cliente_id = clientes.id
            WHERE hservicios.cliente_id = ?
            AND hservicios.fecha = ?',
            [$cliente_id,$fecha]
        );

        return response()->json($obtenerdatos);
    }

    public function verificarFechayDatos(Request $request)
    {
        $cliente_id = $request->input('cliente_id');
        $fecha = $request->input('fecha');

        $obtenerdatos = DB::select('
            SELECT 
                hservicios.id,
                hservicios.hmarca_id,
                hservicios.instrumento,
                hservicios.rango,
                hservicios.medida_bastago,
                hservicios.medida_bastago2,
                hservicios.codigo,
                hservicios.material,
                hservicios.modelo,
                hservicios.serie,
                hservicios.division,
                hservicios.medida_division,
                hservicios.capacidad,
                hservicios.medida_capacidad,
                hservicios.cliente_id,
                hservicios.plataforma,
                hservicios.medida_plataforma,
                hservicios.fecha,
                hservicios.requiere,
                hservicios.diagnostico,
                hservicios.trabajos,
                hservicios.tecnico,
                hservicios.encargado,
                hservicios.n_servicio,
                hservicios.foto,
                hservicios.foto2,
                hservicios.foto3,
                clientes.razonSocial AS cliente_razonSocial
            FROM hservicios
            INNER JOIN clientes ON hservicios.cliente_id = clientes.id
            WHERE hservicios.cliente_id = ?
            AND hservicios.fecha = ?',
            [$cliente_id, $fecha]
        );

        return response()->json($obtenerdatos);
    }

    public function fn_registrarInformesTecnicosdeCliente(InformeTecnicoCli $request)
    {
        $resgistrarDatos = new InformesClientes;
        $resgistrarDatos->hmarca_id = $request->input('hmarca_id');
        $resgistrarDatos->lugar_servicio = $request->input('lugar_servicio');
        $resgistrarDatos->instrumento = $request->input('instrumento');
        $resgistrarDatos->rango = $request->input('rango');
        $resgistrarDatos->medida_bastago = $request->input('medida_bastago');
        $resgistrarDatos->medida_bastago2 = $request->input('medida_bastago2');
        $resgistrarDatos->codigo = $request->input('codigo');
        $resgistrarDatos->material = $request->input('material');
        $resgistrarDatos->modelo = $request->input('modelo');
        $resgistrarDatos->serie = $request->input('serie');
        $resgistrarDatos->division = $request->input('division');
        $resgistrarDatos->medida_division = $request->input('medida_division');
        $resgistrarDatos->capacidad = $request->input('capacidad');
        $resgistrarDatos->medida_capacidad = $request->input('medida_capacidad');
        $resgistrarDatos->cliente_id = $request->input('cliente_id');
        $resgistrarDatos->plataforma = $request->input('plataforma');
        $resgistrarDatos->medida_plataforma = $request->input('medida_plataforma');
        $resgistrarDatos->fecha = $request->input('fecha');
        $resgistrarDatos->fecha_final = $request->input('fecha_final');
        $resgistrarDatos->requiere = $request->input('requiere');
        $resgistrarDatos->diagnostico = $request->input('diagnostico');
        $resgistrarDatos->trabajos = $request->input('trabajos');
        $resgistrarDatos->tecnico = $request->input('tecnico');
        $resgistrarDatos->encargado = $request->input('encargado');
        $resgistrarDatos->n_servicio = $request->input('n_servicio');
        $resgistrarDatos->usuario_registrar = Auth::user()->name;

        if ($request->hasFile('fotoInforme1')) {
            $file = $request->file('fotoInforme1');
            $routeName = $file->store('', ['disk' => 'informes_clientes']);
            $resgistrarDatos->fotoInforme1 = $routeName;
        }

        if ($request->hasFile('fotoInforme2')) {
            $file = $request->file('fotoInforme2');
            $routeName = $file->store('', ['disk' => 'informes_clientes']);
            $resgistrarDatos->fotoInforme2 = $routeName;
        }

        $resgistrarDatos->save();

        return response()->json($resgistrarDatos);
    }

    public function update(HservicioRequest $request, Hservicio $hservicio)
    {
        // Validar todos los datos menos las imágenes
        $validatedData = $request->except('foto', 'foto2', 'foto3');

        foreach (['foto', 'foto2', 'foto3'] as $foto) {
            // Verificar si la imagen está en Base64
            if ($request->has($foto) && str_contains($request->$foto, 'data:image')) {
                // Decodificar la imagen en Base64
                $imageData = $request->$foto;
                $fileName = uniqid() . '.png'; // Puedes usar el tipo de archivo adecuado (jpg, png, etc.)
                
                // Extraer y decodificar el contenido de la imagen
                $imageData = explode(',', $imageData)[1];
                $decodedImage = base64_decode($imageData);

                // Eliminar imagen anterior si existe
                if ($hservicio->$foto) {
                    Storage::disk('hservicio_img')->delete($hservicio->$foto);
                }

                // Guardar la nueva imagen en el disco
                Storage::disk('hservicio_img')->put($fileName, $decodedImage);
                $validatedData[$foto] = $fileName;
            
            } elseif ($request->hasFile($foto)) {
                // Procesar imágenes que se suben como archivos
                if ($hservicio->$foto) {
                    Storage::disk('hservicio_img')->delete($hservicio->$foto);
                }
                
                $file = $request->file($foto);
                $routeName = $file->store('', ['disk' => 'hservicio_img']);
                $validatedData[$foto] = $routeName;
            
            } elseif ($request->has($foto)) {
                // Si la imagen ya tiene una URL, mantener la existente
                $validatedData[$foto] = $hservicio->$foto;
            }
        }

        $hservicio->update($validatedData);

        // Redirección al final
        return redirect()->back();
    }

    public function destroy($id)
    {
        Hservicio::destroy($id);
        return redirect()->back();
    }
}
