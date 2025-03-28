<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;
use App\Http\Resources\RoleResource; // Importar RoleResource

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $roles = $this->roleService->getAllRoles();
        return RoleResource::collection($roles); // Usar RoleResource::collection()
    }

    public function store(Request $request)
    {
        $result = $this->roleService->createRole($request);

        if (isset($result['errors'])) {
            return response()->json(['success' => false, 'errors' => $result['errors']], 422);
        }

        return response()->json(['success' => true, 'data' => new RoleResource($result), 'message' => 'Rol creado exitosamente'], 201); // Usar new RoleResource()
    }

    public function show($id)
    {
        try {
            $role = $this->roleService->getRoleById($id);
            return new RoleResource($role); // Usar new RoleResource()
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Rol no encontrado'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $role = $this->roleService->getRoleById($id);
            $result = $this->roleService->updateRole($request, $role);

            if (isset($result['errors'])) {
                return response()->json(['success' => false, 'errors' => $result['errors']], 422);
            }

            return response()->json(['success' => true, 'data' => new RoleResource($result), 'message' => 'Rol actualizado exitosamente'], 200); // Usar new RoleResource()
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el rol'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = $this->roleService->getRoleById($id);
            $result = $this->roleService->deleteRole($role);

            if (isset($result['error'])) {
                return response()->json(['success' => false, 'message' => $result['error']], 409);
            }

            return response()->json(['success' => true, 'message' => 'Rol eliminado exitosamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el rol'], 500);
        }
    }
}