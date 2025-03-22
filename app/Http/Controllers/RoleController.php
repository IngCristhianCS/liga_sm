<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Obtener todos los roles
     */
    public function index()
    {
        try {
            $roles = Role::all();
            return response()->json([
                'success' => true,
                'data' => $roles
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los roles'
            ], 500);
        }
    }

    /**
     * Crear nuevo rol
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:roles',
                'slug' => 'required|string|max:255|unique:roles'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $role = Role::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $role,
                'message' => 'Rol creado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el rol'
            ], 500);
        }
    }

    /**
     * Obtener un rol específico
     */
    public function show($id)
    {
        try {
            $role = Role::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $role
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], 404);
        }
    }

    /**
     * Actualizar rol
     */
    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

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
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $role->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $role,
                'message' => 'Rol actualizado exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el rol'
            ], 500);
        }
    }

    /**
     * Eliminar rol
     */
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            
            // Validar si el rol está siendo usado
            if ($role->users()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el rol, está siendo utilizado'
                ], 409);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el rol'
            ], 500);
        }
    }
}