<?php

namespace App\Http\Controllers;

use App\Models\Salida;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class SalidasController extends Controller
{
    public function index(Request $request)
    {
        return inertia('Salidas/Index');
    }

    public function obtenerListadeSalidasUnicas(Request $request) {
        $salidas = DB::table('salidas')
                    ->join('users', 'users.id', '=', 'salidas.tecnico')
                    ->join('productos', 'productos.id', '=', 'salidas.producto_id')
                    ->select(
                        'salidas.id',
                        'salidas.empresa',
                        'salidas.unidad_salida',
                        'salidas.comentario_salida',
                        'users.name',
                        'salidas.fecha',
                        'productos.insumo',
                        'salidas.unidad_devolucion',
                        'salidas.devolucion',
                        'salidas.hora_salida',
                    )
                    ->where('salidas.devolucion', '=', '1')
                    ->orderBy('salidas.id', 'DESC')
                    ->get();
        return response()->json($salidas);
    }

    public function listarSalidasRelacionadas(){
        return inertia('Salidas/SalidasRelacionadas');
    }

    public function obtenerIndexListadeSalidas(Request $request) {
        $productoId = $request->query('producto_id');

        $productos = DB::table('salidas')
                        ->join('users', 'users.id', '=', 'salidas.tecnico')
                        ->join('productos', 'productos.id', '=', 'salidas.producto_id')
                        ->select(
                            'salidas.id',
                            'salidas.empresa',
                            'salidas.unidad_salida',
                            'salidas.comentario_salida',
                            'users.name',
                            'salidas.fecha',
                            'productos.insumo',
                            'salidas.unidad_devolucion',
                            'salidas.devolucion',
                            'salidas.hora_salida'
                        )
                        ->where('salidas.producto_id', '=', $productoId)
                        ->orderBy('salidas.id', 'DESC')
                        ->when($productoId, function ($query) use ($productoId) {
                            return $query->where('salidas.producto_id', $productoId);
                        })
                        ->get();

        return response()->json($productos);
    }

    public function create()
    {
        $salidas = Salida::all();
        $productos = Producto::all();

        $tecnico_salidas = DB::table('users')
            ->select('id','name','password')
            ->whereNull('deleted_at')
            ->get();
            
        return inertia('Salidas/Create', ['salidas' => $salidas, 'tecnico_salidas' => $tecnico_salidas, 'productos' => $productos]);
    }

    public function obtenerDatosConsultarProductosSalidas(){

        $productoscs = DB::table('productos')
                    ->select(
                        'productos.id',
                        'productos.insumo'
                    )
                    ->get();

        return response()->json($productoscs);
    }

    public function obtenerDatosUsuariosSalidas(){

        $tecnico_salidas = DB::table('users')
                        ->select('id','name','password')
                        ->whereNull('deleted_at')
                        ->get();

        return response()->json($tecnico_salidas);
    }

    public function comprobarSalida(Request $request)
    {
        $idTec = $request->input('id');
        $passwordconfirmacion = $request->input('passwordconfirmacion');

        // Obtener el usuario por su ID
        $usuario = User::find($idTec);

        if (!$usuario) {
            return response()->json(['siexisteusuario' => false]);
        }

        // Verificar la contraseña utilizando Hash::check()
        $contraseñaCorrecta = Hash::check($passwordconfirmacion, $usuario->password);

        return response()->json(['siexisteusuario' => $contraseñaCorrecta]);
    }

    public function validarstock(Request $request)
    {
        $producto_id = $request->input('producto_id');
        
        $productos = DB::table('productos')
                            ->select(
                                'productos.id',
                                'productos.insumo',
                                'productos.marca',
                                'productos.modelo',
                                'productos.cantidad',
                                'productos.unidad_medida',
                                'productos.fecha',
                                'productos.comprador',
                                'productos.comentario',
                                'productos.stock',
                                'productos.precio',
                                'productos.ultima_entrada',
                                DB::raw('(SELECT COALESCE(SUM(cantidad), 0) FROM entradas WHERE entradas.producto_id = productos.id) as total_entradas'),
                                DB::raw('(SELECT COALESCE(SUM(unidad_salida), 0) FROM salidas WHERE salidas.producto_id = productos.id) as total_salidas'),
                                DB::raw('(SELECT COALESCE(SUM(unidad_devolucion), 0) FROM salidas WHERE salidas.producto_id = productos.id) as total_devolucion'),
                                DB::raw('(SELECT COALESCE((SELECT cantidad FROM entradas WHERE entradas.producto_id = productos.id ORDER BY id DESC LIMIT 1), 0)) as ultima_cantidad_entrada')
                            )
                            ->where('productos.id', '=', $producto_id)->orderBy('id', 'DESC')
                            ->get();

        return response()->json($productos);
    }

    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'empresa' => 'required',
            'unidad_salida' => 'required',
            'comentario_salida' => 'required',
            'tecnico' => 'required',
            'fecha' => 'required|date',
            'hora_salida' => 'required',
            'producto_id' => 'required',
            'devolucion' => 'nullable',
            'unidad_devolucion' => 'nullable',
        ]);

        // Encuentra el producto asociado a la salida
        $producto = Producto::findOrFail($validatedData['producto_id']);

        // Formatear la fecha
        $validatedData['fecha'] = Carbon::parse($validatedData['fecha'])->format('Y-m-d');

        // Crear la salida
        Salida::create($validatedData);
    }

    public function edit(Salida $salida)
    {
        $producto = Producto::all();

        $tecnico_salidas = DB::table('users')
            ->select('id','name','password')
            ->get();

        return inertia('Salidas/Edit', ['salidas' => $salida, 'productos' => $producto, 'tecnico_salidas' => $tecnico_salidas]);
    }

    public function editSalidas(Salida $salida)
    {
        $producto = Producto::all();

        $tecnico_salidas = DB::table('users')
            ->select('id','name','password')
            ->get();

        return inertia('Salidas/EditSalidas', ['salidas' => $salida, 'productos' => $producto, 'tecnico_salidas' => $tecnico_salidas]);
    }

    public function update(Request $request, Salida $salida)
    {
        // Valida los datos recibidos en la solicitud
        $validated = $request->validate([
            'empresa' => 'required|string',
            'unidad_salida' => 'required|numeric',
            'comentario_salida' => 'required|string',
            'tecnico' => 'required|integer',
            'fecha' => 'required',
            'hora_salida' => 'required',
            'producto_id' => 'required',
            'devolucion' => 'nullable',
            'unidad_devolucion'=>'nullable',
        ]);

        if(isset($validated['unidad_devolucion']) && !empty($validated['unidad_devolucion'])) {$validated['devolucion'] = 2;}

        // Inicia una transacción para asegurar la integridad de los datos
        DB::beginTransaction();
        try {

            // dd($validated);
            // Actualiza los datos de la salida
            $salida->update($validated);

            // Encuentra el producto asociado a la salida
            $producto = Producto::findOrFail($validated['producto_id']);

            // Confirma los cambios en la base de datos
            DB::commit();
        } catch (\Exception $e) {
            // Revierte los cambios en caso de error
            DB::rollBack();
            return redirect()->route('salidas.edit', $salida)->withErrors('Error al actualizar la salida: ' . $e->getMessage());
        }
    }
    
    public function destroy(Salida $salida)
    {
        DB::beginTransaction();
        try {
            $producto = Producto::findOrFail($salida->producto_id);
            $producto->stock += $salida->cantidad;
            $producto->save();
            $salida->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Salida eliminada y stock restaurado correctamente.', 'producto_id' => $producto->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false,'message' => 'Error al eliminar la salida: ' . $e->getMessage()], 500);
        }
    }
}
