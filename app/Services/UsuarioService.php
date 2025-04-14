<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class UsuarioService
{
    protected $jugadorService;

    public function __construct(JugadorService $jugadorService)
    {
        $this->jugadorService = $jugadorService;
    }

    public function getAllUsuarios()
    {
        $user = Auth::user();
        if ($user && $user->isEntrenador()) {
            // Obtener el equipo del entrenador
            $equipo = $user->equipo;

            if ($equipo) {
                // Obtener los jugadores que pertenecen al equipo del entrenador
                return User::whereHas('jugador', function ($query) use ($equipo) {
                    $query->where('equipo_id', $equipo->id);
                })->with(['role', 'jugador.equipo'])->latest()->get();
            } else {
                // El entrenador no tiene equipo asignado, devuelve un array vacío
                return [];
            }
        }

        // Si no es entrenador, devuelve todos los usuarios
        return User::with(['role', 'jugador.equipo'])->latest()->get();
    }

    public function createUsuario(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', 'string', 'in:masculino,femenino,otro'],
            'equipo_id' => ['nullable', 'integer', 'exists:equipo,id'], // Asegúrate de que equipo_id esté validado
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
        ]);

        // Si el rol es Jugador y equipo_id está presente, crea un registro en la tabla jugador usando JugadorService
        if ($request->role_id == Role::JUGADOR && $request->equipo_id) {
            $this->jugadorService->createJugador([
                'user_id' => $user->id,
                'equipo_id' => $request->equipo_id,
            ]);
        }

        return $user;
    }

    public function getUsuarioById(User $user)
    {
        return $user->load('role');
    }

    public function updateUsuario(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'fecha_nacimiento' => ['required', 'date'],
            'genero' => ['required', 'string', 'in:masculino,femenino,otro']
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $validated = $validator->validated();

        // Eliminar el role_id si no es admin
        if (!$request->user()->isAdmin() && isset($validated['role_id'])) {
            unset($validated['role_id']);
        }

        // Eliminar la contraseña siempre (no se actualiza)
        if (isset($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return $user->load('role')->refresh();
    }

    public function deleteUsuario(User $user)
    {
        $user->delete();
    }
    
    public function getEntrenadores()
    {
        // Get all users with the entrenador role
        return User::where('role_id', Role::ENTRENADOR)
            ->select('id', 'name', 'email')
            ->get();
    }
}
