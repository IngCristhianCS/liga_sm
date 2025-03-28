<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RoleService
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getAllRoles()
    {
        $user = Auth::user();

        if ($user && $user->isEntrenador()) {
            $jugadorRole = Role::where('slug', 'player')->first();
            return $jugadorRole ? [$jugadorRole] : [];
        }

        // Si no es entrenador, devuelve todos los roles
        return $this->roleRepository->getAll();
    }

    public function createRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            'slug' => 'required|string|max:255|unique:roles'
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        return $this->roleRepository->create($request->all());
    }

    public function getRoleById(int $id)
    {
        return $this->roleRepository->findById($id);
    }

    public function updateRole(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id)
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id)
            ]
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        return $this->roleRepository->update($role, $request->all());
    }

    public function deleteRole(Role $role)
    {
        // Validar si el rol está siendo usado
        if ($role->users()->exists()) {
            return ['error' => 'No se puede eliminar el rol, está siendo utilizado'];
        }

        $this->roleRepository->delete($role);
        return ['success' => true];
    }
}