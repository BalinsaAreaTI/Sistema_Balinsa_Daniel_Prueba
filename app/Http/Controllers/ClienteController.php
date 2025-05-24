<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use App\Models\Tbprovincias;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{

    public function index()
    {
        $clientes = Cliente::with('tbprovincia')->orderBy('id', 'DESC')->select('id', 'numeroDocumento', 'razonSocial', 'direccion','tbprovincia_id','ctg','asesor','fechafactura','codigofactura')->paginate(4000);
        $tbprovincias = Tbprovincias::all();
        return Inertia::render('Clientes/Index', compact('clientes','tbprovincias'));
    }

    public function create()
    {
        $tbprovincias = Tbprovincias::all();
        return inertia::render('Clientes/Create', compact('tbprovincias'));
    }

    public function store(ClienteRequest $request)
    {
        // Permite Regisrtrar a los clientes con el usuario autenticado siempre y cuando el rol sea de "asesor de ventas"
        // $user = auth()->user();

        $asesor = "s/n";
        // Permite Regisrtrar a los clientes con el usuario autenticado siempre y cuando el rol sea de "asesor de ventas"
        // if ($user->roles->contains('name', 'Asesor de Ventas')) {
        //     $asesor = $user->name;
        // }

        $token = 'apis-token-10192.lgbp44SaTMi8tlleXcrrFYn79hAwerEU';
        $ruc = $request->input('numeroDocumento');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.apis.net.pe/v2/sunat/ruc?numero=' . $ruc,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Referer: http://apis.net.pe/api-ruc',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $empresa = json_decode($response);

        $datosEmpresa = $empresa && !empty($empresa->numeroDocumento) ? [
            'numeroDocumento' => $empresa->numeroDocumento,
            'razonSocial' => $empresa->razonSocial,
            'direccion' => $empresa->direccion,
            'distrito' => $empresa->distrito,
            'provincia' => $empresa->provincia,
            'departamento' => $empresa->departamento,
            'estado' => $empresa->estado,
        ] : [
            'numeroDocumento' => $request->input('numeroDocumento'),
            'razonSocial' => $request->input('razonSocial'),
            'direccion' => $request->input('direccion'),
            'distrito' => $request->input('distrito'),
            'provincia' => $request->input('provincia'),
            'departamento' => $request->input('departamento'),
            'estado' => 'No registrado',
        ];
    
        Cliente::create(array_merge($datosEmpresa, [
            'cli_direccion2' => $request->input('cli_direccion2'),
            'cli_observacion' => $request->input('cli_observacion'),
            'tbprovincia_id' => $request->input('tbprovincia_id'),
            'asesor' => $asesor,
            'ctg' => 'Sin Informacion',
        ]));

        return redirect()->route('clientes.index');
    }


    public function edit(Cliente $cliente)
    {
        $tbprovincias = Tbprovincias::all();
        return inertia('Clientes/Edit',compact('cliente','tbprovincias'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($request->all());
        return redirect()->route('clientes.index');
    }

    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        $cliente->delete();
        return redirect()->back();
    }


    public function updateCtg(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'ctg' => 'required|in:Vip,Potencial,Regular,Sin Informacion', // Sin espacios en los valores 'Cliente Potencial' y 'Cliente Regular'
        ]);

        $cliente = Cliente::find($request->cliente_id);

        $cliente->ctg = $request->ctg;
        $cliente->save();
    }

    public function obtenerDatosConsultarClientes(){
        $clientes = DB::select('
            SELECT 
            clientes.id,
            numeroDocumento,
            razonSocial,
            direccion,
            distrito,
            provincia,
            departamento,
            estado,
            ctg,
            asesor,
            fechafactura,
            codigofactura,
            cli_direccion2,
            cli_observacion,
            clientes.tbprovincia_id,
            tbprovincias.prov_nombre
            FROM clientes
            INNER JOIN tbprovincias ON tbprovincias.id = clientes.tbprovincia_id
            ORDER BY clientes.id DESC
        ');

        return response()->json($clientes);
    }

    public function obtenerDatosCiudades(){
        $clientes = DB::select('
            SELECT
            id,
            prov_nombre
            FROM tbprovincias
        ');

        return response()->json($clientes);
    }

}
