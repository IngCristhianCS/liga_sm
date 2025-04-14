<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Services\UsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UsuarioController extends Controller
{
    use AuthorizesRequests;

    protected $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        $usuarios = $this->usuarioService->getAllUsuarios();
        return UsuarioResource::collection($usuarios);
    }

    public function store(Request $request): JsonResponse
    {
        $user = new User($request->all());
        $this->authorize('create', $user);
        $result = $this->usuarioService->createUsuario($request);

        if (isset($result['errors'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $result['errors']
            ], 422);
        }

        return (new UsuarioResource($result))
            ->response()
            ->setStatusCode(201);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        $usuario = $this->usuarioService->getUsuarioById($user);
        return new UsuarioResource($usuario);
    }

    public function update(UpdateUsuarioRequest $request, User $user): UsuarioResource
    {
        $this->authorize('update', $user);
        $usuario = $this->usuarioService->updateUsuario($request, $user);
        return new UsuarioResource($usuario);
    }

    public function destroy(User $user): Response
    {
        $this->authorize('delete', $user);
        $this->usuarioService->deleteUsuario($user);
        return response()->noContent();
    }

    public function entrenadores()
    {
        $entrenadores = $this->usuarioService->getEntrenadores();
        return response()->json(['data' => $entrenadores]);
    }
}