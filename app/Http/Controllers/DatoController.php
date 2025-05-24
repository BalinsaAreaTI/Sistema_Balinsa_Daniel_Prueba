<?php

namespace App\Http\Controllers;

use App\Http\Requests\DatoRequest;
use App\Http\Requests\updateDatoRequest;
use App\Models\Cliente;
use App\Models\Dato;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class DatoController extends Controller
{

    public function index(Request $request)
    {
        return Inertia::render('Datos/Index');
    }

    public function obtenerDatosClientesRelacionados(Request $request)
    {
        $cliente_id = $request->input('cliente_id');
        $datos = Dato::where('cliente_id', $cliente_id)->orderBy('id', 'DESC')->get();

        return response()->json($datos);
    }

    public function store(DatoRequest $request)
    {
        $validatedData = $request->except(['tarjeta']);
        if ($request->hasFile('tarjeta')) {
            $tarjeta = $request->file('tarjeta');
            $routeName = $tarjeta->store('cliente', ['disk' => 'public']);
            $validatedData['tarjeta'] = $routeName;
        }
        $dato = Dato::create($validatedData);
        return redirect()->back();
    }

    public function edit(Dato $dato){
        $clientes=Cliente::all();
        return Inertia::render('Datos/Edit',compact('clientes','dato'));
    }

    public function update(updateDatoRequest $request, $id)
    {
        
        $dato = Dato::findOrFail($id);
        $validatedData = $request->validated();
        $dato->update($validatedData);
        
        return redirect()->back();
    }


    public function destroy($id)
    {
        $dato = Dato::find($id);
        if ($dato->tarjeta) {
            Storage::disk('public')->delete($dato->tarjeta);
        }
        $dato->delete();
        return redirect()->back();
    }

}
