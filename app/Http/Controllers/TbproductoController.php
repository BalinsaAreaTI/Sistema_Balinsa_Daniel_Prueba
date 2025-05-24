<?php

namespace App\Http\Controllers;

use App\Http\Requests\TbproductoRequest;
use App\Http\Requests\UpdateTbproductoRequest;
use App\Models\Tbcategoria;
use App\Models\Tbmarca;
use App\Models\Tbproducto;
use App\Models\Tbsubcategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TbproductoController extends Controller
{

    public function index()
    {
        $tbproductos = Tbproducto::with('tbcategoria', 'tbsubcategoria', 'tbmarca')
            ->orderByRaw('
                CASE 
                    WHEN codigo REGEXP "^[0-9]+$" THEN 0
                    ELSE 1
                END ASC, 
                CAST(codigo AS UNSIGNED) ASC, 
                codigo ASC
            ')
            ->orderBy('id', 'DESC')
            ->paginate(500);
        $tbcategorias = Tbcategoria::with('tbsubcategorias')->get();
        $tbsubcategorias = Tbsubcategoria::orderByRaw("FIELD(id, 19, 20, 1, 3, 9, 10, 4, 16)")->get();
        $tbmarcas = Tbmarca::all();
        return Inertia::render('Catalogo/Index', compact('tbproductos', 'tbcategorias', 'tbsubcategorias', 'tbmarcas'));
    }

    public function create()
    {
        $tbcategorias = Tbcategoria::all();
        $tbsubcategorias = Tbsubcategoria::all();
        $tbmarcas = Tbmarca::all();

        return Inertia::render('Catalogo/Create', compact('tbcategorias','tbsubcategorias','tbmarcas'));
    }

    public function store(TbproductoRequest $request)
    {
        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $routeName = $file->store('', ['disk' => 'productos_img']);
            $data['foto'] = $routeName;
        }

        Tbproducto::create($data);
        return redirect()->route('tbproductos.index');
    }

    public function edit(Tbproducto $tbproducto)
    {
        $tbmarcas = Tbmarca::all();
        $tbcategorias = Tbcategoria::all();
        $tbsubcategorias = Tbsubcategoria::all();
        return Inertia::render('Catalogo/Edit', compact('tbproducto','tbmarcas','tbcategorias','tbsubcategorias'));
    }

    public function update(UpdateTbproductoRequest $request, Tbproducto $tbproducto)
    {
        // Obtener los datos del request, excluyendo el campo 'foto'
        $data = $request->except('foto');

        // Verificar si el precio_list fue modificado
        if ($request->has('precio_list') && $tbproducto->precio_list != $request->input('precio_list')) {
            // Si precio_list ha cambiado, actualizar el campo fecha_act con la fecha actual
            $data['fecha_act'] = now();  // Asignamos la fecha actual
        }

        // Si el request contiene una nueva foto, procesarla
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $routeName = $file->store('', ['disk' => 'productos_img']);
            $data['foto'] = $routeName;
        }

        // Actualizar el producto con los datos proporcionados
        $tbproducto->update($data);

        // Redirigir al usuario al índice de productos
        return redirect()->route('tbproductos.index');
    }

    public function destroy($id)
    {
        $tbproducto = Tbproducto::find($id);
        $tbproducto->delete();
        return redirect()->back();
    }

}
