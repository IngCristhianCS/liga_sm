<?php

namespace App\Http\Controllers;

use App\Services\CategoriaService;
use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoriaController extends Controller
{
    use AuthorizesRequests;

    protected $categoriaService;

    public function __construct(CategoriaService $categoriaService)
    {
        $this->categoriaService = $categoriaService;
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $this->authorize('viewAny', Categoria::class);
        $categorias = $this->categoriaService->getAll();
        return response()->json(['success' => true, 'data' => $categorias]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Categoria::class);
        $result = $this->categoriaService->create($request->all());

        if (isset($result['errors'])) {
            return response()->json(['success' => false, 'errors' => $result['errors']], 422);
        }

        return response()->json(['success' => true, 'data' => $result, 'message' => 'Categoria creada exitosamente'], 201);
    }

    public function show($id)
    {
        $categoria = $this->categoriaService->findById($id);
        $this->authorize('view', $categoria);
        return response()->json(['success' => true, 'data' => $categoria]);
    }

    public function update(Request $request, $id)
    {
        $categoria = $this->categoriaService->findById($id);
        $this->authorize('update', $categoria);
        $result = $this->categoriaService->update($id, $request->all());

        if (isset($result['errors'])) {
            return response()->json(['success' => false, 'errors' => $result['errors']], 422);
        }

        return response()->json(['success' => true, 'data' => $result, 'message' => 'Categoria actualizada exitosamente'], 200);
    }

    public function destroy($id)
    {
        $categoria = $this->categoriaService->findById($id);
        $this->authorize('delete', $categoria);
        $this->categoriaService->delete($id);
        return response()->json(['success' => true, 'message' => 'Categoria eliminada exitosamente'], 200);
    }
}