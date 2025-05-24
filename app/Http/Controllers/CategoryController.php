<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Response;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('categories')
                        ->select('id', 'name', 'descripcion');

        return inertia('Categories/Index', compact('categories'));
    }

    public function obtenerIndexCategorias() {
        $categories = Category::select('id', 'name', 'descripcion')
                     ->orderBy('id', 'ASC')
                     ->get();
    
        return response()->json($categories);
    }    

    public function store(CategoryRequest $request)
    {
        Category::create($request->validated());
        return redirect()->route('categories.index');
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return redirect()->route('categories.index');
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return response()->json(['message' => 'Categoría eliminada correctamente.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar la categoría.'], 500);
        }
    }

    public function obtenerProductosCategorias(Request $request) {
        $categorias = $request->input('categorias', []);
    
        if (empty($categorias)) {
            return response()->json([]);
        }
    
        if (!is_array($categorias)) {
            $categorias = explode(',', $categorias);
        }
    
        $catProductos = DB::table('productos')
            ->join('categories', 'categories.id', '=', 'productos.category_id')
            ->whereIn('productos.category_id', $categorias)
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
                'productos.ultima_entrada',
                'productos.precio',
                'productos.imagen_producto',
                'productos.usuario_registro',
                'productos.category_id',
                'categories.name AS nombre_categoria',
                DB::raw('(SELECT COALESCE(SUM(cantidad), 0) FROM entradas WHERE entradas.producto_id = productos.id) as total_entradas'),
                DB::raw('(SELECT COALESCE(SUM(unidad_salida), 0) FROM salidas WHERE salidas.producto_id = productos.id) as total_salidas'),
                DB::raw('(SELECT COALESCE(SUM(unidad_devolucion), 0) FROM salidas WHERE salidas.producto_id = productos.id) as total_devolucion')
            )
            ->get();
    
        return response()->json($catProductos);
    }        

    public function obtenerCategorias(){

        $categoriasPro = DB::table('categories')
                        ->select('id','name')
                        ->get();

        return response()->json($categoriasPro);
    }

}
