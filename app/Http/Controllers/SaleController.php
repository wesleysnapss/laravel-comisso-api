<?php

namespace App\Http\Controllers;

use App\DTOs\SaleDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Sale\SaleService;

class SaleController extends Controller
{
    protected SaleService $service;

    public function __construct(SaleService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $sales = $this->service->getAll();

        return response()->json([
            'status' => true,
            'count' => count($sales),
            'data' => $sales
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'valor_total' => ['required', 'numeric', 'min:0.01'],
            'tipo_venda' => ['required', 'in:direta,afiliada'],
        ]);

        try {
            $sale = $this->service->simulateAndSave($validated);

            return response()->json($sale, 201);
        } catch (\Throwable $e) {
            logger()->error('Erro ao simular e salvar venda', [
                'mensagem' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'rota' => $request->fullUrl(),
                'input' => $validated,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Erro interno ao processar a venda.'
            ], 500);
        }
    }

    public function show(string $id)
    {
        $sale = $this->service->findById($id);

        if (!$sale) {
            return response()->json(['error' => 'Venda não encontrada.'], 404);
        }

        return response()->json($sale, 200);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'valor_total' => ['required', 'numeric', 'min:0.01'],
            'tipo_venda' => ['required', 'in:direta,afiliada'],
        ]);

        try {
            $updated = $this->service->update($id, $validated);

            if (!$updated) {
                return response()->json(['error' => 'Venda não encontrada.'], 404);
            }

            return response()->json($updated, 200);
        } catch (\Throwable $e) {
            logger()->error('Erro ao atualizar venda de venda', [
                'mensagem' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'rota' => $request->fullUrl(),
                'id' => $id,
                'input' => $validated,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Erro interno ao atualizar a venda.'
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $success = $this->service->delete($id);

        return $success
            ? response()->json(['message' => 'Venda removida.'])
            : response()->json(['error' => 'Venda não encontrada.'], 404);
    }
}
