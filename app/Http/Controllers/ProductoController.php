<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Entrada;
use Inertia\Response;
use App\Models\Category;
use App\Http\Requests\ProductoRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');

        $productos = DB::table('categories')
                        ->join('productos', 'categories.id', '=', 'productos.category_id')
                        ->select(
                            'categories.id',
                            'categories.name',
                            'productos.id as producto_id',
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
                            'productos.imagen_producto',
                            'productos.category_id',
                            DB::raw('(SELECT MAX(entradas.fecha) FROM entradas WHERE entradas.producto_id = productos.id) as entrada_maxima'),
                            DB::raw('(SELECT COALESCE(SUM(cantidad), 0) FROM entradas WHERE entradas.producto_id = productos.id) as total_entradas'),
                            DB::raw('(SELECT COALESCE(SUM(unidad_salida), 0) FROM salidas WHERE salidas.producto_id = productos.id) as total_salidas'),
                            DB::raw('(SELECT COALESCE(SUM(unidad_devolucion), 0) FROM salidas WHERE salidas.producto_id = productos.id) as total_devolucion'),
                            DB::raw('(SELECT COALESCE((SELECT cantidad FROM entradas WHERE entradas.producto_id = productos.id ORDER BY id DESC LIMIT 1), 0)) as ultima_cantidad_entrada')
                        )
                        ->where('categories.id', '=', $categoryId)->orderBy('productos.id', 'DESC')
                        ->paginate(1000);

        $productos->appends(['category_id' => $categoryId]);

        return inertia('Productos/Index', [
            'productos' => $productos,
            'selectedCategoryId' => $categoryId,
        ]);
    }

    public function obtenerIndexInventario(Request $request) {

        $category_id = $request->query('category_id');
    
        $productos = DB::table('categories')
            ->join('productos', 'categories.id', '=', 'productos.category_id')
            ->select(
                'categories.id',
                'categories.name',
                'productos.id as producto_id',
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
                'productos.imagen_producto',
                'productos.category_id',
                DB::raw('(SELECT MAX(entradas.fecha) FROM entradas WHERE entradas.producto_id = productos.id) as entrada_maxima'),
                DB::raw('(SELECT COALESCE(SUM(cantidad), 0) FROM entradas WHERE entradas.producto_id = productos.id) as total_entradas'),
                DB::raw('(SELECT COALESCE(SUM(unidad_salida), 0) FROM salidas WHERE salidas.producto_id = productos.id) as total_salidas'),
                DB::raw('(SELECT COALESCE(SUM(unidad_devolucion), 0) FROM salidas WHERE salidas.producto_id = productos.id) as total_devolucion'),
                DB::raw('(SELECT COALESCE((SELECT cantidad FROM entradas WHERE entradas.producto_id = productos.id ORDER BY id DESC LIMIT 1), 0)) as ultima_cantidad_entrada')
            )
            ->where('categories.id', '=', $category_id)
            ->orderBy('productos.id', 'DESC')
            ->paginate(1000);
    
        return response()->json($productos);
    }        

    public function fn_ObtenerDatosSalidas(Request $request){
        try {
            $query = DB::table('salidas')
                        ->join('productos', 'salidas.producto_id', '=', 'productos.id')
                        ->join('users', 'users.id', '=', 'salidas.tecnico')
                        ->select(
                            'salidas.empresa',
                            'salidas.unidad_salida',
                            'salidas.comentario_salida',
                            'users.name as tecnico',
                            'salidas.fecha',
                            'salidas.hora_salida',
                            'salidas.unidad_devolucion',
                            'productos.insumo as producto_nombre'
                        );
    
            if ($request->has('fechaHoy') && $request->has('fechaHasta')) {
                $query->whereBetween('salidas.fecha', [$request->fechaHoy, $request->fechaHasta]);
            }
    
            $salidas = $query->orderBy('salidas.id', 'DESC')->get();
        
            return response()->json($salidas);
        } catch (\Exception $e) {
            \log("")::error('Error al obtener las salidas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las salidas'], 500);
        }
    }    
    
    public function create()
    {
        return inertia('Productos/Create');
    }

    public function obtenerCategoriasProductos(){

        $categorias = DB::table('categories')
                        ->select('id','name')
                        ->get();

        return response()->json($categorias);
    }

    public function store(ProductoRequest $request)
    {
        $request['fecha'] = Carbon::parse($request['fecha'])->format('Y-m-d H:i:s');

        $producto = Producto::create($request->validated());

        if ($request->hasFile('imagen_producto')) {
            $file = $request->file('imagen_producto');
            $filePath = $file->store('', ['disk' => 'productos_inventario']);
            $producto->imagen_producto = $filePath;
            $producto->save();
        }

        return redirect()->route('productos.index', ['category_id' => $producto->category_id]);
    }

    public function edit(Producto $producto)
    {
        $category = Category::all();
        return inertia('Productos/Edit', ['productos' => $producto, 'categories' => $category]);
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $request->except('imagen_producto');
        
        if ($request->hasFile('imagen_producto')) {
            // Eliminar la imagen anterior si existe
            if ($producto->imagen_producto) {
                Storage::disk('productos_inventario')->delete($producto->imagen_producto);
            }

            // Obtener la nueva imagen
            $file = $request->file('imagen_producto');
            // Almacenar la nueva imagen y obtener la ruta
            $filePath = $file->store('', ['disk' => 'productos_inventario']);
            // Agregar la nueva ruta de imagen a los datos
            $data['imagen_producto'] = $filePath;
        }

        // Actualizar el producto con los nuevos datos
        $producto->update($data);

        return redirect()->route('productos.index', ['category_id' => $producto->category_id]);
    }

    public function destroy($id)
    {
        $producto = Producto::find($id);

        if ($producto) {
            DB::transaction(function () use ($producto) {
                $producto->salidas()->delete();
                $producto->entradas()->delete();
                $imagePath = public_path('productos_inventario/' . $producto->imagen_producto);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $producto->delete();
            });
        }
        return redirect()->back();
    }
}
