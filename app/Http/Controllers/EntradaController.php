<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EntradaController extends Controller
{
    public function index(Request $request)
    {
        return inertia('Entradas/Index');
    }

    public function obtenerEntradas(Request $request){

        $entradas = DB::table('entradas')
                    ->join('productos', 'productos.id', '=', 'entradas.producto_id')
                    ->select(
                        'entradas.id',
                        'entradas.cantidad',
                        'entradas.fecha',
                        'productos.insumo',
                        'entradas.producto_id'
                    )
                    ->orderBy('entradas.id', 'DESC')
                    ->get();

        return response()->json($entradas);
    }

    public function obtenerDatosConsultarProductos(){

        $productoscs = DB::table('productos')
                    ->select(
                        'productos.id',
                        'productos.insumo'
                    )
                    ->get();

        return response()->json($productoscs);
    }

    public function create()
    {
        $entradas = Entrada::all();
        $productos = Producto::all();
        return inertia('Entradas/Create', ['entradas' => $entradas, 'productos' => $productos]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required',
            'cantidad' => 'required|numeric',
            'fecha' => 'required|date',
        ]);

        $entrada = new Entrada();
        $entrada->producto_id = $request->producto_id;
        $entrada->cantidad = $request->cantidad;

        $entrada->fecha = Carbon::parse($request->fecha)->format('Y-m-d');

        $entrada->save();

        return redirect()->route('entradas.index')->with('success', 'Entrada creada exitosamente.');
    }

    public function edit(Entrada $entrada)
    {
        $producto = Producto::all();
        return inertia('Entradas/Edit', ['entradas' => $entrada, 'productos' => $producto]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'producto_id' => 'required',
            'cantidad' => 'required|numeric',
            'fecha' => 'required|date',
        ]);

        DB::beginTransaction();
        $entradaActual = DB::table('entradas')->where('id', $id)->first();

        if (!$entradaActual) {
            throw new \Exception("Entrada no encontrada.");
        }

        $fechaFormateada = Carbon::parse($request->fecha)->format('Y-m-d');

        DB::table('entradas')->where('id', $id)->update([
            'producto_id' => $request->producto_id,
            'cantidad' => $request->cantidad,
            'fecha' => $fechaFormateada,
        ]);

        DB::commit();
    }

    public function destroy($id)
    {
        $entrada = Entrada::find($id);

        if ($entrada) {
            $entrada->delete();
            return response()->json(['success' => true, 'message' => 'Entrada eliminada correctamente.']);
        }

        return response()->json(['success' => false, 'message' => 'No se encontró la entrada.'], 404);
    }
}
