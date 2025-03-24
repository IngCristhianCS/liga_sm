<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Resources\UsuarioResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UsuarioController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        \Log::info('Usuario en sesión:', [
            'user' => auth()->user()->toArray(),
            'session' => session()->all()
        ]);
    $this->authorize('viewAny', User::class);

        return UsuarioResource::collection(
            User::with('role')
                ->latest()
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', 'string', 'in:masculino,femenino,otro']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero
            ]);

            return (new UsuarioResource($user))
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        return new UsuarioResource($user->load('role'));
    }

    public function update(UpdateUsuarioRequest $request, User $user): UsuarioResource
    {
        $this->authorize('update', $user);

        $validated = $request->validated();

        // Eliminar el role_id si no es admin
        if (!$request->user()->isAdmin() && isset($validated['role_id'])) {
            unset($validated['role_id']);
        }

        // Eliminar la contraseña siempre (no se actualiza)
        if (isset($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return new UsuarioResource($user->load('role')->refresh());
    }

    public function destroy(User $user): Response
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->noContent();
    }
}