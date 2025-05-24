<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Users/Index');
    }

    public function obtenerDatosdeUsuarios() {
        $users = User::with('roles')
            ->whereNotIn('id', [1, 21])
            ->orderBy('id', 'DESC')
            ->get();
    
        return response()->json($users);
    }  

    public function obtenerRolesdeUsuarios(){
        $roles = DB::table('roles')->select('id', 'name')->get();
        return response()->json($roles);
    }

    public function deletePermanently($id){
        $user= User::withTrashed()->find($id);
        if(!empty($user)){
            $user->forceDelete();
        }
        return redirect()->back();
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return Inertia::render('Users/Create', [
            'roles'=>$roles
        ]);
    }

    public function store(UserCreateRequest $request)
    {
        // Crear el usuario con los datos validados
        $input = $request->validated();
        $input['password'] = Hash::make($input['password']); // Encripta la contraseña

        $user = User::create($input); // Crea el usuario

        // Asignar los roles al usuario
        $user->assignRole($request->input('roles'));

        // Redirigir al índice de usuarios
        return Inertia::render('Users/Index');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name')->all(); // Obtiene todos los roles disponibles
        $userRoles = $user->roles->pluck('name')->toArray(); // Obtiene los roles actuales del usuario

        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => $roles, // Asegúrate de pasar los roles disponibles
            'userRoles' => $userRoles // Pasa los roles actuales del usuario
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'apellidopat' => 'required',
            'apellidomat' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|confirmed',
            'sexo' => 'required',
            'celular' => 'required',
            'roles' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $input = $request->all();

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $user = User::find($id);
        $user->update($input);

        // Sincroniza roles
        $roles = Role::whereIn('name', $request->input('roles'))->get();
        $user->syncRoles($roles);

        // Sincroniza permisos
        $permissions = [];
        foreach ($roles as $role) {
            $permission = DB::table('role_has_permissions')
                        ->where('role_id', $role->id)
                        ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                        ->pluck('permissions.name')
                        ->toArray();
            $permissions = array_merge($permissions, $permission);
        }
        $user->syncPermissions($permissions);

        return redirect()->route('users.index');
    }

    public function destroy( $id)
    {
        $user =User::find($id);
        $user->delete();

        return redirect()->route('users.index');
    }
}
