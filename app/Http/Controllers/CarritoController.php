<?php

namespace App\Http\Controllers;

use App\Http\Requests\CarritoRequest;
use App\Models\carrito;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarritoController extends Controller
{

    public function index(Request $request)
    {
        return inertia('Carritos/Index');
    }

    public function obtenerDatosdeCompras() {
        $carritos = carrito::select('id','materiales','cantidad','precio_u','moneda')
                    ->orderBy('id','DESC')
                    ->get();
    
        return response()->json($carritos);
    } 

    public function store(CarritoRequest $request){
        $data=$request->validated();
        $data['moneda'] = 's/';
        carrito::create($data);
        return redirect()->back();
    }

    public function destroy($id){
        $carrito=carrito::find($id);
        $carrito->delete();
        return redirect()->back();
    }
}