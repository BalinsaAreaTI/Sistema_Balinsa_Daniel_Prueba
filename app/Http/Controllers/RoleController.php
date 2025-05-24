<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::select('id', 'name')->orderBy('id', 'DESC')->paginate(80);
        return Inertia::render('Roles/Index', [
            'roles' => $roles
        ]);
    }

    public function obtenerRolesdeUsuariosIndex() {
        $roles = Role::select('id', 'name')
                     ->orderBy('id', 'DESC')
                     ->get();
    
        return response()->json($roles);
    } 

    public function obtenerPermisosdeRoles(Role $role)
    {
        $permisos = Permission::pluck('name', 'id')->all();
        $rolePermisos = $role->permissions->pluck('id')->toArray();

        return response()->json([
            'permisos' => $permisos,
            'rolePermisos' => $rolePermisos,
            'role' => $role
        ]);
    }  

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);
        
        Role::create(['name' => $request->input('name')]);
        
        return redirect()->route('roles.index');
    }

    public function edit(Role $role)
    {
        $permisos = Permission::pluck('name', 'id')->all();
        $rolePermisos = $role->permissions->pluck('id')->toArray();

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permisos' => $permisos,
            'rolePermisos' => $rolePermisos
        ]);
    }

    public function update(Request $request,Role $role)
    {
        $permissions = Permission::whereIn('id', $request->input('permisos'))->get();
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index');
    }

    public function destroy($id){
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->back();
    }
}
