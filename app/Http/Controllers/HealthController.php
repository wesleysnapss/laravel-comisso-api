<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function index()
    {
        try {
            $path = storage_path('app/sales.json');

            if (!file_exists($path)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Arquivo sales.json não encontrado.'
                ], 500);
            }

            if (!is_readable($path) || !is_writable($path)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Permissões insuficientes no arquivo sales.json.'
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'API funcionando corretamente.'
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => 'Erro inesperado no health check.'
            ], 500);
        }
    }
}
